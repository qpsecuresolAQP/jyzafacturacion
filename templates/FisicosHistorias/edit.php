<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FisicosHistoria $fisicosHistoria
 * @var string[]|\Cake\Collection\CollectionInterface $examens
 * @var string[]|\Cake\Collection\CollectionInterface $historias
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $fisicosHistoria->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $fisicosHistoria->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Fisicos Historias'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="fisicosHistorias form content">
            <?= $this->Form->create($fisicosHistoria) ?>
            <fieldset>
                <legend><?= __('Edit Fisicos Historia') ?></legend>
                <?php
                    echo $this->Form->control('examen_id', ['options' => $examens]);
                    echo $this->Form->control('historia_id', ['options' => $historias]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
