<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\HorariosDoctore> $horariosDoctores
 */

// Agrupar horarios por doctor
$agrupados = [];
$diasSemana = [
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado',
    0 => 'Domingo',
];

foreach ($horariosDoctores as $horario) {
    $doctorId = $horario->doctor_id;
    if (!isset($agrupados[$doctorId])) {
        $agrupados[$doctorId] = [
            'doctor' => $horario->doctore, // Guardar la relación completa del doctor
            'horarios' => [],
        ];
    }
    $agrupados[$doctorId]['horarios'][] = $horario;
}
?>

<div class="horariosDoctores index content">
    <?= $this->Html->link(__('Añadir Horario'), ['action' => 'add'], ['class' => 'button openModal float-right btn btn-info']) ?>

    <div class="table-responsive">
        <table class="table table-striped mt-3">
            <thead class="bg-info text-white">
                <tr>
                    <th><?= __('Doctor') ?></th>
                    <th><?= __('Horarios') ?></th>
                    <th class="actions text-dark"><?= __('Acciones') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($agrupados as $doctorId => $info): ?>
                    <tr>
                        <td>
                            <!-- Ahora accedes al nombre y apellido del doctor desde la relación guardada -->
                            <?= h($info['doctor']->nombre ?? 'Sin nombre') ?>
                            <?= h($info['doctor']->apellido ?? 'Sin apellido') ?>
                        </td>
                        <td>
                            <!-- Listar los horarios del doctor -->
                            <?php foreach ($info['horarios'] as $horario): ?>
                                <div class="mb-1">
                                    <span class="badge">
                                        <?= $diasSemana[$horario->dia_semana] ?? 'Día desconocido' ?>:
                                        <?= $horario->hora_inicio instanceof \Cake\I18n\Time ? $horario->hora_inicio->format('H:i') : date('H:i', strtotime($horario->hora_inicio)) ?>
                                        - 
                                        <?= $horario->hora_fin instanceof \Cake\I18n\Time ? $horario->hora_fin->format('H:i') : date('H:i', strtotime($horario->hora_fin)) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>

                        </td>
                        <td class="actions text-center">
                            <?= $this->Html->link('<i class="fas fa-eye"></i>', ['controller' => 'HorariosDoctores', 'action' => 'view', $doctorId], ['escape' => false, 'title' => 'Ver Horarios', 'class' => 'btn btn-info btn-sm openModal']) ?>
                            <?= $this->Html->link('<i class="fas fa-edit"></i> Editar horarios', ['controller' => 'HorariosDoctores', 'action' => 'editAll', $doctorId], ['escape' => false, 'class' => 'btn btn-warning btn-sm openModal']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('Primero')) ?>
            <?= $this->Paginator->prev('< ' . __('Anterior')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('Siguiente') . ' >') ?>
            <?= $this->Paginator->last(__('Último') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registro(s) de un total de {{count}}')) ?></p>
    </div>
</div>
