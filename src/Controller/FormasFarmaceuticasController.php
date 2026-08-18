<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * FormasFarmaceuticas Controller
 *
 * @property \App\Model\Table\FormasFarmaceuticasTable $FormasFarmaceuticas
 * @method \App\Model\Entity\FormasFarmaceutica[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class FormasFarmaceuticasController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    /**
     * Index method
     */
    public function index()
    {
        $this->paginate = [
            'limit' => 25,
            'order' => ['nombre' => 'asc']
        ];
        $formasFarmaceuticas = $this->paginate($this->FormasFarmaceuticas);

        $this->set(compact('formasFarmaceuticas'));
    }

    /**
     * View method
     */
    public function view($id = null)
    {
        $formaFarmaceutica = $this->FormasFarmaceuticas->findById($id)->firstOrFail();
        $this->set(compact('formaFarmaceutica'));

        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Add method
     */
    public function add()
    {
        $formaFarmaceutica = $this->FormasFarmaceuticas->newEmptyEntity();
        if ($this->request->is('post')) {
            $formaFarmaceutica = $this->FormasFarmaceuticas->patchEntity($formaFarmaceutica, $this->request->getData());
            if ($this->FormasFarmaceuticas->save($formaFarmaceutica)) {
                $this->Flash->success(__('Forma farmacéutica guardada correctamente.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar la forma farmacéutica. Intenta de nuevo.'));
        }
        $this->set(compact('formaFarmaceutica'));

        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Edit method
     */
    public function edit($id = null)
    {
        $formaFarmaceutica = $this->FormasFarmaceuticas->findById($id)->firstOrFail();
        if ($this->request->is(['patch', 'post', 'put'])) {
            $formaFarmaceutica = $this->FormasFarmaceuticas->patchEntity($formaFarmaceutica, $this->request->getData());
            if ($this->FormasFarmaceuticas->save($formaFarmaceutica)) {
                $this->Flash->success(__('Forma farmacéutica actualizada correctamente.'));

                return $this->redirect(['action' => 'view', $formaFarmaceutica->id]);
            }
            $this->Flash->error(__('No se pudo actualizar la forma farmacéutica. Intenta de nuevo.'));
        }
        $this->set(compact('formaFarmaceutica'));

        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Delete method
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $formaFarmaceutica = $this->FormasFarmaceuticas->findById($id)->firstOrFail();

        if ($this->FormasFarmaceuticas->delete($formaFarmaceutica)) {
            $this->Flash->success(__('Forma farmacéutica eliminada correctamente.'));
        } else {
            $this->Flash->error(__('No se pudo eliminar la forma farmacéutica. Intenta de nuevo.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Get all formas farmacéuticas as JSON
     */
    public function getAll()
    {
        $this->request->allowMethod(['get']);
        $formas = $this->FormasFarmaceuticas->find()->all();
        return $this->response->withType('application/json')->withStringBody(json_encode($formas->toArray()));
    }
}

