<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Doctores y Recetas</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total-row { font-weight: bold; background-color: #e6e6e6; }
    </style>
</head>
<body>
    <h2>Reporte de Consultas y Recetas</h2>
    <p>Desde: <?= h($startDate) ?> Hasta: <?= h($endDate) ?></p>

    <!-- Tabla de Consultas por Doctor -->
    <h3>Consultas Realizadas por Doctor</h3>
    <table>
        <thead>
            <tr>
                <th>Doctor</th>
                <th>Total de Consultas</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($doctores as $doctor): ?>
            <tr>
                <td><?= h($doctor['nombre']) ?></td>
                <td><?= h($doctor['total_consultas']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer" style="margin-top: 30px;">
        <p>Generado el: <?= date('d/m/Y H:i') ?></p>
    </div>
</body>
</html>