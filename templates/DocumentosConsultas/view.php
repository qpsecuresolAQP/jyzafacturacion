<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DocumentosConsulta $documentosConsulta
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Documentos Consulta'), ['action' => 'edit', $documentosConsulta->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Documentos Consulta'), ['action' => 'delete', $documentosConsulta->id], ['confirm' => __('Are you sure you want to delete # {0}?', $documentosConsulta->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Documentos Consultas'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Documentos Consulta'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="documentosConsultas view content">
            <h3><?= h($documentosConsulta->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Consulta') ?></th>
                    <td><?= $documentosConsulta->hasValue('consulta') ? $this->Html->link($documentosConsulta->consulta->motivo, ['controller' => 'Consultas', 'action' => 'view', $documentosConsulta->consulta->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Documento') ?></th>
                    <td><?= $documentosConsulta->hasValue('documento') ? $this->Html->link($documentosConsulta->documento->descripcion, ['controller' => 'Documentos', 'action' => 'view', $documentosConsulta->documento->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Ruta Archivo') ?></th>
                    <td><?= h($documentosConsulta->ruta_archivo) ?></td>
                </tr>
                <tr>
                    <th><?= __('Descripcion') ?></th>
                    <td><?= h($documentosConsulta->descripcion) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($documentosConsulta->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($documentosConsulta->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($documentosConsulta->modified) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>