<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Documento $documento
 * @var string[]|\Cake\Collection\CollectionInterface $pacientes
 */
?>

<div class="container mt-4 mb-4">
    <?= $this->Form->create($documento, ['type' => 'file', 'class' => 'row g-3']) ?>

    <!-- Título -->
    <div class="col-12 mb-4">
        <h3 class="text-info"><i class="fas fa-file-upload"></i> Editar Documento</h3>
    </div>

    <!-- Selección de Historia (paciente) -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('historia_id', [
            'options' => $pacientes,
            'label' => 'Paciente / Historia',
            'class' => 'form-control',
        ]) ?>
    </div>

    <!-- Selección de Tipo -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('tipo', [
            'type' => 'select',
            'empty' => 'Seleccione un Tipo',
            'options' => [
                'foto_inicio' => 'Foto de Inicio',
                'foto_avance' => 'Foto de Avance',
                'foto_fin' => 'Foto de Fin',
                'documento' => 'Documento',
                'consulta' => 'Consulta',
            ],
            'label' => 'Tipo de Archivo',
            'class' => 'form-control',
        ]) ?>
    </div>

    <!-- Archivo -->
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('ruta_archivo', [
            'type' => 'file',
            'label' => 'Archivo (deja vacío para no cambiar)',
            'class' => 'form-control',
        ]) ?>
    </div>

    <!-- Descripción -->
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('descripcion', [
            'label' => 'Descripción',
            'class' => 'form-control',
            'placeholder' => 'Detalles adicionales sobre el documento',
        ]) ?>
    </div>

    <!-- Botones -->
    <div class="col-12 text-center mt-3">
        <?= $this->Form->button(__('Guardar Cambios'), ['class' => 'btn btn-info']) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>
