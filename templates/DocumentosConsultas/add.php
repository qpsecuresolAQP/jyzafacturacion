<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DocumentosConsulta $documentosConsulta
 * @var \Cake\Collection\CollectionInterface|string[] $consultas
 * @var \Cake\Collection\CollectionInterface|string[] $documentos
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Documentos Consultas'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="documentosConsultas form content">
            <?= $this->Form->create($documentosConsulta) ?>
            <fieldset>
                <legend><?= __('Add Documentos Consulta') ?></legend>
                <?php
                    echo $this->Form->control('consulta_id', ['options' => $consultas, 'empty' => true]);
                    echo $this->Form->control('documento_id', ['options' => $documentos, 'empty' => true]);
                    echo $this->Form->control('ruta_archivo');
                    echo $this->Form->control('descripcion');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
