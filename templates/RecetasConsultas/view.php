<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RecetasConsulta $recetasConsulta
 */
?>
<style>
    .label-text {
        font-weight: bold;
        color: #495057; /* Cambié el color del texto a uno más oscuro para mantener una buena legibilidad */
    }
    .data-box {
        border: 1px solid #ced4da;
        border-radius: 5px;
        padding: 5px 10px;
        min-height: 38px;
        display: flex;
        align-items: center;
        background-color: #f8f9fa; /* Fondo ligeramente gris para las cajas de datos */
    }
</style>

<div class="container mt-4 mb-4">
    <!-- Título -->
    <div class="mb-4">
        <h3 class="text-info"><i class="fas fa-cogs me-2"></i> Detalles de la Receta y Consulta</h3>
    </div>
    
    <!-- Receta -->
    <div class="row mb-3">
        <div class="col-md-3">
            <p class="label-text"><?= __('Receta:') ?></p>
        </div>
        <div class="col-md-9">
            <div class="data-box">
                <?= $recetasConsulta->hasValue('receta') ? $this->Html->link($recetasConsulta->receta->nombre, ['controller' => 'Recetas', 'action' => 'view', $recetasConsulta->receta->id]) : '' ?>
            </div>
        </div>
    </div>

    <!-- Descripción -->
    <div class="row mb-3">
        <div class="col-md-3">
            <p class="label-text"><?= __('Descripción:') ?></p>
        </div>
        <div class="col-md-9">
            <div class="data-box">
       <div class="col-md-9">
            <?= ($recetasConsulta->descripcion) ?: 'No especificada' ?>
        </div>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="col-12 mt-3 text-center">
        <?= $this->Html->link(__('Editar Recetas'), ['action' => 'edit', $recetasConsulta->id], ['class' => 'btn btn-warning me-2']) ?>
        <?= $this->Html->link(__('Regresar'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    </div>
</div>
