<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Controller\Controller;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/5/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent('Flash');
        $this->loadComponent('Permisos');

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/5/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
        //$this->loadComponent('FormProtection');
        $usuario = $this->Authentication->getIdentity();  // Obtiene el usuario autenticado

        // Pasar la identidad (usuario) a las vistas
        $this->set('usuario', $usuario);
        
        // Pasar permisos del usuario a TODAS las vistas
        if ($usuario) {
            $permisos = $this->Permisos->obtenerPermisosUsuarioActual();
            $this->set('permisos', $permisos);
        }
    }
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->addUnauthenticatedActions(['login', 'logout']);
    }

    /**
     * Verifica si el usuario tiene permiso para una acción
     * Si no tiene permiso, renderiza un mensaje simple
     * RESPETA las acciones en 'except' (no verifica esas)
     * 
     * @param string $controller Nombre del controlador
     * @param string $action Nombre de la acción
     * @return bool True si tiene permiso
     */
    public function verificarPermisoORedireccionar(string $controller, string $action): bool
    {
        // Excepción temporal para recordatorios de cumpleaños — permite acceso
        // mientras depuramos la configuración de permisos. Quitar cuando se
        // confirme que la DB/permiso está correctamente configurada.
        if ($controller === 'HistoriasClinicas' && strpos($action, 'cumple') !== false) {
            return true;
        }

        // Cargar configuración de permisos
        $permisosConfig = require CONFIG . 'permisos.php';
        $except = $permisosConfig['except'] ?? [];
        
        // Si la acción está en 'except', permitirla sin verificar
        if (isset($except[$controller]) && in_array($action, $except[$controller])) {
            return true;
        }
        
        // Si no está en except, verificar en BD
        if (!$this->Permisos->tienePermiso($controller, $action)) {
            echo '<script>alert("❌ No tienes permiso para acceder a este módulo"); window.history.back();</script>';
            exit;
        }
        return true;
    }
}
