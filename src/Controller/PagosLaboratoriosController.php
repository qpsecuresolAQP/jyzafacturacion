<?php
declare(strict_types=1);

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Cake\Routing\Router;

class PagosLaboratoriosController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $currentAction = $this->request->getParam('action');

        $this->verificarPermisoORedireccionar('PagosLaboratorios', $currentAction);
    }

    public function index()
    {
        $laboratorioId = $this->request->getQuery('laboratorio_id');
        $fechaHoy = date('Y-m-d');
        $fechaDesde = $this->request->getQuery('fecha_desde') ?: $fechaHoy;
        $fechaHasta = $this->request->getQuery('fecha_hasta') ?: $fechaHoy;

        $datos = $this->buildPagosLaboratoriosReport($laboratorioId, $fechaDesde, $fechaHasta);

        $laboratorios = $this->fetchTable('Laboratorios')->find('list', [
            'keyField' => 'id',
            'valueField' => 'nombre',
            'order' => ['nombre' => 'ASC'],
        ])->toArray();

        $this->set(compact(
            'laboratorios',
            'laboratorioId',
            'fechaDesde',
            'fechaHasta'
        ) + $datos);
    }

    /**
     * Pantalla para registrar el pago a un laboratorio, seleccionando
     * individualmente qué distribuciones (de qué comprobantes) se pagan hoy.
     */
    public function registrarPago()
    {
        $laboratorioId = $this->request->getQuery('laboratorio_id');
        $fechaHoy = date('Y-m-d');
        $fechaDesde = $this->request->getQuery('fecha_desde') ?: $fechaHoy;
        $fechaHasta = $this->request->getQuery('fecha_hasta') ?: $fechaHoy;

        $laboratorios = $this->fetchTable('Laboratorios')->find('list', [
            'keyField' => 'id',
            'valueField' => 'nombre',
            'order' => ['nombre' => 'ASC'],
        ])->toArray();

        $distribuciones = [];
        $totalPendiente = 0.0;

        if (!empty($laboratorioId)) {
            $distribuciones = $this->buildDistribucionesPagables((int) $laboratorioId, $fechaDesde, $fechaHasta);
            $totalPendiente = array_sum(array_map(fn($d) => $d['monto'], $distribuciones));
        }

        $this->set(compact(
            'laboratorios',
            'laboratorioId',
            'fechaDesde',
            'fechaHasta',
            'distribuciones',
            'totalPendiente'
        ));
    }

    public function guardarPago()
    {
        $this->request->allowMethod(['post']);

        $laboratorioId = (int) $this->request->getData('laboratorio_id');
        $fechaDesde = (string) $this->request->getData('fecha_desde');
        $fechaHasta = (string) $this->request->getData('fecha_hasta');
        $observaciones = trim((string) $this->request->getData('observaciones', ''));
        $distribucionIds = array_map('intval', (array) $this->request->getData('invoice_distribucion_ids', []));

        if (!$laboratorioId || !$fechaDesde || !$fechaHasta || empty($distribucionIds)) {
            $this->Flash->error('Selecciona al menos una distribución para registrar el pago.');
            return $this->redirect(['action' => 'registrarPago', '?' => [
                'laboratorio_id' => $laboratorioId,
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
            ]]);
        }

        // Recalcular en el servidor las distribuciones pagables de este laboratorio/rango
        // (nunca confiar en montos del cliente) y quedarnos solo con las
        // seleccionadas por el usuario.
        $disponibles = $this->buildDistribucionesPagables($laboratorioId, $fechaDesde, $fechaHasta);
        $seleccionadas = array_values(array_filter(
            $disponibles,
            fn($d) => in_array($d['invoice_distribucion_id'], $distribucionIds, true)
        ));

        if (empty($seleccionadas)) {
            $this->Flash->warning('Las distribuciones seleccionadas ya no están disponibles (quizás ya fueron pagadas).');
            return $this->redirect(['action' => 'registrarPago', '?' => [
                'laboratorio_id' => $laboratorioId,
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
            ]]);
        }

        $identity = $this->request->getAttribute('identity');
        $userId = $identity ? (int) $identity->getIdentifier() : null;

        $PagosLaboratoriosHistorial = $this->fetchTable('PagosLaboratoriosHistorial');
        $PagosLaboratoriosHistorialDistribuciones = $this->fetchTable('PagosLaboratoriosHistorialDistribuciones');

        $montoTotal = array_sum(array_map(fn($d) => (float) $d['monto'], $seleccionadas));

        $pagoHistorialId = null;

        $conn = $PagosLaboratoriosHistorial->getConnection();
        $ok = $conn->transactional(function () use (
            $PagosLaboratoriosHistorial,
            $PagosLaboratoriosHistorialDistribuciones,
            $laboratorioId,
            $userId,
            $fechaDesde,
            $fechaHasta,
            $montoTotal,
            $seleccionadas,
            $observaciones,
            &$pagoHistorialId
        ) {
            $pagoHistorial = $PagosLaboratoriosHistorial->newEntity([
                'laboratorio_id' => $laboratorioId,
                'user_id' => $userId,
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
                'monto_total' => $montoTotal,
                'total_comprobantes' => count($seleccionadas),
                'observaciones' => $observaciones !== '' ? $observaciones : null,
            ]);

            if (!$PagosLaboratoriosHistorial->save($pagoHistorial)) {
                throw new \RuntimeException('No se pudo registrar el pago: ' . json_encode($pagoHistorial->getErrors()));
            }

            $pagoHistorialId = $pagoHistorial->id;

            foreach ($seleccionadas as $dist) {
                $detalle = $PagosLaboratoriosHistorialDistribuciones->newEntity([
                    'pago_historial_id' => $pagoHistorial->id,
                    'invoice_distribucion_id' => $dist['invoice_distribucion_id'],
                    'invoice_id' => $dist['invoice_id'],
                    'monto_pagado' => $dist['monto'],
                    'conceptos' => json_encode([
                        [
                            'descripcion' => $dist['descripcion'] ?? '-',
                            'monto' => $dist['monto'],
                        ],
                    ], JSON_UNESCAPED_UNICODE),
                ]);

                if (!$PagosLaboratoriosHistorialDistribuciones->save($detalle)) {
                    throw new \RuntimeException(
                        'No se pudo vincular la distribución del comprobante ' .
                        $dist['comprobante'] . ': ' . json_encode($detalle->getErrors())
                    );
                }
            }

            return true;
        });

        if ($ok) {
            $this->Flash->success(sprintf(
                'Pago registrado: S/ %.2f por %d comprobante(s).',
                $montoTotal,
                count($seleccionadas)
            ));
            return $this->redirect(['action' => 'pdfPago', $pagoHistorialId]);
        }

        $this->Flash->error('No se pudo registrar el pago.');

        return $this->redirect(['action' => 'registrarPago', '?' => [
            'laboratorio_id' => $laboratorioId,
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
        ]]);
    }

    public function historial()
    {
        $laboratorioId = $this->request->getQuery('laboratorio_id');

        $query = $this->fetchTable('PagosLaboratoriosHistorial')->find()
            ->contain(['Laboratorios', 'Users'])
            ->order(['PagosLaboratoriosHistorial.created' => 'DESC']);

        if (!empty($laboratorioId)) {
            $query->where(['PagosLaboratoriosHistorial.laboratorio_id' => (int) $laboratorioId]);
        }

        $pagosHistorial = $this->paginate($query);

        $laboratorios = $this->fetchTable('Laboratorios')->find('list', [
            'keyField' => 'id',
            'valueField' => 'nombre',
            'order' => ['nombre' => 'ASC'],
        ])->toArray();

        $this->set(compact('pagosHistorial', 'laboratorios', 'laboratorioId'));
    }

    public function historialDetalle($id)
    {
        $pagoHistorial = $this->fetchTable('PagosLaboratoriosHistorial')->get($id, [
            'contain' => [
                'Laboratorios',
                'Users',
                'PagosLaboratoriosHistorialDistribuciones' => ['Invoices', 'InvoiceDistribuciones'],
            ],
        ]);

        $this->set(compact('pagoHistorial'));
    }

    /**
     * Comprobante en PDF de un pago a laboratorio ya registrado: quién pagó
     * (usuario que lo registró), a quién (laboratorio), cuándo, y el detalle
     * de cada comprobante/distribución incluida.
     */
    public function pdfPago($id)
    {
        $pagoHistorial = $this->fetchTable('PagosLaboratoriosHistorial')->get($id, [
            'contain' => [
                'Laboratorios',
                'Users',
                'PagosLaboratoriosHistorialDistribuciones' => ['Invoices', 'InvoiceDistribuciones'],
            ],
        ]);

        $logoUrl = Router::url('/img/logoJyza.webp', true);

        $this->viewBuilder()->disableAutoLayout();
        $this->set(compact('pagoHistorial', 'logoUrl'));

        $html = $this->render('pdf_pago')->getBody()->__toString();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = sprintf('pago_laboratorio_%d.pdf', $pagoHistorial->id);

        return $this->response
            ->withType('application/pdf')
            ->withStringBody($dompdf->output())
            ->withHeader('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    /**
     * Reporte consolidado de lo pendiente por pagar a cada laboratorio en un
     * rango de fechas (basado en la fecha de la factura asociada).
     */
    private function buildPagosLaboratoriosReport($laboratorioId, string $fechaDesde, string $fechaHasta): array
    {
        $conditions = [
            'InvoiceDistribuciones.tipo' => 'LABORATORIO',
            'InvoiceDistribuciones.laboratorio_id IS NOT' => null,
        ];

        if (!empty($laboratorioId)) {
            $conditions['InvoiceDistribuciones.laboratorio_id'] = (int) $laboratorioId;
        }

        if (!empty($fechaDesde)) {
            $conditions['DATE(Invoices.created) >='] = $fechaDesde;
        }

        if (!empty($fechaHasta)) {
            $conditions['DATE(Invoices.created) <='] = $fechaHasta;
        }

        $distribuciones = $this->fetchTable('InvoiceDistribuciones')->find()
            ->contain(['Laboratorios', 'Invoices'])
            ->innerJoinWith('Invoices')
            ->where($conditions)
            ->order(['Invoices.created' => 'DESC'])
            ->all();

        $distribucionIdsPagadas = $this->fetchTable('PagosLaboratoriosHistorialDistribuciones')
            ->find('list', keyField: 'invoice_distribucion_id', valueField: 'invoice_distribucion_id')
            ->toArray();

        $resultado = [];
        $totalPagar = 0.0;
        $totalComprobantes = 0;
        $laboratoriosConDeuda = [];
        $laboratoriosConsolidados = [];

        foreach ($distribuciones as $dist) {
            if (empty($dist->invoice) || $dist->invoice->estado === 'ANULADO') {
                continue;
            }

            $yaPagado = isset($distribucionIdsPagadas[$dist->id]);
            $laboratorioNombre = $dist->laboratorio->nombre ?? '-';
            $laboratorioIdDist = $dist->laboratorio_id;

            $laboratoriosConDeuda[$laboratorioIdDist] = $laboratorioNombre;

            if (!isset($laboratoriosConsolidados[$laboratorioIdDist])) {
                $laboratoriosConsolidados[$laboratorioIdDist] = [
                    'laboratorio_id' => $laboratorioIdDist,
                    'laboratorio' => $laboratorioNombre,
                    'total_pagar' => 0.0,
                    'comprobantes' => 0,
                ];
            }

            $resultado[] = [
                'invoice_distribucion_id' => $dist->id,
                'invoice_id' => $dist->invoice_id,
                'fecha' => $dist->invoice->created,
                'laboratorio' => $laboratorioNombre,
                'cliente' => $dist->invoice->cliente_nombre ?? '-',
                'comprobante' => ($dist->invoice->serie ?: 'RI') . '-' . ($dist->invoice->correlativo ?: $dist->invoice->id),
                'descripcion' => $dist->descripcion ?: '-',
                'monto' => (float) $dist->monto,
                'estado' => $dist->invoice->estado,
                'ya_pagado' => $yaPagado,
            ];

            if (!$yaPagado) {
                $totalPagar += (float) $dist->monto;
                $totalComprobantes++;
                $laboratoriosConsolidados[$laboratorioIdDist]['total_pagar'] += (float) $dist->monto;
                $laboratoriosConsolidados[$laboratorioIdDist]['comprobantes']++;
            }
        }

        return [
            'resultado' => $resultado,
            'total_pagar' => $totalPagar,
            'total_comprobantes' => $totalComprobantes,
            'total_laboratorios' => count($laboratoriosConDeuda),
            'laboratorios_consolidados' => array_values($laboratoriosConsolidados),
        ];
    }

    /**
     * Devuelve, para un laboratorio y rango de fechas, cada distribución
     * (invoice_distribucion tipo LABORATORIO) pendiente de pagar.
     */
    private function buildDistribucionesPagables(int $laboratorioId, string $fechaDesde, string $fechaHasta): array
    {
        $datos = $this->buildPagosLaboratoriosReport($laboratorioId, $fechaDesde, $fechaHasta);

        return array_values(array_filter(
            $datos['resultado'],
            fn($d) => empty($d['ya_pagado'])
        ));
    }
}
