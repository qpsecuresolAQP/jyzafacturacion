<?php
use Cake\I18n\FrozenTime;
use Cake\I18n\I18n;

I18n::setLocale('es_PE'); // Español de Perú

$fechaHora = new FrozenTime($cita->fecha_hora);

// Obtener hora en formato 24h
$hora = $fechaHora->format('H:i A');

// Formatear fecha traducida con IntlDateFormatter en español (Perú)
$formatter = new \IntlDateFormatter(
    'es_PE',
    \IntlDateFormatter::FULL,
    \IntlDateFormatter::NONE,
    $fechaHora->getTimezone()->getName(),
    \IntlDateFormatter::GREGORIAN,
    "EEEE, d 'de' MMMM"
);

$fechaTraducida = ucfirst($formatter->format($fechaHora));
?>

<style>
    .recordatorio-container {
        position: relative;
        background: url('<?= $this->Url->image("clinicFondo.jpg") ?>') no-repeat center center;
        background-size: cover;
        color: #ffffff;
        padding: 50px 20px 30px 20px;
        text-align: center;
    }

    .recordatorio-overlay {
        background-color: rgba(255, 255, 255, 0.85);
        padding: 25px 20px;
        border-radius: 10px;
        color: #000;
        display: inline-block;
        max-width: 500px;
        width: 100%;
    }

    .recordatorio-hora {
        font-size: 48px;
        font-weight: bold;
        color: #783c94;
        margin-top:-17px;
    }

    .recordatorio-fecha {
        font-size: 18px;
        margin-bottom: 20px;
        color: #00b7bf;
        margin-bottom:2px;
    }

    .recordatorio-titulo {
        font-weight: bold;
        font-size: 20px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #003366;
    }

    .recordatorio-titulo i {
        margin-left: 8px;
        color: #003366;
    }

    .recordatorio-mensaje {
        font-size: 16px;
        margin-bottom: 15px;
        color: #003366;
    }

    .recordatorio-boton {
        background-color: #003366;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
        display: inline-block;
    }

    .datos-container {
        background-color: #f8f9fa;
        padding: 30px 20px;
        max-width: 900px;
        margin: 0 auto;
    }

    .dato {
        margin-bottom: 15px;
    }

    .dato label {
        font-weight: bold;
        color: #333;
    }

    .dato span {
        display: block;
        margin-top: 4px;
        color: #555;
        background-color: #e9ecef;
        padding: 6px 10px;
        border-radius: 5px;
    }

    .logo-top {
        max-width: 180px;
        margin-bottom: 20px;
    }
</style>
<div class="recordatorio-container">
    
    <div class="recordatorio-overlay">
        <!-- Mostrar la hora y fecha aquí -->
        

        <div class="recordatorio-titulo">
            🛎️ RECORDATORIO
        </div>
        <div class="text-center">
            <?= $this->Html->image('logoClinica.png', ['alt' => 'Logo Clínica', 'class' => 'logo-top']) ?>
        </div>
        <div class="recordatorio-mensaje">
           <?php
            $nombre = ($cita->Pacientes ? h($cita->Pacientes->nombre) . ' ' . h($cita->Pacientes->apellido) : 'Paciente');
            $tipo = $cita->tipo === 'C' ? 'tu consulta médica' : ($cita->tipo === 'P' ? 'tu procedimiento programado' : 'tu cita');

            echo "$nombre, no olvides $tipo en SpazioDentale. ¡Te esperamos!";
            ?>
        </div>
        <div class="recordatorio-hora"><?= $hora ?></div>
        <div class="recordatorio-fecha"><?= $fechaTraducida ?></div>
        
        
    </div>
</div>

<!-- Detalles de la cita -->
<div class="container my-4">
    <h3 class="text-center text-info mb-4">Detalles de la Cita:</h3>

    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="row align-items-center mb-3">
                <label class="col-md-3 col-form-label fw-bold">Paciente:</label>
                <div class="col-md-9 p-2 bg-light rounded"><?= ($cita->Pacientes ? h($cita->Pacientes->nombre) . ' ' . h($cita->Pacientes->apellido) : 'Paciente desconocido') ?></div>
            </div>

            <div class="row align-items-center mb-3">
                <label class="col-md-3 col-form-label fw-bold">Campaña:</label>
                <div class="col-md-9 p-2 bg-light rounded"><?= h($cita->campana->nombre ?? 'Sin Campaña') ?></div>
            </div>

            <div class="row align-items-center mb-3">
                <label class="col-md-3 col-form-label fw-bold">Tratamiento de Interés:</label>
                <div class="col-md-9 p-2 bg-light rounded"><?= h($cita->Pacientes && $cita->Pacientes->historias_clinicas ? ($cita->Pacientes->historias_clinicas[0]->tipo_orden ?? 'Sin tratamiento') : 'Sin tratamiento') ?></div>
            </div>

            <div class="row align-items-center mb-3">
                <label class="col-md-3 col-form-label fw-bold">Responsable:</label>
                <div class="col-md-9 p-2 bg-light rounded"><?= h($cita->doctore->nombre) ?></div>
            </div>

            <div class="row align-items-center mb-3">
                <label class="col-md-3 col-form-label fw-bold">Cómo se enteró:</label>
                <div class="col-md-9 p-2 bg-light rounded"><?= h($cita->Pacientes && $cita->Pacientes->historias_clinicas ? ($cita->Pacientes->historias_clinicas[0]->como_entero ?? 'No especificado') : 'No especificado') ?></div>
            </div>

            <div class="row align-items-center mb-3">
                <label class="col-md-3 col-form-label fw-bold">Estado:</label>
                <div class="col-md-9 p-2 bg-light rounded"><?= h($cita->estado) ?></div>
            </div>

            <div class="row align-items-center mb-3">
                <label class="col-md-3 col-form-label fw-bold">Fecha y Hora:</label>
                <div class="col-md-9 p-2 bg-light rounded"><?= h($cita->fecha_hora) ?></div>
            </div>

        </div>
    </div>
</div>