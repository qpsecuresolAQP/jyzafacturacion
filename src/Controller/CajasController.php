<?php
declare(strict_types=1);

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;

class CajasController extends AppController
{
    public function index()
    {
        $cajas = $this->paginate(
            $this->Cajas->find()
                ->contain(['Users'])
                ->order(['Cajas.created' => 'DESC'])
        );

        $this->set(compact('cajas'));
    }

    public function abrir()
    {
        $Users = $this->fetchTable('Users');

        $usuarios = $Users->find('list', [
            'keyField' => 'id',
            'valueField' => function ($row) {
                return $row->name ?? $row->username ?? ('Usuario #' . $row->id);
            },
            'order' => ['id' => 'ASC']
        ])->toArray();

        // Obtener usuario actual
        $usuarioActual = $this->Authentication->getIdentity();
        $userIdActual = $usuarioActual?->id ?? null;

       $caja = $this->Cajas->newEmptyEntity();

$cajaAbierta = $this->Cajas->find()
    ->where([
        'estado' => 'ABIERTA'
    ])
    ->first();
        

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $nombre = trim((string)($data['nombre'] ?? ''));
            $codigo = trim((string)($data['codigo'] ?? ''));
            $userId = (int)($data['user_id'] ?? 0);

            $cajaActivaMisma = $this->Cajas->find()
                ->where([
                    'nombre' => $nombre,
                    'codigo' => $codigo,
                    'estado IN' => ['ABIERTA', 'PAUSADA'],
                ])
                ->first();

            if ($cajaActivaMisma) {
                $this->Flash->warning('Esa caja ya está en uso o pausada y no puede abrirse otra vez.');
                return $this->redirect(['action' => 'view', $cajaActivaMisma->id]);
            }

            $caja = $this->Cajas->patchEntity($caja, [
                'nombre' => $nombre,
                'codigo' => $codigo,
                'user_id' => $userId,
                'fecha' => date('Y-m-d'),
                'monto_inicial' => $data['monto_inicial'] ?? 0,
                'estado' => 'ABIERTA',
                'observacion' => $data['observacion'] ?? null,
            ]);

           if ($this->Cajas->save($caja)) {

    // ==========================
    // GUARDAR DENOMINACIONES
    // ==========================

    $CajaDenominaciones = $this->fetchTable('CajaDenominaciones');

    $denominaciones = $data['denominaciones'] ?? [];

    $valores = [
        1 => 200,
        2 => 100,
        3 => 50,
        4 => 20,
        5 => 10,
        6 => 5,
        7 => 2,
        8 => 1,
        9 => 0.50,
        10 => 0.20,
        11 => 0.10,
    ];

    foreach ($denominaciones as $idDen => $cantidad) {

        $cantidad = (int)$cantidad;

        if ($cantidad <= 0) {
            continue;
        }

        $valor = $valores[$idDen] ?? 0;

        $detalle = $CajaDenominaciones->newEmptyEntity();

        $detalle->caja_id = $caja->id;
        $detalle->tipo_movimiento = 'APERTURA';
        $detalle->tipo = $valor >= 1 ? 'BILLETE' : 'MONEDA';
        $detalle->valor = $valor;
        $detalle->cantidad = $cantidad;
        $detalle->subtotal = $valor * $cantidad;

        $CajaDenominaciones->save($detalle);
    }

    $this->Flash->success('Caja abierta correctamente.');

    return $this->redirect([
        'action' => 'view',
        $caja->id
    ]);
}

            $this->Flash->error('No se pudo abrir la caja.');
        }

        $cajasFisicas = [
            'Caja 1' => 'CAJ001',
            
        ];

        // Si hay una sola caja, preseleccionarla
        $cajaFisicaUnica = count($cajasFisicas) === 1 ? current($cajasFisicas) : null;
        $nombreCajaUnica = count($cajasFisicas) === 1 ? key($cajasFisicas) : null;

        $denominaciones = [
            
             (object)['id' => 1, 'tipo' => 'BILLETE', 'valor' => 200],
             (object)['id' => 2, 'tipo' => 'BILLETE', 'valor' => 100],
             (object)['id' => 3, 'tipo' => 'BILLETE', 'valor' => 50],
             (object)['id' => 4, 'tipo' => 'BILLETE', 'valor' => 20],
             (object)['id' => 5, 'tipo' => 'BILLETE', 'valor' => 10],
             (object)['id' => 6, 'tipo' => 'MONEDA', 'valor' => 5],
             (object)['id' => 7, 'tipo' => 'MONEDA', 'valor' => 2],
             (object)['id' => 8, 'tipo' => 'MONEDA', 'valor' => 1],
             (object)['id' => 9, 'tipo' => 'MONEDA', 'valor' => 0.50],
             (object)['id' => 10, 'tipo' => 'MONEDA', 'valor' => 0.20],
             (object)['id' => 11, 'tipo' => 'MONEDA', 'valor' => 0.10],
        ];
            
$this->set(compact(
    'caja',
    'usuarios',
    'cajasFisicas',
    'denominaciones',
    'cajaAbierta',
    'cajaFisicaUnica',
    'nombreCajaUnica',
    'userIdActual'
));    }

    public function view($id)
{
    $caja = $this->Cajas->get($id, [
        'contain' => [
            'Users',
            'CajaMovimientos' => ['Invoices'],
        ],
    ]);

    $totalEfectivo = 0.0;
    $totalYape = 0.0;
    $totalTarjeta = 0.0;
    $totalTransferencia = 0.0;
    $totalPlin = 0.0;
    $totalOtros = 0.0;

    foreach ($caja->caja_movimientos as $mov) {

        if (!empty($mov->invoice) && $mov->invoice->estado === 'ANULADO') {
            continue;
        }

        if ($mov->metodo_pago === 'EFECTIVO') {
            $totalEfectivo += (float)$mov->monto_recibido;

        } elseif ($mov->metodo_pago === 'YAPE') {
            $totalYape += (float)$mov->monto_recibido;

        } elseif ($mov->metodo_pago === 'TARJETA') {
            $totalTarjeta += (float)$mov->monto_recibido;
        }
        elseif ($mov->metodo_pago === 'TRANSFERENCIA') {
            $totalTransferencia += (float)$mov->monto_recibido;
        } elseif ($mov->metodo_pago === 'PLIN') {
            $totalPlin += (float)$mov->monto_recibido;
        } elseif ($mov->metodo_pago === 'OTROS') {
            $totalOtros += (float)$mov->monto_recibido;
        }
    }

    $totalGeneral =
        $totalEfectivo +
        $totalYape +
        $totalPlin +
        $totalTransferencia +
        $totalOtros +
        $totalTarjeta;

    // DESPUÉS
    $CajaIngresos = $this->fetchTable('CajaIngresos');
    $CajaEgresos  = $this->fetchTable('CajaEgresos');

    $ingresosExternos = $CajaIngresos->find()->where(['caja_id' => $id])->order(['created' => 'DESC'])->all();
    $totalIngresosExternos = (float)$ingresosExternos->sumOf('monto');
  
// Totales brutos de ingresos por método (ventas + ingresos externos), sin descontar egresos
    $totalEfectivoIngresos = $totalEfectivo;
    $totalYapeIngresos = $totalYape;
    $totalTarjetaIngresos = $totalTarjeta;
    $totalTransferenciaIngresos = $totalTransferencia;
    $totalPlinIngresos = $totalPlin;
    $totalOtrosIngresos = $totalOtros;

    $totalGeneralIngresos =
        $totalEfectivoIngresos +
        $totalYapeIngresos +
        $totalPlinIngresos +
        $totalTransferenciaIngresos +
        $totalOtrosIngresos +
        $totalTarjetaIngresos;

    $egresosExternos = $CajaEgresos
        ->find()
        ->where(['caja_id' => $id, 'tipo' => 'GASTO'])
        ->order(['created' => 'DESC'])
        ->all();
    $totalEgresosExternos = (float)$egresosExternos->sumOf('monto');

    $reembolsosAnulacion = $CajaEgresos
        ->find()
        ->where(['caja_id' => $id, 'tipo' => 'REEMBOLSO_ANULACION'])
        ->order(['created' => 'DESC'])
        ->all();

    $totalReembolsosAnulacion =
        (float)$reembolsosAnulacion->sumOf('monto');

    // Descontar solo los GASTOS del método correspondiente. Los egresos de tipo
    // REEMBOLSO_ANULACION NO se restan aquí: su ingreso original ya fue excluido
    // arriba (el movimiento de la factura ANULADA no se sumó), así que restarlos
    // de nuevo duplicaría el descuento y generaría totales negativos.
    foreach ($CajaEgresos->find()->where(['caja_id' => $id, 'tipo' => 'GASTO'])->all() as $egr) {
        $metodo = strtoupper(trim((string)($egr->metodo_pago ?? 'EFECTIVO')));
        $monto  = (float)$egr->monto;
        match ($metodo) {
            'YAPE'          => $totalYape          -= $monto,
            'TARJETA'       => $totalTarjeta       -= $monto,
            'TRANSFERENCIA' => $totalTransferencia -= $monto,
            'PLIN'          => $totalPlin          -= $monto,
            'OTROS'         => $totalOtros         -= $monto,
            default         => $totalEfectivo      -= $monto,
        };
    }



    // Recalcular total general (neto de egresos)
    $totalGeneral =
        $totalEfectivo +
        $totalYape +
        $totalPlin +
        $totalTransferencia +
        $totalOtros +
        $totalTarjeta;


    $efectivoEsperadoEnCaja =
        (float)$caja->monto_inicial +
        $totalEfectivo;

    // Distribución del cobro (Laboratorio/Materiales) de las facturas cobradas en esta caja
    $invoiceIdsCaja = array_unique(array_map(
        fn($mov) => $mov->invoice_id,
        array_filter($caja->caja_movimientos, fn($mov) => empty($mov->invoice) || $mov->invoice->estado !== 'ANULADO')
    ));

    $distribucionesCaja = new \Cake\Collection\Collection([]);
    $totalDistribuidoCaja = 0.0;
    $totalDistribuidoLaboratorio = 0.0;
    $totalDistribuidoMateriales = 0.0;

    if (!empty($invoiceIdsCaja)) {
        $InvoiceDistribuciones = $this->fetchTable('InvoiceDistribuciones');
        $distribucionesCaja = $InvoiceDistribuciones->find()
            ->contain(['Laboratorios', 'Invoices'])
            ->where(['invoice_id IN' => $invoiceIdsCaja])
            ->order(['InvoiceDistribuciones.created' => 'DESC'])
            ->all();

        foreach ($distribucionesCaja as $dist) {
            $totalDistribuidoCaja += (float) $dist->monto;
            if ($dist->tipo === 'LABORATORIO') {
                $totalDistribuidoLaboratorio += (float) $dist->monto;
            } else {
                $totalDistribuidoMateriales += (float) $dist->monto;
            }
        }
    }

    $this->set(compact(
        'caja',
        'totalEfectivo',
        'totalYape',
        'totalTarjeta',
        'totalPlin',
        'totalOtros',
        'totalTransferencia',

        'totalGeneral',
        'totalEfectivoIngresos',
        'totalYapeIngresos',
        'totalTarjetaIngresos',
        'totalTransferenciaIngresos',
        'totalPlinIngresos',
        'totalOtrosIngresos',
        'totalGeneralIngresos',
        'efectivoEsperadoEnCaja',
        'ingresosExternos',
        'totalIngresosExternos',
        'egresosExternos',
        'totalEgresosExternos',
        'reembolsosAnulacion',
        'totalReembolsosAnulacion',
        'distribucionesCaja',
        'totalDistribuidoCaja',
        'totalDistribuidoLaboratorio',
        'totalDistribuidoMateriales'
    ));
}

    public function pdf($id)
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;

        $caja = $this->Cajas->get($id, [
            'contain' => [
                'Users',
                'CajaMovimientos' => ['Invoices'],
            ],
        ]);

        $totalEfectivo = 0.0;
        $totalYape = 0.0;
        $totalTarjeta = 0.0;
        $totalTransferencia = 0.0;
        $totalPlin = 0.0;
        $totalOtros = 0.0;

        foreach ($caja->caja_movimientos as $mov) {
            if (!empty($mov->invoice) && $mov->invoice->estado === 'ANULADO') {
                continue;
            }

            if ($mov->metodo_pago === 'EFECTIVO') {
                $totalEfectivo += (float)$mov->monto_recibido;
            } elseif ($mov->metodo_pago === 'YAPE') {
                $totalYape += (float)$mov->monto_recibido;
            } elseif ($mov->metodo_pago === 'TARJETA') {
                $totalTarjeta += (float)$mov->monto_recibido;
            } elseif ($mov->metodo_pago === 'TRANSFERENCIA') {
                $totalTransferencia += (float)$mov->monto_recibido;
            } elseif ($mov->metodo_pago === 'PLIN') {
                $totalPlin += (float)$mov->monto_recibido;
            } elseif ($mov->metodo_pago === 'OTROS') {
                $totalOtros += (float)$mov->monto_recibido;
            }
        }

        $CajaIngresos = $this->fetchTable('CajaIngresos');
        $CajaEgresos = $this->fetchTable('CajaEgresos');
        $CajaDenominaciones = $this->fetchTable('CajaDenominaciones');

        $ingresosExternos = $CajaIngresos
            ->find()
            ->where(['caja_id' => $id])
            ->order(['created' => 'DESC'])
            ->all();

        $totalIngresosExternos = (float)$ingresosExternos->sumOf('monto');

        foreach ($ingresosExternos as $ing) {
            $metodo = strtoupper(trim((string)($ing->metodo_pago ?? 'EFECTIVO')));
            $monto = (float)$ing->monto;
            match ($metodo) {
                'YAPE'          => $totalYape          += $monto,
                'TARJETA'       => $totalTarjeta       += $monto,
                'TRANSFERENCIA' => $totalTransferencia += $monto,
                'PLIN'          => $totalPlin          += $monto,
                'OTROS'         => $totalOtros         += $monto,
                default         => $totalEfectivo      += $monto,
            };
        }

        // Totales brutos de ingresos por método (ventas + ingresos externos), sin descontar egresos
        $totalEfectivoIngresos = $totalEfectivo;
        $totalYapeIngresos = $totalYape;
        $totalTarjetaIngresos = $totalTarjeta;
        $totalTransferenciaIngresos = $totalTransferencia;
        $totalPlinIngresos = $totalPlin;
        $totalOtrosIngresos = $totalOtros;

        $totalGeneralIngresos =
            $totalEfectivoIngresos +
            $totalYapeIngresos +
            $totalPlinIngresos +
            $totalTransferenciaIngresos +
            $totalOtrosIngresos +
            $totalTarjetaIngresos;

        $egresosExternos = $CajaEgresos
            ->find()
            ->where(['caja_id' => $id, 'tipo' => 'GASTO'])
            ->order(['created' => 'DESC'])
            ->all();

        $totalEgresosExternos = (float)$egresosExternos->sumOf('monto');

        $reembolsosAnulacion = $CajaEgresos
            ->find()
            ->where(['caja_id' => $id, 'tipo' => 'REEMBOLSO_ANULACION'])
            ->order(['created' => 'DESC'])
            ->all();

        $totalReembolsosAnulacion = (float)$reembolsosAnulacion->sumOf('monto');


        // Descontar solo los GASTOS del método correspondiente (ver explicación en view()):
        // los REEMBOLSO_ANULACION no se restan porque su ingreso original ya fue
        // excluido al no sumar los movimientos de facturas ANULADAS.
        foreach ($CajaEgresos->find()->where(['caja_id' => $id, 'tipo' => 'GASTO'])->all() as $egr) {
            $metodo = strtoupper(trim((string)($egr->metodo_pago ?? 'EFECTIVO')));
            $monto = (float)$egr->monto;
            match ($metodo) {
                'YAPE'          => $totalYape          -= $monto,
                'TARJETA'       => $totalTarjeta       -= $monto,
                'TRANSFERENCIA' => $totalTransferencia -= $monto,
                'PLIN'          => $totalPlin          -= $monto,
                'OTROS'         => $totalOtros         -= $monto,
                default         => $totalEfectivo      -= $monto,
            };
        }

        $totalGeneral =
            $totalEfectivo +
            $totalYape +
            $totalPlin +
            $totalTransferencia +
            $totalOtros +
            $totalTarjeta;

        // Obtener denominaciones de APERTURA y CIERRE
        $denominacionesApertura = $CajaDenominaciones
            ->find()
            ->where(['caja_id' => $id, 'tipo_movimiento' => 'APERTURA'])
            ->order(['valor' => 'DESC'])
            ->all();

        $denominacionesCierre = $CajaDenominaciones
            ->find()
            ->where(['caja_id' => $id, 'tipo_movimiento' => 'CIERRE'])
            ->order(['valor' => 'DESC'])
            ->all();

        $totalApertura = (float)$denominacionesApertura->sumOf('subtotal');
        $totalCierre = (float)$denominacionesCierre->sumOf('subtotal');

        $efectivoEsperadoEnCaja =
            (float)$caja->monto_inicial +
            $totalEfectivo;

        // Total esperado considerando todos los métodos de pago (efectivo + digitales)
        $totalEsperadoGeneral =
            (float)$caja->monto_inicial +
            $totalGeneral;

        // Generar PDF
        $this->viewBuilder()
            ->disableAutoLayout()
            ->setTemplatePath('Cajas')
            ->setTemplate('pdf');

        $this->set(compact(
            'caja',
            'totalEfectivo',
            'totalYape',
            'totalTarjeta',
            'totalPlin',
            'totalOtros',
            'totalTransferencia',
            'totalGeneral',
            'totalEfectivoIngresos',
            'totalYapeIngresos',
            'totalTarjetaIngresos',
            'totalTransferenciaIngresos',
            'totalPlinIngresos',
            'totalOtrosIngresos',
            'totalGeneralIngresos',
            'efectivoEsperadoEnCaja',
            'totalEsperadoGeneral',
            'ingresosExternos',
            'totalIngresosExternos',
            'egresosExternos',
            'totalEgresosExternos',
            'reembolsosAnulacion',
            'totalReembolsosAnulacion',
            'denominacionesApertura',
            'denominacionesCierre',
            'totalApertura',
            'totalCierre'
        ));

        $html = $this->render('pdf')->getBody();

        // Usar DOMPDF
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml((string)$html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfOutput = $dompdf->output();
        $nombrePdf = 'Caja_' . $caja->codigo . '_' . date('Ymd_His') . '.pdf';

        return $this->response
            ->withType('application/pdf')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $nombrePdf . '"')
            ->withStringBody($pdfOutput);
    }

    public function pausar($id)
    {
        $this->request->allowMethod(['post']);

        $caja = $this->Cajas->get($id);

        if ($caja->estado !== 'ABIERTA') {
            $this->Flash->warning('Solo se puede pausar una caja abierta.');
            return $this->redirect(['action' => 'view', $id]);
        }

        $caja->estado = 'PAUSADA';

        if ($this->Cajas->save($caja)) {
            $this->Flash->success('Caja pausada correctamente.');
        } else {
            $this->Flash->error('No se pudo pausar la caja.');
        }

        return $this->redirect(['action' => 'view', $id]);
    }

    public function reanudar($id)
    {
        $this->request->allowMethod(['post']);

        $caja = $this->Cajas->get($id);

        if ($caja->estado !== 'PAUSADA') {
            $this->Flash->warning('Solo se puede reanudar una caja pausada.');
            return $this->redirect(['action' => 'view', $id]);
        }

        $cajaActivaMisma = $this->Cajas->find()
            ->where([
                'id !=' => $caja->id,
                'nombre' => $caja->nombre,
                'codigo' => $caja->codigo,
                'estado' => 'ABIERTA',
            ])
            ->first();

        if ($cajaActivaMisma) {
            $this->Flash->warning('No se puede reanudar porque esa caja ya está abierta en otro registro.');
            return $this->redirect(['action' => 'view', $id]);
        }

        $usuarioConCajaAbierta = $this->Cajas->find()
            ->where([
                'id !=' => $caja->id,
                'user_id' => $caja->user_id,
                'estado' => 'ABIERTA',
            ])
            ->first();

        if ($usuarioConCajaAbierta) {
            $this->Flash->warning('Ese usuario ya tiene otra caja abierta.');
            return $this->redirect(['action' => 'view', $id]);
        }

        $caja->estado = 'ABIERTA';

        if ($this->Cajas->save($caja)) {
            $this->Flash->success('Caja reanudada correctamente.');
        } else {
            $this->Flash->error('No se pudo reanudar la caja.');
        }

        return $this->redirect(['action' => 'view', $id]);
    }

   public function cerrar($id)
{
    $caja = $this->Cajas->get($id, [
        'contain' => ['CajaMovimientos'],
    ]);

    if (!in_array($caja->estado, ['ABIERTA', 'PAUSADA'], true)) {
        $this->Flash->warning('La caja ya está cerrada.');
        return $this->redirect(['action' => 'view', $id]);
    }

    $totalEfectivo = 0;

    foreach ($caja->caja_movimientos as $mov) {

        if ($mov->metodo_pago === 'EFECTIVO') {
            $totalEfectivo += (float)$mov->monto_recibido;
        }
    }

    $esperado = (float)$caja->monto_inicial + $totalEfectivo;

    if ($this->request->is(['post', 'put'])) {

        $data = $this->request->getData();

        $montoReal = (float)($data['monto_cierre'] ?? 0);

        $caja->monto_cierre = $montoReal;
        $caja->estado = 'CERRADA';

        if ($this->Cajas->save($caja)) {

            // ==========================
            // GUARDAR DENOMINACIONES
            // ==========================

            $CajaDenominaciones = $this->fetchTable('CajaDenominaciones');

            $denominaciones = $data['denominaciones'] ?? [];

            $valores = [
                1 => 200,
                2 => 100,
                3 => 50,
                4 => 20,
                5 => 10,
                6 => 5,
                7 => 2,
                8 => 1,
                9 => 0.50,
                10 => 0.20,
                11 => 0.10,
            ];

            foreach ($denominaciones as $idDen => $cantidad) {

                $cantidad = (int)$cantidad;

                if ($cantidad <= 0) {
                    continue;
                }

                $valor = $valores[$idDen] ?? 0;

                $detalle = $CajaDenominaciones->newEmptyEntity();

                $detalle->caja_id = $caja->id;
                $detalle->tipo_movimiento = 'CIERRE';
                $detalle->tipo = $valor >= 1 ? 'BILLETE' : 'MONEDA';
                $detalle->valor = $valor;
                $detalle->cantidad = $cantidad;
                $detalle->subtotal = $valor * $cantidad;

                $CajaDenominaciones->save($detalle);
            }

            $this->Flash->success('Caja cerrada correctamente.');

            return $this->redirect([
                'action' => 'view',
                $id
            ]);
        }

        $this->Flash->error('No se pudo cerrar.');
    }

    $denominaciones = [
        ['id'=>1,'tipo'=>'BILLETE','valor'=>200],
        ['id'=>2,'tipo'=>'BILLETE','valor'=>100],
        ['id'=>3,'tipo'=>'BILLETE','valor'=>50],
        ['id'=>4,'tipo'=>'BILLETE','valor'=>20],
        ['id'=>5,'tipo'=>'BILLETE','valor'=>10],
        ['id'=>6,'tipo'=>'MONEDA','valor'=>5],
        ['id'=>7,'tipo'=>'MONEDA','valor'=>2],
        ['id'=>8,'tipo'=>'MONEDA','valor'=>1],
        ['id'=>9,'tipo'=>'MONEDA','valor'=>0.50],
        ['id'=>10,'tipo'=>'MONEDA','valor'=>0.20],
        ['id'=>11,'tipo'=>'MONEDA','valor'=>0.10],
    ];

    // Obtener usuario actual
    $usuarioActual = $this->Authentication->getIdentity();
    $userIdActual = $usuarioActual?->id ?? null;

    $this->set(compact(
        'caja',
        'esperado',
        'denominaciones',
        'userIdActual'
    ));
}

public function agregarIngreso($id)
{
    $caja = $this->Cajas->get($id);

    if ($caja->estado !== 'ABIERTA') {

        $this->Flash->error(
            'La caja debe estar abierta.'
        );

        return $this->redirect([
            'action' => 'view',
            $id
        ]);
    }

    $CajaIngresos = $this->fetchTable('CajaIngresos');


    // Crear entidad vacía
    $ingreso = $CajaIngresos->newEmptyEntity();


    if ($this->request->is(['post'])) {

        $data = $this->request->getData();


        $ingreso = $CajaIngresos->patchEntity(
            $ingreso,
            [
                'caja_id'     => $id,
                'monto'       => $data['monto'],
                'descripcion' => $data['descripcion'],
                'metodo_pago' => strtoupper(
                    trim((string)($data['metodo_pago'] ?? 'EFECTIVO'))
                ),
            ]
        );


        if ($CajaIngresos->save($ingreso)) {

            $this->Flash->success(
                'Ingreso registrado.'
            );

            return $this->redirect([
                'action' => 'view',
                $id
            ]);
        }


        $this->Flash->error(
            'No se pudo registrar.'
        );
    }


    $this->set(compact(
        'caja',
        'ingreso'
    ));
}
    public function agregarEgreso($id)
{
    $caja = $this->Cajas->get($id);

    if ($caja->estado !== 'ABIERTA') {

        $this->Flash->error(
            'La caja debe estar abierta.'
        );

        return $this->redirect([
            'action' => 'view',
            $id
        ]);
    }

    $CajaEgresos = $this->fetchTable(
        'CajaEgresos'
    );

    $egreso = $CajaEgresos->newEmptyEntity();
    $montoPrelleno       = (float)$this->request->getQuery('monto', 0);
    $descripcionPrelleno = (string)$this->request->getQuery('descripcion', '');

    $this->set(compact('caja', 'egreso', 'montoPrelleno', 'descripcionPrelleno'));

    if ($this->request->is(['post'])) {

        $data = $this->request->getData();

        $monto = (float)($data['monto'] ?? 0);

        if ($monto <= 0) {

            $this->Flash->error(
                'El monto debe ser mayor a 0.'
            );

            return $this->redirect([
                'action' => 'agregarEgreso',
                $id
            ]);
        }

        $egreso = $CajaEgresos->patchEntity(
            $egreso,
            [
                'caja_id' => $id,
                'monto' => $monto,
                'descripcion' => $data['descripcion'],
                'metodo_pago' => strtoupper(trim((string)($data['metodo_pago'] ?? 'EFECTIVO'))),

            ]
        );

        if ($CajaEgresos->save($egreso)) {

            $this->Flash->success(
                'Egreso registrado.'
            );

            return $this->redirect([
                'action' => 'view',
                $id
            ]);
        }

        $this->Flash->error(
            'No se pudo registrar.'
        );
    }

    $this->set(compact(
        'caja',
        'egreso'
    ));
}

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Todas las acciones requieren autenticación
    }
}