<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * UsuariosPermisos Controller
 *
 * @property \App\Model\Table\UsuariosPermisosTable $UsuariosPermisos
 */
class UsuariosPermisosController extends AppController
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
        $query = $this->UsuariosPermisos->find()
            ->contain(['Usuarios', 'Permisos']);
        $usuariosPermisos = $this->paginate($query);

        $this->set(compact('usuariosPermisos'));
    }

    /**
     * View method
     *
     * @param string|null $id Usuarios Permiso id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $usuariosPermiso = $this->UsuariosPermisos->get($id, contain: ['Usuarios', 'Permisos']);
        $this->set(compact('usuariosPermiso'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $usuariosPermiso = $this->UsuariosPermisos->newEmptyEntity();
        if ($this->request->is('post')) {
            $usuariosPermiso = $this->UsuariosPermisos->patchEntity($usuariosPermiso, $this->request->getData());
            if ($this->UsuariosPermisos->save($usuariosPermiso)) {
                $this->Flash->success(__('The usuarios permiso has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The usuarios permiso could not be saved. Please, try again.'));
        }
        $usuarios = $this->UsuariosPermisos->Usuarios->find('list', limit: 200)->all();
        $permisos = $this->UsuariosPermisos->Permisos->find('list', limit: 200)->all();
        $this->set(compact('usuariosPermiso', 'usuarios', 'permisos'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Usuarios Permiso id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $usuariosPermiso = $this->UsuariosPermisos->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $usuariosPermiso = $this->UsuariosPermisos->patchEntity($usuariosPermiso, $this->request->getData());
            if ($this->UsuariosPermisos->save($usuariosPermiso)) {
                $this->Flash->success(__('The usuarios permiso has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The usuarios permiso could not be saved. Please, try again.'));
        }
        $usuarios = $this->UsuariosPermisos->Usuarios->find('list', limit: 200)->all();
        $permisos = $this->UsuariosPermisos->Permisos->find('list', limit: 200)->all();
        $this->set(compact('usuariosPermiso', 'usuarios', 'permisos'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Usuarios Permiso id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $usuariosPermiso = $this->UsuariosPermisos->get($id);
        if ($this->UsuariosPermisos->delete($usuariosPermiso)) {
            $this->Flash->success(__('The usuarios permiso has been deleted.'));
        } else {
            $this->Flash->error(__('The usuarios permiso could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

