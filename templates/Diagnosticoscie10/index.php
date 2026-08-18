<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Diagnosticoscie10> $diagnosticoscie10
 */
?>
<div class="diagnosticoscie10 index content">
    <?= $this->Html->link(__('New Diagnosticoscie10'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Diagnosticoscie10') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('clave') ?></th>
                    <th><?= $this->Paginator->sort('descripcion') ?></th>
                    <th><?= $this->Paginator->sort('idCategoria') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($diagnosticoscie10 as $diagnosticoscie10): ?>
                <tr>
                    <td><?= $this->Number->format($diagnosticoscie10->id) ?></td>
                    <td><?= h($diagnosticoscie10->clave) ?></td>
                    <td><?= h($diagnosticoscie10->descripcion) ?></td>
                    <td><?= $this->Number->format($diagnosticoscie10->idCategoria) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $diagnosticoscie10->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $diagnosticoscie10->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $diagnosticoscie10->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $diagnosticoscie10->id),
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