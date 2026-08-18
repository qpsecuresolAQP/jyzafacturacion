<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Receta $receta
 */
?>
<style>
    .label-text {
        font-weight: bold;
    }
    .row { 
                font-family: Calibri, Arial, sans-serif; /* Cambia a Calibri */
                /* font-size: 13px; */
                margin: 0.6cm; 
                line-height: 1.4;
            }
    .data-box {
        border: 1px solid #ced4da;
        border-radius: 5px;
        padding: 5px 10px;
        min-height: 38px;
        display: flex;
        align-items: center;
        
    }
     .tabulado1 {
                margin-left: 2em;
               
            }
            .tabulado2 {
                margin-left: 4em;
            }
            .tab {
                display: inline-block;
                margin-left: 2em;
            }
            .tabuladoS {
                 display: inline-block;
                margin-left: 0.6em;
                
            }

            
            .separator {
                border-top: 1px solid #000;
            }
</style>

<div class="container mt-4 mb-4">
    <!-- Título -->
    <div class="mb-4">
        <h3 class="text-info"><i class="fas fa-prescription me-2"></i> Detalles de la Receta</h3>
    </div>

    <!-- Nombre -->
    <div class="row mb-3">
        <div class="col-md-3">
            <p class="label-text"><?= __('Nombre:') ?></p>
        </div>
        <div class="col-md-9">
            <div class="data-box"><?= h($receta->nombre) ?></div>
        </div>
    </div>

    <!-- Descripción -->
    <div class="row mb-3">
        <div class="col-md-3">
            <p class="label-text"><?= __('Descripción:') ?></p>
        </div>
        <div class="col-md-9">
            <?= ($receta->descripcion) ?: 'No especificada' ?>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="col-12 mt-3 text-center">
        <?= $this->Html->link(__('Editar Receta'), ['action' => 'edit', $receta->id], ['class' => 'btn btn-warning me-2']) ?>
        <?= $this->Html->link(__('Regresar'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    </div>
</div>