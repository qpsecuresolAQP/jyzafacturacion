<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\RolesPermiso> $rolesPermisos
 */
?>
<div class="rolesPermisos index content">
    <?= $this->Html->link(__('New Roles Permiso'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Roles Permisos') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('rol_id') ?></th>
                    <th><?= $this->Paginator->sort('permiso_id') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rolesPermisos as $rolesPermiso): ?>
                <tr>
                    <td><?= $this->Number->format($rolesPermiso->id) ?></td>
                    <td><?= $rolesPermiso->hasValue('rol') ? $this->Html->link($rolesPermiso->rol->nombre, ['controller' => 'Roles', 'action' => 'view', $rolesPermiso->rol->id]) : '' ?></td>
                    <td><?= $rolesPermiso->hasValue('permiso') ? $this->Html->link($rolesPermiso->permiso->controller, ['controller' => 'Permisos', 'action' => 'view', $rolesPermiso->permiso->id]) : '' ?></td>
                    <td><?= h($rolesPermiso->created) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $rolesPermiso->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $rolesPermiso->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $rolesPermiso->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $rolesPermiso->id),
                            ]
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>