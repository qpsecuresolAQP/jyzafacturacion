<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Examene $examene
 */
?>
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="mb-4 mt-3">
                <h3 class="text-info"><i class="fas fa-flask"></i> Detalle del Examen</h3>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Categoría:</strong></div>
                <div class="col-md-9"><?= h($examene->categorias_examene->nombre ?? '-') ?></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Nombre:</strong></div>
                <div class="col-md-9"><?= h($examene->nombre) ?></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Muestra:</strong></div>
                <div class="col-md-9"><?= h($examene->muestra ?: '-') ?></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Precio al Paciente:</strong></div>
                <div class="col-md-9">S/ <?= number_format((float) $examene->precio, 2) ?></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Precio Convenio (Laboratorio):</strong></div>
                <div class="col-md-9 text-muted">S/ <?= number_format((float) $examene->precio_convenio, 2) ?></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Laboratorio:</strong></div>
                <div class="col-md-9"><?= h($examene->laboratorio->nombre ?? '-') ?></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Comisión Médico:</strong></div>
                <div class="col-md-9">
                    <?= $examene->comision_medico > 0
                        ? 'S/ ' . number_format((float) $examene->comision_medico, 2)
                        : '<span class="text-muted">No aplica</span>' ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Gasto en Materiales:</strong></div>
                <div class="col-md-9">
                    <?= $examene->gasto_materiales > 0
                        ? 'S/ ' . number_format((float) $examene->gasto_materiales, 2)
                        : '<span class="text-muted">No aplica</span>' ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Utilidad Estimada:</strong></div>
                <div class="col-md-9">
                    <?php
                    $utilidadExamen = (float) $examene->precio - (float) $examene->comision_medico - (float) $examene->gasto_materiales;
                    ?>
                    <span class="fw-bold <?= $utilidadExamen >= 0 ? 'text-success' : 'text-danger' ?>">
                        S/ <?= number_format($utilidadExamen, 2) ?>
                    </span>
                    <small class="text-muted d-block">Precio − Comisión Médico − Materiales (no incluye convenio de laboratorio)</small>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Estado:</strong></div>
                <div class="col-md-9">
                    <span class="badge bg-<?= $examene->estado ? 'success' : 'secondary' ?>">
                        <?= $examene->estado ? 'Activo' : 'Inactivo' ?>
                    </span>
                </div>
            </div>

            <div class="text-center mt-4">
                <?= $this->Html->link(__('Volver a la Lista'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                <?= $this->Html->link(__('Editar'), ['action' => 'edit', $examene->id], ['class' => 'btn btn-primary ms-2']) ?>
            </div>
        </div>
    </div>
</div>
