<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\PagosDoctoresHistorial $pagoHistorial
 */
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0">
            <i class="fas fa-receipt"></i> Detalle de Pago #<?= h($pagoHistorial->id) ?>
        </h3>

        <a href="<?= $this->Url->build(['action' => 'historial']) ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Historial
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <h6>Doctor</h6>
                    <h5><?= h(trim(($pagoHistorial->doctore->nombre ?? '') . ' ' . ($pagoHistorial->doctore->apellido ?? ''))) ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <h6>Período Cubierto</h6>
                    <h6><?= h($pagoHistorial->fecha_desde) ?> a <?= h($pagoHistorial->fecha_hasta) ?></h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <h6>Monto Pagado</h6>
                    <h4>S/ <?= number_format((float) $pagoHistorial->monto_total, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <h6>Comprobantes</h6>
                    <h4><?= (int) $pagoHistorial->total_comprobantes ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-light border mb-4">
        <strong>Registrado por:</strong> <?= h($pagoHistorial->user->username ?? '-') ?>
        <span class="text-muted ms-2">el <?= h($pagoHistorial->created?->format('Y-m-d H:i')) ?></span>
    </div>

    <?php if (!empty($pagoHistorial->observaciones)): ?>
        <div class="alert alert-light border mb-4">
            <strong>Observaciones:</strong> <?= h($pagoHistorial->observaciones) ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive card">
        <table class="table table-striped mb-0">
            <thead class="bg-dark text-white">
                <tr>
                    <th>Comprobante</th>
                    <th>Cliente</th>
                    <th>Método de Pago</th>
                    <th>Base Doctor</th>
                    <th>Monto Pagado</th>
                    <th>Estado Comprobante</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pagoHistorial->pagos_doctores_historial_movimientos as $detalle): ?>
                    <tr>
                        <td>
                            <?= $this->Html->link(
                                h(($detalle->invoice->serie ?? 'RI') . '-' . ($detalle->invoice->correlativo ?? $detalle->invoice->id)),
                                ['controller' => 'Invoices', 'action' => 'view', $detalle->invoice_id]
                            ) ?>
                        </td>
                        <td><?= h($detalle->invoice->cliente_nombre ?? '-') ?></td>
                        <td><span class="badge bg-info text-dark"><?= h($detalle->metodo_pago) ?></span></td>
                        <td>S/ <?= number_format((float) $detalle->base_doctor, 2) ?></td>
                        <td><strong>S/ <?= number_format((float) $detalle->monto_pagado, 2) ?></strong></td>
                        <td>
                            <span class="badge bg-<?= $detalle->invoice->estado === 'ANULADO' ? 'danger' : 'success' ?>">
                                <?= h($detalle->invoice->estado ?? '-') ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
