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
            <?php if (!$this->Permisos->tiene('Medicamentos', 'edit')): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-lock"></i> No tienes permisos para editar medicamentos.
                </div>
            <?php else: ?>
                <?= $this->Form->create($medicamento, ['class' => 'row g-3']) ?>

                <!-- Título -->
                <div class="col-12 mb-4">
                    <h3 class="text-info"><i class="fas fa-pills"></i> Editar Medicamento</h3>
                </div>

                <!-- Código (solo lectura) -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('codigo', [
                        'label'    => 'Código',
                        'type'     => 'text',
                        'class'    => 'form-control',
                        'readonly' => true
                    ]) ?>
                    <small class="form-text text-muted">No se puede modificar el código</small>
                </div>

                <!-- Nombre -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('nombre', [
                        'label'    => 'Nombre',
                        'type'     => 'text',
                        'class'    => 'form-control',
                        'required' => true
                    ]) ?>
                </div>

                <!-- Concentración -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('concentracion', [
                        'label' => 'Concentración',
                        'type'  => 'text',
                        'class' => 'form-control',
                    ]) ?>
                </div>

                <!-- Descripción -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('descripcion', [
                        'label' => 'Descripción',
                        'type'  => 'textarea',
                        'class' => 'form-control',
                        'rows'  => 3
                    ]) ?>
                </div>

                <!-- Botones -->
                <div class="col-12 text-center">
                    <?= $this->Form->button(__('Actualizar Medicamento'), ['class' => 'btn btn-info']) ?>
                    <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary ms-2']) ?>
                </div>

                <?= $this->Form->end() ?>
            <?php endif; ?>
        </div>
    </div>
</div>