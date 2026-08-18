<?php
$horariosPorDia = [];
foreach ($horarios as $h) {
    $fecha = $h->fecha->format('Y-m-d');
    $horaInicio = isset($h->hora_inicio) && $h->hora_inicio instanceof \DateTimeInterface ? $h->hora_inicio->format('H:i') : date('H:i', strtotime((string)($h->hora_inicio ?? '')));
    $horaFin = isset($h->hora_fin) && $h->hora_fin instanceof \DateTimeInterface ? $h->hora_fin->format('H:i') : date('H:i', strtotime((string)($h->hora_fin ?? '')));
    list($hiH, $hiM) = explode(':', $horaInicio) + [0,0];
    list($hfH, $hfM) = explode(':', $horaFin) + [0,0];
    $startMinutes = ((int)$hiH) * 60 + (int)$hiM;
    $endMinutes = ((int)$hfH) * 60 + (int)$hfM;
    $horariosPorDia[$fecha][] = [
        'start' => $horaInicio,
        'end' => $horaFin,
        'startMinutes' => $startMinutes,
        'endMinutes' => $endMinutes,
        'raw' => $h,
    ];
}
?>
<tbody>
<?php for ($hour = 9; $hour <= 19; $hour++): ?>
    <?php for ($minute = 0; $minute < 60; $minute += 15): ?>
        <tr>
            <td><?= h(date('H:i', strtotime("$hour:$minute"))) ?></td>
            <?php for ($i = 0; $i < 7; $i++): ?>
                <?php
                $fecha = (new DateTime($fechaSeleccionada))->modify("+$i days")->format('Y-m-d');
                $horarioDisponible = false;
                $cellTime = sprintf('%02d:%02d', $hour, $minute);
                $matchedHorario = null;
                if (!empty($horariosPorDia[$fecha])) {
                    foreach ($horariosPorDia[$fecha] as $h) {
                        $startMin = $h['startMinutes'];
                        $endMin = $h['endMinutes'];
                        list($cH, $cM) = explode(':', $cellTime) + [0,0];
                        $cellMin = ((int)$cH) * 60 + (int)$cM;
                        if ($startMin <= $cellMin && $endMin > $cellMin) {
                            $horarioDisponible = true;
                            $matchedHorario = $h['raw'];
                            break;
                        }
                    }
                }
                ?>
                <td
                    class="horario-celda <?= $horarioDisponible ? 'ocupado' : '' ?>"
                    <?= $horarioDisponible ? 'draggable="true"' : '' ?>
                    data-fecha="<?= $fecha ?>"
                    data-hora-inicio="<?= date('H:i', strtotime("$hour:$minute")) ?>"
                    data-hora-fin="<?= date('H:i', strtotime("$hour:$minute +15 minutes")) ?>"
                    <?= $horarioDisponible && isset($matchedHorario->id) ? 'data-horario-id="'.h($matchedHorario->id).'"' : '' ?>
                ></td>
            <?php endfor; ?>
        </tr>
    <?php endfor; ?>
<?php endfor; ?>
</tbody>