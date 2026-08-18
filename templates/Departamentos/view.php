<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Departamento $departamento
 */
?>

<?php $this->assign('title', 'Detalle de Lugar de Procedencia'); ?>

<div class="departamentos view content">

    <div class="container mt-4 mb-4">
        <div class="row justify-content-center">
            <div class="col-md-8 offset-md-2 row g-3">

                <!-- Título -->
                <div class="col-12 mb-4">
                    <h3 class="text-info">
                        <i class="fas fa-building"></i> <?= h($departamento->nombre) ?>
                    </h3>
                </div>

                <!-- Nombre -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Nombre</label>
                    <div class="form-control bg-light"><?= h($departamento->nombre) ?></div>
                </div>

                <!-- Fecha de Creación -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Fecha de Creación</label>
                    <div class="form-control bg-light"><?= h($departamento->created) ?></div>
                </div>

                <!-- Última Modificación -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Última Modificación</label>
                    <div class="form-control bg-light"><?= h($departamento->modified) ?></div>
                </div>

            </div>
        </div>
    </div>

</div>