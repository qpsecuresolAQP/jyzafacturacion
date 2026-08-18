<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tratamiento $tratamiento
 */
?>

<?php $this->assign('title', 'Detalle de Tratamiento'); ?>

<div class="tratamientos view content">


    <div class="container mt-4 mb-4">
        <div class="row justify-content-center">
            <div class="col-md-8 offset-md-2 row g-3">

                <!-- Título -->
                <div class="col-12 mb-4">
                    <h3 class="text-info">
                        <i class="fas fa-tooth"></i> <?= h($tratamiento->nombre) ?>
                    </h3>
                </div>

                <!-- Nombre -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Nombre</label>
                    <div class="form-control bg-light"><?= h($tratamiento->nombre) ?></div>
                </div>

                <!-- Descripción -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Descripción</label>
                    <div class="form-control bg-light" style="min-height: 80px;">
                        <?= !empty($tratamiento->descripcion) ? nl2br(h($tratamiento->descripcion)) : '<em class="text-muted">No especificada</em>' ?>
                    </div>
                </div>

                <!-- Costo -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Costo (S/)</label>
                    <div class="form-control bg-light">S/ <?= number_format((float)$tratamiento->costo, 2) ?></div>
                </div>

                <!-- Monto fijo a pagar al doctor -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Monto Fijo a Pagar al Doctor (S/)</label>
                    <div class="form-control bg-light">
                        <?= $tratamiento->monto_fijo_pago > 0
                            ? 'S/ ' . number_format((float)$tratamiento->monto_fijo_pago, 2)
                            : 'No definido' ?>
                    </div>
                </div>

                <!-- Estado -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Estado</label>
                    <div class="form-control bg-light">
                        <span class="badge bg-<?= $tratamiento->estado ? 'success' : 'secondary' ?>">
                            <?= $tratamiento->estado ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </div>
                </div>

                <!-- Fecha de Creación -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Fecha de Creación</label>
                    <div class="form-control bg-light"><?= h($tratamiento->created) ?></div>
                </div>

                <!-- Última Modificación -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Última Modificación</label>
                    <div class="form-control bg-light"><?= h($tratamiento->modified) ?></div>
                </div>

            </div>
        </div>
    </div>

</div>