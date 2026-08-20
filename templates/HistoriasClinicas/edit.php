<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\HistoriasClinica $historiasClinica
 * @var string[]|\Cake\Collection\CollectionInterface $pacientes
 * @var string[]|\Cake\Collection\CollectionInterface $users
 */
?>

<div class="container mt-4 mb-4">
    <?= $this->Form->create($historiasClinica, ['class' => 'row g-3']) ?>

    <!-- Título -->
    <div class="col-12 mb-4">
        <h3 class="text-info"><i class="fas fa-notes-medical"></i> Editar Historia Clínica</h3>
    </div>

    <!-- Campo Paciente -->
    <div class="col-md-12 mb-3">
        <?php if (!empty($historiasClinica->paciente_id)) : ?>
            <!-- Campo oculto si el paciente ya está definido -->
            <?= $this->Form->control('paciente_id', [
                'type' => 'hidden',
                'value' => $historiasClinica->paciente_id
            ]) ?>
        <?php else : ?>
            <!-- Campo desplegable si no hay paciente definido -->
            <?= $this->Form->control('paciente_id', [
                'options' => $pacientes, 
                'empty' => true,
                'label' => 'Paciente',
                'class' => 'form-control',
            ]) ?>
        <?php endif; ?>
    </div>    

    <!-- Botones -->
    <div class="col-12 text-center mt-3">
        <?= $this->Form->button(__('Guardar Cambios'), ['class' => 'btn btn-info']) ?>
        <?= $this->Html->link(__('Cancelar'), ['controller' => 'Pacientes', 'action' => 'view', $historiasClinica->paciente_id], ['class' => 'btn btn-secondary ms-2']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>