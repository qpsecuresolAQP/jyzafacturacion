<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CitasTratamiento $citasTratamiento
 * @var \Cake\Collection\CollectionInterface|string[] $tratamientos
 * @var \Cake\Collection\CollectionInterface|string[] $citas
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Citas Tratamientos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="citasTratamientos form content">
            <?= $this->Form->create($citasTratamiento) ?>
            <fieldset>
                <legend><?= __('Add Citas Tratamiento') ?></legend>
                <?php
                    echo $this->Form->control('tratamiento_id', ['options' => $tratamientos, 'empty' => true]);
                    echo $this->Form->control('cita_id', ['options' => $citas, 'empty' => true]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
