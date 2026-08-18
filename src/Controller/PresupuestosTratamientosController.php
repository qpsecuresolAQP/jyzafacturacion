<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * PresupuestosTratamientos Controller
 *
 * @property \App\Model\Table\PresupuestosTratamientosTable $PresupuestosTratamientos
 */
class PresupuestosTratamientosController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->PresupuestosTratamientos->find()
            ->contain(['Presupuestos', 'Tratamientos']);
        $presupuestosTratamientos = $this->paginate($query);

        $this->set(compact('presupuestosTratamientos'));
    }

    /**
     * View method
     *
     * @param string|null $id Presupuestos Tratamiento id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $presupuestosTratamiento = $this->PresupuestosTratamientos->get($id, contain: ['Presupuestos', 'Tratamientos']);
        $this->set(compact('presupuestosTratamiento'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $presupuestosTratamiento = $this->PresupuestosTratamientos->newEmptyEntity();
        if ($this->request->is('post')) {
            $presupuestosTratamiento = $this->PresupuestosTratamientos->patchEntity($presupuestosTratamiento, $this->request->getData());
            if ($this->PresupuestosTratamientos->save($presupuestosTratamiento)) {
                $this->Flash->success(__('The presupuestos tratamiento has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The presupuestos tratamiento could not be saved. Please, try again.'));
        }
        $presupuestos = $this->PresupuestosTratamientos->Presupuestos->find('list', limit: 200)->all();
        $tratamientos = $this->PresupuestosTratamientos->Tratamientos->find('list', limit: 200)->all();
        $this->set(compact('presupuestosTratamiento', 'presupuestos', 'tratamientos'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Presupuestos Tratamiento id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $presupuestosTratamiento = $this->PresupuestosTratamientos->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $presupuestosTratamiento = $this->PresupuestosTratamientos->patchEntity($presupuestosTratamiento, $this->request->getData());
            if ($this->PresupuestosTratamientos->save($presupuestosTratamiento)) {
                $this->Flash->success(__('The presupuestos tratamiento has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The presupuestos tratamiento could not be saved. Please, try again.'));
        }
        $presupuestos = $this->PresupuestosTratamientos->Presupuestos->find('list', limit: 200)->all();
        $tratamientos = $this->PresupuestosTratamientos->Tratamientos->find('list', limit: 200)->all();
        $this->set(compact('presupuestosTratamiento', 'presupuestos', 'tratamientos'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Presupuestos Tratamiento id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $presupuestosTratamiento = $this->PresupuestosTratamientos->get($id);
        if ($this->PresupuestosTratamientos->delete($presupuestosTratamiento)) {
            $this->Flash->success(__('The presupuestos tratamiento has been deleted.'));
        } else {
            $this->Flash->error(__('The presupuestos tratamiento could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

