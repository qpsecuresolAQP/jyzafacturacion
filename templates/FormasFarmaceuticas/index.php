<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FormasFarmaceutica[]|\Cake\Datasource\ResultSetInterface $formasFarmaceuticas
 */
?>

<div class="formasfarmaceuticas index content">
    <?= $this->Html->link(__('Añadir Forma Farmacéutica'), ['action' => 'add'], ['class' => 'button float-right btn btn-info openModal']) ?>
    <div class="contenedor principal">
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('nombre', 'Nombre') ?></th>
                        <th><?= $this->Paginator->sort('descripcion', 'Descripción') ?></th>
                        <th><?= $this->Paginator->sort('activa', 'Estado') ?></th>
                        <th><?= $this->Paginator->sort('created', 'Creado') ?></th>
                        <th><?= $this->Paginator->sort('modified', 'Modificado') ?></th>
                        <th class="actions text-dark"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($formasFarmaceuticas as $formaFarmaceutica): ?>
                    <tr>
                        <td><?= h($formaFarmaceutica->nombre) ?></td>
                        <td><?= h($formaFarmaceutica->descripcion) ?: '-' ?></td>
                        <td>
                            <?php if ($formaFarmaceutica->activa): ?>
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i> Activa
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-times"></i> Inactiva
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?= h($formaFarmaceutica->created) ?></td>
                        <td><?= h($formaFarmaceutica->modified) ?></td>
                        <td class="actions text-center">
                            <!-- Íconos personalizados para acciones -->
                            <?= $this->Html->link(
                                '<i class="fas fa-eye"></i>',
                                ['action' => 'view', $formaFarmaceutica->id],
                                ['escape' => false, 'title' => 'Ver', 'class' => 'btn btn-info btn-sm openModal']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i>',
                                ['action' => 'edit', $formaFarmaceutica->id],
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
