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
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $categoriascie10->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $categoriascie10->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Categoriascie10'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="categoriascie10 form content">
            <?= $this->Form->create($categoriascie10) ?>
            <fieldset>
                <legend><?= __('Edit Categoriascie10') ?></legend>
                <?php
                    echo $this->Form->control('clave');
                    echo $this->Form->control('descripcion');
                    echo $this->Form->control('idSubGrupo');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
