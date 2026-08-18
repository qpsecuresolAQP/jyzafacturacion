<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Laboratorio $laboratorio
 */
?>
<div class="container mt-4 mb-4">
    <?= $this->Form->create($laboratorio, ['class' => 'row g-3']) ?>

    <div class="col-12 mb-4">
        <h3 class="text-info"><i class="fas fa-flask"></i> Editar Laboratorio</h3>
    </div>

    <div class="col-md-8 mb-3">
        <?= $this->Form->control('nombre', [
            'label' => 'Nombre',
            'class' => 'form-control',
            'placeholder' => 'Ej: Laboratorio A',
        ]) ?>
    </div>

    <div class="col-md-4 mb-3">
        <?= $this->Form->control('activo', [
            'label' => 'Activo',
            'type'  => 'checkbox',
        ]) ?>
    </div>

    <div class="col-12 text-center">
        <?= $this->Form->button(__('Guardar Cambios'), ['class' => 'btn btn-info', 'id' => 'submitButton']) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary ms-2']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("form");
        const btnGuardar = document.getElementById("submitButton");

        if (form && btnGuardar) {
            form.addEventListener("submit", function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    return;
                }
                btnGuardar.disabled = true;
                btnGuardar.innerText = "Guardando...";
            });
        }
    });
</script>
