<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Pacientes</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total-row { font-weight: bold; background-color: #e6e6e6; }
    </style>
</head>
<body>
    <h2>Reporte de Pacientes</h2>
    <p>Desde: <?= h($startDate) ?> Hasta: <?= h($endDate) ?></p>

    <!-- Tabla principal con información detallada -->
    <table>
        <thead>
            <tr>
                <th>Nombre del Paciente</th>
                <th>Departamento</th>
                <th>Registrado por</th>
                <th>Fecha de Registro</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reportes as $paciente): ?>
            <tr>
                <td><?= h($paciente->nombre_paciente) ?>  <?= h($paciente->apellido_paciente) ?></td>
                <td><?= h($paciente->nombre_departamento) ?></td>
                <td><?= h($paciente->nombre_usuario) ?></td>
                <td><?= h($paciente->modified) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Tabla de resumen por departamentos -->
    <h3>Resumen por Departamentos</h3>
    <table>
        <thead>
            <tr>
                <th>Departamento</th>
                <th>Total de Pacientes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($departamentos as $departamento): ?>
            <tr>
                <td><?= h($departamento->nombre) ?></td>
                <td><?= h($departamento->total_pacientes) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Tabla de resumen por usuarios -->
    <h3>Resumen por Usuarios</h3>
    <table>
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Total de Pacientes Registrados</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?= h($usuario->nombre) ?></td>
                <td><?= h($usuario->total_pacientes) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer" style="margin-top: 30px;">
        <p>Generado el: <?= date('d/m/Y H:i') ?></p>
    </div>
</body>
</html>