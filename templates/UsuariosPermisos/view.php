<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\UsuariosPermiso $usuariosPermiso
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Usuarios Permiso'), ['action' => 'edit', $usuariosPermiso->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Usuarios Permiso'), ['action' => 'delete', $usuariosPermiso->id], ['confirm' => __('Are you sure you want to delete # {0}?', $usuariosPermiso->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Usuarios Permisos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Usuarios Permiso'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="usuariosPermisos view content">
            <h3><?= h($usuariosPermiso->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Usuario') ?></th>
                    <td><?= $usuariosPermiso->hasValue('usuario') ? $this->Html->link($usuariosPermiso->usuario->username, ['controller' => 'Users', 'action' => 'view', $usuariosPermiso->usuario->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Permiso') ?></th>
                    <td><?= $usuariosPermiso->hasValue('permiso') ? $this->Html->link($usuariosPermiso->permiso->controller, ['controller' => 'Permisos', 'action' => 'view', $usuariosPermiso->permiso->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($usuariosPermiso->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($usuariosPermiso->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Allow') ?></th>
                    <td><?= $usuariosPermiso->allow ? __('Yes') : __('No'); ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>