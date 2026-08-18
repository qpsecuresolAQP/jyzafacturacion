<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Doctore> $doctores
 */
?>
<?php $this->assign('title', 'Especialistas'); ?>

<div class="doctores index content">
    <?= $this->Html->link(__('Añadir Especialista'), ['action' => 'add'], ['class' => 'button float-right btn btn-info openModal']) ?>
    <div class="contenedor principal">
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'N° de Especialista') ?></th>
                        <th><?= $this->Paginator->sort('nombre', 'Nombre') ?></th>
                        <th><?= $this->Paginator->sort('apellido', 'Apellido') ?></th>
                        <th><?= $this->Paginator->sort('especialidad', 'Especialidad') ?></th>
                        <th><?= $this->Paginator->sort('telefono', 'Teléfono') ?></th>
                        <th><?= $this->Paginator->sort('email', 'Email') ?></th>
                        <th><?= $this->Paginator->sort('modo_pago', 'Modo de Pago') ?></th>
                        <th><?= $this->Paginator->sort('created', 'Creado') ?></th>
                        <th><?= $this->Paginator->sort('modified', 'Modificado') ?></th>
                        <th class="actions text-dark"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($doctores as $doctore): ?>
                    <tr>
                        <td><?= $this->Number->format($doctore->id) ?></td>
                        <td><?= h($doctore->nombre) ?></td>
                        <td><?= h($doctore->apellido) ?></td>
                        <td><?= h($doctore->especialidad) ?></td>
                        <td><?= h($doctore->telefono) ?></td>
                        <td><?= h($doctore->email) ?></td>
                        <td>
                            <?php if ($doctore->modo_pago === 'FIJO'): ?>
                                <span class="badge bg-secondary">Tarifa fija</span>
                            <?php else: ?>
                                <span class="badge bg-info text-dark"><?= h($doctore->porcentaje_pago) ?>%</span>
                            <?php endif; ?>
                        </td>
                        <td><?= h($doctore->created) ?></td>
                        <td><?= h($doctore->modified) ?></td>
                        <td class="actions text-center">
                            <!-- Íconos personalizados para acciones -->
                            <?= $this->Html->link(
                                '<i class="fas fa-eye"></i>',
                                ['action' => 'view', $doctore->id],
                                ['escape' => false, 'title' => 'Ver', 'class' => 'btn btn-info btn-sm openModal']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i>',
                                ['action' => 'edit', $doctore->id],
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