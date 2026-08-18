<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\HorariosBloqueo $horariosBloqueo
 */
?>
<style>
    .label-text {
        font-weight: bold;
    }
    .data-box {
        border: 1px solid #ced4da;
        border-radius: 5px;
        padding: 5px 10px;
        min-height: 38px;
        display: flex;
        align-items: center;
    }
</style>

<div class="container mt-4 mb-4">
    <!-- Título -->
    <div class="mb-4">
        <h3 class="text-info"><i class="fas fa-ban me-2"></i> Detalles del Bloqueo de Horario</h3>
    </div>

    <!-- Doctor -->
    <div class="row mb-3">
        <div class="col-md-3">
            <p class="label-text"><?= __('Doctor:') ?></p>
        </div>
        <div class="col-md-9">
            <div class="data-box">
                <?= h($horariosBloqueo->doctore->nombre . ' ' . $horariosBloqueo->doctore->apellido) ?></div>
            </div>
        </div>
    </div>

    <!-- Fecha -->
    <div class="row mb-3">
        <div class="col-md-3">
            <p class="label-text"><?= __('Fecha:') ?></p>
        </div>
        <div class="col-md-9">
            <div class="data-box"><?= $this->Time->format($horariosBloqueo->fecha, 'dd/MM/yyyy') ?></div>
        </div>
    </div>

    <!-- Hora Inicio y Hora Fin -->
    <div class="row mb-3">
        <div class="col-md-6">
            <p class="label-text"><?= __('Hora Inicio:') ?></p>
            <div class="data-box"><?= h($horariosBloqueo->hora_inicio) ?></div>
        </div>
        <div class="col-md-6">
            <p class="label-text"><?= __('Hora Fin:') ?></p>
            <div class="data-box"><?= h($horariosBloqueo->hora_fin) ?></div>
        </div>
    </div>

    <!-- Motivo -->
    <div class="row mb-3">
        <div class="col-md-3">
            <p class="label-text"><?= __('Motivo:') ?></p>
        </div>
        <div class="col-md-9">
            <div class="data-box"><?= h($horariosBloqueo->motivo) ?: 'No especificado' ?></div>
        </div>
    </div>

    <!-- Fechas de Creación y Modificación -->
    <div class="row mb-3">
        <div class="col-md-6">
            <p class="label-text"><?= __('Fecha de Creación:') ?></p>
            <div class="data-box"><?= h($horariosBloqueo->created) ?></div>
        </div>
        <div class="col-md-6">
            <p class="label-text"><?= __('Última Modificación:') ?></p>
            <div class="data-box"><?= h($horariosBloqueo->modified) ?></div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="col-12 mt-3 text-center">
        <?= $this->Html->link(__('Regresar'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    </div>
</div>