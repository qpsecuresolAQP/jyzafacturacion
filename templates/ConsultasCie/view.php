<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ConsultasCie $consultasCie
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Consultas Cie'), ['action' => 'edit', $consultasCie->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Consultas Cie'), ['action' => 'delete', $consultasCie->id], ['confirm' => __('Are you sure you want to delete # {0}?', $consultasCie->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Consultas Cie'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Consultas Cie'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="consultasCie view content">
            <h3><?= h($consultasCie->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Consulta') ?></th>
                    <td><?= $consultasCie->hasValue('consulta') ? $this->Html->link($consultasCie->consulta->motivo, ['controller' => 'Consultas', 'action' => 'view', $consultasCie->consulta->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Cy') ?></th>
                    <td><?= $consultasCie->hasValue('cy') ? $this->Html->link($consultasCie->cy->clave, ['controller' => 'Diagnosticoscie10', 'action' => 'view', $consultasCie->cy->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($consultasCie->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>