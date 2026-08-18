<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ConsultasCie $consultasCie
 * @var \Cake\Collection\CollectionInterface|string[] $consultas
 * @var \Cake\Collection\CollectionInterface|string[] $cies
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Consultas Cie'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="consultasCie form content">
            <?= $this->Form->create($consultasCie) ?>
            <fieldset>
                <legend><?= __('Add Consultas Cie') ?></legend>
                <?php
                    echo $this->Form->control('consulta_id', ['options' => $consultas]);
                    echo $this->Form->control('cie_id', ['options' => $cies]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
