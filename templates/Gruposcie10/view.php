<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Gruposcie10 $gruposcie10
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Gruposcie10'), ['action' => 'edit', $gruposcie10->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Gruposcie10'), ['action' => 'delete', $gruposcie10->id], ['confirm' => __('Are you sure you want to delete # {0}?', $gruposcie10->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Gruposcie10'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Gruposcie10'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="gruposcie10 view content">
            <h3><?= h($gruposcie10->clave) ?></h3>
            <table>
                <tr>
                    <th><?= __('Clave') ?></th>
                    <td><?= h($gruposcie10->clave) ?></td>
                </tr>
                <tr>
                    <th><?= __('Descripcion') ?></th>
                    <td><?= h($gruposcie10->descripcion) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($gruposcie10->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>