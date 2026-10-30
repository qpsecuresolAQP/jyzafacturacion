<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\ConfiguracionesTable;
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

    /**
     * Totales de una caja, únicos para la pantalla, el PDF y el cierre para que
     * siempre muestren las mismas cifras.
     *
     * Por método de pago: ventas cobradas (sin comprobantes anulados) + ingresos
     * externos = ingresos brutos; menos los gastos = neto. Los reembolsos por
     * anulación NO se restan: el cobro original ya se excluyó al ignorar los
     * movimientos de comprobantes ANULADOS (restarlos otra vez duplicaría el descuento).
     *
     * @param \App\Model\Entity\Caja $caja Con CajaMovimientos => Invoices cargados
     */
    private function calcularTotalesCaja($caja): array
    {
        $metodos = ['EFECTIVO' => 'Efectivo', 'YAPE' => 'Yape', 'TARJETA' => 'Tarjeta', 'TRANSFERENCIA' => 'Transferencia', 'PLIN' => 'Plin', 'OTROS' => 'Otros'];
        $ingresos = array_fill_keys(array_keys($metodos), 0.0);
        $neto = $ingresos;

        $normalizar = function ($metodo) use ($metodos): string {
            $m = strtoupper(trim((string) ($metodo ?? 'EFECTIVO')));

            return isset($metodos[$m]) ? $m : 'EFECTIVO';
        };

        foreach ($caja->caja_movimientos as $mov) {
            if (!empty($mov->invoice) && $mov->invoice->estado === 'ANULADO') {
                continue;
            }
            $m = strtoupper(trim((string) $mov->metodo_pago));
            if (isset($metodos[$m])) {
                $ingresos[$m] += (float) $mov->monto_recibido;
            }
        }
        $ventasEfectivo = $ingresos['EFECTIVO'];
        $ingresosExternosEfectivo = 0.0;
        $gastosEfectivo = 0.0;

        $ingresosExternos = $this->fetchTable('CajaIngresos')->find()
            ->where(['caja_id' => $caja->id])->order(['created' => 'DESC'])->all();
        foreach ($ingresosExternos as $ing) {
            $m = $normalizar($ing->metodo_pago);
            $ingresos[$m] += (float) $ing->monto;
            if ($m === 'EFECTIVO') {
                $ingresosExternosEfectivo += (float) $ing->monto;
            }
        }

        $CajaEgresos = $this->fetchTable('CajaEgresos');
        $egresosExternos = $CajaEgresos->find()
            ->where(['caja_id' => $caja->id, 'tipo' => 'GASTO'])->order(['created' => 'DESC'])->all();
        $reembolsosAnulacion = $CajaEgresos->find()
            ->where(['caja_id' => $caja->id, 'tipo' => 'REEMBOLSO_ANULACION'])->order(['created' => 'DESC'])->all();

        $neto = $ingresos;
        foreach ($egresosExternos as $egr) {
            $m = $normalizar($egr->metodo_pago);
            $neto[$m] -= (float) $egr->monto;
            if ($m === 'EFECTIVO') {
                $gastosEfectivo += (float) $egr->monto;
            }
        }

        $totalGeneralIngresos = array_sum($ingresos);
        $totalGeneral = array_sum($neto);

        return [
            'totalEfectivo' => $neto['EFECTIVO'],
            'totalYape' => $neto['YAPE'],
            'totalTarjeta' => $neto['TARJETA'],
            'totalTransferencia' => $neto['TRANSFERENCIA'],
            'totalPlin' => $neto['PLIN'],
            'totalOtros' => $neto['OTROS'],
            'totalGeneral' => $totalGeneral,
            'totalEfectivoIngresos' => $ingresos['EFECTIVO'],
            'totalYapeIngresos' => $ingresos['YAPE'],
            'totalTarjetaIngresos' => $ingresos['TARJETA'],
            'totalTransferenciaIngresos' => $ingresos['TRANSFERENCIA'],
            'totalPlinIngresos' => $ingresos['PLIN'],
            'totalOtrosIngresos' => $ingresos['OTROS'],
            'totalGeneralIngresos' => $totalGeneralIngresos,
            'ingresosExternos' => $ingresosExternos,
            'totalIngresosExternos' => (float) $ingresosExternos->sumOf('monto'),
            'egresosExternos' => $egresosExternos,
            'totalEgresosExternos' => (float) $egresosExternos->sumOf('monto'),
            'reembolsosAnulacion' => $reembolsosAnulacion,
            'totalReembolsosAnulacion' => (float) $reembolsosAnulacion->sumOf('monto'),
            'ventasEfectivo' => $ventasEfectivo,
            'ingresosExternosEfectivo' => $ingresosExternosEfectivo,
            'gastosEfectivo' => $gastosEfectivo,
            'efectivoEsperadoEnCaja' => (float) $caja->monto_inicial + $neto['EFECTIVO'],
            'totalEsperadoGeneral' => (float) $caja->monto_inicial + $totalGeneral,
        ];
    }

    public function view($id)
{
    $caja = $this->Cajas->get($id, [
        'contain' => [
            'Users',
            'CajaMovimientos' => ['Invoices'],
        ],
    ]);

    extract($this->calcularTotalesCaja($caja));

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

        extract($this->calcularTotalesCaja($caja));
        $CajaDenominaciones = $this->fetchTable('CajaDenominaciones');

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
            'ventasEfectivo',
            'ingresosExternosEfectivo',
            'gastosEfectivo',
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
        'contain' => ['CajaMovimientos' => ['Invoices']],
    ]);

    if (!in_array($caja->estado, ['ABIERTA', 'PAUSADA'], true)) {
        $this->Flash->warning('La caja ya está cerrada.');
        return $this->redirect(['action' => 'view', $id]);
    }

    $esperado = (float) $this->calcularTotalesCaja($caja)['efectivoEsperadoEnCaja'];

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

        // Si el administrador definió una clave de caja, los demás usuarios deben
        // ingresarla una vez por sesión antes de usar cualquier pantalla de Caja.
        $accion = $this->request->getParam('action');
        if (in_array($accion, ['desbloquear', 'clave'], true) || !$this->cajaBloqueada()) {
            return;
        }

        $event->setResult($this->redirect([
            'action' => 'desbloquear',
            '?' => ['redirect' => $this->request->getRequestTarget()],
        ]));
        $event->stopPropagation();
    }

    /**
     * Pide la clave de caja y, si es correcta, la deja habilitada hasta que termine la sesión.
     */
    public function desbloquear()
    {
        $destino = (string)$this->request->getQuery('redirect', '');
        // Solo rutas internas, para no redirigir fuera del sistema
        if ($destino === '' || !str_starts_with($destino, '/') || str_starts_with($destino, '//')) {
            $destino = '/cajas';
        }

        if (!$this->cajaBloqueada()) {
            return $this->redirect($destino);
        }

        if ($this->request->is('post')) {
            $hash = $this->fetchTable('Configuraciones')->obtener(ConfiguracionesTable::CAJA_PASSWORD_HASH);
            if ($hash && password_verify((string)$this->request->getData('clave'), $hash)) {
                $this->request->getSession()->write(self::SESION_CAJA, $hash);

                return $this->redirect($destino);
            }

            $this->set('error', 'La clave de caja es incorrecta.');
        }

        $this->set(compact('destino'));
    }

    /**
     * Solo el administrador: define, cambia o quita la clave de caja.
     */
    public function clave()
    {
        if (!$this->esAdministrador()) {
            echo '<script>alert("❌ Solo el administrador puede configurar la clave de caja"); window.history.back();</script>';
            exit;
        }

        $Configuraciones = $this->fetchTable('Configuraciones');
        $tieneClave = (bool)$Configuraciones->obtener(ConfiguracionesTable::CAJA_PASSWORD_HASH);

        if ($this->request->is('post')) {
            if ($this->request->getData('quitar')) {
                $Configuraciones->guardar(ConfiguracionesTable::CAJA_PASSWORD_HASH, null);

                return $this->redirect(['action' => 'index']);
            }

            $clave = (string)$this->request->getData('clave');
            $confirmacion = (string)$this->request->getData('confirmacion');

            if (mb_strlen($clave) < 4) {
                $this->set('error', 'La clave debe tener al menos 4 caracteres.');
            } elseif ($clave !== $confirmacion) {
                $this->set('error', 'La clave y su confirmación no coinciden.');
            } else {
                // Al cambiar el hash, las sesiones que ya habían ingresado la clave anterior vuelven a pedirla.
                $Configuraciones->guardar(ConfiguracionesTable::CAJA_PASSWORD_HASH, password_hash($clave, PASSWORD_DEFAULT));

                return $this->redirect(['action' => 'index']);
            }
        }

        $this->set(compact('tieneClave'));
    }

    private const SESION_CAJA = 'Caja.claveDesbloqueada';

    private function esAdministrador(): bool
    {
        return $this->Permisos->tieneRol([1]);
    }

    private function cajaBloqueada(): bool
    {
        if ($this->esAdministrador()) {
            return false;
        }

        $hash = $this->fetchTable('Configuraciones')->obtener(ConfiguracionesTable::CAJA_PASSWORD_HASH);
        if (!$hash) {
            return false;
        }

        // Se guarda el hash vigente en la sesión: si el administrador cambia la clave, deja de coincidir.
        return $this->request->getSession()->read(self::SESION_CAJA) !== $hash;
    }
}