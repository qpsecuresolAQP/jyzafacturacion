<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Role> $roles
 */
?>


<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Roles</h3>
                        <div class="card-tools">
                            <?= $this->Html->link(
                                '<i class="fas fa-plus"></i> Agregar Rol',
                                ['action' => 'add'],
                                ['class' => 'btn btn-primary btn-sm', 'escape' => false]
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-magic"></i> Crear con Plantilla',
                                ['action' => 'createWithDefaults'],
                                ['class' => 'btn btn-info btn-sm', 'escape' => false]
                            ) ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($roles)) : ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><?= $this->Paginator->sort('id', 'ID') ?></th>
                                        <th><?= $this->Paginator->sort('nombre', 'Nombre') ?></th>
                                        <th><?= $this->Paginator->sort('descripcion', 'Descripción') ?></th>
                                        <th><?= $this->Paginator->sort('created', 'Creado') ?></th>
                                        <th class="text-center" style="width: 150px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($roles as $role): ?>
                                    <tr>
                                        <td><?= $this->Number->format($role->id) ?></td>
                                        <td>
                                            <strong><?= h($role->nombre) ?></strong>
                                        </td>
                                        <td><?= h($role->descripcion) ?></td>
                                        <td><?= $role->created->format('d/m/Y H:i') ?></td>
                                        <td class="text-center">
                                            <?= $this->Html->link(
                                                '<i class="fas fa-eye"></i>',
                                                ['action' => 'view', $role->id],
                                                ['class' => 'btn btn-info btn-sm', 'escape' => false, 'title' => 'Ver']
                                            ) ?>
                                            <?= $this->Html->link(
                                                '<i class="fas fa-edit"></i>',
                                                ['action' => 'edit', $role->id],
                                                ['class' => 'btn btn-warning btn-sm', 'escape' => false, 'title' => 'Editar']
                                            ) ?>
                                            <?= $this->Form->postLink(
                                                '<i class="fas fa-trash"></i>',
                                                ['action' => 'delete', $role->id],
                                                [
                                                    'class' => 'btn btn-danger btn-sm',
                                                    'escape' => false,
                                                    'title' => 'Eliminar',
                                                    'confirm' => '¿Estás seguro de eliminar este rol?'
                                                ]
                                            ) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-muted">
                                    Mostrando <?= count($roles) ?> de <?= $this->Paginator->counter('{count}') ?> roles
                                </p>
                            </div>
                            <div class="col-md-6">
                                <nav>
                                    <ul class="pagination justify-content-end">
                                        <?= $this->Paginator->first('Primera') ?>
                                        <?= $this->Paginator->prev('Anterior') ?>
                                        <?= $this->Paginator->numbers() ?>
                                        <?= $this->Paginator->next('Siguiente') ?>
                                        <?= $this->Paginator->last('Última') ?>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">
                            <p>No hay roles disponibles.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>