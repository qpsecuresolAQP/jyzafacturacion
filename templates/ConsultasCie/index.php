<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\ConsultasCie> $consultasCie
 */
?>
<div class="consultasCie index content">
    <?= $this->Html->link(__('New Consultas Cie'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Consultas Cie') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('consulta_id') ?></th>
                    <th><?= $this->Paginator->sort('cie_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($consultasCie as $consultasCie): ?>
                <tr>
                    <td><?= $this->Number->format($consultasCie->id) ?></td>
                    <td><?= $consultasCie->hasValue('consulta') ? $this->Html->link($consultasCie->consulta->motivo, ['controller' => 'Consultas', 'action' => 'view', $consultasCie->consulta->id]) : '' ?></td>
                    <td><?= $consultasCie->hasValue('cy') ? $this->Html->link($consultasCie->cy->clave, ['controller' => 'Diagnosticoscie10', 'action' => 'view', $consultasCie->cy->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $consultasCie->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $consultasCie->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $consultasCie->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $consultasCie->id),
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