<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Permiso> $permisos
 */
?>
<div class="permisos index content">
    <?= $this->Html->link(__('New Permiso'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Permisos') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('controller') ?></th>
                    <th><?= $this->Paginator->sort('action') ?></th>
                    <th><?= $this->Paginator->sort('descripcion') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($permisos as $permiso): ?>
                <tr>
                    <td><?= $this->Number->format($permiso->id) ?></td>
                    <td><?= h($permiso->controller) ?></td>
                    <td><?= h($permiso->action) ?></td>
                    <td><?= h($permiso->descripcion) ?></td>
                    <td><?= h($permiso->created) ?></td>
                    <td><?= h($permiso->modified) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $permiso->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $permiso->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $permiso->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $permiso->id),
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