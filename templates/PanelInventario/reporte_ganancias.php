<?php
/**
 * @var \App\View\AppView $this
 * @var string $fechaDesde
 * @var string $fechaHasta
 * @var array $filas
 * @var float $totalVendido
 * @var float $totalCosto
 * @var float $totalGanancia
 */
$queryParams = ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta];
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0">
            <i class="fas fa-chart-line"></i> Ganancia por Ventas de Productos
        </h3>

        <div class="d-flex flex-wrap gap-2">
            <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary">
                <i class="fas fa-warehouse"></i> Panel de Inventario
            </a>
            <a href="<?= $this->Url->build(['action' => 'exportarGananciasPdf', '?' => $queryParams]) ?>" target="_blank" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="<?= $this->Url->build(['action' => 'exportarGananciasExcel', '?' => $queryParams]) ?>" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
        </div>
    </div>

    <div class="alert alert-light border small mb-4">
        <i class="fas fa-circle-info"></i>
        El costo se calcula con el <strong>precio de compra actual</strong> de cada producto (no se guarda un costo histórico por venta), así que si el precio de compra cambió durante el período, la ganancia mostrada es una aproximación con el costo vigente.
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Fecha desde</label>
                    <input type="date" name="fecha_desde" value="<?= h($fechaDesde) ?>" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha hasta</label>
                    <input type="date" name="fecha_hasta" value="<?= h($fechaHasta) ?>" class="form-control">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-info w-100">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-body">
                    <h6>Total Vendido</h6>
                    <h4>S/ <?= number_format($totalVendido, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-secondary">
                <div class="card-body">
                    <h6>Costo Total</h6>
                    <h4>S/ <?= number_format($totalCosto, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body">
                    <h6>Ganancia Total</h6>
                    <h4 class="<?= $totalGanancia >= 0 ? 'text-success' : 'text-danger' ?>">S/ <?= number_format($totalGanancia, 2) ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive card">
        <table class="table table-striped mb-0 align-middle">
            <thead class="bg-info text-white">
                <tr>
                    <th>Producto</th>
                    <th>Código</th>
                    <th class="text-end">Cantidad Vendida</th>
                    <th class="text-end">Total Vendido</th>
                    <th class="text-end">Costo Total</th>
                    <th class="text-end">Ganancia</th>
                    <th class="text-end">Margen %</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($filas)): ?>
                    <?php foreach ($filas as $f): ?>
                        <tr>
                            <td><?= h($f['nombre']) ?></td>
                            <td><?= h($f['codigo']) ?></td>
                            <td class="text-end"><?= number_format($f['cantidad_vendida'], 2) ?></td>
                            <td class="text-end">S/ <?= number_format($f['total_vendido'], 2) ?></td>
                            <td class="text-end">S/ <?= number_format($f['costo_total'], 2) ?></td>
                            <td class="text-end">
                                <strong class="<?= $f['ganancia'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    S/ <?= number_format($f['ganancia'], 2) ?>
                                </strong>
                            </td>
                            <td class="text-end"><?= number_format($f['margen_porcentaje'], 1) ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No se vendieron productos en el rango seleccionado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

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
