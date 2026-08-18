<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\CitasTratamiento> $citasTratamientos
 */
?>
<div class="citasTratamientos index content">
    <?= $this->Html->link(__('New Citas Tratamiento'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Citas Tratamientos') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('tratamiento_id') ?></th>
                    <th><?= $this->Paginator->sort('cita_id') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($citasTratamientos as $citasTratamiento): ?>
                <tr>
                    <td><?= $this->Number->format($citasTratamiento->id) ?></td>
                    <td><?= $citasTratamiento->hasValue('tratamiento') ? $this->Html->link($citasTratamiento->tratamiento->nombre, ['controller' => 'Tratamientos', 'action' => 'view', $citasTratamiento->tratamiento->id]) : '' ?></td>
                    <td><?= $citasTratamiento->hasValue('cita') ? $this->Html->link($citasTratamiento->cita->id, ['controller' => 'Citas', 'action' => 'view', $citasTratamiento->cita->id]) : '' ?></td>
                    <td><?= h($citasTratamiento->created) ?></td>
                    <td><?= h($citasTratamiento->modified) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $citasTratamiento->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $citasTratamiento->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $citasTratamiento->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $citasTratamiento->id),
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