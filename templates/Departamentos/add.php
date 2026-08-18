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
        <h3 class="text-info"><i class="fas fa-building"></i> Agregar Procedencia</h3>
    </div>

    <!-- Campo Nombre -->
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('nombre', [
            'label' => 'Nombre del Lugar',
            'class' => 'form-control',
            'placeholder' => 'Ejemplo: Socabaya, Arequipa',
        ]) ?>
    </div>

    <!-- Botones -->
    <div class="col-12 text-center mt-3">
        <?= $this->Form->button('Guardar Procedencia', [
            'type' => 'submit',
            'class' => 'btn btn-info',
            'id' => 'btnGuardar'
        ]) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    </div>

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