<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\HorariosDoctore $horariosDoctore
 * @var \Cake\Collection\CollectionInterface|string[] $doctores
 */
?>

<div class="container mt-4 mb-4">
    <?= $this->Form->create($horariosDoctore, ['class' => 'row g-3']) ?>

    <!-- Título -->
    <div class="col-12 mb-4">
        <h3 class="text-info"><i class="fas fa-clock"></i> Agregar Horarios del Doctor</h3>
    </div>

    <!-- Doctor -->
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('doctor_id', [
            'label' => 'Doctor',
            'options' => $doctores,
            'class' => 'form-control'
        ]) ?>
    </div>

    <!-- Contenedor dinámico de horarios -->
    <div id="horariosContainer" class="col-12"></div>

    <!-- Botón para agregar más horarios -->
    <div class="col-12 mb-3 text-start">
        <button type="button" class="btn btn-info" id="agregarHorario">
            <i class="fas fa-plus"></i> Agregar Horario
        </button>
    </div>

    <!-- Botones Guardar y Cancelar -->
    <div class="col-12 text-center mt-3">
        <?= $this->Form->button('Guardar Horarios', [
            'type' => 'submit',
            'class' => 'btn btn-info',
            'id' => 'btnGuardar'
        ]) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>

<!-- Template oculto para clonar -->
<template id="horarioTemplate">
    <div class="row border rounded p-3 mb-3 horario-item">
        <!-- Día de la Semana -->
        <div class="col-md-12 mb-3">
            <label>Día de la Semana</label>
            <select name="horarios[__INDEX__][dia_semana]" class="form-control" required>
                <option value="">Seleccione un día</option>
                <option value="1">Lunes</option>
                <option value="2">Martes</option>
                <option value="3">Miércoles</option>
                <option value="4">Jueves</option>
                <option value="5">Viernes</option>
                <option value="6">Sábado</option>
                <option value="0">Domingo</option>
            </select>
        </div>

        <!-- Hora Inicio -->
        <div class="col-md-6 mb-3">
            <label>Hora de Inicio</label>
            <input type="time" name="horarios[__INDEX__][hora_inicio]" class="form-control" required>
        </div>

        <!-- Hora Fin -->
        <div class="col-md-6 mb-3">
            <label>Hora de Fin</label>
            <input type="time" name="horarios[__INDEX__][hora_fin]" class="form-control" required>
        </div>
    </div>
</template>

<script>
(function() {
    const container = document.getElementById("horariosContainer");
    const template = document.getElementById("horarioTemplate");
    const btnAgregar = document.getElementById("agregarHorario");

    // Contador único para los bloques
    let contador = 0;

    btnAgregar.addEventListener("click", () => {
        // Reemplazar los placeholders __INDEX__ por el valor del contador
        const html = template.innerHTML.replaceAll('__INDEX__', contador++);
        container.insertAdjacentHTML('beforeend', html);
    });

    // Delegar evento para eliminar bloques
    container.addEventListener("click", function(e) {
        if (e.target.classList.contains("btnEliminarHorario")) {
            e.target.closest(".horario-item").remove();
        }
    });

    // Agrega al menos un bloque al iniciar
    window.addEventListener('DOMContentLoaded', () => {
        btnAgregar.click();
    });

    // Desactivar botón al enviar
    const form = document.querySelector("form");
    const btnGuardar = document.getElementById("btnGuardar");
    if (form && btnGuardar) {
        form.addEventListener("submit", function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                return;
            }
            btnGuardar.disabled = true;
            btnGuardar.innerText = "Guardando...";
        });
    }
})();
</script>
