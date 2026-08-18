<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * CitasTratamientos Controller
 *
 * @property \App\Model\Table\CitasTratamientosTable $CitasTratamientos
 */
class CitasTratamientosController extends AppController
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
        $query = $this->CitasTratamientos->find()
            ->contain(['Tratamientos', 'Citas']);
        $citasTratamientos = $this->paginate($query);

        $this->set(compact('citasTratamientos'));
    }

    /**
     * View method
     *
     * @param string|null $id Citas Tratamiento id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $citasTratamiento = $this->CitasTratamientos->get($id, contain: ['Tratamientos', 'Citas']);
        $this->set(compact('citasTratamiento'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $citasTratamiento = $this->CitasTratamientos->newEmptyEntity();
        if ($this->request->is('post')) {
            $citasTratamiento = $this->CitasTratamientos->patchEntity($citasTratamiento, $this->request->getData());
            if ($this->CitasTratamientos->save($citasTratamiento)) {
                $this->Flash->success(__('The citas tratamiento has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The citas tratamiento could not be saved. Please, try again.'));
        }
        $tratamientos = $this->CitasTratamientos->Tratamientos->find('list', limit: 200)->all();
        $citas = $this->CitasTratamientos->Citas->find('list', limit: 200)->all();
        $this->set(compact('citasTratamiento', 'tratamientos', 'citas'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Citas Tratamiento id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $citasTratamiento = $this->CitasTratamientos->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $citasTratamiento = $this->CitasTratamientos->patchEntity($citasTratamiento, $this->request->getData());
            if ($this->CitasTratamientos->save($citasTratamiento)) {
                $this->Flash->success(__('The citas tratamiento has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The citas tratamiento could not be saved. Please, try again.'));
        }
        $tratamientos = $this->CitasTratamientos->Tratamientos->find('list', limit: 200)->all();
        $citas = $this->CitasTratamientos->Citas->find('list', limit: 200)->all();
        $this->set(compact('citasTratamiento', 'tratamientos', 'citas'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Citas Tratamiento id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $citasTratamiento = $this->CitasTratamientos->get($id);
        if ($this->CitasTratamientos->delete($citasTratamiento)) {
            $this->Flash->success(__('The citas tratamiento has been deleted.'));
        } else {
            $this->Flash->error(__('The citas tratamiento could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

