<style>
    /* Colores con contraste suficiente (AA) sobre fondo claro */
    .btn-outline-ambar { color: #92400e; border-color: #92400e; background: transparent; }
    .btn-outline-ambar:hover, .btn-outline-ambar:focus { color: #fff; background: #92400e; border-color: #92400e; }
    .btn-abrir-caja { color: #fff; background: #0b5a6b; border-color: #0b5a6b; }
    .btn-abrir-caja:hover, .btn-abrir-caja:focus { color: #fff; background: #083f4b; border-color: #083f4b; }

    /* Scroll horizontal: la barra nativa (al final de la tabla) se oculta y se usa una barra
       flotante pegada al borde inferior de la pantalla, sincronizada con la tabla. */
    .tabla-comprobantes-wrap {
        overflow-x: auto;
        scrollbar-width: none;
    }
    .tabla-comprobantes-wrap::-webkit-scrollbar { display: none; }
    .scroll-x-flotante {
        position: sticky;
        bottom: 0;
        z-index: 5;
        overflow-x: auto;
        overflow-y: hidden;
        background: #fff;
        border-top: 1px solid #dee2e6;
    }
    .scroll-x-flotante > div { height: 1px; }
    .tabla-comprobantes { font-size: .875rem; }
    .tabla-comprobantes th,
    .tabla-comprobantes td { padding: .45rem .5rem; vertical-align: middle; }
    .tabla-comprobantes thead th { white-space: nowrap; }
    .tabla-comprobantes .nowrap { white-space: nowrap; }
    .tabla-comprobantes .cliente-nombre { min-width: 160px; max-width: 260px; }

    /* Columna de acciones fija a la derecha: siempre visible aunque la tabla tenga scroll horizontal */
    .tabla-comprobantes .col-acciones {
        position: sticky;
        right: 0;
        z-index: 2;
        background-color: #fff;
        box-shadow: -6px 0 6px -4px rgba(0, 0, 0, .2);
    }
    .tabla-comprobantes thead .col-acciones { background-color: #17a2b8; }
    .tabla-comprobantes.table-striped tbody tr:nth-of-type(odd) .col-acciones { background-color: #f2f2f2; }
    .tabla-comprobantes tbody tr.table-danger .col-acciones { background-color: #f5c6cb; }
    .tabla-comprobantes .acciones-wrap {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 4px;
        min-width: 140px;
        max-width: 190px;
        margin: 0 auto;
    }
</style>
<div class="container-fluid py-4">
    <?= $this->Flash->render() ?>
    <?php if (empty($cajaAbierta)): ?>
        <div class="alert alert-info d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span><i class="fas fa-info-circle me-1"></i> No hay caja abierta. Puede consultar, anular y enviar comprobantes; para crear uno nuevo debe abrir una caja.</span>
            <a href="<?= $this->Url->build(['controller' => 'Cajas', 'action' => 'abrir']) ?>" class="btn btn-sm btn-abrir-caja">Abrir caja</a>
        </div>
    <?php endif; ?>
    <!-- HEADER CON BOTONES -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0">
            <i class="fas fa-receipt"></i> Gestión de Comprobantes
        </h3>

        <div class="d-flex flex-wrap gap-2">
            <a href="<?= $this->Url->build(['controller' => 'DailySummaries', 'action' => 'index']) ?>" class="btn btn-outline-dark">
                <i class="fas fa-calendar-check"></i> Resúmenes
            </a>

            <a href="<?= $this->Url->build(['action' => 'anulaciones']) ?>" class="btn btn-outline-danger">
                <i class="fas fa-ban"></i> Anulaciones
            </a>
            <a href="<?= $this->Url->build(['action' => 'add']) ?>" class="btn btn-info">
                <i class="fas fa-plus-circle"></i> Nuevo Comprobante
            </a>

            <a href="<?= $this->Url->build(['controller' => 'Cajas', 'action' => 'index']) ?>" class="btn btn-outline-primary">
                <i class="fas fa-cash-register"></i> Caja
            </a>

            <a href="<?= $this->Url->build(['controller' => 'PagosDoctores', 'action' => 'index']) ?>" class="btn btn-outline-ambar">
                <i class="fas fa-money-bill-wave"></i> Pagos Doc.
            </a>

            <a href="<?= $this->Url->build(['controller' => 'PagosLaboratorios', 'action' => 'index']) ?>" class="btn btn-outline-ambar">
                <i class="fas fa-flask"></i> Pagos Lab.
            </a>

            <!-- AHORA (va a la vista de confirmación) -->
<a href="<?= $this->Url->build([
    'action' => 'previsualizarResumen',
    '?' => ['company_id' => 1, 'fecha' => date('Y-m-d')]
]) ?>" class="btn btn-outline-success">
    <i class="fas fa-paper-plane"></i> Resumen Diario
</a>

        </div>
    </div>

    <?php
    $q = fn($k) => $this->request->getQuery($k);
    // La fecha se conserva al cambiar de tipo o buscar.
    $paramsFecha = ['fecha' => $fecha];
    $conFecha = fn(array $extra = []) => $this->Url->build(['action' => 'index', '?' => array_filter($extra + $paramsFecha)]);
    $ayer = date('Y-m-d', strtotime($hoy . ' -1 day'));
    $paramsSinFecha = array_filter([
        'tipo' => $q('tipo'),
        'sin_doctor' => $q('sin_doctor'),
        'cliente' => $q('cliente'),
        'documento' => $q('documento'),
    ]);
    $irA = fn($f) => $this->Url->build(['action' => 'index', '?' => $paramsSinFecha + ['fecha' => $f]]);
    ?>

    <!-- FILTRO POR FECHA -->
    <?php
    $diaAnterior = date('Y-m-d', strtotime($fecha . ' -1 day'));
    $diaSiguiente = date('Y-m-d', strtotime($fecha . ' +1 day'));
    $mesesEs = [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    $diasEs = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
    $fechaLarga = function (string $f) use ($mesesEs, $diasEs): string {
        $t = strtotime($f);
        return $diasEs[(int)date('w', $t)] . ' ' . date('j', $t) . ' de ' . $mesesEs[(int)date('n', $t)] . ' de ' . date('Y', $t);
    };
    ?>
    <style>
        .filtro-fechas .rango-titulo { font-size: 1.05rem; font-weight: 600; line-height: 1.2; }
        .filtro-fechas .rango-sub { font-size: .8rem; color: #6c757d; }
        .filtro-fechas .nav-dia { width: 34px; padding-left: 0; padding-right: 0; }
        .filtro-fechas .fecha-actual { min-width: 190px; }
        .filtro-fechas .form-fecha input[type=date] { width: 155px; }
        .filtro-fechas .kpi { border-left: 3px solid #dee2e6; padding: 2px 12px; min-width: 120px; }
        .filtro-fechas .kpi .kpi-label { font-size: .72rem; color: #6c757d; text-transform: uppercase; letter-spacing: .02em; }
        .filtro-fechas .kpi .kpi-valor { font-size: 1.15rem; font-weight: 700; line-height: 1.2; white-space: nowrap; }
        .filtro-fechas .kpi-total { border-left-color: #28a745; }
        .filtro-fechas .kpi-anulados { border-left-color: #dc3545; }
        .filtro-fechas .kpi-cantidad { border-left-color: #17a2b8; }
        @media (max-width: 767.98px) {
            .filtro-fechas .kpis { margin-top: .75rem; }
            .filtro-fechas .form-fecha input[type=date] { width: 100%; }
        }
    </style>
    <div class="card filtro-fechas mb-3 shadow-sm">
        <div class="card-body py-3">
            <?php if ($filtraPorFecha): ?>
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <div class="d-flex flex-wrap align-items-center">
                        <!-- Día mostrado + navegación por día -->
                        <div class="d-flex align-items-center mb-2 mr-3">
                            <a href="<?= $irA($diaAnterior) ?>" class="btn btn-outline-secondary btn-sm nav-dia" title="Día anterior">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                            <div class="text-center px-3 fecha-actual">
                                <div class="rango-titulo">
                                    <?= $fecha === $hoy ? 'Hoy' : ($fecha === $ayer ? 'Ayer' : h(date('d/m/Y', strtotime($fecha)))) ?>
                                </div>
                                <div class="rango-sub"><?= h($fechaLarga($fecha)) ?></div>
                            </div>
                            <?php if ($fecha < $hoy): ?>
                                <a href="<?= $irA($diaSiguiente) ?>" class="btn btn-outline-secondary btn-sm nav-dia" title="Día siguiente">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php else: ?>
                                <button type="button" class="btn btn-outline-secondary btn-sm nav-dia" disabled><i class="fas fa-chevron-right"></i></button>
                            <?php endif; ?>
                        </div>

                        <!-- Elegir otro día (se aplica al cambiar la fecha) -->
                        <?= $this->Form->create(null, ['type' => 'get', 'class' => 'form-fecha d-flex align-items-center mb-2']) ?>
                            <input type="date" name="fecha" value="<?= h($fecha) ?>" max="<?= h($hoy) ?>"
                                   class="form-control form-control-sm mr-2" title="Elegir fecha"
                                   onchange="if (this.value) this.form.submit()">
                            <?php foreach ($paramsSinFecha as $k => $v): ?>
                                <?= $this->Form->hidden($k, ['value' => $v]) ?>
                            <?php endforeach; ?>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Accesos rápidos">
                                <a href="<?= $irA($hoy) ?>" class="btn <?= $fecha === $hoy ? 'btn-info' : 'btn-outline-info' ?>">Hoy</a>
                                <a href="<?= $irA($ayer) ?>" class="btn <?= $fecha === $ayer ? 'btn-info' : 'btn-outline-info' ?>">Ayer</a>
                            </div>
                        <?= $this->Form->end() ?>
                    </div>

                    <!-- Resumen -->
                    <div class="d-flex flex-wrap kpis mb-2">
                        <div class="kpi kpi-cantidad">
                            <div class="kpi-label">Comprobantes</div>
                            <div class="kpi-valor"><?= (int)($resumen['cantidad'] ?? 0) ?></div>
                        </div>
                        <div class="kpi kpi-total">
                            <div class="kpi-label">Total vendido</div>
                            <div class="kpi-valor text-success">S/ <?= number_format((float)($resumen['total'] ?? 0), 2) ?></div>
                        </div>
                        <div class="kpi kpi-anulados" title="Los anulados no suman al total">
                            <div class="kpi-label">Anulados</div>
                            <div class="kpi-valor <?= (int)($resumenAnulados['cantidad'] ?? 0) > 0 ? 'text-danger' : 'text-muted' ?>">
                                <?= (int)($resumenAnulados['cantidad'] ?? 0) ?>
                                <small class="font-weight-normal">· S/ <?= number_format((float)($resumenAnulados['total'] ?? 0), 2) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <span><i class="fas fa-info-circle text-info mr-1"></i>
                        <?= $buscandoCliente ? 'Resultados de la búsqueda en' : 'Crédito pendiente muestra' ?> <strong>todas las fechas</strong>.
                    </span>
                    <div class="d-flex kpis">
                        <div class="kpi kpi-cantidad">
                            <div class="kpi-label">Comprobantes</div>
                            <div class="kpi-valor"><?= (int)($resumen['cantidad'] ?? 0) ?></div>
                        </div>
                        <div class="kpi kpi-total">
                            <div class="kpi-label">Total</div>
                            <div class="kpi-valor">S/ <?= number_format((float)($resumen['total'] ?? 0), 2) ?></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
        .filtros-tipo > * { margin: 0 .4rem .4rem 0; }
        .buscador-cliente > * { margin: 0 .5rem .5rem 0; }
    </style>
    <!-- FILTRO POR TIPO -->
    <div class="mb-3 d-flex flex-wrap filtros-tipo">
        <a href="<?= $conFecha() ?>"
        class="btn btn-sm <?= (!$q('tipo') && !$q('credito') && !$q('sin_doctor')) ? 'btn-dark' : 'btn-outline-dark' ?>">
            Todos
        </a>
        <a href="<?= $conFecha(['tipo' => 'RI']) ?>"
        class="btn btn-sm <?= $q('tipo') === 'RI' ? 'btn-dark' : 'btn-outline-dark' ?>">
            Notas de Venta
        </a>
        <a href="<?= $conFecha(['tipo' => '03']) ?>"
        class="btn btn-sm <?= $q('tipo') === '03' ? 'btn-warning' : 'btn-outline-ambar' ?>">
            Boletas
        </a>
        <a href="<?= $conFecha(['tipo' => '01']) ?>"
        class="btn btn-sm <?= $q('tipo') === '01' ? 'btn-primary' : 'btn-outline-primary' ?>">
            Facturas
        </a>
        <a href="<?= $this->Url->build(['action' => 'index', '?' => ['credito' => '1']]) ?>"
        class="btn btn-sm <?= $q('credito') === '1' ? 'btn-danger' : 'btn-outline-danger' ?>">
            <i class="fas fa-clock"></i> Crédito Pendiente
        </a>
        <a href="<?= $conFecha(['sin_doctor' => '1']) ?>"
        class="btn btn-sm <?= $q('sin_doctor') === '1' ? 'btn-secondary' : 'btn-outline-secondary' ?>">
            <i class="fas fa-user-md"></i> Sin Doctor Asignado
        </a>
    </div>

    <!-- BUSCADOR POR CLIENTE -->
    <div class="mb-3">
        <?= $this->Form->create(null, ['type' => 'get', 'class' => 'd-flex flex-wrap buscador-cliente']) ?>
            <div style="max-width: 320px; flex: 1;">
                <?= $this->Form->control('cliente', [
                    'type' => 'text',
                    'label' => false,
                    'value' => $q('cliente'),
                    'placeholder' => 'Buscar por nombre de cliente...',
                    'class' => 'form-control',
                    'templates' => ['inputContainer' => '{{content}}'],
                ]) ?>
            </div>

            <div style="max-width: 220px; flex: 1;">
                <?= $this->Form->control('documento', [
                    'type' => 'text',
                    'label' => false,
                    'value' => $q('documento'),
                    'placeholder' => 'Buscar por DNI/RUC...',
                    'class' => 'form-control',
                    'templates' => ['inputContainer' => '{{content}}'],
                ]) ?>
            </div>

            <?php foreach (array_filter(['tipo' => $q('tipo'), 'credito' => $q('credito'), 'sin_doctor' => $q('sin_doctor')]) as $k => $v): ?>
                <?= $this->Form->hidden($k, ['value' => $v]) ?>
            <?php endforeach; ?>
            <?php if ($filtraPorFecha): ?>
                <?= $this->Form->hidden('fecha', ['value' => $fecha]) ?>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Buscar
            </button>

            <?php if ($q('cliente') || $q('documento')): ?>
                <?php
                $paramsSinCliente = array_filter([
                    'tipo' => $q('tipo'),
                    'credito' => $q('credito'),
                    'sin_doctor' => $q('sin_doctor'),
                ]) + ($filtraPorFecha ? $paramsFecha : []);
                ?>
                <a href="<?= $this->Url->build(['action' => 'index', '?' => $paramsSinCliente]) ?>"
                class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            <?php endif; ?>
        <?= $this->Form->end() ?>
        <?php if ($buscandoCliente): ?>
            <small class="text-muted">Se busca en todas las fechas. Use "Limpiar" para volver a la vista por día.</small>
        <?php endif; ?>
    </div>

    <!-- TABLA DE COMPROBANTES -->
    <div class="card mb-0">
    <div class="tabla-comprobantes-wrap" id="tablaComprobantesWrap">
        <table class="table table-striped mb-0 align-middle tabla-comprobantes">

            <thead class="bg-info text-white">
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Comprobante</th>
                    <th>Cliente</th>
                    <th>Doctor</th>
                    <th>Total</th>
                    <th>Pago</th>
                    <th>Estado</th>
                    <th class="text-center col-acciones">Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($invoices as $inv): ?>
                <?php
                    $esCredito = ($inv->forma_pago ?? 'CONTADO') === 'CREDITO';
                    $saldoPendiente = 0.0;
                    $proximaCuota = null;
                    $cuotaVencidaHoy = false;

                   if ($esCredito && !empty($inv->invoice_cuotas)) {
    $hoy = date('Y-m-d');
    foreach ($inv->invoice_cuotas as $cuota) {
        if ($cuota->estado === 'PENDIENTE') {
            $saldoPendiente += (float) $cuota->monto;
            $fechaCuota = $cuota->fecha_vencimiento_iso; // ← usa el accessor normalizado
            if ($fechaCuota) {
                if ($proximaCuota === null || $fechaCuota < $proximaCuota) {
                    $proximaCuota = $fechaCuota;
                }
                if ($fechaCuota <= $hoy) {
                    $cuotaVencidaHoy = true;
                }
            }
        }
    }
}
                ?>
                <tr class="<?= $cuotaVencidaHoy ? 'table-danger' : '' ?>">

                    <td>
                        <span class="badge bg-secondary">#<?= $inv->id ?></span>
                    </td>

                    <td class="nowrap">
                        <small><?= $inv->created ? $inv->created->format('d/m/Y') : '-' ?><br><?= $inv->created ? $inv->created->format('H:i') : '' ?></small>
                    </td>

                    <td class="nowrap">
                        <?php
                        if ($inv->tipo_doc === '01') {
                            echo '<span class="badge bg-primary">Factura</span>';
                        } elseif ($inv->tipo_doc === '03') {
                            echo '<span class="badge bg-warning text-dark">Boleta</span>';
                        } else {
                            echo '<span class="badge bg-dark">Nota de Venta</span>';
                        }
                        ?>
                        <div><small class="fw-semibold"><?= h(($inv->serie ?: 'RI') . '-' . ($inv->correlativo ?: $inv->id)) ?></small></div>
                    </td>

                    <td class="cliente-nombre">
                        <?= h($inv->cliente_nombre) ?>
                        <?php if ($inv->cliente_numero): ?>
                            <div><small class="text-muted"><?= h($inv->cliente_numero) ?></small></div>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if ($inv->doctor_id && $inv->doctore): ?>
                            <span class="badge bg-success-subtle text-success border border-success">
                                <i class="fas fa-user-md"></i> <?= h(trim($inv->doctore->nombre . ' ' . $inv->doctore->apellido)) ?>
                            </span>
                        <?php else: ?>
                            <span class="badge bg-light text-muted border">Sin asignar</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <strong>S/ <?= number_format((float)$inv->total, 2) ?></strong>
                    </td>

                    <td>
                        <?php if ($esCredito): ?>
                            <span class="badge bg-info text-dark">
                                <i class="fas fa-hourglass-half"></i> CRÉDITO
                            </span>
                            <?php if ($saldoPendiente > 0): ?>
                                <div class="mt-1">
                                    <span class="badge <?= $cuotaVencidaHoy ? 'bg-danger' : 'bg-secondary' ?>">
                                        Debe: S/ <?= number_format($saldoPendiente, 2) ?>
                                    </span>
                                    <?php if ($proximaCuota): ?>
                                        <br>
                                        <small class="<?= $cuotaVencidaHoy ? 'text-danger fw-bold' : 'text-muted' ?>">
                                            Vence: <?= (new \DateTime($proximaCuota))->format('d/m/Y') ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="mt-1">
                                    <span class="badge bg-success">Cancelado</span>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle"></i> CONTADO
                            </span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php
                        $color = 'secondary';
                        if ($inv->estado === 'ACEPTADO') $color = 'success';
                        elseif ($inv->estado === 'RECHAZADO') $color = 'danger';
                        elseif ($inv->estado === 'PENDIENTE_RESUMEN') $color = 'warning';
                        elseif ($inv->estado === 'EN_RESUMEN') $color = 'info';
                        elseif ($inv->estado === 'RECIBO_INTERNO') $color = 'dark';
                        ?>
                        <span class="badge bg-<?= $color ?>">
                            <?= h($inv->estado) ?>
                        </span>
                    </td>

                    <td class="text-center col-acciones">
                        <div class="acciones-wrap">

                        <a href="<?= $this->Url->build(['action' => 'view', $inv->id]) ?>"
                           class="btn btn-info btn-sm" title="Ver">
                           <i class="fas fa-eye"></i>
                        </a>

                        <button type="button"
                                class="btn btn-sm <?= $inv->doctor_id ? 'btn-outline-success' : 'btn-outline-secondary' ?>"
                                title="<?= ($inv->doctor_id && $inv->doctore) ? 'Doctor: ' . h(trim($inv->doctore->nombre . ' ' . $inv->doctore->apellido)) : 'Asignar doctor' ?>"
                                data-toggle="modal"
                                data-target="#modalAsignarDoctor<?= $inv->id ?>">
                           <i class="fas fa-user-md"></i>
                        </button>

                        <?php if ($esCredito && $saldoPendiente > 0): ?>
                            <a href="<?= $this->Url->build(['action' => 'view', $inv->id]) ?>#cuotas"
                               class="btn btn-warning btn-sm"
                               title="Ir a cobrar cuota pendiente">
                               <i class="fas fa-hand-holding-usd"></i> Cobrar
                            </a>
                        <?php endif; ?>

                        <?php if ($inv->xml_path): ?>
                            <a href="<?= $this->Url->build('/' . ltrim($inv->xml_path, '/')) ?>" target="_blank"
                               class="btn btn-secondary btn-sm" title="Descargar XML">
                               <i class="fas fa-code"></i> XML
                            </a>
                        <?php endif; ?>

                        <?php if ($inv->cdr_path): ?>
                            <a href="<?= $this->Url->build('/' . ltrim($inv->cdr_path, '/')) ?>" target="_blank"
                               class="btn btn-secondary btn-sm" title="Descargar CDR">
                               <i class="fas fa-file"></i> CDR
                            </a>
                        <?php endif; ?>

                        <a href="<?= $this->Url->build(['action' => 'pdf', $inv->id]) ?>"
                           target="_blank"
                           class="btn btn-danger btn-sm" title="PDF">
                           <i class="fas fa-file-pdf"></i>
                        </a>

                        <?php if ($inv->tipo_doc === 'RI' && $inv->estado === 'RECIBO_INTERNO'): ?>
                            <a href="<?= $this->Url->build(['action' => 'transformarABoleta', $inv->id]) ?>"
                            class="btn btn-warning btn-sm openModal" title="Convertir a Boleta">
                                <i class="fas fa-exchange-alt"></i>
                            </a>
                            <a href="<?= $this->Url->build(['action' => 'anularReciboInterno', $inv->id]) ?>"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('¿Anular Nota de Venta #<?= $inv->correlativo ?>?\n\n⚠️ Recuerda registrar el EGRESO en Caja.')">
                                <i class="fas fa-trash"></i>
                            </a>
                        <?php endif; ?>

                        <?php if ($inv->tipo_doc === '03' && $inv->estado === 'PENDIENTE_RESUMEN'): ?>
                            <a href="<?= $this->Url->build(['action' => 'anularBoletaPendiente', $inv->id]) ?>"
                            class="btn btn-danger btn-sm" title="Anular (aún no enviada a SUNAT)"
                            onclick="return confirm('¿Anular esta boleta antes de enviarla a SUNAT?\n\nTodavía no fue enviada al Resumen Diario.\nSe registrará el reembolso en Caja.')">
                                <i class="fas fa-trash"></i>
                            </a>
                        <?php endif; ?>

                        </div>
                    </td>

                </tr>

                <!-- MODAL: Asignar Doctor -->
                <div class="modal fade" id="modalAsignarDoctor<?= $inv->id ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <?= $this->Form->create(null, [
                                'url' => ['action' => 'asignarDoctor', $inv->id],
                            ]) ?>
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-user-md"></i> Asignar Doctor — Comprobante #<?= $inv->id ?>
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <label class="form-label fw-semibold">Doctor Responsable</label>
                                <select name="doctor_id" class="form-select">
                                    <option value="">-- Sin doctor asignado --</option>
                                    <?php foreach ($doctores as $docId => $docLabel): ?>
                                        <option value="<?= $docId ?>" <?= (int) $inv->doctor_id === (int) $docId ? 'selected' : '' ?>>
                                            <?= h($docLabel) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </div>
                            <?= $this->Form->end() ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>
    <div class="scroll-x-flotante" id="scrollXFlotante"><div></div></div>
    </div>

    <script>
    (function () {
        const wrap = document.getElementById('tablaComprobantesWrap');
        const barra = document.getElementById('scrollXFlotante');
        if (!wrap || !barra) return;
        const relleno = barra.firstElementChild;
        let sincronizando = false;

        function ajustar() {
            relleno.style.width = wrap.scrollWidth + 'px';
            // Sin desborde horizontal no hace falta la barra
            barra.style.display = wrap.scrollWidth > wrap.clientWidth ? 'block' : 'none';
        }
        function sincronizar(origen, destino) {
            if (sincronizando) return;
            sincronizando = true;
            destino.scrollLeft = origen.scrollLeft;
            requestAnimationFrame(() => { sincronizando = false; });
        }

        barra.addEventListener('scroll', () => sincronizar(barra, wrap));
        wrap.addEventListener('scroll', () => sincronizar(wrap, barra));
        window.addEventListener('resize', ajustar);
        window.addEventListener('load', ajustar);
        ajustar();
    })();
    </script>

    <p class="text-muted small mt-2 mb-0">
        Mostrando <?= count($invoices) ?> registro(s)<?= $filtraPorFecha ? ' del día seleccionado' : '' ?>.
    </p>
</div>