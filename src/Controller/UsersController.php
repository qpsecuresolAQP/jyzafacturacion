<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\ORM\TableRegistry;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        // Acciones públicas que no necesitan verificación
        $publicActions = ['login', 'logout'];
        
        $currentAction = $this->request->getParam('action');
        
        // Si es acción pública, permitir
        if (in_array($currentAction, $publicActions)) {
            return;
        }
        
        // Verificar permiso dinámico para cualquier otra acción
        // En lugar de lanzar excepción, redirigir con mensaje amigable
        $this->verificarPermisoORedireccionar('Users', $currentAction);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // El beforeFilter ya verificó el permiso 'Users/index'
        $query = $this->Users->find();
        $users = $this->paginate($query);
        $roles = $this->Users->Roles->find('list', keyField: 'id', valueField: 'nombre')->toArray();

        $this->set(compact('users', 'roles'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // El beforeFilter ya verificó el permiso 'Users/view'
        $user = $this->Users->get($id, contain: ['Roles']);
        $this->set(compact('user'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        // El beforeFilter ya verificó el permiso 'Users/add'
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $roles = $this->Users->Roles->find('list', keyField: 'id', valueField: 'nombre')->toArray();
        $this->set(compact('user', 'roles'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        // El beforeFilter ya verificó el permiso 'Users/edit'
        $user = $this->Users->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $roles = $this->Users->Roles->find('list', keyField: 'id', valueField: 'nombre')->toArray();
        $this->set(compact('user', 'roles'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        // El beforeFilter ya verificó el permiso 'Users/delete'
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Asignar permisos a un usuario
     * Permite que un administrador dé permisos adicionales a un usuario más allá de su rol
     */
    public function permisos($id = null)
    {
        // Solo admin (rol 1) puede gestionar permisos
        $currentUser = $this->request->getAttribute('identity');
        if ($currentUser->rol_id !== 1) {
            throw new \Cake\Http\Exception\ForbiddenException('No tienes permiso para gestionar permisos');
        }

        $user = $this->Users->get($id);
        
        // Obtener todos los permisos disponibles
        $PermisosTable = TableRegistry::getTableLocator()->get('Permisos');
        $todosPermisos = $PermisosTable->find('all')->toArray();

        // Agrupar permisos por controlador
        $permisosPorControlador = [];
        foreach ($todosPermisos as $permiso) {
            if (!isset($permisosPorControlador[$permiso->controller])) {
                $permisosPorControlador[$permiso->controller] = [];
            }
            $permisosPorControlador[$permiso->controller][] = $permiso;
        }

        // Obtener permisos actuales del usuario
        $UsuariosPermisosTable = TableRegistry::getTableLocator()->get('UsuariosPermisos');
        $permisosActuales = $UsuariosPermisosTable->find()
            ->where(['usuario_id' => $id])
            ->toArray();

        // Obtener permisos del rol del usuario
        $RolesPermisosTable = TableRegistry::getTableLocator()->get('RolesPermisos');
        $rolesPermisos = $RolesPermisosTable->find()
            ->select(['permiso_id'])
            ->where(['rol_id' => $user->rol_id])
            ->toArray();
        $permisosDelRol = array_column($rolesPermisos, 'permiso_id');

        // Crear array para mapeo rápido
        $permisosActualesMap = [];
        $permisosActualesEstado = []; // 'rol', 'adicional', 'denegado'
        foreach ($permisosActuales as $permisoAsignado) {
            $permisosActualesMap[$permisoAsignado->permiso_id] = $permisoAsignado->allow;
            
            // Determinar estado
            if (in_array($permisoAsignado->permiso_id, $permisosDelRol)) {
                if ($permisoAsignado->allow) {
                    $permisosActualesEstado[$permisoAsignado->permiso_id] = 'rol'; // No hacer nada, hereda del rol
                } else {
                    $permisosActualesEstado[$permisoAsignado->permiso_id] = 'denegado'; // Negado explícitamente
                }
            } else {
                if ($permisoAsignado->allow) {
                    $permisosActualesEstado[$permisoAsignado->permiso_id] = 'adicional'; // Permiso adicional
                }
            }
        }
        
        // Para permisos del rol sin override, marcar como 'rol'
        foreach ($permisosDelRol as $permisoId) {
            if (!isset($permisosActualesMap[$permisoId])) {
                $permisosActualesMap[$permisoId] = true;
                $permisosActualesEstado[$permisoId] = 'rol';
            }
        }

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();
            
            // Obtener permisos del rol del usuario
            $RolesPermisosTable = TableRegistry::getTableLocator()->get('RolesPermisos');
            $rolesPermisos = $RolesPermisosTable->find()
                ->select(['permiso_id'])
                ->where(['rol_id' => $user->rol_id])
                ->toArray();
            $permisosDelRol = array_column($rolesPermisos, 'permiso_id');
            
            // Procesar permisos enviados
            foreach ($todosPermisos as $permiso) {
                $permisoKey = 'permiso_' . $permiso->id;
                $permitido = isset($data[$permisoKey]) ? (bool)$data[$permisoKey] : false;

                // Verificar si el permiso ya está asignado en usuarios_permisos
                $permisoAsignado = $UsuariosPermisosTable->find()
                    ->where([
                        'usuario_id' => $id,
                        'permiso_id' => $permiso->id,
                    ])
                    ->first();

                $enRol = in_array($permiso->id, $permisosDelRol);
                
                if ($enRol && $permitido) {
                    // Está en el rol y está permitido: NO crear registro (hereda del rol)
                    if ($permisoAsignado) {
                        $UsuariosPermisosTable->delete($permisoAsignado);
                    }
                } elseif ($enRol && !$permitido) {
                    // Está en el rol pero quiero negarlo: crear con allow=false
                    if ($permisoAsignado) {
                        $permisoAsignado->allow = false;
                        $UsuariosPermisosTable->save($permisoAsignado);
                    } else {
                        $nuevoPermiso = $UsuariosPermisosTable->newEntity([
                            'usuario_id' => $id,
                            'permiso_id' => $permiso->id,
                            'allow' => false,
                        ]);
                        $UsuariosPermisosTable->save($nuevoPermiso);
                    }
                } elseif (!$enRol && $permitido) {
                    // NO está en el rol pero quiero otorgarlo: crear con allow=true
                    if ($permisoAsignado) {
                        $permisoAsignado->allow = true;
                        $UsuariosPermisosTable->save($permisoAsignado);
                    } else {
                        $nuevoPermiso = $UsuariosPermisosTable->newEntity([
                            'usuario_id' => $id,
                            'permiso_id' => $permiso->id,
                            'allow' => true,
                        ]);
                        $UsuariosPermisosTable->save($nuevoPermiso);
                    }
                } else {
                    // NO está en el rol y NO está permitido: eliminar registro si existe
                    if ($permisoAsignado) {
                        $UsuariosPermisosTable->delete($permisoAsignado);
                    }
                }
            }

            $this->Flash->success('Permisos actualizados correctamente');
            return $this->redirect(['action' => 'index']);
        }

        $this->set(compact('user', 'permisosPorControlador', 'permisosActualesMap', 'permisosActualesEstado', 'permisosDelRol'));
    }

    public function login()
    {
        $this->viewBuilder()->setLayout('login'); // Usar el layout 'login'
        $this->request->allowMethod(['get', 'post']);

        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            $usuario = $this->request->getAttribute('identity');

            // Verificar si el usuario está activo
            if ($usuario->estado_user !== 'A') {
                $this->Authentication->logout();
                $this->Flash->error('Tu usuario está inactivo. Contacta al administrador.');
                return $this->redirect(['controller' => 'Users', 'action' => 'login']);
            }

            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
        }

        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error('Usuario o contraseña incorrectos');
        }
    }

    public function logout()
    {
        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            $this->request->getSession()->destroy();
            $this->Authentication->logout();
            $this->Flash->success('You have been logged out');
        }
        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
    }

    /**
     * Acción para cambiar el estado de un usuario (Activo/Inactivo)
     * Toggle entre 'A' (Activo) e 'I' (Inactivo)
     *
     * @param int|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function toggleStatus($id = null)
    {
        // Verificar que existe el usuario
        $user = $this->Users->findById($id)->firstOrFail();

        // Cambiar el estado: A <-> I
        $newEstado = $user->estado_user === 'A' ? 'I' : 'A';
        $user->estado_user = $newEstado;

        // Guardar el cambio
        if ($this->Users->save($user)) {
            $estadoText = $newEstado === 'A' ? 'activado' : 'inactivado';
            $this->Flash->success('Usuario ' . h($user->username) . ' ha sido ' . $estadoText . '.');
        } else {
            $this->Flash->error('No se pudo cambiar el estado del usuario.');
        }

        return $this->redirect(['action' => 'index']);
    }
}