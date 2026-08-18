<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\ExamenesFisico> $examenesFisicos
 */
?>
<div class="examenesFisicos index content">
    <?= $this->Html->link(__('New Examenes Fisico'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Examenes Fisicos') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('edad') ?></th>
                    <th><?= $this->Paginator->sort('peso') ?></th>
                    <th><?= $this->Paginator->sort('altura') ?></th>
                    <th><?= $this->Paginator->sort('temperatura') ?></th>
                    <th><?= $this->Paginator->sort('eva') ?></th>
                    <th><?= $this->Paginator->sort('presion') ?></th>
                    <th><?= $this->Paginator->sort('frecuencia_cardiaca') ?></th>
                    <th><?= $this->Paginator->sort('saturacion') ?></th>
                    <th><?= $this->Paginator->sort('glicemina') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($examenesFisicos as $examenesFisico): ?>
                <tr>
                    <td><?= $this->Number->format($examenesFisico->id) ?></td>
                    <td><?= $examenesFisico->edad === null ? '' : $this->Number->format($examenesFisico->edad) ?></td>
                    <td><?= $examenesFisico->peso === null ? '' : $this->Number->format($examenesFisico->peso) ?></td>
                    <td><?= $examenesFisico->altura === null ? '' : $this->Number->format($examenesFisico->altura) ?></td>
                    <td><?= $examenesFisico->temperatura === null ? '' : $this->Number->format($examenesFisico->temperatura) ?></td>
                    <td><?= $examenesFisico->eva === null ? '' : $this->Number->format($examenesFisico->eva) ?></td>
                    <td><?= $examenesFisico->presion === null ? '' : $this->Number->format($examenesFisico->presion) ?></td>
                    <td><?= $examenesFisico->frecuencia_cardiaca === null ? '' : $this->Number->format($examenesFisico->frecuencia_cardiaca) ?></td>
                    <td><?= $examenesFisico->saturacion === null ? '' : $this->Number->format($examenesFisico->saturacion) ?></td>
                    <td><?= $examenesFisico->glicemina === null ? '' : $this->Number->format($examenesFisico->glicemina) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $examenesFisico->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $examenesFisico->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $examenesFisico->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $examenesFisico->id),
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