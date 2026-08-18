<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Proveedores Controller
 *
 * @property \App\Model\Table\ProveedoresTable $Proveedores
 */
class ProveedoresController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $currentAction = $this->request->getParam('action');

        $this->verificarPermisoORedireccionar('Proveedores', $currentAction);
    }

    public function index()
    {
        $query = $this->Proveedores->find()->order(['nombre' => 'ASC']);
        $proveedores = $this->paginate($query);

        $this->set(compact('proveedores'));
    }

    public function view($id = null)
    {
        $proveedor = $this->Proveedores->get($id, contain: []);
        $this->set(compact('proveedor'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    public function add()
    {
        $proveedor = $this->Proveedores->newEmptyEntity();
        if ($this->request->is('post')) {
            $proveedor = $this->Proveedores->patchEntity($proveedor, $this->request->getData());
            if ($this->Proveedores->save($proveedor)) {
                $this->Flash->success(__('Proveedor guardado correctamente.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar el proveedor.'));
        }
        $this->set(compact('proveedor'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    public function edit($id = null)
    {
        $proveedor = $this->Proveedores->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $proveedor = $this->Proveedores->patchEntity($proveedor, $this->request->getData());
            if ($this->Proveedores->save($proveedor)) {
                $this->Flash->success(__('Proveedor guardado correctamente.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar el proveedor.'));
        }
        $this->set(compact('proveedor'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $proveedor = $this->Proveedores->get($id);
        if ($this->Proveedores->delete($proveedor)) {
            $this->Flash->success(__('Proveedor eliminado correctamente.'));
        } else {
            $this->Flash->error(__('No se pudo eliminar el proveedor.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
