<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\ResumenDiarioService;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Log\Log;

/**
 * Envío automático del Resumen Diario de boletas a SUNAT.
 *
 * Envía, por empresa y de la fecha más antigua a la más reciente, todas las
 * boletas que siguen pendientes (incluye días anteriores que quedaron sin
 * enviar). Usa la misma lógica que el botón "Enviar resumen".
 *
 * Uso:   bin/cake resumen_diario
 *        bin/cake resumen_diario --dry-run   (solo muestra lo que enviaría)
 * Cron:  todos los días a las 23:00 hora de Lima (ver DOCKER_SETUP.md).
 */
class ResumenDiarioCommand extends Command
{
    public static function defaultName(): string
    {
        return 'resumen_diario';
    }

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription('Envía a SUNAT el Resumen Diario de las boletas pendientes.')
            ->addOption('dry-run', [
                'boolean' => true,
                'help' => 'Solo muestra qué se enviaría, sin enviar nada.',
            ])
            ->addOption('company', [
                'help' => 'ID de la empresa (por defecto, todas).',
            ]);
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $servicio = new ResumenDiarioService();
        $dryRun = (bool)$args->getOption('dry-run');
        $hoy = (new \DateTimeImmutable('now', new \DateTimeZone('America/Lima')))->format('Y-m-d');

        $empresas = $this->fetchTable('Companies')->find();
        if ($args->getOption('company')) {
            $empresas->where(['id' => (int)$args->getOption('company')]);
        }

        $huboError = false;
        $this->registrar($io, 'info', 'Inicio del envío automático del resumen diario' . ($dryRun ? ' (simulación)' : ''));

        foreach ($empresas->all() as $empresa) {
            $fechas = array_filter($servicio->fechasPendientes((int)$empresa->id), fn($f) => $f <= $hoy);

            if (empty($fechas)) {
                $this->registrar($io, 'info', "Empresa {$empresa->id}: no hay boletas pendientes.");
                continue;
            }

            if ($dryRun) {
                $this->registrar($io, 'info', "Empresa {$empresa->id}: enviaría las fechas " . implode(', ', $fechas));
                continue;
            }

            foreach ($fechas as $fecha) {
                if (!$this->enviarFecha($servicio, (int)$empresa->id, $fecha, $io)) {
                    // Las fechas siguientes quedarían bloqueadas por esta: se reintenta en la próxima ejecución.
                    $huboError = true;
                    break;
                }
            }
        }

        $this->registrar($io, 'info', 'Fin del envío automático del resumen diario.');

        return $huboError ? static::CODE_ERROR : static::CODE_SUCCESS;
    }

    /**
     * Envía todas las boletas pendientes de una fecha (en tandas de hasta
     * ResumenDiarioService::MAX_BOLETAS). Devuelve false si algo falló.
     */
    private function enviarFecha(ResumenDiarioService $servicio, int $companyId, string $fecha, ConsoleIo $io): bool
    {
        $dia = new \DateTimeImmutable($fecha, new \DateTimeZone('America/Lima'));
        $reintentosSunatOcupada = 3;

        // Tope de seguridad: 20 tandas de 100 boletas por día.
        for ($tanda = 1; $tanda <= 20; $tanda++) {
            try {
                // Sin nadie esperando en pantalla: se consulta el ticket hasta ~2 minutos.
                $r = $servicio->enviar($companyId, $dia, 6, 20);
            } catch (\Throwable $e) {
                $this->registrar($io, 'error', "Empresa {$companyId}, boletas del {$fecha}: " . $e->getMessage());

                return false;
            }

            $detalle = "Empresa {$companyId}, boletas del {$fecha}: [{$r['estado']}] {$r['mensaje']}" .
                ($r['cantidad'] ? " ({$r['cantidad']} boletas)" : '');

            switch ($r['estado']) {
                case 'SIN_BOLETAS':
                    return true;
                case 'ACEPTADO':
                case 'ENVIADO':
                    // ENVIADO: el ticket queda guardado para consultarlo luego.
                    $this->registrar($io, $r['estado'] === 'ACEPTADO' ? 'info' : 'warning', $detalle);
                    continue 2;
                case 'REINTENTAR':
                    $this->registrar($io, 'warning', $detalle);
                    if ($reintentosSunatOcupada-- > 0) {
                        sleep(60);
                        continue 2;
                    }

                    return false;
                default: // RECHAZADO, BLOQUEADO
                    $this->registrar($io, 'error', $detalle);

                    return false;
            }
        }

        return true;
    }

    /**
     * Queda en logs/cli-debug.log (info) o logs/cli-error.log (warning/error); al
     * correr como comando, CakePHP también lo muestra en la consola.
     */
    private function registrar(ConsoleIo $io, string $nivel, string $mensaje): void
    {
        Log::write($nivel, '[resumen_diario] ' . $mensaje);
    }
}
