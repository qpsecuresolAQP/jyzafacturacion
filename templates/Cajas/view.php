<?php
// Ingresos/egresos externos: editables con la caja abierta o pausada; cerrada, solo el administrador.
$puedeEditarExternos = in_array($caja->estado, ['ABIERTA', 'PAUSADA'], true) || $this->Permisos->tieneRol([1]);
?>
<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

        <h3 class="fw-bold mb-0">
            <i class="fas fa-cash-register me-1"></i>
            Detalle de Caja
        </h3>

        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= $this->Url->build(['action' => 'pdf', $caja->id]) ?>"
               class="btn btn-success"
               target="_blank"
               <?= $caja->estado !== 'CERRADA' ? 'title="Reporte parcial: la caja aún no está cerrada"' : '' ?>>
                <i class="fas fa-file-pdf me-1"></i>
                Exportar PDF<?= $caja->estado !== 'CERRADA' ? ' (parcial)' : '' ?>
            </a>

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

    <!-- RESUMEN POR MÉTODO DE PAGO -->
    <?php
    $estiloMetodo = [
        'EFECTIVO' => ['fa-money-bill-wave', '#28a745'],
        'YAPE' => ['fa-mobile-alt', '#6f42c1'],
        'TARJETA' => ['fa-credit-card', '#17a2b8'],
        'TRANSFERENCIA' => ['fa-university', '#007bff'],
        'PLIN' => ['fa-comment-dollar', '#e83e8c'],
        'OTROS' => ['fa-wallet', '#6c757d'],
    ];
    $totIngresos = array_sum(array_column($resumenMetodos, 'ingresos'));
    $totEgresos = array_sum(array_column($resumenMetodos, 'egresos'));
    $totSaldo = array_sum(array_column($resumenMetodos, 'saldo'));
    ?>

    <!-- SALDO POR MÉTODO (tarjetas superiores) -->
    <div class="row mb-4">
        <?php foreach ($resumenMetodos as $clave => $m): ?>
            <?php [$icono, $color] = $estiloMetodo[$clave]; ?>
            <div class="col-md-3 mb-3">
                <div class="card text-center shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted">
                            <i class="fas <?= $icono ?> me-1"></i>
                            <?= h($m['nombre']) ?>
                        </div>
                        <h5 class="fw-bold mb-0" style="color: <?= $m['saldo'] < 0 ? '#dc3545' : $color ?>;">
                            S/ <?= number_format($m['saldo'], 2) ?>
                        </h5>
                        <?php if ($m['egresos'] > 0): ?>
                            <small class="text-muted">
                                Ingresó S/ <?= number_format($m['ingresos'], 2) ?> · Salió S/ <?= number_format($m['egresos'], 2) ?>
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm border-success h-100">
                <div class="card-body">
                    <div class="text-muted">
                        <i class="fas fa-calculator me-1"></i>
                        Saldo total
                    </div>
                    <h4 class="text-success fw-bold mb-0">
                        S/ <?= number_format($totSaldo, 2) ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <style>
        .resumen-caja .kpi-caja { border-radius: .5rem; padding: .9rem 1rem; height: 100%; border: 1px solid #e9ecef; background: #fff; }
        .resumen-caja .kpi-caja .kpi-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .03em; color: #6c757d; }
        .resumen-caja .kpi-caja .kpi-valor { font-size: 1.45rem; font-weight: 700; line-height: 1.2; white-space: nowrap; }
        .resumen-caja .kpi-ingresos { border-left: 4px solid #28a745; }
        .resumen-caja .kpi-egresos { border-left: 4px solid #dc3545; }
        .resumen-caja .kpi-saldo { border-left: 4px solid #007bff; background: #f4f9ff; }
        .resumen-caja .metodo-icono { width: 34px; height: 34px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: #fff; flex-shrink: 0; }
        .resumen-caja .tabla-metodos td, .resumen-caja .tabla-metodos th { vertical-align: middle; }
        .resumen-caja .tabla-metodos th { font-size: .75rem; text-transform: uppercase; letter-spacing: .03em; color: #6c757d; border-top: 0; }
        .resumen-caja .barra-uso { height: 4px; background: #e9ecef; border-radius: 2px; overflow: hidden; margin-top: 4px; max-width: 160px; }
        .resumen-caja .barra-uso > div { height: 100%; background: #dc3545; }
        .resumen-caja .saldo-metodo { font-size: 1.05rem; }
        .resumen-caja details summary { cursor: pointer; }
    </style>

    <div class="card shadow-sm mb-4 resumen-caja">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="font-weight-bold"><i class="fas fa-calculator mr-1 text-primary"></i> Resumen por método de pago</span>
            <small class="text-muted">Ingresos − Egresos = Saldo</small>
        </div>

        <div class="card-body">
            <!-- Totales -->
            <div class="row mb-3">
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="kpi-caja kpi-ingresos">
                        <div class="kpi-label"><i class="fas fa-arrow-down text-success mr-1"></i> Ingresos</div>
                        <div class="kpi-valor text-success">S/ <?= number_format($totIngresos, 2) ?></div>
                    </div>
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="kpi-caja kpi-egresos">
                        <div class="kpi-label"><i class="fas fa-arrow-up text-danger mr-1"></i> Egresos</div>
                        <div class="kpi-valor text-danger">S/ <?= number_format($totEgresos, 2) ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="kpi-caja kpi-saldo">
                        <div class="kpi-label"><i class="fas fa-wallet text-primary mr-1"></i> Saldo</div>
                        <div class="kpi-valor text-primary">S/ <?= number_format($totSaldo, 2) ?></div>
                    </div>
                </div>
            </div>

            <!-- Detalle por método -->
            <div class="table-responsive">
                <table class="table tabla-metodos mb-0">
                    <thead>
                        <tr>
                            <th>Método</th>
                            <th class="text-right">Ingresos</th>
                            <th class="text-right">Egresos</th>
                            <th class="text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resumenMetodos as $clave => $m): ?>
                            <?php if ($clave !== 'EFECTIVO' && $m['ingresos'] == 0 && $m['egresos'] == 0) continue; ?>
                            <?php
                            [$icono, $color] = $estiloMetodo[$clave];
                            // Hacia abajo: 99.8% no debe mostrarse como 100% si aún queda saldo.
                            $porcUso = $m['ingresos'] > 0 ? (int) min(100, floor($m['egresos'] / $m['ingresos'] * 100)) : ($m['egresos'] > 0 ? 100 : 0);
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="metodo-icono mr-2" style="background: <?= $color ?>;"><i class="fas <?= $icono ?>"></i></span>
                                        <div>
                                            <div class="font-weight-bold"><?= h($m['nombre']) ?></div>
                                            <?php if ($m['egresos'] > 0): ?>
                                                <div class="barra-uso" title="<?= $porcUso ?>% de lo ingresado salió como egreso"><div style="width: <?= $porcUso ?>%;"></div></div>
                                                <small class="text-muted"><?= $porcUso ?>% usado en egresos</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right">S/ <?= number_format($m['ingresos'], 2) ?></td>
                                <td class="text-right <?= $m['egresos'] > 0 ? 'text-danger' : 'text-muted' ?>">
                                    <?= $m['egresos'] > 0 ? '− S/ ' . number_format($m['egresos'], 2) : '—' ?>
                                </td>
                                <td class="text-right font-weight-bold saldo-metodo <?= $m['saldo'] < 0 ? 'text-danger' : '' ?>">
                                    S/ <?= number_format($m['saldo'], 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <details class="mt-3 small text-muted">
                <summary><i class="fas fa-info-circle mr-1"></i>¿Qué incluye cada columna?</summary>
                <div class="mt-2 pl-3">
                    <div><strong>Ingresos:</strong> cobros de comprobantes (sin anulados) + ingresos externos.</div>
                    <div><strong>Egresos:</strong> gastos registrados en caja.</div>
                    <div><strong>Saldo:</strong> lo que queda en cada método (en efectivo, sin contar el monto de apertura).</div>
                    <div>Los reembolsos por anulación no se restan: la venta anulada ya no se cuenta en ingresos.</div>
                </div>
            </details>
        </div>
    </div>

    <div class="card shadow-sm mb-4">

        <div class="card-header fw-bold bg-light">

            💰 Ingresos Externos

        </div>

        <div class="card-body">

            <h4 class="fw-bold mb-4" style="color:#92400e;">

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
                            <th class="text-center">Acciones</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($ingresosExternos as $i): ?>

                        <tr>

                            <td>

                                <?php if ($i->created): ?>
                                    <?= h($i->created->format('Y-m-d H:i')) ?>
                                <?php else: ?>
                                    <?php // Registros antiguos, guardados sin fecha: se muestra el día de la caja. ?>
                                    <?= h($caja->created?->format('Y-m-d')) ?>
                                    <br><small class="text-muted fst-italic">hora no registrada</small>
                                <?php endif; ?>

                            </td>

                            <td>

                                <?= h($i->descripcion) ?>
                                <?php if ($i->modified && (!$i->created || $i->modified->format('Y-m-d H:i:s') !== $i->created->format('Y-m-d H:i:s'))): ?>
                                    <br><small class="text-muted fst-italic">Editado <?= h($i->modified->format('Y-m-d H:i')) ?></small>
                                <?php endif; ?>

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

                            <td class="text-center text-nowrap">
                                <?php if ($puedeEditarExternos): ?>
                                    <?= $this->Html->link(
                                        '<i class="fas fa-edit"></i>',
                                        ['action' => 'editarIngreso', $i->id],
                                        ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm']
                                    ) ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                    <?php if ($ingresosExternos->isEmpty()): ?>

                        <tr>

                            <td colspan="5"
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
                        <th class="text-center">Acciones</th>

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
                                <?php if ($e->modified && $e->created && $e->modified->format('Y-m-d H:i:s') !== $e->created->format('Y-m-d H:i:s')): ?>
                                    <br><small class="text-muted fst-italic">Editado <?= h($e->modified->format('Y-m-d H:i')) ?></small>
                                <?php endif; ?>

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

                            <td class="text-center text-nowrap">
                                <?php if ($puedeEditarExternos): ?>
                                    <?= $this->Html->link(
                                        '<i class="fas fa-edit"></i>',
                                        ['action' => 'editarEgreso', $e->id],
                                        ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm']
                                    ) ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>

                    </tr>

                <?php endforeach; ?>

                <?php if ($egresosExternos->isEmpty()): ?>

                    <tr>

                        <td colspan="5"
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

    <div class="card-header fw-bold bg-light" style="color:#92400e;">

        ↩️ Reembolsos por Anulación

    </div>

    <div class="card-body">

        <h4 class="fw-bold mb-4" style="color:#92400e;">

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

                        <td class="text-end fw-bold" style="color:#92400e;">

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

        <?php if (!empty($puedeReabrir)): ?>

            <?= $this->Form->postLink(
                '<i class="fas fa-lock-open me-1"></i> Reabrir Caja',
                ['action' => 'reabrir', $caja->id],
                [
                    'escape' => false,
                    'class' => 'btn btn-warning',
                    'title' => 'Para registrar una venta más hoy. Se borrará el conteo de cierre y habrá que cerrarla de nuevo.',
                    'confirm' => '¿Reabrir esta caja?' . "\n\n" . 'Se borrará el conteo del cierre (monto y billetes). Al terminar deberá cerrarla de nuevo.',
                ]
            ) ?>

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
                            : ($inv->tipo_doc === '03' ? 'Boleta' : 'Nota de Venta');

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