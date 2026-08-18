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
            <?= $this->Html->link(__('List Categoriascie10'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="categoriascie10 form content">
            <?= $this->Form->create($categoriascie10) ?>
            <fieldset>
                <legend><?= __('Add Categoriascie10') ?></legend>
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
