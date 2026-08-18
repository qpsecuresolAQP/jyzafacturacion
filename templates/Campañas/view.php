<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Campana $campaña
 */
?>

<?php $this->assign('title', 'Detalle de Campaña'); ?>

<div class="campanas view content">

    <div class="container mt-4 mb-4">
        <div class="row justify-content-center">
            <div class="col-md-8 offset-md-2 row g-3">

                <!-- Título -->
                <div class="col-12 mb-4">
                    <h3 class="text-info">
                        <i class="fas fa-cogs"></i> <?= h($campaña->nombre) ?>
                    </h3>
                </div>

                <!-- Nombre -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Nombre</label>
                    <div class="form-control bg-light"><?= h($campaña->nombre) ?></div>
                </div>

                <!-- Descripción -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Descripción</label>
                    <div class="form-control bg-light" style="min-height: 80px;">
                        <?= !empty($campaña->descripcion) ? nl2br(h($campaña->descripcion)) : '<em class="text-muted">No especificada</em>' ?>
                    </div>
                </div>

                <!-- Fecha de Creación -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Fecha de Creación</label>
                    <div class="form-control bg-light"><?= h($campaña->created) ?></div>
                </div>

                <!-- Última Modificación -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Última Modificación</label>
                    <div class="form-control bg-light"><?= h($campaña->modified) ?></div>
                </div>

            </div>
        </div>
    </div>

</div>