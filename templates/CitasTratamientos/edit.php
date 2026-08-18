<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CitasTratamiento $citasTratamiento
 * @var string[]|\Cake\Collection\CollectionInterface $tratamientos
 * @var string[]|\Cake\Collection\CollectionInterface $citas
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $citasTratamiento->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $citasTratamiento->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Citas Tratamientos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="citasTratamientos form content">
            <?= $this->Form->create($citasTratamiento) ?>
            <fieldset>
                <legend><?= __('Edit Citas Tratamiento') ?></legend>
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
