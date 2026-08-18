<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DocumentosProcedimiento $documentosProcedimiento
 * @var string[]|\Cake\Collection\CollectionInterface $procedimientos
 * @var string[]|\Cake\Collection\CollectionInterface $documentos
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $documentosProcedimiento->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $documentosProcedimiento->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Documentos Procedimientos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="documentosProcedimientos form content">
            <?= $this->Form->create($documentosProcedimiento) ?>
            <fieldset>
                <legend><?= __('Edit Documentos Procedimiento') ?></legend>
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
