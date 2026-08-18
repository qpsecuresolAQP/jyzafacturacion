<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\ORM\TableRegistry;

/**
 * AsignarPermisosRoles Command
 * 
 * Asigna automáticamente permisos por defecto a cada rol
 * Uso: bin/cake asignar-permisos-roles
 */
class AsignarPermisosRolesCommand extends Command
{
    public static $defaultName = 'asignar-permisos-roles';

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $io->out('<info>Asignando permisos por defecto a los roles...</info>');
        $io->out('');

        $PermisosTable = TableRegistry::getTableLocator()->get('Permisos');
        $RolesPermisosTable = TableRegistry::getTableLocator()->get('RolesPermisos');

        // Definir permisos por rol
        $permisosRoles = [
            // Recepcionista (rol_id = 2)
            2 => [
                'Pacientes' => ['index', 'view', 'add', 'edit'],
                'Citas' => ['index', 'view', 'add', 'edit', 'citaDiaria'],
                'Consultas' => ['index', 'view'],
                'Medicamentos' => ['index', 'view'],
                'Recetas' => ['view'],
                'HistoriasClinicas' => ['index'],
                'Transacciones' => ['index'],
                'VistaPacientesCampanas' => ['index'],
                'VistaReportePacientes' => ['index'],
            ],
            // Especialista/Doctor (rol_id = 3)
            3 => [
                'Pacientes' => ['index', 'view'],
                'Citas' => ['index', 'view', 'add', 'edit', 'delete', 'citaDiaria', 'reportecitas'],
                'Consultas' => ['index', 'view', 'add', 'edit', 'delete'],
                'Recetas' => ['index', 'view', 'add', 'edit', 'delete'],
                'RecetasMedicamentos' => ['index'],
                'Medicamentos' => ['index', 'view'],
                'Procedimientos' => ['index', 'view'],
                'Tratamientos' => ['index'],
                'Embarazos' => ['index', 'view', 'add', 'edit'],
                'ConsultasGinecologicas' => ['index'],
                'ExamenesFisicos' => ['index'],
                'Documentos' => ['index'],
                'DocumentosConsultas' => ['index'],
                'HistoriasClinicas' => ['index'],
                'VistaConsultasProcedimientos' => ['index'],
            ],
        ];

        $asignados = 0;
        $yaExisten = 0;
        $errores = 0;

        foreach ($permisosRoles as $rolId => $controllers) {
            $nombreRol = $rolId === 2 ? 'Recepcionista' : 'Especialista/Doctor';
            $io->out("<info>Rol: $nombreRol (ID: $rolId)</info>");

            foreach ($controllers as $controller => $acciones) {
                foreach ($acciones as $action) {
                    try {
                        // Buscar el permiso
                        $permiso = $PermisosTable->find()
                            ->where([
                                'controller' => $controller,
                                'action' => $action,
                            ])
                            ->first();

                        if (!$permiso) {
                            $io->error("  ✗ Permiso no encontrado: $controller/$action");
                            $errores++;
                            continue;
                        }

                        // Verificar si ya está asignado
                        $yaAsignado = $RolesPermisosTable->find()
                            ->where([
                                'rol_id' => $rolId,
                                'permiso_id' => $permiso->id,
                            ])
                            ->first();

                        if ($yaAsignado) {
                            $io->out("  ✓ Ya existe: $controller/$action");
                            $yaExisten++;
                        } else {
                            // Crear la asignación
                            $entity = $RolesPermisosTable->newEntity([
                                'rol_id' => $rolId,
                                'permiso_id' => $permiso->id,
                            ]);

                            if ($RolesPermisosTable->save($entity)) {
                                $io->out("  ✓ Asignado: $controller/$action");
                                $asignados++;
                            } else {
                                $io->error("  ✗ Error al asignar: $controller/$action");
                                $errores++;
                            }
                        }
                    } catch (\Exception $e) {
                        $io->error("  ✗ Error: " . $e->getMessage());
                        $errores++;
                    }
                }
            }
            $io->out('');
        }

        $io->out('<success>Resumen:</success>');
        $io->out("  Permisos asignados: $asignados");
        $io->out("  Permisos que ya existían: $yaExisten");
        $io->out("  Errores: $errores");

        return self::CODE_SUCCESS;
    }
}
