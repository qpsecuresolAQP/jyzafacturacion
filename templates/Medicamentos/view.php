<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Medicamento $medicamento
 */
?>

<?php $this->assign('title', 'Detalle de Medicamento'); ?>

<div class="medicamentos view content">


    <div class="container mt-4 mb-4">
        <div class="row justify-content-center">
            <div class="col-md-8 offset-md-2 row g-3">

                <!-- Título -->
                <div class="col-12 mb-4">
                    <h3 class="text-info">
                        <i class="fas fa-pills"></i> <?= h($medicamento->nombre) ?>
                    </h3>
                </div>

                <!-- Código -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Código</label>
                    <div class="form-control bg-light"><?= h($medicamento->codigo) ?></div>
                    <small class="form-text text-muted">Código único del medicamento</small>
                </div>

                <!-- Nombre -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Nombre</label>
                    <div class="form-control bg-light"><?= h($medicamento->nombre) ?></div>
                    <small class="form-text text-muted">Nombre del medicamento</small>
                </div>

                <!-- Concentración -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Concentración</label>
                    <div class="form-control bg-light">
                        <?= h($medicamento->concentracion) ?: '<em class="text-muted">No especificada</em>' ?>
                    </div>
                    <small class="form-text text-muted">Concentración por defecto del medicamento</small>
                </div>

                <!-- Descripción -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Descripción</label>
                    <div class="form-control bg-light" style="min-height: 80px;">
                        <?= !empty($medicamento->descripcion) ? nl2br(h($medicamento->descripcion)) : '<em class="text-muted">Sin descripción</em>' ?>
                    </div>
                </div>

                <!-- Creado -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Creado</label>
                    <div class="form-control bg-light"><?= $medicamento->created->format('d/m/Y H:i') ?></div>
                </div>

                <!-- Última Actualización -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Última Actualización</label>
                    <div class="form-control bg-light"><?= $medicamento->modified->format('d/m/Y H:i') ?></div>
                </div>

            </div>
        </div>
    </div>

</div>