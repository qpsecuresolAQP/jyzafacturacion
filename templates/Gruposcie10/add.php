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
            <?= $this->Html->link(__('List Gruposcie10'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="gruposcie10 form content">
            <?= $this->Form->create($gruposcie10) ?>
            <fieldset>
                <legend><?= __('Add Gruposcie10') ?></legend>
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
