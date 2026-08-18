<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Diagnosticoscie10 Controller
 *
 * @property \App\Model\Table\Diagnosticoscie10Table $Diagnosticoscie10
 */
class Diagnosticoscie10Controller extends AppController
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
        $query = $this->Diagnosticoscie10->find();
        $diagnosticoscie10 = $this->paginate($query);

        $this->set(compact('diagnosticoscie10'));
    }

    /**
     * View method
     *
     * @param string|null $id Diagnosticoscie10 id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $diagnosticoscie10 = $this->Diagnosticoscie10->get($id, contain: []);
        $this->set(compact('diagnosticoscie10'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $diagnosticoscie10 = $this->Diagnosticoscie10->newEmptyEntity();
        if ($this->request->is('post')) {
            $diagnosticoscie10 = $this->Diagnosticoscie10->patchEntity($diagnosticoscie10, $this->request->getData());
            if ($this->Diagnosticoscie10->save($diagnosticoscie10)) {
                $this->Flash->success(__('The diagnosticoscie10 has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The diagnosticoscie10 could not be saved. Please, try again.'));
        }
        $this->set(compact('diagnosticoscie10'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Diagnosticoscie10 id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $diagnosticoscie10 = $this->Diagnosticoscie10->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $diagnosticoscie10 = $this->Diagnosticoscie10->patchEntity($diagnosticoscie10, $this->request->getData());
            if ($this->Diagnosticoscie10->save($diagnosticoscie10)) {
                $this->Flash->success(__('The diagnosticoscie10 has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The diagnosticoscie10 could not be saved. Please, try again.'));
        }
        $this->set(compact('diagnosticoscie10'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Diagnosticoscie10 id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $diagnosticoscie10 = $this->Diagnosticoscie10->get($id);
        if ($this->Diagnosticoscie10->delete($diagnosticoscie10)) {
            $this->Flash->success(__('The diagnosticoscie10 has been deleted.'));
        } else {
            $this->Flash->error(__('The diagnosticoscie10 could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

