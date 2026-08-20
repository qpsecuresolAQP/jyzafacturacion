<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\HistoriasClinica $historiasClinica
 */
?>

<style>
    .label-text {
        color: #f8f9fa;
        font-weight: bold;
    }
    .data-box {
        background-color: #6c757d;
        border: 1px solid #ced4da;
        border-radius: 5px;
        padding: 5px 10px;
        min-height: 38px;
        display: flex;
        align-items: center;
        color: #ffffff;
    }
</style>

<div class="container mt-4 mb-4">
    <!-- Título -->
    <div class="mb-4">
        <h3 class="text-info"><i class="fas fa-cogs me-2"></i> Detalles de la Historia Clínica</h3>
    </div>

    <!-- Paciente -->
    <div class="row mb-3">
        <div class="col-md-3">
            <p class="label-text"><?= __('Paciente:') ?></p>
        </div>
        <div class="col-md-9">
            <div class="data-box"><?= $historiasClinica->hasValue('paciente') ? $this->Html->link($historiasClinica->paciente->nombre, ['controller' => 'Pacientes', 'action' => 'view', $historiasClinica->paciente->id]) : 'No disponible' ?></div>
        </div>
    </div>

    <!-- Fechas -->
    <div class="row mb-3">
        <div class="col-md-4">
            <p class="label-text"><?= __('Fecha de Creación:') ?></p>
            <div class="data-box"><?= h($historiasClinica->created) ?></div>
        </div>
        <div class="col-md-4">
            <p class="label-text"><?= __('Última Modificación:') ?></p>
            <div class="data-box"><?= h($historiasClinica->modified) ?></div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="col-12 mt-3 text-center">
        <?= $this->Html->link(__('Editar Historia Clínica'), ['action' => 'edit', $historiasClinica->id], ['class' => 'btn btn-warning me-2 openModal', 'target' => '_blank']) ?>
        <?= $this->Html->link(__('Regresar'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    </div>
</div>