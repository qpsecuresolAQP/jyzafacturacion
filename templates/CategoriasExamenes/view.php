<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CategoriasExamene $categoriasExamene
 */
?>
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3 flex-wrap gap-2">
                <h3 class="text-info mb-0"><i class="fas fa-flask"></i> Categoría: <?= h($categoriasExamene->nombre) ?></h3>

                <?php if ($this->Permisos->tiene('Examenes', 'add')): ?>
                    <?= $this->Html->link(
                        '<i class="fas fa-plus"></i> Añadir Examen',
                        [
                            'controller' => 'Examenes',
                            'action' => 'add',
                            '?' => ['categoria_examen_id' => $categoriasExamene->id],
                        ],
                        ['escape' => false, 'class' => 'btn btn-info btn-sm openModal']
                    ) ?>
                <?php endif; ?>
            </div>

            <div class="row mb-4">
                <div class="col-md-3"><strong>Estado:</strong></div>
                <div class="col-md-9">
                    <span class="badge bg-<?= $categoriasExamene->estado ? 'success' : 'secondary' ?>">
                        <?= $categoriasExamene->estado ? 'Activo' : 'Inactivo' ?>
                    </span>
                </div>
            </div>

            <h5 class="text-info">Exámenes en esta categoría</h5>
            <div class="table-responsive">
                <table class="table table-striped mt-2">
                    <thead class="bg-info text-white">
                        <tr>
                            <th>Nombre</th>
                            <th>Muestra</th>
                            <th>Precio (S/)</th>
                            <th>Precio Convenio (S/)</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categoriasExamene->examenes)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No hay exámenes registrados en esta categoría.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categoriasExamene->examenes as $examen): ?>
                                <tr>
                                    <td><?= h($examen->nombre) ?></td>
                                    <td><?= h($examen->muestra ?: '-') ?></td>
                                    <td><?= number_format((float)$examen->precio, 2) ?></td>
                                    <td><?= number_format((float)$examen->precio_convenio, 2) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $examen->estado ? 'success' : 'secondary' ?>">
                                            <?= $examen->estado ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($this->Permisos->tiene('Examenes', 'edit')): ?>
                                            <?= $this->Html->link(
                                                '<i class="fas fa-edit"></i>',
                                                ['controller' => 'Examenes', 'action' => 'edit', $examen->id],
                                                ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm openModal']
                                            ) ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-4">
                <?= $this->Html->link(__('Volver a la Lista'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                <?= $this->Html->link(__('Editar Categoría'), ['action' => 'edit', $categoriasExamene->id], ['class' => 'btn btn-primary ms-2']) ?>
            </div>
        </div>
    </div>
</div>
