<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * DocumentosProcedimientos Controller
 *
 * @property \App\Model\Table\DocumentosProcedimientosTable $DocumentosProcedimientos
 */
class DocumentosProcedimientosController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->DocumentosProcedimientos->find()
            ->contain(['Procedimientos', 'Documentos']);
        $documentosProcedimientos = $this->paginate($query);

        $this->set(compact('documentosProcedimientos'));
    }

    /**
     * View method
     *
     * @param string|null $id Documentos Procedimiento id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $documentosProcedimiento = $this->DocumentosProcedimientos->get($id, contain: ['Procedimientos', 'Documentos']);
        $this->set(compact('documentosProcedimiento'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $documentosProcedimiento = $this->DocumentosProcedimientos->newEmptyEntity();
        if ($this->request->is('post')) {
            $documentosProcedimiento = $this->DocumentosProcedimientos->patchEntity($documentosProcedimiento, $this->request->getData());
            if ($this->DocumentosProcedimientos->save($documentosProcedimiento)) {
                $this->Flash->success(__('The documentos procedimiento has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The documentos procedimiento could not be saved. Please, try again.'));
        }
        $procedimientos = $this->DocumentosProcedimientos->Procedimientos->find('list', limit: 200)->all();
        $documentos = $this->DocumentosProcedimientos->Documentos->find('list', limit: 200)->all();
        $this->set(compact('documentosProcedimiento', 'procedimientos', 'documentos'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Documentos Procedimiento id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $documentosProcedimiento = $this->DocumentosProcedimientos->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $documentosProcedimiento = $this->DocumentosProcedimientos->patchEntity($documentosProcedimiento, $this->request->getData());
            if ($this->DocumentosProcedimientos->save($documentosProcedimiento)) {
                $this->Flash->success(__('The documentos procedimiento has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The documentos procedimiento could not be saved. Please, try again.'));
        }
        $procedimientos = $this->DocumentosProcedimientos->Procedimientos->find('list', limit: 200)->all();
        $documentos = $this->DocumentosProcedimientos->Documentos->find('list', limit: 200)->all();
        $this->set(compact('documentosProcedimiento', 'procedimientos', 'documentos'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Documentos Procedimiento id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $documentosProcedimiento = $this->DocumentosProcedimientos->get($id);
        if ($this->DocumentosProcedimientos->delete($documentosProcedimiento)) {
            $this->Flash->success(__('The documentos procedimiento has been deleted.'));
        } else {
            $this->Flash->error(__('The documentos procedimiento could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
