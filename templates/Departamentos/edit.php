<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Departamento $departamento
 */
?>
<div class="container mt-4 mb-4">
    <?= $this->Form->create($departamento, ['class' => 'row g-3']) ?>

    <!-- Información del Departamento -->
    <div class="col-12 mb-4">
        <h3 class="text-info"><i class="fas fa-cogs"></i> Editar Nombre</h3>
    </div>

    <!-- Campo Nombre -->
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('nombre', [
            'label' => 'Nombre del Lugar',
            'class' => 'form-control',
            'placeholder' => 'Ejemplo: Recursos Humanos, Finanzas'
        ]) ?>
    </div>

    <!-- Botones -->
    <div class="col-12 text-center">
        <?= $this->Form->button(__('Guardar Cambios'), ['class' => 'btn btn-info']) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary me-2']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>