<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Campaña> $campañas
 */
?>

<div class="campañas index content">
    <?= $this->Html->link(__('Añadir Campaña'), ['action' => 'add'], ['class' => 'button float-right btn btn-info openModal']) ?>
    <div class="contenedor principal">
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'N° de Campaña') ?></th>
                        <th><?= $this->Paginator->sort('nombre', 'Nombre') ?></th>
                        <th><?= $this->Paginator->sort('descripcion', 'Descripción') ?></th>
                        <th><?= $this->Paginator->sort('created', 'Creado') ?></th>
                        <th><?= $this->Paginator->sort('modified', 'Modificado') ?></th>
                        <th class="actions text-dark"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($campañas as $campaña): ?>
                    <tr>
                        <td><?= $this->Number->format($campaña->id) ?></td>
                        <td><?= h($campaña->nombre) ?></td>
                        <td><?= h($campaña->descripcion) ?></td>
                        <td><?= h($campaña->created) ?></td>
                        <td><?= h($campaña->modified) ?></td>
                        <td class="actions text-center">
                            <!-- Íconos personalizados para acciones -->
                            <?= $this->Html->link(
                                '<i class="fas fa-eye"></i>',
                                ['action' => 'view', $campaña->id],
                                ['escape' => false, 'title' => 'Ver', 'class' => 'btn btn-info btn-sm openModal']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i>',
                                ['action' => 'edit', $campaña->id],
                                ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm openModal']
                            ) ?>
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