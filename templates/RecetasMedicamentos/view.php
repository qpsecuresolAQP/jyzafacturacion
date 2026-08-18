<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RecetaMedicamento $recetaMedicamento
 */
?>
<div class="row mt-4 mb-4">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h3 class="mb-0"><i class="fas fa-prescription-bottle me-2"></i> Detalles del Medicamento</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Receta:</h6>
                        <p class="fw-bold">
                            <?= $this->Html->link(
                                $recetaMedicamento->receta->nombre ?? 'Receta #' . $recetaMedicamento->receta->id,
                                ['controller' => 'Recetas', 'action' => 'view', $recetaMedicamento->receta->id]
                            ) ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Medicamento:</h6>
                        <p class="fw-bold"><?= h($recetaMedicamento->medicamento->nombre) ?></p>
                        <small class="text-muted">Código: <?= h($recetaMedicamento->medicamento->codigo) ?></small>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Concentración:</h6>
                        <p><?= h($recetaMedicamento->concentracion) ?: 'No especificada' ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Cantidad:</h6>
                        <p><?= $this->Number->format($recetaMedicamento->cantidad) ?> unidades</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Forma Farmacéutica:</h6>
                        <p><?= $recetaMedicamento->hasValue('forma_farmaceutica') ? h($recetaMedicamento->forma_farmaceutica->nombre) : 'No especificada' ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Vía de Administración:</h6>
                        <p><?= $recetaMedicamento->hasValue('via_administracion') ? h($recetaMedicamento->via_administracion->nombre) : 'No especificada' ?></p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Duración:</h6>
                        <p><?= $recetaMedicamento->duracion_dias ? $this->Number->format($recetaMedicamento->duracion_dias) . ' días' : 'No especificada' ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Fecha de Creación:</h6>
                        <p><?= $recetaMedicamento->created->format('d/m/Y H:i') ?></p>
                    </div>
                </div>

                <?php if ($recetaMedicamento->observaciones): ?>
                <hr>
                <div class="mb-3">
                    <h6 class="text-muted">Observaciones:</h6>
                    <p class="alert alert-light border"><?= nl2br(h($recetaMedicamento->observaciones)) ?></p>
                </div>
                <?php endif; ?>

                <hr>

                <!-- Botones de Acción -->
                <div class="mt-4 text-center">
                    <?= $this->Html->link(
                        '<i class="fas fa-edit me-2"></i>Editar',
                        ['action' => 'edit', $recetaMedicamento->id],
                        ['class' => 'btn btn-warning', 'escape' => false]
                    ) ?>
                    
                    <?= $this->Form->postLink(
                        '<i class="fas fa-trash me-2"></i>Eliminar',
                        ['action' => 'delete', $recetaMedicamento->id],
                        [
                            'class' => 'btn btn-danger',
                            'confirm' => '¿Estás seguro de que deseas eliminar este medicamento?',
                            'escape' => false
                        ]
                    ) ?>
                    
                    <?= $this->Html->link(
                        '<i class="fas fa-arrow-left me-2"></i>Volver a Receta',
                        ['controller' => 'Recetas', 'action' => 'view', $recetaMedicamento->receta_id],
                        ['class' => 'btn btn-secondary', 'escape' => false]
                    ) ?>
                </div>
            </div>
        </div>
    </div>
</div>
