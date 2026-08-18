<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RecetasConsulta $recetasConsulta
 * @var \Cake\Collection\CollectionInterface|string[] $consultas
 * @var \Cake\Collection\CollectionInterface|string[] $recetas
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Recetas Consultas'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="recetasConsultas form content">
            <?= $this->Form->create($recetasConsulta) ?>
            <fieldset>
                <legend><?= __('Add Recetas Consulta') ?></legend>
                <?php
                    echo $this->Form->control('consulta_id', ['options' => $consultas]);
                    echo $this->Form->control('receta_id', ['options' => $recetas]);
                    echo $this->Form->control('observaciones');
                    echo $this->Form->control('descripcion');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
