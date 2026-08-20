<?php
declare(strict_types=1);

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Cake\Routing\Router;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PagosDoctoresController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $currentAction = $this->request->getParam('action');

        $this->verificarPermisoORedireccionar('PagosDoctores', $currentAction);
    }

    public function index()
    {
        $doctorId = $this->request->getQuery('doctor_id');
        $fechaHoy = date('Y-m-d');
        $fechaDesde = $this->request->getQuery('fecha_desde') ?: $fechaHoy;
        $fechaHasta = $this->request->getQuery('fecha_hasta') ?: $fechaHoy;

        $datos = $this->buildPagosDoctoresReport($doctorId, $fechaDesde, $fechaHasta);

        $doctores = $this->fetchTable('Doctores')->find('list', [
            'keyField' => 'id',
            'valueField' => function ($row) {
                return trim((string)$row->nombre . ' ' . (string)$row->apellido);
            },
            'order' => ['nombre' => 'ASC', 'apellido' => 'ASC']
        ])->toArray();

        $resumen = [
            'total_pagar' => $datos['total_pagar'],
            'total_tratamientos' => $datos['total_tratamientos'],
            'total_comprobantes' => $datos['total_comprobantes'],
            'total_doctores' => $datos['total_doctores'],
        ];

        $resultado = $datos['resultado'];
        $metodosConsolidados = $datos['metodos_consolidados'];
        $doctoresConsolidados = $datos['doctores_consolidados'];

        $this->set(compact(
            'resultado',
            'metodosConsolidados',
            'doctoresConsolidados',
            'resumen',
            'doctores',
            'doctorId',
            'fechaDesde',
            'fechaHasta'
        ));
    }

    /**
     * Pantalla dedicada para registrar el pago a un doctor, seleccionando
     * individualmente qué métodos de pago (de qué comprobantes) se pagan hoy.
     * Permite pagar, por ejemplo, solo el EFECTIVO de una factura y dejar el
     * YAPE/TARJETA de esa misma factura para un pago posterior.
     */
    public function registrarPago()
    {
        $doctorId = $this->request->getQuery('doctor_id');
        $fechaHoy = date('Y-m-d');
        $fechaDesde = $this->request->getQuery('fecha_desde') ?: $fechaHoy;
        $fechaHasta = $this->request->getQuery('fecha_hasta') ?: $fechaHoy;
        $metodoPago = trim((string) $this->request->getQuery('metodo_pago', ''));

        $doctores = $this->fetchTable('Doctores')->find('list', [
            'keyField' => 'id',
            'valueField' => function ($row) {
                return trim((string)$row->nombre . ' ' . (string)$row->apellido);
            },
            'order' => ['nombre' => 'ASC', 'apellido' => 'ASC']
        ])->toArray();

        $metodosPagoDisponibles = ['EFECTIVO', 'YAPE', 'TARJETA', 'TRANSFERENCIA', 'PLIN', 'OTROS'];

        $movimientos = [];
        $comprobantes = [];
        $totalPendiente = 0.0;

        if (!empty($doctorId)) {
            $movimientos = $this->buildMovimientosPagables((int) $doctorId, $fechaDesde, $fechaHasta);

            if ($metodoPago !== '') {
                $movimientos = array_values(array_filter(
                    $movimientos,
                    fn($m) => $m['metodo_pago'] === $metodoPago
                ));
            }

            $totalPendiente = array_sum(array_map(fn($m) => $m['pago_doctor'], $movimientos));
            $comprobantes = $this->agruparMovimientosPorComprobante($movimientos);
        }

        $this->set(compact(
            'doctores',
            'doctorId',
            'fechaDesde',
            'fechaHasta',
            'metodoPago',
            'metodosPagoDisponibles',
            'comprobantes',
            'totalPendiente'
        ));
    }

    /**
     * Agrupa los métodos de pago pendientes por comprobante, para que la
     * interfaz muestre cada factura/recibo una sola vez (con sus métodos de
     * pago listados adentro) en vez de repetir el comprobante por cada método.
     */
    private function agruparMovimientosPorComprobante(array $movimientos): array
    {
        $comprobantes = [];

        foreach ($movimientos as $mov) {
            $invoiceId = $mov['invoice_id'];

            if (!isset($comprobantes[$invoiceId])) {
                $comprobantes[$invoiceId] = [
                    'invoice_id' => $invoiceId,
                    'comprobante' => $mov['comprobante'],
                    'cliente' => $mov['cliente'],
                    'fecha' => $mov['fecha'],
                    'estado' => $mov['estado'],
                    'conceptos' => $mov['conceptos'],
                    'distribuciones' => $mov['distribuciones'],
                    'total_pagar' => 0.0,
                    'metodos' => [],
                ];
            }

            $comprobantes[$invoiceId]['total_pagar'] += $mov['pago_doctor'];
            $comprobantes[$invoiceId]['metodos'][] = $mov;
        }

        return array_values($comprobantes);
    }

    public function guardarPago()
    {
        $this->request->allowMethod(['post']);

        $doctorId = (int) $this->request->getData('doctor_id');
        $fechaDesde = (string) $this->request->getData('fecha_desde');
        $fechaHasta = (string) $this->request->getData('fecha_hasta');
        $observaciones = trim((string) $this->request->getData('observaciones', ''));
        $cajaMovimientoIds = array_map('intval', (array) $this->request->getData('caja_movimiento_ids', []));

        if (!$doctorId || !$fechaDesde || !$fechaHasta || empty($cajaMovimientoIds)) {
            $this->Flash->error('Selecciona al menos un método de pago para registrar el pago.');
            return $this->redirect(['action' => 'registrarPago', '?' => [
                'doctor_id' => $doctorId,
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
            ]]);
        }

        // Recalcular en el servidor los movimientos pagables de este doctor/rango
        // (nunca confiar en montos del cliente) y quedarnos solo con los
        // seleccionados por el usuario.
        $disponibles = $this->buildMovimientosPagables($doctorId, $fechaDesde, $fechaHasta);
        $seleccionados = array_values(array_filter(
            $disponibles,
            fn($m) => in_array($m['caja_movimiento_id'], $cajaMovimientoIds, true)
        ));

        if (empty($seleccionados)) {
            $this->Flash->warning('Los métodos de pago seleccionados ya no están disponibles (quizás ya fueron pagados).');
            return $this->redirect(['action' => 'registrarPago', '?' => [
                'doctor_id' => $doctorId,
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
            ]]);
        }

        $identity = $this->request->getAttribute('identity');
        $userId = $identity ? (int) $identity->getIdentifier() : null;

        $PagosDoctoresHistorial = $this->fetchTable('PagosDoctoresHistorial');
        $PagosDoctoresHistorialMovimientos = $this->fetchTable('PagosDoctoresHistorialMovimientos');

        $montoTotal = array_sum(array_map(fn($m) => (float) $m['pago_doctor'], $seleccionados));

        $pagoHistorialId = null;

        $conn = $PagosDoctoresHistorial->getConnection();
        $ok = $conn->transactional(function () use (
            $PagosDoctoresHistorial,
            $PagosDoctoresHistorialMovimientos,
            $doctorId,
            $userId,
            $fechaDesde,
            $fechaHasta,
            $montoTotal,
            $seleccionados,
            $observaciones,
            &$pagoHistorialId
        ) {
            $pagoHistorial = $PagosDoctoresHistorial->newEntity([
                'doctor_id' => $doctorId,
                'user_id' => $userId,
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
                'monto_total' => $montoTotal,
                'total_comprobantes' => count($seleccionados),
                'observaciones' => $observaciones !== '' ? $observaciones : null,
            ]);

            if (!$PagosDoctoresHistorial->save($pagoHistorial)) {
                throw new \RuntimeException('No se pudo registrar el pago: ' . json_encode($pagoHistorial->getErrors()));
            }

            $pagoHistorialId = $pagoHistorial->id;

            foreach ($seleccionados as $mov) {
                $detalle = $PagosDoctoresHistorialMovimientos->newEntity([
                    'pago_historial_id' => $pagoHistorial->id,
                    'caja_movimiento_id' => $mov['caja_movimiento_id'],
                    'invoice_id' => $mov['invoice_id'],
                    'metodo_pago' => $mov['metodo_pago'],
                    'base_doctor' => $mov['base_doctor'],
                    'monto_pagado' => $mov['pago_doctor'],
                    'conceptos' => json_encode($mov['conceptos_movimiento'] ?? [], JSON_UNESCAPED_UNICODE),
                ]);

                if (!$PagosDoctoresHistorialMovimientos->save($detalle)) {
                    throw new \RuntimeException(
                        'No se pudo vincular el método de pago (' . $mov['metodo_pago'] . ') del comprobante ' .
                        $mov['comprobante'] . ': ' . json_encode($detalle->getErrors())
                    );
                }
            }

            return true;
        });

        if ($ok) {
            $this->Flash->success(sprintf(
                'Pago registrado: S/ %.2f por %d método(s) de pago.',
                $montoTotal,
                count($seleccionados)
            ));
            return $this->redirect(['action' => 'pdfPago', $pagoHistorialId]);
        }

        $this->Flash->error('No se pudo registrar el pago.');

        return $this->redirect(['action' => 'registrarPago', '?' => [
            'doctor_id' => $doctorId,
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
        ]]);
    }

    public function historial()
    {
        $doctorId = $this->request->getQuery('doctor_id');

        $query = $this->fetchTable('PagosDoctoresHistorial')->find()
            ->contain(['Doctores', 'Users'])
            ->order(['PagosDoctoresHistorial.created' => 'DESC']);

        if (!empty($doctorId)) {
            $query->where(['PagosDoctoresHistorial.doctor_id' => (int) $doctorId]);
        }

        $pagosHistorial = $this->paginate($query);

        $doctores = $this->fetchTable('Doctores')->find('list', [
            'keyField' => 'id',
            'valueField' => function ($row) {
                return trim((string)$row->nombre . ' ' . (string)$row->apellido);
            },
            'order' => ['nombre' => 'ASC', 'apellido' => 'ASC']
        ])->toArray();

        $this->set(compact('pagosHistorial', 'doctores', 'doctorId'));
    }

    public function historialDetalle($id)
    {
        $pagoHistorial = $this->fetchTable('PagosDoctoresHistorial')->get($id, [
            'contain' => [
                'Doctores',
                'Users',
                'PagosDoctoresHistorialMovimientos' => ['Invoices'],
            ],
        ]);

        $this->set(compact('pagoHistorial'));
    }

    /**
     * Comprobante en PDF de un pago a doctor ya registrado: quién pagó
     * (usuario que lo registró), a quién (doctor), cuándo, y el detalle
     * de cada comprobante/método de pago incluido, con los conceptos
     * (tratamientos/exámenes) que originaron el pago.
     */
    public function pdfPago($id)
    {
        $pagoHistorial = $this->fetchTable('PagosDoctoresHistorial')->get($id, [
            'contain' => [
                'Doctores',
                'Users',
                'PagosDoctoresHistorialMovimientos' => [
                    'Invoices' => [
                        'InvoiceItems',
                    ],
                ],
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

        $filename = sprintf('pago_doctor_%d.pdf', $pagoHistorial->id);

        return $this->response
            ->withType('application/pdf')
            ->withStringBody($dompdf->output())
            ->withHeader('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    public function exportPdf()
    {
        $doctorId = $this->request->getQuery('doctor_id');
        $fechaHoy = date('Y-m-d');
        $fechaDesde = $this->request->getQuery('fecha_desde') ?: $fechaHoy;
        $fechaHasta = $this->request->getQuery('fecha_hasta') ?: $fechaHoy;

        $datos = $this->buildPagosDoctoresReport($doctorId, $fechaDesde, $fechaHasta);

        $doctores = $this->fetchTable('Doctores')->find('list', [
            'keyField' => 'id',
            'valueField' => function ($row) {
                return trim((string)$row->nombre . ' ' . (string)$row->apellido);
            },
            'order' => ['nombre' => 'ASC', 'apellido' => 'ASC']
        ])->toArray();

        $logoUrl = Router::url('/img/logoJyza.webp', true);

        $this->viewBuilder()->disableAutoLayout();
        $this->set([
            'resultado' => $datos['resultado'],
            'metodosConsolidados' => $datos['metodos_consolidados'],
            'doctoresConsolidados' => $datos['doctores_consolidados'],
            'resumen' => [
                'total_pagar' => $datos['total_pagar'],
                'total_tratamientos' => $datos['total_tratamientos'],
                'total_comprobantes' => $datos['total_comprobantes'],
                'total_doctores' => $datos['total_doctores'],
            ],
            'doctorId' => $doctorId,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'doctores' => $doctores,
            'logoUrl' => $logoUrl,
        ]);

        $html = $this->render('pdf')->getBody()->__toString();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = sprintf(
            'pagos_doctores_%s_%s.pdf',
            str_replace('-', '', $fechaDesde),
            str_replace('-', '', $fechaHasta)
        );

        return $this->response
            ->withType('application/pdf')
            ->withStringBody($dompdf->output())
            ->withHeader('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    private function buildPagosDoctoresReport($doctorId, string $fechaDesde, string $fechaHasta): array
    {
        $conditions = [
            'Invoices.doctor_id IS NOT' => null,
        ];

        if (!empty($doctorId)) {
            $conditions['Invoices.doctor_id'] = (int)$doctorId;
        }

        if (!empty($fechaDesde)) {
            $conditions['DATE(Invoices.created) >='] = $fechaDesde;
        }

        if (!empty($fechaHasta)) {
            $conditions['DATE(Invoices.created) <='] = $fechaHasta;
        }

        $invoices = $this->fetchTable('Invoices')->find()
            ->contain([
                'Doctores',
                'InvoiceItems',
                'Pacientes',
                'CajaMovimientos',
                'InvoiceDistribuciones',
            ])
            ->where($conditions)
            ->order(['Invoices.created' => 'DESC'])
            ->all();

        // Movimientos de caja (métodos de pago) ya cubiertos por un pago histórico
        $cajaMovimientoIdsPagados = $this->fetchTable('PagosDoctoresHistorialMovimientos')
            ->find('list', keyField: 'caja_movimiento_id', valueField: 'caja_movimiento_id')
            ->toArray();

        // Comisión fija por examen: aplica siempre, sin importar el modo_pago
        // del doctor, igual que en buildMovimientosPagables().
        $comisionesExamenes = $this->fetchTable('Examenes')
            ->find('list', keyField: 'id', valueField: 'comision_medico')
            ->toArray();

        $resultado = [];
        $totalPagar = 0.0;
        $totalTratamientos = 0.0;
        $totalComprobantes = 0;
        $doctoresConProduccion = [];
        $metodosConsolidados = [];
        $doctoresConsolidados = [];

        foreach ($invoices as $invoice) {
            $baseDoctorBruta = 0.0;
            $pagoExamenes = 0.0;

            foreach ($invoice->invoice_items as $item) {
                $tipoItem = $item->tipo_item ?? '';
                if ($tipoItem === 'tratamiento') {
                    $baseDoctorBruta += (float)$item->total;
                } elseif ($tipoItem === 'examen') {
                    $comision = (float) ($comisionesExamenes[$item->examen_id] ?? 0);
                    $pagoExamenes += $comision * (float) ($item->cantidad ?? 0);
                }
            }
            $pagoExamenes = round($pagoExamenes, 2);

            if ($baseDoctorBruta <= 0 && $pagoExamenes <= 0) {
                continue;
            }

            // La factura se considera "ya pagada" solo si TODOS sus movimientos con
            // método de pago ya fueron cubiertos por algún pago histórico. Si solo
            // se pagó parte (ej. el EFECTIVO pero no el YAPE), sigue apareciendo
            // como pendiente para que el resto se pueda pagar después.
            $movimientosConMetodo = array_filter(
                ($invoice->caja_movimientos ?? []),
                fn($mov) => trim((string)($mov->metodo_pago ?? '')) !== ''
            );
            $yaPagado = !empty($movimientosConMetodo) && count(array_filter(
                $movimientosConMetodo,
                fn($mov) => !isset($cajaMovimientoIdsPagados[$mov->id])
            )) === 0;

            // Descontar lo ya distribuido a laboratorio/materiales: esa parte del
            // cobro no le corresponde al doctor ni a la clínica, no debe formar
            // parte de la base sobre la que se calcula la comisión del doctor.
            $totalDistribuido = 0.0;
            foreach (($invoice->invoice_distribuciones ?? []) as $dist) {
                $totalDistribuido += (float)($dist->monto ?? 0);
            }
            $totalDistribuido = min($totalDistribuido, $baseDoctorBruta);

            $baseDoctor = round($baseDoctorBruta - $totalDistribuido, 2);

            // Factor para prorratear cada método de pago a la base ya neta de
            // distribución, manteniendo la proporción entre métodos.
            $factorNeto = $baseDoctorBruta > 0 ? ($baseDoctor / $baseDoctorBruta) : 1.0;

            // Una factura solo de exámenes no tiene base de tratamientos
            // (baseDoctor = 0) pero igual debe generar el pago de comisión.
            if ($baseDoctor <= 0 && $pagoExamenes <= 0) {
                continue;
            }

            $porcentaje = 0.0;
            $nombreDoctor = '-';

            if (!empty($invoice->doctore)) {
                $porcentaje = (float)($invoice->doctore->porcentaje_pago ?? 0);
                $nombreDoctor = trim(
                    (string)($invoice->doctore->nombre ?? '') . ' ' .
                    (string)($invoice->doctore->apellido ?? '')
                );
                $doctoresConProduccion[$invoice->doctore->id] = $nombreDoctor;

                if (!isset($doctoresConsolidados[$invoice->doctore->id])) {
                    $doctoresConsolidados[$invoice->doctore->id] = [
                        'doctor_id' => $invoice->doctore->id,
                        'doctor' => $nombreDoctor,
                        'total_tratamientos' => 0.0,
                        'total_pagar' => 0.0,
                        'porcentaje' => $porcentaje,
                        'comprobantes' => 0,
                        'metodos_pago' => [],
                    ];
                }
            }

            $metodosPago = [];
            foreach (($invoice->caja_movimientos ?? []) as $movimiento) {
                $metodo = trim((string)($movimiento->metodo_pago ?? ''));
                if ($metodo === '') {
                    continue;
                }

                $monto = (float)($movimiento->monto_recibido ?? 0);
                // El monto del método de pago sigue siendo el bruto recibido (para mostrarlo),
                // pero la comisión del doctor se calcula sobre su parte neta de distribución.
                $montoNetoMetodo = round($monto * $factorNeto, 2);
                $pagoMetodoDoctor = round(($montoNetoMetodo * $porcentaje) / 100, 2);

                $metodosPago[] = [
                    'metodo' => $metodo,
                    'monto' => $monto,
                    'pago_doctor' => $pagoMetodoDoctor,
                ];

                if (!isset($metodosConsolidados[$metodo])) {
                    $metodosConsolidados[$metodo] = [
                        'metodo' => $metodo,
                        'monto_total' => 0.0,
                        'pago_doctor_total' => 0.0,
                        'movimientos' => 0,
                    ];
                }

                $metodosConsolidados[$metodo]['monto_total'] += $monto;
                $metodosConsolidados[$metodo]['pago_doctor_total'] += $pagoMetodoDoctor;
                $metodosConsolidados[$metodo]['movimientos']++;
            }

            $pagoDoctor = round((($baseDoctor * $porcentaje) / 100) + $pagoExamenes, 2);

            // Desglosar items en tratamientos, productos y exámenes, indicando
            // cuánto le corresponde al doctor por cada uno (tratamientos pagan
            // por % sobre su base neta, exámenes pagan comisión fija, productos
            // no pagan comisión).
            $items_detalles = [];
            foreach ($invoice->invoice_items as $item) {
                $tipoItemInterno = $item->tipo_item ?? '';
                $tipoItemLabel = match ($tipoItemInterno) {
                    'tratamiento' => 'Tratamiento',
                    'examen' => 'Examen',
                    default => 'Producto',
                };

                if ($tipoItemInterno === 'tratamiento') {
                    $totalItem = (float) ($item->total ?? 0);
                    $pagoDoctorItem = round((($totalItem * $factorNeto) * $porcentaje) / 100, 2);
                } elseif ($tipoItemInterno === 'examen') {
                    $comision = (float) ($comisionesExamenes[$item->examen_id] ?? 0);
                    $pagoDoctorItem = round($comision * (float) ($item->cantidad ?? 0), 2);
                } else {
                    $pagoDoctorItem = 0.0;
                }

                $items_detalles[] = [
                    'descripcion' => $item->descripcion ?? '-',
                    'tipo' => $tipoItemLabel,
                    'cantidad' => (float)($item->cantidad ?? 0),
                    'valor_unitario' => (float)($item->valor_unitario ?? 0),
                    'total' => (float)($item->total ?? 0),
                    'pago_doctor_item' => $pagoDoctorItem,
                ];
            }

            $resultado[] = [
                'invoice_id' => $invoice->id,
                'fecha' => $invoice->created,
                'doctor' => $nombreDoctor,
                'cliente' => $invoice->cliente_nombre ?? '-',
                'comprobante' => ($invoice->serie ?: 'RI') . '-' . ($invoice->correlativo ?: $invoice->id),
                'tipo_doc' => $invoice->tipo_doc === '01' ? 'Factura' : 'Boleta',
                'base_doctor' => $baseDoctor,
                'base_doctor_bruta' => $baseDoctorBruta,
                'total_distribuido' => $totalDistribuido,
                'porcentaje' => $porcentaje,
                'pago_doctor' => $pagoDoctor,
                'estado' => $invoice->estado,
                'ya_pagado' => $yaPagado,
                'metodos_pago' => array_values($metodosPago),
                'items_detalles' => $items_detalles,
            ];

            // Las facturas ya pagadas al doctor no vuelven a sumar en los totales
            // pendientes de pago, pero quedan visibles en el detalle (marcadas).
            if (!$yaPagado) {
                $totalPagar += $pagoDoctor;
                $totalTratamientos += $baseDoctor;
                $totalComprobantes++;
            }

            if (!empty($invoice->doctore) && !$yaPagado) {
                $doctorId = $invoice->doctore->id;
                $doctoresConsolidados[$doctorId]['total_tratamientos'] += $baseDoctor;
                $doctoresConsolidados[$doctorId]['total_pagar'] += $pagoDoctor;
                $doctoresConsolidados[$doctorId]['comprobantes']++;

                    foreach ($metodosPago as $metodoPago) {
                        $metodoNombre = $metodoPago['metodo'];
                        if (!isset($doctoresConsolidados[$doctorId]['metodos_pago'][$metodoNombre])) {
                            $doctoresConsolidados[$doctorId]['metodos_pago'][$metodoNombre] = [
                                'metodo' => $metodoNombre,
                                'monto_total' => 0.0,
                                'pago_doctor_total' => 0.0,
                                'movimientos' => 0,
                            ];
                        }

                        $doctoresConsolidados[$doctorId]['metodos_pago'][$metodoNombre]['monto_total'] += (float)$metodoPago['monto'];
                        $doctoresConsolidados[$doctorId]['metodos_pago'][$metodoNombre]['pago_doctor_total'] += (float)$metodoPago['pago_doctor'];
                        $doctoresConsolidados[$doctorId]['metodos_pago'][$metodoNombre]['movimientos']++;
                    }
            }
        }

        foreach ($doctoresConsolidados as &$doctorConsolidado) {
            $doctorConsolidado['metodos_pago'] = array_values($doctorConsolidado['metodos_pago']);
        }
        unset($doctorConsolidado);

        return [
            'resultado' => $resultado,
            'total_pagar' => $totalPagar,
            'total_tratamientos' => $totalTratamientos,
            'total_comprobantes' => $totalComprobantes,
            'total_doctores' => count($doctoresConProduccion),
            'metodos_consolidados' => array_values($metodosConsolidados),
            'doctores_consolidados' => array_values($doctoresConsolidados),
        ];
    }

    /**
     * Devuelve, para un doctor y rango de fechas, cada método de pago (caja_movimiento)
     * pendiente de pagar al doctor, de forma individual — permite pagar solo el
     * EFECTIVO de una factura hoy y el YAPE/TARJETA de esa misma factura después.
     */
    private function buildMovimientosPagables(int $doctorId, string $fechaDesde, string $fechaHasta): array
    {
        $invoices = $this->fetchTable('Invoices')->find()
            ->contain(['Doctores', 'InvoiceItems', 'CajaMovimientos', 'InvoiceDistribuciones' => ['Laboratorios']])
            ->where([
                'Invoices.doctor_id' => $doctorId,
                'DATE(Invoices.created) >=' => $fechaDesde,
                'DATE(Invoices.created) <=' => $fechaHasta,
            ])
            ->order(['Invoices.created' => 'DESC'])
            ->all();

        $cajaMovimientoIdsPagados = $this->fetchTable('PagosDoctoresHistorialMovimientos')
            ->find('list', keyField: 'caja_movimiento_id', valueField: 'caja_movimiento_id')
            ->toArray();

        $modoPago = 'PORCENTAJE';
        $tarifasFijas = [];
        $doctorRow = $this->fetchTable('Doctores')->find()
            ->where(['id' => $doctorId])
            ->first();

        if ($doctorRow) {
            $modoPago = $doctorRow->modo_pago ?? 'PORCENTAJE';
        }

        if ($modoPago === 'FIJO') {
            // Base: tarifa por defecto de cada tratamiento (aplica a cualquier
            // doctor en modo FIJO). Override: tarifa específica para este doctor
            // en doctor_tratamiento_tarifas, para los casos ocasionales donde se
            // le paga distinto al default del tratamiento.
            $tarifasFijas = $this->fetchTable('Tratamientos')
                ->find('list', keyField: 'id', valueField: 'monto_fijo_pago')
                ->toArray();

            $overridesDoctor = $this->fetchTable('DoctorTratamientoTarifas')
                ->find('list', keyField: 'tratamiento_id', valueField: 'monto_fijo')
                ->where(['doctor_id' => $doctorId])
                ->toArray();

            $tarifasFijas = array_replace($tarifasFijas, $overridesDoctor);
        }

        // Comisión fija por examen: aplica siempre, sin importar el modo_pago
        // del doctor (PORCENTAJE o FIJO), ya que es independiente de la
        // comisión por tratamientos.
        $comisionesExamenes = $this->fetchTable('Examenes')
            ->find('list', keyField: 'id', valueField: 'comision_medico')
            ->toArray();

        $movimientosPagables = [];

        foreach ($invoices as $invoice) {
            if (empty($invoice->doctore) || $invoice->estado === 'ANULADO') {
                continue;
            }

            $baseDoctorBruta = 0.0;
            $baseExamenesBruta = 0.0;
            foreach ($invoice->invoice_items as $item) {
                $tipoItem = $item->tipo_item ?? '';
                if ($tipoItem === 'tratamiento') {
                    $baseDoctorBruta += (float) $item->total;
                } elseif ($tipoItem === 'examen') {
                    $baseExamenesBruta += (float) $item->total;
                }
            }

            // La base combinada (tratamientos + exámenes) se usa para prorratear
            // el pago al doctor entre los distintos métodos de pago de caja; la
            // distribución a laboratorio/materiales sigue calculándose solo sobre
            // tratamientos, ya que así se registra hoy en invoice_distribuciones.
            $baseFacturaTotal = round($baseDoctorBruta + $baseExamenesBruta, 2);

            if ($baseFacturaTotal <= 0) {
                continue;
            }

            $totalDistribuido = 0.0;
            foreach (($invoice->invoice_distribuciones ?? []) as $dist) {
                $totalDistribuido += (float) ($dist->monto ?? 0);
            }
            $totalDistribuido = min($totalDistribuido, $baseDoctorBruta);

            $baseDoctorNeta = round($baseDoctorBruta - $totalDistribuido, 2);
            // Solo se descarta la factura si no queda base neta de tratamientos
            // NI base de exámenes: una factura únicamente de exámenes no tiene
            // tratamientos (baseDoctorBruta = 0) pero igual debe generar el pago
            // de comisión por examen.
            if ($baseDoctorNeta <= 0 && $baseExamenesBruta <= 0) {
                continue;
            }

            $factorNeto = $baseDoctorBruta > 0 ? ($baseDoctorNeta / $baseDoctorBruta) : 1.0;
            $porcentaje = (float) ($invoice->doctore->porcentaje_pago ?? 0);
            $comprobante = ($invoice->serie ?: 'RI') . '-' . ($invoice->correlativo ?: $invoice->id);
            $clienteNombre = $invoice->cliente_nombre ?? '-';

            // Monto total que le corresponde al doctor por esta factura: en modo
            // PORCENTAJE se calcula sobre la base neta (post-distribución) según su
            // %; en modo FIJO se suma cantidad × tarifa fija de cada tratamiento
            // atendido (independiente del precio cobrado y de la distribución).
            if ($modoPago === 'FIJO') {
                $pagoTratamientos = 0.0;
                foreach ($invoice->invoice_items as $item) {
                    if (($item->tipo_item ?? '') !== 'tratamiento') {
                        continue;
                    }
                    $tarifa = (float) ($tarifasFijas[$item->tratamiento_id] ?? 0);
                    $pagoTratamientos += $tarifa * (float) ($item->cantidad ?? 0);
                }
                $pagoTratamientos = round($pagoTratamientos, 2);
            } else {
                $pagoTratamientos = round(($baseDoctorNeta * $porcentaje) / 100, 2);
            }

            // Comisión por exámenes: monto fijo por examen realizado, aparte de
            // la comisión por tratamientos y sin importar el modo_pago del doctor.
            $pagoExamenes = 0.0;
            foreach ($invoice->invoice_items as $item) {
                if (($item->tipo_item ?? '') !== 'examen') {
                    continue;
                }
                $comision = (float) ($comisionesExamenes[$item->examen_id] ?? 0);
                $pagoExamenes += $comision * (float) ($item->cantidad ?? 0);
            }
            $pagoExamenes = round($pagoExamenes, 2);

            $pagoTotalFactura = round($pagoTratamientos + $pagoExamenes, 2);

            if ($pagoTotalFactura <= 0) {
                continue;
            }

            // Conceptos (tratamientos y exámenes) que componen el pago al doctor
            // por esta factura, indicando también cuánto le corresponde por cada
            // uno individualmente, no solo el total final de la factura.
            $conceptos = [];
            foreach ($invoice->invoice_items as $item) {
                $tipoItem = $item->tipo_item ?? '';

                if ($tipoItem === 'tratamiento') {
                    $totalItem = (float) ($item->total ?? 0);

                    if ($modoPago === 'FIJO') {
                        $tarifa = (float) ($tarifasFijas[$item->tratamiento_id] ?? 0);
                        $pagoItem = round($tarifa * (float) ($item->cantidad ?? 0), 2);
                    } else {
                        // Prorrateo proporcional a lo que este ítem representa dentro
                        // del total bruto de tratamientos, aplicado sobre el pago
                        // de tratamientos ya neto de distribución.
                        $proporcionItem = $baseDoctorBruta > 0 ? ($totalItem / $baseDoctorBruta) : 0.0;
                        $pagoItem = round($pagoTratamientos * $proporcionItem, 2);
                    }

                    $conceptos[] = [
                        'descripcion' => $item->descripcion ?? '-',
                        'cantidad' => (float) ($item->cantidad ?? 0),
                        'total' => $totalItem,
                        'pago_doctor_item' => $pagoItem,
                    ];
                } elseif ($tipoItem === 'examen') {
                    $comision = (float) ($comisionesExamenes[$item->examen_id] ?? 0);
                    $pagoItem = round($comision * (float) ($item->cantidad ?? 0), 2);

                    $conceptos[] = [
                        'descripcion' => $item->descripcion ?? '-',
                        'cantidad' => (float) ($item->cantidad ?? 0),
                        'total' => (float) ($item->total ?? 0),
                        'pago_doctor_item' => $pagoItem,
                    ];
                }
            }

            // Distribución (Laboratorio/Materiales) de esta factura, informativa:
            // ya está descontada de la base del doctor, pero debe verse explícita
            // para saber por qué la base bajó y a dónde se fue ese dinero.
            $distribuciones = [];
            foreach (($invoice->invoice_distribuciones ?? []) as $dist) {
                $distribuciones[] = [
                    'tipo' => $dist->tipo,
                    'laboratorio' => $dist->laboratorio->nombre ?? null,
                    'monto' => (float) $dist->monto,
                ];
            }

            foreach (($invoice->caja_movimientos ?? []) as $mov) {
                $metodo = trim((string) ($mov->metodo_pago ?? ''));
                if ($metodo === '' || isset($cajaMovimientoIdsPagados[$mov->id])) {
                    continue;
                }

                $montoRecibido = (float) $mov->monto_recibido;
                $montoNeto = round($montoRecibido * $factorNeto, 2);

                // Proporción que representa este método de pago sobre el total
                // real cobrado en la factura (incluye productos y cualquier otro
                // ítem, no solo tratamientos+exámenes); se usa para prorratear
                // tanto el pago al doctor como la distribución a
                // laboratorio/materiales entre los distintos métodos de pago.
                // Usar baseFacturaTotal aquí subestima el divisor cuando la
                // factura tiene productos, haciendo que la suma de los pagos
                // prorrateados supere el pago total real de la factura.
                $totalFacturaReal = (float) $invoice->total;
                $proporcionMovimiento = $totalFacturaReal > 0 ? ($montoRecibido / $totalFacturaReal) : 0.0;
                $pagoDoctor = round($pagoTotalFactura * $proporcionMovimiento, 2);

                if ($pagoDoctor <= 0) {
                    continue;
                }

                // Parte de la distribución (Lab/Materiales) que corresponde
                // proporcionalmente a este método de pago específico.
                $distribuidoEnMovimiento = round($totalDistribuido * $proporcionMovimiento, 2);

                // Conceptos prorrateados a este movimiento específico (mismo
                // criterio que el pago total del movimiento), para dejar un
                // snapshot fiel de qué se pagó exactamente con este método.
                $conceptosMovimiento = array_map(function ($concepto) use ($proporcionMovimiento) {
                    $concepto['pago_doctor_item'] = round(
                        (float) $concepto['pago_doctor_item'] * $proporcionMovimiento,
                        2
                    );

                    return $concepto;
                }, $conceptos);

                $movimientosPagables[] = [
                    'caja_movimiento_id' => $mov->id,
                    'invoice_id' => $invoice->id,
                    'comprobante' => $comprobante,
                    'cliente' => $clienteNombre,
                    'fecha' => $invoice->created,
                    'metodo_pago' => $metodo,
                    'monto_recibido' => $montoRecibido,
                    'base_doctor' => $montoNeto,
                    'modo_pago' => $modoPago,
                    'porcentaje' => $porcentaje,
                    'pago_doctor' => $pagoDoctor,
                    'estado' => $invoice->estado,
                    'conceptos' => $conceptos,
                    'conceptos_movimiento' => $conceptosMovimiento,
                    'distribuciones' => $distribuciones,
                    'total_distribuido_movimiento' => $distribuidoEnMovimiento,
                ];
            }
        }

        return $movimientosPagables;
    }

    public function exportarExcel()
    {
        $doctorId = $this->request->getQuery('doctor_id');
        $fechaDesde = $this->request->getQuery('fecha_desde') ?: date('Y-m-d');
        $fechaHasta = $this->request->getQuery('fecha_hasta') ?: date('Y-m-d');

        $datos = $this->buildPagosDoctoresReport($doctorId, $fechaDesde, $fechaHasta);
        $resultado = $datos['resultado'];
        $doctoresConsolidados = $datos['doctores_consolidados'];
        $metodosConsolidados = $datos['metodos_consolidados'];
        $resumen = [
            'total_pagar' => $datos['total_pagar'],
            'total_tratamientos' => $datos['total_tratamientos'],
            'total_comprobantes' => $datos['total_comprobantes'],
            'total_doctores' => $datos['total_doctores'],
        ];

        // Crear spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pagos Doctores');

        // Estilos
        $headerStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF0DCAF0'],
            ],
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'border' => ['allBorders' => ['borderStyle' => 'thin']],
        ];

        // ===== HOJA 1: RESUMEN =====
        $row = 1;
        $sheet->setCellValue('A' . $row, 'RESUMEN DE PAGOS A DOCTORES');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A' . $row . ':E' . $row);

        $row = 2;
        $sheet->setCellValue('A' . $row, 'Período: ' . $fechaDesde . ' - ' . $fechaHasta);
        $sheet->mergeCells('A' . $row . ':E' . $row);

        $row = 4;
        $sheet->setCellValue('A' . $row, 'Doctores con producción:');
        $sheet->setCellValue('B' . $row, $resumen['total_doctores']);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Total tratamientos:');
        $sheet->setCellValue('B' . $row, 'S/ ' . number_format((float)$resumen['total_tratamientos'], 2));
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Total a pagar:');
        $sheet->setCellValue('B' . $row, 'S/ ' . number_format((float)$resumen['total_pagar'], 2));
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12)->getColor()->setRGB('FF0000');

        $row++;
        $sheet->setCellValue('A' . $row, 'Comprobantes:');
        $sheet->setCellValue('B' . $row, $resumen['total_comprobantes']);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        // ===== TABLA: CONSOLIDADO POR DOCTOR =====
        $row = 11;
        $sheet->setCellValue('A' . $row, 'Doctor');
        $sheet->setCellValue('B' . $row, 'Total Tratamientos');
        $sheet->setCellValue('C' . $row, '% Pago');
        $sheet->setCellValue('D' . $row, 'Total a Pagar');
        $sheet->setCellValue('E' . $row, 'Comprobantes');
        $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($headerStyle);

        $row = 12;
        foreach ($doctoresConsolidados as $doctor) {
            $sheet->setCellValue('A' . $row, $doctor['doctor']);
            $sheet->setCellValue('B' . $row, 'S/ ' . number_format((float)$doctor['total_tratamientos'], 2));
            $sheet->setCellValue('C' . $row, number_format((float)$doctor['porcentaje'], 2) . '%');
            $sheet->setCellValue('D' . $row, 'S/ ' . number_format((float)$doctor['total_pagar'], 2));
            $sheet->setCellValue('E' . $row, $doctor['comprobantes']);
            $row++;
        }

        // ===== TABLA: DETALLE DE FACTURAS =====
        $row += 2;
        $sheet->setCellValue('A' . $row, 'DETALLE DE FACTURAS');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $sheet->mergeCells('A' . $row . ':J' . $row);

        $row++;
        $headers = ['Fecha', 'Doctor', 'Cliente', 'Comprobante', 'Tipo', 'Total Trat.', '%', 'Pago Doctor', 'Métodos', 'Estado'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        
        foreach ($columns as $idx => $col) {
            $sheet->setCellValue($col . $row, $headers[$idx]);
            $sheet->getStyle($col . $row)->applyFromArray($headerStyle);
        }

        $row++;
        foreach ($resultado as $invoice) {
            $sheet->setCellValue('A' . $row, $invoice['fecha']->format('Y-m-d H:i'));
            $sheet->setCellValue('B' . $row, $invoice['doctor']);
            $sheet->setCellValue('C' . $row, $invoice['cliente']);
            $sheet->setCellValue('D' . $row, $invoice['comprobante']);
            $sheet->setCellValue('E' . $row, $invoice['tipo_doc']);
            $sheet->setCellValue('F' . $row, 'S/ ' . number_format((float)$invoice['base_doctor'], 2));
            $sheet->setCellValue('G' . $row, number_format((float)$invoice['porcentaje'], 2) . '%');
            $sheet->setCellValue('H' . $row, 'S/ ' . number_format((float)$invoice['pago_doctor'], 2));
            
            // Métodos de pago
            $metodos = '';
            if (!empty($invoice['metodos_pago'])) {
                $metodos = implode(', ', array_map(fn($m) => $m['metodo'] . ' S/ ' . number_format((float)$m['monto'], 2), $invoice['metodos_pago']));
            }
            $sheet->setCellValue('I' . $row, $metodos);
            $sheet->setCellValue('J' . $row, $invoice['estado']);
            
            // Items como sub-información
            $row++;
            if (!empty($invoice['items_detalles'])) {
                foreach ($invoice['items_detalles'] as $item) {
                    $sheet->setCellValue('A' . $row, '  → ' . $item['descripcion']);
                    $sheet->setCellValue('B' . $row, $item['tipo']);
                    $sheet->setCellValue('C' . $row, $item['cantidad']);
                    $sheet->setCellValue('D' . $row, 'S/ ' . number_format((float)$item['valor_unitario'], 2));
                    $sheet->setCellValue('E' . $row, 'S/ ' . number_format((float)$item['total'], 2));
                    $sheet->setCellValue('F' . $row, ($item['tipo'] === 'Tratamiento' ? 'Sí' : 'No (Clínica)'));
                    $sheet->getStyle('A' . $row . ':J' . $row)->getFont()->setItalic(true)->setSize(9);
                    $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->setFillType(Fill::FILL_SOLID);
                    $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->getStartColor()->setARGB('FFF0F0F0');
                    $row++;
                }
            }
        }

        // Ajustar ancho de columnas
        foreach ($columns as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Descargar
        $writer = new Xlsx($spreadsheet);
        $fileName = 'pagos_doctores_' . str_replace('-', '', $fechaDesde) . '_' . str_replace('-', '', $fechaHasta) . '.xlsx';

        $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($tmpFile);

        return $this->response->withFile($tmpFile, [
            'download' => true,
            'name' => $fileName,
            'delete' => true,
        ]);
    }
}