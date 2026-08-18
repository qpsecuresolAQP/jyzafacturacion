<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\PaquetesPago> $paquetesPagos
 */
?>

<div class="container-fluid mt-4">

    <?php if (!empty($paquetesPagos)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th><?= __('Paquete') ?></th>
                        <th><?= __('Sesiones') ?></th>
                        <th><?= __('Precio Total') ?></th>
                        <th><?= __('Forma de Pago') ?></th>
                        <th><?= __('Estado') ?></th>
                        <th><?= __('Fecha Recordatorio') ?></th>
                        <th><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paquetesPagos as $paquete): ?>
                        <tr>
                            <td><?= h($paquete->nombre) ?></td>
                            <td><?= $paquete->num_sesiones ?></td>
                            <td><?= $this->Number->currency($paquete->precio_total) ?></td>
                            <td>
                                <span class="badge <?= $paquete->tipo_pago === 'contado' ? 'bg-success' : 'bg-info' ?>">
                                    <?= __($paquete->tipo_pago === 'contado' ? 'Contado' : 'En Partes') ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                    $estadoClase = match($paquete->estado) {
                                        'pendiente' => 'bg-warning',
                                        'pagado_parcial' => 'bg-info',
                                        'pagado_completo' => 'bg-success',
                                        'cancelado' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                    $estadoTexto = match($paquete->estado) {
                                        'pendiente' => 'Pendiente',
                                        'pagado_parcial' => 'Parcial',
                                        'pagado_completo' => 'Pagado',
                                        'cancelado' => 'Cancelado',
                                        default => 'Desconocido'
                                    };
                                ?>
                                <span class="badge <?= $estadoClase ?>">
                                    <?= __($estadoTexto) ?>
                                </span>
                            </td>
                            <td><?= $paquete->fecha_recordatorio?->format('d-m-Y') ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <?= $this->Html->link(
                                        '<i class="bi bi-eye"></i>',
                                        ['action' => 'view', $paquete->id],
                                        ['class' => 'btn btn-sm btn-info', 'escape' => false, 'title' => 'Ver']
                                    ) ?>
                                    <?= $this->Html->link(
                                        '<i class="bi bi-pencil"></i>',
                                        ['action' => 'edit', $paquete->id],
                                        ['class' => 'btn btn-sm btn-warning', 'escape' => false, 'title' => 'Editar']
                                    ) ?>
                                    <?= $this->Form->postLink(
                                        '<i class="bi bi-trash"></i>',
                                        ['action' => 'delete', $paquete->id],
                                        [
                                            'class' => 'btn btn-sm btn-danger',
                                            'escape' => false,
                                            'confirm' => __('¿Estás seguro?')
                                        ]
                                    ) ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <?= __('No hay paquetes de pago registrados.') ?>
        </div>
    <?php endif; ?>
</div>
