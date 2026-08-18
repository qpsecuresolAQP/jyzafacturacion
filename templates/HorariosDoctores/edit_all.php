<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Collection\CollectionInterface $horariosDoctores
 * @var int $doctorId
 */
?>

<div class="container mt-5 mb-4">
    <div class="mb-4">
    <h3 class="text-info">
    <i class="fas fa-edit"></i> Editar Horarios - <?= h($doctor->nombre) ?> (ID: <?= h($doctorId) ?>)
</h3>

    </div>

    <?= $this->Form->create(null, ['url' => ['action' => 'editAll', $doctorId], 'class' => 'needs-validation']) ?>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table">
                <tr>
                    <th><?= __('Día de la Semana') ?></th>
                    <th><?= __('Hora de Inicio') ?></th>
                    <th><?= __('Hora de Fin') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($horariosDoctores as $index => $horario): ?>
                    <tr>
                        <td>
                            <?= $this->Form->select(
                                "horarios[{$index}][dia_semana]",
                                [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 0 => 'Domingo'],
                                ['default' => $horario->dia_semana, 'class' => 'form-select', 'required' => true]
                            ) ?>
                        </td>
                        <td>
                            <?= $this->Form->text("horarios[{$index}][hora_inicio]", [
                                'type' => 'time',
                                'value' => $horario->hora_inicio instanceof \Cake\I18n\Time
                                    ? $horario->hora_inicio->format('H:i')
                                    : substr((string)$horario->hora_inicio, 0, 5)
,
                                'default' => $horario->hora_inicio,
                                'class' => 'form-control',
                                'required' => true
                            ]) ?>
                        </td>
                        <td>
                            <?= $this->Form->text("horarios[{$index}][hora_fin]", [
                                'type' => 'time',
                                'value' => $horario->hora_fin instanceof \Cake\I18n\Time
                                    ? $horario->hora_fin->format('H:i')
                                    : substr((string)$horario->hora_fin, 0, 5),
                                'default' => $horario->hora_fin,
                                'class' => 'form-control',
                                'required' => true
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="text-center mt-4">
        <?= $this->Form->button(__('Guardar Cambios'), ['class' => 'btn btn-primary px-4']) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary ms-2']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>
