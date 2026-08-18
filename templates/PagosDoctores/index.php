<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0">
            <i class="fas fa-money-bill-wave"></i> Pagos de Doctores
        </h3>

        <div class="d-flex flex-wrap gap-2">
            <a href="<?= $this->Url->build(['controller' => 'Invoices', 'action' => 'index']) ?>" class="btn btn-warning">
                <i class="fas fa-receipt"></i> Comprobantes
            </a>
            <a href="<?= $this->Url->build(['action' => 'registrarPago', '?' => [
                'doctor_id' => $doctorId,
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
            ]]) ?>" class="btn btn-success">
                <i class="fas fa-check-circle"></i> Registrar Pago
            </a>
            <a href="<?= $this->Url->build(['action' => 'historial']) ?>" class="btn btn-outline-dark">
                <i class="fas fa-history"></i> Historial de Pagos
            </a>
            <a href="<?= $this->Url->build(['action' => 'exportPdf', '?' => [
                'doctor_id' => $doctorId,
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
            ]]) ?>" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="<?= $this->Url->build(['action' => 'exportarExcel', '?' => [
                'doctor_id' => $doctorId,
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
            ]]) ?>" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Doctor</label>
                    <select name="doctor_id" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($doctores as $id => $nombre): ?>
                            <option value="<?= $id ?>" <?= ((string)$doctorId === (string)$id) ? 'selected' : '' ?>>
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
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <h6>Doctores con producción</h6>
                    <h4><?= (int)$resumen['total_doctores'] ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <h6>Total tratamientos</h6>
                    <h4>S/ <?= number_format((float)$resumen['total_tratamientos'], 2) ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <h6>Total a pagar</h6>
                    <h4>S/ <?= number_format((float)$resumen['total_pagar'], 2) ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <h6>Comprobantes</h6>
                    <h4><?= (int)$resumen['total_comprobantes'] ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Consolidado por método de pago</h6>
            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead class="bg-info text-white">
                        <tr>
                            <th>Método</th>
                            <th class="text-end">Monto total</th>
                            <th class="text-end">Pago doctor total</th>
                            <th class="text-center">Movimientos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($metodosConsolidados)): ?>
                            <?php foreach ($metodosConsolidados as $metodo): ?>
                                <tr>
                                    <td><?= h($metodo['metodo']) ?></td>
                                    <td class="text-end">S/ <?= number_format((float)$metodo['monto_total'], 2) ?></td>
                                    <td class="text-end"><strong>S/ <?= number_format((float)$metodo['pago_doctor_total'], 2) ?></strong></td>
                                    <td class="text-center"><?= (int)$metodo['movimientos'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No hay métodos de pago para el rango seleccionado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Total a pagar por doctor</h6>
            <div class="table-responsive">
                <table class="table table-striped table-sm align-middle mb-0">
                    <thead class="bg-info text-white">
                        <tr>
                            <th>Doctor</th>
                            <th class="text-end">Total tratamientos</th>
                            <th class="text-end">% pago</th>
                            <th class="text-end">Total a pagar</th>
                            <th class="text-center">Comprobantes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($doctoresConsolidados)): ?>
                            <?php foreach ($doctoresConsolidados as $doctor): ?>
                                <tr>
                                    <td><?= h($doctor['doctor']) ?></td>
                                    <td class="text-end">S/ <?= number_format((float)$doctor['total_tratamientos'], 2) ?></td>
                                    <td class="text-end"><?= number_format((float)$doctor['porcentaje'], 2) ?>%</td>
                                    <td class="text-end">
                                        <strong>S/ <?= number_format((float)$doctor['total_pagar'], 2) ?></strong>
                                        <?php if (!empty($doctor['metodos_pago'])): ?>
                                            <div class="mt-2 small text-start">
                                                <?php foreach ($doctor['metodos_pago'] as $metodo): ?>
                                                    <div class="mb-1">
                                                        <strong><?= h($metodo['metodo']) ?></strong>:
                                                        S/ <?= number_format((float)$metodo['pago_doctor_total'], 2) ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?= (int)$doctor['comprobantes'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">No hay doctores para el rango seleccionado.</td>
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
                            <th style="width: 40px;"></th>
                            <th>Fecha</th>
                            <th>Doctor</th>
                            <th>Cliente</th>
                            <th>Comprobante</th>
                            <th>Tipo</th>
                            <th>Métodos de pago</th>
                            <th>Total Tratamientos</th>
                            <th>%</th>
                            <th>Pago Doctor</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
            <tbody>
                        <?php if (!empty($resultado)): ?>
                            <?php foreach ($resultado as $idx => $row): ?>
                                <tr class="align-middle <?= !empty($row['ya_pagado']) ? 'table-success' : '' ?>">
                                    <td>
                                        <button class="btn btn-sm btn-outline-info toggle-details" type="button" data-target="items-<?= $idx ?>" onclick="toggleDetails(this)">
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </td>
                                    <td><?= h($row['fecha']->format('Y-m-d H:i')) ?></td>
                                    <td><?= h($row['doctor']) ?></td>
                                    <td><?= h($row['cliente']) ?></td>
                                    <td><?= h($row['comprobante']) ?></td>
                                    <td><?= h($row['tipo_doc']) ?></td>
                                    <td>
                                        <?php if (!empty($row['metodos_pago'])): ?>
                                            <?php foreach ($row['metodos_pago'] as $pago): ?>
                                                <div class="small mb-1">
                                                    <strong><?= h($pago['metodo']) ?></strong>: S/ <?= number_format((float)$pago['monto'], 2) ?>
                                                    <span class="text-muted">| Pago doctor: S/ <?= number_format((float)$pago['pago_doctor'], 2) ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        S/ <?= number_format((float)$row['base_doctor'], 2) ?>
                                        <?php if (!empty($row['total_distribuido'])): ?>
                                            <div class="small text-muted">
                                                <span class="text-decoration-line-through">S/ <?= number_format((float)$row['base_doctor_bruta'], 2) ?></span>
                                                − S/ <?= number_format((float)$row['total_distribuido'], 2) ?> (Lab/Materiales)
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= number_format((float)$row['porcentaje'], 2) ?>%</td>
                                    <td><strong>S/ <?= number_format((float)$row['pago_doctor'], 2) ?></strong></td>
                                    <td>
                                        <?= h($row['estado']) ?>
                                        <?php if (!empty($row['ya_pagado'])): ?>
                                            <span class="badge bg-success"><i class="fas fa-check"></i> Pagado a doctor</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <!-- Fila expandible para items -->
                                <tr class="details-row" id="items-<?= $idx ?>" style="display: none;">
                                    <td colspan="11">
                                        <div class="p-3 bg-light">
                                            <h6 class="mb-3"><i class="fas fa-receipt"></i> Detalles de items</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <thead class="bg-secondary text-white small">
                                                        <tr>
                                                            <th style="width: 35%;">Descripción</th>
                                                            <th style="width: 15%;">Tipo</th>
                                                            <th class="text-end" style="width: 12%;">Cantidad</th>
                                                            <th class="text-end" style="width: 15%;">Valor unitario</th>
                                                            <th class="text-end" style="width: 15%;">Total</th>
                                                            <th style="width: 8%;">¿Se paga?</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="small">
                                                        <?php foreach ($row['items_detalles'] as $item): ?>
                                                            <?php $sePaga = (float) ($item['pago_doctor_item'] ?? 0) > 0; ?>
                                                            <tr class="<?= $sePaga ? 'table-success' : 'table-light' ?>">
                                                                <td><?= h($item['descripcion']) ?></td>
                                                                <td>
                                                                    <?php if ($item['tipo'] === 'Tratamiento'): ?>
                                                                        <span class="badge bg-success"><i class="fas fa-check-circle"></i> <?= h($item['tipo']) ?></span>
                                                                    <?php elseif ($item['tipo'] === 'Examen'): ?>
                                                                        <span class="badge bg-info text-dark"><i class="fas fa-vial"></i> <?= h($item['tipo']) ?></span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-warning text-dark"><i class="fas fa-box"></i> <?= h($item['tipo']) ?></span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td class="text-end"><?= number_format((float)$item['cantidad'], 2) ?></td>
                                                                <td class="text-end">S/ <?= number_format((float)$item['valor_unitario'], 2) ?></td>
                                                                <td class="text-end"><strong>S/ <?= number_format((float)$item['total'], 2) ?></strong></td>
                                                                <td>
                                                                    <?php if ($sePaga): ?>
                                                                        <span class="badge bg-success">Sí: S/ <?= number_format((float) $item['pago_doctor_item'], 2) ?></span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-secondary">No (Clínica)</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="mt-2 small text-muted">
                                                <i class="fas fa-info-circle"></i>
                                                <strong>Se paga el porcentaje (<?= number_format((float)$row['porcentaje'], 2) ?>%) de los tratamientos, más la comisión fija configurada en cada examen. Los productos son de la clínica.</strong>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted">No hay registros para mostrar.</td>
                            </tr>
                        <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>


<script>
function toggleDetails(btn) {
    const targetId = btn.getAttribute('data-target');
    const row = document.getElementById(targetId);
    const icon = btn.querySelector('i');
    
    if (row.style.display === 'none') {
        row.style.display = 'table-row';
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        row.style.display = 'none';
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}
</script>

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

    .container-fluid.py-4 .table th,
    .container-fluid.py-4 .table td {
        vertical-align: middle;
    }
    
    .container-fluid.py-4 .card h6 {
        font-weight: 700;
        margin-bottom: 0.75rem;
    }

    .details-row {
        transition: all 0.3s ease;
    }

    .details-row td {
        padding: 0 !important;
    }
</style>