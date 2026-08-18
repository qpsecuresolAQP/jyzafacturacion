<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * ConsultasCie Controller
 *
 * @property \App\Model\Table\ConsultasCieTable $ConsultasCie
 */
class ConsultasCieController extends AppController
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
        $query = $this->ConsultasCie->find()
            ->contain(['Consultas', 'Cies']);
        $consultasCie = $this->paginate($query);

        $this->set(compact('consultasCie'));
    }

    /**
     * View method
     *
     * @param string|null $id Consultas Cie id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $consultasCie = $this->ConsultasCie->get($id, contain: ['Consultas', 'Cies']);
        $this->set(compact('consultasCie'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $consultasCie = $this->ConsultasCie->newEmptyEntity();
        if ($this->request->is('post')) {
            $consultasCie = $this->ConsultasCie->patchEntity($consultasCie, $this->request->getData());
            if ($this->ConsultasCie->save($consultasCie)) {
                $this->Flash->success(__('The consultas cie has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The consultas cie could not be saved. Please, try again.'));
        }
        $consultas = $this->ConsultasCie->Consultas->find('list', limit: 200)->all();
        $cies = $this->ConsultasCie->Cies->find('list', limit: 200)->all();
        $this->set(compact('consultasCie', 'consultas', 'cies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Consultas Cie id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $consultasCie = $this->ConsultasCie->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $consultasCie = $this->ConsultasCie->patchEntity($consultasCie, $this->request->getData());
            if ($this->ConsultasCie->save($consultasCie)) {
                $this->Flash->success(__('The consultas cie has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The consultas cie could not be saved. Please, try again.'));
        }
        $consultas = $this->ConsultasCie->Consultas->find('list', limit: 200)->all();
        $cies = $this->ConsultasCie->Cies->find('list', limit: 200)->all();
        $this->set(compact('consultasCie', 'consultas', 'cies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Consultas Cie id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $consultasCie = $this->ConsultasCie->get($id);
        if ($this->ConsultasCie->delete($consultasCie)) {
            $this->Flash->success(__('The consultas cie has been deleted.'));
        } else {
            $this->Flash->error(__('The consultas cie could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

