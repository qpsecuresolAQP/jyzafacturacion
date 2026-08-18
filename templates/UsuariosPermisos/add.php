<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\UsuariosPermiso $usuariosPermiso
 * @var \Cake\Collection\CollectionInterface|string[] $usuarios
 * @var \Cake\Collection\CollectionInterface|string[] $permisos
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Usuarios Permisos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="usuariosPermisos form content">
            <?= $this->Form->create($usuariosPermiso) ?>
            <fieldset>
                <legend><?= __('Add Usuarios Permiso') ?></legend>
                <?php
                    echo $this->Form->control('usuario_id', ['options' => $usuarios]);
                    echo $this->Form->control('permiso_id', ['options' => $permisos]);
                    echo $this->Form->control('allow');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
