<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Laboratorios Controller
 *
 * @property \App\Model\Table\LaboratoriosTable $Laboratorios
 */
class LaboratoriosController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $currentAction = $this->request->getParam('action');

        $this->verificarPermisoORedireccionar('Laboratorios', $currentAction);
    }

    public function index()
    {
        $query = $this->Laboratorios->find()->order(['nombre' => 'ASC']);
        $laboratorios = $this->paginate($query);

        $this->set(compact('laboratorios'));

    }

    public function view($id = null)
    {
        $laboratorio = $this->Laboratorios->get($id, contain: []);
        $this->set(compact('laboratorio'));
        // Si es una petición AJAX, usamos un layout específico
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    public function add()
    {
        $laboratorio = $this->Laboratorios->newEmptyEntity();
        if ($this->request->is('post')) {
            $laboratorio = $this->Laboratorios->patchEntity($laboratorio, $this->request->getData());
            if ($this->Laboratorios->save($laboratorio)) {
                $this->Flash->success(__('Laboratorio guardado correctamente.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar el laboratorio.'));
        }
        $this->set(compact('laboratorio'));
        // Si es una petición AJAX, usamos un layout específico
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    public function edit($id = null)
    {
        $laboratorio = $this->Laboratorios->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $laboratorio = $this->Laboratorios->patchEntity($laboratorio, $this->request->getData());
            if ($this->Laboratorios->save($laboratorio)) {
                $this->Flash->success(__('Laboratorio guardado correctamente.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar el laboratorio.'));
        }
        $this->set(compact('laboratorio'));
        // Si es una petición AJAX, usamos un layout específico
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $laboratorio = $this->Laboratorios->get($id);
        if ($this->Laboratorios->delete($laboratorio)) {
            $this->Flash->success(__('Laboratorio eliminado correctamente.'));
        } else {
            $this->Flash->error(__('No se pudo eliminar el laboratorio.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
