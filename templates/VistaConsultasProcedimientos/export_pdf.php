<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Consultas y Procedimientos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h2, h3 { margin-bottom: 5px; }
        p { margin: 3px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 4px; text-align: left; }
        th { background-color: #f2f2f2; }
        .section { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>Reporte de Consultas y Procedimientos</h2>
    <p><strong>Desde:</strong> <?= h($startDate) ?> <strong>Hasta:</strong> <?= h($endDate) ?></p>

    <?php
        $consultas = array_filter($reportes, fn($r) => $r->motivo !== null);
        $procedimientos = array_filter($reportes, fn($r) => $r->procedimiento !== null);
    ?>

    <!-- Tabla de Consultas -->
    <?php if (empty($tipo) || $tipo === 'consulta'): ?>
    <div class="section">
        <h3>Consultas</h3>
        <table>
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Motivo</th>
                    <th>Doctor</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($consultas)): ?>
                    <?php foreach ($consultas as $row): ?>
                    <tr>
                        <td><?= h($row->dni) ?></td>
                        <td><?= h($row->nombre) ?></td>
                        <td><?= h($row->apellido) ?></td>
                        <td><?= h($row->motivo) ?></td>
                        <td><?= h($row->doctor_nombre . ' ' . $row->doctor_apellido) ?></td>
                        <td><?= h($row->fecha_registro) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No se encontraron consultas registradas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Tabla de Procedimientos -->
    <?php if (empty($tipo) || $tipo === 'procedimiento'): ?>
    <div class="section">
        <h3>Procedimientos</h3>
        <table>
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Procedimiento</th>
                    <th>Doctor</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($procedimientos)): ?>
                    <?php foreach ($procedimientos as $row): ?>
                    <tr>
                        <td><?= h($row->dni) ?></td>
                        <td><?= h($row->nombre) ?></td>
                        <td><?= h($row->apellido) ?></td>
                        <td><?= h($row->procedimiento) ?></td>
                        <td><?= h($row->doctor_nombre . ' ' . $row->doctor_apellido) ?></td>
                        <td><?= h($row->fecha_registro) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No se encontraron procedimientos registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</body>
</html>

