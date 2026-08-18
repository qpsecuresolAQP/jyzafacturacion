<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RecetaMedicamento $recetaMedicamento
 * @var \Cake\Collection\CollectionInterface $medicamentos
 * @var \Cake\Collection\CollectionInterface $formasFarmaceuticas
 * @var \Cake\Collection\CollectionInterface $viasAdministracion
 */
?>
<div class="container mt-4 mb-4">
    <h2 class="text-info mb-4">
        <i class="fas fa-prescription-bottle me-2"></i>
        Editar Medicamento en Receta
    </h2>

    <div class="card card-body bg-light">
        <?= $this->Form->create($recetaMedicamento, ['class' => 'row g-3']) ?>

        <!-- Receta (Solo lectura) -->
        <div class="col-md-6">
            <label class="form-label">Receta:</label>
            <input type="hidden" name="receta_id" value="<?= $recetaMedicamento->receta_id ?>">
            <p class="form-control-plaintext fw-bold"><?= $recetaMedicamento->receta->nombre ?? 'Receta #' . $recetaMedicamento->receta_id ?></p>
        </div>

        <!-- Medicamento (Solo lectura) -->
        <div class="col-md-6">
            <label class="form-label">Medicamento:</label>
            <input type="hidden" name="medicamento_id" value="<?= $recetaMedicamento->medicamento_id ?>">
            <p class="form-control-plaintext fw-bold">
                <?= $recetaMedicamento->medicamento->codigo ?> - <?= $recetaMedicamento->medicamento->nombre ?>
            </p>
        </div>

        <!-- Concentración -->
        <div class="col-md-6">
            <label class="form-label">Concentración:</label>
            <?= $this->Form->control('concentracion', [
                'type' => 'text',
                'class' => 'form-control',
                'label' => false,
                'placeholder' => 'Ej: 500MG, 10MG'
            ]) ?>
        </div>

        <!-- Cantidad -->
        <div class="col-md-6">
            <label class="form-label">Cantidad:</label>
            <?= $this->Form->control('cantidad', [
                'type' => 'number',
                'class' => 'form-control',
                'label' => false,
                'placeholder' => 'Ej: 1, 20, 100',
                'required' => true,
                'min' => 1
            ]) ?>
        </div>

        <!-- Forma Farmacéutica -->
        <div class="col-md-6">
            <label class="form-label">Forma Farmacéutica:</label>
            <?= $this->Form->control('forma_farmaceutica_id', [
                'options' => $formasFarmaceuticas,
                'class' => 'form-control',
                'label' => false,
                'empty' => 'Seleccionar forma...'
            ]) ?>
        </div>

        <!-- Vía de Administración -->
        <div class="col-md-6">
            <label class="form-label">Vía de Administración:</label>
            <?= $this->Form->control('via_administracion_id', [
                'options' => $viasAdministracion,
                'class' => 'form-control',
                'label' => false,
                'empty' => 'Seleccionar vía...'
            ]) ?>
        </div>

        <!-- Duración en Días -->
        <div class="col-md-6">
            <label class="form-label">Duración (días):</label>
            <?= $this->Form->control('duracion_dias', [
                'type' => 'number',
                'class' => 'form-control',
                'label' => false,
                'placeholder' => 'Ej: 7, 10, 30',
                'min' => 1
            ]) ?>
        </div>

        <!-- Observaciones -->
        <div class="col-12">
            <label class="form-label">Observaciones / Notas Adicionales:</label>
            <?= $this->Form->control('observaciones', [
                'type' => 'textarea',
                'class' => 'form-control',
                'label' => false,
                'rows' => 3,
                'placeholder' => 'Ej: Tomar con alimentos, Evitar durante el embarazo, etc.'
            ]) ?>
        </div>

        <!-- Botones -->
        <div class="col-12 text-center mt-4">
            <?= $this->Form->button(
                '<i class="fas fa-save me-2"></i>Actualizar',
                ['class' => 'btn btn-success', 'escape' => false]
            ) ?>
            
            <?= $this->Form->postLink(
                '<i class="fas fa-trash me-2"></i>Eliminar',
                ['action' => 'delete', $recetaMedicamento->id],
                [
                    'class' => 'btn btn-danger',
                    'confirm' => '¿Estás seguro de que deseas eliminar este medicamento?',
                    'escape' => false
                ]
            ) ?>
            
            <?= $this->Html->link(
                '<i class="fas fa-arrow-left me-2"></i>Volver',
                ['controller' => 'Recetas', 'action' => 'view', $recetaMedicamento->receta_id],
                ['class' => 'btn btn-secondary', 'escape' => false]
            ) ?>
        </div>

        <?= $this->Form->end() ?>
    </div>
</div>
