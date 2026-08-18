<?php
declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

/**
 * Componente para verificar permisos de usuario
 * Uso en controladores: $this->Permisos->verificar($controller, $action, $userId)
 */
class PermisosComponent extends Component
{
    /**
     * Verifica si el usuario tiene un rol específico (compatible con 'rol' y 'rol_id')
     * 
     * @param int|array $roles ID(s) de rol a verificar
     * @return bool
     */
    public function tieneRol($roles): bool
    {
        $user = $this->getController()->Authentication->getIdentity();
        if (!$user) {
            return false;
        }

        // Asegurar que roles sea array
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        // Intentar con rol_id primero (nuevo sistema)
        if (isset($user->rol_id)) {
            return in_array($user->rol_id, $roles);
        }

        // Fallback a rol (sistema antiguo)
        if (isset($user->rol)) {
            return in_array($user->rol, $roles);
        }

        return false;
    }

    /**
     * 
     * @param string $controller Nombre del controlador
     * @param string $action Nombre de la acción
     * @param int $userId ID del usuario (si no se proporciona, usa el usuario autenticado)
     * @return bool
     */
    public function verificar(string $controller, string $action, ?int $userId = null): bool
    {
        $controllerInstance = $this->getController();
        $user = $controllerInstance->Authentication->getIdentity();

        if (!$user) {
            return false;
        }

        if ($userId === null) {
            $userId = $user->id;
        }

        return $this->hasPermission($userId, $user->rol_id, $controller, $action);
    }

    /**
     * Verifica si el usuario tiene permiso y lanza una excepción si no
     * 
     * @param string $controller Nombre del controlador
     * @param string $action Nombre de la acción
     * @param int|null $userId ID del usuario
     * @return void
     * @throws \Cake\Http\Exception\ForbiddenException
     */
    public function verificarOForbidden(string $controller, string $action, ?int $userId = null): void
    {
        if (!$this->verificar($controller, $action, $userId)) {
            throw new \Cake\Http\Exception\ForbiddenException('No tienes permiso para acceder a esta acción');
        }
    }

    /**
     * Obtiene todos los permisos del usuario autenticado actual
     * Devuelve los permisos formateados para usar en vistas
     * 
     * @return array Array con estructura ['Controller/action' => true]
     */
    public function obtenerPermisosUsuarioActual(): array
    {
        $controller = $this->getController();
        $user = $controller->Authentication->getIdentity();

        if (!$user) {
            return [];
        }

        // Verificar permisos para todos los usuarios (incluyendo admin)
        // basándose en roles_permisos y usuarios_permisos
        return $this->obtenerPermisos($user->id, $user->rol_id);
    }

    /**
     * Verifica si el usuario actual tiene un permiso específico
     * Útil para mostrar/ocultar elementos en vistas
     * 
     * @param string $controller
     * @param string $action
     * @return bool
     */
    public function tienePermiso(string $controller, string $action): bool
    {
        $permisos = $this->obtenerPermisosUsuarioActual();
        $key = $controller . '/' . $action;
        
        // DEBUG: Log para verificar
        error_log("Buscando permiso: $key en " . count($permisos) . " permisos disponibles");
        
        return isset($permisos[$key]);
    }

    /**
     * Devuelve todos los permisos disponibles (para admin)
     * @return array
     */
    private function obtenerPermisosAdmin(): array
    {
        try {
            $PermisosTable = TableRegistry::getTableLocator()->get('Permisos');
            $permisos = $PermisosTable->find()->toArray();
            
            $resultado = [];
            foreach ($permisos as $permiso) {
                // Estructura: controller/action
                // Manejar tanto entidades como arrays
                $controller = is_object($permiso) ? $permiso->controller : $permiso['controller'];
                $action = is_object($permiso) ? $permiso->action : $permiso['action'];
                
                if (!empty($controller) && !empty($action)) {
                    $key = $controller . '/' . $action;
                    $resultado[$key] = true;
                }
            }
            
            // DEBUG: Log del total
            error_log("Admin permisos totales obtenidos: " . count($resultado));
            
            return $resultado;
        } catch (\Exception $e) {
            // Si hay error, devolver permisos por defecto
            error_log("Error en obtenerPermisosAdmin: " . $e->getMessage());
            return $this->obtenerPermisosDefault();
        }
    }

    /**
     * Permisos por defecto si la BD no está disponible
     * IMPORTANTE: Retorna array VACÍO por seguridad
     * No debería permitir nada si hay error en la BD
     * @return array
     */
    private function obtenerPermisosDefault(): array
    {
        // NO devolver permisos por defecto - eso es un riesgo de seguridad
        // Si la BD no está disponible, NO permitir acceso a nada
        return [];
    }

    /**
     * 
     * @param int $userId ID del usuario
     * @param int $rolId ID del rol del usuario
     * @return array
     */
    public function obtenerPermisos(int $userId, int $rolId): array
    {
        try {
            $PermisosTable = TableRegistry::getTableLocator()->get('Permisos');
            $RolesPermisosTable = TableRegistry::getTableLocator()->get('RolesPermisos');
            $UsuariosPermisosTable = TableRegistry::getTableLocator()->get('UsuariosPermisos');

            // 1. Permisos del rol
            $rolesPermisos = $RolesPermisosTable->find()
                ->select(['RolesPermisos.id', 'RolesPermisos.permiso_id'])
                ->where(['RolesPermisos.rol_id' => $rolId])
                ->toArray();

            $permisoIdsDelRol = array_column($rolesPermisos, 'permiso_id');

            // 2. Permisos y negaciones del usuario
            $usuariosPermisos = $UsuariosPermisosTable->find()
                ->select(['UsuariosPermisos.id', 'UsuariosPermisos.permiso_id', 'UsuariosPermisos.allow'])
                ->where(['UsuariosPermisos.usuario_id' => $userId])
                ->toArray();

            // Separar permisos otorgados y negados
            $permisoIdsOtorgados = [];
            $permisoIdsDenegados = [];
            
            foreach ($usuariosPermisos as $up) {
                if ($up->allow) {
                    $permisoIdsOtorgados[] = $up->permiso_id;
                } else {
                    $permisoIdsDenegados[] = $up->permiso_id;
                }
            }

            // 3. Combinar: permisos del rol + permisos otorgados al usuario - permisos denegados
            $permisoIds = array_merge($permisoIdsDelRol, $permisoIdsOtorgados);
            $permisoIds = array_unique($permisoIds);
            
            // 4. RESTAR los permisos denegados explícitamente al usuario
            $permisoIds = array_diff($permisoIds, $permisoIdsDenegados);

            // Si no hay permisos, retornar array vacío
            if (empty($permisoIds)) {
                return [];
            }

            // Obtener detalles de los permisos
            $permisos = $PermisosTable->find()
                ->where(['Permisos.id IN' => $permisoIds])
                ->toArray();

            // Construir resultado
            $resultado = [];
            foreach ($permisos as $permiso) {
                // Manejar tanto entidades como arrays
                $controller = is_object($permiso) ? $permiso->controller : $permiso['controller'];
                $action = is_object($permiso) ? $permiso->action : $permiso['action'];
                
                if (!empty($controller) && !empty($action)) {
                    $key = $controller . '/' . $action;
                    $resultado[$key] = true;
                }
            }

            return $resultado;
        } catch (\Exception $e) {
            // Si hay error en BD, devolver array VACÍO (seguridad)
            return [];
        }
    }

    /**
     * Lógica privada para verificar permiso
     */
    private function hasPermission(int $userId, int $rolId, string $controller, string $action): bool
    {
        $PermisosTable = TableRegistry::getTableLocator()->get('Permisos');
        $RolesPermisosTable = TableRegistry::getTableLocator()->get('RolesPermisos');
        $UsuariosPermisosTable = TableRegistry::getTableLocator()->get('UsuariosPermisos');

        // Buscar el permiso
        $permiso = $PermisosTable->find()
            ->where([
                'controller' => $controller,
                'action' => $action,
            ])
            ->first();

        if (!$permiso) {
            return false; // Si no existe permiso registrado, DENEGAR (era el problema)
        }

        // 1. Verificar permisos del rol
        $rolePermission = $RolesPermisosTable->find()
            ->where([
                'rol_id' => $rolId,
                'permiso_id' => $permiso->id,
            ])
            ->first();

        if ($rolePermission) {
            return true;
        }

        // 2. Verificar permisos directos del usuario (usando 'allow')
        $userPermission = $UsuariosPermisosTable->find()
            ->where([
                'usuario_id' => $userId,
                'permiso_id' => $permiso->id,
                'allow' => true,
            ])
            ->first();

        return $userPermission !== null;
    }
}
