<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * Roles Controller
 *
 * @property \App\Model\Table\RolesTable $Roles
 */
class RolesController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $currentAction = $this->request->getParam('action');

        $this->verificarPermisoORedireccionar('Roles', $currentAction);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Roles->find();
        $roles = $this->paginate($query);

        $this->set(compact('roles'));
    }

    /**
     * View method
     *
     * @param string|null $id Role id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $role = $this->Roles->get($id, contain: ['Permisos']);
        $this->set(compact('role'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $role = $this->Roles->newEmptyEntity();
        if ($this->request->is('post')) {
            $role = $this->Roles->patchEntity($role, $this->request->getData());
            if ($this->Roles->save($role)) {
                $this->Flash->success(__('The role has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The role could not be saved. Please, try again.'));
        }

        // Obtener permisos agrupados por controlador
        $todosPermisos = $this->Roles->Permisos->find()->toArray();
        $permisosAgrupados = [];

        foreach ($todosPermisos as $permiso) {
            if (!isset($permisosAgrupados[$permiso->controller])) {
                $permisosAgrupados[$permiso->controller] = [];
            }
            $permisosAgrupados[$permiso->controller][$permiso->id] = $permiso;
        }

        ksort($permisosAgrupados);

        $this->set(compact('role', 'permisosAgrupados'));
    }

    /**
     * Crear rol con permisos personalizados
     * Permite crear un rol nuevo y seleccionar específicamente qué permisos asignarle
     */
    public function createWithDefaults()
    {
        // Obtener todos los permisos disponibles
        $permisosTable = $this->Roles->Permisos;
        $todosPermisos = $permisosTable->find()->orderBy(['controller' => 'ASC', 'action' => 'ASC'])->all();

        // Agrupar permisos por controller para mejor visualización
        $permisosAgrupados = [];
        foreach ($todosPermisos as $permiso) {
            if (!isset($permisosAgrupados[$permiso->controller])) {
                $permisosAgrupados[$permiso->controller] = [];
            }
            $permisosAgrupados[$permiso->controller][] = $permiso;
        }
        ksort($permisosAgrupados);

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Validar que nombre no esté vacío
            if (empty($data['nombre'])) {
                $this->Flash->error(__('El nombre del rol es obligatorio'));
                return $this->redirect(['action' => 'createWithDefaults']);
            }

            // Crear el rol
            $role = $this->Roles->newEntity([
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'] ?? '',
            ]);

            if ($this->Roles->save($role)) {
                // Obtener los IDs de permisos seleccionados
                $permisosSeleccionados = $data['permisos'] ?? [];

                if (!empty($permisosSeleccionados)) {
                    // Asignar permisos seleccionados al rol
                    $rolesPermisosTable = $this->Roles->getAssociation('Permisos')->junction();

                    foreach ($permisosSeleccionados as $permisoId) {
                        $rolPermiso = $rolesPermisosTable->newEntity([
                            'rol_id' => $role->id,
                            'permiso_id' => $permisoId,
                        ]);
                        $rolesPermisosTable->save($rolPermiso);
                    }
                }

                $this->Flash->success(__('El rol ha sido creado exitosamente con los permisos seleccionados.'));
                return $this->redirect(['action' => 'view', $role->id]);
            } else {
                $this->Flash->error(__('No se pudo crear el rol. Por favor intenta de nuevo.'));
            }
        }

        $this->set(compact('permisosAgrupados', 'todosPermisos'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Role id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $role = $this->Roles->get($id, contain: ['Permisos']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $role = $this->Roles->patchEntity($role, $this->request->getData());
            if ($this->Roles->save($role)) {
                $this->Flash->success(__('The role has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The role could not be saved. Please, try again.'));
        }

        // Obtener permisos agrupados por controlador
        $todosPermisos = $this->Roles->Permisos->find()->toArray();
        $permisosAgrupados = [];

        foreach ($todosPermisos as $permiso) {
            if (!isset($permisosAgrupados[$permiso->controller])) {
                $permisosAgrupados[$permiso->controller] = [];
            }
            $permisosAgrupados[$permiso->controller][$permiso->id] = $permiso;
        }

        ksort($permisosAgrupados);

        // Obtener los IDs de permisos que ya tiene asignados el rol
        $permisoIdsAsignados = array_map(function ($p) {
            return $p->id;
        }, $role->permisos);

        $this->set(compact('role', 'permisosAgrupados', 'permisoIdsAsignados'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Role id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $role = $this->Roles->get($id);
        if ($this->Roles->delete($role)) {
            $this->Flash->success(__('The role has been deleted.'));
        } else {
            $this->Flash->error(__('The role could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
