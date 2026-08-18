<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Receta $receta
 */
?>
<style>
    .label-text {
        font-weight: bold;
    }
 .row { 
    font-family: Calibri, Arial, sans-serif;
    line-height: 1.4;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}
</style>

<div class="container mt-4 mb-4">
    <!-- <?= $this->Form->create($receta) ?> -->
    <?= $this->Form->create($receta, ['id' => 'form-receta']) ?>

    <!-- Título -->
    <div class="mb-4">
        <h3 class="text-info"><i class="fas fa-edit me-2"></i> Editar Receta</h3>
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
                'placeholder' => 'Ejemplo: Antibiótico, Analgésico',
                'value' => h($receta->nombre)
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
            'id' => 'descripcion-receta',
            'class' => 'form-control receta-descripcion-tinymce',
            'rows' => 6,
            'placeholder' => 'Detalles adicionales sobre la receta...',
            'value' => $receta->descripcion ?? ''
        ]) ?>
    </div>
</div>


    <!-- <div class="row mb-3">
        <div class="col-md-3">
            <p class="label-text"><?= __('Descripción:') ?></p>
        </div>
        <div class="col-md-9">
            <?= $this->Form->textarea('descripcion', [
                'value' => $receta->descripcion,
                'rows' => 6,
                'class' => 'form-control'
            ]) ?>
        </div>
    </div> -->

    <!-- Botones de Acción -->
    <div class="col-12 mt-3 text-center">
        <?= $this->Form->button(__('Guardar Cambios'), ['class' => 'btn btn-primary me-2']) ?>
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
            plugins: [
      // Core editing features
      'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
      // Your account includes a free trial of TinyMCE premium features
      // Try the most popular premium features until Jun 27, 2025:
      'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'mentions', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
    ],
        menubar: false,
        toolbar: 'undo redo | bold italic underline | bullist numlist outdent indent | removeformat',
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