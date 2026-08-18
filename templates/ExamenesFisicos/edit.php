<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ExamenesFisico $examenesFisico
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $examenesFisico->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $examenesFisico->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Examenes Fisicos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="examenesFisicos form content">
            <?= $this->Form->create($examenesFisico) ?>
            <fieldset>
                <legend><?= __('Edit Examenes Fisico') ?></legend>
                <?php
                    echo $this->Form->control('edad');
                    echo $this->Form->control('peso');
                    echo $this->Form->control('altura');
                    echo $this->Form->control('temperatura');
                    echo $this->Form->control('eva');
                    echo $this->Form->control('presion');
                    echo $this->Form->control('frecuencia_cardiaca');
                    echo $this->Form->control('saturacion');
                    echo $this->Form->control('glicemina');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
