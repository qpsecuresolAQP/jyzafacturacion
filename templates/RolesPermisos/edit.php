<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RolesPermiso $rolesPermiso
 * @var string[]|\Cake\Collection\CollectionInterface $rols
 * @var string[]|\Cake\Collection\CollectionInterface $permisos
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $rolesPermiso->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $rolesPermiso->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Roles Permisos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="rolesPermisos form content">
            <?= $this->Form->create($rolesPermiso) ?>
            <fieldset>
                <legend><?= __('Edit Roles Permiso') ?></legend>
                <?php
                    echo $this->Form->control('rol_id', ['options' => $rols]);
                    echo $this->Form->control('permiso_id', ['options' => $permisos]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
