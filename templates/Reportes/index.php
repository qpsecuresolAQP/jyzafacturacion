<?php
$queryParams = [
    'desde' => $desde,
    'hasta' => $hasta,
    'tipo' => $tipo
];
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0">
            <i class="fas fa-chart-bar"></i> Reporte de Comprobantes
        </h3>

        <div class="d-flex flex-wrap gap-2">
            <a href="<?= $this->Url->build(['controller' => 'Invoices', 'action' => 'index']) ?>" class="btn btn-warning">
                <i class="fas fa-receipt"></i> Comprobantes
            </a>
            <a href="<?= $this->Url->build(['controller' => 'Reportes', 'action' => 'exportarPdf', '?' => $queryParams]) ?>" target="_blank" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="<?= $this->Url->build(['controller' => 'Reportes', 'action' => 'exportarExcel', '?' => $queryParams]) ?>" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="ALL" <?= ($tipo === 'ALL') ? 'selected' : '' ?>>Todo</option>
                        <option value="BOLETA" <?= ($tipo === 'BOLETA') ? 'selected' : '' ?>>Boletas</option>
                        <option value="FACTURA" <?= ($tipo === 'FACTURA') ? 'selected' : '' ?>>Facturas</option>
                        <option value="RI" <?= ($tipo === 'RI') ? 'selected' : '' ?>>Recibos</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha desde</label>
                    <input type="date" name="desde" value="<?= h($desde ?? date('Y-m-d')) ?>" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha hasta</label>
                    <input type="date" name="hasta" value="<?= h($hasta ?? date('Y-m-d')) ?>" class="form-control">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-info w-100">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h6>Boletas</h6>
                    <h4><?= $totalBoletas ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h6>Facturas</h6>
                    <h4><?= $totalFacturas ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h6>Recibos</h6>
                    <h4><?= $totalRecibos ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h6>Total Ventas</h6>
                    <h4>S/ <?= number_format((float)$totalVentas, 2) ?></h4>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($resumenPagos)): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Resumen de Pagos</h6>
            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead class="bg-info text-white">
                        <tr>
                            <th>Método</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resumenPagos as $metodo => $datos): ?>
                        <tr>
                            <td><?= h(ucfirst(strtolower((string)$metodo))) ?></td>
                            <td class="text-center"><?= (int)$datos['cantidad'] ?></td>
                            <td class="text-end"><strong>S/ <?= number_format((float)$datos['total'], 2) ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="table-responsive card">
        <table class="table table-striped mb-0 align-middle">
            <thead class="bg-info text-white">
                <tr>
                    <th>ID</th>
                    <th>Serie</th>
                    <th>Número</th>
                    <th>Tipo</th>
                    <th>Cliente</th>
                    <th class="text-end">Total</th>
                    <th>Estado</th>
                    <th>Fecha / Hora</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($invoices as $inv): ?>
                <tr>
                    <td>
                        <span class="badge bg-secondary">#<?= $inv->id ?></span>
                    </td>

                    <td><?= h($inv->serie ?: 'RI') ?></td>

                    <td><strong><?= h($inv->correlativo ?: $inv->id) ?></strong></td>

                    <td>
                        <?= $inv->tipo_doc === '01' ? 'Factura' : ($inv->tipo_doc === '03' ? 'Boleta' : 'Recibo') ?>
                    </td>

                    <td><?= h($inv->cliente_nombre) ?></td>

                    <td class="text-end">
                        <strong class="text-success">S/ <?= number_format((float)$inv->total, 2) ?></strong>
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

                    <td>
                        <small><?= h($inv->created->format('d/m/Y H:i')) ?></small>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="text-muted small mt-3">
        Mostrando <?= count($invoices) ?> comprobantes
    </div>
</div>

<style>
    .container-fluid.py-4 .card {
        border: none;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .container-fluid.py-4 .btn {
        font-weight: 600;
        border-radius: 0.375rem;
    }

    .container-fluid.py-4 .btn-info {
        background-color: #0dcaf0;
        border-color: #0dcaf0;
        color: #fff;
    }

    .container-fluid.py-4 .btn-info:hover {
        background-color: #0ba5d4;
        border-color: #0ba5d4;
    }

    .container-fluid.py-4 .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .container-fluid.py-4 .table th,
    .container-fluid.py-4 .table td {
        vertical-align: middle;
    }
</style>