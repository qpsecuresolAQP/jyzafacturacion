<?php
$esFactura = $invoice->tipo_doc === '01';
$esInterno = $invoice->estado === 'RECIBO_INTERNO';
$tituloDocumento = $esInterno
    ? 'NOTA DE VENTA'
    : ($esFactura ? 'FACTURA ELECTRÓNICA' : 'BOLETA ELECTRÓNICA');
$numeroDocumento = ($invoice->serie && $invoice->correlativo)
    ? $invoice->serie . '-' . $invoice->correlativo
    : 'NV-' . $invoice->id;

$totalPagadoCuotas = 0;
$totalPendienteCuotas = 0;
foreach ($todasCuotas as $c) {
    if ($c->estado === 'PAGADA') {
        $totalPagadoCuotas += (float) $c->monto;
    } elseif ($c->estado === 'PENDIENTE') {
        $totalPendienteCuotas += (float) $c->monto;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante Cuota <?= h($numeroDocumento) ?></title>
    <style>
    * {
        color: #000000 !important;
        border-color: #000000 !important;
        font-weight: bold !important;
    }
    body {
        font-family: 'Courier New', Courier, monospace;
        background-color: #fff;
        margin: 0 auto;
        padding: 5px;
        font-size: 10px;
        width: 54mm;
        -webkit-font-smoothing: none !important;
        -moz-osx-font-smoothing: none !important;
        text-shadow: none !important;
    }
    @page { margin: 0; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-left { text-align: left; }
    .mb-5 { margin-bottom: 5px; }
    .mt-5 { margin-top: 5px; }
    .dashed-divider { border-top: 1px dashed #000000; margin: 6px 0; }
    .header { text-align: center; margin-bottom: 8px; }
    .header img { max-width: 60px; height: auto; margin-bottom: 5px; }
    .company-name { font-size: 11px; margin-bottom: 3px; text-transform: uppercase; }
    .subtitle { font-size: 9px; line-height: 1.3; }
    .info-box {
        border: 1px dashed #000000;
        padding: 4px;
        margin-bottom: 6px;
        text-align: center;
    }
    .info-box-left {
        border: 1px dashed #000000;
        padding: 4px;
        margin-bottom: 6px;
        text-align: left;
        line-height: 1.4;
        font-size: 9px;
    }
    .doc-title { font-size: 11px; margin: 3px 0; }
    .doc-number { font-size: 12px; }
    .totals-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
        margin-top: 5px;
    }
    .totals-table td { padding: 3px 0; }
    .total-final {
        font-size: 11px;
        border-top: 1px dashed #000000;
        border-bottom: 1px dashed #000000;
    }
    .footer {
        text-align: center;
        font-size: 9px;
        line-height: 1.4;
        margin-top: 10px;
        padding-top: 5px;
        border-top: 1px dashed #000000;
    }
    .cuota-badge {
        border: 1px dashed #000000;
        padding: 4px;
        margin: 6px 0;
        text-align: center;
        font-size: 10px;
    }
    </style>
</head>
<body>
    <div class="header">
        <?php if (!empty($logoUrl)): ?>
            <div><img src="<?= $logoUrl ?>" alt="Logo"></div>
        <?php endif; ?>
        <div class="company-name"><?= h($invoice->company->razon_social) ?></div>
        <div class="subtitle"><strong>RUC:</strong> <?= h($invoice->company->ruc) ?></div>
        <div class="subtitle"><strong>Dir:</strong> <?= h($invoice->company->direccion) ?></div>
    </div>

    <div class="info-box">
        <div>RUC <?= h($invoice->company->ruc) ?></div>
        <div class="doc-title"><?= h($tituloDocumento) ?></div>
        <div class="doc-number"><?= h($numeroDocumento) ?></div>
        <div class="dashed-divider" style="margin: 4px 0;"></div>
        <div style="font-size: 9px; line-height: 1.4;">
            <strong>Venta al crédito</strong><br>
            <strong>Cuota N°:</strong> <?= h($cuota->numero_cuota) ?><br>
            <strong>Pagado:</strong> <?= h($cuota->pagado_en?->format('d/m/Y H:i')) ?>
        </div>
    </div>

    <div class="info-box-left">
        <div><strong>Cliente:</strong> <?= h($invoice->cliente_nombre) ?></div>
        <div><strong>Documento:</strong> <?= h($invoice->cliente_numero) ?></div>
    </div>

    <div class="dashed-divider"></div>

    <div class="cuota-badge">
        RECIBO DE PAGO DE CUOTA
    </div>

    <table class="totals-table">
        <tr>
            <td style="width: 60%;">Cuota N° <?= h($cuota->numero_cuota) ?>:</td>
            <td style="width: 40%;" class="text-right">S/ <?= number_format((float) $cuota->monto, 2) ?></td>
        </tr>
        <tr>
            <td>Método de pago:</td>
            <td class="text-right"><?= h($cuota->metodo_pago) ?></td>
        </tr>
        <tr class="total-final">
            <td style="padding: 5px 0;">TOTAL FACTURA:</td>
            <td class="text-right" style="padding: 5px 0;">S/ <?= number_format((float) $invoice->total, 2) ?></td>
        </tr>
    </table>

    <div class="dashed-divider"></div>
    <div class="text-center mb-5" style="font-size: 10px;">ESTADO DE CUOTAS</div>

    <table class="totals-table" style="font-size: 9px;">
        <?php foreach ($todasCuotas as $c): ?>
            <tr>
                <td style="width: 15%;">N°<?= h($c->numero_cuota) ?></td>
                <td style="width: 45%;">
                    <?= h($c->fecha_vencimiento instanceof \DateTimeInterface ? $c->fecha_vencimiento->format('d/m/Y') : $c->fecha_vencimiento) ?>
                </td>
                <td style="width: 20%;" class="text-right">S/ <?= number_format((float) $c->monto, 2) ?></td>
                <td style="width: 20%;" class="text-right"><?= h($c->estado) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <table class="totals-table" style="margin-top: 6px;">
        <tr>
            <td>Pagado:</td>
            <td class="text-right">S/ <?= number_format($totalPagadoCuotas, 2) ?></td>
        </tr>
        <tr>
            <td>Pendiente:</td>
            <td class="text-right">S/ <?= number_format($totalPendienteCuotas, 2) ?></td>
        </tr>
    </table>

    <div class="footer">
        <div>GRACIAS POR SU PREFERENCIA</div>
        <div style="margin-top: 3px;">Recibo de pago de cuota — venta al crédito.</div>
    </div>
</body>
</html>