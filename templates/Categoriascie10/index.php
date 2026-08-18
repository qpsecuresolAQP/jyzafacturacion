<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Categoriascie10> $categoriascie10
 */
?>
<div class="categoriascie10 index content">
    <?= $this->Html->link(__('New Categoriascie10'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Categoriascie10') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('clave') ?></th>
                    <th><?= $this->Paginator->sort('descripcion') ?></th>
                    <th><?= $this->Paginator->sort('idSubGrupo') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categoriascie10 as $categoriascie10): ?>
                <tr>
                    <td><?= $this->Number->format($categoriascie10->id) ?></td>
                    <td><?= h($categoriascie10->clave) ?></td>
                    <td><?= h($categoriascie10->descripcion) ?></td>
                    <td><?= $this->Number->format($categoriascie10->idSubGrupo) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $categoriascie10->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $categoriascie10->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $categoriascie10->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $categoriascie10->id),
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