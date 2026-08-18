<style>
    .label-text {
        color: #000000;
        font-weight: bold;
    }

    .data-box {
        /* background-color: #6c757d; */
        border: 1px solid #ced4da;
        border-radius: 5px;
        padding: 5px 10px;
        min-height: 38px;
        display: flex;
        align-items: center;
        margin-top: -5px;
    }

    .table-responsive {
        margin-top: 20px;
    }

    .actions {
        text-align: center;
        margin-top: 30px;
    }
</style>

<div class="container">
    <!-- Header -->
    <div class="mb-4 mt-3">
        <h3 class="text-info"><i class="fas fa-tooth"></i> Información del Presupuesto</h3>
    </div>

    <!-- Cuerpo -->
    <div>
        <!-- Campo: Información del Paciente -->
        <div class="row mb-3">
            <div class="col-md-2">
                <p class="label-text"><?= __('Paciente:') ?></p>
            </div>
            <div class="col-md-10">
                <div class="data-box">
                    <?php
                    if (!empty($presupuesto->historias_clinica)) {

                        echo h(
                            $presupuesto->historias_clinica->paciente->nombre . ' ' .
                                $presupuesto->historias_clinica->paciente->apellido
                        );
                    } else {

                        echo !empty($presupuesto->nombre_apellido)
                            ? h($presupuesto->nombre_apellido)
                            : 'Paciente nuevo';
                    }
                    ?>
                </div>
            </div>
        </div>

        <?php if (!empty($presupuesto->historias_clinica)): ?>
            <div class="row mb-3">
                <div class="col-md-2">
                    <p class="label-text"><?= __('Dirección:') ?></p>
                </div>
                <div class="col-md-10">
                    <div class="data-box">
                        <?= h($presupuesto->historias_clinica->direccion ?: 'No disponible') ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row mb-3">

            <!-- Fecha siempre visible -->
            <div class="col-md-2">
                <p class="label-text"><?= __('Fecha:') ?></p>
            </div>

            <div class="col-md-3">
                <div class="data-box">
                    <?= $presupuesto->modified->format('d/m/Y') ?>
                </div>
            </div>
            <?php if (!empty($presupuesto->historias_clinica)): ?>
                <div class="col-md-2">
                    <p class="label-text"><?= __('DNI:') ?></p>
                </div>

                <div class="col-md-3">
                    <div class="data-box">
                        <?= h($presupuesto->historias_clinica->dni ?: 'No disponible') ?>
                    </div>
                </div>

                <div class="col-md-2"></div>
            <?php else: ?>
                <div class="col-md-7"></div>
            <?php endif; ?>

        </div>
        <div class="row mb-3">
            <div class="col-md-2">
                <p class="label-text"><?= __('Teléfono:') ?></p>
            </div>

            <div class="col-md-10">
                <div class="data-box">
                    <?php

                    if (!empty($presupuesto->historias_clinica)) {

                        echo 'No registrado';
                    } else {

                        echo !empty($presupuesto->telefono)
                            ? h($presupuesto->telefono)
                            : 'No registrado';
                    }

                    ?>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-2">
                <p class="label-text"><?= __('Observaciones:') ?></p>
            </div>
            <div class="col-md-10">
                <div class="data-box">
                    <?= !empty($presupuesto->notas) && !empty($presupuesto->notas)
                        ? h($presupuesto->notas)
                        : __('Ninguna observacion') ?>
                </div>
            </div>
        </div>

        <!-- Detalles de la Cotización -->


        <div class="mt-4 ">
            <h3 class="text-info"><i class="fas fa-list"></i> Detalles de la Cotización</h3>
        </div>


        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= __('Ítem') ?></th>
                        <th><?= __('Cantidad') ?></th>
                        <th><?= __('Descuento (%)') ?></th>
                        <th><?= __('Precio Unitario') ?></th>
                        <th><?= __('Subtotal') ?></th>
                        <th><?= __('Observaciones') ?></th>
                        <th><?= __('Facturación') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($presupuesto->presupuestos_tratamientos as $detalle): ?>
                        <?php
                            $tipoItemDetalle = $detalle->tipo_item ?? 'tratamiento';
                            $refIdDetalle = match ($tipoItemDetalle) {
                                'producto' => $detalle->producto_id,
                                'examen' => $detalle->examen_id,
                                default => $detalle->tratamiento_id,
                            };
                            $claveDetalle = $tipoItemDetalle . '_' . $refIdDetalle;
                            $facturado = (float) ($montoFacturadoPorItem[$claveDetalle] ?? 0);
                            $lineaTotal = (float) $detalle->total;
                        ?>
                        <?php
                            $detalleNombre = match ($tipoItemDetalle) {
                                'producto' => $detalle->producto->nombre ?? '',
                                'examen' => $detalle->examene->nombre ?? '',
                                default => $detalle->tratamiento->nombre ?? '',
                            };
                        ?>
                        <tr>
                            <td><?= h($detalleNombre) ?></td>
                            <td><?= h($detalle->cantidad) ?></td>
                            <td>
                                <?php if (empty($detalle->descuento)): ?>
                                    Sin descuento
                                <?php elseif (($detalle->descuento_tipo ?? 'porcentaje') === 'monto'): ?>
                                    <?= $this->Number->currency($detalle->descuento, 'S/ ') ?>
                                <?php else: ?>
                                    <?= h($detalle->descuento) ?> %
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $this->Number->currency($detalle->precio_unitario, 'S/ ') ?>
                            </td>
                            <td>
                                <?= $this->Number->currency($detalle->total, 'S/ ') ?>
                            </td>
                            <td>
                                <?= !empty($detalle->observaciones)
                                    ? h($detalle->observaciones)
                                    : '--' ?>
                            </td>
                            <td>
                                <?php if ($facturado <= 0): ?>
                                    <span class="badge bg-secondary">Sin facturar</span>
                                <?php elseif ($facturado < $lineaTotal): ?>
                                    <span class="badge bg-warning text-dark">
                                        Parcial (<?= $this->Number->currency($facturado, 'S/ ') ?> / <?= $this->Number->currency($lineaTotal, 'S/ ') ?>)
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success">
                                        Facturado (<?= $this->Number->currency($facturado, 'S/ ') ?>)
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Total -->
        <div class="row mb-3">
            <div class="col-md-2">
                <p class="label-text"><?= __('Total:') ?></p>
            </div>
            <div class="col-md-3">
                <div class="data-box">
                    S/ <?= $this->Number->format(array_sum(array_map(function ($detalle) {
                            return $detalle->total;
                        }, $presupuesto->presupuestos_tratamientos)), ['places' => 2]) ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($presupuesto->presupuestos_invoices)): ?>
        <div class="mt-4">
            <h3 class="text-info"><i class="fas fa-file-invoice"></i> Comprobantes generados desde este presupuesto</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= __('Comprobante') ?></th>
                        <th><?= __('Fecha') ?></th>
                        <th><?= __('Monto Facturado') ?></th>
                        <th><?= __('Estado') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($presupuesto->presupuestos_invoices as $pi): ?>
                        <tr>
                            <td>
                                <?= $this->Html->link(
                                    h(($pi->invoice->serie ?? '') . '-' . ($pi->invoice->correlativo ?? '')),
                                    ['controller' => 'Invoices', 'action' => 'view', $pi->invoice_id]
                                ) ?>
                            </td>
                            <td><?= h($pi->created?->format('d/m/Y H:i')) ?></td>
                            <td><?= $this->Number->currency($pi->monto_facturado, 'S/ ') ?></td>
                            <td>
                                <?php $estadoInvoice = $pi->invoice->estado ?? ''; ?>
                                <span class="badge bg-<?= $estadoInvoice === 'ANULADO' ? 'danger' : 'success' ?>">
                                    <?= h($estadoInvoice) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- Acciones -->
    <div class="actions">
        <?= $this->Html->link(__('Descargar PDF'), ['action' => 'exportPresupuestoPdf', $presupuesto->id], ['class' => 'btn btn-info me-2']) ?>
        <?= $this->Html->link(__('Volver a la Lista'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
        <?= $this->Html->link(__('Editar Presupuesto'), ['action' => 'edit', $presupuesto->id], ['class' => 'btn btn-primary ms-2']) ?>
        <?= $this->Html->link(
            __('Convertir a Factura'),
            ['controller' => 'Invoices', 'action' => 'add', '?' => ['presupuesto_id' => $presupuesto->id]],
            ['class' => 'btn btn-success ms-2']
        ) ?>
    </div>
</div>