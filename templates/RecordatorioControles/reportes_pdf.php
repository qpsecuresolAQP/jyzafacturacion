<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\RecordatorioControl> $recordatorioControles
 */
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Recordatorios</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .resumen {
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
    <h2>Reporte de Recordatorios</h2>

    <?php if (!empty($fechaInicio)): ?>
        <p><strong>Fecha inicio:</strong> <?= h($fechaInicio) ?></p>
    <?php endif; ?>
    <?php if (!empty($fechaFin)): ?>
        <p><strong>Fecha fin:</strong> <?= h($fechaFin) ?></p>
    <?php endif; ?>
    <?php if (!empty($estado)): ?>
        <p><strong>Estado:</strong> <?= $estado === 'C' ? 'Citado' : 'Pendiente' ?></p>
    <?php endif; ?>

    <div class="resumen">
        <p><strong>Pendientes:</strong> <?= (int)($resumen['P']['total'] ?? 0) ?></p>
        <p><strong>Citados:</strong> <?= (int)($resumen['C']['total'] ?? 0) ?></p>
        <p><strong>Total registros:</strong> <?= count($recordatorioControles) ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Recordatorio</th>
                <th>Fecha control</th>
                <th>Próximo control</th>
                <th>Estado</th>
                <th>Enviado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recordatorioControles as $control): ?>
                <?php $paciente = $control->recordatorio->paciente ?? null; ?>
                <tr>
                    <td><?= $paciente ? h(trim($paciente->nombre . ' ' . $paciente->apellido)) : '-' ?></td>
                    <td><?= $control->recordatorio ? h($control->recordatorio->titulo) : '-' ?></td>
                    <td><?= h($control->fecha_control) ?></td>
                    <td><?= h($control->proximo_control) ?: '-' ?></td>
                    <td><?= $control->estado === 'C' ? 'Citado' : 'Pendiente' ?></td>
                    <td><?= $control->recordatorio_enviado ? 'Sí' : 'No' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>