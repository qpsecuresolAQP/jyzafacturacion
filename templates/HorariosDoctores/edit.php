<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\HorariosDoctore $horariosDoctore
 * @var string[]|\Cake\Collection\CollectionInterface $doctors
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $horariosDoctore->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $horariosDoctore->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Horarios Doctores'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="horariosDoctores form content">
            <?= $this->Form->create($horariosDoctore) ?>
            <fieldset>
                <legend><?= __('Edit Horarios Doctore') ?></legend>
                <?php
                    echo $this->Form->control('doctor_id', ['options' => $doctores]);
                    echo $this->Form->control('dia_semana');
                    echo $this->Form->control('hora_inicio');
                    echo $this->Form->control('hora_fin');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
