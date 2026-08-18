<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Consulta> $consultas
 */
?>

<div class="consultas index content">
    <?= $this->Html->link(__('Añadir Consulta'), ['action' => 'add'], ['class' => 'button float-right btn btn-info openModal']) ?>
    <div class="contenedor principal">
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'N° de Consulta') ?></th>
                        <th><?= $this->Paginator->sort('motivo', 'Motivo') ?></th>
                        <th><?= $this->Paginator->sort('diagnostico', 'Diagnostico') ?></th>
                        <th><?= $this->Paginator->sort('prescripcion', 'Prescripción') ?></th>
                        <th><?= $this->Paginator->sort('created', 'Creado') ?></th>
                        <th><?= $this->Paginator->sort('modified', 'Modificado') ?></th>
                        <th class="actions text-dark"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($consultas as $consulta): ?>
                    <tr>
                        <td><?= $this->Number->format($consulta->id) ?></td>
                        <td><?= h($consulta->motivo) ?></td>
                        <td><?= h($consulta->diagnostico) ?></td>
                        <td><?= h($consulta->prescripcion) ?></td>
                        <td><?= h($consulta->created) ?></td>
                        <td><?= h($consulta->modified) ?></td>
                        <td class="actions text-center">
                            <!-- Íconos personalizados para acciones -->
                            <?= $this->Html->link(
                                '<i class="fas fa-eye"></i>',
                                ['action' => 'view', $consulta->id],
                                ['escape' => false, 'title' => 'Ver', 'class' => 'btn btn-info btn-sm openModal']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i>',
                                ['action' => 'edit', $consulta->id],
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