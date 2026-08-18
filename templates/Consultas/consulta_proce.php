<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Receta Médica</title>
        <style>
            body { 
                font-family: Calibri, Arial, sans-serif; /* Cambia a Calibri */
                font-size: 13px;
                margin: 0.6cm; 
                line-height: 1.4;
            }
            .header img { 
                width: 100px; /* Ajusta el tamaño del logo */
                height: auto; 
            }
            .header h1 { 
                font-size: 16px; 
                margin: 0;
                margin-left: 50px;
                font-weight: bold;
                border-bottom: 1px solid #000;
                display: inline-block; /* Asegura que la línea se ajuste al texto */
                padding-bottom: 2px; /* Espaciado entre el texto y la línea */
            }
    
            .patient-info p{
                font-size: 13px; 
            }
    
            .separator {
                border-top: 1px solid #000;
            }
    
            
            .signature {
                margin-top: 1cm;
                font-size: 13px; 
                text-align: center;
            }
    
            .footer { margin-top: 1cm; font-size: 0.8em; text-align: center; color: #777; }
        </style>
    </head>
<body>
    <div class="header">
        <table width="100%">
            <tr>
                <td style="width: 50%; text-align: left;">
                    <img src="<?= $logoUrl ?>" alt="Logo">
                </td>
                <td style="width: 100%; text-align: left;">
                    <h1>Orden Médica</h1>
                </td>
            </tr>
        </table>
    </div>

    <div class="patient-info">
        <p>Nombre: <?= h($consulta->historias_clinica->paciente->nombre) ?> <?= h($consulta->historias_clinica->paciente->apellido) ?></p>
        <p>Fecha de Consulta: <?= h($consulta->created->format('d-m-Y')) ?></p>
    </div>
    <div class="section">
        <div class="separator"></div>
        <p class="text-light" style="white-space: pre-line;"><?= h($consulta->orden_medico) ?></p>
    </div>

        <div class="separator"></div>
        <!--
    <div class="signature">
        <p>___________________________</p>
        <p>Dr. <?= h($consulta->doctore->nombre ?? 'No Especificado') ?> <?= h($consulta->doctore->apellido ?? '') ?></p>
    </div>
    -->
    <table width="100%">
</table>
</body>
</html>