<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DocumentosProcedimiento $documentosProcedimiento
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Documentos Procedimiento'), ['action' => 'edit', $documentosProcedimiento->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Documentos Procedimiento'), ['action' => 'delete', $documentosProcedimiento->id], ['confirm' => __('Are you sure you want to delete # {0}?', $documentosProcedimiento->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Documentos Procedimientos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Documentos Procedimiento'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="documentosProcedimientos view content">
            <h3><?= h($documentosProcedimiento->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Procedimiento') ?></th>
                    <td><?= $documentosProcedimiento->hasValue('procedimiento') ? $this->Html->link($documentosProcedimiento->procedimiento->id, ['controller' => 'Procedimientos', 'action' => 'view', $documentosProcedimiento->procedimiento->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Documento') ?></th>
                    <td><?= $documentosProcedimiento->hasValue('documento') ? $this->Html->link($documentosProcedimiento->documento->descripcion, ['controller' => 'Documentos', 'action' => 'view', $documentosProcedimiento->documento->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($documentosProcedimiento->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($documentosProcedimiento->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($documentosProcedimiento->modified) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>