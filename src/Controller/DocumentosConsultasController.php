<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * DocumentosConsultas Controller
 *
 * @property \App\Model\Table\DocumentosConsultasTable $DocumentosConsultas
 */
class DocumentosConsultasController extends AppController
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
        $query = $this->DocumentosConsultas->find()
            ->contain(['Consultas', 'Documentos']);
        $documentosConsultas = $this->paginate($query);

        $this->set(compact('documentosConsultas'));
    }

    /**
     * View method
     *
     * @param string|null $id Documentos Consulta id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $documentosConsulta = $this->DocumentosConsultas->get($id, contain: ['Consultas', 'Documentos']);
        $this->set(compact('documentosConsulta'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $documentosConsulta = $this->DocumentosConsultas->newEmptyEntity();
        if ($this->request->is('post')) {
            $documentosConsulta = $this->DocumentosConsultas->patchEntity($documentosConsulta, $this->request->getData());
            if ($this->DocumentosConsultas->save($documentosConsulta)) {
                $this->Flash->success(__('The documentos consulta has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The documentos consulta could not be saved. Please, try again.'));
        }
        $consultas = $this->DocumentosConsultas->Consultas->find('list', limit: 200)->all();
        $documentos = $this->DocumentosConsultas->Documentos->find('list', limit: 200)->all();
        $this->set(compact('documentosConsulta', 'consultas', 'documentos'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Documentos Consulta id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $documentosConsulta = $this->DocumentosConsultas->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $documentosConsulta = $this->DocumentosConsultas->patchEntity($documentosConsulta, $this->request->getData());
            if ($this->DocumentosConsultas->save($documentosConsulta)) {
                $this->Flash->success(__('The documentos consulta has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The documentos consulta could not be saved. Please, try again.'));
        }
        $consultas = $this->DocumentosConsultas->Consultas->find('list', limit: 200)->all();
        $documentos = $this->DocumentosConsultas->Documentos->find('list', limit: 200)->all();
        $this->set(compact('documentosConsulta', 'consultas', 'documentos'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Documentos Consulta id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $documentosConsulta = $this->DocumentosConsultas->get($id);
        if ($this->DocumentosConsultas->delete($documentosConsulta)) {
            $this->Flash->success(__('The documentos consulta has been deleted.'));
        } else {
            $this->Flash->error(__('The documentos consulta could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

