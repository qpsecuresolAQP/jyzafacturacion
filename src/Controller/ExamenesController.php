<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Examenes Controller
 *
 * @property \App\Model\Table\ExamenesTable $Examenes
 */
class ExamenesController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $currentAction = $this->request->getParam('action');

        $this->verificarPermisoORedireccionar('Examenes', $currentAction);
    }

    public function index()
    {
        $searchTerm = $this->request->getQuery('search', '');
        $estadoFiltro = $this->request->getQuery('estado', 'activos');

        $query = $this->Examenes->find()
            ->contain(['CategoriasExamenes', 'Laboratorios'])
            ->order(['CategoriasExamenes.nombre' => 'ASC', 'Examenes.nombre' => 'ASC']);

        if ($estadoFiltro === 'activos') {
            $query->where(['Examenes.estado' => 1]);
        } elseif ($estadoFiltro === 'inactivos') {
            $query->where(['Examenes.estado' => 0]);
        }
        // 'todos' no aplica filtro

        if (!empty($searchTerm)) {
            $query->where([
                'LOWER(Examenes.nombre) LIKE' => '%' . strtolower($searchTerm) . '%',
            ]);
        }

        $examenes = $this->paginate($query);

        $this->set(compact('examenes', 'searchTerm', 'estadoFiltro'));
    }

    public function view($id = null)
    {
        $examene = $this->Examenes->get($id, contain: ['CategoriasExamenes', 'Laboratorios']);
        $this->set(compact('examene'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    public function add()
    {
        $examene = $this->Examenes->newEmptyEntity();

        $categoriaExamenId = $this->request->getQuery('categoria_examen_id');

        if ($this->request->is('post')) {
            $examene = $this->Examenes->patchEntity($examene, $this->request->getData());
            if ($this->Examenes->save($examene)) {
                $this->Flash->success(__('Examen guardado correctamente.'));

                if (!empty($examene->categoria_examen_id)) {
                    return $this->redirect([
                        'controller' => 'CategoriasExamenes',
                        'action' => 'view',
                        $examene->categoria_examen_id,
                    ]);
                }

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar el examen.'));
        } elseif ($categoriaExamenId) {
            $examene->categoria_examen_id = (int)$categoriaExamenId;
        }

        $categoriasExamenes = $this->Examenes->CategoriasExamenes->find('list', [
            'keyField' => 'id',
            'valueField' => 'nombre',
        ])->order(['nombre' => 'ASC'])->toArray();

        $laboratorios = $this->Examenes->Laboratorios->find('list', [
            'keyField' => 'id',
            'valueField' => 'nombre',
        ])->where(['activo' => 1])->order(['nombre' => 'ASC'])->toArray();

        $this->set(compact('examene', 'categoriasExamenes', 'laboratorios'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    public function edit($id = null)
    {
        $examene = $this->Examenes->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $examene = $this->Examenes->patchEntity($examene, $this->request->getData());
            if ($this->Examenes->save($examene)) {
                $this->Flash->success(__('Examen guardado correctamente.'));

                if (!empty($examene->categoria_examen_id)) {
                    return $this->redirect([
                        'controller' => 'CategoriasExamenes',
                        'action' => 'view',
                        $examene->categoria_examen_id,
                    ]);
                }

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar el examen.'));
        }

        $categoriasExamenes = $this->Examenes->CategoriasExamenes->find('list', [
            'keyField' => 'id',
            'valueField' => 'nombre',
        ])->order(['nombre' => 'ASC'])->toArray();

        $laboratorios = $this->Examenes->Laboratorios->find('list', [
            'keyField' => 'id',
            'valueField' => 'nombre',
        ])->where(['activo' => 1])->order(['nombre' => 'ASC'])->toArray();

        $this->set(compact('examene', 'categoriasExamenes', 'laboratorios'));
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
        $examene = $this->Examenes->get($id);
        $categoriaExamenId = $examene->categoria_examen_id;

        // Eliminación lógica: solo se desactiva, no se borra el registro
        // (queda referenciado por facturas ya emitidas vía invoice_items.examen_id).
        $examene->estado = 0;

        if ($this->Examenes->save($examene)) {
            $this->Flash->success(__('Examen desactivado correctamente.'));
        } else {
            $this->Flash->error(__('No se pudo desactivar el examen.'));
        }

        // Si se llegó desde la vista de una categoría, volver ahí en vez del
        // índice general, para que el usuario vea el resultado en el mismo lugar.
        $referer = $this->request->referer();
        if ($referer && str_contains($referer, '/categorias-examenes/view/')) {
            return $this->redirect([
                'controller' => 'CategoriasExamenes',
                'action' => 'view',
                $categoriaExamenId,
            ]);
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Reactiva un examen previamente desactivado.
     *
     * @param string|null $id Examen id.
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function reactivar($id = null)
    {
        $this->request->allowMethod(['post']);
        $examene = $this->Examenes->get($id);

        $examene->estado = 1;

        if ($this->Examenes->save($examene)) {
            $this->Flash->success(__('Examen reactivado correctamente.'));
        } else {
            $this->Flash->error(__('No se pudo reactivar el examen.'));
        }

        return $this->redirect(['action' => 'index', '?' => ['estado' => 'inactivos']]);
    }

    /**
     * Búsqueda AJAX de exámenes para el formulario de facturación
     * (mismo patrón que Productos::buscar).
     */
    public function buscar()
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;

        $q = trim((string) $this->request->getQuery('q', ''));

        $query = $this->Examenes->find()
            ->contain(['CategoriasExamenes'])
            ->where(['Examenes.estado' => 1])
            ->order(['Examenes.nombre' => 'ASC'])
            ->limit(20);

        if ($q !== '') {
            $query->where(['LOWER(Examenes.nombre) LIKE' => '%' . strtolower($q) . '%']);
        }

        $resultados = $query->all()->map(function ($examene) {
            return [
                'id' => $examene->id,
                'nombre' => $examene->nombre,
                'categoria' => $examene->categorias_examene->nombre ?? '',
                'muestra' => $examene->muestra,
                'precio' => (float) $examene->precio,
            ];
        })->toList();

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($resultados, JSON_UNESCAPED_UNICODE));
    }
}
