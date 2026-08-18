<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ConsultasCie $consultasCie
 * @var string[]|\Cake\Collection\CollectionInterface $consultas
 * @var string[]|\Cake\Collection\CollectionInterface $cies
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $consultasCie->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $consultasCie->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Consultas Cie'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="consultasCie form content">
            <?= $this->Form->create($consultasCie) ?>
            <fieldset>
                <legend><?= __('Edit Consultas Cie') ?></legend>
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
