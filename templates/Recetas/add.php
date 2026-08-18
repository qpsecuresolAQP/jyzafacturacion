<?php
/**
 * @var \App\View\AppView $this
 */
?>
<style>
    .label-text {
        
        font-weight: bold;
    }
    .textarea-box {
        
        border: 1px solid #ced4da;
        border-radius: 5px;
        padding: 10px;
        min-height: 150px;
        width: 100%;
        
        resize: vertical;
        white-space: pre-wrap;
    }
</style>

<div class="container mt-4 mb-4">
    <?= $this->Form->create($receta, ['type' => 'file']) ?>

    <!-- Título -->
    <div class="mb-4">
        <h3 class="text-info"><i class="fas fa-plus me-2"></i> Añadir Nueva Receta</h3>
    </div>

    <!-- Nombre -->
    <div class="row mb-3">
        <div class="col-md-3">
            <p class="label-text"><?= __('Nombre:') ?></p>
        </div>
        <div class="col-md-9">
            <?= $this->Form->control('nombre', [
                'label' => false,
                'class' => 'form-control',
                'placeholder' => 'Ejemplo: Antibiótico, Analgésico'
            ]) ?>
        </div>
    </div>
    
    <!-- Descripción -->
<div class="row mb-3">
    <div class="col-md-3">
        <p class="label-text"><?= __('Descripción:') ?></p>
    </div>
    <div class="col-md-9">
        <?= $this->Form->control('descripcion', [
            'type' => 'textarea',
            'label' => false,
            'class' => 'form-control receta-descripcion-tinymce', // CLASE CLAVE
            'placeholder' => 'Detalles adicionales sobre la receta...',
            'rows' => 6,
            'id' => 'descripcion-receta' // solo para identificación, puede ser cualquier ID válido
        ]) ?>
    </div>
</div>

    <!-- Botones de Acción -->
    <div class="col-12 mt-3 text-center">
        <?= $this->Form->button(__('Guardar Receta'), ['class' => 'btn btn-primary me-2']) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>

<script>
function initializeRecetaForm() {
    // Solo reinicializa si el textarea aún no está activado
    const descripcion = document.getElementById('descripcion');
    if (!descripcion) return;

    if (tinymce.get('descripcion')) {
        return; // Ya inicializado por el global
    }

    tinymce.init({
        selector: '#descripcion',
        menubar: false,
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        height: 150,
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });
}

// Ejecutar solo si no fue inicializado aún
document.addEventListener('DOMContentLoaded', initializeRecetaForm);
$(document).on('shown.bs.modal', '#modalLg', initializeRecetaForm);
$(document).on('ajaxComplete', initializeRecetaForm);
</script>