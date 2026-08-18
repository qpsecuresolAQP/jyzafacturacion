<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ViaAdministracion $viaAdministracion
 */
?>

<?php $this->assign('title', 'Detalle de Vía de Administración'); ?>

<div class="viasAdministracion view content">

    <div class="container mt-4 mb-4">
        <div class="row justify-content-center">
            <div class="col-md-8 offset-md-2 row g-3">

                <!-- Título -->
                <div class="col-12 mb-4">
                    <h3 class="text-info">
                        <i class="fas fa-route"></i> <?= h($viaAdministracion->nombre) ?>
                    </h3>
                </div>

                <!-- Nombre -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Nombre</label>
                    <div class="form-control bg-light"><?= h($viaAdministracion->nombre) ?></div>
                </div>

                <!-- Estado -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Estado</label>
                    <div class="form-control bg-light">
                        <?php if ($viaAdministracion->activa): ?>
                            <span class="badge badge-success">Activa</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Inactiva</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Descripción -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Descripción</label>
                    <div class="form-control bg-light" style="min-height: 80px;">
                        <?= !empty($viaAdministracion->descripcion) ? nl2br(h($viaAdministracion->descripcion)) : '<em class="text-muted">Sin descripción</em>' ?>
                    </div>
                </div>
                <!-- Creada -->
                
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Creada</label>
                    <div class="form-control bg-light"><?= $viaAdministracion->created->format('d/m/Y H:i') ?></div>
                </div>  
                    <!-- modificada -->
                    <div class="col-md-12 mb-3">
                        <label class="fw-bold">Modificada</label>
                        <div class="form-control bg-light"><?= $viaAdministracion->modified->format('d/m/Y H:i') ?></div>
                    </div>

            </div>
        </div>
    </div>

</div>