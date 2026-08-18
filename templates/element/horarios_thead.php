<?php
// Espera variable: $fechaSeleccionada (Y-m-d)
setlocale(LC_TIME, 'es_ES.UTF-8');
$formatter = new IntlDateFormatter('es_PE', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
$formatter->setPattern('EEEE d/MM');
?>
<thead>
    <tr>
        <th>Hora</th>
        <?php for ($i = 0; $i < 7; $i++):
            $dia = (new DateTime($fechaSeleccionada))->modify("+{$i} days");
        ?>
            <th><?= h(ucfirst($formatter->format($dia))) ?></th>
        <?php endfor; ?>
    </tr>
</thead>