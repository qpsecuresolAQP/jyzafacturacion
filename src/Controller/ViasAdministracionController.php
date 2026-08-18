<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * ViasAdministracion Controller
 *
 * @property \App\Model\Table\ViasAdministracionTable $ViasAdministracion
 * @method \App\Model\Entity\ViasAdministracion[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ViasAdministracionController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $currentAction = $this->request->getParam('action');
        
        $this->verificarPermisoORedireccionar('ViasAdministracion', $currentAction);
    }

    /**
     * Index method
     */
    public function index()
    {
        $this->paginate = [
            'limit' => 25,
            'order' => ['nombre' => 'asc']
        ];
        $viasAdministracion = $this->paginate($this->ViasAdministracion);

        $this->set(compact('viasAdministracion'));
    }

    /**
     * View method
     */
    public function view($id = null)
    {
        $viaAdministracion = $this->ViasAdministracion->findById($id)->firstOrFail();
        $this->set(compact('viaAdministracion'));
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
        $viaAdministracion = $this->ViasAdministracion->newEmptyEntity();
        if ($this->request->is('post')) {
            $viaAdministracion = $this->ViasAdministracion->patchEntity($viaAdministracion, $this->request->getData());
            if ($this->ViasAdministracion->save($viaAdministracion)) {
                $this->Flash->success(__('Vía de administración guardada correctamente.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar la vía de administración. Intenta de nuevo.'));
        }
        $this->set(compact('viaAdministracion'));
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
        $viaAdministracion = $this->ViasAdministracion->findById($id)->firstOrFail();
        if ($this->request->is(['patch', 'post', 'put'])) {
            $viaAdministracion = $this->ViasAdministracion->patchEntity($viaAdministracion, $this->request->getData());
            if ($this->ViasAdministracion->save($viaAdministracion)) {
                $this->Flash->success(__('Vía de administración actualizada correctamente.'));

                return $this->redirect(['action' => 'view', $viaAdministracion->id]);
            }
            $this->Flash->error(__('No se pudo actualizar la vía de administración. Intenta de nuevo.'));
        }
        $this->set(compact('viaAdministracion'));
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
        $viaAdministracion = $this->ViasAdministracion->findById($id)->firstOrFail();

        if ($this->ViasAdministracion->delete($viaAdministracion)) {
            $this->Flash->success(__('Vía de administración eliminada correctamente.'));
        } else {
            $this->Flash->error(__('No se pudo eliminar la vía de administración. Intenta de nuevo.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Get all vías de administración as JSON
     */
    public function getAll()
    {
        $this->request->allowMethod(['get']);
        $vias = $this->ViasAdministracion->find()->all();
        return $this->response->withType('application/json')->withStringBody(json_encode($vias->toArray()));
    }
}

