<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Subgruposcie10 $subgruposcie10
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Subgruposcie10'), ['action' => 'edit', $subgruposcie10->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Subgruposcie10'), ['action' => 'delete', $subgruposcie10->id], ['confirm' => __('Are you sure you want to delete # {0}?', $subgruposcie10->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Subgruposcie10'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Subgruposcie10'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="subgruposcie10 view content">
            <h3><?= h($subgruposcie10->clave) ?></h3>
            <table>
                <tr>
                    <th><?= __('Clave') ?></th>
                    <td><?= h($subgruposcie10->clave) ?></td>
                </tr>
                <tr>
                    <th><?= __('Descripcion') ?></th>
                    <td><?= h($subgruposcie10->descripcion) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($subgruposcie10->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('IdGrupo') ?></th>
                    <td><?= $this->Number->format($subgruposcie10->idGrupo) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>