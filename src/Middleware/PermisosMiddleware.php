<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Cake\ORM\TableRegistry;
use Cake\Http\Exception\ForbiddenException;
use Cake\Core\Configure;

/**
 * Middleware AUTOMÁTICO para verificar permisos de usuario
 * Verifica tanto permisos del rol como permisos directos del usuario
 * Basado en configuración de permisos.php
 */
class PermisosMiddleware implements MiddlewareInterface
{
    private array $config = [];
    private array $permisosCache = [];

    public function __construct()
    {
        // Cargar configuración de permisos
        $config = Configure::read('permisos');
        
        if (is_array($config)) {
            $this->config = $config;
        } elseif (file_exists(CONFIG . 'permisos.php')) {
            // Si no hay config cargada, intentar incluir archivo
            $loadedConfig = include CONFIG . 'permisos.php';
            $this->config = is_array($loadedConfig) ? $loadedConfig : [];
        }
        
        // Si aún está vacío, usar configuración por defecto
        if (empty($this->config)) {
            $this->config = [
                'except' => [
                    'Users' => ['login', 'logout'],
                    'Pages' => ['display'],
                    'Error' => ['index', 'notFound'],
                ],
                'require' => [],
            ];
        }
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // El usuario solo está disponible DESPUÉS de pasar por el Authentication middleware
        $user = $request->getAttribute('identity');
        
        // Si no hay usuario autenticado, dejar pasar (Login, etc)
        if (!$user) {
            return $handler->handle($request);
        }

        // Obtener parámetros de ruta de forma segura
        try {
            $params = $request->getAttribute('routeParameters');
            if (!$params || !is_array($params)) {
                return $handler->handle($request);
            }

            $controller = $params['controller'] ?? null;
            $action = $params['action'] ?? null;

            if (!$controller || !$action) {
                return $handler->handle($request);
            }

            // Verificar si esta acción está exenta de permisos
            if ($this->isExcepted($controller, $action)) {
                return $handler->handle($request);
            }

            // Verificar si esta acción requiere permisos
            if ($this->requiresPermission($controller, $action)) {
                if (!$this->hasPermission($user, $controller, $action)) {
                    throw new ForbiddenException('No tienes permiso para acceder a esta acción');
                }
            }
        } catch (ForbiddenException $e) {
            throw $e; // Re-lanzar excepciones de permiso
        } catch (\Exception $e) {
            // Si hay cualquier otro error, dejar pasar (es más seguro)
            // y registrar el error para debugging
            error_log('PermisosMiddleware error: ' . $e->getMessage());
        }

        return $handler->handle($request);
    }

    /**
     * Verifica si una acción está exenta de verificación
     */
    private function isExcepted(?string $controller, ?string $action): bool
    {
        if (!$controller || !$action) {
            return false;
        }

        $except = $this->config['except'] ?? [];
        
        if (isset($except[$controller]) && in_array($action, $except[$controller])) {
            return true;
        }

        return false;
    }

    /**
     * Verifica si una acción requiere permisos
     */
    private function requiresPermission(?string $controller, ?string $action): bool
    {
        if (!$controller || !$action) {
            return false;
        }

        $require = $this->config['require'] ?? [];
        
        // Si el controlador no está en require, no se necesita permiso
        if (!isset($require[$controller])) {
            return false;
        }

        // Si la lista está vacía, todas las acciones requieren permiso
        if (empty($require[$controller])) {
            return true;
        }

        // Si la acción está en la lista, requiere permiso
        return in_array($action, $require[$controller]);
    }

    /**
     * Verifica si un usuario tiene permiso para acceder a una acción
     * Combina permisos del rol + permisos directos del usuario
     */
    private function hasPermission($user, ?string $controller, ?string $action): bool
    {
        if (!$controller || !$action || !$user) {
            return false;
        }

        // Usar caché para evitar queries repetidas
        $cacheKey = $user->id . '_' . $controller . '_' . $action;
        if (isset($this->permisosCache[$cacheKey])) {
            return $this->permisosCache[$cacheKey];
        }

        try {
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
                // Si no existe permiso registrado en BD, NEGAR por seguridad (whitelist)
                $this->permisosCache[$cacheKey] = false;
                return false;
            }

            // 1. Verificar permisos del rol (compatible con rol_id y rol)
            $rolId = $user->rol_id ?? $user->rol ?? null;
            if ($rolId) {
                $rolePermission = $RolesPermisosTable->find()
                    ->where([
                        'rol_id' => $rolId,
                        'permiso_id' => $permiso->id,
                    ])
                    ->first();

                if ($rolePermission) {
                    $this->permisosCache[$cacheKey] = true;
                    return true;
                }
            }

            // 2. Verificar permisos directos del usuario (usando 'otorgado')
            $userPermission = $UsuariosPermisosTable->find()
                ->where([
                    'usuario_id' => $user->id,
                    'permiso_id' => $permiso->id,
                    'otorgado' => true,
                ])
                ->first();

            $result = $userPermission !== null;
            $this->permisosCache[$cacheKey] = $result;
            return $result;
        } catch (\Exception $e) {
            // Si hay error en BD, permitir acceso (es más seguro que bloquear)
            return true;
        }
    }
}
