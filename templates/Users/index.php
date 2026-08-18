<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\User> $users
 * @var string $searchTerm
 */
?>

<?php $this->assign('title', 'Usuarios'); ?>

<div class="users index content">
    <!-- Botón Agregar (solo si tiene permiso) -->
    <?php if ($this->Permisos->tiene('Users', 'add')): ?>
        <?= $this->Html->link(__('Añadir Usuario'), ['action' => 'add'], ['class' => 'button float-right btn btn-info openModal']) ?>
    <?php endif; ?>
    
    <div class="contenedor principal">
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'ID') ?></th>
                        <th><?= $this->Paginator->sort('username', 'Usuario') ?></th>
                        <th><?= $this->Paginator->sort('rol_id', 'Rol') ?></th>
                        <th><?= $this->Paginator->sort('estado_user', 'Estado') ?></th>
                        <th><?= $this->Paginator->sort('created', 'Creado') ?></th>
                        <th><?= $this->Paginator->sort('modified', 'Modificado') ?></th>
                        <th class="actions text-dark"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $this->Number->format($user->id) ?></td>
                        <td><?= h($user->username) ?></td>
                        <td><?= h($roles[$user->rol_id] ?? 'Sin rol') ?></td>
                        <td>
                            <?php 
                                $estado = $user->estado_user === 'A' ? 'Activo' : 'Inactivo';
                                $badge = $user->estado_user === 'A' ? 'badge-success' : 'badge-danger';
                                echo '<span class="badge ' . $badge . '">' . h($estado) . '</span>';
                            ?>
                        </td>
                        <td><?= h($user->created) ?></td>
                        <td><?= h($user->modified) ?></td>
                        
                        <td class="actions text-center">
                            <!-- Ver (solo si tiene permiso) -->
                            <?php if ($this->Permisos->tiene('Users', 'view')): ?>
                                <?= $this->Html->link(
                                    '<i class="fas fa-eye"></i>',
                                    ['action' => 'view', $user->id],
                                    ['escape' => false, 'title' => 'Ver', 'class' => 'btn btn-info btn-sm openModal']
                                    ) ?>
                            <?php endif; ?>
                            
                            <!-- Editar (solo si tiene permiso) -->
                            <?php if ($this->Permisos->tiene('Users', 'edit')): ?>
                                <?= $this->Html->link(
                                    '<i class="fas fa-edit"></i>',
                                    ['action' => 'edit', $user->id],
                                    ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm openModal']
                                ) ?>
                            <?php endif; ?>
                            
                            <!-- Activar/Inactivar (solo si tiene permiso) -->
                            <?php if ($this->Permisos->tiene('Users', 'toggleStatus')): ?>
                                <?php 
                                    $buttonText = $user->estado_user === 'A' ? '<i class="fas fa-times"></i>' : '<i class="fas fa-check"></i>';
                                    $buttonClass = $user->estado_user === 'A' ? 'btn btn-danger' : 'btn btn-success';
                                    $buttonTitle = $user->estado_user === 'A' ? 'Inactivar' : 'Activar';
                                ?>
                                <?= $this->Html->link(
                                    $buttonText,
                                    ['action' => 'toggleStatus', $user->id],
                                    ['escape' => false, 'title' => $buttonTitle, 'class' => $buttonClass . ' btn-sm',
                                    'confirm' => '¿Estás seguro? El usuario será ' . ($user->estado_user === 'A' ? 'inactivado' : 'activado')]
                                ) ?>
                            <?php endif; ?>
                            
                            <!-- Permisos (solo si tiene permiso) -->
                            <?php if ($this->Permisos->tiene('Users', 'permisos')): ?>
                                <?= $this->Html->link(
                                    '<i class="fas fa-lock"></i>',
                                    ['action' => 'permisos', $user->id],
                                    ['escape' => false, 'title' => 'Gestionar Permisos', 'class' => 'btn btn-secondary btn-sm']
                                ) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('Primero')) ?>
            <?= $this->Paginator->prev('< ' . __('Anterior')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('Siguiente') . ' >') ?>
            <?= $this->Paginator->last(__('Último') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registro(s) de un total de {{count}}')) ?></p>
    </div>
</div>