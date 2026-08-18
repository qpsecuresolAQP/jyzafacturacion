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
        <h3 class="text-info"><i class="fas fa-bullhorn"></i> Agregar Campaña</h3>
    </div>

    <!-- Campos Nombre -->
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('nombre', [
            'label' => 'Nombre de la Campaña',
            'class' => 'form-control',
            'placeholder' => 'Ejemplo: Campaña de Verano, Campaña de Navidad'
        ]) ?>
    </div>

    <!-- Campo Descripción con ancho de 12 columnas -->
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('descripcion', [
            'label' => 'Descripción',
            'class' => 'form-control',
            'placeholder' => 'Detalles adicionales de la campaña',
            'type' => 'text'
        ]) ?>
    </div>

    <!-- Botones -->
    <div class="col-12 text-center mt-3">
        <?= $this->Form->button('Guardar Campaña', [
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