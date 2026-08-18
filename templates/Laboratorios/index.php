<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Laboratorio> $laboratorios
 */
?>
<?php $this->assign('title', 'Laboratorios'); ?>

<div class="laboratorios index content">
    <?= $this->Html->link(__('Añadir Laboratorio'), ['action' => 'add'], ['class' => 'button float-right btn btn-info openModal']) ?>
    <?= $this->Html->link(
        '<i class="fas fa-money-bill-wave"></i> Pagos a Laboratorios',
        ['controller' => 'PagosLaboratorios', 'action' => 'index'],
        ['escape' => false, 'class' => 'button float-right btn btn-outline-warning me-2']
    ) ?>
    <div class="contenedor principal">
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'N°') ?></th>
                        <th><?= $this->Paginator->sort('nombre', 'Nombre') ?></th>
                        <th><?= $this->Paginator->sort('activo', 'Activo') ?></th>
                        <th><?= $this->Paginator->sort('created', 'Creado') ?></th>
                        <th class="actions text-dark"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($laboratorios as $laboratorio): ?>
                    <tr>
                        <td><?= $this->Number->format($laboratorio->id) ?></td>
                        <td><?= h($laboratorio->nombre) ?></td>
                        <td>
                            <span class="badge bg-<?= $laboratorio->activo ? 'success' : 'secondary' ?>">
                                <?= $laboratorio->activo ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td><?= h($laboratorio->created) ?></td>
                        <td class="actions text-center">
                            <?= $this->Html->link(
                                '<i class="fas fa-eye"></i>',
                                ['action' => 'view', $laboratorio->id],
                                ['escape' => false, 'title' => 'Ver', 'class' => 'btn btn-info btn-sm openModal']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i>',
                                ['action' => 'edit', $laboratorio->id],
                                ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm openModal']
                            ) ?>
                            <?= $this->Form->postLink(
                                '<i class="fas fa-trash"></i>',
                                ['action' => 'delete', $laboratorio->id],
                                ['escape' => false, 'title' => 'Eliminar', 'class' => 'btn btn-danger btn-sm', 'confirm' => __('¿Seguro que desea eliminar el laboratorio "{0}"?', $laboratorio->nombre)]
                            ) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

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
