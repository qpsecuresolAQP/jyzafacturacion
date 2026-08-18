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
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $subgruposcie10->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $subgruposcie10->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Subgruposcie10'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="subgruposcie10 form content">
            <?= $this->Form->create($subgruposcie10) ?>
            <fieldset>
                <legend><?= __('Edit Subgruposcie10') ?></legend>
                <?php
                    echo $this->Form->control('clave');
                    echo $this->Form->control('descripcion');
                    echo $this->Form->control('idGrupo');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
