<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;

/**
 * Helper para gestionar permisos en vistas
 * Permite mostrar/ocultar elementos basado en los permisos del usuario
 */
class PermisosHelper extends Helper
{
    protected array $helpers = ['Url'];

    /**
     * Verifica si el usuario tiene un rol específico (compatible con 'rol' y 'rol_id')
     * 
     * @param int|array $roles ID(s) de rol a verificar
     * @return bool
     */
    public function tieneRol($roles): bool
    {
        $usuario = $this->getView()->get('usuario');
        if (!$usuario) {
            return false;
        }

        // Asegurar que roles sea array
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        // Intentar con rol_id primero (nuevo sistema)
        if (isset($usuario->rol_id)) {
            return in_array($usuario->rol_id, $roles);
        }

        // Fallback a rol (sistema antiguo)
        if (isset($usuario->rol)) {
            return in_array($usuario->rol, $roles);
        }

        return false;
    }

    /**
     * Verifica si el usuario tiene un permiso específico
     * 
     * @param string $controller Nombre del controlador
     * @param string $action Nombre de la acción
     * @return bool
     */
    public function tiene(string $controller, string $action): bool
    {
        $permisos = $this->getView()->get('permisos') ?? [];
        $key = $controller . '/' . $action;
        return isset($permisos[$key]);
    }

    /**
     * Renderiza un elemento de menú si el usuario tiene permiso
     * 
     * @param string $label Etiqueta del menú
     * @param string $controller Controlador
     * @param string $action Acción
     * @param string $icon Clase de icono (Font Awesome)
     * @return string HTML del elemento de menú o string vacío
     */
    public function menuItem(
        string $label,
        string $controller,
        string $action,
        string $icon = 'fas fa-link'
    ): string {
        if (!$this->tiene($controller, $action)) {
            return '';
        }

        $url = $this->Url->build(['controller' => $controller, 'action' => $action]);
        
        return sprintf(
            '<li class="nav-item"><a href="%s" class="nav-link"><i class="%s nav-icon"></i><p>%s</p></a></li>',
            $url,
            htmlspecialchars($icon),
            htmlspecialchars($label)
        );
    }

    /**
     * Renderiza un botón si el usuario tiene permiso
     * 
     * @param string $label Etiqueta del botón
     * @param string $controller Controlador
     * @param string $action Acción
     * @param mixed $id ID opcional para acciones como edit/delete
     * @param string $class Clases CSS del botón
     * @param string $icon Icono opcional
     * @return string HTML del botón o string vacío
     */
    public function boton(
        string $label,
        string $controller,
        string $action,
        mixed $id = null,
        string $class = 'btn btn-primary',
        string $icon = ''
    ): string {
        if (!$this->tiene($controller, $action)) {
            return '';
        }

        $url = $id 
            ? $this->Url->build(['controller' => $controller, 'action' => $action, $id])
            : $this->Url->build(['controller' => $controller, 'action' => $action]);
        
        $iconHtml = $icon ? sprintf('<i class="%s"></i> ', htmlspecialchars($icon)) : '';
        
        return sprintf(
            '<a href="%s" class="%s">%s%s</a>',
            $url,
            htmlspecialchars($class),
            $iconHtml,
            htmlspecialchars($label)
        );
    }

    /**
     * Retorna todos los permisos del usuario
     * 
     * @return array
     */
    public function obtenerPermisos(): array
    {
        return $this->getView()->get('permisos') ?? [];
    }

    /**
     * Retorna true si el usuario tiene TODOS los permisos especificados
     * 
     * @param array $permisos Array de permisos [['controller' => 'Pacientes', 'action' => 'index'], ...]
     * @return bool
     */
    public function tieneMultiples(array $permisos): bool
    {
        foreach ($permisos as $permiso) {
            if (!$this->tiene($permiso['controller'], $permiso['action'])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Retorna true si el usuario tiene AL MENOS UNO de los permisos especificados
     * 
     * @param array $permisos Array de permisos
     * @return bool
     */
    public function tieneAlguno(array $permisos): bool
    {
        foreach ($permisos as $permiso) {
            if ($this->tiene($permiso['controller'], $permiso['action'])) {
                return true;
            }
        }
        return false;
    }
}
