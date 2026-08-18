<?php
    $historia = $paciente->historias_clinica ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historia Clínica</title>
    <style>
        body {
            font-size: 12px;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        td {
            padding: 5px 10px;
            vertical-align: middle;
        }

        .label-text {
            font-weight: bold;
        }

        .align-right {
            text-align: right;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin: 0 auto;
        }

        .subtitle {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
        }

        hr {
            border: 0;
            height: 1px;
            background: #ccc;
            margin: 10px 0;
        }

        .footer {
            margin-top: 60px;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
        }

        .signature-line {
            margin-top: 10px;
            display: inline-block;
            width: 50%;
            border-top: 1px solid #000;
            text-align: center;
        }

        .margin-izquierdo {
            margin-left: 10px;
        }

        .Fecha {
            margin-bottom: 15px;
            margin-left: 8px;
        }

        .data-box {
            border: 1px solid #ccc;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="title" style="text-align: center;">FICHA DE DATOS</div>
    <br>

    <table>
        <tr>
            <td style="width: 100%; text-align: right;">
                <div style="display: inline-block; width: 150px; height: 30px; border: 1px solid #000; text-align: center; line-height: 30px;">
                    <?= h($paciente->id) ?>
                </div>
                <br>
                <span style="font-size: 12px; text-align: center; margin-top:10px;">N° DE HIST. CLÍNICA</span>
            </td>
        </tr>
    </table>

    <div class="Fecha">FECHA: <?= h($paciente->created->format('Y-m-d')) ?></div>

    <div class="container">
        <h1 class="title">Historia Clínica de <?= h($paciente->nombre) ?> <?= h($paciente->apellido) ?></h1>

        <hr />

        <!-- Campo: Nombres y Apellidos -->
        <div class="row mb-3">
            <div class="col-md-2"><p class="label-text">Nombres:</p></div>
            <div class="col-md-10"><div class="data-box"><?= h($paciente->nombre . ' ' . $paciente->apellido) ?></div></div>
        </div>

        <!-- Campo: DNI -->
        <div class="row mb-3">
            <div class="col-md-2"><p class="label-text">DNI:</p></div>
            <div class="col-md-3"><div class="data-box"><?= h($historia->dni) ?></div></div>

            <div class="col-md-1"></div>

            <div class="col-md-3"><p class="label-text">Fecha de Nacimiento:</p></div>
            <div class="col-md-3"><div class="data-box"><?= !empty($historia->fecha_nacimiento) ? h($historia->fecha_nacimiento->format('d-m-Y')) : '&nbsp;' ?></div></div>
        </div>

        <!-- Sexo y Edad -->
        <div class="row mb-3">
            <div class="col-md-2"><p class="label-text">Sexo:</p></div>
            <div class="col-md-3"><div class="data-box"><?= h($historia->sexo ?? '') ?></div></div>

            <div class="col-md-1"></div>

            <div class="col-md-3"><p class="label-text">Edad:</p></div>
            <div class="col-md-3"><div class="data-box"><?= h($historia->edad ?? '') ?></div></div>
        </div>

        <!-- Teléfono -->
        <div class="row mb-3">
            <div class="col-md-2"><p class="label-text">Teléfono Celular:</p></div>
            <div class="col-md-10"><div class="data-box"><?= h($paciente->telefono_celular ?? '') ?></div></div>
        </div>

        <!-- Departamento -->
        <div class="row mb-3">
            <div class="col-md-2"><p class="label-text">Procedencia:</p></div>
            <div class="col-md-10"><div class="data-box"><?= !empty($historia->departamento->nombre) ? h($historia->departamento->nombre) : '&nbsp;' ?></div></div>
        </div>

        <!-- Campaña -->
<?php
$campanas = [];

if (!empty($paciente->citas)) {
    foreach ($paciente->citas as $cita) {
        if (!empty($cita->campana)) {
            $campanas[] = h($cita->campana->nombre);
        }
    }
}

$campanasTexto = !empty($campanas) ? implode(' • ', array_unique($campanas)) : 'N/A';
?>

<!-- Campañas -->
<div class="row mb-3">
    <div class="col-md-2"><p class="label-text">Campañas:</p></div>
    <div class="col-md-10"><div class="data-box"><?= $campanasTexto ?></div></div>
</div>

        <!-- Tratamiento de Interés -->
        <div class="row mb-3">
            <div class="col-md-2"><p class="label-text">Tratamiento de Interés:</p></div>
            <div class="col-md-10"><div class="data-box"><?= h($historia->tipo_orden ?? '') ?></div></div>
        </div>

        <!-- Cómo se enteró y Observaciones -->
        <div class="row mb-3">
            <div class="col-md-2"><p class="label-text">Cómo se enteró:</p></div>
            <div class="col-md-3"><div class="data-box"><?= h($historia->como_entero ?? '') ?></div></div>

            <div class="col-md-1"></div>

            <div class="col-md-3"><p class="label-text">Observaciones Administrativas:</p></div>
            <div class="col-md-3"><div class="data-box"><?= h($historia->obs_administrativas ?? '') ?></div></div>
        </div>

        <?php if ($historia): ?>
            <div class="subtitle">Historia Clínica</div>

            <div class="row mb-3">
                <div class="col-md-2"><p class="label-text">Antecedentes:</p></div>
                <div class="col-md-10"><div class="data-box"><?= h($historia->antecedentes ?? '') ?></div></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-2"><p class="label-text">Alergias:</p></div>
                <div class="col-md-10"><div class="data-box"><?= h($historia->alergias ?? '') ?></div></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-2"><p class="label-text">Enfermedades:</p></div>
                <div class="col-md-10"><div class="data-box"><?= h($historia->enfermedades ?? '') ?></div></div>
            </div>
        <?php else: ?>
            <p class="text-center">No hay historia clínica registrada para este paciente.</p>
        <?php endif; ?>

        <hr />

        <div class="footer">
            <div class="signature-line">Firma</div>
            <div class="Fecha"><?= date('d-m-Y') ?></div>
        </div>
    </div>
</body>
</html>
