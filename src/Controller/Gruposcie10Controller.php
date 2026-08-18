<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Gruposcie10 Controller
 *
 * @property \App\Model\Table\Gruposcie10Table $Gruposcie10
 */
class Gruposcie10Controller extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Gruposcie10->find();
        $gruposcie10 = $this->paginate($query);

        $this->set(compact('gruposcie10'));
    }

    /**
     * View method
     *
     * @param string|null $id Gruposcie10 id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $gruposcie10 = $this->Gruposcie10->get($id, contain: []);
        $this->set(compact('gruposcie10'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $gruposcie10 = $this->Gruposcie10->newEmptyEntity();
        if ($this->request->is('post')) {
            $gruposcie10 = $this->Gruposcie10->patchEntity($gruposcie10, $this->request->getData());
            if ($this->Gruposcie10->save($gruposcie10)) {
                $this->Flash->success(__('The gruposcie10 has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The gruposcie10 could not be saved. Please, try again.'));
        }
        $this->set(compact('gruposcie10'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Gruposcie10 id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $gruposcie10 = $this->Gruposcie10->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $gruposcie10 = $this->Gruposcie10->patchEntity($gruposcie10, $this->request->getData());
            if ($this->Gruposcie10->save($gruposcie10)) {
                $this->Flash->success(__('The gruposcie10 has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The gruposcie10 could not be saved. Please, try again.'));
        }
        $this->set(compact('gruposcie10'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Gruposcie10 id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $gruposcie10 = $this->Gruposcie10->get($id);
        if ($this->Gruposcie10->delete($gruposcie10)) {
            $this->Flash->success(__('The gruposcie10 has been deleted.'));
        } else {
            $this->Flash->error(__('The gruposcie10 could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

