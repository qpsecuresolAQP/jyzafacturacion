<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Permiso $permiso
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Permiso'), ['action' => 'edit', $permiso->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Permiso'), ['action' => 'delete', $permiso->id], ['confirm' => __('Are you sure you want to delete # {0}?', $permiso->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Permisos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Permiso'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="permisos view content">
            <h3><?= h($permiso->controller) ?></h3>
            <table>
                <tr>
                    <th><?= __('Controller') ?></th>
                    <td><?= h($permiso->controller) ?></td>
                </tr>
                <tr>
                    <th><?= __('Action') ?></th>
                    <td><?= h($permiso->action) ?></td>
                </tr>
                <tr>
                    <th><?= __('Descripcion') ?></th>
                    <td><?= h($permiso->descripcion) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($permiso->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($permiso->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($permiso->modified) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Roles') ?></h4>
                <?php if (!empty($permiso->roles)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Nombre') ?></th>
                            <th><?= __('Descripcion') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Modified') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($permiso->roles as $role) : ?>
                        <tr>
                            <td><?= h($role->id) ?></td>
                            <td><?= h($role->nombre) ?></td>
                            <td><?= h($role->descripcion) ?></td>
                            <td><?= h($role->created) ?></td>
                            <td><?= h($role->modified) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Roles', 'action' => 'view', $role->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Roles', 'action' => 'edit', $role->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'Roles', 'action' => 'delete', $role->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $role->id),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Usuarios Permisos') ?></h4>
                <?php if (!empty($permiso->usuarios_permisos)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Usuario Id') ?></th>
                            <th><?= __('Permiso Id') ?></th>
                            <th><?= __('Allow') ?></th>
                            <th><?= __('Created') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($permiso->usuarios_permisos as $usuariosPermiso) : ?>
                        <tr>
                            <td><?= h($usuariosPermiso->id) ?></td>
                            <td><?= h($usuariosPermiso->usuario_id) ?></td>
                            <td><?= h($usuariosPermiso->permiso_id) ?></td>
                            <td><?= h($usuariosPermiso->allow) ?></td>
                            <td><?= h($usuariosPermiso->created) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'UsuariosPermisos', 'action' => 'view', $usuariosPermiso->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'UsuariosPermisos', 'action' => 'edit', $usuariosPermiso->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'UsuariosPermisos', 'action' => 'delete', $usuariosPermiso->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $usuariosPermiso->id),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>