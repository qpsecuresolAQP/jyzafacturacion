<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Doctor $doctor
 * @var \Cake\Collection\CollectionInterface $horariosDoctores
 */
?>

<div class="column column-80">
    <div class="doctor view content">
        <h3 class="text-info">Horarios del Doctor: <?= h($doctor->nombre) ?></h3>
        
        <?php if ($horariosDoctores->isEmpty()): ?>
            <p><?= __('No hay horarios disponibles para este doctor.') ?></p>
        <?php else: ?>
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <!-- <th><?= __('ID del Horario') ?></th> -->
                        <th><?= __('Día de la Semana') ?></th>
                        <th><?= __('Hora de Inicio') ?></th>
                        <th><?= __('Hora de Fin') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Mapa de días de la semana
                    $diasSemana = [
                        1 => 'Lunes',
                        2 => 'Martes',
                        3 => 'Miércoles',
                        4 => 'Jueves',
                        5 => 'Viernes',
                        6 => 'Sábado',
                        0 => 'Domingo'
                    ];
                    ?>
                    <?php foreach ($horariosDoctores as $horario): ?>
                        <tr>
                            <!-- <td><?= $this->Number->format($horario->id) ?></td> < -->
                            <td><?= h($diasSemana[$horario->dia_semana] ?? 'Día desconocido') ?></td>
                            <td><?= h($horario->hora_inicio instanceof \Cake\I18n\Time ? $horario->hora_inicio->format('H:i') : substr((string)$horario->hora_inicio, 0, 5)) ?></td>
                            <td><?= h($horario->hora_fin instanceof \Cake\I18n\Time ? $horario->hora_fin->format('H:i') : substr((string)$horario->hora_fin, 0, 5)) ?></td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
