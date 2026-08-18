<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Categoriascie10 $categoriascie10
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Categoriascie10'), ['action' => 'edit', $categoriascie10->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Categoriascie10'), ['action' => 'delete', $categoriascie10->id], ['confirm' => __('Are you sure you want to delete # {0}?', $categoriascie10->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Categoriascie10'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Categoriascie10'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="categoriascie10 view content">
            <h3><?= h($categoriascie10->clave) ?></h3>
            <table>
                <tr>
                    <th><?= __('Clave') ?></th>
                    <td><?= h($categoriascie10->clave) ?></td>
                </tr>
                <tr>
                    <th><?= __('Descripcion') ?></th>
                    <td><?= h($categoriascie10->descripcion) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($categoriascie10->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('IdSubGrupo') ?></th>
                    <td><?= $this->Number->format($categoriascie10->idSubGrupo) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>