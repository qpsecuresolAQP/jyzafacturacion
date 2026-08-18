<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RolesPermiso $rolesPermiso
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Roles Permiso'), ['action' => 'edit', $rolesPermiso->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Roles Permiso'), ['action' => 'delete', $rolesPermiso->id], ['confirm' => __('Are you sure you want to delete # {0}?', $rolesPermiso->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Roles Permisos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Roles Permiso'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="rolesPermisos view content">
            <h3><?= h($rolesPermiso->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Rol') ?></th>
                    <td><?= $rolesPermiso->hasValue('rol') ? $this->Html->link($rolesPermiso->rol->nombre, ['controller' => 'Roles', 'action' => 'view', $rolesPermiso->rol->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Permiso') ?></th>
                    <td><?= $rolesPermiso->hasValue('permiso') ? $this->Html->link($rolesPermiso->permiso->controller, ['controller' => 'Permisos', 'action' => 'view', $rolesPermiso->permiso->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($rolesPermiso->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($rolesPermiso->created) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>