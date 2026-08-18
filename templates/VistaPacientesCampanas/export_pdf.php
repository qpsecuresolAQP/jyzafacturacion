<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Pacientes y Campañas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h2, h3 { margin-bottom: 5px; }
        p { margin: 3px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 4px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total-row { font-weight: bold; background-color: #e6e6e6; }
    </style>
</head>
<body>
    <h2>Reporte de Pacientes y Campaña</h2>
    <p><strong>Desde:</strong> <?= h($startDate) ?> <strong>Hasta:</strong> <?= h($endDate) ?></p>

    <h3>Detalle de Pacientes y Campañas</h3>
    <table>
        <thead>
            <tr>
                <th>Paciente</th>
                <th>DNI</th>
                <th>Campaña</th>
                <th>Fecha nacimiento</th>
                <th>Ocupación</th>
                <th>Fecha de cita</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($reportes)): ?>
                <?php foreach ($reportes as $row): ?>
                <tr>
                    <td><?= h($row->nombre) . ' ' . h($row->apellido) ?></td>
                    <td><?= h($row->dni) ?></td>
                    <td><?= h($row->nombre_campana) ?></td>
                    <td><?= h($row->fecha_nacimiento) ?></td>
                    <td><?= h($row->ocupacion) ?></td>
                    <td><?= h($row->fecha_consulta) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">No se encontraron registros en el rango de fechas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>