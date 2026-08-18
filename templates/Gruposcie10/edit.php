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
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $gruposcie10->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $gruposcie10->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Gruposcie10'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="gruposcie10 form content">
            <?= $this->Form->create($gruposcie10) ?>
            <fieldset>
                <legend><?= __('Edit Gruposcie10') ?></legend>
                <?php
                    echo $this->Form->control('clave');
                    echo $this->Form->control('descripcion');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
