<?php
/**
 * @var \App\View\AppView $this
 * @var array $resultado
 * @var array $laboratorios_consolidados
 * @var array $laboratorios
 * @var mixed $laboratorioId
 * @var string $fechaDesde
 * @var string $fechaHasta
 * @var float $total_pagar
 * @var int $total_comprobantes
 * @var int $total_laboratorios
 */
?>
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0">
            <i class="fas fa-flask"></i> Pagos a Laboratorios
        </h3>

        <div class="d-flex flex-wrap gap-2">
            <a href="<?= $this->Url->build(['controller' => 'Laboratorios', 'action' => 'index']) ?>" class="btn btn-outline-secondary">
                <i class="fas fa-vials"></i> Laboratorios
            </a>
            <a href="<?= $this->Url->build(['action' => 'registrarPago', '?' => [
                'laboratorio_id' => $laboratorioId,
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
            ]]) ?>" class="btn btn-success">
                <i class="fas fa-check-circle"></i> Registrar Pago
            </a>
            <a href="<?= $this->Url->build(['action' => 'historial']) ?>" class="btn btn-outline-dark">
                <i class="fas fa-history"></i> Historial de Pagos
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Laboratorio</label>
                    <select name="laboratorio_id" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($laboratorios as $id => $nombre): ?>
                            <option value="<?= $id ?>" <?= ((string)$laboratorioId === (string)$id) ? 'selected' : '' ?>>
                                <?= h($nombre) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha desde</label>
                    <input type="date" name="fecha_desde" value="<?= h($fechaDesde ?? date('Y-m-d')) ?>" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha hasta</label>
                    <input type="date" name="fecha_hasta" value="<?= h($fechaHasta ?? date('Y-m-d')) ?>" class="form-control">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-info w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body">
                    <h6>Laboratorios con deuda</h6>
                    <h4><?= (int) $total_laboratorios ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body">
                    <h6>Total a pagar</h6>
                    <h4>S/ <?= number_format((float) $total_pagar, 2) ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-body">
                    <h6>Comprobantes</h6>
                    <h4><?= (int) $total_comprobantes ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Total a pagar por laboratorio</h6>
            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead class="bg-info text-white">
                        <tr>
                            <th>Laboratorio</th>
                            <th class="text-end">Total a pagar</th>
                            <th class="text-center">Comprobantes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($laboratorios_consolidados)): ?>
                            <?php foreach ($laboratorios_consolidados as $lab): ?>
                                <tr>
                                    <td><?= h($lab['laboratorio']) ?></td>
                                    <td class="text-end"><strong>S/ <?= number_format((float) $lab['total_pagar'], 2) ?></strong></td>
                                    <td class="text-center"><?= (int) $lab['comprobantes'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">No hay laboratorios con deuda para el rango seleccionado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="table-responsive card">
        <table class="table table-striped mb-0 align-middle">
            <thead class="bg-info text-white">
                <tr>
                    <th>Fecha</th>
                    <th>Laboratorio</th>
                    <th>Cliente</th>
                    <th>Comprobante</th>
                    <th>Descripción</th>
                    <th class="text-end">Monto</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($resultado)): ?>
                    <?php foreach ($resultado as $row): ?>
                        <tr class="<?= !empty($row['ya_pagado']) ? 'table-success' : '' ?>">
                            <td><?= h($row['fecha']->format('Y-m-d H:i')) ?></td>
                            <td><?= h($row['laboratorio']) ?></td>
                            <td><?= h($row['cliente']) ?></td>
                            <td><?= h($row['comprobante']) ?></td>
                            <td><?= h($row['descripcion']) ?></td>
                            <td class="text-end"><strong>S/ <?= number_format((float) $row['monto'], 2) ?></strong></td>
                            <td>
                                <?= h($row['estado']) ?>
                                <?php if (!empty($row['ya_pagado'])): ?>
                                    <span class="badge bg-success"><i class="fas fa-check"></i> Pagado</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No hay registros para mostrar.</td>
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

    .container-fluid.py-4 .btn {
        font-weight: 600;
        border-radius: 0.375rem;
    }

    .container-fluid.py-4 .table th,
    .container-fluid.py-4 .table td {
        vertical-align: middle;
    }

    .container-fluid.py-4 .card h6 {
        font-weight: 700;
        margin-bottom: 0.75rem;
    }
</style>
