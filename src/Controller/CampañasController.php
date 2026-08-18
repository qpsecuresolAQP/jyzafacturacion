<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * Campañas Controller
 *
 * @property \App\Model\Table\CampañasTable $Campañas
 */
class CampañasController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $currentAction = $this->request->getParam('action');
        
        $this->verificarPermisoORedireccionar('Campañas', $currentAction);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Campañas->find();
        $campañas = $this->paginate($query);

        $this->set(compact('campañas'));
    }

    /**
     * View method
     *
     * @param string|null $id Campaña id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $campaña = $this->Campañas->get($id, contain: []);

        $this->set(compact('campaña'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $campaña = $this->Campañas->newEmptyEntity();
        if ($this->request->is('post')) {
            $campaña = $this->Campañas->patchEntity($campaña, $this->request->getData());
            if ($this->Campañas->save($campaña)) {
                $this->Flash->success(__('The campaña has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The campaña could not be saved. Please, try again.'));
        }
        $this->set(compact('campaña'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Campaña id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $campaña = $this->Campañas->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $campaña = $this->Campañas->patchEntity($campaña, $this->request->getData());
            if ($this->Campañas->save($campaña)) {
                $this->Flash->success(__('The campaña has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The campaña could not be saved. Please, try again.'));
        }
        $this->set(compact('campaña'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Campaña id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $campaña = $this->Campañas->get($id);
        if ($this->Campañas->delete($campaña)) {
            $this->Flash->success(__('The campaña has been deleted.'));
        } else {
            $this->Flash->error(__('The campaña could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

