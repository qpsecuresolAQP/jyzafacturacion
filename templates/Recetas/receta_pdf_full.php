<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receta Médica</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.3;
        }
        
        .page {
            width: 100%;
            padding: 8px;
        }
        
        /* TWO COLUMN LAYOUT usando position */
        .columns-wrapper {
            position: relative;
            width: 100%;
            height: 175mm;
        }
        
        .left-column {
            position: absolute;
            left: 0;
            top: 0;
            width: 46%;
            padding-right: 12px;
        }
        
        .right-column {
            position: absolute;
            right: 0;
            top: 1.5rem;
            width: 48%;
            padding-left: 12px;
            padding-right: 15px;
        }
        
        /* HEADER */
        .header {
            margin-bottom: 8px;
            padding-bottom: 6px;
            position: relative;
            min-height: auto;
        }
        
        .logo {
            position: absolute;
            left: 0;
            top: 4px;
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #4A90E2 0%, #2E5C8A 100%);
            text-align: center;
            line-height: 35px;
            color: white;
            font-size: 24px;
            font-weight: bold;
            border-radius: 3px;
        }
        
        .header-info {
            margin-left: 80px;
            padding-top: 8px;
            margin-top: 7px;
        }
        
        .clinic-name {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .clinic-details {
            font-size: 9px;
            line-height: 1.3;
        }
        
        .header-title {
            text-align: center;
            margin-top: 10px;
            width: 100%;
        }
        
        .receta-title {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .specialty {
            font-size: 11px;
            font-weight: bold;
        }

        .field-line {
            width: 100%;
            display: table;
            table-layout: fixed;
            margin-bottom: 4px;
        }

        .field-cell {
            display: table-cell;
            vertical-align: bottom;
            white-space: nowrap;
        }

        .field-label {
            font-size: 9px;
            font-weight: bold;
            padding-right: 3px;
            background-color: #000;
            color: white;
            padding: 2px 4px;
            display: inline-block;
            margin-right: 3px;
        }

        .field-value {
            font-size: 9px;
            border-bottom: 1px solid #000;
            width: 100%;
            display: table-cell;
            vertical-align: bottom;
        }

        .fields-row {
            width: 100%;
            display: table;
            table-layout: fixed;
            margin-bottom: 4px;
        }

        .fields-row-cell {
            display: table-cell;
            vertical-align: bottom;
            padding-right: 6px;
        }

        .fields-row-cell:last-child {
            padding-right: 0;
        }

        .inline-label {
            font-size: 9px;
            font-weight: bold;
        }

        .inline-value {
            font-size: 9px;
            border-bottom: 1px solid #000;
            display: inline-block;
            width: 100%;
        }

        .checkbox-fields {
            width: 100%;
            margin-top: 4px;
            margin-bottom: 4px;
            font-size: 9px;
        }

        .checkbox-row {
            width: 100%;
            display: table;
            table-layout: fixed;
        }

        .checkbox-col {
            display: table-cell;
            vertical-align: top;
            padding-right: 4px;
        }

        .checkbox-col:last-child {
            padding-right: 0;
        }

        .column-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 3px;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .checkbox-item {
            margin-bottom: 2px;
            font-size: 9px;
            white-space: nowrap;
        }

        .cb-box {
            display: inline-block;
            width: 7px;
            height: 7px;
            border: 1px solid #333;
            margin-right: 2px;
            vertical-align: middle;
        }

        .checkbox-item.checked-item .cb-box {
            background-color: #000;
            border-color: #000;
        }

        .checkbox-item.checked-item {
            font-weight: bold;
        }

        .rp-section {
            margin-top: 6px;
        }

        .rp-title {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .rp-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        .rp-table th {
            background-color: transparent;
            border: none;
            border-bottom: 1px solid #333;
            padding: 2px 3px;
            font-weight: bold;
            text-align: left;
            font-size: 10px;
        }

        .rp-table td {
            border: none;
            padding: 2px 3px;
            vertical-align: top;
            font-size: 9px;
        }

        .rp-table .col-med { width: 44%; }
        .rp-table .col-conc { width: 22%; }
        .rp-table .col-forma { width: 22%; }
        .rp-table .col-cant { width: 12%; }

        .med-ind-code {
            font-size: 9px;
            font-weight: bold;
            color: #0066cc;
        }

        .med-dci-small {
            font-size: 8px;
            color: #555;
        }

        .medications-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 9px;
        }
        
        .medications-table th,
        .medications-table td {
            padding: 2px 3px;
            vertical-align: top;
        }

        .medications-table th {
            background-color: #e8e8e8;
            font-weight: bold;
            text-align: left;
            font-size: 10px;
            border: 1px solid #666;
            border-bottom: 2px solid #000;
        }

        .medications-table td {
            border: none;
            border-bottom: 1px solid #ddd;
        }

        .col-num { text-align: center; }
        .col-duracion { text-align: center; }
        
        .med-code {
            color: #0066cc;
            font-weight: bold;
            font-size: 9px;
        }
        
        .med-dci {
            font-size: 8px;
            color: #333;
            margin-top: 1px;
        }

        .diagnostico {
            margin-bottom: 8px;
            font-size: 9px;
        }

        .diag-label {
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .signatures {
            margin-top: 50px;
            margin-bottom: 8px;
            width: 100%;
        }
        
        .signature {
            width: 48%;
            display: inline-block;
            text-align: center;
            vertical-align: top;
        }
        
        .sig-line {
            border-top: 1px solid #000;
            margin-bottom: 4px;
        }
        
        .sig-name {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .sig-title {
            font-size: 7.5px;
        }

        .right-header {
            margin-bottom: 8px;
            padding-bottom: 6px;
            text-align: left;
        }
        
        .right-title {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .right-clinic-name {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 4px;
        }
        
        .right-patient-name {
            font-size: 10px;
            font-weight: bold;
        }


        /* FOOTER - NUEVO DISEÑO */
        /* ===================== */
        .column-footer {
            position: fixed;
            bottom: 10mm;
            left: 10mm;
            right: 10mm;
            width: calc(100% - 20mm);
            padding-top: 6px;
        }

        /* Ocultar footers dentro de columnas, solo usar el global */
        .left-column .column-footer,
        .right-column .column-footer {
            display: none;
        }

        .footer-box {
            display: inline-block;
            width: 12px;
            height: 14px;
            border: 1px solid #333;
            margin-right: 1px;
            vertical-align: middle;
            font-size: 8px;
            text-align: center;
            line-height: 14px;
        }

        .footer-box-sep {
            display: inline-block;
            vertical-align: middle;
            font-size: 9px;
            margin: 0 1px;
        }

        .footer-label {
            font-size: 7.5px;
            display: block;
            margin-top: 2px;
            text-align: center;
        }

        @page {
            size: A4 landscape;
            margin: 10mm;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="columns-wrapper">

            <!-- COLUMNA IZQUIERDA -->
            <div class="left-column">

                <!-- HEADER -->
                <div class="header">
                    <div style="position: relative; min-height: 55px; margin-top: 8px;">
                        <div class="logo">
                            <?php if (!empty($logoUrl)): ?>
                                <img src="<?= $logoUrl ?>" style="width: 100%; height: 100%; object-fit: contain; border-radius: 3px;">
                            <?php else: ?>
                                Dr
                            <?php endif; ?>
                        </div>
                        <div class="header-info" >
                            <div class="clinic-name">ODENT odontologia integral</div>
                        </div>
                    </div>
                </div>

                <!-- NOMBRE Y APELLIDOS -->
                <div class="field-line">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="font-size:10px; font-weight:bold; white-space:nowrap; padding-right:3px; vertical-align:bottom;">Nombres y Apellidos</td>
                            <td style="border-bottom:1px solid #000; font-size:10px; vertical-align:bottom; width:100%;">
                                <?php 
                                if (!empty($historiasClinicas->paciente)):
                                    echo strtoupper(h($historiasClinicas->paciente->nombre . ' ' . $historiasClinicas->paciente->apellido));
                                endif;
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- OA / FECHA -->
                <div style="width:100%; display:table; table-layout:fixed; margin-bottom:3px;">
                    <div style="display:table-cell; width:40%; padding-right:6px; vertical-align:bottom;">
                        <table width="100%" cellpadding="0" cellspacing="0"><tr>
                            <td style="font-size:10px;font-weight:bold;white-space:nowrap;padding-right:3px;vertical-align:bottom;">OA:</td>
                            <td style="font-size:10px;vertical-align:bottom;width:100%;"><?= !empty($consulta) ? h($consulta->id) : '' ?></td>
                        </tr></table>
                    </div>
                    <div style="display:table-cell; width:60%; vertical-align:bottom;">
                        <table width="100%" cellpadding="0" cellspacing="0"><tr>
                            <td style="font-size:10px;font-weight:bold;white-space:nowrap;padding-right:3px;vertical-align:bottom;">Fecha:</td>
                            <td style="font-size:10px;vertical-align:bottom;width:100%;"><?= !empty($consulta) ? $consulta->created->format('d/m/Y H:i') : '' ?></td>
                        </tr></table>
                    </div>
                </div>

                <!-- EDAD / PESO / H.CL. / SIS -->
                <div style="width:100%; display:table; table-layout:fixed; margin-bottom:3px;">
                    <div style="display:table-cell; width:20%; padding-right:4px; vertical-align:bottom;">
                        <table width="100%" cellpadding="0" cellspacing="0"><tr>
                            <td style="font-size:10px;font-weight:bold;white-space:nowrap;padding-right:2px;vertical-align:bottom;">Edad:</td>
                            <td style="font-size:10px;vertical-align:bottom;width:100%;"><?= !empty($historiasClinicas->edad) ? h($historiasClinicas->edad) : '' ?></td>
                        </tr></table>
                    </div>
                    <div style="display:table-cell; width:22%; padding-right:4px; vertical-align:bottom;">
                        <table width="100%" cellpadding="0" cellspacing="0"><tr>
                            <td style="font-size:10px;font-weight:bold;white-space:nowrap;padding-right:2px;vertical-align:bottom;">Peso:</td>
                            <td style="font-size:10px;vertical-align:bottom;width:100%;"><?= !empty($receta->peso) ? h($receta->peso) : '' ?></td>
                        </tr></table>
                    </div>
                    <div style="display:table-cell; width:28%; padding-right:4px; vertical-align:bottom;">
                        <table width="100%" cellpadding="0" cellspacing="0"><tr>
                            <td style="font-size:10px;font-weight:bold;white-space:nowrap;padding-right:2px;vertical-align:bottom;">H. Cl.:</td>
                            <td style="font-size:10px;vertical-align:bottom;width:100%;"><?= !empty($receta->h_cl) ? h($receta->h_cl) : '' ?></td>
                        </tr></table>
                    </div>
                    <!-- <div style="display:table-cell; width:30%; vertical-align:bottom;">
                        <table width="100%" cellpadding="0" cellspacing="0"><tr>
                            <td style="font-size:10px;font-weight:bold;white-space:nowrap;padding-right:2px;vertical-align:bottom;">SIS:</td>
                            <td style="font-size:10px;vertical-align:bottom;width:100%;"><?= !empty($receta->sis) ? h($receta->sis) : '' ?></td>
                        </tr></table>
                    </div> -->
                </div>

                <!-- DIAGNÓSTICO / PARTICULAR -->
                <div style="width:100%; display:table; table-layout:fixed; margin-bottom:4px;">
                    <div style="display:table-cell; width:55%; padding-right:4px; vertical-align:bottom;">
                        <table width="100%" cellpadding="0" cellspacing="0"><tr>
                            <td style="font-size:10px;font-weight:bold;white-space:nowrap;padding-right:3px;vertical-align:bottom;">Diagnóstico:</td>
                            <td style="font-size:10px;vertical-align:bottom;width:100%;">
                                <?= !empty($consulta->diagnostico) ? h($consulta->diagnostico) : '' ?>
                            </td>
                        </tr></table>
                    </div>
                    <!-- <div style="display:table-cell; width:45%; vertical-align:bottom;">
                        <table width="100%" cellpadding="0" cellspacing="0"><tr>
                            <td style="font-size:10px;font-weight:bold;white-space:nowrap;padding-right:3px;vertical-align:bottom;">Particular:</td>
                            <td style="font-size:10px;vertical-align:bottom;width:100%;"><?= !empty($receta->particular) ? h($receta->particular) : '' ?></td>
                        </tr></table>
                    </div> -->
                </div>

                <!-- CIE-10 -->
                <div style="margin-bottom:6px; font-size:9px;">
                    <div style="font-weight:bold; margin-bottom:2px;">CIE-10:</div>
                    <?php if (!empty($consulta->consultas_cie) && count($consulta->consultas_cie) > 0): ?>
                        <?php foreach ($consulta->consultas_cie as $cie): ?>
                            <?php 
                                $clave = '';
                                $descripcion = '';
                                if (!empty($cie->diagnosticoscie10)) {
                                    $clave = $cie->diagnosticoscie10->clave;
                                    $descripcion = $cie->diagnosticoscie10->descripcion;
                                } elseif (!empty($cie->categoriascie10)) {
                                    $clave = $cie->categoriascie10->clave;
                                    $descripcion = $cie->categoriascie10->descripcion;
                                }
                                if (!empty($clave)):
                            ?>
                            <div style="margin-bottom:8px;">
                                <strong><?= h($clave) ?></strong> - <?= h($descripcion) ?>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- CHECKBOXES: USUARIO / ATENCIÓN / ESPECIALIDAD MÉDICA (COMENTADO - DE MOMENTO NO NECESARIO) -->
                <!-- <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:8px;">
                    <tr>
                       
                        <td style="vertical-align:top; padding:3px 4px; width:33%;">
                            <div class="column-title" style="text-align:center; text-decoration:none;">USUARIO</div>
                            <div class="checkbox-item <?= ($receta->tipo_usuario === 'DEMANDA') ? 'checked-item' : '' ?>">
                                <span class="cb-box"></span> DEMANDA
                            </div>
                            <div class="checkbox-item <?= ($receta->tipo_usuario === 'SIS') ? 'checked-item' : '' ?>">
                                <span class="cb-box"></span> SIS
                            </div>
                            <div class="checkbox-item <?= ($receta->tipo_usuario === 'INTERVENCIÓN SANITARIA') ? 'checked-item' : '' ?>">
                                <span class="cb-box"></span> INTERVENCIÓN SANITARIA
                            </div>
                            <div class="checkbox-item <?= ($receta->tipo_usuario === 'SOAT') ? 'checked-item' : '' ?>">
                                <span class="cb-box"></span> SOAT
                            </div>
                            <div class="checkbox-item <?= ($receta->tipo_usuario === 'OTROS') ? 'checked-item' : '' ?>">
                                <span class="cb-box"></span> OTROS
                            </div>
                        </td>

                        
                        <td style="vertical-align:top; padding:3px 4px; width:33%;">
                            <div class="column-title" style="text-align:center; text-decoration:none;">ATENCIÓN</div>
                            <div class="checkbox-item <?= ($receta->tipo_atencion === 'CONSULTA EXTERNA') ? 'checked-item' : '' ?>">
                                <span class="cb-box"></span> CONSULTA EXTERNA
                            </div>
                            <div class="checkbox-item <?= ($receta->tipo_atencion === 'EMERGENCIA') ? 'checked-item' : '' ?>">
                                <span class="cb-box"></span> EMERGENCIA
                            </div>
                            <div class="checkbox-item <?= ($receta->tipo_atencion === 'HOSPITALIZACIÓN') ? 'checked-item' : '' ?>">
                                <span class="cb-box"></span> HOSPITALIZACIÓN
                            </div>
                            <div class="checkbox-item <?= ($receta->tipo_atencion === 'PARTICULAR') ? 'checked-item' : '' ?>">
                                <span class="cb-box"></span> PARTICULAR
                            </div>
                            <div class="checkbox-item <?= ($receta->tipo_atencion === 'CAMA') ? 'checked-item' : '' ?>">
                                <span class="cb-box"></span> CAMA: <span style="border-bottom:1px solid #000; display:inline-block; width:25px;"><?= !empty($receta->cama) ? h($receta->cama) : '' ?></span>
                            </div>
                        </td>

                        
                        <td style="vertical-align:top; padding:3px 4px; width:34%;">
                            <div class="column-title" style="text-align:center; text-decoration:none;">ESPECIALIDAD MÉDICA</div>
                            <div class="checkbox-item <?= ($receta->especialidad_medica === 'CIRUGÍA') ? 'checked-item' : '' ?>">
                                <span class="cb-box"></span> CIRUGÍA
                            </div>
                            <div class="checkbox-item <?= ($receta->especialidad_medica === 'GINECO-OBSTETRICIA') ? 'checked-item' : '' ?>">
                                <span class="cb-box"></span> GINECO-OBSTETRICIA
                            </div>
                            <div style="display:table; width:100%; table-layout:fixed;">
                                <div style="display:table-cell; width:50%;">
                                    <div class="checkbox-item <?= ($receta->especialidad_medica === 'MEDICINA') ? 'checked-item' : '' ?>">
                                        <span class="cb-box"></span> MEDICINA
                                    </div>
                                </div>
                                <div style="display:table-cell; width:50%;">
                                    <div class="checkbox-item <?= ($receta->especialidad_medica === 'DENTAL') ? 'checked-item' : '' ?>">
                                        <span class="cb-box"></span> DENTAL
                                    </div>
                                </div>
                            </div>
                            <div class="checkbox-item <?= ($receta->especialidad_medica === 'ONCOLOGÍA') ? 'checked-item' : '' ?>">
                                <span class="cb-box"></span> ONCOLOGÍA
                            </div>
                            <div style="display:table; width:100%; table-layout:fixed;">
                                <div style="display:table-cell; width:50%;">
                                    <div class="checkbox-item <?= ($receta->especialidad_medica === 'PEDIATRÍA') ? 'checked-item' : '' ?>">
                                        <span class="cb-box"></span> PEDIATRÍA
                                    </div>
                                </div>
                                <div style="display:table-cell; width:50%;">
                                    <div class="checkbox-item <?= ($receta->especialidad_medica === 'NEONATO') ? 'checked-item' : '' ?>">
                                        <span class="cb-box"></span> NEONATO
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table> -->

                <!-- TABLA Rp. MEDICAMENTOS (estilo formulario físico) -->
                <table class="rp-table">
                    <thead>
                        <tr>
                            <th class="col-med">Rp. Medicamento o Insumo<br><span style="font-weight:normal; font-size:6.5px;">(Obligatorio el DCI)</span></th>
                            <th class="col-conc">Concentración</th>
                            <th class="col-forma">Forma Farmacéutica</th>
                            <th class="col-cant">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($receta->recetas_medicamentos) && count($receta->recetas_medicamentos) > 0): ?>
                            <?php foreach ($receta->recetas_medicamentos as $med): ?>
                            <tr>
                                <td>
                                    <div class="med-ind-code"><?= strtoupper(h($med->medicamento ? $med->medicamento->nombre : ($med->nombre_medicamento ?? ''))) ?></div>
                                    <!-- <div class="med-dci-small">DCI: <?= strtoupper(h($med->medicamento->dci ?? $med->medicamento->nombre ?? '')) ?></div> -->
                                </td>
                                <td><?= strtoupper(h($med->concentracion ?? '')) ?></td>
                                <td><?= strtoupper(h($med->formas_farmaceutica->nombre ?? $med->forma_farmaceutica->nombre ?? '')) ?></td>
                                <td><?= h($med->cantidad_total ?? $med->cantidad ?? '') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- FOOTER IZQUIERDO oculto, se usa el global -->

            </div><!-- /left-column -->

            <!-- COLUMNA DERECHA -->
            <div class="right-column">
                <!-- HEADER DERECHO -->
                <div class="right-header">
                    <div class="right-clinic-name">ODENT odontologia integral</div>
                    <div class="right-title">Indicaciones</div>
                    
                    <!-- NOMBRE Y APELLIDOS similar al izquierdo -->
                    <div class="field-line" style="margin-bottom:8px;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="font-size:10px; font-weight:bold; white-space:nowrap; padding-right:3px; vertical-align:bottom;">Nombres y Apellidos</td>
                                <td style="border-bottom:1px solid #000; font-size:10px; vertical-align:bottom; width:100%;">
                                    <?php 
                                    if (!empty($historiasClinicas->paciente)):
                                        echo strtoupper(h($historiasClinicas->paciente->nombre . ' ' . $historiasClinicas->paciente->apellido));
                                    endif;
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- TABLA DE MEDICAMENTOS DETALLADA -->
                <table class="medications-table">
                    <thead>
                        <tr>
                            <th class="col-num" width="5%">#</th>
                            <th class="col-med-r" width="38%">Medicamento o Insumo</th>
                            <th class="col-dosis" width="18%">Dosis</th>
                            <th class="col-via" width="12%">Vía</th>
                            <th class="col-frecuencia" width="22%">Frecuencia</th>
                            <th class="col-duracion" width="5%">Duración</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($receta->recetas_medicamentos) && count($receta->recetas_medicamentos) > 0): ?>
                            <?php $num = 1; ?>
                            <?php foreach ($receta->recetas_medicamentos as $med): ?>
                            <tr>
                                <td class="col-num"><?= $num++ ?></td>
                                <td>
                                    <div class="med-code">
                                        <?= h($med->medicamento ? $med->medicamento->codigo : ($med->codigo_medicamento ?? '000000000')) ?> 
                                        <?= strtoupper(h($med->medicamento ? $med->medicamento->nombre : ($med->nombre_medicamento ?? ''))) ?> 
                                        <?= strtoupper(h($med->medicamento ? $med->medicamento->concentracion : '')) ?> 
                                        <!-- X <?= h($med->cantidad_total ?? $med->cantidad ?? '') ?> -->
                                    </div>
                                    <!-- <div class="med-dci">DCI: <?= strtoupper(h($med->medicamento->dci ?? $med->medicamento->nombre ?? '')) ?></div> -->
                                </td>
                                <td><?= h($med->dosis ?? '') ?> <?= strtoupper(h($med->formas_farmaceutica->nombre ?? $med->forma_farmaceutica->nombre ?? '')) ?></td>
                                <td><?= strtoupper(h($med->vias_administracion->nombre ?? $med->via_administracion->nombre ?? '')) ?></td>
                                <td style="font-size: 9px;"><?= nl2br(h($med->observaciones ?? '')) ?></td>
                                <td class="col-duracion"><?= h($med->duracion_dias ?? '') ?> días</td>
                            </tr>
                            <!-- Espacio libre debajo de cada medicamento -->
                            <tr><td colspan="6" style="height:18px;"></td></tr>
                            <tr><td colspan="6" style="height:18px;"></td></tr>
                            <tr><td colspan="6" style="height:18px;"></td></tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- FOOTER DERECHO oculto, se usa el global -->

            </div><!-- /right-column -->

            <!-- FOOTER GLOBAL - anclado al fondo del wrapper -->
            <div class="column-footer">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <!-- IZQUIERDO: Fecha de Atención -->
                        <td style="width:15%; vertical-align:bottom;">
                            <div>
                                <?php 
                                    $fechaAtencion = !empty($consulta) ? $consulta->created->format('d/m/Y') : '';
                                    $partes = explode('/', $fechaAtencion);
                                    $dia = isset($partes[0]) ? $partes[0] : '';
                                    $mes = isset($partes[1]) ? $partes[1] : '';
                                    $anio = isset($partes[2]) ? $partes[2] : '';
                                ?>
                                <span class="footer-box"><?= substr($dia, 0, 1) ?></span><span class="footer-box"><?= substr($dia, 1, 1) ?></span>
                                <span class="footer-box-sep">/</span>
                                <span class="footer-box"><?= substr($mes, 0, 1) ?></span><span class="footer-box"><?= substr($mes, 1, 1) ?></span>
                                <span class="footer-box-sep">/</span>
                                <span class="footer-box"><?= substr($anio, 0, 1) ?></span><span class="footer-box"><?= substr($anio, 1, 1) ?></span><span class="footer-box"><?= substr($anio, 2, 1) ?></span><span class="footer-box"><?= substr($anio, 3, 1) ?></span>
                            </div>
                            <span class="footer-label">Fecha de Atención:</span>
                        </td>
                        <!-- IZQUIERDO: Firma y Sello -->
                        <td style="width:18%; vertical-align:bottom; padding:0 8px; text-align:center;">
                            <div style="border-bottom:1px solid #000; height:18px;"></div>
                            <span class="footer-label">Firma y Sello</span>
                        </td>
                        <!-- IZQUIERDO: Válido Hasta -->
                        <td style="width:15%; vertical-align:bottom;">
                            <div>
                                <?php 
                                    $fechaValidoHasta = !empty($receta) && !empty($receta->valido_hasta) ? $receta->valido_hasta->format('d/m/Y') : '';
                                    $partes = explode('/', $fechaValidoHasta);
                                    $diaVH = isset($partes[0]) ? $partes[0] : '';
                                    $mesVH = isset($partes[1]) ? $partes[1] : '';
                                    $anioVH = isset($partes[2]) ? $partes[2] : '';
                                ?>
                                <span class="footer-box"><?= substr($diaVH, 0, 1) ?></span><span class="footer-box"><?= substr($diaVH, 1, 1) ?></span>
                                <span class="footer-box-sep">/</span>
                                <span class="footer-box"><?= substr($mesVH, 0, 1) ?></span><span class="footer-box"><?= substr($mesVH, 1, 1) ?></span>
                                <span class="footer-box-sep">/</span>
                                <span class="footer-box"><?= substr($anioVH, 0, 1) ?></span><span class="footer-box"><?= substr($anioVH, 1, 1) ?></span><span class="footer-box"><?= substr($anioVH, 2, 1) ?></span><span class="footer-box"><?= substr($anioVH, 3, 1) ?></span>
                            </div>
                            <span class="footer-label">Válido Hasta:</span>
                        </td>
                        <!-- SEPARADOR CENTRAL -->
                        <td style="width:4%;"></td>
                        <!-- DERECHO: Fecha de Atención -->
                        <td style="width:15%; vertical-align:bottom;">
                            <div>
                                <?php 
                                    $fechaAtencion = !empty($consulta) ? $consulta->created->format('d/m/Y') : '';
                                    $partes = explode('/', $fechaAtencion);
                                    $dia = isset($partes[0]) ? $partes[0] : '';
                                    $mes = isset($partes[1]) ? $partes[1] : '';
                                    $anio = isset($partes[2]) ? $partes[2] : '';
                                ?>
                                <span class="footer-box"><?= substr($dia, 0, 1) ?></span><span class="footer-box"><?= substr($dia, 1, 1) ?></span>
                                <span class="footer-box-sep">/</span>
                                <span class="footer-box"><?= substr($mes, 0, 1) ?></span><span class="footer-box"><?= substr($mes, 1, 1) ?></span>
                                <span class="footer-box-sep">/</span>
                                <span class="footer-box"><?= substr($anio, 0, 1) ?></span><span class="footer-box"><?= substr($anio, 1, 1) ?></span><span class="footer-box"><?= substr($anio, 2, 1) ?></span><span class="footer-box"><?= substr($anio, 3, 1) ?></span>
                            </div>
                            <span class="footer-label">Fecha de Atención:</span>
                        </td>
                        <!-- DERECHO: Firma y Sello -->
                        <td style="width:18%; vertical-align:bottom; padding:0 8px; text-align:center;">
                            <div style="border-bottom:1px solid #000; height:18px;"></div>
                            <span class="footer-label">Firma y Sello</span>
                        </td>
                        <!-- DERECHO: Válido Hasta -->
                        <td style="width:15%; vertical-align:bottom;">
                            <div>
                                <?php 
                                    $fechaValidoHasta = !empty($receta) && !empty($receta->valido_hasta) ? $receta->valido_hasta->format('d/m/Y') : '';
                                    $partes = explode('/', $fechaValidoHasta);
                                    $diaVH = isset($partes[0]) ? $partes[0] : '';
                                    $mesVH = isset($partes[1]) ? $partes[1] : '';
                                    $anioVH = isset($partes[2]) ? $partes[2] : '';
                                ?>
                                <span class="footer-box"><?= substr($diaVH, 0, 1) ?></span><span class="footer-box"><?= substr($diaVH, 1, 1) ?></span>
                                <span class="footer-box-sep">/</span>
                                <span class="footer-box"><?= substr($mesVH, 0, 1) ?></span><span class="footer-box"><?= substr($mesVH, 1, 1) ?></span>
                                <span class="footer-box-sep">/</span>
                                <span class="footer-box"><?= substr($anioVH, 0, 1) ?></span><span class="footer-box"><?= substr($anioVH, 1, 1) ?></span><span class="footer-box"><?= substr($anioVH, 2, 1) ?></span><span class="footer-box"><?= substr($anioVH, 3, 1) ?></span>
                            </div>
                            <span class="footer-label">Válido Hasta:</span>
                        </td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</body>
</html>