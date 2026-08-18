<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pagos de Doctores</title>
    <style>
        @page {
            margin: 12mm 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #222;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header img {
            max-width: 140px;
            margin-bottom: 6px;
        }

        .title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .subtitle {
            font-size: 10px;
            color: #555;
            margin-bottom: 3px;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 14px 0;
        }

        .summary td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            vertical-align: top;
        }

        .summary .label {
            font-size: 9px;
            color: #666;
            display: block;
            margin-bottom: 2px;
        }

        .summary .value {
            font-size: 13px;
            font-weight: 700;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .table th {
            background: #f3f5f7;
            color: #222;
            border: 1px solid #cfd6dd;
            padding: 6px;
            text-align: left;
        }

        .table td {
            border: 1px solid #e0e0e0;
            padding: 6px;
            vertical-align: top;
        }

        .table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .muted {
            color: #777;
        }

        .payment-line {
            margin-bottom: 2px;
            line-height: 1.25;
        }

        .footer {
            margin-top: 10px;
            font-size: 9px;
            color: #666;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <?php if (!empty($logoUrl)): ?>
            <img src="<?= h($logoUrl) ?>" alt="Logo">
        <?php endif; ?>
        <div class="title">Pagos de Doctores</div>
        <div class="subtitle">Del <?= h($fechaDesde ?? '') ?> al <?= h($fechaHasta ?? '') ?></div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <span class="label">Doctores con producción</span>
                <span class="value"><?= (int)($resumen['total_doctores'] ?? 0) ?></span>
            </td>
            <td>
                <span class="label">Total tratamientos</span>
                <span class="value">S/ <?= number_format((float)($resumen['total_tratamientos'] ?? 0), 2) ?></span>
            </td>
            <td>
                <span class="label">Total a pagar</span>
                <span class="value">S/ <?= number_format((float)($resumen['total_pagar'] ?? 0), 2) ?></span>
            </td>
            <td>
                <span class="label">Comprobantes</span>
                <span class="value"><?= (int)($resumen['total_comprobantes'] ?? 0) ?></span>
            </td>
        </tr>
    </table>

    <div style="font-size: 12px; font-weight: 700; margin: 4px 0 6px 0;">Total a pagar por doctor</div>
    <table class="table" style="margin-bottom: 14px;">
        <thead>
            <tr>
                <th style="width: 34%;">Doctor</th>
                <th style="width: 18%;">Total tratamientos</th>
                <th style="width: 12%;">%</th>
                <th style="width: 18%;">Total a pagar</th>
                <th style="width: 18%;">Comprobantes</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($doctoresConsolidados)): ?>
                <?php foreach ($doctoresConsolidados as $doctor): ?>
                    <tr>
                        <td><?= h($doctor['doctor']) ?></td>
                        <td>S/ <?= number_format((float)$doctor['total_tratamientos'], 2) ?></td>
                        <td><?= number_format((float)$doctor['porcentaje'], 2) ?>%</td>
                        <td>
                            <strong>S/ <?= number_format((float)$doctor['total_pagar'], 2) ?></strong>
                            <?php if (!empty($doctor['metodos_pago'])): ?>
                                <div style="margin-top: 4px; font-size: 9px; line-height: 1.25;">
                                    <?php foreach ($doctor['metodos_pago'] as $metodo): ?>
                                        <div style="margin-bottom: 2px;">
                                            <strong><?= h($metodo['metodo']) ?></strong>: S/ <?= number_format((float)$metodo['pago_doctor_total'], 2) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= (int)$doctor['comprobantes'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="muted" style="text-align:center; padding: 14px;">No hay doctores para el rango seleccionado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="font-size: 12px; font-weight: 700; margin: 4px 0 6px 0;">Consolidado por método de pago</div>
    <table class="table" style="margin-bottom: 14px;">
        <thead>
            <tr>
                <th style="width: 34%;">Método</th>
                <th style="width: 22%;">Monto total</th>
                <th style="width: 22%;">Pago doctor total</th>
                <th style="width: 22%;">Movimientos</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($metodosConsolidados)): ?>
                <?php foreach ($metodosConsolidados as $metodo): ?>
                    <tr>
                        <td><?= h($metodo['metodo']) ?></td>
                        <td>S/ <?= number_format((float)$metodo['monto_total'], 2) ?></td>
                        <td><strong>S/ <?= number_format((float)$metodo['pago_doctor_total'], 2) ?></strong></td>
                        <td><?= (int)$metodo['movimientos'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="muted" style="text-align:center; padding: 14px;">No hay métodos de pago para el rango seleccionado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 10%;">Fecha</th>
                <th style="width: 16%;">Doctor</th>
                <th style="width: 14%;">Cliente</th>
                <th style="width: 12%;">Comprobante</th>
                <th style="width: 8%;">Tipo</th>
                <th style="width: 24%;">Métodos de pago</th>
                <th style="width: 8%;">Total Trat.</th>
                <th style="width: 5%;">%</th>
                <th style="width: 7%;">Pago</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($resultado)): ?>
                <?php foreach ($resultado as $row): ?>
                    <tr>
                        <td><?= h($row['fecha']->format('Y-m-d H:i')) ?></td>
                        <td><?= h($row['doctor']) ?></td>
                        <td><?= h($row['cliente']) ?></td>
                        <td><?= h($row['comprobante']) ?></td>
                        <td><?= h($row['tipo_doc']) ?></td>
                        <td>
                            <?php if (!empty($row['metodos_pago'])): ?>
                                <?php foreach ($row['metodos_pago'] as $pago): ?>
                                    <div class="payment-line">
                                        <strong><?= h($pago['metodo']) ?></strong>: S/ <?= number_format((float)$pago['monto'], 2) ?>
                                        <span class="muted">| Pago doctor: S/ <?= number_format((float)$pago['pago_doctor'], 2) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            S/ <?= number_format((float)$row['base_doctor'], 2) ?>
                            <?php if (!empty($row['total_distribuido'])): ?>
                                <div class="muted" style="font-size: 9px;">
                                    (Bruto S/ <?= number_format((float)$row['base_doctor_bruta'], 2) ?> − S/ <?= number_format((float)$row['total_distribuido'], 2) ?> Lab/Materiales)
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= number_format((float)$row['porcentaje'], 2) ?>%</td>
                        <td><strong>S/ <?= number_format((float)$row['pago_doctor'], 2) ?></strong></td>
                    </tr>
                    <!-- Detalles de items -->
                    <?php if (!empty($row['items_detalles'])): ?>
                    <tr style="background-color: #f5f5f5;">
                        <td colspan="9" style="padding: 10px;">
                            <div style="font-size: 10px;">
                                <strong style="display: block; margin-bottom: 6px;">📋 Items:</strong>
                                <table style="width: 100%; font-size: 9px; border-collapse: collapse;">
                                    <tr style="border-bottom: 1px solid #ddd; background-color: #e9ecef;">
                                        <th style="padding: 4px; text-align: left; width: 40%;">Descripción</th>
                                        <th style="padding: 4px; text-align: left; width: 15%;">Tipo</th>
                                        <th style="padding: 4px; text-align: right; width: 10%;">Cant.</th>
                                        <th style="padding: 4px; text-align: right; width: 12%;">V. Unit.</th>
                                        <th style="padding: 4px; text-align: right; width: 12%;">Total</th>
                                        <th style="padding: 4px; text-align: center; width: 11%;">¿Pagar?</th>
                                    </tr>
                                    <?php foreach ($row['items_detalles'] as $item): ?>
                                    <?php
                                        $colorTipo = match ($item['tipo']) {
                                            'Tratamiento' => '#28a745',
                                            'Examen' => '#17a2b8',
                                            default => '#ffc107',
                                        };
                                    ?>
                                    <tr style="border-bottom: 1px solid #eee; background-color: <?= $item['tipo'] === 'Tratamiento' ? '#d4edda' : '#fff' ?>;">
                                        <td style="padding: 4px;"><?= h($item['descripcion']) ?></td>
                                        <td style="padding: 4px;">
                                            <strong style="color: <?= $colorTipo ?>;">
                                                <?= h($item['tipo']) ?>
                                            </strong>
                                        </td>
                                        <td style="padding: 4px; text-align: right;"><?= number_format((float)$item['cantidad'], 2) ?></td>
                                        <td style="padding: 4px; text-align: right;">S/ <?= number_format((float)$item['valor_unitario'], 2) ?></td>
                                        <td style="padding: 4px; text-align: right; font-weight: bold;">S/ <?= number_format((float)$item['total'], 2) ?></td>
                                        <td style="padding: 4px; text-align: center;">
                                            <?php if (trim($item['tipo']) === 'Tratamiento'): ?>
                                                <span style="color: #28a745; font-weight: bold;">Sí</span>
                                            <?php else: ?>
                                                <span style="color: #6c757d;">No</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </table>
                                <div style="margin-top: 6px; color: #666; font-size: 8px;">
                                    <strong>Nota:</strong> Solo se paga el <?= number_format((float)$row['porcentaje'], 2) ?>% de los tratamientos. Los productos y exámenes son de la clínica.
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="muted" style="text-align:center; padding: 14px;">No hay registros para mostrar.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Generado el <?= date('Y-m-d H:i') ?>
    </div>
</body>
</html>
