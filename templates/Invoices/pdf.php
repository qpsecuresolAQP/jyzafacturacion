<?php
$esFactura = $invoice->tipo_doc === '01';
$esInterno = $invoice->estado === 'RECIBO_INTERNO';

$tituloDocumento = $esInterno
    ? 'NOTA DE VENTA'
    : ($esFactura ? 'FACTURA ELECTRÓNICA' : 'BOLETA ELECTRÓNICA');

$numeroDocumento = ($invoice->serie && $invoice->correlativo)
    ? $invoice->serie . '-' . $invoice->correlativo
    : 'NV-' . $invoice->id;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?= h($tituloDocumento . ' ' . $numeroDocumento) ?></title>
    <style>
    /* ── CONFIGURACIÓN BASE NEGRO Y NEGRITA ABSOLUTA ── */
    * {
        color: #000000 !important;        /* Fuerza negro puro en absolutamente todo */
        border-color: #000000 !important;  /* Fuerza que ningún borde sea gris */
        font-weight: bold !important;     /* ← NUEVO: Fuerza todo el texto a NEGRITA */
    }

    body {
        font-family: 'Courier New', Courier, monospace;
        background-color: #fff;
        margin: 0 auto;
        padding: 5px; 
        font-size: 10px; 
        width: 54mm; 
        
        /* Desactiva el suavizado de fuentes (evita bordes con pixeles grises) */
        -webkit-font-smoothing: none !important;
        -moz-osx-font-smoothing: none !important;
        font-smoothing: none !important;
        text-shadow: none !important;
    }

    @page {
        margin: 0;
    }

    /* ── UTILIDADES ── */
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-left { text-align: left; }
    .mb-5 { margin-bottom: 5px; }
    .mt-5 { margin-top: 5px; }
    
    .dashed-divider {
        border-top: 1px dashed #000000;
        margin: 6px 0;
    }

    /* ── ENCABEZADO ── */
    .header {
        text-align: center;
        margin-bottom: 8px;
    }
    .header img {
        max-width: 60px;
        height: auto;
        margin-bottom: 5px;
    }
    .company-name {
        font-size: 11px;
        margin-bottom: 3px;
        text-transform: uppercase;
    }
    .subtitle {
        font-size: 9px;
        line-height: 1.3;
    }

    /* ── CAJAS DE INFORMACIÓN ── */
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
    .doc-title {
        font-size: 11px;
        margin: 3px 0;
    }
    .doc-number {
        font-size: 12px;
    }

    /* ── TABLA DE ITEMS ── */
    .items-container {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
        font-size: 9px;
    }
    .item-desc {
        padding: 4px 0 2px 0;
        text-align: left;
    }
    .item-details {
        padding: 0 0 4px 0;
        border-bottom: 1px dotted #000000; 
    }
    .item-details td {
        padding: 2px 0;
    }

    /* ── TABLA DE TOTALES ── */
    .totals-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
        margin-top: 5px;
    }
    .totals-table td {
        padding: 3px 0;
    }
    .total-final {
        font-size: 11px;
        border-top: 1px dashed #000000;
        border-bottom: 1px dashed #000000;
    }

    /* ── FOOTER ── */
    .footer {
        text-align: center;
        font-size: 9px;
        line-height: 1.4;
        margin-top: 10px;
        padding-top: 5px;
        border-top: 1px dashed #000000;
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
        <div class="subtitle"><strong>Email:</strong> <?= h($invoice->company->email ?? '-') ?></div>
    </div>

    <div class="info-box">
        <div>RUC <?= h($invoice->company->ruc) ?></div>
        <div class="doc-title"><?= h($tituloDocumento) ?></div>
        <div class="doc-number"><?= h($numeroDocumento) ?></div>
        <div class="dashed-divider" style="margin: 4px 0;"></div>
        <div style="font-size: 9px; line-height: 1.4;">
            <strong>Estado:</strong> <?= h($invoice->estado) ?><br>
            <strong>Emisión:</strong> <?= h($invoice->created?->format('d/m/Y H:i')) ?><br>
            <strong>Pago:</strong> <?= h($invoice->created?->format('d/m/Y H:i')) ?>
        </div>
    </div>

    <div class="info-box-left">
        <div><strong>Cliente:</strong> <?= h($invoice->cliente_nombre) ?></div>
        <div><strong>Documento:</strong> <?= h($invoice->cliente_numero) ?></div>
        <div><strong>Dirección:</strong> <?= h($invoice->cliente_direccion ?: '-') ?></div>
        <div><strong>Email:</strong> <?= h($invoice->cliente_email ?: '-') ?></div>
    </div>

    <div class="dashed-divider"></div>
    <div class="text-center mb-5" style="font-size: 11px;">DETALLE DE COMPRA</div>

    <table class="items-container">
        <?php foreach ($invoice->invoice_items as $item): ?>
            <tr>
                <td colspan="4" class="item-desc">
                    <?= h($item->descripcion) ?>
                </td>
            </tr>
            <tr class="item-details">
                <td style="width: 20%;" class="text-left">
                    Cant: <?= h($item->cantidad) ?>
                </td>
                <td style="width: 25%;" class="text-center">
                    VU: S/ <?= number_format((float) $item->valor_unitario, 2) ?>
                </td>
                <td style="width: 25%;" class="text-center">
                    PU: S/ <?= number_format((float) $item->precio_unitario, 2) ?>
                </td>
                <td style="width: 30%;" class="text-right">
                    Tot: S/ <?= number_format((float) $item->total, 2) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <table class="totals-table">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 25%;" class="text-right">Subtotal:</td>
            <td style="width: 25%;" class="text-right">S/ <?= number_format((float) $invoice->subtotal, 2) ?></td>
        </tr>
        <tr>
            <td></td>
            <td class="text-right">IGV (18%):</td>
            <td class="text-right">S/ <?= number_format((float) $invoice->igv, 2) ?></td>
        </tr>
        <tr class="total-final">
            <td></td>
            <td class="text-right" style="padding: 5px 0;">TOTAL:</td>
            <td class="text-right" style="padding: 5px 0;">S/ <?= number_format((float) $invoice->total, 2) ?></td>
        </tr>
    </table>

    <!-- MÉTODOS DE PAGO -->
    <?php
    $paymentMethods = [];
    if (!empty($invoice->caja_movimientos) && is_array($invoice->caja_movimientos)) {
        foreach ($invoice->caja_movimientos as $mov) {
            if (!empty($mov->metodo_pago) && (float)$mov->monto_recibido > 0) {
                $paymentMethods[] = [
                    'metodo' => $mov->metodo_pago,
                    'monto' => (float)$mov->monto_recibido
                ];
            }
        }
    }
    ?>
    <?php if (!empty($paymentMethods) && count($paymentMethods) > 0): ?>
        <div class="dashed-divider"></div>
        <div class="text-center mb-5" style="font-size: 11px;">MÉTODOS DE PAGO</div>
        <table class="totals-table" style="font-size: 9px;">
            <?php foreach ($paymentMethods as $pm): ?>
                <tr>
                    <td style="width: 60%;" class="text-left">
                        <?= h($pm['metodo']) ?>
                    </td>
                    <td style="width: 40%;" class="text-right">
                        S/ <?= number_format((float) $pm['monto'], 2) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <!-- DESCUENTOS APLICADOS -->
    <?php
    $descuentosAplicados = [];
    foreach ($invoice->invoice_items as $item) {
        if ((float)$item->descuento > 0) {
            $descuentosAplicados[] = [
                'descripcion' => $item->descripcion,
                'descuento' => (float)$item->descuento,
                'tipo' => $item->descuento_tipo ?? 'porcentaje'
            ];
        }
    }
    ?>
    <?php if (!empty($descuentosAplicados) && count($descuentosAplicados) > 0): ?>
        <div class="dashed-divider"></div>
        <div class="text-center mb-5" style="font-size: 11px;">DESCUENTOS APLICADOS</div>
        <table class="totals-table" style="font-size: 9px;">
            <?php foreach ($descuentosAplicados as $desc): ?>
                <tr>
                    <td style="width: 60%;" class="text-left">
                        <?= h($desc['descripcion']) ?>
                    </td>
                    <td style="width: 40%;" class="text-right">
                        <?php if ($desc['tipo'] === 'porcentaje'): ?>
                            −<?= number_format((float) $desc['descuento'], 2) ?>%
                        <?php else: ?>
                            −S/ <?= number_format((float) $desc['descuento'], 2) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <div class="footer">
        <?php if ($esInterno): ?>
            <div>Documento: Nota de Venta</div>
            <div>Uso: Constancia interna</div>
            <div>No enviada a SUNAT</div>
        <?php else: ?>
            <div>GRACIAS POR SU PREFERENCIA</div>
            <div style="margin-top: 3px;">Representación impresa de un comprobante electrónico.</div>
        <?php endif; ?>
    </div>

</body>
</html>