<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\DocumentosProcedimiento> $documentosProcedimientos
 */
?>
<div class="documentosProcedimientos index content">
    <?= $this->Html->link(__('New Documentos Procedimiento'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Documentos Procedimientos') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('procedimiento_id') ?></th>
                    <th><?= $this->Paginator->sort('documento_id') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documentosProcedimientos as $documentosProcedimiento): ?>
                <tr>
                    <td><?= $this->Number->format($documentosProcedimiento->id) ?></td>
                    <td><?= $documentosProcedimiento->hasValue('procedimiento') ? $this->Html->link($documentosProcedimiento->procedimiento->id, ['controller' => 'Procedimientos', 'action' => 'view', $documentosProcedimiento->procedimiento->id]) : '' ?></td>
                    <td><?= $documentosProcedimiento->hasValue('documento') ? $this->Html->link($documentosProcedimiento->documento->descripcion, ['controller' => 'Documentos', 'action' => 'view', $documentosProcedimiento->documento->id]) : '' ?></td>
                    <td><?= h($documentosProcedimiento->created) ?></td>
                    <td><?= h($documentosProcedimiento->modified) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $documentosProcedimiento->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $documentosProcedimiento->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $documentosProcedimiento->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $documentosProcedimiento->id),
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