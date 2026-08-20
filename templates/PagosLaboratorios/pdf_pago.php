<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\PagosLaboratoriosHistorial $pagoHistorial
 * @var string $logoUrl
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Pago a Laboratorio</title>
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

        .table tfoot td {
            border: 1px solid #cfd6dd;
            padding: 6px;
            font-weight: 700;
            background: #f3f5f7;
        }

        .muted {
            color: #777;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            margin: 4px 0 6px 0;
        }

        .footer {
            margin-top: 24px;
            font-size: 9px;
            color: #666;
        }

        .signature {
            margin-top: 40px;
            display: table;
            width: 100%;
        }

        .signature .box {
            display: table-cell;
            width: 50%;
            text-align: center;
        }

        .signature .line {
            margin: 0 30px;
            border-top: 1px solid #333;
            padding-top: 4px;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <div class="header">
        <?php if (!empty($logoUrl)): ?>
            <img src="<?= h($logoUrl) ?>" alt="Logo">
        <?php endif; ?>
        <div class="title">Comprobante de Pago a Laboratorio</div>
        <div class="subtitle">Pago #<?= h($pagoHistorial->id) ?></div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <span class="label">Laboratorio (a quién se pagó)</span>
                <span class="value"><?= h($pagoHistorial->laboratorio->nombre ?? '-') ?></span>
            </td>
            <td>
                <span class="label">Registrado por (quién pagó)</span>
                <span class="value"><?= h($pagoHistorial->user->username ?? '-') ?></span>
            </td>
            <td>
                <span class="label">Fecha de pago (cuándo)</span>
                <span class="value"><?= h($pagoHistorial->created?->format('d-m-Y H:i')) ?></span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Período cubierto</span>
                <span class="value" style="font-size: 11px;"><?= h($pagoHistorial->fecha_desde) ?> al <?= h($pagoHistorial->fecha_hasta) ?></span>
            </td>
            <td>
                <span class="label">Comprobantes incluidos</span>
                <span class="value"><?= (int) $pagoHistorial->total_comprobantes ?></span>
            </td>
            <td>
                <span class="label">Monto total pagado</span>
                <span class="value">S/ <?= number_format((float) $pagoHistorial->monto_total, 2) ?></span>
            </td>
        </tr>
    </table>

    <?php if (!empty($pagoHistorial->observaciones)): ?>
        <table class="summary">
            <tr>
                <td>
                    <span class="label">Observaciones</span>
                    <span style="font-size: 11px;"><?= h($pagoHistorial->observaciones) ?></span>
                </td>
            </tr>
        </table>
    <?php endif; ?>

    <div class="section-title">Detalle del pago (dónde se pagó: por comprobante y distribución)</div>
    <table class="table">
        <thead>
            <tr>
                <th style="width: 15%;">Comprobante</th>
                <th style="width: 30%;">Cliente</th>
                <th style="width: 25%;">Descripción</th>
                <th style="width: 15%;">Monto pagado</th>
                <th style="width: 15%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php $totalDistribuciones = 0.0; ?>
            <?php foreach ($pagoHistorial->pagos_laboratorios_historial_distribuciones as $detalle): ?>
                <?php
                $totalDistribuciones += (float) $detalle->monto_pagado;
                $conceptos = json_decode((string) ($detalle->conceptos ?? ''), true) ?: [];
                $descripcion = $conceptos[0]['descripcion'] ?? ($detalle->invoice_distribucion->descripcion ?? '-');
                ?>
                <tr>
                    <td><?= h(($detalle->invoice->serie ?? 'RI') . '-' . ($detalle->invoice->correlativo ?? $detalle->invoice->id)) ?></td>
                    <td><?= h($detalle->invoice->cliente_nombre ?? '-') ?></td>
                    <td><?= h($descripcion) ?></td>
                    <td><strong>S/ <?= number_format((float) $detalle->monto_pagado, 2) ?></strong></td>
                    <td><?= h($detalle->invoice->estado ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right;">Total</td>
                <td>S/ <?= number_format($totalDistribuciones, 2) ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="signature">
        <div class="box">
            <div class="line">Entregado por</div>
        </div>
        <div class="box">
            <div class="line">Recibido por (Laboratorio)</div>
        </div>
    </div>

    <div class="footer">
        Generado el <?= h((new \DateTime())->format('d-m-Y H:i')) ?>
    </div>
</body>
</html>
