<?php
/**
 * @var \App\View\AppView $this
 * @var int $totalProductos
 * @var float $valorInventario
 * @var int $totalAgotados
 * @var int $totalBajos
 * @var int $totalNormales
 * @var array $porCategoria
 * @var array $topBajoStock
 * @var array $alertas
 * @var float $presupuestoReposicion
 */
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0">
            <i class="fas fa-warehouse"></i> Panel de Inventario
        </h3>

        <div class="d-flex flex-wrap gap-2">
            <a href="<?= $this->Url->build(['controller' => 'Productos', 'action' => 'index']) ?>" class="btn btn-outline-secondary">
                <i class="fas fa-box"></i> Productos
            </a>
            <a href="<?= $this->Url->build(['action' => 'reporteGanancias']) ?>" class="btn btn-outline-primary">
                <i class="fas fa-chart-line"></i> Ganancia por Ventas
            </a>
            <a href="<?= $this->Url->build(['action' => 'exportarPdf']) ?>" target="_blank" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="<?= $this->Url->build(['action' => 'exportarExcel']) ?>" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
        </div>
    </div>

    <div class="row mb-4 row-cols-2 row-cols-md-5 g-3">
        <div class="col">
            <div class="card border-primary h-100">
                <div class="card-body">
                    <h6>Total Productos</h6>
                    <h4><?= (int) $totalProductos ?></h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-info h-100">
                <div class="card-body">
                    <h6>Valor de Inventario</h6>
                    <h4>S/ <?= number_format($valorInventario, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-warning h-100">
                <div class="card-body">
                    <h6>Stock Bajo</h6>
                    <h4 class="text-warning"><?= (int) $totalBajos ?></h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-danger h-100">
                <div class="card-body">
                    <h6>Agotados</h6>
                    <h4 class="text-danger"><?= (int) $totalAgotados ?></h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-secondary h-100">
                <div class="card-body">
                    <h6>Presupuesto de Reposición</h6>
                    <h4>S/ <?= number_format($presupuestoReposicion, 2) ?></h4>
                    <small class="text-muted">Para llegar al mínimo de todos los productos con alerta</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Estado del Stock</h6>
                    <div style="position: relative; height: 260px;">
                        <canvas id="chartEstadoStock"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Productos por Categoría</h6>
                    <div style="position: relative; height: 260px;">
                        <canvas id="chartCategorias"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Top 10 productos más cerca de agotarse (stock actual vs. mínimo)</h6>
                    <div style="position: relative; height: 320px;">
                        <canvas id="chartTopBajoStock"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="fas fa-triangle-exclamation text-warning"></i> Alertas de Reposición</h6>
            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead class="bg-info text-white">
                        <tr>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th class="text-end">Stock</th>
                            <th class="text-end">Mínimo</th>
                            <th class="text-end">Cant. a Reponer</th>
                            <th class="text-end">Costo Reposición</th>
                            <th>Proveedor</th>
                            <th>Estado</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($alertas)): ?>
                            <?php foreach ($alertas as $p): ?>
                                <tr>
                                    <td><?= h($p->nombre) ?></td>
                                    <td><?= h($p->categoria_nombre ?: '-') ?></td>
                                    <td class="text-end"><?= number_format((float) $p->stock, 2) ?></td>
                                    <td class="text-end"><?= number_format((float) $p->stock_minimo, 2) ?></td>
                                    <td class="text-end"><?= number_format((float) $p->cantidad_reponer, 2) ?></td>
                                    <td class="text-end">S/ <?= number_format((float) $p->costo_reposicion, 2) ?></td>
                                    <td><?= h($p->proveedor_nombre ?: '-') ?></td>
                                    <td>
                                        <span class="badge bg-<?= $p->estado_stock === 'AGOTADO' ? 'danger' : 'warning text-dark' ?>">
                                            <?= $p->estado_stock === 'AGOTADO' ? 'Agotado' : 'Bajo' ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($p->proveedor_whatsapp)): ?>
                                            <?= $this->Html->link(
                                                '<i class="fab fa-whatsapp"></i> WhatsApp',
                                                'https://wa.me/' . preg_replace('/\D/', '', $p->proveedor_whatsapp)
                                                    . '?text=' . rawurlencode('Hola, necesito reponer stock de: ' . $p->nombre),
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
                                <td colspan="9" class="text-center text-muted">No hay productos con stock bajo o agotado. 🎉</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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

        new Chart(document.getElementById('chartCategorias'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($porCategoria), JSON_UNESCAPED_UNICODE) ?>,
                datasets: [{
                    label: 'Productos',
                    data: <?= json_encode(array_values($porCategoria)) ?>,
                    backgroundColor: '#0dcaf0',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });

        new Chart(document.getElementById('chartTopBajoStock'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($topBajoStock, 'nombre'), JSON_UNESCAPED_UNICODE) ?>,
                datasets: [
                    {
                        label: 'Stock actual',
                        data: <?= json_encode(array_column($topBajoStock, 'stock')) ?>,
                        backgroundColor: '#dc3545',
                    },
                    {
                        label: 'Stock mínimo',
                        data: <?= json_encode(array_column($topBajoStock, 'stock_minimo')) ?>,
                        backgroundColor: '#adb5bd',
                    },
                ],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: { x: { beginAtZero: true } },
            },
        });
    })();
</script>

<style>
    .container-fluid.py-4 .card {
        border: none;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .container-fluid.py-4 .table th,
    .container-fluid.py-4 .table td {
        vertical-align: middle;
    }
</style>
