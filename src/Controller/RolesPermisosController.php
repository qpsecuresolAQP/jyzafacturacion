<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * RolesPermisos Controller
 *
 * @property \App\Model\Table\RolesPermisosTable $RolesPermisos
 */
class RolesPermisosController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->RolesPermisos->find()
            ->contain(['Rols', 'Permisos']);
        $rolesPermisos = $this->paginate($query);

        $this->set(compact('rolesPermisos'));
    }

    /**
     * View method
     *
     * @param string|null $id Roles Permiso id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $rolesPermiso = $this->RolesPermisos->get($id, contain: ['Rols', 'Permisos']);
        $this->set(compact('rolesPermiso'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $rolesPermiso = $this->RolesPermisos->newEmptyEntity();
        if ($this->request->is('post')) {
            $rolesPermiso = $this->RolesPermisos->patchEntity($rolesPermiso, $this->request->getData());
            if ($this->RolesPermisos->save($rolesPermiso)) {
                $this->Flash->success(__('The roles permiso has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The roles permiso could not be saved. Please, try again.'));
        }
        $rols = $this->RolesPermisos->Rols->find('list', limit: 200)->all();
        $permisos = $this->RolesPermisos->Permisos->find('list', limit: 200)->all();
        $this->set(compact('rolesPermiso', 'rols', 'permisos'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Roles Permiso id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $rolesPermiso = $this->RolesPermisos->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $rolesPermiso = $this->RolesPermisos->patchEntity($rolesPermiso, $this->request->getData());
            if ($this->RolesPermisos->save($rolesPermiso)) {
                $this->Flash->success(__('The roles permiso has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The roles permiso could not be saved. Please, try again.'));
        }
        $rols = $this->RolesPermisos->Rols->find('list', limit: 200)->all();
        $permisos = $this->RolesPermisos->Permisos->find('list', limit: 200)->all();
        $this->set(compact('rolesPermiso', 'rols', 'permisos'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Roles Permiso id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $rolesPermiso = $this->RolesPermisos->get($id);
        if ($this->RolesPermisos->delete($rolesPermiso)) {
            $this->Flash->success(__('The roles permiso has been deleted.'));
        } else {
            $this->Flash->error(__('The roles permiso could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

