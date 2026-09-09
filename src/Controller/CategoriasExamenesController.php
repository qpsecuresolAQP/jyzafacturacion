<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * CategoriasExamenes Controller
 *
 * @property \App\Model\Table\CategoriasExamenesTable $CategoriasExamenes
 */
class CategoriasExamenesController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $currentAction = $this->request->getParam('action');

        $this->verificarPermisoORedireccionar('CategoriasExamenes', $currentAction);
    }

    public function index()
    {
        $estadoFiltro = $this->request->getQuery('estado', 'activos');
        $searchTerm = trim((string) $this->request->getQuery('search', ''));

        $query = $this->CategoriasExamenes->find()->order(['nombre' => 'ASC']);

        if ($estadoFiltro === 'activos') {
            $query->where(['estado' => 1]);
        } elseif ($estadoFiltro === 'inactivos') {
            $query->where(['estado' => 0]);
        }
        // 'todos' no aplica filtro

        if ($searchTerm !== '') {
            $query->where([
                'LOWER(CategoriasExamenes.nombre) LIKE' => '%' . strtolower($searchTerm) . '%',
            ]);
        }

        $categoriasExamenes = $this->paginate($query);

        $this->set(compact('categoriasExamenes', 'estadoFiltro', 'searchTerm'));
    }

    public function view($id = null)
    {
        $categoriasExamene = $this->CategoriasExamenes->get($id, contain: [
            'Examenes' => function ($q) {
                return $q->order(['Examenes.nombre' => 'ASC']);
            },
        ]);

        // Separar activos e inactivos: la tabla principal solo muestra los
        // activos, los inactivos van en su propio apartado reactivable.
        $examenesActivos = $categoriasExamene->examenes
            ? array_values(array_filter($categoriasExamene->examenes, fn($e) => (int)$e->estado === 1))
            : [];
        $examenesInactivos = $categoriasExamene->examenes
            ? array_values(array_filter($categoriasExamene->examenes, fn($e) => (int)$e->estado === 0))
            : [];

        $this->set(compact('categoriasExamene', 'examenesActivos', 'examenesInactivos'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    public function add()
    {
        $categoriasExamene = $this->CategoriasExamenes->newEmptyEntity();
        if ($this->request->is('post')) {
            $categoriasExamene = $this->CategoriasExamenes->patchEntity($categoriasExamene, $this->request->getData());
            if ($this->CategoriasExamenes->save($categoriasExamene)) {
                $this->Flash->success(__('Categoría de examen guardada correctamente.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar la categoría de examen.'));
        }
        $this->set(compact('categoriasExamene'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    public function edit($id = null)
    {
        $categoriasExamene = $this->CategoriasExamenes->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $categoriasExamene = $this->CategoriasExamenes->patchEntity($categoriasExamene, $this->request->getData());
            if ($this->CategoriasExamenes->save($categoriasExamene)) {
                $this->Flash->success(__('Categoría de examen guardada correctamente.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar la categoría de examen.'));
        }
        $this->set(compact('categoriasExamene'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $categoriasExamene = $this->CategoriasExamenes->get($id);

        $conn = $this->CategoriasExamenes->getConnection();
        $ok = $conn->transactional(function () use ($categoriasExamene) {
            // Eliminación lógica: solo se desactiva, no se borra el registro
            // (los exámenes existentes de esta categoría no deben quedar huérfanos).
            $categoriasExamene->estado = 0;
            if (!$this->CategoriasExamenes->save($categoriasExamene)) {
                return false;
            }

            // Cascada: desactiva también los exámenes que sigan activos,
            // marcándolos para poder distinguirlos luego de los que ya
            // estaban inactivos por su cuenta (esos no se tocan).
            $this->CategoriasExamenes->Examenes->updateAll(
                ['estado' => 0, 'desactivado_por_categoria' => 1],
                ['categoria_examen_id' => $categoriasExamene->id, 'estado' => 1]
            );

            return true;
        });

        if ($ok) {
            $this->Flash->success(__('Categoría de examen y sus exámenes fueron desactivados correctamente.'));
        } else {
            $this->Flash->error(__('No se pudo desactivar la categoría de examen.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Reactiva una categoría de examen previamente desactivada. Solo
     * reactiva los exámenes que la propia cascada de delete() había
     * desactivado (desactivado_por_categoria = 1); los que ya estaban
     * inactivos por su cuenta antes de eso quedan como estaban.
     *
     * @param string|null $id Categoría de examen id.
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function reactivar($id = null)
    {
        $this->request->allowMethod(['post']);
        $categoriasExamene = $this->CategoriasExamenes->get($id);

        $conn = $this->CategoriasExamenes->getConnection();
        $ok = $conn->transactional(function () use ($categoriasExamene) {
            $categoriasExamene->estado = 1;
            if (!$this->CategoriasExamenes->save($categoriasExamene)) {
                return false;
            }

            $this->CategoriasExamenes->Examenes->updateAll(
                ['estado' => 1, 'desactivado_por_categoria' => 0],
                ['categoria_examen_id' => $categoriasExamene->id, 'desactivado_por_categoria' => 1]
            );

            return true;
        });

        if ($ok) {
            $this->Flash->success(__('Categoría de examen reactivada correctamente, junto con los exámenes que se desactivaron con ella.'));
        } else {
            $this->Flash->error(__('No se pudo reactivar la categoría de examen.'));
        }

        return $this->redirect(['action' => 'index', '?' => ['estado' => 'inactivos']]);
    }
}
