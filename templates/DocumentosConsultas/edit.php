<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DocumentosConsulta $documentosConsulta
 * @var string[]|\Cake\Collection\CollectionInterface $consultas
 * @var string[]|\Cake\Collection\CollectionInterface $documentos
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $documentosConsulta->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $documentosConsulta->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Documentos Consultas'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="documentosConsultas form content">
            <?= $this->Form->create($documentosConsulta) ?>
            <fieldset>
                <legend><?= __('Edit Documentos Consulta') ?></legend>
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
