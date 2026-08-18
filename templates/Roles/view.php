<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role $role
 */
?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <!-- Información del Rol -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Detalles del Rol</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Nombre:</strong></p>
                                <p><?= h($role->nombre) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>ID:</strong></p>
                                <p><?= $this->Number->format($role->id) ?></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <p><strong>Descripción:</strong></p>
                                <p><?= h($role->descripcion) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permisos Asignados -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Permisos Asignados (<?= count($role->permisos) ?>)</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($role->permisos)) : ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Controlador</th>
                                        <th>Acción</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($role->permisos as $permiso) : ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-primary"><?= h($permiso->controller) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-success"><?= h($permiso->action) ?></span>
                                        </td>
                                        <td><?= h($permiso->descripcion) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning">
                            <p>Este rol no tiene permisos asignados.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-cog"></i> Acciones</h3>
                    </div>
                    <div class="card-body">
                        <p>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i> Editar Rol',
                                ['action' => 'edit', $role->id],
                                ['class' => 'btn btn-warning btn-block mb-2', 'escape' => false]
                            ) ?>
                        </p>
                        <p>
                            <?= $this->Html->link(
                                '<i class="fas fa-list"></i> Volver a Roles',
                                ['action' => 'index'],
                                ['class' => 'btn btn-secondary btn-block', 'escape' => false]
                            ) ?>
                        </p>
                    </div>
                </div>

                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-trash"></i> Peligro</h3>
                    </div>
                    <div class="card-body">
                        <p>¿Deseas eliminar este rol?</p>
                        <?= $this->Form->postLink(
                            '<i class="fas fa-trash"></i> Eliminar',
                            ['action' => 'delete', $role->id],
                            [
                                'class' => 'btn btn-danger btn-block',
                                'escape' => false,
                                'confirm' => '¿Estás seguro?'
                            ]
                        ) ?>
                    </div>
                </div>

                <div class="card card-light">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calendar"></i> Información</h3>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Creado:</strong><br />
                            <?= $role->created->format('d/m/Y H:i') ?>
                        </p>
                        <p>
                            <strong>Modificado:</strong><br />
                            <?= $role->modified->format('d/m/Y H:i') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
            </div>
        </div>
    </div>
</div>