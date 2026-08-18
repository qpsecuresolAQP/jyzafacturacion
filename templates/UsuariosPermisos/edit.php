<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\UsuariosPermiso $usuariosPermiso
 * @var string[]|\Cake\Collection\CollectionInterface $usuarios
 * @var string[]|\Cake\Collection\CollectionInterface $permisos
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $usuariosPermiso->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $usuariosPermiso->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Usuarios Permisos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="usuariosPermisos form content">
            <?= $this->Form->create($usuariosPermiso) ?>
            <fieldset>
                <legend><?= __('Edit Usuarios Permiso') ?></legend>
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
