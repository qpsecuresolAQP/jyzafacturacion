<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Citas</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11.5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .estado-header {
            font-weight: bold;
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Reporte de Citas</h1>
    <p>Fecha Inicio: <?= $fechaInicio->format('Y-m-d') ?></p>
    <p>Fecha Fin: <?= $fechaFin->format('Y-m-d') ?></p>
    <p>Total de Citas: <?= $totalCitas ?></p>
    
    <?php if ($username): ?>
        <p><strong>Usuario:</strong> <?= h($username) ?></p> <!-- Mostrar el username en el reporte -->
    <?php endif; ?>
    <?php if ($estadoFiltro): ?>
        <p><strong>Estado:</strong> <?= h($estadoFiltro) ?></p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Estado</th>
                <th>Total</th>
                <th>Paciente</th>
                <th>Telefono</th>
                <th>Doctor</th>
                <th>Fecha y Hora</th>
                <th>Duración (min.)</th>
                <th>Tratamientos</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($citasAgrupadas as $estado => $detalles): ?>
                <tr class="estado-header">
                    <td><?= h($estado) ?></td>
                    <td><?= h($detalles['total']) ?></td>
                    <td colspan="7"></td>
                </tr>
                <?php foreach ($detalles['citas'] as $cita): ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><?= h($cita['paciente']) ?></td>
                        <td><?= h($cita['telefono_celular']) ?></td>
                        <td><?= h($cita['doctor']) ?></td>
                        <td><?= h($cita['fecha_hora']) ?></td>
                        <td><?= h($cita['duracion_minutos']) ?></td>
                        <td><?= h(implode(', ', $cita['tratamientos'] ?? [])) ?></td>
                        <td><?= h($cita['usuario']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
