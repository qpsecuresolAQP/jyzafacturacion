<?php
/**
 * @var \App\View\AppView $this
 * @var int $totalProductos
 * @var float $valorInventario
 * @var float $valorVenta
 * @var float $gananciaPotencial
 * @var int $totalAgotados
 * @var int $totalBajos
 * @var int $totalNormales
 * @var array $topBajoStock
 * @var array $alertas
 * @var float $presupuestoReposicion
 * @var array $lotesVencidos
 * @var array $lotesPorVencer
 * @var float $valorVencido
 * @var float $valorPorVencer
 */
$dias = \App\Model\Table\ProductoLotesTable::DIAS_ALERTA_VENCIMIENTO;
$puedeMovimientos = $this->Permisos->tiene('ProductoMovimientos', 'add');
$puedePerdidas = $this->Permisos->tiene('ProductoMovimientos', 'perdidas');
$puedeIngreso = $this->Permisos->tiene('IngresosMercaderia', 'add');
$nVencidos = count($lotesVencidos);
$nPorVencer = count($lotesPorVencer);
$nAlertas = count($alertas);
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="panel-inv container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-0"><i class="fas fa-warehouse"></i> Panel de Inventario</h3>
            <small class="text-muted">Situación al <?= date('d/m/Y H:i') ?> · solo productos activos</small>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <?php if ($puedeIngreso): ?>
                <a href="<?= $this->Url->build(['controller' => 'IngresosMercaderia', 'action' => 'add']) ?>" class="btn btn-info text-white">
                    <i class="fas fa-truck-loading"></i> Registrar compra
                </a>
            <?php endif; ?>
            <a href="<?= $this->Url->build(['controller' => 'Productos', 'action' => 'index']) ?>" class="btn btn-outline-secondary">
                <i class="fas fa-box"></i> Productos
            </a>
            <a href="<?= $this->Url->build(['action' => 'reporteGanancias']) ?>" class="btn btn-outline-primary">
                <i class="fas fa-chart-line"></i> Ganancia por Ventas
            </a>
            <a href="<?= $this->Url->build(['action' => 'exportarPdf']) ?>" target="_blank" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="<?= $this->Url->build(['action' => 'exportarExcel']) ?>" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Excel
            </a>
        </div>
    </div>

    <?php if ($nVencidos > 0): ?>
        <div class="alert alert-danger d-flex justify-content-between align-items-center flex-wrap gap-2" role="alert">
            <span>
                <i class="fas fa-skull-crossbones"></i>
                <strong><?= $nVencidos ?> lote(s) vencido(s)</strong> con stock por S/ <?= number_format($valorVencido, 2) ?> al costo.
                No se pueden vender: retírelos y regístrelos como baja.
            </span>
            <?php if ($puedePerdidas): ?>
                <a href="<?= $this->Url->build(['controller' => 'ProductoMovimientos', 'action' => 'perdidas']) ?>" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash-alt"></i> Ir a Pérdidas y bajas
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- ══════════ INDICADORES ══════════ -->
    <div class="row row-cols-2 row-cols-md-4 row-cols-xl-8 g-3 mb-4">
        <div class="col">
            <div class="card kpi h-100 kpi-borde border-primary">
                <div class="card-body">
                    <div class="kpi-label">Productos</div>
                    <div class="kpi-valor"><?= (int) $totalProductos ?></div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card kpi h-100 kpi-borde border-info">
                <div class="card-body">
                    <div class="kpi-label">Inventario al costo</div>
                    <div class="kpi-valor">S/ <?= number_format($valorInventario, 2) ?></div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card kpi h-100 kpi-borde border-success">
                <div class="card-body">
                    <div class="kpi-label">Inventario a precio de venta</div>
                    <div class="kpi-valor">S/ <?= number_format($valorVenta, 2) ?></div>
                    <small class="text-muted">Ganancia potencial S/ <?= number_format($gananciaPotencial, 2) ?></small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card kpi h-100 kpi-borde border-secondary">
                <div class="card-body">
                    <div class="kpi-label">Reponer hasta el mínimo</div>
                    <div class="kpi-valor">S/ <?= number_format($presupuestoReposicion, 2) ?></div>
                    <small class="text-muted"><?= $nAlertas ?> producto(s)</small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card kpi h-100 kpi-borde border-warning">
                <div class="card-body">
                    <div class="kpi-label">Stock bajo</div>
                    <div class="kpi-valor text-warning"><?= (int) $totalBajos ?></div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card kpi h-100 kpi-borde border-danger">
                <div class="card-body">
                    <div class="kpi-label">Agotados</div>
                    <div class="kpi-valor text-danger"><?= (int) $totalAgotados ?></div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card kpi h-100 kpi-borde border-warning">
                <div class="card-body">
                    <div class="kpi-label">Por vencer (<?= $dias ?> días)</div>
                    <div class="kpi-valor text-warning"><?= $nPorVencer ?> <small class="fs-6">lote(s)</small></div>
                    <small class="text-muted">S/ <?= number_format($valorPorVencer, 2) ?> al costo</small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card kpi h-100 kpi-borde border-danger">
                <div class="card-body">
                    <div class="kpi-label">Vencidos</div>
                    <div class="kpi-valor text-danger"><?= $nVencidos ?> <small class="fs-6">lote(s)</small></div>
                    <small class="text-muted">S/ <?= number_format($valorVencido, 2) ?> al costo</small>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════ GRÁFICOS ══════════ -->
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Estado del stock</h6>
                    <div style="position: relative; height: 260px;">
                        <canvas id="chartEstadoStock"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Los 10 más cerca de agotarse <small class="text-muted fw-normal">(stock vigente vs. mínimo)</small></h6>
                    <?php if (empty($topBajoStock)): ?>
                        <p class="text-muted mb-0">Ningún producto tiene stock mínimo configurado.</p>
                    <?php else: ?>
                        <div style="position: relative; height: 260px;">
                            <canvas id="chartTopBajoStock"></canvas>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════ DETALLE ══════════ -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link <?= $nVencidos + $nPorVencer > 0 && $nAlertas === 0 ? '' : 'active' ?>" data-tab="#tab-reposicion" type="button">
                        <i class="fas fa-cart-plus"></i> Reposición
                        <span class="badge bg-<?= $nAlertas ? 'warning text-dark' : 'secondary' ?>"><?= $nAlertas ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link <?= $nVencidos + $nPorVencer > 0 && $nAlertas === 0 ? 'active' : '' ?>" data-tab="#tab-vencimientos" type="button">
                        <i class="fas fa-hourglass-half"></i> Vencimientos
                        <span class="badge bg-<?= $nVencidos ? 'danger' : ($nPorVencer ? 'warning text-dark' : 'secondary') ?>"><?= $nVencidos + $nPorVencer ?></span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">

            <!-- Reposición -->
            <div class="tab-pane <?= $nVencidos + $nPorVencer > 0 && $nAlertas === 0 ? '' : 'active' ?>" id="tab-reposicion">
                <?php if ($nAlertas): ?>
                    <input type="search" class="form-control form-control-sm mb-2 filtro-tabla" data-tabla="#tabla-reposicion" placeholder="Filtrar por producto, categoría o proveedor…">
                <?php endif; ?>
                <div class="table-responsive">
                    <table class="table table-striped table-sm align-middle mb-0" id="tabla-reposicion">
                        <thead class="bg-info text-white">
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th class="text-end">Stock vigente</th>
                                <th class="text-end">Mínimo</th>
                                <th class="text-end">A reponer</th>
                                <th class="text-end">Costo reposición</th>
                                <th>Proveedor</th>
                                <th>Estado</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($nAlertas): ?>
                                <?php foreach ($alertas as $p): ?>
                                    <tr>
                                        <td>
                                            <?= h($p->nombre) ?>
                                            <?php if ($p->stock_vencido > 0): ?>
                                                <br><small class="text-danger"><i class="fas fa-ban"></i> +<?= number_format($p->stock_vencido, 2) ?> vencido(s) no vendible(s)</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= h($p->categoria_nombre ?: '-') ?></td>
                                        <td class="text-end"><?= number_format($p->stock_vigente, 2) ?></td>
                                        <td class="text-end"><?= number_format((float) $p->stock_minimo, 2) ?></td>
                                        <td class="text-end"><?= number_format((float) $p->cantidad_reponer, 2) ?></td>
                                        <td class="text-end">S/ <?= number_format((float) $p->costo_reposicion, 2) ?></td>
                                        <td><?= h($p->proveedor_nombre ?: '-') ?></td>
                                        <td>
                                            <span class="badge bg-<?= $p->estado_stock === 'AGOTADO' ? 'danger' : 'warning text-dark' ?>">
                                                <?= $p->estado_stock === 'AGOTADO' ? 'Agotado' : 'Bajo' ?>
                                            </span>
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <?php if (!empty($p->proveedor_whatsapp)): ?>
                                                <?= $this->Html->link(
                                                    '<i class="fab fa-whatsapp"></i> Pedir',
                                                    'https://wa.me/' . preg_replace('/\D/', '', $p->proveedor_whatsapp)
                                                        . '?text=' . rawurlencode('Hola, necesito reponer stock de: ' . $p->nombre
                                                        . ' (cantidad sugerida: ' . number_format((float) $p->cantidad_reponer, 0) . ')'),
                                                    ['escape' => false, 'target' => '_blank', 'class' => 'btn btn-success btn-sm']
                                                ) ?>
                                            <?php else: ?>
                                                <span class="text-muted small">Sin proveedor</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-3">Todo el inventario está sobre su mínimo. 🎉</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Vencimientos -->
            <div class="tab-pane <?= $nVencidos + $nPorVencer > 0 && $nAlertas === 0 ? 'active' : '' ?>" id="tab-vencimientos">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                    <small class="text-muted">Lotes con stock vencidos o que vencen en los próximos <?= $dias ?> días, del más urgente al menos urgente.</small>
                    <?php if ($this->Permisos->tiene('IngresosMercaderia', 'vencimientos')): ?>
                        <a href="<?= $this->Url->build(['controller' => 'IngresosMercaderia', 'action' => 'vencimientos']) ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-list"></i> Reporte completo
                        </a>
                    <?php endif; ?>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-sm align-middle mb-0">
                        <thead class="bg-info text-white">
                            <tr>
                                <th>Producto</th>
                                <th>Lote</th>
                                <th>Vence</th>
                                <th>Situación</th>
                                <th class="text-end">Stock</th>
                                <th class="text-end">Valor al costo</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_merge($lotesVencidos, $lotesPorVencer) as $l): ?>
                                <?php $dv = $l->dias_para_vencer; ?>
                                <tr class="<?= $l->vencido ? 'table-danger' : ($dv <= 30 ? 'table-warning' : '') ?>">
                                    <td><?= h($l->producto->nombre) ?></td>
                                    <td><?= h($l->lote ?: 's/n') ?></td>
                                    <td><?= $l->fecha_vencimiento->format('d/m/Y') ?></td>
                                    <td>
                                        <?php if ($l->vencido): ?>
                                            <span class="badge bg-danger">Vencido hace <?= abs((int) $dv) ?> día(s)</span>
                                        <?php else: ?>
                                            <span class="badge bg-<?= $dv <= 30 ? 'warning text-dark' : 'secondary' ?>">
                                                <?= (int) $dv === 0 ? 'Vence hoy' : 'Vence en ' . (int) $dv . ' día(s)' ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end"><?= number_format((float) $l->stock, 2) ?></td>
                                    <td class="text-end">S/ <?= number_format($l->valor_costo, 2) ?></td>
                                    <td class="text-center text-nowrap">
                                        <?php if ($l->vencido && $puedePerdidas): ?>
                                            <a href="<?= $this->Url->build(['controller' => 'ProductoMovimientos', 'action' => 'perdidas']) ?>" class="btn btn-outline-danger btn-sm">Dar de baja</a>
                                        <?php elseif (!$l->vencido): ?>
                                            <span class="text-muted small">Vender primero</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ($nVencidos + $nPorVencer === 0): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">No hay lotes vencidos ni por vencer en los próximos <?= $dias ?> días. 🎉</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        new Chart(document.getElementById('chartEstadoStock'), {
            type: 'doughnut',
            data: {
                labels: ['Normal', 'Bajo', 'Agotado'],
                datasets: [{
                    data: [<?= (int) $totalNormales ?>, <?= (int) $totalBajos ?>, <?= (int) $totalAgotados ?>],
                    backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
            },
        });

        const top = document.getElementById('chartTopBajoStock');
        if (top) {
            new Chart(top, {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_column($topBajoStock, 'nombre'), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>,
                    datasets: [
                        { label: 'Stock vigente', data: <?= json_encode(array_column($topBajoStock, 'stock')) ?>, backgroundColor: '#dc3545' },
                        { label: 'Stock mínimo', data: <?= json_encode(array_column($topBajoStock, 'stock_minimo')) ?>, backgroundColor: '#adb5bd' },
                    ],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { x: { beginAtZero: true } },
                },
            });
        }

        // Pestañas (sin depender de la versión de Bootstrap).
        const botones = document.querySelectorAll('.panel-inv [data-tab]');
        botones.forEach(function (b) {
            b.addEventListener('click', function () {
                botones.forEach(x => x.classList.toggle('active', x === b));
                document.querySelectorAll('.panel-inv .tab-pane').forEach(function (p) {
                    p.classList.toggle('active', '#' + p.id === b.dataset.tab);
                });
            });
        });

        // Filtro rápido de la tabla de reposición.
        document.querySelectorAll('.filtro-tabla').forEach(function (input) {
            input.addEventListener('input', function () {
                const q = input.value.trim().toLowerCase();
                document.querySelectorAll(input.dataset.tabla + ' tbody tr').forEach(function (tr) {
                    tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
                });
            });
        });
    })();
</script>

<style>
    .panel-inv .card { border: none; box-shadow: 0 1px 3px rgba(0, 0, 0, .08); }
    .panel-inv .tab-pane { display: none; }
    .panel-inv .tab-pane.active { display: block; }
    .panel-inv .kpi-borde { border-left-width: 4px !important; border-left-style: solid !important; }
    .panel-inv .nav-tabs .nav-link { background: none; cursor: pointer; }
    .panel-inv .kpi .card-body { padding: .75rem .9rem; }
    .panel-inv .kpi-label { font-size: .72rem; text-transform: uppercase; letter-spacing: .03em; color: #6c757d; }
    .panel-inv .kpi-valor { font-size: 1.35rem; font-weight: 700; line-height: 1.2; }
    .panel-inv .table th, .panel-inv .table td { vertical-align: middle; }
    @media (min-width: 1200px) { .panel-inv .row-cols-xl-8 > * { flex: 0 0 auto; width: 12.5%; } }
    @media (min-width: 1200px) and (max-width: 1599px) { .panel-inv .kpi-valor { font-size: 1.05rem; } }
</style>
