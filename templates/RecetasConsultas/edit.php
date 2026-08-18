<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RecetasConsulta $recetasConsulta
 * @var string[]|\Cake\Collection\CollectionInterface $consultas
 * @var string[]|\Cake\Collection\CollectionInterface $recetas
 */
?>
<div class="container mt-4 mb-4">
    <?= $this->Form->create($recetasConsulta, ['class' => 'row g-3']) ?>

    <!-- Información de Recetas Consulta -->
    <div class="col-12 mb-4">
        <h3 class="text-info"><i class="fas fa-cogs me-2"></i> Editar Recetas Consulta</h3>
    </div>

    <!-- Campo Receta -->
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('receta_id', [
            'label' => 'Receta',
            'options' => $recetas,
            'class' => 'form-control',
            'placeholder' => 'Selecciona una receta'
        ]) ?>
    </div>

<!-- Campo Descripción con TinyMCE -->
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
            'placeholder' => 'Detalles adicionales sobre la receta...'
        ]) ?>
    </div>
</div>


    <!-- Botones -->
    <div class="col-12 text-center">
        <?= $this->Form->button(__('Guardar Cambios'), ['class' => 'btn btn-info']) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary me-2']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>
<?php if (!empty($cerrarVentana)): ?>
<script>
    if (window.opener) {
        window.opener.location.reload();
    }
    window.close();
</script>
<?php endif; ?>
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