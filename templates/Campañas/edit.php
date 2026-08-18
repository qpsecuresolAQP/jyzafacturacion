<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Campaña $campaña
 */
?>
<div class="container mt-4 mb-4">
    <?= $this->Form->create($campaña, ['class' => 'row g-3']) ?>

    <!-- Información de la Campaña -->
    <div class="col-12 mb-4">
        <h3 class="text-info"><i class="fas fa-cogs"></i> Editar Campaña</h3>
    </div>

    <!-- Campo Nombre -->
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('nombre', [
            'label' => 'Nombre de la Campaña',
            'class' => 'form-control',
            'placeholder' => 'Ejemplo: Campaña de Verano 2025'
        ]) ?>
    </div>

    <!-- Campo Descripción -->
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('descripcion', [
            'label' => 'Descripción',
            'class' => 'form-control',
            'type' => 'text',
            'placeholder' => 'Detalles sobre la campaña'
        ]) ?>
    </div>

    <!-- Botones -->
    <div class="col-12 text-center">
        <?= $this->Form->button(__('Guardar Cambios'), ['class' => 'btn btn-info']) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary me-2']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>