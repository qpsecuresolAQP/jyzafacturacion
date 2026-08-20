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
 * @var float $totalSalidas
 * @var float $balanceNeto
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
        <div class="col-md-6">
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
    </div>
</div>
