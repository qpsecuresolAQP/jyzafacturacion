<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RecordatorioControl $recordatorioControl
 * @var \Cake\Collection\CollectionInterface $recordatorios
 */
?>

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <?php if (!$this->Permisos->tiene('RecordatorioControles', 'edit')): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-lock"></i> No tienes permisos para editar controles.
                </div>
            <?php else: ?>
                <?= $this->Form->create($recordatorioControl, ['class' => 'row g-3']) ?>

                <div class="col-12 mb-4">
                    <h3 class="text-info">
                        <i class="fas fa-clipboard-check"></i> Editar Control
                    </h3>
                </div>

                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('recordatorio_id', [
                        'label' => 'Recordatorio',
                        'type' => 'select',
                        'options' => $recordatorios,
                        'class' => 'form-control',
                        'empty' => '-- Seleccionar Recordatorio --',
                        'required' => true
                    ]) ?>
                </div>

                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('fecha_control', [
                        'label' => 'Fecha del control',
                        'type' => 'date',
                        'class' => 'form-control',
                        'required' => true
                    ]) ?>
                </div>

                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('proximo_control', [
                        'label' => 'Próximo control sugerido',
                        'type' => 'date',
                        'class' => 'form-control'
                    ]) ?>
                </div>

                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('detalles', [
                        'label' => 'Detalles',
                        'type' => 'textarea',
                        'rows' => 3,
                        'class' => 'form-control'
                    ]) ?>
                </div>

                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('productos_utilizados', [
                        'label' => 'Productos utilizados',
                        'type' => 'textarea',
                        'rows' => 3,
                        'class' => 'form-control'
                    ]) ?>
                </div>

                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('observaciones', [
                        'label' => 'Observaciones',
                        'type' => 'textarea',
                        'rows' => 3,
                        'class' => 'form-control'
                    ]) ?>
                </div>

                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('recordatorio_enviado', [
                        'label' => 'Recordatorio enviado al paciente',
                        'type' => 'checkbox'
                    ]) ?>
                </div>

                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('estado', [
                        'label' => 'Estado del control',
                        'type' => 'select',
                        'options' => ['P' => 'Pendiente', 'C' => 'Citado'],
                        'class' => 'form-control',
                        'required' => true
                    ]) ?>
                </div>

                <div class="col-12 text-center">
                    <?= $this->Form->button(__('Actualizar Control'), ['class' => 'btn btn-info']) ?>
                </div>

                <?= $this->Form->end() ?>
            <?php endif; ?>
        </div>
    </div>
</div>