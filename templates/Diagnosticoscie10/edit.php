<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Diagnosticoscie10 $diagnosticoscie10
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $diagnosticoscie10->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $diagnosticoscie10->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Diagnosticoscie10'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="diagnosticoscie10 form content">
            <?= $this->Form->create($diagnosticoscie10) ?>
            <fieldset>
                <legend><?= __('Edit Diagnosticoscie10') ?></legend>
                <?php
                    echo $this->Form->control('clave');
                    echo $this->Form->control('descripcion');
                    echo $this->Form->control('idCategoria');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
