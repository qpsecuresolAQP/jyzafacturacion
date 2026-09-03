<?php
/**
 * @var \App\View\AppView $this
 * @var string $fechaDesde
 * @var string $fechaHasta
 * @var float $totalIngresosVentas
 * @var float $totalIngresosManuales
 * @var float $totalIngresos
 * @var array $ingresosPorMetodo
 * @var array $ingresosManualesDetalle
 * @var array $movimientosDetalle
 * @var float $totalEgresos
 * @var array $egresosPorTipo
 * @var array $egresosDetalle
 * @var float $totalReembolsosAnulacion
 * @var array $reembolsosAnulacionDetalle
 * @var float $totalPagosDoctores
 * @var array $pagosDoctoresPorDoctor
 * @var array $pagosDoctoresDetalle
 * @var float $totalPagosLaboratorios
 * @var array $pagosLaboratoriosPorLab
 * @var array $pagosLaboratoriosDetalle
 * @var float $totalGastoMateriales
 * @var array $gastoMaterialesPorItem
 * @var float $totalSalidas
 * @var float $balanceNeto
 * @var array $serieDiaria
 */
?>
<div class="container-fluid py-4">
    <h3 class="fw-bold mb-4"><i class="fas fa-chart-pie"></i> Finanzas</h3>

    <?= $this->Form->create(null, ['type' => 'get', 'class' => 'row g-2 align-items-end mb-4']) ?>
        <div class="col-auto">
            <label class="form-label mb-0">Desde</label>
            <?= $this->Form->control('fecha_desde', [
                'type' => 'date',
                'value' => $fechaDesde,
                'label' => false,
                'class' => 'form-control',
            ]) ?>
        </div>
        <div class="col-auto">
            <label class="form-label mb-0">Hasta</label>
            <?= $this->Form->control('fecha_hasta', [
                'type' => 'date',
                'value' => $fechaHasta,
                'label' => false,
                'class' => 'form-control',
            ]) ?>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtrar</button>
        </div>
    <?= $this->Form->end() ?>

    <!-- ══════════════════════════════════════════
         CONSOLIDADO / BALANCE NETO
    ══════════════════════════════════════════ -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-success h-100">
                <div class="card-body">
                    <h6 class="text-muted">Ingresos totales</h6>
                    <h3 class="text-success mb-0">S/ <?= number_format($totalIngresos, 2) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger h-100">
                <div class="card-body">
                    <h6 class="text-muted">Salidas totales</h6>
                    <h3 class="text-danger mb-0">S/ <?= number_format($totalSalidas, 2) ?></h3>
                    <small class="text-muted">Egresos + Dres. + Labs.</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-<?= $balanceNeto >= 0 ? 'success' : 'danger' ?> h-100">
                <div class="card-body">
                    <h6 class="text-muted">Balance neto</h6>
                    <h3 class="mb-0 <?= $balanceNeto >= 0 ? 'text-success' : 'text-danger' ?>">
                        S/ <?= number_format($balanceNeto, 2) ?>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-secondary h-100">
                <div class="card-body">
                    <h6 class="text-muted">Periodo</h6>
                    <h6 class="mb-0"><?= h($fechaDesde) ?> al <?= h($fechaHasta) ?></h6>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Ventas cobradas</h6>
                    <h4 class="text-success mb-0">S/ <?= number_format($totalIngresosVentas, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Ingresos manuales</h6>
                    <h4 class="text-success mb-0">S/ <?= number_format($totalIngresosManuales, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Pagado a doctores</h6>
                    <h4 class="text-danger mb-0">S/ <?= number_format($totalPagosDoctores, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Pagado a laboratorios</h6>
                    <h4 class="text-danger mb-0">S/ <?= number_format($totalPagosLaboratorios, 2) ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted">Gastos de caja (egresos)</h6>
                    <h4 class="text-danger mb-0">S/ <?= number_format($totalEgresos, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-secondary">
                <div class="card-body text-center">
                    <h6 class="text-muted">
                        Reembolsos por anulación
                        <i class="fas fa-info-circle" title="Devoluciones al paciente por facturas anuladas. No afectan el balance neto: el ingreso original de esas facturas ya está excluido."></i>
                    </h6>
                    <h4 class="text-secondary mb-0">S/ <?= number_format($totalReembolsosAnulacion, 2) ?></h4>
                    <small class="text-muted">Informativo, no afecta el balance</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h6 class="text-muted">
                        Gasto en materiales
                        <i class="fas fa-info-circle" title="Estimado según lo vendido: cantidad de cada tratamiento/examen facturado × su gasto de materiales configurado. Informativo, no afecta el balance neto."></i>
                    </h6>
                    <h4 class="text-warning mb-0">S/ <?= number_format($totalGastoMateriales, 2) ?></h4>
                    <small class="text-muted">Estimado según ventas, no afecta el balance</small>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         HISTOGRAMA: ingresos / egresos / pagos por día
    ══════════════════════════════════════════ -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <strong>Movimientos por día</strong>
            <?php if (count($serieDiaria) > 62): ?>
                <span class="text-muted small">
                    <i class="fas fa-info-circle"></i> Rango amplio (<?= count($serieDiaria) ?> días) — acota las fechas para un detalle más legible.
                </span>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if (empty($serieDiaria)): ?>
                <p class="text-center text-muted mb-0 py-4">Sin datos en el periodo seleccionado.</p>
            <?php else: ?>
                <div style="position: relative; height: 340px;">
                    <canvas id="finanzasHistograma"></canvas>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabs para el detalle -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#ingresos">Ingresos (Pagos Recibidos)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#egresos">Egresos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#doctores">Pagos a Doctores</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#laboratorios">Pagos a Laboratorios</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#materiales">Gasto en Materiales</a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- ══════════════ INGRESOS ══════════════ -->
        <div id="ingresos" class="tab-pane fade show active">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header"><strong>Por método de pago</strong></div>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr><th>Método</th><th>Cant.</th><th>Total</th></tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($ingresosPorMetodo)): ?>
                                        <?php foreach ($ingresosPorMetodo as $m): ?>
                                            <tr>
                                                <td><span class="badge bg-info text-dark"><?= h($m['metodo']) ?></span></td>
                                                <td><?= (int) $m['cantidad'] ?></td>
                                                <td>S/ <?= number_format($m['total'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="3" class="text-center text-muted py-3">Sin movimientos en el periodo.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header"><strong>Ingresos manuales de caja</strong></div>
                        <div class="table-responsive" style="max-height: 260px; overflow-y: auto;">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr><th>Fecha</th><th>Descripción</th><th>Monto</th></tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($ingresosManualesDetalle)): ?>
                                        <?php foreach ($ingresosManualesDetalle as $i): ?>
                                            <tr>
                                                <td><?= h($i['fecha']?->format('d-m-Y H:i')) ?></td>
                                                <td><?= h($i['descripcion']) ?></td>
                                                <td>S/ <?= number_format($i['monto'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="3" class="text-center text-muted py-3">Sin ingresos manuales.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><strong>Detalle de pagos recibidos (ventas)</strong></div>
                <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Fecha</th>
                                <th>Comprobante</th>
                                <th>Cliente</th>
                                <th>Método</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($movimientosDetalle)): ?>
                                <?php foreach ($movimientosDetalle as $m): ?>
                                    <tr>
                                        <td><?= h($m['fecha']?->format('d-m-Y H:i')) ?></td>
                                        <td><?= h($m['comprobante']) ?></td>
                                        <td><?= h($m['cliente']) ?></td>
                                        <td><span class="badge bg-info text-dark"><?= h($m['metodo']) ?></span></td>
                                        <td>S/ <?= number_format($m['monto'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-muted py-3">Sin pagos recibidos en el periodo.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ══════════════ EGRESOS ══════════════ -->
        <div id="egresos" class="tab-pane fade">
            <div class="card mb-3">
                <div class="card-header"><strong>Por tipo</strong></div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Tipo</th><th>Cant.</th><th>Total</th></tr></thead>
                        <tbody>
                            <?php if (!empty($egresosPorTipo)): ?>
                                <?php foreach ($egresosPorTipo as $e): ?>
                                    <tr>
                                        <td><span class="badge bg-<?= $e['tipo'] === 'GASTO' ? 'danger' : 'warning text-dark' ?>"><?= h($e['tipo']) ?></span></td>
                                        <td><?= (int) $e['cantidad'] ?></td>
                                        <td>S/ <?= number_format($e['total'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center text-muted py-3">Sin egresos en el periodo.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><strong>Detalle de egresos</strong></div>
                <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Método</th>
                                <th>Descripción</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($egresosDetalle)): ?>
                                <?php foreach ($egresosDetalle as $e): ?>
                                    <tr>
                                        <td><?= h($e['fecha']?->format('d-m-Y H:i')) ?></td>
                                        <td><span class="badge bg-<?= $e['tipo'] === 'GASTO' ? 'danger' : 'warning text-dark' ?>"><?= h($e['tipo']) ?></span></td>
                                        <td><?= h($e['metodo']) ?></td>
                                        <td><?= h($e['descripcion']) ?></td>
                                        <td>S/ <?= number_format($e['monto'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-muted py-3">Sin egresos en el periodo.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <strong>Reembolsos por anulación</strong>
                    <span class="text-muted ms-2" style="font-size: 0.85em;">(informativo, no afecta el balance neto)</span>
                </div>
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="bg-secondary text-white">
                            <tr>
                                <th>Fecha</th>
                                <th>Método</th>
                                <th>Descripción</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($reembolsosAnulacionDetalle)): ?>
                                <?php foreach ($reembolsosAnulacionDetalle as $r): ?>
                                    <tr>
                                        <td><?= h($r['fecha']?->format('d-m-Y H:i')) ?></td>
                                        <td><?= h($r['metodo']) ?></td>
                                        <td><?= h($r['descripcion']) ?></td>
                                        <td>S/ <?= number_format($r['monto'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">Sin reembolsos por anulación en el periodo.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ══════════════ PAGOS A DOCTORES ══════════════ -->
        <div id="doctores" class="tab-pane fade">
            <div class="card mb-3">
                <div class="card-header"><strong>Por doctor</strong></div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Doctor</th><th>Pagos</th><th>Total</th></tr></thead>
                        <tbody>
                            <?php if (!empty($pagosDoctoresPorDoctor)): ?>
                                <?php foreach ($pagosDoctoresPorDoctor as $d): ?>
                                    <tr>
                                        <td><?= h($d['doctor']) ?></td>
                                        <td><?= (int) $d['cantidad'] ?></td>
                                        <td>S/ <?= number_format($d['total'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center text-muted py-3">Sin pagos a doctores en el periodo.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><strong>Detalle de pagos</strong></div>
                <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Fecha</th>
                                <th>Doctor</th>
                                <th>Comprobantes</th>
                                <th>Monto</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pagosDoctoresDetalle)): ?>
                                <?php foreach ($pagosDoctoresDetalle as $p): ?>
                                    <tr>
                                        <td><?= h($p['fecha']?->format('d-m-Y H:i')) ?></td>
                                        <td><?= h($p['doctor']) ?></td>
                                        <td><?= (int) $p['comprobantes'] ?></td>
                                        <td>S/ <?= number_format($p['monto'], 2) ?></td>
                                        <td>
                                            <?= $this->Html->link(
                                                '<i class="fas fa-file-pdf"></i>',
                                                ['controller' => 'PagosDoctores', 'action' => 'pdfPago', $p['id']],
                                                ['class' => 'btn btn-sm btn-outline-danger', 'escape' => false, 'target' => '_blank']
                                            ) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-muted py-3">Sin pagos a doctores en el periodo.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ══════════════ PAGOS A LABORATORIOS ══════════════ -->
        <div id="laboratorios" class="tab-pane fade">
            <div class="card mb-3">
                <div class="card-header"><strong>Por laboratorio</strong></div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Laboratorio</th><th>Pagos</th><th>Total</th></tr></thead>
                        <tbody>
                            <?php if (!empty($pagosLaboratoriosPorLab)): ?>
                                <?php foreach ($pagosLaboratoriosPorLab as $l): ?>
                                    <tr>
                                        <td><?= h($l['laboratorio']) ?></td>
                                        <td><?= (int) $l['cantidad'] ?></td>
                                        <td>S/ <?= number_format($l['total'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center text-muted py-3">Sin pagos a laboratorios en el periodo.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><strong>Detalle de pagos</strong></div>
                <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Fecha</th>
                                <th>Laboratorio</th>
                                <th>Comprobantes</th>
                                <th>Monto</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pagosLaboratoriosDetalle)): ?>
                                <?php foreach ($pagosLaboratoriosDetalle as $p): ?>
                                    <tr>
                                        <td><?= h($p['fecha']?->format('d-m-Y H:i')) ?></td>
                                        <td><?= h($p['laboratorio']) ?></td>
                                        <td><?= (int) $p['comprobantes'] ?></td>
                                        <td>S/ <?= number_format($p['monto'], 2) ?></td>
                                        <td>
                                            <?= $this->Html->link(
                                                '<i class="fas fa-file-pdf"></i>',
                                                ['controller' => 'PagosLaboratorios', 'action' => 'pdfPago', $p['id']],
                                                ['class' => 'btn btn-sm btn-outline-danger', 'escape' => false, 'target' => '_blank']
                                            ) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-muted py-3">Sin pagos a laboratorios en el periodo.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ══════════════ GASTO EN MATERIALES ══════════════ -->
        <div id="materiales" class="tab-pane fade">
            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i>
                Estimado según lo vendido en el periodo: por cada tratamiento o examen facturado (no anulado),
                se multiplica su cantidad por el gasto de materiales configurado en su ficha. Es informativo
                para calcular utilidad real; no mueve caja ni afecta el balance neto.
            </div>
            <div class="card">
                <div class="card-header"><strong>Por tratamiento / examen</strong></div>
                <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Tipo</th>
                                <th>Nombre</th>
                                <th>Cantidad vendida</th>
                                <th>Gasto materiales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($gastoMaterialesPorItem)): ?>
                                <?php foreach ($gastoMaterialesPorItem as $g): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-<?= $g['tipo'] === 'tratamiento' ? 'info text-dark' : 'primary' ?>">
                                                <?= h(ucfirst($g['tipo'])) ?>
                                            </span>
                                        </td>
                                        <td><?= h($g['nombre']) ?></td>
                                        <td><?= number_format($g['cantidad'], 2) ?></td>
                                        <td>S/ <?= number_format($g['total'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">Sin gasto de materiales registrado en el periodo.</td></tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($gastoMaterialesPorItem)): ?>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="3" class="text-end">Total</td>
                                    <td>S/ <?= number_format($totalGastoMateriales, 2) ?></td>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($serieDiaria)): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var serie = <?= json_encode($serieDiaria, JSON_NUMERIC_CHECK) ?>;

    // Paleta categórica validada (dataviz), primeros 5 slots en orden fijo.
    var COLOR_INGRESOS = '#2a78d6';   // slot 1 blue
    var COLOR_EGRESOS = '#eb6834';    // slot 2 orange
    var COLOR_DOCTORES = '#1baf7a';   // slot 3 aqua
    var COLOR_LABS = '#eda100';       // slot 4 yellow
    var COLOR_MATERIALES = '#e87ba4'; // slot 5 magenta

    var labels = serie.map(function (d) {
        var partes = d.fecha.split('-');
        return partes[2] + '/' + partes[1];
    });

    var ctx = document.getElementById('finanzasHistograma').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Ingresos',
                    data: serie.map(function (d) { return d.ingresos; }),
                    backgroundColor: COLOR_INGRESOS,
                },
                {
                    label: 'Egresos',
                    data: serie.map(function (d) { return d.egresos; }),
                    backgroundColor: COLOR_EGRESOS,
                },
                {
                    label: 'Pagos a Doctores',
                    data: serie.map(function (d) { return d.pagosDoctores; }),
                    backgroundColor: COLOR_DOCTORES,
                },
                {
                    label: 'Pagos a Laboratorios',
                    data: serie.map(function (d) { return d.pagosLaboratorios; }),
                    backgroundColor: COLOR_LABS,
                },
                {
                    label: 'Gasto en Materiales',
                    data: serie.map(function (d) { return d.gastoMateriales; }),
                    backgroundColor: COLOR_MATERIALES,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'top',
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: function (item, data) {
                        var label = data.datasets[item.datasetIndex].label || '';
                        var valor = parseFloat(item.yLabel).toLocaleString('es-PE', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        });
                        return label + ': S/ ' + valor;
                    },
                },
            },
            scales: {
                xAxes: [{
                    gridLines: { display: false },
                }],
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function (value) {
                            return 'S/ ' + value.toLocaleString('es-PE');
                        },
                    },
                    gridLines: { color: '#e1e0d9' },
                }],
            },
        },
    });
});
</script>
<?php endif; ?>
