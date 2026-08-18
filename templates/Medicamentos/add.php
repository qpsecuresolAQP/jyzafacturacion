<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Medicamento $medicamento
 */
?>

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- Alerta de permiso -->
            <?php if ($medicamento->isNew() && !$this->Permisos->tiene('Medicamentos', 'add')): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-lock"></i> No tienes permisos para agregar medicamentos.
                </div>
            <?php elseif (!$medicamento->isNew() && !$this->Permisos->tiene('Medicamentos', 'edit')): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-lock"></i> No tienes permisos para editar medicamentos.
                </div>
            <?php else: ?>
                <?= $this->Form->create($medicamento, ['class' => 'row g-3']) ?>

                <!-- Título -->
                <div class="col-12 mb-4">
                    <h3 class="text-info">
                        <i class="fas fa-pills"></i>
                        <?= $medicamento->isNew() ? 'Agregar Medicamento' : 'Editar Medicamento' ?>
                    </h3>
                </div>

                <!-- Código -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('codigo', [
                        'label'       => 'Código',
                        'type'        => 'text',
                        'class'       => 'form-control',
                        'placeholder' => 'Ej: 0000000065',
                        'required'    => true,
                        'readonly'    => !$medicamento->isNew()
                    ]) ?>
                    <small class="form-text text-muted">Código único del medicamento</small>
                </div>

                <!-- Nombre -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('nombre', [
                        'label'       => 'Nombre',
                        'type'        => 'text',
                        'class'       => 'form-control',
                        'placeholder' => 'Ej: CEFALEXINA',
                        'required'    => true
                    ]) ?>
                    <small class="form-text text-muted">Nombre del medicamento</small>
                </div>

                <!-- Concentración -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('concentracion', [
                        'label'       => 'Concentración',
                        'type'        => 'text',
                        'class'       => 'form-control',
                        'placeholder' => 'Ej: 500MG, 10MG, 3X'
                    ]) ?>
                    <small class="form-text text-muted">Concentración por defecto del medicamento</small>
                </div>

                <!-- Descripción -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('descripcion', [
                        'label'       => 'Descripción',
                        'type'        => 'textarea',
                        'class'       => 'form-control',
                        'rows'        => 3,
                        'placeholder' => 'Detalles adicionales del medicamento'
                    ]) ?>
                </div>

                <!-- Botones -->
                <div class="col-12 text-center">
                    <?= $this->Form->button(
                        $medicamento->isNew() ? __('Guardar Medicamento') : __('Actualizar Medicamento'),
                        ['class' => 'btn btn-info']
                    ) ?>
                    <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary ms-2']) ?>
                </div>

                <?= $this->Form->end() ?>
            <?php endif; ?>
        </div>
    </div>
</div>