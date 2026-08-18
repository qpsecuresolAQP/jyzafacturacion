<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receta Médica</title>
    <style>
        body { 
            font-family: Calibri, sans-serif /* Cambia a Calibri */
            font-size: 10pt !important;
            margin: 0.6cm; 
            line-height: 1.3;
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

        /* .patient-info p{
            font-size: 14px; 
        } */
            .tabulado1 {
                margin-left: 4em;
                margin-bottom: -1.2em;
                text-indent: -1.5em;
            }
            .tabulado2 {
                margin-left: 4em;
            }
            .tab {
                display: inline-block;
                margin-left: 2em;
            }
            .tabuladoS {
                 display: inline-block;
                margin-left: 0.6em;
                
            }

            pre {
                    font-family: Calibri, Arial, sans-serif; 
                    font-size: 13px;                        
                    white-space: pre-wrap;                 
                    line-height: 1.4;                     
                }
        .separator {
            border-bottom: 1.5pt solid #000000 !important;
        }

        /* .prescription-info {
            margin-top: 1.5cm;
        } */
        .signature {
            margin-top: 1cm;
            font-size: 14px; 
            text-align: center;
        }
          ol {
                margin-left: 0 !important;
                padding-left: 36pt !important;
            }

            li {
                margin-bottom: 3pt !important;
            }
            p[style*="border-bottom"] {
                border-bottom: 1.5pt solid #000000 !important;
            }
    

        .footer { margin-top: 1cm; font-size: 0.8em; text-align: center; color: #777; }
    </style>
</head>
<body>
     <?php
    // Cambiamos 'windowtext' por el color que deseas
    $recetaConsulta->descripcion = str_replace('windowtext', '#000000', $recetaConsulta->descripcion);
    ?>
    <div class="header">
        <table width="100%">
            <tr>
                <td style="width: 50%; text-align: left;">
                    <img src="<?= $logoUrl ?>" alt="Logo">
                </td>
                <td style="width: 100%; text-align: left;">
                    <h1>Receta Médica</h1>
                </td>
            </tr>
        </table>
    </div>


    <div class="patient-info">
        <p>Fecha de Prescripción: <?= h($recetaConsulta->consulta->created->format('d-m-Y')) ?></p>
        <p>Apellidos y nombres: <?= h($recetaConsulta->consulta->historias_clinica->paciente->nombre) ?> <?= h($recetaConsulta->consulta->historias_clinica->paciente->apellido) ?></p>
    </div>

    <div class="separator"></div>
    <div class="prescription-info">
        <div>
            <!-- <?= nl2br(h($recetaConsulta->descripcion)) ?> -->
             <?= $recetaConsulta->descripcion ?>

        </div>
    </div>

    <!-- <div class="separator"></div>
    <div class="signature">
        <p>___________________________</p>
        <p>Dr. <?= h($recetaConsulta->consulta->doctore->nombre ?? ' No Especificado') ?> <?= h($recetaConsulta->consulta->doctore->apellido ?? '') ?></p>
    </div>
 -->
</body>
</html>