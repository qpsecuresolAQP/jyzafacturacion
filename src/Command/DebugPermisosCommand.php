<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\ORM\TableRegistry;

/**
 * Debug Permisos Command
 * 
 * Muestra información de debug sobre usuarios, roles y permisos
 * Uso: bin/cake debug-permisos
 */
class DebugPermisosCommand extends Command
{
    public static $defaultName = 'debug-permisos';

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $UsersTable = TableRegistry::getTableLocator()->get('Users');
        $RolesTable = TableRegistry::getTableLocator()->get('Roles');
        $RolesPermisosTable = TableRegistry::getTableLocator()->get('RolesPermisos');
        $PermisosTable = TableRegistry::getTableLocator()->get('Permisos');

        // Mostrar todos los usuarios
        $io->out('<info>========== USUARIOS ==========</info>');
        $usuarios = $UsersTable->find()->all();
        foreach ($usuarios as $user) {
            $rolNombre = 'SIN ROL';
            if ($user->rol_id) {
                $rol = $RolesTable->get($user->rol_id);
                $rolNombre = $rol->nombre;
            }
            $io->out("ID: {$user->id} | Username: {$user->username} | rol_id: {$user->rol_id} | Rol: $rolNombre");
        }

        // Mostrar permisos por rol
        $io->out('');
        $io->out('<info>========== PERMISOS POR ROL ==========</info>');
        $roles = $RolesTable->find()->all();
        foreach ($roles as $rol) {
            $io->out("<info>Rol: {$rol->nombre} (ID: {$rol->id})</info>");
            
            $rolesPermisos = $RolesPermisosTable->find()
                ->contain(['Permisos'])
                ->where(['rol_id' => $rol->id])
                ->all();

            if ($rolesPermisos->count() === 0) {
                $io->out("  ✗ Sin permisos asignados");
            } else {
                foreach ($rolesPermisos as $rp) {
                    $io->out("  ✓ {$rp->permiso->controller}/{$rp->permiso->action}");
                }
            }
            $io->out('');
        }

        // Total de permisos en BD
        $io->out('<info>========== ESTADÍSTICAS ==========</info>');
        $totalPermisos = $PermisosTable->find()->count();
        $totalRolesPermisos = $RolesPermisosTable->find()->count();
        $io->out("Total de permisos en BD: $totalPermisos");
        $io->out("Total de asignaciones (roles_permisos): $totalRolesPermisos");

        return self::CODE_SUCCESS;
    }
}
