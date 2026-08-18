<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Recetas por Departamento</title>
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
    <h2>Reporte de Recetas por Procedencia</h2>
    <p><strong>Desde:</strong> <?= h($startDate) ?> <strong>Hasta:</strong> <?= h($endDate) ?></p>

    <!-- Tabla Detallada de Recetas -->
<h3>Detalle de Recetas Emitidas</h3>
<table>
    <thead>
        <tr>
            <th>Procedencia</th>
            <th>Paciente</th>
            <th>Dni</th>
            <th>Sexo</th>
            <th>Edad</th>
            <th>Receta</th>
            <th>Fecha de Consulta</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($reportes)): ?>
            <?php foreach ($reportes as $row): ?>
            <tr>
                <td><?= h($row->nombre_departamento) ?></td>
                <td><?= h($row->nombre_paciente) ?></td>
                <td><?= h($row->dni_paciente) ?></td>

                <!-- NUEVOS CAMPOS -->
                <td><?= h($row->sexo_paciente ?? '') ?></td>
                <td><?= h($row->edad_paciente ?? '') ?></td>

                <td><?= h($row->nombre_receta) ?></td>

                <!-- Fecha de consulta formateada -->
                <td>
                    <?php
                        try {
                            $dt = new \DateTime($row->fecha_consulta);
                            $dt->setTimezone(new \DateTimeZone('America/Lima'));
                            echo $dt->format('d/m/Y H:i');
                        } catch (\Exception $e) {
                            echo h($row->fecha_consulta);
                        }
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">No se encontraron registros en el rango de fechas.</td></tr>
        <?php endif; ?>
    </tbody>
</table>


    <!-- Tabla Resumen por Departamento -->
    <h3>Resumen de Recetas por Procedencia</h3>
    <table>
        <thead>
            <tr>
                <th>Procedencia</th>
                <th>Total de Recetas</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($departamentos)): ?>
                <?php foreach ($departamentos as $dep): ?>
                <tr class="total-row">
                    <td><?= h($dep['nombre_departamento']) ?></td>
                    <td><?= h($dep['total_recetas']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="2">Sin datos para mostrar.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tabla Resumen de Recetas -->
    <h3>Resumen de Recetas Emitidas</h3>
    <table>
        <thead>
            <tr>
                <th>Receta</th>
                <th>Total de Recetas</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($recetas)): ?>
                <?php foreach ($recetas as $receta): ?>
                <tr class="total-row">
                    <td><?= h($receta['nombre_receta']) ?></td>
                    <td><?= h($receta['total_recetas_independiente']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="2">Sin datos para mostrar.</td></tr>
            <?php endif; ?>
        </tbody>

    <div style="margin-top: 30px; font-size: 10px;">
        <p><strong>Generado el:</strong> <?= date('d/m/Y H:i') ?></p>
    </div>
</body>
</html>