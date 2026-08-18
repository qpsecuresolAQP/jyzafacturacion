<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Proveedor> $proveedores
 */
?>
<?php $this->assign('title', 'Proveedores'); ?>

<div class="proveedores index content">
    <?php if ($this->Permisos->tiene('Proveedores', 'add')): ?>
        <?= $this->Html->link(__('Añadir Proveedor'), ['action' => 'add'], ['class' => 'button float-right btn btn-info openModal']) ?>
    <?php endif; ?>
    <div class="contenedor principal">
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'N°') ?></th>
                        <th><?= $this->Paginator->sort('nombre', 'Nombre') ?></th>
                        <th>WhatsApp</th>
                        <th>Correo</th>
                        <th><?= $this->Paginator->sort('activo', 'Activo') ?></th>
                        <th class="actions text-dark"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proveedores as $proveedor): ?>
                    <tr>
                        <td><?= $this->Number->format($proveedor->id) ?></td>
                        <td><?= h($proveedor->nombre) ?></td>
                        <td><?= h($proveedor->whatsapp ?: '-') ?></td>
                        <td><?= h($proveedor->email ?: '-') ?></td>
                        <td>
                            <span class="badge bg-<?= $proveedor->activo ? 'success' : 'secondary' ?>">
                                <?= $proveedor->activo ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="actions text-center">
                            <?php if ($this->Permisos->tiene('Proveedores', 'view')): ?>
                                <?= $this->Html->link(
                                    '<i class="fas fa-eye"></i>',
                                    ['action' => 'view', $proveedor->id],
                                    ['escape' => false, 'title' => 'Ver', 'class' => 'btn btn-info btn-sm openModal']
                                ) ?>
                            <?php endif; ?>
                            <?php if ($this->Permisos->tiene('Proveedores', 'edit')): ?>
                                <?= $this->Html->link(
                                    '<i class="fas fa-edit"></i>',
                                    ['action' => 'edit', $proveedor->id],
                                    ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm openModal']
                                ) ?>
                            <?php endif; ?>
                            <?php if ($this->Permisos->tiene('Proveedores', 'delete')): ?>
                                <?= $this->Form->postLink(
                                    '<i class="fas fa-trash"></i>',
                                    ['action' => 'delete', $proveedor->id],
                                    ['escape' => false, 'title' => 'Eliminar', 'class' => 'btn btn-danger btn-sm', 'confirm' => __('¿Seguro que desea eliminar el proveedor "{0}"?', $proveedor->nombre)]
                                ) ?>
                            <?php endif; ?>
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
