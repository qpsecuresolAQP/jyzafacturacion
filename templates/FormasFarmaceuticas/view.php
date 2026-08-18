<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FormasFarmaceutica $formaFarmaceutica
 */
?>

<?php $this->assign('title', 'Detalle de Forma Farmacéutica'); ?>

<div class="formasFarmaceuticas view content">


    <div class="container mt-4 mb-4">
        <div class="row justify-content-center">
            <div class="col-md-8 offset-md-2 row g-3">

                <!-- Título -->
                <div class="col-12 mb-4">
                    <h3 class="text-info">
                        <i class="fas fa-capsules"></i> <?= h($formaFarmaceutica->nombre) ?>
                    </h3>
                </div>

                <!-- Nombre -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Nombre</label>
                    <div class="form-control bg-light"><?= h($formaFarmaceutica->nombre) ?></div>
                    <small class="form-text text-muted">Nombre de la forma farmacéutica (único)</small>
                </div>

                <!-- Estado -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Estado</label>
                    <div class="form-control bg-light">
                        <?php if ($formaFarmaceutica->activa): ?>
                            <span class="badge badge-success"><i class="fas fa-check"></i> Activa</span>
                        <?php else: ?>
                            <span class="badge badge-secondary"><i class="fas fa-times"></i> Inactiva</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Descripción -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Descripción</label>
                    <div class="form-control bg-light" style="min-height: 80px;">
                        <?= !empty($formaFarmaceutica->descripcion) ? nl2br(h($formaFarmaceutica->descripcion)) : '<em class="text-muted">Sin descripción</em>' ?>
                    </div>
                    <small class="form-text text-muted">Detalles adicionales (opcional)</small>
                </div>

                <!-- Creada -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Creada</label>
                    <div class="form-control bg-light"><?= $formaFarmaceutica->created->format('d/m/Y H:i') ?></div>
                </div>

                <!-- Última Actualización -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Última Actualización</label>
                    <div class="form-control bg-light"><?= $formaFarmaceutica->modified->format('d/m/Y H:i') ?></div>
                </div>

            </div>
        </div>
    </div>

</div>