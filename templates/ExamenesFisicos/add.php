<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ExamenesFisico $examenesFisico
 */
?>
<div class="container mt-4 mb-4">
    <?= $this->Form->create($examenFisico, ['class' => 'row g-3 justify-content-center']) ?>

    <!-- Información del Examen Físico -->
    <div class="col-10 mb-4 text-start">
        <h3 class="text-info"><i class="fas fa-heartbeat"></i> Agregar Examen Físico</h3>
    </div>

    <!-- Campos del Examen Físico -->
    <div class="col-md-5 mb-2">
        <?= $this->Form->control('edad', [
            'label' => 'Edad',
            'class' => 'form-control',
            'value' => $edad,
            'placeholder' => 'Edad del paciente'
        ]) ?>
    </div>    

    <div class="col-md-5 mb-2">
        <?= $this->Form->control('peso', [
            'label' => 'Peso (kg)',
            'class' => 'form-control',
            'placeholder' => 'Ingrese el peso en kg'
        ]) ?>
    </div>
    
    <div class="col-md-5 mb-2">
        <?= $this->Form->control('altura', [
            'label' => 'Altura (cm)',
            'class' => 'form-control',
            'placeholder' => 'Ingrese la altura en cm'
        ]) ?>
    </div>
    
    <div class="col-md-5 mb-2">
        <?= $this->Form->control('temperatura', [
            'label' => 'Temperatura (°C)',
            'class' => 'form-control',
            'placeholder' => 'Ingrese la temperatura'
        ]) ?>
    </div>
    
    <div class="col-md-5 mb-2">
        <?= $this->Form->control('eva', [
            'label' => 'Escala EVA',
            'class' => 'form-control',
            'placeholder' => 'Ingrese el nivel de dolor EVA'
        ]) ?>
    </div>
    
    <div class="col-md-5 mb-2">
        <?= $this->Form->control('presion', [
            'label' => 'Presión Arterial',
            'class' => 'form-control',
            'placeholder' => 'Ejemplo: 120/80'
        ]) ?>
    </div>
    
    <div class="col-md-5 mb-2">
        <?= $this->Form->control('frecuencia_cardiaca', [
            'label' => 'Frecuencia Cardíaca',
            'class' => 'form-control',
            'placeholder' => 'Ejemplo: 72 bpm'
        ]) ?>
    </div>
    
    <div class="col-md-5 mb-2">
        <?= $this->Form->control('saturacion', [
            'label' => 'Saturación de Oxígeno (%)',
            'class' => 'form-control',
            'placeholder' => 'Ejemplo: 98%'
        ]) ?>
    </div>
    
    <div class="col-md-5 mb-2">
        <?= $this->Form->control('glicemina', [
            'label' => 'Glicemia',
            'class' => 'form-control',
            'placeholder' => 'Ejemplo: 90 mg/dL'
        ]) ?>
    </div>
    <?= $this->Form->hidden('historia_id', ['value' => $historiaId]) ?>
    
    <!-- Botones -->
    <div class="col-12 text-center mt-3">
        <?= $this->Form->button('Guardar Examen Físico', [
            'type' => 'submit',
            'class' => 'btn btn-primary',
            'id' => 'btnGuardar'
        ]) ?>
        <?= $this->Html->link(__('Cancelar'), ['controller' => 'Pacientes', 'action' => 'view', $historia->paciente_id], ['class' => 'btn btn-secondary ms-2']) ?>
    <?= $this->Form->end() ?>
</div>

<script>
    const form = document.querySelector("form");
    const btnGuardar = document.getElementById("btnGuardar");

    if (form && btnGuardar) {
        form.addEventListener("submit", function(event) {
            if (!form.checkValidity()) {
                event.preventDefault(); // Evita que el formulario se envíe si no es válido
                return;
            }
            btnGuardar.disabled = true;
            btnGuardar.innerText = "Guardando...";
        });
    }
</script>
<style>
    /* Mejora la visibilidad de los placeholders en fondo oscuro */
    .form-control::placeholder {
        color: #ccc; /* Color de texto para el placeholder */
    }
</style>