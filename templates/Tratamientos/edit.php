<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tratamiento $tratamiento
 */
?>
<div class="container mt-4 mb-4">
    <?= $this->Form->create($tratamiento, ['class' => 'row g-3']) ?>

    <!-- Información del Servicio -->
    <div class="col-12 mb-4">
        <h3 class="text-info"><i class="fas fa-briefcase-medical"></i> Editar Servicio</h3>
    </div>

    <!-- Campos Nombre -->
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('nombre', [
            'label' => 'Nombre del Servicio',
            'class' => 'form-control',
            'placeholder' => 'Ejemplo: Servicio, Limpieza Dental'
        ]) ?>
    </div>

    <!-- Campo Descripción con ancho de 12 columnas -->
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('descripcion', [
            'label' => 'Descripción',
            'class' => 'form-control',
            'type' => 'text',
            'placeholder' => 'Detalles adicionales del servicio'
        ]) ?>
    </div>

    <!-- Campo Costo -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('costo', [
            'label' => 'Costo',
            'class' => 'form-control',
            'type' => 'number',
            'step' => '0.01',
            'placeholder' => 'Ej: 100.00'
        ]) ?>
    </div>

    <!-- Monto fijo a pagar al doctor -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('monto_fijo_pago', [
            'label' => 'Monto Fijo a Pagar al Doctor (S/)',
            'class' => 'form-control',
            'type' => 'number',
            'step' => '0.01',
            'min' => '0',
            'placeholder' => 'Ej: 20.00'
        ]) ?>
        <small class="text-muted">
            Solo aplica a doctores con modo de pago "Monto fijo por servicio".
        </small>
    </div>

    <!-- Gasto en materiales -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('gasto_materiales', [
            'label' => 'Gasto en Materiales (S/)',
            'class' => 'form-control',
            'type' => 'number',
            'step' => '0.01',
            'min' => '0',
            'placeholder' => 'Ej: 15.00'
        ]) ?>
        <small class="text-muted">
            Costo estimado de los materiales usados en este servicio.
        </small>
    </div>

    <!-- Estado -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('estado', [
            'label' => 'Activo',
            'type'  => 'checkbox',
        ]) ?>
    </div>

    <!-- Botones -->
    <div class="col-12 text-center">
        <?= $this->Form->button(__('Guardar Cambios'), ['class' => 'btn btn-info']) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary me-2']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>