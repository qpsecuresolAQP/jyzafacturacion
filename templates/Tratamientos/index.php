<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Tratamiento> $tratamientos
 * @var string $estadoFiltro
 * @var string $busqueda
 */
?>

<div class="tratamientos index content">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="btn-group" role="group">
            <?= $this->Html->link('Activos', ['action' => 'index', '?' => ['estado' => 'activos', 'q' => $busqueda]], [
                'class' => 'btn btn-sm ' . ($estadoFiltro === 'activos' ? 'btn-info' : 'btn-outline-info'),
            ]) ?>
            <?= $this->Html->link('Inactivos', ['action' => 'index', '?' => ['estado' => 'inactivos', 'q' => $busqueda]], [
                'class' => 'btn btn-sm ' . ($estadoFiltro === 'inactivos' ? 'btn-info' : 'btn-outline-info'),
            ]) ?>
            <?= $this->Html->link('Todos', ['action' => 'index', '?' => ['estado' => 'todos', 'q' => $busqueda]], [
                'class' => 'btn btn-sm ' . ($estadoFiltro === 'todos' ? 'btn-info' : 'btn-outline-info'),
            ]) ?>
        </div>

        <?= $this->Form->create(null, ['type' => 'get', 'class' => 'd-flex gap-2']) ?>
            <?= $this->Form->hidden('estado', ['value' => $estadoFiltro]) ?>
            <?= $this->Form->control('q', [
                'label' => false,
                'value' => $busqueda,
                'placeholder' => 'Buscar por nombre...',
                'class' => 'form-control form-control-sm',
                'templates' => ['inputContainer' => '{{content}}'],
            ]) ?>
            <button type="submit" class="btn btn-sm btn-outline-info"><i class="fas fa-search"></i></button>
            <?php if ($busqueda !== ''): ?>
                <?= $this->Html->link('<i class="fas fa-times"></i>', ['action' => 'index', '?' => ['estado' => $estadoFiltro]], [
                    'escape' => false,
                    'class' => 'btn btn-sm btn-outline-secondary',
                    'title' => 'Limpiar búsqueda',
                ]) ?>
            <?php endif; ?>
        <?= $this->Form->end() ?>

        <?= $this->Html->link(__('Añadir Tratamiento'), ['action' => 'add'], ['class' => 'btn btn-info openModal']) ?>
    </div>

    <div class="contenedor principal">
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'N° de Tratamiento') ?></th>
                        <th><?= $this->Paginator->sort('nombre', 'Nombre') ?></th>
                        <th><?= $this->Paginator->sort('costo', 'Costo') ?></th>
                        <th><?= $this->Paginator->sort('monto_fijo_pago', 'Pago Fijo Doctor') ?></th>
                        <th><?= $this->Paginator->sort('estado', 'Estado') ?></th>
                        <th><?= $this->Paginator->sort('created', 'Creado') ?></th>
                        <th><?= $this->Paginator->sort('modified', 'Modificado') ?></th>
                        <th class="actions text-dark"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tratamientos as $tratamiento): ?>
                    <tr>
                        <td><?= $this->Number->format($tratamiento->id) ?></td>
                        <td><?= h($tratamiento->nombre) ?></td>
                        <td><?= h($tratamiento->costo) ?></td>
                        <td>
                            <?php if ($tratamiento->monto_fijo_pago > 0): ?>
                                S/ <?= h($tratamiento->monto_fijo_pago) ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= $tratamiento->estado ? 'success' : 'secondary' ?>">
                                <?= $tratamiento->estado ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td><?= h($tratamiento->created) ?></td>
                        <td><?= h($tratamiento->modified) ?></td>
                        <td class="actions text-center">
                            <!-- Íconos personalizados para acciones -->
                            <?= $this->Html->link(
                                '<i class="fas fa-eye"></i>',
                                ['action' => 'view', $tratamiento->id],
                                ['escape' => false, 'title' => 'Ver', 'class' => 'btn btn-info btn-sm openModal']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i>',
                                ['action' => 'edit', $tratamiento->id],
                                ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm openModal']
                            ) ?>
                            <?php if ($tratamiento->estado): ?>
                                <?= $this->Form->postLink(
                                    '<i class="fas fa-ban"></i>',
                                    ['action' => 'delete', $tratamiento->id],
                                    ['escape' => false, 'title' => 'Desactivar', 'class' => 'btn btn-danger btn-sm', 'confirm' => __('¿Seguro que desea desactivar el tratamiento "{0}"?', $tratamiento->nombre)]
                                ) ?>
                            <?php else: ?>
                                <?= $this->Form->postLink(
                                    '<i class="fas fa-check-circle"></i>',
                                    ['action' => 'reactivar', $tratamiento->id],
                                    ['escape' => false, 'title' => 'Reactivar', 'class' => 'btn btn-success btn-sm', 'confirm' => __('¿Reactivar el tratamiento "{0}"?', $tratamiento->nombre)]
                                ) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('Primero')) ?>
            <?= $this->Paginator->prev('< ' . __('Anterior')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('Siguiente') . ' >') ?>
            <?= $this->Paginator->last(__('Último') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registro(s) de un total de {{count}}')) ?></p>
    </div>
</div>
