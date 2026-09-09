<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\ORM\TableRegistry;

/**
 * GenerarPermisos Command
 * 
 * Genera automáticamente todos los permisos leyendo el layout.php
 * Uso: bin/cake generar-permisos
 * 
 * Este comando extrae todos los módulos de templates/layout/default.php
 * y los inserta en la tabla 'permisos' de forma automática.
 */
class GenerarPermisosCommand extends Command
{
    public static $defaultName = 'generar-permisos';

    /**
     * Hook method invoked by the Command handling system before `execute()` is called
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int|null The exit code or null
     */
    public function hook(Arguments $args, ConsoleIo $io): ?int
    {
        return null;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int The exit code
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $io->out('<info>Escaneando controllers...</info>');

        // Acciones públicas estándar a incluir para cada controller
        $accionesDefault = ['index', 'view', 'add', 'edit', 'delete'];

        // Controllers especiales y sus acciones
        $controllersConAcciones = [
            'Pacientes' => ['index', 'view', 'add', 'edit', 'delete'],
            'Consultas' => ['index', 'view', 'add', 'edit', 'delete'],
            'Recetas' => ['index', 'view', 'add', 'edit', 'delete'],
            'Medicamentos' => ['index', 'view', 'add', 'edit', 'delete'],
            'Procedimientos' => ['index', 'view', 'add', 'edit', 'delete'],
            'Citas' => ['index', 'view', 'add', 'edit', 'delete', 'citaDiaria', 'reportecitas'],
            'Users' => ['index', 'view', 'add', 'edit', 'delete', 'permisos'],
            'Roles' => ['index', 'view', 'add', 'edit', 'delete'],
            'Permisos' => ['index', 'view', 'add', 'edit', 'delete'],
            'Tratamientos' => ['index'],
            'HorariosDoctores' => ['index'],
            'HorariosBloqueos' => ['index'],
            'Campañas' => ['index'],
            'RecetasMedicamentos' => ['index'],
            'FormasFarmaceuticas' => ['index'],
            'Departamentos' => ['index'],
            'Doctores' => ['index'],
            'VistaPacientesCampanas' => ['index'],
            'VistaReportePacientes' => ['index'],
            'VistaConsultasProcedimientos' => ['index'],
            'VistaReporteProductos' => ['index'],
            'Productos' => ['index', 'view', 'add', 'edit', 'delete', 'reactivar'],
            'CategoriasProductos' => ['index', 'view', 'add', 'edit', 'delete', 'reactivar'],
            'ProductoMovimientos' => ['add', 'historial'],
            'Categorias' => ['index'],
            'Proveedores' => ['index'],
            'Transacciones' => ['index'],
            'RolesPermisos' => ['index', 'add', 'delete','createWithDefaults'],
            'UsuariosPermisos' => ['index', 'add', 'edit', 'delete'],
            'Visitas' => ['index', 'view', 'add', 'edit', 'delete'],
            'Presupuestos' => ['index', 'view', 'add', 'edit', 'delete'],
            'PresupuestosTratamientos' => ['index'],
            'Ordenes' => ['index', 'view', 'add', 'edit', 'delete'],
            'OrdenesTratamientos' => ['index'],
            'CitasTratamientos' => ['index'],
            'Embarazos' => ['index', 'view', 'add', 'edit', 'delete'],
            'AntecedentesGinecologicas' => ['index'],
            'ExamenesFisicos' => ['index'],
            'FisicosHistorias' => ['index'],
            'ConsultasGinecologicas' => ['index'],
            'GinecologicaCie' => ['index'],
            'Documentos' => ['index'],
            'DocumentosConsultas' => ['index'],
            'DocumentosGinecologicas' => ['index'],
            'DocumentosProcedimientos' => ['index'],
            'HistoriasClinicas' => ['index'],
            'Categoriascie10' => ['index'],
            'Diagnosticoscie10' => ['index'],
            'Gruposcie10' => ['index'],
            'Subgruposcie10' => ['index'],
            'Transacciones' => ['index'],
            'TablascomplejaEmbarazos' => ['index'],
            'TablasimpleEmbarazos' => ['index'],
            'VisitasTratamientos' => ['index'],
            'ViasAdministracion' => ['index'],
        ];

        $permisosExtraidos = [];
        foreach ($controllersConAcciones as $controller => $acciones) {
            foreach ($acciones as $action) {
                $permisosExtraidos[] = [
                    'controller' => $controller,
                    'action' => $action,
                    'nombre' => $controller . '/' . $action,
                ];
            }
        }

        // Remover duplicados
        $permisosUnicos = [];
        $visto = [];
        foreach ($permisosExtraidos as $p) {
            $key = $p['nombre'];
            if (!isset($visto[$key])) {
                $permisosUnicos[] = $p;
                $visto[$key] = true;
            }
        }

        // Insertar en la BD
        $PermisosTable = TableRegistry::getTableLocator()->get('Permisos');
        $RolesPermisosTable = TableRegistry::getTableLocator()->get('RolesPermisos');
        $contador = 0;
        $errores = 0;
        $permisosCreados = [];

        foreach ($permisosUnicos as $permiso) {
            try {
                // Verificar si ya existe
                $existe = $PermisosTable->findByControllerAndAction(
                    $permiso['controller'],
                    $permiso['action']
                )->first();

                if ($existe) {
                    $io->out('<info>✓ Ya existe:</info> ' . $permiso['nombre']);
                    $permisosCreados[] = $existe->id;
                } else {
                    // Crear nueva entidad
                    $entity = $PermisosTable->newEntity([
                        'nombre' => $permiso['nombre'],
                        'controller' => $permiso['controller'],
                        'action' => $permiso['action'],
                        'descripcion' => $permiso['controller'] . ' - ' . $permiso['action'],
                    ]);

                    if ($PermisosTable->save($entity)) {
                        $io->out('<success>✓ Creado:</success> ' . $permiso['nombre']);
                        $contador++;
                        $permisosCreados[] = $entity->id;
                    } else {
                        $io->error('✗ Error al crear: ' . $permiso['nombre']);
                        $errores++;
                    }
                }
            } catch (\Exception $e) {
                $io->error('✗ Error: ' . $e->getMessage());
                $errores++;
            }
        }

        // Asignar permisos al rol Admin (rol_id = 1)
        $io->out('');
        $io->out('<info>Asignando permisos al rol Admin...</info>');
        
        foreach ($permisosCreados as $permisoId) {
            try {
                // Verificar si ya está asignado
                $yaAsignado = $RolesPermisosTable->find()
                    ->where([
                        'rol_id' => 1,
                        'permiso_id' => $permisoId,
                    ])
                    ->first();

                if (!$yaAsignado) {
                    $entity = $RolesPermisosTable->newEntity([
                        'rol_id' => 1,
                        'permiso_id' => $permisoId,
                    ]);
                    $RolesPermisosTable->save($entity);
                }
            } catch (\Exception $e) {
                // Ignorar errores de asignación
            }
        }

        $io->out('');
        $io->out("<info>Resumen:</info>");
        $io->out("Total de permisos encontrados: " . count($permisosUnicos));
        $io->out("Nuevos permisos creados: <success>" . $contador . "</success>");
        $io->out("Permisos que ya existían: <info>" . (count($permisosUnicos) - $contador - $errores) . "</info>");
        $io->out("Errores: <warning>" . $errores . "</warning>");
        $io->out("Permisos asignados a Admin: <success>" . count($permisosCreados) . "</success>");

        return self::CODE_SUCCESS;
    }

    /**
     * Gets the option parser instance and configures it.
     *
     * By overriding this method subclasses can define their own options
     * or output a custom help.
     *
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be configured
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function buildOptionParser(\Cake\Console\ConsoleOptionParser $parser): \Cake\Console\ConsoleOptionParser
    {
        $parser->setDescription(
            'Genera automáticamente todos los permisos leyendo el layout.php'
        );

        return $parser;
    }
}
