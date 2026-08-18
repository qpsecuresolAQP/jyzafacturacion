<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\UsuariosPermiso> $usuariosPermisos
 */
?>
<div class="usuariosPermisos index content">
    <?= $this->Html->link(__('New Usuarios Permiso'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Usuarios Permisos') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('usuario_id') ?></th>
                    <th><?= $this->Paginator->sort('permiso_id') ?></th>
                    <th><?= $this->Paginator->sort('allow') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuariosPermisos as $usuariosPermiso): ?>
                <tr>
                    <td><?= $this->Number->format($usuariosPermiso->id) ?></td>
                    <td><?= $usuariosPermiso->hasValue('usuario') ? $this->Html->link($usuariosPermiso->usuario->username, ['controller' => 'Users', 'action' => 'view', $usuariosPermiso->usuario->id]) : '' ?></td>
                    <td><?= $usuariosPermiso->hasValue('permiso') ? $this->Html->link($usuariosPermiso->permiso->controller, ['controller' => 'Permisos', 'action' => 'view', $usuariosPermiso->permiso->id]) : '' ?></td>
                    <td><?= h($usuariosPermiso->allow) ?></td>
                    <td><?= h($usuariosPermiso->created) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $usuariosPermiso->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $usuariosPermiso->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $usuariosPermiso->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $usuariosPermiso->id),
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