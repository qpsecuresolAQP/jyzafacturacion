<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\DocumentosConsulta> $documentosConsultas
 */
?>
<div class="documentosConsultas index content">
    <?= $this->Html->link(__('New Documentos Consulta'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Documentos Consultas') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('consulta_id') ?></th>
                    <th><?= $this->Paginator->sort('documento_id') ?></th>
                    <th><?= $this->Paginator->sort('ruta_archivo') ?></th>
                    <th><?= $this->Paginator->sort('descripcion') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documentosConsultas as $documentosConsulta): ?>
                <tr>
                    <td><?= $this->Number->format($documentosConsulta->id) ?></td>
                    <td><?= $documentosConsulta->hasValue('consulta') ? $this->Html->link($documentosConsulta->consulta->motivo, ['controller' => 'Consultas', 'action' => 'view', $documentosConsulta->consulta->id]) : '' ?></td>
                    <td><?= $documentosConsulta->hasValue('documento') ? $this->Html->link($documentosConsulta->documento->descripcion, ['controller' => 'Documentos', 'action' => 'view', $documentosConsulta->documento->id]) : '' ?></td>
                    <td><?= h($documentosConsulta->ruta_archivo) ?></td>
                    <td><?= h($documentosConsulta->descripcion) ?></td>
                    <td><?= h($documentosConsulta->created) ?></td>
                    <td><?= h($documentosConsulta->modified) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $documentosConsulta->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $documentosConsulta->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $documentosConsulta->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $documentosConsulta->id),
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