<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Subgruposcie10 Controller
 *
 * @property \App\Model\Table\Subgruposcie10Table $Subgruposcie10
 */
class Subgruposcie10Controller extends AppController
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
        $query = $this->Subgruposcie10->find();
        $subgruposcie10 = $this->paginate($query);

        $this->set(compact('subgruposcie10'));
    }

    /**
     * View method
     *
     * @param string|null $id Subgruposcie10 id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $subgruposcie10 = $this->Subgruposcie10->get($id, contain: []);
        $this->set(compact('subgruposcie10'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $subgruposcie10 = $this->Subgruposcie10->newEmptyEntity();
        if ($this->request->is('post')) {
            $subgruposcie10 = $this->Subgruposcie10->patchEntity($subgruposcie10, $this->request->getData());
            if ($this->Subgruposcie10->save($subgruposcie10)) {
                $this->Flash->success(__('The subgruposcie10 has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The subgruposcie10 could not be saved. Please, try again.'));
        }
        $this->set(compact('subgruposcie10'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Subgruposcie10 id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $subgruposcie10 = $this->Subgruposcie10->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $subgruposcie10 = $this->Subgruposcie10->patchEntity($subgruposcie10, $this->request->getData());
            if ($this->Subgruposcie10->save($subgruposcie10)) {
                $this->Flash->success(__('The subgruposcie10 has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The subgruposcie10 could not be saved. Please, try again.'));
        }
        $this->set(compact('subgruposcie10'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Subgruposcie10 id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $subgruposcie10 = $this->Subgruposcie10->get($id);
        if ($this->Subgruposcie10->delete($subgruposcie10)) {
            $this->Flash->success(__('The subgruposcie10 has been deleted.'));
        } else {
            $this->Flash->error(__('The subgruposcie10 could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

