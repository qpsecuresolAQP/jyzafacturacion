<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Diagnosticoscie10 $diagnosticoscie10
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Diagnosticoscie10'), ['action' => 'edit', $diagnosticoscie10->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Diagnosticoscie10'), ['action' => 'delete', $diagnosticoscie10->id], ['confirm' => __('Are you sure you want to delete # {0}?', $diagnosticoscie10->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Diagnosticoscie10'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Diagnosticoscie10'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="diagnosticoscie10 view content">
            <h3><?= h($diagnosticoscie10->clave) ?></h3>
            <table>
                <tr>
                    <th><?= __('Clave') ?></th>
                    <td><?= h($diagnosticoscie10->clave) ?></td>
                </tr>
                <tr>
                    <th><?= __('Descripcion') ?></th>
                    <td><?= h($diagnosticoscie10->descripcion) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($diagnosticoscie10->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('IdCategoria') ?></th>
                    <td><?= $this->Number->format($diagnosticoscie10->idCategoria) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>