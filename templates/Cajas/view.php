<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

        <h3 class="fw-bold mb-0">
            <i class="fas fa-cash-register me-1"></i>
            Detalle de Caja
        </h3>

        <div class="d-flex gap-2 flex-wrap">
            <?php if ($caja->estado === 'CERRADA'): ?>
                <a href="<?= $this->Url->build(['action' => 'pdf', $caja->id]) ?>"
                   class="btn btn-success"
                   target="_blank">
                    <i class="fas fa-file-pdf me-1"></i>
                    Exportar PDF
                </a>
            <?php endif; ?>

            <a href="<?= $this->Url->build(['action' => 'index']) ?>"
               class="btn btn-outline-secondary">

                <i class="fas fa-arrow-left me-1"></i>

                Volver

            </a>
        </div>

    </div>

    <!-- INFO -->
    <div class="card shadow-sm mb-4">

        <div class="card-body row">

            <div class="col-md-4">

                <div class="text-muted small">
                    Caja
                </div>

                <div class="fw-semibold">
                    <?= h($caja->nombre) ?>
                </div>

                <div class="text-muted small mt-2">
                    Código
                </div>

                <div class="fw-semibold">
                    <?= h($caja->codigo) ?>
                </div>

            </div>

            <div class="col-md-4">

                <div class="text-muted small">
                    Usuario
                </div>

                <div class="fw-semibold">

                    <?= h(
                        $caja->user->name
                        ?? $caja->user->username
                        ?? 'N/A'
                    ) ?>

                </div>

                <div class="text-muted small mt-2">
                    Fecha
                </div>

                <div class="fw-semibold">

                    <?= h(
                        $caja->fecha?->format('Y-m-d')
                    ) ?>

                </div>

            </div>

            <div class="col-md-4 text-end">

                <?php

                $color = 'secondary';

                if ($caja->estado === 'ABIERTA') {
                    $color = 'success';
                }

                if ($caja->estado === 'PAUSADA') {
                    $color = 'warning';
                }

                if ($caja->estado === 'CERRADA') {
                    $color = 'dark';
                }

                ?>

                <span class="badge bg-<?= $color ?> px-3 py-2">

                    <?= h($caja->estado) ?>

                </span>

            </div>

        </div>

    </div>

    <!-- RESUMEN -->
    <div class="row mb-4">

        <div class="col-md-3">

            <div class="card text-center shadow-sm">

                <div class="card-body">

                    <div class="text-muted">
                        <i class="fas fa-money-bill-wave me-1"></i>
                        Efectivo
                    </div>

                    <h5 class="text-success fw-bold">

                        S/ <?= number_format($totalEfectivoIngresos, 2) ?>

                    </h5>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card text-center shadow-sm">

                <div class="card-body">

                    <div class="text-muted">
                        <i class="fas fa-mobile-alt me-1"></i>
                        Yape
                    </div>

                    <h5 class="fw-bold" style="color:#6f42c1;">

                        S/ <?= number_format($totalYapeIngresos, 2) ?>

                    </h5>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card text-center shadow-sm">

                <div class="card-body">

                    <div class="text-muted">
                        <i class="fas fa-credit-card me-1"></i>
                        Tarjeta
                    </div>

                    <h5 class="text-info fw-bold">

                        S/ <?= number_format($totalTarjetaIngresos, 2) ?>

                    </h5>

                </div>

            </div>

        </div>
        <div class="col-md-3">

    <div class="card text-center shadow-sm">

        <div class="card-body">

            <div class="text-muted">
                <i class="fas fa-comment-dollar me-1"></i>
                Plin
            </div>

            <h5 class="fw-bold" style="color:#e83e8c;">

                S/ <?= number_format($totalPlinIngresos, 2) ?>

            </h5>

        </div>

    </div>

</div>

<div class="col-md-3">

    <div class="card text-center shadow-sm">

        <div class="card-body">

            <div class="text-muted">
                <i class="fas fa-university me-1"></i>
                Transferencia
            </div>

            <h5 class="fw-bold text-primary">

                S/ <?= number_format($totalTransferenciaIngresos, 2) ?>

            </h5>

        </div>

    </div>

</div>

<div class="col-md-3">

    <div class="card text-center shadow-sm">

        <div class="card-body">

            <div class="text-muted">
                <i class="fas fa-wallet me-1"></i>
                Otros
            </div>

            <h5 class="fw-bold text-secondary">

                S/ <?= number_format($totalOtrosIngresos, 2) ?>

            </h5>

        </div>

    </div>

</div>

        <div class="col-md-3">

            <div class="card text-center shadow-sm border-success">

                <div class="card-body">

                    <div class="text-muted">
                        <i class="fas fa-calculator me-1"></i>
                        Ingresos de hoy
                    </div>

                    <h4 class="text-success fw-bold">

                        S/ <?= number_format($totalGeneralIngresos, 2) ?>

                    </h4>

                </div>

            </div>

        </div>

    </div>
    <div class="card shadow-sm mb-4">

        <div class="card-header fw-bold bg-light">

            💰 Ingresos Externos

        </div>

        <div class="card-body">

            <h4 class="text-warning fw-bold mb-4">

                S/
                <?= number_format(
                    $totalIngresosExternos,
                    2
                ) ?>

            </h4>

            <div class="table-responsive">

                <table class="table table-bordered">

                    <thead>

                        <tr>

                            <th>Fecha</th>
                            <th>Descripción</th>
                            <th>Método</th>    
                            <th class="text-end">Monto</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($ingresosExternos as $i): ?>

                        <tr>

                            <td>

                                <?= h(
                                    $i->created?->format(
                                        'Y-m-d H:i'
                                    )
                                ) ?>

                            </td>

                            <td>

                                <?= h($i->descripcion) ?>

                            </td>
                            
                            <td>
        <?php
        $mc = match(strtoupper($i->metodo_pago ?? 'EFECTIVO')) {
            'YAPE'          => 'warning',
            'TARJETA'       => 'info',
            'TRANSFERENCIA' => 'primary',
            'PLIN'          => 'danger',
            'OTROS'         => 'secondary',
            default         => 'success',
        };
        ?>
        <span class="badge bg-<?= $mc ?>">
            <?= h(strtoupper($i->metodo_pago ?? 'EFECTIVO')) ?>
        </span>
    </td>

                            <td class="text-end fw-bold">

                                S/
                                <?= number_format(
                                    (float)$i->monto,
                                    2
                                ) ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    <?php if ($ingresosExternos->isEmpty()): ?>

                        <tr>

                            <td colspan="3"
                                class="text-center text-muted">

                                No hay ingresos externos

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <div class="card shadow-sm mb-4">

    <div class="card-header fw-bold bg-light text-danger">

        💸 Egresos Externos

    </div>

    <div class="card-body">

        <h4 class="text-danger fw-bold mb-4">

            S/
            <?= number_format(
                $totalEgresosExternos,
                2
            ) ?>

        </h4>

        <div class="table-responsive">

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>Fecha</th>
                        <th>Descripción</th>
                        <th>Método</th>

                        <th class="text-end">Monto</th>

                    </tr>

                </thead>

                <tbody>

                <?php foreach ($egresosExternos as $e): ?>

                    <tr>

                        <td>

                            <?= h(
                                $e->created?->format(
                                    'Y-m-d H:i'
                                )
                            ) ?>

                        </td>

                        <td>

                            <?= h($e->descripcion) ?>

                        </td>
                        <td>
            <?php
            $mc = match(strtoupper($e->metodo_pago ?? 'EFECTIVO')) {
                'YAPE'          => 'warning',
                'TARJETA'       => 'info',
                'TRANSFERENCIA' => 'primary',
                'PLIN'          => 'danger',
                'OTROS'         => 'secondary',
                default         => 'success',
            };
            ?>
            <span class="badge bg-<?= $mc ?>">
                <?= h(strtoupper($e->metodo_pago ?? 'EFECTIVO')) ?>
            </span>
        </td>

                        <td class="text-end fw-bold text-danger">

                            S/
                            <?= number_format(
                                (float)$e->monto,
                                2
                            ) ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

                <?php if ($egresosExternos->isEmpty()): ?>

                    <tr>

                        <td colspan="4"
                            class="text-center text-muted">

                            No hay egresos externos

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

    <div class="card shadow-sm mb-4">

    <div class="card-header fw-bold bg-light text-warning">

        ↩️ Reembolsos por Anulación

    </div>

    <div class="card-body">

        <h4 class="text-warning fw-bold mb-4">

            S/
            <?= number_format(
                $totalReembolsosAnulacion,
                2
            ) ?>

        </h4>

        <div class="table-responsive">

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>Fecha</th>
                        <th>Descripción</th>
                        <th>Método</th>

                        <th class="text-end">Monto</th>

                    </tr>

                </thead>

                <tbody>

                <?php foreach ($reembolsosAnulacion as $r): ?>

                    <tr>

                        <td>

                            <?= h(
                                $r->created?->format(
                                    'Y-m-d H:i'
                                )
                            ) ?>

                        </td>

                        <td>

                            <?= h($r->descripcion) ?>

                        </td>
                        <td>
            <?php
            $mc = match(strtoupper($r->metodo_pago ?? 'EFECTIVO')) {
                'YAPE'          => 'warning',
                'TARJETA'       => 'info',
                'TRANSFERENCIA' => 'primary',
                'PLIN'          => 'danger',
                'OTROS'         => 'secondary',
                default         => 'success',
            };
            ?>
            <span class="badge bg-<?= $mc ?>">
                <?= h(strtoupper($r->metodo_pago ?? 'EFECTIVO')) ?>
            </span>
        </td>

                        <td class="text-end fw-bold text-warning">

                            S/
                            <?= number_format(
                                (float)$r->monto,
                                2
                            ) ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

                <?php if ($reembolsosAnulacion->isEmpty()): ?>

                    <tr>

                        <td colspan="4"
                            class="text-center text-muted">

                            No hay reembolsos por anulación

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

    <div class="card shadow-sm mb-4">

    <div class="card-header fw-bold bg-light text-primary">

        🧪 Distribución del Cobro (Laboratorio / Materiales)

    </div>

    <div class="card-body">

        <div class="row mb-3">

            <div class="col-md-6">
                <div class="text-muted small">Laboratorio</div>
                <h4 class="text-primary fw-bold">
                    S/ <?= number_format($totalDistribuidoLaboratorio, 2) ?>
                </h4>
            </div>

            <div class="col-md-6">
                <div class="text-muted small">Materiales</div>
                <h4 class="text-secondary fw-bold">
                    S/ <?= number_format($totalDistribuidoMateriales, 2) ?>
                </h4>
            </div>

        </div>

        <div class="table-responsive">

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>Fecha</th>
                        <th>Comprobante</th>
                        <th>Tipo</th>
                        <th>Laboratorio</th>

                        <th class="text-end">Monto</th>

                    </tr>

                </thead>

                <tbody>

                <?php foreach ($distribucionesCaja as $dist): ?>

                    <tr>

                        <td>
                            <?= h($dist->created?->format('Y-m-d H:i')) ?>
                        </td>

                        <td>
                            <?= $this->Html->link(
                                h(($dist->invoice->serie ?? '') . '-' . ($dist->invoice->correlativo ?? '')),
                                ['controller' => 'Invoices', 'action' => 'view', $dist->invoice_id]
                            ) ?>
                        </td>

                        <td>
                            <span class="badge bg-<?= $dist->tipo === 'LABORATORIO' ? 'warning text-dark' : 'secondary' ?>">
                                <?= h($dist->tipo) ?>
                            </span>
                        </td>

                        <td><?= h($dist->laboratorio->nombre ?? '-') ?></td>

                        <td class="text-end fw-bold text-primary">
                            S/ <?= number_format((float) $dist->monto, 2) ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

                <?php if ($distribucionesCaja->isEmpty()): ?>

                    <tr>

                        <td colspan="5" class="text-center text-muted">
                            No hay distribuciones registradas en esta caja
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

    <!-- MONTO INICIAL -->
    <div class="card shadow-sm mb-4">

        <div class="card-header bg-light fw-bold">

            💵 Apertura de Caja

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">

                    <div class="text-muted small">
                        Monto inicial registrado
                    </div>

                    <h3 class="text-success fw-bold">

                        S/
                        <?= number_format(
                            (float)$caja->monto_inicial,
                            2
                        ) ?>

                    </h3>

                </div>

                <div class="col-md-4">

                    <div class="text-muted small">
                        Efectivo esperado actual
                    </div>

                    <h3 class="text-primary fw-bold">

                        S/
                        <?= number_format(
                            (float)$efectivoEsperadoEnCaja,
                            2
                        ) ?>

                    </h3>

                </div>

                <?php if ($caja->estado === 'CERRADA'): ?>

                <div class="col-md-4">

                    <div class="text-muted small">
                        Monto cierre
                    </div>

                    <h3 class="text-dark fw-bold">

                        S/
                        <?= number_format(
                            (float)$caja->monto_cierre,
                            2
                        ) ?>

                    </h3>

                </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

    <!-- BOTONES -->
    <div class="d-flex flex-wrap gap-2 justify-content-end mb-3">
        <?php if ($caja->estado === 'ABIERTA'): ?>

    <a href="<?= $this->Url->build([
        'action' => 'agregarIngreso',
        $caja->id
    ]) ?>"
       class="btn btn-warning">

        <i class="fas fa-hand-holding-usd me-1"></i>

        Ingreso Externo

    </a>

<?php endif; ?>
<?php if ($caja->estado === 'ABIERTA'): ?>

    <a href="<?= $this->Url->build([
        'action' => 'agregarEgreso',
        $caja->id
    ]) ?>"
       class="btn btn-danger">

        <i class="fas fa-money-bill-wave me-1"></i>

        Egreso

    </a>

<?php endif; ?>

        <?php if ($caja->estado === 'ABIERTA'): ?>

            <a href="<?= $this->Url->build([
                'controller' => 'Invoices',
                'action' => 'add',
                '?' => ['caja_id' => $caja->id]
            ]) ?>"
               class="btn btn-primary">

                <i class="fas fa-plus-circle me-1"></i>

                Crear Comprobante

            </a>

        <?php endif; ?>

        <?php if ($caja->estado === 'PAUSADA'): ?>

            <?= $this->Form->create(null, [
                'url' => ['action' => 'reanudar', $caja->id],
                'style' => 'display:inline;'
            ]) ?>

            <button class="btn btn-success">

                <i class="fas fa-play me-1"></i>

                Reanudar

            </button>

            <?= $this->Form->end() ?>

        <?php endif; ?>

        <?php if ($caja->estado !== 'CERRADA'): ?>

        <a href="<?= $this->Url->build([
            'action' => 'cerrar',
            $caja->id
        ]) ?>"
        class="btn btn-dark">

            <i class="fas fa-lock me-1"></i>

            Cerrar Caja

        </a>

        <?php endif; ?>

    </div>

    <!-- MOVIMIENTOS -->
    <div class="card shadow-sm">

        <div class="card-body">

            <h5 class="mb-3">

                <i class="fas fa-receipt me-1"></i>

                Movimientos

            </h5>

            <div class="table-responsive">

                <table class="table table-striped table-hover align-middle mb-0">

                    <thead class="bg-info text-white">

                        <tr>

                            <th>#</th>
                            <th>Comprobante</th>
                            <th>Tipo</th>
                            <th>Método</th>
                            <th class="text-end">Monto</th>
                            <th class="text-center">Acciones</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php

                    $invoicesGrouped = [];

                    foreach ($caja->caja_movimientos as $mov) {

                        $inv = $mov->invoice ?? null;

                        if (!$inv || empty($inv->id)) {
                            continue;
                        }

                        $iid = (int)$inv->id;

                        if (!isset($invoicesGrouped[$iid])) {

                            $invoicesGrouped[$iid] = [
                                'invoice' => $inv,
                                'movimientos' => [],
                            ];
                        }

                        $invoicesGrouped[$iid]['movimientos'][] = $mov;
                    }

                    ?>
                    

                    <?php foreach ($invoicesGrouped as $group): ?>

                        <?php

                        $inv = $group['invoice'];

                        $movs = $group['movimientos'];

                        $metodos = [];

                        foreach ($movs as $m) {

                            $metodos[] = strtoupper(
                                $m->metodo_pago
                            );
                        }

                        $metodos = array_unique($metodos);

                        $methodsLabel = implode(
                            ' + ',
                            $metodos
                        );

                        $color = 'secondary';

                        if (count($metodos) > 1) {
                            $color = 'dark';
                        } elseif (in_array('EFECTIVO', $metodos)) {
                            $color = 'success';
                        } elseif (in_array('YAPE', $metodos)) {
                            $color = 'warning';
                        } elseif (in_array('TARJETA', $metodos)) {
                            $color = 'info';
                        } elseif (in_array('TRANSFERENCIA', $metodos)) {
                            $color = 'primary';
                        } elseif (in_array('PLIN', $metodos)) {
                            $color = 'danger';
                        } elseif (in_array('OTROS', $metodos)) {
                            $color = 'secondary';
                        }

                        $tipoLabel = $inv->tipo_doc === '01'
                            ? 'Factura'
                            : ($inv->tipo_doc === '03' ? 'Boleta' : 'Recibo Interno');

                        $esAnulado = $inv->estado === 'ANULADO';

                        ?>

                        <tr class="<?= $esAnulado ? 'table-danger' : '' ?>">

                            <td>
                                #<?= h($inv->id) ?>
                            </td>

                            <td>

                                <?= h(
                                    $inv->serie ?? 'RI'
                                ) ?>

                                <?= h(
                                    $inv->correlativo
                                    ?? $inv->id
                                ) ?>

                                <br>

                                <?php

                                $statusColor = 'secondary';

                                if ($inv->estado === 'ACEPTADO') {
                                    $statusColor = 'success';
                                }

                                if ($inv->estado === 'RECHAZADO') {
                                    $statusColor = 'danger';
                                }

                                if ($inv->estado === 'RECIBO_INTERNO') {
                                    $statusColor = 'dark';
                                }

                                if ($esAnulado) {
                                    $statusColor = 'danger';
                                }

                                ?>

                                <span class="badge bg-<?= $statusColor ?>">

                                    <?= $esAnulado ? '🚫 ANULADO' : h($inv->estado) ?>

                                </span>

                            </td>

                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?= h($tipoLabel) ?>
                                </span>
                            </td>

                            <td>

                                <span class="badge bg-<?= $color ?>">

                                    <?= h($methodsLabel) ?>

                                </span>

                            </td>

                            <td class="text-end">

                                <span class="<?= $esAnulado ? 'text-danger text-decoration-line-through' : '' ?>">
                                    S/
                                    <?= number_format(
                                        (float)($inv->total ?? 0),
                                        2
                                    ) ?>
                                </span>

                                <?php if ($esAnulado): ?>
                                    <br><small class="text-danger">No suma al total</small>
                                <?php endif; ?>

                            </td>

                            <td class="text-center">

                                <a href="<?= $this->Url->build([
                                    'controller' => 'Invoices',
                                    'action' => 'view',
                                    $inv->id
                                ]) ?>"
                                   class="btn btn-info btn-sm">

                                    <i class="fas fa-eye"></i>

                                </a>

                                <a href="<?= $this->Url->build([
                                    'controller' => 'Invoices',
                                    'action' => 'pdf',
                                    $inv->id
                                ]) ?>"
                                   target="_blank"
                                   class="btn btn-danger btn-sm">

                                    <i class="fas fa-file-pdf"></i>

                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    <?php if (empty($caja->caja_movimientos)): ?>

                        <tr>

                            <td colspan="6"
                                class="text-center text-muted">

                                No hay movimientos en esta caja

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<style>

.container-fluid.py-4 .card {
    border: none;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
}

.container-fluid.py-4 .badge {
    border-radius: 999px;
    padding: .45rem .75rem;
    font-weight: 700;
}

.container-fluid.py-4 .btn {
    font-weight: 600;
}

.container-fluid.py-4 .table tbody tr:hover {
    background-color: #f8f9fa;
}

</style>