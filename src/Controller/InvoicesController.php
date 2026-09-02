<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\SunatService;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company as GreCompany;
use Greenter\Model\Client\Client;
use Greenter\Model\Sale\Invoice as GreInvoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Summary\Summary;
use Greenter\Model\Summary\SummaryDetail;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Cake\Core\Configure;
use Greenter\Model\Voided\Voided;
use Greenter\Model\Voided\VoidedDetail;
//use Cake\Http\Client;
use Dompdf\Dompdf;
use Dompdf\Options;
use Cake\Routing\Router;
use Greenter\Model\Sale\Cuota;
use Greenter\Model\Sale\FormaPagos\FormaPagoCredito;
class InvoicesController extends AppController
{
    public function index()
    {
        $Cajas = $this->fetchTable('Cajas');

        $identity = $this->request->getAttribute('identity');
        $userId = $identity ? (int) $identity->getIdentifier() : 0;

        $cajaAbierta = $Cajas->find()
            ->where([
                'Cajas.estado' => 'ABIERTA',
            ])
            ->order(['Cajas.created' => 'DESC'])
            ->first();
        if (!$cajaAbierta) {
            $this->Flash->error('Debe haber una caja abierta antes de gestionar comprobantes.');
            return $this->redirect(['controller' => 'Cajas', 'action' => 'abrir']);
        }

        $query = $this->Invoices->find()
        ->contain([
            'Companies',
            'Pacientes',
            'HistoriasClinicas',
            'Doctores',
            'Users',
            'InvoiceDistribuciones',
            'InvoiceCuotas' => function ($q) {
                return $q->order(['InvoiceCuotas.numero_cuota' => 'ASC']);
            },
        ])
        ->order(['Invoices.created' => 'DESC']);
        $tipo = $this->request->getQuery('tipo');
        if ($tipo === 'RI') {
            $query->where(['Invoices.tipo_doc' => 'RI']);
        } elseif (in_array($tipo, ['01', '03'], true)) {
            $query->where(['Invoices.tipo_doc' => $tipo]);
        }
        if ($this->request->getQuery('credito') === '1') {
        $query->where([
            'Invoices.forma_pago' => 'CREDITO',
            'Invoices.id IN' => $this->fetchTable('InvoiceCuotas')->find()
                ->select(['invoice_id'])
                ->where(['estado' => 'PENDIENTE'])
                ->distinct(['invoice_id']),
        ]);
}

// NUEVO: búsqueda por nombre de cliente
$busquedaCliente = trim((string) $this->request->getQuery('cliente', ''));
if ($busquedaCliente !== '') {
    $query->where(['Invoices.cliente_nombre LIKE' => '%' . $busquedaCliente . '%']);
}

// NUEVO: búsqueda por número de documento (DNI/RUC)
$busquedaDocumento = trim((string) $this->request->getQuery('documento', ''));
if ($busquedaDocumento !== '') {
    $query->where(['Invoices.cliente_numero LIKE' => '%' . $busquedaDocumento . '%']);
}

// NUEVO: filtro de comprobantes sin doctor asignado
if ($this->request->getQuery('sin_doctor') === '1') {
    $query->where(['Invoices.doctor_id IS' => null]);
}

        $invoices = $this->paginate($query);

        $doctores = $this->fetchTable('Doctores')->find('list', [
            'keyField' => 'id',
            'valueField' => function ($row) {
                return trim((string) $row->nombre . ' ' . (string) $row->apellido) . ' - ' . (string) $row->especialidad;
            },
            'order' => ['nombre' => 'ASC', 'apellido' => 'ASC'],
        ])->toArray();

        $this->set(compact('invoices', 'cajaAbierta', 'doctores'));
    }

    public function asignarDoctor($id = null)
    {
        $this->request->allowMethod(['post']);

        $invoice = $this->Invoices->get($id);

        $doctorId = $this->request->getData('doctor_id');
        $invoice->doctor_id = !empty($doctorId) ? (int) $doctorId : null;

        if ($this->Invoices->save($invoice)) {
            $this->Flash->success($invoice->doctor_id ? 'Doctor asignado correctamente.' : 'Doctor removido del comprobante.');
        } else {
            $this->Flash->error('No se pudo asignar el doctor.');
        }

        return $this->redirect($this->referer(['action' => 'index'], true));
    }

    public function view($id = null)
{
    $invoice = $this->Invoices->get($id, [
        'contain' => [
            'Companies',
            'Pacientes',
            'HistoriasClinicas',
            'Doctores',
            'Users',
            'InvoiceItems' => ['Tratamientos'],
            'DailySummaries',
            'CajaMovimientos',
            'InvoiceDistribuciones' => ['Laboratorios'],
        ],
    ]);

    // NUEVO: traer cuotas si es crédito
    $cuotas = [];
    if (($invoice->forma_pago ?? null) === 'CREDITO') {
        $cuotas = $this->fetchTable('InvoiceCuotas')->find()
            ->where(['invoice_id' => $invoice->id])
            ->order(['numero_cuota' => 'ASC'])
            ->all()
            ->toList();
    }

    $pagosComprobante = $this->Invoices->CajaMovimientos->find()
        ->where(['invoice_id' => $invoice->id])
        ->orderBy(['CajaMovimientos.id' => 'ASC']);

    if (!empty($invoice->caja_id)) {
        $pagosComprobante->where(['caja_id' => $invoice->caja_id]);
    }

    $pagosComprobante = $pagosComprobante->all()->toList();

    $this->set(compact('invoice', 'pagosComprobante', 'cuotas'));

    if ($this->request->is('ajax')) {
        $this->viewBuilder()->setLayout('ajax');
    } else {
        $this->viewBuilder()->setLayout('default');
    }
}
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Todas las acciones requieren autenticación
    }

    public function add($cajaId = null)
    {
        $cajaId = $cajaId ?? (int) $this->request->getQuery('caja_id', 0);

        // Evitar que el navegador sirva una copia cacheada de este formulario
        // (el JS de métodos de pago híbridos debe ser siempre la versión vigente).
        $this->response = $this->response
            ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->withHeader('Pragma', 'no-cache');

        if ($this->request->is('get')) {
            // Obtener usuario autenticado
            $identity = $this->request->getAttribute('identity');
            if (!$identity) {
                return $this->redirect(['action' => 'index']);
            }

            $userId = (int) $identity->getIdentifier();
            $userName = (string) ($identity->get('username') ?? '');

            // Obtener cajas abiertas disponibles para crear comprobantes
            $Cajas = $this->fetchTable('Cajas');
            $cajasAbiertas = $Cajas->find()
                ->where(['estado' => 'ABIERTA'])
                ->order(['created' => 'DESC'])
                ->all()
                ->toList();

            // Si no hay cajas abiertas, redirigir
            if (empty($cajasAbiertas)) {
                $this->Flash->error('Debe haber una caja abierta antes de crear comprobantes.');
                return $this->redirect(['controller' => 'Cajas', 'action' => 'abrir']);
            }

            // Si viene caja_id, validar que exista y esté abierta
            $cajaSeleccionada = null;
            if ($cajaId) {
                $cajaSeleccionada = $Cajas->find()
                    ->where(['id' => (int) $cajaId, 'estado' => 'ABIERTA'])
                    ->first();

                if (!$cajaSeleccionada) {
                    $this->Flash->error('La caja no existe o no está abierta.');
                    return $this->redirect(['action' => 'add']);
                }
            } else {
                // Si no viene caja_id, no hay caja pre-seleccionada
                // El usuario debe elegir en el formulario
                $cajaSeleccionada = null;
            }

            $companies = $this->Invoices->Companies->find('list', [
                'keyField' => 'id',
                'valueField' => 'razon_social',
                'order' => ['razon_social' => 'ASC']
            ])->toArray();



            $historiasClinicas = $this->Invoices->HistoriasClinicas->find('list', [
                'keyField' => 'id',
                'valueField' => function ($row) {
                    return 'HC #' . $row->id;
                },
                'order' => ['id' => 'DESC']
            ])->toArray();

            $tratamientos = $this->Invoices->InvoiceItems->Tratamientos->find()
                ->select(['id', 'nombre', 'costo'])
                ->where(['estado' => 1])
                ->order(['nombre' => 'ASC'])
                ->all()
                ->toList();
            $productos = $this->fetchTable('Productos')
                ->find()
                ->where(['estado' => 1])
                ->order(['nombre' => 'ASC'])
                ->all()
                ->toList();

            $examenes = $this->fetchTable('Examenes')
                ->find()
                ->contain(['CategoriasExamenes'])
                ->where(['Examenes.estado' => 1])
                ->order(['Examenes.nombre' => 'ASC'])
                ->all()
                ->toList();

            $doctores = $this->fetchTable('Doctores')->find('list', [
                'keyField' => 'id',
                'valueField' => function ($row) {
                    return trim((string) $row->nombre . ' ' . (string) $row->apellido) . ' - ' . (string) $row->especialidad;
                },
                'order' => ['nombre' => 'ASC', 'apellido' => 'ASC']
            ])->toArray();

            $session = $this->request->getSession();
            $oldFormData = $session->read('old_invoice_form') ?? [];
            if (!empty($oldFormData)) {
                $session->delete('old_invoice_form');
            }

            // Obtener paciente_id del query string si viene
            $pacienteId = (int) $this->request->getQuery('paciente_id', 0);
            $pacientePrelleno = null;

            // Prellenar factura desde un presupuesto (?presupuesto_id=X)
            $presupuestoId = (int) $this->request->getQuery('presupuesto_id', 0);
            if ($presupuestoId > 0) {
                $Presupuestos = $this->fetchTable('Presupuestos');

                try {
                    $presupuesto = $Presupuestos->get($presupuestoId, [
                        'contain' => [
                            'HistoriasClinicas',
                            'PresupuestosTratamientos.Tratamientos',
                            'PresupuestosTratamientos.Productos',
                            'PresupuestosTratamientos.Examenes',
                            'PresupuestosInvoices.Invoices',
                        ],
                    ]);

                    if (!empty($presupuesto->historias_clinica)) {
                        $pacienteId = (int) $presupuesto->historias_clinica->paciente_id;
                    }

                    // Facturas vigentes (no anuladas) generadas desde este presupuesto
                    $invoiceIdsPresupuesto = array_map(
                        fn($pi) => $pi->invoice_id,
                        array_filter(
                            $presupuesto->presupuestos_invoices,
                            fn($pi) => ($pi->invoice->estado ?? '') !== 'ANULADO'
                        )
                    );

                    $montoFacturadoPorItem = [];
                    if (!empty($invoiceIdsPresupuesto)) {
                        $itemsFacturados = $this->Invoices->InvoiceItems->find()
                            ->where(['invoice_id IN' => $invoiceIdsPresupuesto])
                            ->all();

                        foreach ($itemsFacturados as $item) {
                            $claveItem = ($item->tipo_item ?? 'tratamiento') . '_' . (
                                $item->tratamiento_id ?? $item->producto_id ?? $item->examen_id
                            );
                            $montoFacturadoPorItem[$claveItem] =
                                ($montoFacturadoPorItem[$claveItem] ?? 0) + (float) $item->total;
                        }
                    }

                    $itemsPresupuestoPendientes = [];
                    foreach ($presupuesto->presupuestos_tratamientos as $detalle) {
                        $tipoItem = $detalle->tipo_item ?: 'tratamiento';
                        $refId = $tipoItem === 'producto'
                            ? $detalle->producto_id
                            : ($tipoItem === 'examen' ? $detalle->examen_id : $detalle->tratamiento_id);
                        $nombreItem = $tipoItem === 'producto'
                            ? ($detalle->producto->nombre ?? '')
                            : ($tipoItem === 'examen' ? ($detalle->examene->nombre ?? '') : ($detalle->tratamiento->nombre ?? ''));

                        $claveItem = $tipoItem . '_' . $refId;
                        $facturado = (float) ($montoFacturadoPorItem[$claveItem] ?? 0);
                        $saldoLinea = round((float) $detalle->total - $facturado, 2);

                        if ($saldoLinea <= 0) {
                            continue;
                        }

                        $itemBase = [
                            'tipo_item' => $tipoItem,
                            'tratamiento_id' => $tipoItem === 'tratamiento' ? $refId : null,
                            'producto_id' => $tipoItem === 'producto' ? $refId : null,
                            'examen_id' => $tipoItem === 'examen' ? $refId : null,
                            'desde_presupuesto' => true,
                        ];

                        if ($facturado <= 0) {
                            // Nada facturado aún: el precio unitario ya viene neto (con el descuento
                            // del presupuesto aplicado una sola vez). El descuento se manda solo como
                            // dato informativo/de registro: no vuelve a restarse en el cálculo de la
                            // línea, para poder permitir pagos parciales (editar cantidad/precio) sin
                            // aplicar el descuento dos veces.
                            $precioNetoUnitario = $detalle->cantidad > 0
                                ? round((float) $detalle->total / (float) $detalle->cantidad, 2)
                                : (float) $detalle->total;

                            $itemsPresupuestoPendientes[] = $itemBase + [
                                'descripcion' => $nombreItem,
                                'cantidad' => $detalle->cantidad,
                                'precio_unitario' => $precioNetoUnitario,
                                'descuento' => $detalle->descuento,
                                'descuento_tipo' => $detalle->descuento_tipo ?? 'porcentaje',
                            ];
                        } else {
                            // Facturación parcial previa: el saldo restante ya no corresponde a un
                            // precio/descuento "limpio", se factura el residual sin descuento adicional.
                            $itemsPresupuestoPendientes[] = $itemBase + [
                                'descripcion' => $nombreItem . ' (saldo pendiente)',
                                'cantidad' => 1,
                                'precio_unitario' => $saldoLinea,
                                'descuento' => 0,
                                'descuento_tipo' => 'porcentaje',
                            ];
                        }
                    }

                    if (!empty($itemsPresupuestoPendientes)) {
                        $oldFormData['items'] = $itemsPresupuestoPendientes;
                        $oldFormData['presupuesto_id'] = $presupuestoId;
                    } else {
                        $this->Flash->warning('Este presupuesto ya fue facturado en su totalidad.');
                    }
                } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                    $this->Flash->error(__('Presupuesto no encontrado'));
                }
            }

            if ($pacienteId > 0) {
                $pacientePrelleno = $this->Invoices->Pacientes->find()
                    ->leftJoinWith('HistoriasClinicas')
                    ->select([
                        'Pacientes.id',
                        'Pacientes.nombre',
                        'Pacientes.apellido',
                        'HistoriasClinicas.dni'
                    ])
                    ->where(['Pacientes.id' => $pacienteId])
                    ->first();

                // Extraer el DNI de _matchingData si está presente
                if ($pacientePrelleno && isset($pacientePrelleno->_matchingData['HistoriasClinicas']['dni'])) {
                    // Crear un objeto con acceso más fácil al DNI
                    $historia = new \stdClass();
                    $historia->dni = $pacientePrelleno->_matchingData['HistoriasClinicas']['dni'];
                    $pacientePrelleno->historias_clinicas = $historia;
                }
            }

            $this->set(compact(
                'userId',
                'userName',
                'companies',
                'productos',
                'examenes',
                'historiasClinicas',
                'tratamientos',
                'doctores',
                'cajaSeleccionada',
                'cajasAbiertas',
                'cajaId',
                'oldFormData',
                'pacienteId',
                'pacientePrelleno',
                'presupuestoId'
            ));

            return;
        }

        $data = $this->request->getData();
        $isJson = str_contains($this->request->getHeaderLine('Content-Type'), 'application/json');

        // Obtener usuario autenticado (requerido)
        $identity = $this->request->getAttribute('identity');
        if (!$identity) {
            return $this->redirect(['action' => 'index']);
        }

        $userId = (int) $identity->getIdentifier();

        $invoice = $this->Invoices->newEmptyEntity();
        $Cajas = $this->fetchTable('Cajas');
        $CajaMovimientos = $this->fetchTable('CajaMovimientos');

        // Si viene caja_id en POST, validar y usar esa caja
        $cajaIdFromPost = (int) ($data['caja_id'] ?? 0);

        if ($cajaIdFromPost <= 0) {
            $this->Flash->error('Debes seleccionar una caja para crear el comprobante.');
            return $this->redirect(['action' => 'add']);
        }

        // Validar que la caja sea abierta
        $cajaAbierta = $Cajas->find()
            ->contain(['Users'])
            ->where([
                'Cajas.id' => $cajaIdFromPost,
                'Cajas.estado' => 'ABIERTA'
            ])
            ->first();

        if (!$cajaAbierta) {
            $this->Flash->error('La caja seleccionada no existe o no está abierta.');
            return $this->redirect(['action' => 'add']);
        }

        // Procesar métodos de pago híbridos
        $paymentMethodsJson = $data['payment_methods_json'] ?? '[]';
        $paymentMethods = json_decode($paymentMethodsJson, true) ?? [];

        // NUEVO: forma de pago (contado / crédito)
        $formaPago = strtoupper(trim((string) ($data['forma_pago'] ?? 'CONTADO')));
        if (!in_array($formaPago, ['CONTADO', 'CREDITO'], true)) {
            $formaPago = 'CONTADO';
        }

        // NUEVO: cuotas de crédito (solo se usan si forma_pago === 'CREDITO')
        $cuotasJson = $data['cuotas_json'] ?? '[]';
        $cuotasInput = json_decode($cuotasJson, true) ?? [];

        try {
            $companyId = (int) ($data['company_id'] ?? 0);
            $pacienteId = !empty($data['paciente_id']) ? (int) $data['paciente_id'] : null;
            $historiaClinicaId = !empty($data['historia_clinica_id']) ? (int) $data['historia_clinica_id'] : null;
            $doctorId = !empty($data['doctor_id']) ? (int) $data['doctor_id'] : null;
            $tipoDoc = (string) ($data['tipo_doc'] ?? '03');

            if (!in_array($tipoDoc, ['01', '03', 'RI'], true)) {

                return $this->response
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'ok' => false,
                        'message' => 'Tipo de documento inválido',
                        'data' => $data,
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }

           // Asignar serie según tipo_doc INMEDIATAMENTE al crear
            $serie = null;
            $correlativo = null;

            if ($tipoDoc === 'RI') {
                $serie = 'RI';
                $correlativo = $this->obtenerSiguienteCorrelativo($companyId, $serie);
            } elseif ($tipoDoc === '03') {
                $serie = 'B001';
                $correlativo = $this->obtenerSiguienteCorrelativo($companyId, $serie);
            } elseif ($tipoDoc === '01') {
                $serie = 'F001';
                $correlativo = $this->obtenerSiguienteCorrelativo($companyId, $serie);
            }

            $clienteFacturacionId = null;

            $clienteTipoDoc = null;
            $clienteNumero = null;
            $clienteNombre = null;
            $clienteDireccion = null;
            $clienteEmail = null;

            if ($tipoDoc === '03') {
                $clienteTipoDoc = trim((string) ($data['boleta_tipo_doc'] ?? '1'));

                if (!in_array($clienteTipoDoc, ['1', '4'], true)) {
                    $clienteTipoDoc = '1';
                }

                $clienteNumero = trim((string) ($data['boleta_dni'] ?? ''));
                $clienteNombre = trim((string) ($data['boleta_nombre'] ?? ''));

                if ($clienteTipoDoc === '1' && !preg_match('/^\d{8}$/', $clienteNumero)) {
                    throw new \RuntimeException('La boleta requiere DNI válido de 8 dígitos.');
                }

                if ($clienteTipoDoc === '4' && !preg_match('/^[A-Za-z0-9]{9,12}$/', $clienteNumero)) {
                    throw new \RuntimeException('El carnet de extranjería debe tener entre 9 y 12 caracteres alfanuméricos.');
                }

                if ($clienteNombre === '') {
                    throw new \RuntimeException('La boleta requiere nombre del cliente.');
                }
            }
            if ($tipoDoc === 'RI') {
                $clienteTipoDoc = trim((string) ($data['boleta_tipo_doc'] ?? '1'));
                $clienteNumero  = trim((string) ($data['boleta_dni']      ?? ''));
                $clienteNombre  = trim((string) ($data['boleta_nombre']   ?? ''));
            }

            if ($tipoDoc === '01') {
                $clienteTipoDoc = '6';
                $clienteNumero = trim((string) ($data['factura_ruc'] ?? ''));
                $clienteNombre = trim((string) ($data['factura_razon_social'] ?? ''));
                $clienteDireccion = trim((string) ($data['factura_direccion'] ?? ''));
                $clienteEmail = trim((string) ($data['factura_email'] ?? ''));

                if (!preg_match('/^\d{11}$/', $clienteNumero)) {
                    throw new \RuntimeException('La factura requiere un RUC válido de 11 dígitos.');
                }

                if ($clienteNombre === '') {
                    throw new \RuntimeException('La factura requiere razón social.');
                }
            }

            $invoiceData = [
                'company_id' => $companyId,
                'paciente_id' => $pacienteId,
                'historia_clinica_id' => $historiaClinicaId,
                'doctor_id' => $doctorId,
                'user_id' => $userId,
                'caja_id' => $cajaAbierta->id,

                'tipo_doc' => $tipoDoc,
                'serie' => $serie,
                'correlativo' => $correlativo,
                'cliente_facturacion_id' => $clienteFacturacionId,
                'cliente_tipo_doc' => $clienteTipoDoc,
                'cliente_numero' => $clienteNumero,
                'cliente_nombre' => $clienteNombre,
                'cliente_direccion' => $clienteDireccion,
                'cliente_email' => $clienteEmail,
                'subtotal' => 0,
                'igv' => 0,
                'total' => 0,
                'estado' => $tipoDoc === 'RI' ? 'RECIBO_INTERNO' : 'PENDIENTE_SUNAT',
                'forma_pago' => $formaPago, // ← AGREGAR


            ];

            $invoice = $this->Invoices->patchEntity($invoice, $invoiceData);

            if ($invoice->getErrors()) {
                return $this->response
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'ok' => false,
                        'message' => 'Errores de validación en invoice',
                        'errors' => $invoice->getErrors(),
                        'invoiceData' => $invoiceData,
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }

            $conn = $this->Invoices->getConnection();

            // Pre-validate stock for productos before starting transaction
            $itemsToCheck = $data['items'] ?? [];
            foreach ($itemsToCheck as $rowCheck) {
                $tipoItemCheck = trim((string) ($rowCheck['tipo_item'] ?? ''));
                if ($tipoItemCheck === 'producto') {
                    $productoIdCheck = !empty($rowCheck['producto_id']) ? (int) $rowCheck['producto_id'] : 0;
                    $cantidadCheck = (float) ($rowCheck['cantidad'] ?? 0);
                    if ($productoIdCheck && $cantidadCheck > 0) {
                        $productoCheck = $this->fetchTable('Productos')->get($productoIdCheck);
                        if ((float) $productoCheck->stock < $cantidadCheck) {
                            $this->request->getSession()->write('old_invoice_form', $data);
                            $this->Flash->error('Stock insuficiente para el producto: ' . $productoCheck->nombre);
                            return $this->redirect(['action' => 'add']);
                        }
                    }
                }
            }
            //debug($data['items']);
            //die;

            $ok = $conn->transactional(function () use ($invoice, $data, $CajaMovimientos, $cajaAbierta, $paymentMethods, $formaPago, $cuotasInput,$tipoDoc ) {
                // AGREGAR ESTE BLOQUE ANTI-DUPLICADOS AL INICIO:
                if (!empty($invoice->serie) && !empty($invoice->correlativo)) {
                    $existe = $this->Invoices->find()->where([
                        'company_id'  => $invoice->company_id,
                        'serie'       => $invoice->serie,
                        'correlativo' => $invoice->correlativo,
                    ])->first();

                    if ($existe) {
                        // Reasignar correlativo si hubo colisión (race condition)
                        $invoice->correlativo = $this->obtenerSiguienteCorrelativo(
                            (int)$invoice->company_id,
                            (string)$invoice->serie
                        );
                    }
                }    
                if (!$this->Invoices->save($invoice)) {
                        throw new \RuntimeException('No se pudo guardar invoice');
                    }

                    $itemsData = $data['items'] ?? [];
                    if (empty($itemsData) || !is_array($itemsData)) {
                        throw new \RuntimeException('Debe enviar al menos un item.');
                    }

                    $subtotal = 0.0;
                    $igv = 0.0;
                    $total = 0.0;
                    $guardados = 0;

                    // Distribución automática a laboratorio por cada examen facturado
                    // que tenga laboratorio_id asignado: se le debe pagar su
                    // precio_convenio × cantidad.
                    $distribucionesLabPorExamen = [];

                    // Distribución automática de MATERIALES: por cada tratamiento o
                    // examen facturado con gasto_materiales configurado, se acumula
                    // gasto_materiales × cantidad en un único monto de MATERIALES
                    // para la factura (no depende de laboratorio).
                    $montoMaterialesAuto = 0.0;
                    $itemsConMateriales = [];

                foreach ($itemsData as $row) {
                    $cantidad = (float) ($row['cantidad'] ?? 0);
                    if ($cantidad <= 0) {
                        continue;
                    }

                    $tipoItem = trim((string) ($row['tipo_item'] ?? ''));
                    $tratamientoId = !empty($row['tratamiento_id']) ? (int) $row['tratamiento_id'] : null;
                    $productoId = !empty($row['producto_id']) ? (int) $row['producto_id'] : null;
                    $examenId = !empty($row['examen_id']) ? (int) $row['examen_id'] : null;

                    $descripcion = trim((string) ($row['descripcion'] ?? ''));
                    $precioUnitario = (float) ($row['precio_unitario'] ?? 0);
                    $codigoProducto = trim((string) ($row['codigo_producto'] ?? ''));
                    $unidad = trim((string) ($row['unidad'] ?? 'NIU'));

                    if (!in_array($tipoItem, ['tratamiento', 'producto', 'examen'], true)) {
                        throw new \RuntimeException('Tipo de item inválido.');
                    }

                    if ($tipoItem === 'tratamiento') {
                        if (!$tratamientoId) {
                            throw new \RuntimeException('Debe seleccionar un tratamiento.');
                        }

                        $tratamiento = $this->Invoices->InvoiceItems->Tratamientos->get($tratamientoId);

                        if ($descripcion === '') {
                            $descripcion = (string) $tratamiento->nombre;
                        }

                        if ($precioUnitario <= 0) {
                            $precioUnitario = (float) $tratamiento->costo;
                        }

                        if ($codigoProducto === '') {
                            $codigoProducto = 'TRAT';
                        }

                        if ($unidad === '') {
                            $unidad = 'NIU';
                        }

                        $productoId = null;
                        $examenId = null;
                            $nombreSunat = (string) $tratamiento->nombre; // ← AGREGAR AQUÍ

                        if ((float) $tratamiento->gasto_materiales > 0) {
                            $montoMaterialesAuto += round((float) $tratamiento->gasto_materiales * $cantidad, 2);
                            $itemsConMateriales[] = (string) $tratamiento->nombre;
                        }
                    }

                    if ($tipoItem === 'producto') {
                        if (!$productoId) {
                            throw new \RuntimeException('Debe seleccionar un producto.');
                        }

                        $producto = $this->fetchTable('Productos')->get($productoId);

                        if ($descripcion === '') {
                            $descripcion = (string) $producto->nombre;
                        }

                        if ($precioUnitario <= 0) {
                            $precioUnitario = (float) $producto->precio;
                        }

                        if ($codigoProducto === '') {
                            $codigoProducto = (string) ($producto->codigo ?: 'PROD');
                        }

                        if ($unidad === '') {
                            $unidad = (string) ($producto->unidad ?: 'NIU');
                        }

                        if ((float) $producto->stock < $cantidad) {
                            throw new \RuntimeException('Stock insuficiente para el producto: ' . $producto->nombre);
                        }

                        $producto->stock = (float) $producto->stock - $cantidad;


                        if (!$this->fetchTable('Productos')->save($producto)) {
                            throw new \RuntimeException('No se pudo actualizar stock del producto: ' . $producto->nombre);
                        }

                        $tratamientoId = null;
                        $examenId = null;
                       $nombreSunat = (string) $producto->nombre; // ← AGREGAR AQUÍ
                        }

                    if ($tipoItem === 'examen') {
                        if (!$examenId) {
                            throw new \RuntimeException('Debe seleccionar un examen.');
                        }

                        $examen = $this->fetchTable('Examenes')->get($examenId);

                        if ($descripcion === '') {
                            $descripcion = (string) $examen->nombre;
                        }

                        if ($precioUnitario <= 0) {
                            $precioUnitario = (float) $examen->precio;
                        }

                        if ($codigoProducto === '') {
                            $codigoProducto = 'EXAM';
                        }

                        if ($unidad === '') {
                            $unidad = 'NIU';
                        }

                        $tratamientoId = null;
                        $productoId = null;
                        $nombreSunat = (string) $examen->nombre;

                        if (!empty($examen->laboratorio_id) && (float) $examen->precio_convenio > 0) {
                            $laboratorioIdExamen = (int) $examen->laboratorio_id;
                            $montoConvenio = round((float) $examen->precio_convenio * $cantidad, 2);

                            if (!isset($distribucionesLabPorExamen[$laboratorioIdExamen])) {
                                $distribucionesLabPorExamen[$laboratorioIdExamen] = [
                                    'monto' => 0.0,
                                    'examenes' => [],
                                ];
                            }

                            $distribucionesLabPorExamen[$laboratorioIdExamen]['monto'] += $montoConvenio;
                            $distribucionesLabPorExamen[$laboratorioIdExamen]['examenes'][] = (string) $examen->nombre;
                        }

                        if ((float) $examen->gasto_materiales > 0) {
                            $montoMaterialesAuto += round((float) $examen->gasto_materiales * $cantidad, 2);
                            $itemsConMateriales[] = (string) $examen->nombre;
                        }
                    }

                    if ($descripcion === '' || $precioUnitario <= 0) {
                        continue;
                    }
                    if (empty($nombreSunat)) {
                        $nombreSunat = $descripcion;
                    }
                    
                    $descuento     = (float) ($row['descuento']      ?? 0);
                    $descuentoTipo = trim((string) ($row['descuento_tipo'] ?? 'porcentaje'));
                    $desdePresupuesto = !empty($row['desde_presupuesto']);

                    // Items de presupuesto: el precio unitario ya viene neto (el descuento fue
                    // pactado una sola vez en el presupuesto). El descuento se guarda como dato
                    // informativo pero no se resta de nuevo aquí, para no aplicarlo dos veces
                    // cuando el usuario edita cantidad/precio para un pago parcial.
                    $precioConDescuento = $precioUnitario;

                    if (!$desdePresupuesto && $descuento > 0) {
                        if ($descuentoTipo === 'porcentaje') {
                            $precioConDescuento = $precioUnitario * (1 - $descuento / 100);
                        } else {
                            $precioConDescuento = $precioUnitario - $descuento;
                        }
                        $precioConDescuento = max(0, $precioConDescuento);
                    }

                    $lineTotal  = round($cantidad * $precioConDescuento, 2);
                    $lineBase   = round($lineTotal / 1.18, 2);
                    $lineIgv    = round($lineTotal - $lineBase, 2);
                    $valorUnitario = (float) number_format($precioConDescuento / 1.18, 6, '.', '');

                    

                    $codigoProductoDefault = match ($tipoItem) {
                        'producto' => 'PROD',
                        'examen' => 'EXAM',
                        default => 'TRAT',
                    };

                    $itemEntity = $this->Invoices->InvoiceItems->newEntity([
                        'invoice_id' => $invoice->id,
                        'tipo_item' => $tipoItem,
                        'tratamiento_id' => $tratamientoId,
                        'producto_id' => $productoId,
                        'examen_id' => $examenId,
                        'descripcion' => $descripcion,
                        'cantidad' => $cantidad,
                        'valor_unitario' => $valorUnitario,
                        'precio_unitario' => $precioUnitario,
                        'base_igv' => $lineBase,
                        'igv' => $lineIgv,
                        'total' => $lineTotal,
                        'codigo_producto' => $codigoProducto !== '' ? $codigoProducto : $codigoProductoDefault,
                        'unidad' => $unidad !== '' ? $unidad : 'NIU',
                        'descuento'      => $descuento,
                        'descuento_tipo' => $descuentoTipo,
                        'nombre_sunat'   => $nombreSunat, // ← AGREGAR AQUÍ

                    ]);

                    if ($itemEntity->getErrors()) {
                        throw new \RuntimeException('Error de validación en item: ' . json_encode($itemEntity->getErrors(), JSON_UNESCAPED_UNICODE));
                    }

                    if (!$this->Invoices->InvoiceItems->save($itemEntity)) {
                        throw new \RuntimeException('No se pudo guardar invoice_item');
                    }

                    $subtotal += $lineBase;
                    $igv += $lineIgv;
                    $total += $lineTotal;
                    $guardados++;
                }

                if ($guardados === 0) {
                    throw new \RuntimeException('No se guardó ningún item válido');
                }

                $invoice->subtotal = round($subtotal, 2);
                $invoice->igv = round($igv, 2);
                $invoice->total = round($total, 2);

                if (!$this->Invoices->save($invoice)) {
                    throw new \RuntimeException('No se pudo actualizar totales de invoice');
                }

                if ($formaPago === 'CONTADO') {
    // ============================================================
    // FLUJO EXISTENTE — SIN CAMBIOS (pago contado, caja_movimientos)
    // ============================================================
    $validPaymentMethods = [];
    foreach ($paymentMethods as $pm) {
        $metodo = strtoupper(trim((string) ($pm['metodo'] ?? '')));
        $monto = (float) ($pm['monto'] ?? 0);

        if (!empty($metodo) && $monto > 0) {
            if (!in_array($metodo, ['EFECTIVO', 'YAPE', 'TARJETA', 'TRANSFERENCIA', 'PLIN', 'OTROS'], true)) {
                throw new \RuntimeException('Método de pago inválido: ' . $metodo);
            }
            $validPaymentMethods[] = [
                'metodo' => $metodo,
                'monto' => round($monto, 2)
            ];
        }
    }

    if (empty($validPaymentMethods)) {
        throw new \RuntimeException('Debes agregar al menos un método de pago con monto válido.');
    }

    $invoiceTotal = round((float) $invoice->total, 2);
    $totalRecibido = 0.0;
    foreach ($validPaymentMethods as $pm) {
        $totalRecibido += (float) $pm['monto'];
    }
    $totalRecibido = round($totalRecibido, 2);

    if ($totalRecibido !== $invoiceTotal) {
        throw new \RuntimeException(sprintf(
            'El monto recibido debe ser igual al total a pagar. Total: S/ %.2f | Recibido: S/ %.2f',
            $invoiceTotal,
            $totalRecibido
        ));
    }

    $metodosPermitidos = ['EFECTIVO', 'YAPE', 'TARJETA', 'TRANSFERENCIA', 'PLIN', 'OTROS'];

    foreach ($validPaymentMethods as $pm) {
        if (!in_array($pm['metodo'], $metodosPermitidos, true) || $pm['monto'] <= 0) {
            throw new \RuntimeException('Método de pago inválido o monto no válido al registrar el movimiento de caja: ' . json_encode($pm));
        }

        $movimiento = $CajaMovimientos->newEntity([
            'caja_id' => $cajaAbierta->id,
            'invoice_id' => $invoice->id,
            'metodo_pago' => $pm['metodo'],
            'monto_total' => $invoiceTotal,
            'monto_recibido' => $pm['monto'],
            'vuelto' => 0.0,
        ]);

        if ($movimiento->getErrors()) {
            throw new \RuntimeException('Error de validación en caja_movimiento: ' . json_encode($movimiento->getErrors()));
        }

        if (!$CajaMovimientos->save($movimiento)) {
            throw new \RuntimeException('No se pudo guardar el movimiento de caja: ' . json_encode($movimiento->getErrors()));
        }
    }
} else {
    $invoiceTotal = round((float) $invoice->total, 2);

    if (empty($cuotasInput) || !is_array($cuotasInput)) {
        throw new \RuntimeException('Debes registrar al menos una cuota para el pago al crédito.');
    }

    $InvoiceCuotas = $this->fetchTable('InvoiceCuotas');
    $hoy = (new \DateTime('now', new \DateTimeZone('America/Lima')))->format('Y-m-d');
    $sumaCuotas = 0.0;
    $numero = 0;

    foreach ($cuotasInput as $c) {
        $numero++;
        $monto = round((float) ($c['monto'] ?? 0), 2);
        $fecha = trim((string) ($c['fecha_vencimiento'] ?? ''));
        $metodoPago = strtoupper(trim((string) ($c['metodo_pago'] ?? '')));

        if ($monto <= 0) {
            throw new \RuntimeException("Monto inválido en la cuota {$numero}.");
        }
        if ($fecha === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            throw new \RuntimeException("Fecha de vencimiento inválida en la cuota {$numero}.");
        }

        $venceHoy = ($fecha === $hoy);

        if ($fecha < $hoy) {
            throw new \RuntimeException("La cuota {$numero} tiene una fecha de vencimiento en el pasado ({$fecha}). Debe ser hoy o una fecha futura.");
        }

        // SUNAT exige que en una FACTURA a crédito todas las cuotas declaradas
        // tengan fecha POSTERIOR a la emisión (código 3267 si no se cumple).
        // Para evitar depender del filtro "silencioso" al emitir, no permitimos
        // cuotas con vencimiento HOY cuando el comprobante es Factura (01).
        if ($tipoDoc === '01' && $venceHoy) {
            throw new \RuntimeException(
                "La cuota {$numero} vence hoy ({$hoy}). En una Factura a crédito, SUNAT exige que todas las cuotas tengan fecha posterior a la emisión. " .
                "Usa una fecha desde mañana en adelante, o registra el adelanto como pago Contado."
            );
        }

        if ($venceHoy) {
            if (!in_array($metodoPago, ['EFECTIVO', 'YAPE', 'TARJETA', 'TRANSFERENCIA', 'PLIN', 'OTROS'], true)) {
                throw new \RuntimeException("La cuota {$numero} vence hoy, debes indicar un método de pago válido.");
            }
        }

        $sumaCuotas += $monto;

        // Forzar parseo estricto Y-m-d, evitando que el marshaller del ORM
        // reinterprete el string con el locale de la app (bug conocido:
        // invierte día/mes cuando el día es <= 12, ej. 2026-08-07 -> 2026-07-08)
       try {
            $fechaVencimientoObj = new \Cake\I18n\Date($fecha); // $fecha ya viene validado como 'Y-m-d'
        } catch (\Throwable $e) {
            throw new \RuntimeException("Fecha de vencimiento inválida en la cuota {$numero}: {$fecha}");
        }

        $cuotaEntity = $InvoiceCuotas->newEntity([
            'invoice_id' => $invoice->id,
            'numero_cuota' => $numero,
            'monto' => $monto,
            'fecha_vencimiento' => $fechaVencimientoObj,
            'estado' => $venceHoy ? 'PAGADA' : 'PENDIENTE',
            'metodo_pago' => $venceHoy ? $metodoPago : null,
            'caja_id' => $venceHoy ? $cajaAbierta->id : null,
            'pagado_en' => $venceHoy ? new \DateTime() : null,
        ]);

        if ($cuotaEntity->getErrors()) {
            throw new \RuntimeException('Error de validación en cuota: ' . json_encode($cuotaEntity->getErrors(), JSON_UNESCAPED_UNICODE));
        }

        if (!$InvoiceCuotas->save($cuotaEntity)) {
            throw new \RuntimeException('No se pudo guardar la cuota ' . $numero);
        }

        // Cuota que vence hoy: se cobra de inmediato, igual que el flujo CONTADO
        if ($venceHoy) {
            $movimiento = $CajaMovimientos->newEntity([
                'caja_id' => $cajaAbierta->id,
                'invoice_id' => $invoice->id,
                'metodo_pago' => $metodoPago,
                'monto_total' => $invoiceTotal,
                'monto_recibido' => $monto,
                'vuelto' => 0.0,
            ]);

            if ($movimiento->getErrors()) {
                throw new \RuntimeException('Error de validación en caja_movimiento (cuota): ' . json_encode($movimiento->getErrors()));
            }

            if (!$CajaMovimientos->save($movimiento)) {
                throw new \RuntimeException('No se pudo guardar el movimiento de caja de la cuota ' . $numero);
            }
        }
    }

    $sumaCuotas = round($sumaCuotas, 2);
    if ($sumaCuotas !== $invoiceTotal) {
        throw new \RuntimeException(sprintf(
            'La suma de cuotas debe ser igual al total del comprobante. Total: S/ %.2f | Cuotas: S/ %.2f',
            $invoiceTotal,
            $sumaCuotas
        ));
    }
}

                // Si la factura se generó desde un presupuesto, registrar el vínculo
                $presupuestoIdPost = (int) ($data['presupuesto_id'] ?? 0);
                if ($presupuestoIdPost > 0) {
                    $PresupuestosInvoices = $this->fetchTable('PresupuestosInvoices');
                    $puentePresupuesto = $PresupuestosInvoices->newEntity([
                        'presupuesto_id' => $presupuestoIdPost,
                        'invoice_id' => $invoice->id,
                        'monto_facturado' => (float) $invoice->total,
                    ]);

                    if (!$PresupuestosInvoices->save($puentePresupuesto)) {
                        throw new \RuntimeException('No se pudo vincular la factura con el presupuesto: ' . json_encode($puentePresupuesto->getErrors()));
                    }
                }

                $InvoiceDistribuciones = $this->fetchTable('InvoiceDistribuciones');

                // Generar automáticamente la distribución a laboratorio por cada
                // examen facturado que tenga laboratorio_id asignado: es lo que se
                // le debe pagar al laboratorio (precio_convenio × cantidad),
                // independiente de las distribuciones manuales que cargue el
                // usuario (ej. por materiales de tratamientos).
                foreach ($distribucionesLabPorExamen as $laboratorioIdExamen => $distLab) {
                    $montoLab = round((float) $distLab['monto'], 2);
                    if ($montoLab <= 0) {
                        continue;
                    }

                    $distribucionAutoEntity = $InvoiceDistribuciones->newEntity([
                        'invoice_id' => $invoice->id,
                        'tipo' => 'LABORATORIO',
                        'laboratorio_id' => $laboratorioIdExamen,
                        'monto' => $montoLab,
                        'descripcion' => 'Convenio por examen(es): ' . implode(', ', $distLab['examenes']),
                    ]);

                    if ($distribucionAutoEntity->getErrors()) {
                        throw new \RuntimeException('Error de validación en distribución automática de examen: ' . json_encode($distribucionAutoEntity->getErrors()));
                    }

                    if (!$InvoiceDistribuciones->save($distribucionAutoEntity)) {
                        throw new \RuntimeException('No se pudo guardar la distribución automática del examen hacia el laboratorio.');
                    }
                }

                // Generar automáticamente la distribución de MATERIALES por cada
                // tratamiento/examen facturado que tenga gasto_materiales configurado.
                if ($montoMaterialesAuto > 0) {
                    $distribucionMaterialesEntity = $InvoiceDistribuciones->newEntity([
                        'invoice_id' => $invoice->id,
                        'tipo' => 'MATERIALES',
                        'laboratorio_id' => null,
                        'monto' => round($montoMaterialesAuto, 2),
                        'descripcion' => 'Materiales por: ' . implode(', ', $itemsConMateriales),
                    ]);

                    if ($distribucionMaterialesEntity->getErrors()) {
                        throw new \RuntimeException('Error de validación en distribución automática de materiales: ' . json_encode($distribucionMaterialesEntity->getErrors()));
                    }

                    if (!$InvoiceDistribuciones->save($distribucionMaterialesEntity)) {
                        throw new \RuntimeException('No se pudo guardar la distribución automática de materiales.');
                    }
                }

                return true;
            });

            if ($ok) {
            $invoiceFinal = $this->Invoices->get($invoice->id, [
                'contain' => ['InvoiceItems', 'Users']
            ]);

            if ($isJson) {
                $this->autoRender = false;
                return $this->response
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'ok' => true,
                        'message' => 'Comprobante guardado correctamente',
                        'invoice' => $invoiceFinal,
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }

            // ✅ FLUJO AUTOMÁTICO SEGÚN TIPO
            if ($tipoDoc === 'RI') {
                // Recibo Interno: no va a SUNAT, ir directo a la vista
                $this->Flash->success('Recibo Interno creado correctamente.');
                return $this->redirect(['action' => 'view', $invoice->id]);
            }

            if ($tipoDoc === '03') {
                // Boleta: enviar automáticamente al Resumen Diario
                $this->Flash->success('Boleta creada. Enviando al Resumen Diario...');
                return $this->redirect([
                    'action' => 'emitir',
                    $invoice->id,
                ]);
            }

            if ($tipoDoc === '01') {
                // Factura: enviar automáticamente a SUNAT
                $this->Flash->success('Factura creada. Enviando a SUNAT...');
                return $this->redirect([
                    'action' => 'emitir',
                    $invoice->id,
                ]);
            }

            // Fallback
            return $this->redirect(['action' => 'view', $invoice->id]);
        }

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'ok' => false,
                    'message' => 'No se pudo guardar el comprobante',
                    'data' => $data,
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } catch (\Throwable $e) {
            if ($isJson) {
                return $this->response
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'ok' => false,
                        'message' => 'Excepción al guardar invoice',
                        'error' => $e->getMessage(),
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }

            // Log detailed debug info for failed invoice saves (temporary)
            try {
                $logPath = ROOT . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'invoice_save_errors.log';
                $logData = [
                    'timestamp' => date('c'),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'post' => (is_array($data) ? $data : []),
                    'payment_methods_json' => $paymentMethodsJson ?? null,
                ];
                file_put_contents($logPath, json_encode($logData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n---\n", FILE_APPEND);
            } catch (\Throwable $ignore) {
                // ignore logging errors
            }

            $this->request->getSession()->write('old_invoice_form', $data);
            $this->Flash->error('⚠️ ' . $e->getMessage());

            return $this->redirect(['action' => 'add']);
        }
    }
    public function pagarCuota($cuotaId)
{
    $this->request->allowMethod(['post']);
    $this->autoRender = false;

    $InvoiceCuotas = $this->fetchTable('InvoiceCuotas');
    $CajaMovimientos = $this->fetchTable('CajaMovimientos');
    $Cajas = $this->fetchTable('Cajas');

    $cuota = $InvoiceCuotas->get($cuotaId, ['contain' => ['Invoices']]);

    if ($cuota->estado !== 'PENDIENTE') {
        $this->Flash->error('Esta cuota ya fue pagada o anulada.');
        return $this->redirect(['action' => 'view', $cuota->invoice_id]);
    }
    // NUEVO: exigir orden de pago — no se puede pagar esta cuota si hay una
    // cuota anterior (menor numero_cuota) que sigue PENDIENTE.
    $cuotaAnteriorPendiente = $InvoiceCuotas->find()
        ->where([
            'invoice_id' => $cuota->invoice_id,
            'numero_cuota <' => $cuota->numero_cuota,
            'estado' => 'PENDIENTE',
        ])
        ->order(['numero_cuota' => 'ASC'])
        ->first();

    if ($cuotaAnteriorPendiente) {
        $this->Flash->error(
            'Primero debes pagar la cuota N° ' . $cuotaAnteriorPendiente->numero_cuota . '.'
        );
        return $this->redirect(['action' => 'view', $cuota->invoice_id]);
    }

    $data = $this->request->getData();
    $metodoPago = strtoupper(trim((string) ($data['metodo_pago'] ?? '')));

    if (!in_array($metodoPago, ['EFECTIVO', 'YAPE', 'TARJETA', 'TRANSFERENCIA', 'PLIN', 'OTROS'], true)) {
        $this->Flash->error('Método de pago inválido.');
        return $this->redirect(['action' => 'view', $cuota->invoice_id]);
    }

    $cajaAbierta = $Cajas->find()->where(['estado' => 'ABIERTA'])->order(['created' => 'DESC'])->first();
    if (!$cajaAbierta) {
        $this->Flash->error('Debe haber una caja abierta para registrar el pago.');
        return $this->redirect(['action' => 'view', $cuota->invoice_id]);
    }

    $conn = $InvoiceCuotas->getConnection();

    try {
        $conn->transactional(function () use ($InvoiceCuotas, $CajaMovimientos, $cuota, $cajaAbierta, $metodoPago) {
            $movimiento = $CajaMovimientos->newEntity([
                'caja_id' => $cajaAbierta->id,
                'invoice_id' => $cuota->invoice_id,
                'metodo_pago' => $metodoPago,
                'monto_total' => (float) $cuota->invoice->total,
                'monto_recibido' => (float) $cuota->monto,
                'vuelto' => 0.0,
            ]);

            if (!$CajaMovimientos->save($movimiento)) {
                throw new \RuntimeException('No se pudo registrar el movimiento de caja.');
            }

            $cuota->estado = 'PAGADA';
            $cuota->metodo_pago = $metodoPago;
            $cuota->caja_id = $cajaAbierta->id;
            $cuota->pagado_en = new \DateTime();

            if (!$InvoiceCuotas->save($cuota)) {
                throw new \RuntimeException('No se pudo actualizar la cuota.');
            }
        });

        $this->Flash->success('Cuota #' . $cuota->numero_cuota . ' pagada correctamente.');
        return $this->redirect(['action' => 'view', $cuota->invoice_id]);

    } catch (\Throwable $e) {
        $this->Flash->error('Error al registrar el pago: ' . $e->getMessage());
        return $this->redirect(['action' => 'view', $cuota->invoice_id]);
    }
}
    public function emitir($id)
{
    $this->autoRender = false;
     // Fecha de emisión real que va al XML (hoy, hora Lima)
    $fechaEmisionStr = (new \DateTime('now', new \DateTimeZone('America/Lima')))->format('Y-m-d');

    $invoiceEntity = $this->Invoices->get($id, [
        'contain' => [
            'Companies',
            'Pacientes',
            'HistoriasClinicas',
            'InvoiceItems',
        ],
    ]);

    // ❌ Recibos Internos NUNCA van a SUNAT
    if ($invoiceEntity->tipo_doc === 'RI' || $invoiceEntity->estado === 'RECIBO_INTERNO') {
        $this->Flash->error('Los Recibos Internos no se envían a SUNAT.');
        return $this->redirect(['action' => 'view', $id]);
    }

    // Solo procesar facturas y boletas en estado PENDIENTE_SUNAT
    if ($invoiceEntity->estado !== 'PENDIENTE_SUNAT') {
        $this->Flash->error('Solo se pueden emitir comprobantes en estado PENDIENTE_SUNAT.');
        return $this->redirect(['action' => 'view', $id]);
    }

    if (empty($invoiceEntity->invoice_items)) {
        $this->Flash->error('La invoice no tiene items.');
        return $this->redirect(['action' => 'view', $id]);
    }

    $company = $invoiceEntity->company;

    $xmlDir = WWW_ROOT . 'xml' . DS;
    $cdrDir = WWW_ROOT . 'cdr' . DS;

    if (!is_dir($xmlDir)) {
        mkdir($xmlDir, 0777, true);
    }

    if (!is_dir($cdrDir)) {
        mkdir($cdrDir, 0777, true);
    }

    try {
        // Recién aquí se genera numeración oficial SUNAT
        if (empty($invoiceEntity->serie) || empty($invoiceEntity->correlativo)) {
            $this->Flash->error('Este comprobante no tiene serie/correlativo asignado. Fue creado con una versión anterior del sistema.');
            return $this->redirect(['action' => 'view', $id]);
        }

        $sunat = new SunatService($company);
        $see = $sunat->getSee();

        $empresa = (new GreCompany())
            ->setRuc((string)$company->ruc)
            ->setRazonSocial((string)$company->razon_social)
            ->setNombreComercial((string)($company->nombre_comercial ?: $company->razon_social))
            ->setAddress(
                (new Address())
                    ->setUbigueo((string)$company->ubigeo)
                    ->setDepartamento((string)$company->departamento)
                    ->setProvincia((string)$company->provincia)
                    ->setDistrito((string)$company->distrito)
                    ->setUrbanizacion((string)($company->urbanizacion ?: '-'))
                    ->setDireccion((string)$company->direccion)
                    ->setCodLocal((string)($company->cod_local ?: '0000'))
            );

        if ($invoiceEntity->tipo_doc === '01') {
            if ((string)$invoiceEntity->cliente_tipo_doc !== '6') {
                throw new \RuntimeException('La factura requiere cliente con RUC.');
            }

            if (!preg_match('/^\d{11}$/', (string)$invoiceEntity->cliente_numero)) {
                throw new \RuntimeException('La factura requiere RUC válido de 11 dígitos.');
            }

            if (trim((string)$invoiceEntity->cliente_nombre) === '') {
                throw new \RuntimeException('La factura requiere razón social.');
            }
        }

        $cliente = (new Client())
            ->setTipoDoc((string)$invoiceEntity->cliente_tipo_doc)
            ->setNumDoc((string)$invoiceEntity->cliente_numero)
            ->setRznSocial((string)$invoiceEntity->cliente_nombre);

        $details = [];
        $sumGravada = 0.0;
        $sumIgv = 0.0;
        $sumTotal = 0.0;

        foreach ($invoiceEntity->invoice_items as $it) {
            $qty = (float)$it->cantidad;
            if ($qty <= 0) {
                continue;
            }

            // Usar precio con descuento aplicado (total / cantidad) para el PriceAmount
            $precioUnitConIgv = $qty > 0 ? round((float)$it->total / $qty, 2) : (float)$it->precio_unitario;
            $lineTotalConIgv = (float)$it->total;
            $lineBase = (float)$it->base_igv;
            $lineIgv = (float)$it->igv;

            $sumGravada += $lineBase;
            $sumIgv += $lineIgv;
            $sumTotal += $lineTotalConIgv;

            $details[] = (new SaleDetail())
                ->setCodProducto((string)($it->codigo_producto ?: 'TRAT'))
                ->setUnidad((string)($it->unidad ?: 'NIU'))
                ->setCantidad($qty)
                ->setMtoValorUnitario((float)$it->valor_unitario)
                ->setDescripcion((string)($it->nombre_sunat ?: $it->descripcion))
                ->setMtoBaseIgv($lineBase)
                ->setMtoValorVenta($lineBase)
                ->setPorcentajeIgv(18.00)
                ->setIgv($lineIgv)
                ->setTipAfeIgv('10')
                ->setTotalImpuestos($lineIgv)
                ->setMtoPrecioUnitario($precioUnitConIgv);
        }

        if (empty($details)) {
            throw new \RuntimeException('No se encontraron items válidos.');
        }

        $valorVenta = (float)number_format(round($sumGravada, 2), 2, '.', '');
        $igvTotal = (float)number_format(round($sumIgv, 2), 2, '.', '');
        $total = (float)number_format(round($sumTotal, 2), 2, '.', '');

        $invoiceEntity->subtotal = $valorVenta;
        $invoiceEntity->igv = $igvTotal;
        $invoiceEntity->total = $total;

        if ($invoiceEntity->tipo_doc === '03') {
            $invoiceEntity->estado = 'PENDIENTE_RESUMEN';
            $invoiceEntity->codigo_sunat = null;
            $invoiceEntity->descripcion_sunat = 'Boleta pendiente de envío en resumen diario.';
            $invoiceEntity->enviado_sunat_at = null;

            $this->Invoices->saveOrFail($invoiceEntity);

            $this->Flash->success('Boleta marcada para Resumen Diario.');
            return $this->redirect(['action' => 'view', $id]);
        }
        // ============================================================
        // NUEVO — resolver forma de pago (contado / crédito)
        // ============================================================
        $cuotasGreenter = [];

        if ($invoiceEntity->forma_pago === 'CREDITO') {
            $cuotasEntities = $this->fetchTable('InvoiceCuotas')->find()
                ->where(['invoice_id' => $invoiceEntity->id])
                ->order(['numero_cuota' => 'ASC'])
                ->all()
                ->toList();

            if (empty($cuotasEntities)) {
                throw new \RuntimeException('El comprobante es a crédito pero no tiene cuotas registradas.');
            }

           

            $cuotasGreenter = [];
            $montoPendiente = 0.0;

            foreach ($cuotasEntities as $c) {
                $fechaCuota = $c->fecha_vencimiento instanceof \DateTimeInterface
                    ? $c->fecha_vencimiento->format('Y-m-d')
                    : (string) $c->fecha_vencimiento;

                // SUNAT exige que la fecha de pago de la cuota sea POSTERIOR a la emisión.
                // Si la cuota vence hoy o antes, se considera cobrada/adelanto y NO se declara en el XML.
                if ($fechaCuota <= $fechaEmisionStr) {
                    continue;
                }

                $montoCuota = (float)$c->monto;
                $montoPendiente += $montoCuota;

                $cuotasGreenter[] = (new Cuota())
                    ->setMonto($montoCuota)
                    ->setFechaPago(new \DateTime($fechaCuota . ' 00:00:00-05:00'));
            }

            $montoPendiente = round($montoPendiente, 2);

            // Si todas las cuotas vencían hoy (se pagó todo como inicial/contado)
            if (empty($cuotasGreenter) || $montoPendiente <= 0) {
                $formaPagoGreenter = new FormaPagoContado();
            } else {
                // El monto neteado al crédito es el saldo total pendiente de las cuotas futuras
                $formaPagoGreenter = new FormaPagoCredito($montoPendiente);
            }
        } else {
            $formaPagoGreenter = new FormaPagoContado();
        }

        $invoice = (new GreInvoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion('0101')
            ->setTipoDoc((string)$invoiceEntity->tipo_doc)
            ->setSerie((string)$invoiceEntity->serie)
            ->setCorrelativo((string)$invoiceEntity->correlativo)
            ->setFechaEmision(new \DateTime('now', new \DateTimeZone('America/Lima')))
            ->setTipoMoneda('PEN')
            ->setCompany($empresa)
            ->setClient($cliente)
            ->setMtoOperGravadas($valorVenta)
            ->setMtoIGV($igvTotal)
            ->setTotalImpuestos($igvTotal)
            ->setValorVenta($valorVenta)
            ->setSubTotal($total)
            ->setMtoImpVenta($total)
            ->setFormaPago($formaPagoGreenter)
            ->setDetails($details);

            if (!empty($cuotasGreenter)) {
                $invoice->setCuotas($cuotasGreenter);
            }
            file_put_contents(
                ROOT . DS . 'tmp' . DS . 'debug_cuotas.txt',
                'fechaEmisionStr=' . $fechaEmisionStr . "\n" .
                'cuotasGreenter count=' . count($cuotasGreenter) . "\n" .
                print_r(array_map(fn($c) => [$c->getMonto(), $c->getFechaPago()->format('Y-m-d')], $cuotasGreenter), true)
            );
        $result = $see->send($invoice);

        $xmlPath = 'xml/' . $invoice->getName() . '.xml';
        $xmlContent = $see->getFactory()->getLastXml();
        file_put_contents(WWW_ROOT . $xmlPath, $xmlContent);

        $invoiceEntity->xml_path = $xmlPath;

        $digestValue = null;
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlContent);

        if ($xml !== false) {
            $namespaces = $xml->getNamespaces(true);
            foreach ($namespaces as $prefix => $ns) {
                $xml->registerXPathNamespace($prefix, $ns);
            }
            $nodes = $xml->xpath('//ds:DigestValue');
            if (!empty($nodes)) {
                $digestValue = (string)$nodes[0];
            }
        }

        $invoiceEntity->hash_xml = $digestValue;
        $invoiceEntity->enviado_sunat_at = new \DateTime();

        if ($result->isSuccess()) {
            $cdrPath = 'cdr/R-' . $invoice->getName() . '.zip';
            file_put_contents(WWW_ROOT . $cdrPath, $result->getCdrZip());

            $cdr = $result->getCdrResponse();

            $invoiceEntity->estado = 'ACEPTADO';
            $invoiceEntity->codigo_sunat = (string)$cdr->getCode();
            $invoiceEntity->descripcion_sunat = (string)$cdr->getDescription();
            $invoiceEntity->cdr_path = $cdrPath;

            $this->Invoices->saveOrFail($invoiceEntity);

            $this->Flash->success('SUNAT aceptó el comprobante: ' . $cdr->getDescription());
            return $this->redirect(['action' => 'view', $id]);
        }

        $err = $result->getError();
        $invoiceEntity->estado = 'RECHAZADO';
        $invoiceEntity->codigo_sunat = (string)$err->getCode();
        $invoiceEntity->descripcion_sunat = (string)$err->getMessage();

        $this->Invoices->saveOrFail($invoiceEntity);

        $this->Flash->error('SUNAT rechazó: ' . $err->getCode() . ' - ' . $err->getMessage());
        return $this->redirect(['action' => 'view', $id]);

    } catch (\Throwable $e) {
        $invoiceEntity->estado = 'RECHAZADO';
        $invoiceEntity->codigo_sunat = 'EXCEPTION';
        $invoiceEntity->descripcion_sunat = $e->getMessage();
        $this->Invoices->save($invoiceEntity);

        

        $this->Flash->error('Error interno al emitir: ' . $e->getMessage());
        return $this->redirect(['action' => 'view', $id]);
    }
}
public function comprobanteCuota($cuotaId)
{
    $this->autoRender = false;

    $InvoiceCuotas = $this->fetchTable('InvoiceCuotas');

    $cuota = $InvoiceCuotas->get($cuotaId, [
        'contain' => [
            'Invoices' => [
                'Companies',
                'Pacientes',
                'HistoriasClinicas',
                'InvoiceItems' => ['Tratamientos'],
            ],
        ],
    ]);

    $invoice = $cuota->invoice;

    if (!$invoice) {
        $this->Flash->error('No se encontró el comprobante asociado a esta cuota.');
        return $this->redirect(['action' => 'index']);
    }

    // Todas las cuotas del comprobante, para mostrar el saldo pendiente
    $todasCuotas = $InvoiceCuotas->find()
        ->where(['invoice_id' => $invoice->id])
        ->order(['numero_cuota' => 'ASC'])
        ->all()
        ->toList();

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');

    $dompdf = new Dompdf($options);

    $this->viewBuilder()
        ->disableAutoLayout()
        ->setTemplatePath('Invoices')
        ->setTemplate('comprobante_cuota');

    $logoPath = WWW_ROOT . 'img' . DS . 'logoJyza.webp';
    if (file_exists($logoPath)) {
        $logoUrl = 'data:image/webp;base64,' . base64_encode(file_get_contents($logoPath));
    } else {
        $logoUrl = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
    }

    $this->set(compact('invoice', 'cuota', 'todasCuotas', 'logoUrl'));

    $html = $this->render('comprobante_cuota')->getBody();

    $dompdf->loadHtml((string) $html, 'UTF-8');
    $dompdf->setPaper([0, 0, 161.5, 500.5], 'portrait');
    $dompdf->render();

    $pdfDir = WWW_ROOT . 'pdf' . DS . 'cuotas' . DS;
    if (!is_dir($pdfDir)) {
        mkdir($pdfDir, 0777, true);
    }

    $fileName = 'CUOTA-' . $invoice->serie . '-' . $invoice->correlativo . '-N' . $cuota->numero_cuota . '.pdf';
    $filePath = $pdfDir . $fileName;

    $pdfOutput = $dompdf->output();
    file_put_contents($filePath, $pdfOutput);

    return $this->response
        ->withType('application/pdf')
        ->withStringBody($pdfOutput);
}

     public function enviarResumenDiario()
{
    $this->request->allowMethod(['post', 'get']);

    $companyId = (int)$this->request->getQuery('company_id', 0);
    $fechaStr  = (string)$this->request->getQuery('fecha', date('Y-m-d'));

    if ($companyId <= 0) {
        $this->Flash->error('Falta company_id');
        return $this->redirect($this->referer());
    }

    $fecha          = new \DateTimeImmutable($fechaStr, new \DateTimeZone('America/Lima'));
    $DailySummaries = $this->fetchTable('DailySummaries');
    $Companies      = $this->fetchTable('Companies');
    $company        = $Companies->get($companyId);

 
    $start = $fecha->setTime(0, 0, 0);
    $end   = $fecha->setTime(23, 59, 59);

    // 🔒 Bloquear si hay un resumen de fecha ANTERIOR aún pendiente
    $pendienteAnterior = $this->Invoices->find()
        ->where([
            'company_id' => $companyId,
            'tipo_doc'   => '03',
            'estado'     => 'PENDIENTE_RESUMEN',
            'created <'  => $start->format('Y-m-d H:i:s'),
        ])
        ->where(function ($exp, $q) {
            return $exp->or([
                $exp->isNull('daily_summary_id'),
                'daily_summary_id IN' => $q->newExpr(
                    "(SELECT id FROM daily_summaries WHERE estado NOT IN ('ACEPTADO'))"
                ),
            ]);
        })
        ->select(['created'])
        ->order(['created' => 'ASC'])
        ->first();

    if ($pendienteAnterior) {
        $this->Flash->error(
            'Debes enviar primero el resumen del ' .
            $pendienteAnterior->created->format('d/m/Y') .
            ' antes de enviar este.'
        );
        return $this->redirect($this->referer());
    }

  

    // Solo boletas PENDIENTE_RESUMEN sin resumen ACEPTADO asociado
    // Permite reintentar si falló, o enviar nuevas boletas creadas después
    $boletas = $this->Invoices->find()
        ->where([
            'company_id' => $companyId,
            'tipo_doc'   => '03',
            'estado'     => 'PENDIENTE_RESUMEN',
            'created >=' => $start->format('Y-m-d H:i:s'),
            'created <=' => $end->format('Y-m-d H:i:s'),
        ])
        ->where(function ($exp, $q) {
            return $exp->or([
                $exp->isNull('daily_summary_id'),
                'daily_summary_id IN' => $q->newExpr(
                    "(SELECT id FROM daily_summaries WHERE estado NOT IN ('ACEPTADO'))"
                ),
            ]);
        })
        ->order(['correlativo' => 'ASC'])
        ->limit(30)
        ->all()
        ->toList();

    if (empty($boletas)) {
        $this->Flash->warning('No hay boletas pendientes de resumen para esa fecha.');
        return $this->redirect($this->referer());
    }

    // Correlativo contando TODOS los resúmenes del día (exitosos y fallidos)
    // para evitar duplicados cuando se reintenta un envío
    $last = $DailySummaries->find()
        ->where([
            'company_id' => $companyId,
            'fecha'      => $fecha->format('Y-m-d'),
        ])
        ->order(['correlativo' => 'DESC'])
        ->first();

    $corr   = $last ? ((int)$last->correlativo + 1) : 1;
    $nombre = $company->ruc . '-RC-' . $fecha->format('Ymd') . '-' . $corr;

    try {
        $sunat = new SunatService($company);
        $see   = $sunat->getSee();

        $empresa = (new GreCompany())
            ->setRuc((string)$company->ruc)
            ->setRazonSocial((string)$company->razon_social)
            ->setNombreComercial((string)($company->nombre_comercial ?: $company->razon_social))
            ->setAddress(
                (new Address())
                    ->setUbigueo((string)$company->ubigeo)
                    ->setDepartamento((string)$company->departamento)
                    ->setProvincia((string)$company->provincia)
                    ->setDistrito((string)$company->distrito)
                    ->setUrbanizacion((string)($company->urbanizacion ?: '-'))
                    ->setDireccion((string)$company->direccion)
                    ->setCodLocal((string)($company->cod_local ?: '0000'))
            );

        $details = [];
        foreach ($boletas as $b) {
            $details[] = (new SummaryDetail())
                ->setTipoDoc('03')
                ->setSerieNro($b->serie . '-' . str_pad((string)$b->correlativo, 8, '0', STR_PAD_LEFT))
                ->setEstado('1')
                ->setClienteTipo((string)($b->cliente_tipo_doc ?: '1'))
                ->setClienteNro((string)($b->cliente_numero ?: '00000000'))
                ->setTotal((float)$b->total)
                ->setMtoOperGravadas((float)$b->subtotal)
                ->setMtoIGV((float)$b->igv)
                ->setPorcentajeIgv(18.00);
        }

        $summary = (new Summary())
            ->setFecGeneracion(new \DateTime($fecha->format('Y-m-d')))
            ->setFecResumen(new \DateTime('now', new \DateTimeZone('America/Lima')))
            ->setCorrelativo((string)$corr)
            ->setCompany($empresa)
            ->setDetails($details);

        // ✅ Primero enviamos a SUNAT — si falla aquí, no tocamos la BD
        $res = $see->send($summary);

        // Guardar XML siempre
        $xmlDir = WWW_ROOT . 'xml' . DS;
        if (!is_dir($xmlDir)) {
            mkdir($xmlDir, 0777, true);
        }
        $xmlPath = 'xml/' . $summary->getName() . '.xml';
        file_put_contents(WWW_ROOT . $xmlPath, $see->getFactory()->getLastXml());

        if (!$res->isSuccess()) {
            $err     = $res->getError();
            $code    = (string)$err->getCode();
            $message = (string)$err->getMessage();

            // Código 0135: SUNAT ocupada, no guardar nada, boletas siguen libres
            if ($code === '0135') {
                $this->Flash->warning(
                    'SUNAT no pudo encolar el resumen ahora. Reintenta en unos minutos. Código: ' . $code
                );
                return $this->redirect($this->referer());
            }

            // Otro error: guardar registro de auditoría pero SIN tocar las boletas
            $summaryFallido = $DailySummaries->newEntity([
                'company_id'        => $companyId,
                'fecha'             => $fecha->format('Y-m-d'),
                'correlativo'       => $corr,
                'nombre'            => $nombre,
                'estado'            => 'RECHAZADO',
                'codigo_sunat'      => $code,
                'descripcion_sunat' => $message,
                'xml_path'          => $xmlPath,
            ]);
            $DailySummaries->save($summaryFallido);

            $this->Flash->error('SUNAT rechazó resumen: ' . $code . ' - ' . $message);
            return $this->redirect($this->referer());
        }

        // ✅ SUNAT encoló el resumen — tenemos ticket
        $ticket = $res->getTicket();

        // ✅ Consultar CDR automáticamente sin paso manual
        $cdrZip         = null;
        $cdrCode        = null;
        $cdrDescription = null;
        $estadoFinal    = 'ENVIADO'; // fallback si aún no está listo

        try {
            sleep(3); // esperar que SUNAT procese
            $statusRes = $see->getStatus($ticket);

            if ($statusRes->isSuccess()) {
                $cdr            = $statusRes->getCdrResponse();
                $cdrZip         = $statusRes->getCdrZip();
                $cdrCode        = (string)$cdr->getCode();
                $cdrDescription = (string)$cdr->getDescription();
                $estadoFinal    = 'ACEPTADO';
            }
        } catch (\Throwable $ignore) {
            // Si falla la consulta automática el resumen queda ENVIADO
            // y se puede consultar manualmente desde DailySummaries
        }

        // Guardar CDR si lo obtuvimos
        $cdrPath = null;
        if ($cdrZip) {
            $cdrDir = WWW_ROOT . 'cdr' . DS;
            if (!is_dir($cdrDir)) {
                mkdir($cdrDir, 0777, true);
            }
            $cdrPath = 'cdr/R-' . $nombre . '.zip';
            file_put_contents(WWW_ROOT . $cdrPath, $cdrZip);
        }

        // ✅ Persistir todo en una transacción atómica
        $conn          = $DailySummaries->getConnection();
        $summaryEntity = null;

        $conn->transactional(function () use (
            $DailySummaries, $companyId, $fecha, $corr, $nombre,
            $ticket, $xmlPath, $cdrPath, $cdrCode, $cdrDescription,
            $estadoFinal, $boletas, &$summaryEntity
        ) {
            $summaryEntity = $DailySummaries->newEntity([
                'company_id'        => $companyId,
                'fecha'             => $fecha->format('Y-m-d'),
                'correlativo'       => $corr,
                'nombre'            => $nombre,
                'estado'            => $estadoFinal,
                'ticket'            => $ticket,
                'xml_path'          => $xmlPath,
                'cdr_path'          => $cdrPath,
                'codigo_sunat'      => $cdrCode,
                'descripcion_sunat' => $cdrDescription,
            ]);

            if (!$DailySummaries->save($summaryEntity)) {
                throw new \RuntimeException('No se pudo guardar el resumen diario.');
            }

            foreach ($boletas as $b) {
                $b->daily_summary_id = $summaryEntity->id;
                $b->estado           = $estadoFinal === 'ACEPTADO' ? 'ACEPTADO' : 'EN_RESUMEN';

                if ($estadoFinal === 'ACEPTADO') {
                    $b->codigo_sunat      = $cdrCode;
                    $b->descripcion_sunat = $cdrDescription;
                    $b->cdr_path          = $cdrPath;
                }

                if (!$this->Invoices->save($b)) {
                    throw new \RuntimeException('No se pudo actualizar boleta ID ' . $b->id);
                }
            }
        });

        if ($estadoFinal === 'ACEPTADO') {
            $this->Flash->success('✅ Resumen aceptado por SUNAT: ' . $cdrDescription);
        } else {
            $this->Flash->warning(
                '📨 Resumen enviado (Ticket: ' . $ticket . '). ' .
                'SUNAT aún no procesó la respuesta. Puedes consultar el CDR desde el detalle del resumen.'
            );
        }

        return $this->redirect(['controller' => 'DailySummaries', 'action' => 'view', $summaryEntity->id]);

    } catch (\Throwable $e) {
        // Excepción general — las boletas NO fueron tocadas, siguen PENDIENTE_RESUMEN
        $this->Flash->error('Error enviando resumen: ' . $e->getMessage());
        return $this->redirect($this->referer());
    }
}
    public function previsualizarResumen()
{
    $this->request->allowMethod(['get']);

    $companyId = (int) $this->request->getQuery('company_id', 0);

    if ($companyId <= 0) {
        $this->Flash->error('Falta company_id');
        return $this->redirect(['action' => 'index']);
    }

    // Ya NO filtramos por "hoy". Traemos TODAS las boletas pendientes
    // de resumen (de cualquier día) que aún no tengan daily_summary_id.
    $boletas = $this->Invoices->find()
        ->where([
            'company_id' => $companyId,
            'tipo_doc' => '03',
            'estado' => 'PENDIENTE_RESUMEN',
        ])
        ->where(function ($exp, $q) {
            return $exp->isNull('daily_summary_id');
        })
        ->order(['created' => 'ASC'])
        ->all()
        ->toList();

    // Las agrupamos por su fecha REAL de emisión (created),
    // porque SUNAT exige que cada resumen tenga la fecha de generación
    // correspondiente a esas boletas, no la fecha de hoy.
    $gruposPorFecha = [];
    foreach ($boletas as $b) {
        $diaKey = $b->created->format('Y-m-d');
        $gruposPorFecha[$diaKey][] = $b;
    }
   ksort($gruposPorFecha);

    $fechaMasAntigua = array_key_first($gruposPorFecha);

    $Companies = $this->fetchTable('Companies');
    $company = $Companies->get($companyId);

    $this->set(compact('gruposPorFecha', 'company', 'companyId', 'fechaMasAntigua'));

    $Companies = $this->fetchTable('Companies');
    $company = $Companies->get($companyId);

    $this->set(compact('gruposPorFecha', 'company', 'companyId'));
}

    public function pdf($id)
    {
        $this->autoRender = false;

        $invoice = $this->Invoices->get($id, [
            'contain' => [
                'Companies',
                'Pacientes',
                'HistoriasClinicas',
                'InvoiceItems' => ['Tratamientos'],
                'CajaMovimientos',
            ],
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);

        $this->viewBuilder()
            ->disableAutoLayout()
            ->setTemplatePath('Invoices')
            ->setTemplate('pdf');

        // ✅ SOLUCIÓN: Convertir logo a Base64
        $logoPath = WWW_ROOT . 'img' . DS . 'logoJyza.webp';

        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoUrl = 'data:image/webp;base64,' . $logoData;
        } else {
            // Si no existe, usar una imagen vacía
            $logoUrl = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
        }

        $this->set(compact('invoice', 'logoUrl'));

        $html = $this->render('pdf')->getBody();

        $dompdf->loadHtml((string) $html, 'UTF-8');
        $dompdf->setPaper([0, 0, 161.5, 500.5], 'portrait');
        $dompdf->render();

        $subFolder = $this->obtenerSubcarpetaPdf($invoice);
        $baseDir = WWW_ROOT . 'pdf' . DS . $subFolder . DS;

        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0777, true);
        }

        $fileName = $this->obtenerNombrePdf($invoice);
        $filePath = $baseDir . $fileName;

        $pdfOutput = $dompdf->output();
        file_put_contents($filePath, $pdfOutput);

        $invoice->pdf_path = 'pdf/' . $subFolder . '/' . $fileName;
        $this->Invoices->saveOrFail($invoice);

        return $this->response
            ->withType('application/pdf')
            ->withStringBody($pdfOutput);
    }

    public function consultarResumen($id)
    {
        $this->request->allowMethod(['get', 'post']);

        $DailySummaries = $this->fetchTable('DailySummaries');
        $Companies = $this->fetchTable('Companies');

        $summary = $DailySummaries->get($id);

        if (empty($summary->ticket)) {
            $this->Flash->error('El resumen no tiene ticket.');
            return $this->redirect($this->referer());
        }

        $company = $Companies->get((int) $summary->company_id);

        try {
            $sunat = new SunatService($company);
            $see = $sunat->getSee();

            $res = $see->getStatus($summary->ticket);

            if (!$res->isSuccess()) {
                $err = $res->getError();
                $code = (string) $err->getCode();
                $message = (string) $err->getMessage();

                $summary->estado = 'RECHAZADO';
                $summary->codigo_sunat = $code;
                $summary->descripcion_sunat = $message;
                $DailySummaries->save($summary);

                $this->Flash->error('Error consultando ticket: ' . $code . ' - ' . $message);
                return $this->redirect(['controller' => 'DailySummaries', 'action' => 'view', $summary->id]);
            }

            $cdrDir = WWW_ROOT . 'cdr' . DS;
            if (!is_dir($cdrDir)) {
                mkdir($cdrDir, 0777, true);
            }

            $cdrPath = 'cdr/R-' . $summary->nombre . '.zip';
            file_put_contents(WWW_ROOT . $cdrPath, $res->getCdrZip());

            $cdr = $res->getCdrResponse();

            $summary->estado = 'ACEPTADO';
            $summary->codigo_sunat = (string) $cdr->getCode();
            $summary->descripcion_sunat = (string) $cdr->getDescription();
            $summary->cdr_path = $cdrPath;
            $DailySummaries->save($summary);

            $boletas = $this->Invoices->find()
                ->where(['daily_summary_id' => $summary->id])
                ->all();

            foreach ($boletas as $b) {
                $b->estado = 'ACEPTADO';
                $b->codigo_sunat = (string) $cdr->getCode();
                $b->descripcion_sunat = (string) $cdr->getDescription();
                $b->cdr_path = $cdrPath;
                $this->Invoices->save($b);
            }

            $this->Flash->success('Resumen aceptado: ' . $cdr->getDescription());
            return $this->redirect(['controller' => 'DailySummaries', 'action' => 'view', $summary->id]);
        } catch (\Throwable $e) {
            $summary->estado = 'RECHAZADO';
            $summary->codigo_sunat = 'EXCEPTION';
            $summary->descripcion_sunat = $e->getMessage();
            $DailySummaries->save($summary);

            $this->Flash->error('Error consultando resumen: ' . $e->getMessage());
            return $this->redirect(['controller' => 'DailySummaries', 'action' => 'view', $summary->id]);
        }
    }
    //pdf rutas y generacion de pdf
    private function generarPdfInvoice(int $id): void
    {
        $invoice = $this->Invoices->get($id, [
            'contain' => [
                'Companies',
                'Pacientes',
                'HistoriasClinicas',
                'InvoiceItems' => ['Tratamientos'],
            ],
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);

        $this->viewBuilder()
            ->disableAutoLayout()
            ->setTemplatePath('Invoices')
            ->setTemplate('pdf');

        $this->set(compact('invoice'));

        $html = $this->render('pdf')->getBody();

        $dompdf->loadHtml((string) $html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $subFolder = $this->obtenerSubcarpetaPdf($invoice);
        $baseDir = WWW_ROOT . 'pdf' . DS . $subFolder . DS;

        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0777, true);
        }

        $fileName = $this->obtenerNombrePdf($invoice);
        $filePath = $baseDir . $fileName;

        file_put_contents($filePath, $dompdf->output());

        $invoice->pdf_path = 'pdf/' . $subFolder . '/' . $fileName;
        $this->Invoices->saveOrFail($invoice);
    }
    private function obtenerSubcarpetaPdf($invoice): string
    {
        if ($invoice->estado === 'RECIBO_INTERNO') {
            return 'recibos_internos';
        }

        if ($invoice->tipo_doc === '01') {
            return 'facturas';
        }

        return 'boletas';
    }

    private function obtenerNombrePdf($invoice): string
    {
        if ($invoice->estado === 'RECIBO_INTERNO') {
            return 'RI-' . $invoice->id . '.pdf';
        }

        $serie = $invoice->serie ?: 'DOC';
        $correlativo = $invoice->correlativo ?: $invoice->id;

        return $serie . '-' . $correlativo . '.pdf';
    }
    private function obtenerSiguienteCorrelativo(int $companyId, string $serie): int
    {
        $last = $this->Invoices->find()
            ->select(['correlativo'])
            ->where([
                'company_id' => $companyId,
                'serie' => $serie,
            ])
            ->order(['correlativo' => 'DESC'])
            ->first();

        return $last ? ((int) $last->correlativo + 1) : 1;
    }
    public function consultarDniApi()
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;

        $dni = trim((string) $this->request->getQuery('dni', ''));

        if (!preg_match('/^\d{8}$/', $dni)) {
            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'ok' => false,
                    'message' => 'DNI inválido'
                ], JSON_UNESCAPED_UNICODE));
        }

        try {
            $apiKey = (string) \Cake\Core\Configure::read('PeruApi.key');
            $baseUrl = rtrim((string) \Cake\Core\Configure::read('PeruApi.baseUrl'), '/');

            if ($apiKey === '' || $baseUrl === '') {
                throw new \RuntimeException('No se configuró PeruApi en app_local.php');
            }

            $url = $baseUrl . '/dni/' . $dni . '?summary=0&plan=0';

            $ch = \curl_init($url);
            \curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'X-API-KEY: ' . $apiKey,
                    'Accept: application/json',
                ],
                CURLOPT_TIMEOUT => 15,
            ]);

            $res = \curl_exec($ch);

            if ($res === false) {
                $errorCurl = \curl_error($ch);
                \curl_close($ch);
                throw new \RuntimeException('Error CURL: ' . $errorCurl);
            }

            $httpCode = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
            \curl_close($ch);

            $json = json_decode($res, true);

            if ($httpCode !== 200 || !is_array($json)) {
                throw new \RuntimeException('Respuesta inválida: ' . $res);
            }

            $nombreCompleto = trim((string) ($json['cliente'] ?? ''));

            if ($nombreCompleto === '') {
                $nombreCompleto = trim(
                    (string) ($json['nombres'] ?? '') . ' ' .
                    (string) ($json['apellido_paterno'] ?? '') . ' ' .
                    (string) ($json['apellido_materno'] ?? '')
                );
            }

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'ok' => true,
                    'dni' => $json['dni'] ?? $dni,
                    'nombre' => $nombreCompleto,
                ], JSON_UNESCAPED_UNICODE));

        } catch (\Throwable $e) {
            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'ok' => false,
                    'message' => 'Error consultando DNI',
                    'error' => $e->getMessage(),
                ], JSON_UNESCAPED_UNICODE));
        }
    }

    public function consultarRucApi()
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;

        $ruc = trim((string) $this->request->getQuery('ruc', ''));

        if (!preg_match('/^\d{11}$/', $ruc)) {
            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'ok' => false,
                    'message' => 'RUC inválido'
                ], JSON_UNESCAPED_UNICODE));
        }

        try {
            $apiKey = (string) \Cake\Core\Configure::read('PeruApi.key');
            $baseUrl = rtrim((string) \Cake\Core\Configure::read('PeruApi.baseUrl'), '/');

            if ($apiKey === '' || $baseUrl === '') {
                throw new \RuntimeException('No se configuró PeruApi en app_local.php');
            }

            $url = $baseUrl . '/ruc/' . $ruc . '?summary=0&plan=0';

            $ch = \curl_init($url);
            \curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'X-API-KEY: ' . $apiKey,
                    'Accept: application/json',
                ],
                CURLOPT_TIMEOUT => 15,
            ]);

            $res = \curl_exec($ch);

            if ($res === false) {
                $errorCurl = \curl_error($ch);
                \curl_close($ch);
                throw new \RuntimeException('Error CURL: ' . $errorCurl);
            }

            $httpCode = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
            \curl_close($ch);

            $json = json_decode($res, true);

            if ($httpCode !== 200 || !is_array($json)) {
                throw new \RuntimeException('Respuesta inválida: ' . $res);
            }

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'ok' => true,
                    'ruc' => $json['ruc'] ?? $ruc,
                    'razon_social' => $json['razon_social'] ?? '',
                    'direccion' => $json['direccion'] ?? '',
                    'estado' => $json['estado'] ?? '',
                    'condicion' => $json['condicion'] ?? '',
                ], JSON_UNESCAPED_UNICODE));

        } catch (\Throwable $e) {
            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'ok' => false,
                    'message' => 'Error consultando RUC',
                    'error' => $e->getMessage(),
                ], JSON_UNESCAPED_UNICODE));
        }
    }


    //Eliminar factura
    public function anular($id)
{
    $this->request->allowMethod(['post', 'get']);

    $invoice = $this->Invoices->get($id, [
        'contain' => ['Companies']
    ]);

    // Solo facturas aceptadas
    if ($invoice->tipo_doc !== '01') {
        $this->Flash->error('Solo se pueden anular facturas.');
        return $this->redirect(['action' => 'view', $id]);
    }

    if ($invoice->estado !== 'ACEPTADO') {
        $this->Flash->error('Solo se pueden anular facturas aceptadas.');
        return $this->redirect(['action' => 'view', $id]);
    }

    $company = $invoice->company;

    try {

        $sunat = new SunatService($company);
        $see = $sunat->getSee();

        $empresa = (new GreCompany())
            ->setRuc((string)$company->ruc)
            ->setRazonSocial((string)$company->razon_social)
            ->setNombreComercial((string)($company->nombre_comercial ?: $company->razon_social))
            ->setAddress(
                (new Address())
                    ->setUbigueo((string)$company->ubigeo)
                    ->setDepartamento((string)$company->departamento)
                    ->setProvincia((string)$company->provincia)
                    ->setDistrito((string)$company->distrito)
                    ->setUrbanizacion((string)($company->urbanizacion ?: '-'))
                    ->setDireccion((string)$company->direccion)
                    ->setCodLocal((string)($company->cod_local ?: '0000'))
            );

        $detail = (new VoidedDetail())
            ->setTipoDoc('01')
            ->setSerie((string)$invoice->serie)
            ->setCorrelativo((string)$invoice->correlativo)
            ->setDesMotivoBaja('ERROR EN EMISION');

        $fechaEmision = $invoice->created->format('Y-m-d');
        $hoy = (new \DateTime('now', new \DateTimeZone('America/Lima')))->format('Y-m-d');

        // Correlativo incremental por empresa y fecha (sin duplicados)
        $correlativoBaja = (string) $this->obtenerSiguienteCorrelativoBaja(
            (int) $invoice->company_id,
            $hoy
        );

        $voided = (new Voided())
            ->setCorrelativo($correlativoBaja)
            ->setFecGeneracion(
                new \DateTime($fechaEmision)
            )
            ->setFecComunicacion(
                new \DateTime('now', new \DateTimeZone('America/Lima'))
            )
            ->setCompany($empresa)
            ->setDetails([$detail]);

        $result = $see->send($voided);

        // guardar XML
        $xmlDir = WWW_ROOT . 'xml' . DS;

        if (!is_dir($xmlDir)) {
            if (!mkdir($xmlDir, 0777, true)) {
                \Cake\Log\Log::error('No se pudo crear carpeta XML: ' . $xmlDir);
            }
        }

        $xmlPath = $xmlDir . $voided->getName() . '.xml';
        $xmlContent = $see->getFactory()->getLastXml();
        
        if (!$xmlContent) {
            \Cake\Log\Log::error('XML vacío para voided: ' . $voided->getName());
        } else {
            $written = file_put_contents($xmlPath, $xmlContent);
            if ($written === false) {
                \Cake\Log\Log::error('No se pudo escribir XML en: ' . $xmlPath);
            } else {
                \Cake\Log\Log::info('XML guardado: ' . $xmlPath . ' (' . $written . ' bytes)');
            }
        }

        if (!$result->isSuccess()) {

            $err = $result->getError();

            $this->Flash->error(
                'SUNAT rechazó la baja: ' .
                $err->getCode() . ' - ' .
                $err->getMessage()
            );

            return $this->redirect(['action' => 'view', $id]);
        }

        $ticket = $result->getTicket();

        sleep(3);

        $statusResult = $see->getStatus($ticket);

        if (!$statusResult->isSuccess()) {

            $err = $statusResult->getError();

            $this->Flash->warning(
                'Baja enviada pero no se pudo consultar ticket: ' .
                $err->getMessage()
            );

            return $this->redirect(['action' => 'view', $id]);
        }

        // guardar CDR
        $cdrDir = WWW_ROOT . 'cdr' . DS;

        if (!is_dir($cdrDir)) {
            if (!mkdir($cdrDir, 0777, true)) {
                \Cake\Log\Log::error('No se pudo crear carpeta CDR: ' . $cdrDir);
            }
        }

        $cdrPath = $cdrDir . 'R-' . $voided->getName() . '.zip';
        $cdrContent = $statusResult->getCdrZip();
        
        if (!$cdrContent) {
            \Cake\Log\Log::error('CDR vacío para voided: ' . $voided->getName());
        } else {
            $written = file_put_contents($cdrPath, $cdrContent);
            if ($written === false) {
                \Cake\Log\Log::error('No se pudo escribir CDR en: ' . $cdrPath);
            } else {
                \Cake\Log\Log::info('CDR guardado: ' . $cdrPath . ' (' . $written . ' bytes)');
            }
        }

        $cdr = $statusResult->getCdrResponse();

        // guardar en daily_summaries y actualizar invoice en transacción atómica
        $DailySummaries = $this->fetchTable('DailySummaries');
        $hoy = (new \DateTime('now', new \DateTimeZone('America/Lima')))->format('Y-m-d');

        try {
            $conn = $DailySummaries->getConnection();
            
            $conn->transactional(function () use (
                $DailySummaries, $invoice, $cdr, $voided, 
                $result, $correlativoBaja, $hoy
            ) {
                // crear registro de anulación en daily_summaries
                $voidSummary = $DailySummaries->newEntity([
                    'company_id' => (int)$invoice->company_id,
                    'fecha' => $hoy,
                    'correlativo' => (int)$correlativoBaja,
                    'nombre' => $voided->getName(),
                    'ticket' => $result->getTicket(),
                    'estado' => 'ACEPTADO',
                    'codigo_sunat' => (string)$cdr->getCode(),
                    'descripcion_sunat' => 'ANULADO: ' . $cdr->getDescription(),
                    'xml_path' => 'xml/' . $voided->getName() . '.xml',
                    'cdr_path' => 'cdr/R-' . $voided->getName() . '.zip',
                ]);

                if (!$DailySummaries->save($voidSummary)) {
                    throw new \RuntimeException('No se pudo guardar la anulación en daily_summaries.');
                }

                // actualizar invoice con el daily_summary_id
                $invoice->estado = 'ANULADO';
                $invoice->codigo_sunat = (string)$cdr->getCode();
                $invoice->descripcion_sunat = 'ANULADO: ' . $cdr->getDescription();
                $invoice->daily_summary_id = $voidSummary->id;
                $invoice->cdr_path = null; // limpiar CDR antiguo, ahora está en daily_summaries

                $this->Invoices->saveOrFail($invoice);
            });
        } catch (\Exception $e) {
            $this->Flash->error('Error guardando anulación: ' . $e->getMessage());
            return $this->redirect(['action' => 'view', $id]);
        }

        $this->registrarEgresoAnulacion($invoice, 'REEMBOLSO ANULACIÓN FACTURA');
        $this->devolverStockAnulacion($invoice);


        $this->Flash->success(
            'Factura anulada y reembolso registrado en caja: ' .
            $cdr->getDescription()
        );

        return $this->redirect(['action' => 'view', $id]);

    } catch (\Throwable $e) {

        $this->Flash->error(
            'Error anulando factura: ' .
            $e->getMessage()
        );

        return $this->redirect(['action' => 'view', $id]);
    }
}

    //elimnar boleta
    public function anularBoleta($id)
{
    $this->request->allowMethod(['post', 'get']);

    $invoice = $this->Invoices->get($id, [
        'contain' => ['Companies']
    ]);

    if ($invoice->tipo_doc !== '03') {
        $this->Flash->error('Solo se pueden anular boletas.');
        return $this->redirect(['action' => 'view', $id]);
    }

    if ($invoice->estado !== 'ACEPTADO') {
        $this->Flash->error('Solo se pueden anular boletas aceptadas.');
        return $this->redirect(['action' => 'view', $id]);
    }

    $company = $invoice->company;

    try {
        $sunat = new SunatService($company);
        $see = $sunat->getSee();

        $empresa = (new GreCompany())
            ->setRuc((string)$company->ruc)
            ->setRazonSocial((string)$company->razon_social)
            ->setNombreComercial((string)($company->nombre_comercial ?: $company->razon_social))
            ->setAddress(
                (new Address())
                    ->setUbigueo((string)$company->ubigeo)
                    ->setDepartamento((string)$company->departamento)
                    ->setProvincia((string)$company->provincia)
                    ->setDistrito((string)$company->distrito)
                    ->setUrbanizacion((string)($company->urbanizacion ?: '-'))
                    ->setDireccion((string)$company->direccion)
                    ->setCodLocal((string)($company->cod_local ?: '0000'))
            );

        $fechaEmision = $invoice->created->format('Y-m-d');
        $hoy = (new \DateTime('now', new \DateTimeZone('America/Lima')))->format('Y-m-d');

        // Correlativo incremental por empresa y fecha
        $correlativoBaja = (string) $this->obtenerSiguienteCorrelativoBaja(
            (int) $invoice->company_id,
            $hoy
        );

        $detail = (new SummaryDetail())
            ->setTipoDoc('03')
            ->setSerieNro(
                $invoice->serie . '-' .
                str_pad((string)$invoice->correlativo, 8, '0', STR_PAD_LEFT)
            )
            ->setEstado('3') // 3 = ANULACION
            ->setClienteTipo((string)$invoice->cliente_tipo_doc)
            ->setClienteNro((string)$invoice->cliente_numero)
            ->setTotal((float)$invoice->total)
            ->setMtoOperGravadas((float)$invoice->subtotal)
            ->setMtoIGV((float)$invoice->igv)
            ->setPorcentajeIgv(18.00);

        $summary = (new Summary())
            ->setFecGeneracion(new \DateTime($fechaEmision))
            ->setFecResumen(new \DateTime('now', new \DateTimeZone('America/Lima')))
            ->setCorrelativo($correlativoBaja)  // ✅ número correcto
            ->setCompany($empresa)
            ->setDetails([$detail]);

        $res = $see->send($summary);

        if (!$res->isSuccess()) {
            $err = $res->getError();
            $this->Flash->error(
                'SUNAT rechazó anulación: ' . $err->getCode() . ' - ' . $err->getMessage()
            );
            return $this->redirect(['action' => 'view', $id]);
        }

        $ticket = $res->getTicket();
        sleep(3);
        $statusRes = $see->getStatus($ticket);

        if (!$statusRes->isSuccess()) {
            $err = $statusRes->getError();
            $this->Flash->warning('Anulación enviada pero ticket pendiente: ' . $err->getMessage());
            return $this->redirect(['action' => 'view', $id]);
        }

        $cdr = $statusRes->getCdrResponse();

        // guardar en daily_summaries y actualizar invoice en transacción atómica
        $DailySummaries = $this->fetchTable('DailySummaries');

        try {
            $conn = $DailySummaries->getConnection();
            
            $conn->transactional(function () use (
                $DailySummaries, $invoice, $cdr, $summary, 
                $ticket, $correlativoBaja, $hoy
            ) {
                // crear registro de anulación en daily_summaries
                $summaryRecord = $DailySummaries->newEntity([
                    'company_id' => (int)$invoice->company_id,
                    'fecha' => $hoy,
                    'correlativo' => (int)$correlativoBaja,
                    'nombre' => $summary->getName(),
                    'ticket' => $ticket,
                    'estado' => 'ACEPTADO',
                    'codigo_sunat' => (string)$cdr->getCode(),
                    'descripcion_sunat' => 'ANULADO: ' . $cdr->getDescription(),
                    'xml_path' => 'xml/' . $summary->getName() . '.xml',
                    'cdr_path' => 'cdr/R-' . $summary->getName() . '.zip',
                ]);

                if (!$DailySummaries->save($summaryRecord)) {
                    throw new \RuntimeException('No se pudo guardar la anulación en daily_summaries.');
                }

                // actualizar boleta con el daily_summary_id
                $invoice->estado = 'ANULADO';
                $invoice->codigo_sunat = (string)$cdr->getCode();
                $invoice->descripcion_sunat = 'ANULADO: ' . $cdr->getDescription();
                $invoice->daily_summary_id = $summaryRecord->id;
                $invoice->cdr_path = null; // limpiar CDR antiguo, ahora está en daily_summaries

                $this->Invoices->saveOrFail($invoice);
            });
        } catch (\Exception $e) {
            $this->Flash->error('Error guardando anulación: ' . $e->getMessage());
            return $this->redirect(['action' => 'view', $id]);
        }

        $this->registrarEgresoAnulacion($invoice, 'REEMBOLSO ANULACIÓN BOLETA');
        $this->devolverStockAnulacion($invoice);

        $this->Flash->success('Boleta anulada y reembolso registrado en caja: ' . $cdr->getDescription());
        return $this->redirect(['action' => 'view', $id]);

    } catch (\Throwable $e) {
        $this->Flash->error('Error anulando boleta: ' . $e->getMessage());
        return $this->redirect(['action' => 'view', $id]);
    }
}
    //vista para anular factura
    public function anulaciones()
{
    // =========================================
    // PROCESAR ANULACION DE BOLETAS
    // =========================================

    if ($this->request->is('post')) {

        $ids = $this->request->getData('boletas');

        if (empty($ids)) {

            $this->Flash->error(
                'Seleccione al menos una boleta.'
            );

            return $this->redirect([
                'action' => 'anulaciones'
            ]);
        }

        $boletas = $this->Invoices->find()
            ->contain(['Companies'])
            ->where([
                'Invoices.id IN' => $ids,
                'Invoices.tipo_doc' => '03',
                'Invoices.estado' => 'ACEPTADO'
            ])
            ->order(['Invoices.created' => 'ASC'])
            ->all();

        if ($boletas->isEmpty()) {

            $this->Flash->error(
                'No se encontraron boletas válidas.'
            );

            return $this->redirect([
                'action' => 'anulaciones'
            ]);
        }

        try {

            $first = $boletas->first();

            $company = $first->company;

            $sunat = new SunatService($company);

            $see = $sunat->getSee();

            // =========================================
            // EMPRESA
            // =========================================

            $empresa = (new GreCompany())
                ->setRuc((string)$company->ruc)
                ->setRazonSocial((string)$company->razon_social)
                ->setNombreComercial(
                    (string)($company->nombre_comercial ?: $company->razon_social)
                )
                ->setAddress(
                    (new Address())
                        ->setUbigueo((string)$company->ubigeo)
                        ->setDepartamento((string)$company->departamento)
                        ->setProvincia((string)$company->provincia)
                        ->setDistrito((string)$company->distrito)
                        ->setUrbanizacion((string)($company->urbanizacion ?: '-'))
                        ->setDireccion((string)$company->direccion)
                        ->setCodLocal((string)($company->cod_local ?: '0000'))
                );

            // =========================================
            // DETALLES
            // =========================================

            $details = [];

            foreach ($boletas as $i => $b) {

                $detail = (new SummaryDetail())

                ->setTipoDoc('03')

                ->setSerieNro(
                    $b->serie . '-' .
                    str_pad(
                        (string)$b->correlativo,
                        8,
                        '0',
                        STR_PAD_LEFT
                    )
                )

                ->setEstado('3')

                ->setClienteTipo(
                    (string)$b->cliente_tipo_doc
                )

                ->setClienteNro(
                    (string)$b->cliente_numero
                )

                ->setTotal((float)$b->total)

                ->setMtoOperGravadas(
                    (float)$b->subtotal
                )

                ->setMtoIGV(
                    (float)$b->igv
                )

                ->setPorcentajeIgv(18);

                $details[] = $detail;
            }

            // =========================================
            // RESUMEN
            // =========================================

            $summary = (new Summary())

                ->setFecGeneracion(
                    new \DateTime(
                        $first->created->format('Y-m-d')
                    )
                )

                ->setFecResumen(
                    new \DateTime(
                        'now',
                        new \DateTimeZone('America/Lima')
                    )
                )

                ->setCorrelativo((string) $this->obtenerSiguienteCorrelativoBaja(
                    (int) $first->company_id,
                    (new \DateTime('now', new \DateTimeZone('America/Lima')))->format('Y-m-d')
                ))

                ->setCompany($empresa)

                ->setDetails($details);

            // =========================================
            // ENVIAR SUNAT
            // =========================================

            $res = $see->send($summary);

            // =========================================
            // GUARDAR XML
            // =========================================

            $xmlDir = WWW_ROOT . 'xml' . DS;

            if (!is_dir($xmlDir)) {
                if (!mkdir($xmlDir, 0777, true)) {
                    \Cake\Log\Log::error('No se pudo crear carpeta XML: ' . $xmlDir);
                }
            }

            $relativeXmlPath = 'xml/' . $summary->getName() . '.xml';
            $xmlPath = $xmlDir . $summary->getName() . '.xml';
            $xmlContent = $see->getFactory()->getLastXml();
            
            if (!$xmlContent) {
                \Cake\Log\Log::error('XML vacío para summary: ' . $summary->getName());
            } else {
                $written = file_put_contents($xmlPath, $xmlContent);
                if ($written === false) {
                    \Cake\Log\Log::error('No se pudo escribir XML en: ' . $xmlPath);
                } else {
                    \Cake\Log\Log::info('XML guardado: ' . $xmlPath . ' (' . $written . ' bytes)');
                }
            }

            // =========================================
            // ERROR SUNAT
            // =========================================

        //    if (!$res->isSuccess()) {

        //         $err = $res->getError();

        //         dd([
        //             'codigo' => $err->getCode(),
        //             'mensaje' => $err->getMessage()
        //         ]);
        //     }
        // Bloque 1 - error SUNAT
            if (!$res->isSuccess()) {
                $err = $res->getError();
                $this->Flash->error(
                    'SUNAT rechazó anulación: ' . $err->getCode() . ' - ' . $err->getMessage()
                );
                return $this->redirect(['action' => 'anulaciones']);
            }

            // =========================================
            // TICKET
            // =========================================

            $ticket = $res->getTicket();

            sleep(3);

            $status = $see->getStatus($ticket);

            // if (!$status->isSuccess()) {

            //     $err = $status->getError();

            //     dd([
            //         'ticket_error' => $err->getMessage()
            //     ]);
            // }
            // Bloque 2 - error ticket
            if (!$status->isSuccess()) {
                $err = $status->getError();
                $this->Flash->warning(
                    'Anulación enviada pero ticket pendiente: ' . $err->getMessage()
                );
                return $this->redirect(['action' => 'anulaciones']);
            }

            // =========================================
            // GUARDAR CDR
            // =========================================

            $cdrDir = WWW_ROOT . 'cdr' . DS;

            if (!is_dir($cdrDir)) {
                if (!mkdir($cdrDir, 0777, true)) {
                    \Cake\Log\Log::error('No se pudo crear carpeta CDR: ' . $cdrDir);
                }
            }

            $relativeCdrPath = 'cdr/R-' . $summary->getName() . '.zip';
            $cdrPath = $cdrDir . 'R-' . $summary->getName() . '.zip';
            $cdrContent = $status->getCdrZip();
            
            if (!$cdrContent) {
                \Cake\Log\Log::error('CDR vacío para summary: ' . $summary->getName());
            } else {
                $written = file_put_contents($cdrPath, $cdrContent);
                if ($written === false) {
                    \Cake\Log\Log::error('No se pudo escribir CDR en: ' . $cdrPath);
                } else {
                    \Cake\Log\Log::info('CDR guardado: ' . $cdrPath . ' (' . $written . ' bytes)');
                }
            }

            $cdr = $status->getCdrResponse();

            // =========================================
            // ACTUALIZAR BOLETAS
            // =========================================

            foreach ($boletas as $b) {

                $b->estado = 'ANULADO';

                $b->codigo_sunat =
                    (string)$cdr->getCode();

                $b->descripcion_sunat =
                    'ANULADO: ' .
                    $cdr->getDescription();

                $this->Invoices->save($b);

                $this->registrarEgresoAnulacion($b, 'REEMBOLSO ANULACIÓN BOLETA');
                $this->devolverStockAnulacion($b);
            }

            $this->Flash->success(
                'Boletas anuladas y reembolsos registrados en caja.'
            );

            return $this->redirect([
                'action' => 'anulaciones'
            ]);

        // } catch (\Throwable $e) {

        //     dd($e->getMessage());
        // }
        // Bloque 3 - catch
        } catch (\Throwable $e) {
            $this->Flash->error('Error anulando boletas: ' . $e->getMessage());
            return $this->redirect(['action' => 'anulaciones']);
        }
    }

    // =========================================
    // MOSTRAR VISTA
    // =========================================

    $desde = new \DateTime('-24 hours');

    $facturas = $this->Invoices->find()
        ->where([
            'tipo_doc' => '01',
            'estado' => 'ACEPTADO',
            'created >=' => $desde
        ])
        ->order(['created' => 'DESC'])
        ->all();

    $boletas = $this->Invoices->find()
        ->where([
            'tipo_doc' => '03',
            'estado' => 'ACEPTADO',
            'created >=' => $desde
        ])
        ->order(['created' => 'DESC'])
        ->all();

        // Cámbialo por:
    $recibos = $this->Invoices->find()
        ->where([
            'tipo_doc' => 'RI',
            'estado'   => 'RECIBO_INTERNO',
            'created >=' => $desde
        ])
        ->order(['created' => 'DESC'])
        ->all();

    $this->set(compact(
        'facturas',
        'boletas',
        'recibos'
    ));
}
private function obtenerSiguienteCorrelativoBaja(int $companyId, string $fecha): int
{
    // Busca en daily_summaries los resúmenes de ANULACIÓN del mismo día
    // para no chocar con los resúmenes normales de boletas
    $DailySummaries = $this->fetchTable('DailySummaries');

    $last = $DailySummaries->find()
        ->select(['correlativo'])
        ->where([
            'company_id' => $companyId,
            'fecha' => $fecha,
        ])
        ->order(['correlativo' => 'DESC'])
        ->first();

    return $last ? ((int) $last->correlativo + 1) : 1;
}
public function anularReciboInterno($id)
{
    $this->request->allowMethod(['post', 'get']);

    $invoice = $this->Invoices->get($id);

    if ($invoice->tipo_doc !== 'RI') {
        $this->Flash->error('Solo se pueden anular Recibos Internos aquí.');
        return $this->redirect(['action' => 'view', $id]);
    }

    if ($invoice->estado !== 'RECIBO_INTERNO') {
        $this->Flash->error('Este recibo ya fue anulado o no está activo.');
        return $this->redirect(['action' => 'view', $id]);
    }

    $invoice->estado = 'ANULADO';
    $invoice->descripcion_sunat = 'ANULADO INTERNAMENTE';

    if (!$this->Invoices->save($invoice)) {
        $this->Flash->error('No se pudo anular el Recibo Interno.');
        return $this->redirect(['action' => 'view', $id]);
    }

    $this->registrarEgresoAnulacion($invoice, 'REEMBOLSO ANULACIÓN RI');
    $this->devolverStockAnulacion($invoice);
    $this->Flash->success('Recibo Interno #' . $invoice->correlativo . ' anulado y reembolso registrado en caja.');
    return $this->redirect(['action' => 'index']);
}


/**
 * Registra en CajaEgresos el reembolso de una anulación, en la MISMA caja
 * donde se registró el cobro original (via caja_movimientos.caja_id),
 * aunque esa caja ya esté cerrada, replicando el/los métodos de pago
 * originales. Si el comprobante no tiene movimientos de caja asociados
 * (ej. nunca se cobró en una caja), cae a la caja abierta actual con
 * un único egreso en EFECTIVO por el total.
 */
private function registrarEgresoAnulacion($invoice, string $etiqueta): void
{
    $esCredito = ($invoice->forma_pago ?? 'CONTADO') === 'CREDITO';

    // Si es crédito, no hubo cobro en caja: solo anulamos las cuotas
    // pendientes (si alguna ya estaba PAGADA, eso lo resolveremos cuando
    // implementes el módulo de pagos de cuotas — por ahora no aplica).
    if ($esCredito) {
        $this->fetchTable('InvoiceCuotas')->updateAll(
            ['estado' => 'ANULADA'],
            ['invoice_id' => $invoice->id, 'estado' => 'PENDIENTE']
        );
    }
    
    $CajaEgresos = $this->fetchTable('CajaEgresos');
    $CajaMovimientos = $this->fetchTable('CajaMovimientos');

    $movimientos = $CajaMovimientos->find()
        ->where(['invoice_id' => $invoice->id])
        ->orderBy(['created' => 'DESC'])
        ->all();

    // Un invoice puede haber sido pagado/re-pagado más de una vez (varios
    // lotes de caja_movimientos). Solo el lote más reciente (mismo created)
    // representa el cobro vigente que hay que reembolsar.
    if (!$movimientos->isEmpty()) {
        $ultimoCreated = $movimientos->first()->created;
        $movimientos = $movimientos->filter(
            fn($mov) => $mov->created == $ultimoCreated
        );
    }

    $descripcionBase = $etiqueta . ': ' . $invoice->serie . '-' . $invoice->correlativo . ' | ' . $invoice->cliente_nombre;

    if ($movimientos->isEmpty()) {
        $cajaAbierta = $this->fetchTable('Cajas')->find()->where(['estado' => 'ABIERTA'])->first();
        if (!$cajaAbierta) {
            return;
        }
        $CajaEgresos->save($CajaEgresos->newEntity([
            'caja_id' => $cajaAbierta->id,
            'monto' => $invoice->total,
            'descripcion' => $descripcionBase,
            'metodo_pago' => 'EFECTIVO',
            'tipo' => 'REEMBOLSO_ANULACION',
        ]));
        return;
    }

    foreach ($movimientos as $mov) {
        $CajaEgresos->save($CajaEgresos->newEntity([
            'caja_id' => $mov->caja_id,
            'monto' => $mov->monto_recibido,
            'descripcion' => $descripcionBase,
            'metodo_pago' => $mov->metodo_pago,
            'tipo' => 'REEMBOLSO_ANULACION',
        ]));
    }
}

/**
 * Devuelve al inventario el stock de los productos vendidos en una factura
 * que se está anulando. Los tratamientos y exámenes no manejan stock, no
 * requieren nada aquí.
 */
private function devolverStockAnulacion($invoice): void
{
    $Productos = $this->fetchTable('Productos');

    $items = $this->fetchTable('InvoiceItems')->find()
        ->where(['invoice_id' => $invoice->id, 'tipo_item' => 'producto'])
        ->all();

    foreach ($items as $item) {
        if (empty($item->producto_id)) {
            continue;
        }

        $producto = $Productos->find()->where(['id' => $item->producto_id])->first();
        if (!$producto) {
            continue;
        }

        $producto->stock = (float) $producto->stock + (float) $item->cantidad;
        $Productos->save($producto);
    }
}

}
