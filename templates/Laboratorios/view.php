<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Laboratorio $laboratorio
 */
?>
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="mb-4 mt-3">
                <h3 class="text-info"><i class="fas fa-flask"></i> Detalle del Laboratorio</h3>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Nombre:</strong></div>
                <div class="col-md-9"><?= h($laboratorio->nombre) ?></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Estado:</strong></div>
                <div class="col-md-9">
                    <span class="badge bg-<?= $laboratorio->activo ? 'success' : 'secondary' ?>">
                        <?= $laboratorio->activo ? 'Activo' : 'Inactivo' ?>
                    </span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Creado:</strong></div>
                <div class="col-md-9"><?= h($laboratorio->created) ?></div>
            </div>

            <div class="text-center mt-4">
                <?= $this->Html->link(__('Volver a la Lista'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                <?= $this->Html->link(__('Editar'), ['action' => 'edit', $laboratorio->id], ['class' => 'btn btn-primary ms-2']) ?>
            </div>
        </div>
    </div>
</div>
