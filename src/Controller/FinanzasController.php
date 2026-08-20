<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Panel consolidado de finanzas: ingresos, egresos, pagos a doctores y
 * pagos a laboratorios dentro de un rango de fechas, con el balance neto.
 */
class FinanzasController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $currentAction = $this->request->getParam('action');

        $this->verificarPermisoORedireccionar('Finanzas', $currentAction);
    }

    public function index()
    {
        $fechaHoy = date('Y-m-d');
        $fechaDesde = $this->request->getQuery('fecha_desde') ?: $fechaHoy;
        $fechaHasta = $this->request->getQuery('fecha_hasta') ?: $fechaHoy;

        $datos = $this->buildResumenFinanciero($fechaDesde, $fechaHasta);

        $this->set(compact('fechaDesde', 'fechaHasta') + $datos);
    }

    private function buildResumenFinanciero(string $fechaDesde, string $fechaHasta): array
    {
        // ── Ingresos por ventas/facturas (caja_movimientos), excluyendo
        // facturas ANULADAS: el pago existe pero la venta fue revertida. ──
        $movimientos = $this->fetchTable('CajaMovimientos')->find()
            ->contain(['Invoices'])
            ->innerJoinWith('Invoices')
            ->where([
                'Invoices.estado !=' => 'ANULADO',
                'DATE(CajaMovimientos.created) >=' => $fechaDesde,
                'DATE(CajaMovimientos.created) <=' => $fechaHasta,
            ])
            ->all();

        $ingresosPorMetodo = [];
        $totalIngresosVentas = 0.0;
        $movimientosDetalle = [];

        foreach ($movimientos as $mov) {
            $metodo = $mov->metodo_pago ?? 'OTROS';
            $monto = (float) $mov->monto_recibido;

            if (!isset($ingresosPorMetodo[$metodo])) {
                $ingresosPorMetodo[$metodo] = ['metodo' => $metodo, 'total' => 0.0, 'cantidad' => 0];
            }
            $ingresosPorMetodo[$metodo]['total'] += $monto;
            $ingresosPorMetodo[$metodo]['cantidad']++;

            $totalIngresosVentas += $monto;

            $movimientosDetalle[] = [
                'fecha' => $mov->created,
                'comprobante' => ($mov->invoice->serie ?? 'RI') . '-' . ($mov->invoice->correlativo ?? $mov->invoice->id),
                'cliente' => $mov->invoice->cliente_nombre ?? '-',
                'metodo' => $metodo,
                'monto' => $monto,
            ];
        }

        // ── Ingresos manuales de caja (caja_ingresos), aparte de ventas. ──
        $ingresosManuales = $this->fetchTable('CajaIngresos')->find()
            ->where([
                'DATE(CajaIngresos.created) >=' => $fechaDesde,
                'DATE(CajaIngresos.created) <=' => $fechaHasta,
            ])
            ->all();

        $totalIngresosManuales = 0.0;
        $ingresosManualesDetalle = [];
        foreach ($ingresosManuales as $ing) {
            $monto = (float) $ing->monto;
            $totalIngresosManuales += $monto;
            $ingresosManualesDetalle[] = [
                'fecha' => $ing->created,
                'descripcion' => $ing->descripcion ?? '-',
                'monto' => $monto,
            ];
        }

        $totalIngresos = round($totalIngresosVentas + $totalIngresosManuales, 2);

        // ── Egresos de caja (gastos y reembolsos por anulación). ──
        $egresos = $this->fetchTable('CajaEgresos')->find()
            ->where([
                'DATE(CajaEgresos.created) >=' => $fechaDesde,
                'DATE(CajaEgresos.created) <=' => $fechaHasta,
            ])
            ->all();

        // Los REEMBOLSO_ANULACION no son un gasto real del negocio: son la
        // devolución al paciente del dinero de una factura anulada, cuyo
        // ingreso original ya se excluye del total de ingresos. Contarlos
        // como egreso restaría dos veces el mismo movimiento (una vez al
        // excluir el ingreso, otra al sumar el "gasto" del reembolso), así
        // que se muestran aparte, solo informativos, sin afectar el balance.
        $totalEgresos = 0.0;
        $totalReembolsosAnulacion = 0.0;
        $egresosPorTipo = [];
        $egresosDetalle = [];
        $reembolsosAnulacionDetalle = [];
        foreach ($egresos as $egr) {
            $monto = (float) $egr->monto;
            $tipo = $egr->tipo ?? 'GASTO';

            if ($tipo === 'REEMBOLSO_ANULACION') {
                $totalReembolsosAnulacion += $monto;
                $reembolsosAnulacionDetalle[] = [
                    'fecha' => $egr->created,
                    'metodo' => $egr->metodo_pago ?? '-',
                    'descripcion' => $egr->descripcion ?? '-',
                    'monto' => $monto,
                ];
                continue;
            }

            $totalEgresos += $monto;

            if (!isset($egresosPorTipo[$tipo])) {
                $egresosPorTipo[$tipo] = ['tipo' => $tipo, 'total' => 0.0, 'cantidad' => 0];
            }
            $egresosPorTipo[$tipo]['total'] += $monto;
            $egresosPorTipo[$tipo]['cantidad']++;

            $egresosDetalle[] = [
                'fecha' => $egr->created,
                'tipo' => $tipo,
                'metodo' => $egr->metodo_pago ?? '-',
                'descripcion' => $egr->descripcion ?? '-',
                'monto' => $monto,
            ];
        }
        $totalEgresos = round($totalEgresos, 2);
        $totalReembolsosAnulacion = round($totalReembolsosAnulacion, 2);

        // ── Pagos a doctores, filtrados por la fecha en que se registró
        // (pagó) el historial, no por el periodo que cubría. ──
        $pagosDoctores = $this->fetchTable('PagosDoctoresHistorial')->find()
            ->contain(['Doctores'])
            ->where([
                'DATE(PagosDoctoresHistorial.created) >=' => $fechaDesde,
                'DATE(PagosDoctoresHistorial.created) <=' => $fechaHasta,
            ])
            ->order(['PagosDoctoresHistorial.created' => 'DESC'])
            ->all();

        $totalPagosDoctores = 0.0;
        $pagosDoctoresDetalle = [];
        $pagosDoctoresPorDoctor = [];
        foreach ($pagosDoctores as $pago) {
            $monto = (float) $pago->monto_total;
            $totalPagosDoctores += $monto;

            $doctorNombre = trim((string) ($pago->doctore->nombre ?? '') . ' ' . (string) ($pago->doctore->apellido ?? ''));
            $doctorId = $pago->doctor_id;

            if (!isset($pagosDoctoresPorDoctor[$doctorId])) {
                $pagosDoctoresPorDoctor[$doctorId] = ['doctor' => $doctorNombre ?: '-', 'total' => 0.0, 'cantidad' => 0];
            }
            $pagosDoctoresPorDoctor[$doctorId]['total'] += $monto;
            $pagosDoctoresPorDoctor[$doctorId]['cantidad']++;

            $pagosDoctoresDetalle[] = [
                'id' => $pago->id,
                'fecha' => $pago->created,
                'doctor' => $doctorNombre ?: '-',
                'monto' => $monto,
                'comprobantes' => (int) $pago->total_comprobantes,
            ];
        }
        $totalPagosDoctores = round($totalPagosDoctores, 2);

        // ── Pagos a laboratorios, mismo criterio de fecha. ──
        $pagosLaboratorios = $this->fetchTable('PagosLaboratoriosHistorial')->find()
            ->contain(['Laboratorios'])
            ->where([
                'DATE(PagosLaboratoriosHistorial.created) >=' => $fechaDesde,
                'DATE(PagosLaboratoriosHistorial.created) <=' => $fechaHasta,
            ])
            ->order(['PagosLaboratoriosHistorial.created' => 'DESC'])
            ->all();

        $totalPagosLaboratorios = 0.0;
        $pagosLaboratoriosDetalle = [];
        $pagosLaboratoriosPorLab = [];
        foreach ($pagosLaboratorios as $pago) {
            $monto = (float) $pago->monto_total;
            $totalPagosLaboratorios += $monto;

            $labNombre = $pago->laboratorio->nombre ?? '-';
            $labId = $pago->laboratorio_id;

            if (!isset($pagosLaboratoriosPorLab[$labId])) {
                $pagosLaboratoriosPorLab[$labId] = ['laboratorio' => $labNombre, 'total' => 0.0, 'cantidad' => 0];
            }
            $pagosLaboratoriosPorLab[$labId]['total'] += $monto;
            $pagosLaboratoriosPorLab[$labId]['cantidad']++;

            $pagosLaboratoriosDetalle[] = [
                'id' => $pago->id,
                'fecha' => $pago->created,
                'laboratorio' => $labNombre,
                'monto' => $monto,
                'comprobantes' => (int) $pago->total_comprobantes,
            ];
        }
        $totalPagosLaboratorios = round($totalPagosLaboratorios, 2);

        // ── Balance neto: ingresos − egresos − pagos a doctores/laboratorios. ──
        $totalSalidas = round($totalEgresos + $totalPagosDoctores + $totalPagosLaboratorios, 2);
        $balanceNeto = round($totalIngresos - $totalSalidas, 2);

        return [
            'totalIngresosVentas' => round($totalIngresosVentas, 2),
            'totalIngresosManuales' => round($totalIngresosManuales, 2),
            'totalIngresos' => $totalIngresos,
            'ingresosPorMetodo' => array_values($ingresosPorMetodo),
            'ingresosManualesDetalle' => $ingresosManualesDetalle,
            'movimientosDetalle' => $movimientosDetalle,

            'totalEgresos' => $totalEgresos,
            'egresosPorTipo' => array_values($egresosPorTipo),
            'egresosDetalle' => $egresosDetalle,
            'totalReembolsosAnulacion' => $totalReembolsosAnulacion,
            'reembolsosAnulacionDetalle' => $reembolsosAnulacionDetalle,

            'totalPagosDoctores' => $totalPagosDoctores,
            'pagosDoctoresPorDoctor' => array_values($pagosDoctoresPorDoctor),
            'pagosDoctoresDetalle' => $pagosDoctoresDetalle,

            'totalPagosLaboratorios' => $totalPagosLaboratorios,
            'pagosLaboratoriosPorLab' => array_values($pagosLaboratoriosPorLab),
            'pagosLaboratoriosDetalle' => $pagosLaboratoriosDetalle,

            'totalSalidas' => $totalSalidas,
            'balanceNeto' => $balanceNeto,
        ];
    }
}
