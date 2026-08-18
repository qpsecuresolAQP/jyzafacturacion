<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Procedimiento> $procedimientos
 */
?>
<div class="procedimientos index content">
    <?= $this->Html->link(__('New Procedimiento'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Procedimientos') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('doctor_id') ?></th>
                    <th><?= $this->Paginator->sort('historia_id') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($procedimientos as $procedimiento): ?>
                <tr>
                    <td><?= $this->Number->format($procedimiento->id) ?></td>
                    <td><?= $procedimiento->doctor_id === null ? '' : $this->Number->format($procedimiento->doctor_id) ?></td>
                    <td><?= $procedimiento->historia_id === null ? '' : $this->Number->format($procedimiento->historia_id) ?></td>
                    <td><?= h($procedimiento->created) ?></td>
                    <td><?= h($procedimiento->modified) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $procedimiento->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $procedimiento->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $procedimiento->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $procedimiento->id),
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