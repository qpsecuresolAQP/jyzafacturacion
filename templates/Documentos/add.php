<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Documento $documento
 * @var \Cake\Collection\CollectionInterface|string[] $pacientes
 */
?>

<div class="container mt-4 mb-4">
    <?= $this->Form->create($documento, ['type' => 'file', 'class' => 'row g-3']) ?>

    <!-- Título -->
    <div class="col-md-10 mx-auto mb-3">
        <h3 class="text-info"><i class="fas fa-file-upload"></i> Subir Archivo de Paciente</h3>
    </div>

    <!-- Selección de Historia Clinica -->
    <div class="col-md-10 mx-auto mb-3">
        <?= $this->Form->control('historia_id', [
            'type' => 'select',
            'options' => $historias,
            'label' => 'Historia Clínica (Paciente)',
            'class' => 'form-control',
            'value' => $documento->historia_id, // Preseleccionado si viene desde el botón
        ]) ?>
    </div>    
    
    <!-- Selección de Tipo -->
    <div class="col-md-10 mx-auto mb-3">
        <?= $this->Form->control('tipo', [
            'type' => 'select',
            'empty' => 'Seleccione un Tipo',
            'options' => ['foto_inicio' => 'Foto de Inicio', 'foto_avance' => 'Foto de Avance', 'foto_fin' => 'Foto de Fin', 'documento' => 'Documento'],
            'label' => 'Tipo de Archivo',
            'class' => 'form-control',
        ]) ?>
    </div>

    <!-- Subida de Archivo -->
    <div class="col-md-10 mx-auto mb-3">
        <?= $this->Form->control('ruta_archivo', [
            'type' => 'file',
            'label' => 'Archivo',
            'class' => 'form-control',
        ]) ?>
    </div>

    <!-- Campo Descripción -->
    <div class="col-md-10 mx-auto mb-3">
        <?= $this->Form->control('descripcion', [
            'label' => 'Descripción',
            'type' => 'text', // Se asegura que sea un input normal
            'class' => 'form-control',
            'placeholder' => 'Detalles adicionales sobre el archivo',
        ]) ?>
    </div>


    <!-- Botones -->
    <div class="col-12 text-center mt-3">
        <?= $this->Form->button(__('Guardar Archivo'), ['class' => 'btn btn-info', 'id' => 'submitButton']) ?>
<?php if (!empty($historia)): ?>
    <?= $this->Html->link(__('Cancelar'), ['controller' => 'Pacientes', 'action' => 'view', $historia->paciente_id], ['class' => 'btn btn-secondary']) ?>
<?php else: ?>
    <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
<?php endif; ?>
    </div>  
    <?= $this->Form->end() ?>
</div>
<script>
    $(document).ready(function() {
    $(document).on("submit", "form", function(event) {
        const form = $(this)[0]; 
        const btnGuardar = $(this).find("#submitButton");

        if (!form.checkValidity()) {
            event.preventDefault(); // Evita el envío si hay errores en el formulario
            return;
        }

        btnGuardar.prop("disabled", true).text("Guardando...");
    });

    // Detecta cambios en los archivos dentro del modal o cualquier formulario
    $(document).on("change", "input[type='file']", function() {
        const btnGuardar = $(this).closest("form").find("#submitButton");
        if (this.files.length > 0) {
            btnGuardar.prop("disabled", false);
        }
    });
});

</script>
<style>
    /* Mejora la visibilidad de los placeholders en fondo oscuro */
    .form-control::placeholder {
        color: #ccc; /* Color de texto para el placeholder */
    }
</style>