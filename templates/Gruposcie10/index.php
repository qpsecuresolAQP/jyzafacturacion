<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Gruposcie10> $gruposcie10
 */
?>
<div class="gruposcie10 index content">
    <?= $this->Html->link(__('New Gruposcie10'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Gruposcie10') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('clave') ?></th>
                    <th><?= $this->Paginator->sort('descripcion') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gruposcie10 as $gruposcie10): ?>
                <tr>
                    <td><?= $this->Number->format($gruposcie10->id) ?></td>
                    <td><?= h($gruposcie10->clave) ?></td>
                    <td><?= h($gruposcie10->descripcion) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $gruposcie10->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $gruposcie10->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $gruposcie10->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $gruposcie10->id),
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