<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receta Médica</title>
    <style>
        body { 
            font-family: Helvetica, Arial, sans-serif;
            margin: 2cm; 
            line-height: 1.6; }
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
            font-size: 14px; 
        }

        .separator {
            border-top: 1px solid #000;
        }
        .tabulado1 {
                margin-left: 4em;
                margin-bottom: -1.2em;
                text-indent: -1.5em;
            }
        .tabulado2 {
                margin-left: 4em;
        }

        /* .prescription-info {
            margin-top: 1.5cm;
        } */
        .signature {
            margin-top: 1cm;
            font-size: 14px; 
            text-align: center;
        }

        .footer { margin-top: 1cm; font-size: 0.8em; text-align: center; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <table width="100%">
            <tr>
                <td style="width: 20%; text-align: left;">
                    <img src="<?= $logoUrl ?>" alt="Logo">
                </td>
                <td style="width: 80%; text-align: center;">
                    <h1>Receta Médica</h1>
                </td>
            </tr>
        </table>
    </div>

    <div class="separator"></div>
    <div class="prescription-info">
        <div style="margin: 0.5cm 0; font-size: 14px; min-height: 3cm;">
            <!-- <?= nl2br(h($recetaConsulta->descripcion)) ?> -->
             <?= $receta->descripcion ?>

        </div>
    </div>

</body>
</html>