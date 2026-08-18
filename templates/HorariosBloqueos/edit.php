<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\HorariosBloqueo $horariosBloqueo
 * @var string[]|\Cake\Collection\CollectionInterface $doctores
 */
?>
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="mb-0"><i class="fas fa-edit"></i> Editar Bloqueo Horario</h3>
                </div>
                <div class="card-body">
                    <?= $this->Form->create($horariosBloqueo, ['class' => 'row g-3']) ?>
                    
                    <!-- Doctor -->
                    <div class="col-md-6">
                        <?= $this->Form->control('doctor_id', [
                            'label' => 'Doctor',
                            'options' => $doctores,
                            'class' => 'form-control',
                            'required' => true,
                            'empty' => 'Selecciona un doctor'
                        ]) ?>
                    </div>

                    <!-- Fecha -->
                    <div class="col-md-6">
                        <?= $this->Form->control('fecha', [
                            'label' => 'Fecha',
                            'type' => 'date',
                            'class' => 'form-control',
                            'required' => true
                        ]) ?>
                    </div>

                    <!-- Hora Inicio -->
                    <div class="col-md-6">
                        <?= $this->Form->control('hora_inicio', [
                            'label' => 'Hora de Inicio',
                            'type' => 'time',
                            'class' => 'form-control',
                            'required' => true,
                            'step' => '900' // Intervalos de 15 minutos
                        ]) ?>
                    </div>

                    <!-- Hora Fin -->
                    <div class="col-md-6">
                        <?= $this->Form->control('hora_fin', [
                            'label' => 'Hora de Fin',
                            'type' => 'time',
                            'class' => 'form-control',
                            'required' => true,
                            'step' => '900' // Intervalos de 15 minutos
                        ]) ?>
                    </div>

                    <!-- Motivo -->
                    <div class="col-12">
                        <?= $this->Form->control('motivo', [
                            'label' => 'Motivo del Bloqueo',
                            'type' => 'textarea',
                            'rows' => 3,
                            'class' => 'form-control',
                            'placeholder' => 'Ej: Descanso, Conferencia, Emergencia, etc.'
                        ]) ?>
                    </div>

                    <!-- Botones -->
                    <div class="col-12 text-center mt-4">
                        <?= $this->Form->button('Actualizar Bloqueo', ['class' => 'btn btn-info']) ?>
                        
                        <?= $this->Html->link('Cancelar', ['action' => 'index'], ['class' => 'btn btn-secondary ms-2']) ?>
                    </div>

                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
