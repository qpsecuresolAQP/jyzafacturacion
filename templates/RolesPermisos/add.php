<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RolesPermiso $rolesPermiso
 * @var \Cake\Collection\CollectionInterface|string[] $rols
 * @var \Cake\Collection\CollectionInterface|string[] $permisos
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Roles Permisos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="rolesPermisos form content">
            <?= $this->Form->create($rolesPermiso) ?>
            <fieldset>
                <legend><?= __('Add Roles Permiso') ?></legend>
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
