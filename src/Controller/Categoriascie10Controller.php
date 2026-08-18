<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Categoriascie10 Controller
 *
 * @property \App\Model\Table\Categoriascie10Table $Categoriascie10
 */
class Categoriascie10Controller extends AppController
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
        $query = $this->Categoriascie10->find();
        $categoriascie10 = $this->paginate($query);

        $this->set(compact('categoriascie10'));
    }

    /**
     * View method
     *
     * @param string|null $id Categoriascie10 id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $categoriascie10 = $this->Categoriascie10->get($id, contain: []);
        $this->set(compact('categoriascie10'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $categoriascie10 = $this->Categoriascie10->newEmptyEntity();
        if ($this->request->is('post')) {
            $categoriascie10 = $this->Categoriascie10->patchEntity($categoriascie10, $this->request->getData());
            if ($this->Categoriascie10->save($categoriascie10)) {
                $this->Flash->success(__('The categoriascie10 has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The categoriascie10 could not be saved. Please, try again.'));
        }
        $this->set(compact('categoriascie10'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Categoriascie10 id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $categoriascie10 = $this->Categoriascie10->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $categoriascie10 = $this->Categoriascie10->patchEntity($categoriascie10, $this->request->getData());
            if ($this->Categoriascie10->save($categoriascie10)) {
                $this->Flash->success(__('The categoriascie10 has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The categoriascie10 could not be saved. Please, try again.'));
        }
        $this->set(compact('categoriascie10'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Categoriascie10 id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $categoriascie10 = $this->Categoriascie10->get($id);
        if ($this->Categoriascie10->delete($categoriascie10)) {
            $this->Flash->success(__('The categoriascie10 has been deleted.'));
        } else {
            $this->Flash->error(__('The categoriascie10 could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

