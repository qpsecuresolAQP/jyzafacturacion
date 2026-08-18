<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DocumentosProcedimiento $documentosProcedimiento
 * @var \Cake\Collection\CollectionInterface|string[] $procedimientos
 * @var \Cake\Collection\CollectionInterface|string[] $documentos
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Documentos Procedimientos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="documentosProcedimientos form content">
            <?= $this->Form->create($documentosProcedimiento) ?>
            <fieldset>
                <legend><?= __('Add Documentos Procedimiento') ?></legend>
                <?php
                    echo $this->Form->control('procedimiento_id', ['options' => $procedimientos, 'empty' => true]);
                    echo $this->Form->control('documento_id', ['options' => $documentos, 'empty' => true]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
