<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\PaquetesPago $paquetePago
 */
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><?= h($paquetePago->nombre) ?></h1>
            <p class="text-muted"><?= h($paquetePago->descripcion) ?></p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <?= $this->Html->link(
                    __('Editar'),
                    ['action' => 'edit', $paquetePago->id],
                    ['class' => 'btn btn-warning']
                ) ?>
                <?= $this->Form->postLink(
                    __('Eliminar'),
                    ['action' => 'delete', $paquetePago->id],
                    ['class' => 'btn btn-danger', 'confirm' => __('¿Estás seguro?')]
                ) ?>
            </div>
        </div>
    </div>

    <!-- Información Principal -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><?= __('Información del Paquete') ?></h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4"><?= __('Paciente') ?></dt>
                        <dd class="col-sm-8">
                            <?= h($paquetePago->historias_clinica->paciente->nombre . ' ' . $paquetePago->historias_clinica->paciente->apellido) ?>
                        </dd>

                        <dt class="col-sm-4"><?= __('Sesiones') ?></dt>
                        <dd class="col-sm-8"><?= $paquetePago->num_sesiones ?></dd>

                        <dt class="col-sm-4"><?= __('Precio Total') ?></dt>
                        <dd class="col-sm-8">
                            <strong><?= $this->Number->currency($paquetePago->precio_total) ?></strong>
                        </dd>

                        <dt class="col-sm-4"><?= __('Forma de Pago') ?></dt>
                        <dd class="col-sm-8">
                            <span class="badge <?= $paquetePago->tipo_pago === 'contado' ? 'bg-success' : 'bg-info' ?>">
                                <?= __($paquetePago->tipo_pago === 'contado' ? 'Contado' : 'En Partes') ?>
                            </span>
                        </dd>

                        <dt class="col-sm-4"><?= __('Estado') ?></dt>
                        <dd class="col-sm-8">
                            <?php
                                $estadoClase = match($paquetePago->estado) {
                                    'pendiente' => 'bg-warning',
                                    'cancelado' => 'bg-success',
                                    default => 'bg-secondary'
                                };
                                $estadoTexto = match($paquetePago->estado) {
                                    'pendiente' => 'Pendiente',
                                    'cancelado' => 'Pagado',
                                    default => 'Desconocido'
                                };
                            ?>
                            <span class="badge <?= $estadoClase ?>">
                                <?= __($estadoTexto) ?>
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Información de Pago -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><?= __('Información de Pago') ?></h5>
                </div>
                <div class="card-body">
                    <?php if ($paquetePago->tipo_pago === 'contado'): ?>
                        <div class="alert alert-info mb-0">
                            <p class="mb-2">
                                <strong><?= __('Pago al Contado') ?></strong>
                            </p>
                            <p class="mb-2">
                                <strong>Monto:</strong> <?= $this->Number->currency($paquetePago->precio_total) ?>
                            </p>
                            <p class="mb-0">
                                <strong>Fecha Recordatorio:</strong> 
                                <?= $paquetePago->fecha_recordatorio?->format('d-m-Y') ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">
                            <?= __('Este paquete tiene ') ?><strong><?= count($paquetePago->paquetes_pagos_cuotas) ?></strong> <?= __('cuota(s).') ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Cuotas (si es pago en partes) -->
    <?php if ($paquetePago->tipo_pago === 'partes' && !empty($paquetePago->paquetes_pagos_cuotas)): ?>
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0"><?= __('Detalle de Cuotas') ?></h5>
            </div>
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th><?= __('Cuota') ?></th>
                            <th><?= __('Monto') ?></th>
                            <th><?= __('Fecha Recordatorio') ?></th>
                            <th><?= __('Estado') ?></th>
                            <th><?= __('Fecha Pago') ?></th>
                            <th><?= __('Acciones') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paquetePago->paquetes_pagos_cuotas as $cuota): ?>
                            <tr>
                                <td><?= h($cuota->titulo_referencial) ?></td>
                                <td><?= $this->Number->currency($cuota->monto) ?></td>
                                <td><?= $cuota->fecha_recordatorio?->format('d-m-Y H:i') ?></td>
                                <td>
                                    <?php
                                        $claseBadge = $cuota->estado === 'pagado' ? 'bg-success' : 'bg-warning';
                                        $textoEstado = $cuota->estado === 'pagado' ? 'Pagado' : 'Pendiente';
                                    ?>
                                    <span class="badge <?= $claseBadge ?>">
                                        <?= __($textoEstado) ?>
                                    </span>
                                </td>
                                <td><?= $cuota->fecha_pago?->format('d-m-Y H:i') ?? '---' ?></td>
                                <td>
                                    <?php if ($cuota->estado === 'pendiente'): ?>
    <?= $this->Form->postLink(
        '<i class="bi bi-check-circle"></i> Pagar',
        ['action' => 'registrarPago', $cuota->id],
        [
            'class' => 'btn btn-sm btn-success',
            'escape' => false,
            'confirm' => '¿Confirmar pago?'
        ]
    ) ?>
<?php else: ?>
    <span class="badge bg-success">Pagado</span>
<?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
</div>
<?php endif; ?>