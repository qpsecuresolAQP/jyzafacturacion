<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Presupuesto</title>
    <style>
        /* Page sizing for PDF engines like Dompdf */
        @page {
            margin: 12mm 8mm;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 0;
            font-size: 11px;
            line-height: 1.15;
            color: #222;
        }

        .container {
            max-width: 780px;
            margin: 8px auto;
            padding: 10px;
            background-color: #fff;
        }

        /* Encabezado centrado */
        .header {
            text-align: center;
            margin-bottom: 8px;
        }

        .header img {
            width: 120px;
            margin-bottom: 6px;
        }

        .hed-cotizacion {
            background-color: transparent;
            border-radius: 6px;
            padding: 6px 8px;
            text-align: center;
            margin: 0 auto;
            width: 360px;
        }

        .hed-cotizacion h1 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: #2F5081;
        }

        .hed-cotizacion p {
            margin: 2px 0;
            font-size: 10.5px;
            color: #111111;
        }

        /* Información General */
        .info {
            margin-bottom: 8px;
            font-size: 11px;
        }

        .info p {
            margin: 3px 6px;
        }

        h3 {
            font-size: 13px;
            margin: 6px 0;
        }

        /* Tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            table-layout: fixed;
            font-size: 10.5px;
        }

        table thead {
            background-color: #8FCED3;
            color: #ffffff;
            text-align: left;
        }

        table th,
        table td {
            padding: 6px 6px;
            border: 1px solid #ddd;
            vertical-align: middle;
            word-wrap: break-word;
        }

        table tbody tr:nth-child(even) {
            background-color: #fbfbfb;
        }

        table th:nth-child(1) {
            width: 8%;
        }

        table th:nth-child(2) {
            width: 52%;
        }

        table th:nth-child(3) {
            width: 12%;
        }

        table th:nth-child(4) {
            width: 14%;
        }

        table th:nth-child(5) {
            width: 14%;
        }

        table tfoot tr {
            font-weight: 700;
        }

        table tfoot td {
            text-align: left;
        }

        .total-row td {
            background-color: #8FCED3;
            color: #ffffff;
            font-weight: 700;
        }

        /* Nota */
        .footer-note {
            font-size: 10px;
            color: #555;
            margin-top: 6px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <?= $this->Html->image($logoUrl, ['alt' => 'Logo', 'style' => 'width: 180px;']) ?>
            <div class="hed-cotizacion">
                <h1>SpazioDentale</h1>
                <p>Av. Defensores del Morro Mz 05 lot. 05 Santa Teresa de Villa Chorrillos, Lima</p>
                <!-- <p>Correo electrónico: yuwer_eg@hotmail.com</p> -->
                <p>Nro de Contacto: +51 982 713 480</p>
            </div>
        </div>

        <div class="info">

            <p><strong>Fecha:</strong>
                <?= $presupuesto->modified
                    ? $presupuesto->modified->format('d/m/Y')
                    : __('No disponible') ?>
            </p>

            <?php
            $nombreMostrar = '';

            if (!empty($presupuesto->historias_clinica)) {

                $paciente = $presupuesto->historias_clinica->paciente ?? null;

                if ($paciente) {
                    $nombreMostrar = trim(
                        ($paciente->nombre ?? '') . ' ' .
                            ($paciente->apellido ?? '')
                    );
                }
            } elseif (!empty($presupuesto->nombre_apellido)) {

                $nombreMostrar = $presupuesto->nombre_apellido;
            }
            ?>

            <?php if (!empty($nombreMostrar)): ?>
                <p>
                    <strong>Nombres y Apellidos:</strong>
                    <?= h($nombreMostrar) ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($presupuesto->historias_clinica)): ?>

                <p><strong>Dirección:</strong>
                    <?= !empty($presupuesto->historias_clinica->direccion)
                        ? h($presupuesto->historias_clinica->direccion)
                        : __('No disponible') ?>
                </p>

                <p><strong>DNI:</strong>
                    <?= !empty($presupuesto->historias_clinica->dni)
                        ? h($presupuesto->historias_clinica->dni)
                        : __('No disponible') ?>
                </p>

            <?php endif; ?>

            <?php if (!empty($presupuesto->telefono)): ?>
                <p>
                    <strong>Teléfono:</strong>
                    <?= h($presupuesto->telefono) ?>
                </p>
            <?php endif; ?>

        </div>

    </div>

    <?php
    // Inicializar subtotal
    $subtotal = 0;

    // Calcular el subtotal sumando los totales de los tratamientos
    foreach ($presupuesto->presupuestos_tratamientos as $tratamiento) {
        $subtotal += $tratamiento->total;
    }
    ?>

    <!-- Tabla de Tratamientos -->
    <h3>Detalles de la Cotización</h3>
    <table>
        <thead>
            <tr>
                <th>Cantidad</th>
                <th>Ítem</th>
                <th>Descuento (%)</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($presupuesto->presupuestos_tratamientos as $tratamiento): ?>
                <tr>
                    <?php
                        $itemNombre = match ($tratamiento->tipo_item ?? 'tratamiento') {
                            'producto' => $tratamiento->producto->nombre ?? '',
                            'examen' => $tratamiento->examene->nombre ?? '',
                            default => $tratamiento->tratamiento->nombre ?? '',
                        };
                    ?>
                    <td><?= $tratamiento->cantidad ?></td>
                    <td><?= h($itemNombre) ?></td>
                    <td>
                        <?php if (empty($tratamiento->descuento)): ?>
                            Sin descuento
                        <?php elseif (($tratamiento->descuento_tipo ?? 'porcentaje') === 'monto'): ?>
                            S/ <?= number_format($tratamiento->descuento, 2) ?>
                        <?php else: ?>
                            <?= h($tratamiento->descuento) ?> %
                        <?php endif; ?>
                    </td>
                    <td><?= number_format($tratamiento->precio_unitario, 2) ?></td>
                    <td><?= number_format($tratamiento->total, 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total</td>
                <td><?= number_format($subtotal, 2) ?></td>
            </tr>
        </tfoot>
    </table>

    <!-- Nota -->
    <p class="footer-note">*Los precios incluyen IGV.</p>
    </div>
</body>

</html>