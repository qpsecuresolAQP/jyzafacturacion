<?php
$esFactura = $invoice->tipo_doc === '01';
$esInterno = $invoice->estado === 'RECIBO_INTERNO';

/* ── MONTOS ── */
$montoGravado = 0.0;
$montoExonerado = 0.0;
$totalDescuentos = 0.0;

foreach ($invoice->invoice_items as $item) {
    $tipoAfectacion = (string) ($item->tip_afe_igv ?? '10');
    $base = (float) $item->base_igv;

    if ($tipoAfectacion === '20') {
        $montoExonerado += $base;
    } else {
        $montoGravado += $base;
    }

    // Descuento por ítem (porcentaje o monto fijo)
    $desc = (float) ($item->descuento ?? 0);
    if ($desc > 0) {
        if (($item->descuento_tipo ?? 'porcentaje') === 'porcentaje') {
            $totalDescuentos += ((float) $item->cantidad * (float) $item->precio_unitario) * ($desc / 100);
        } else {
            $totalDescuentos += $desc;
        }
    }
}

$montoGravado = round($montoGravado, 2);
$montoExonerado = round($montoExonerado, 2);
$totalDescuentos = round($totalDescuentos, 2);

/* ── TÍTULO Y NÚMERO ── */
$tituloDocumento = $esInterno
    ? 'NOTA DE VENTA'
    : ($esFactura ? 'FACTURA DE VENTA ELECTRÓNICA' : 'BOLETA DE VENTA ELECTRÓNICA');

$numeroDocumento = ($invoice->serie && $invoice->correlativo)
    ? $invoice->serie . '-' . $invoice->correlativo
    : 'NV-' . $invoice->id;

$etiquetaDoc = $esFactura ? 'R.U.C.' : 'D.N.I.';

/* ── FORMA DE PAGO (desde caja_movimientos) ── */
// Monto pagado por método (lo recibido menos el vuelto), sumando si un método se repite.
$paymentMethods = [];
if (!empty($invoice->caja_movimientos)) {
    foreach ($invoice->caja_movimientos as $mov) {
        $montoPagado = (float) $mov->monto_recibido - (float) ($mov->vuelto ?? 0);
        if (!empty($mov->metodo_pago) && $montoPagado > 0) {
            $paymentMethods[$mov->metodo_pago] = ($paymentMethods[$mov->metodo_pago] ?? 0) + $montoPagado;
        }
    }
}

/* ── MONTO EN LETRAS ── */
if (!function_exists('montoEnLetras')) {
    function montoEnLetras(float $monto): string
    {
        $u = ['', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE', 'DIEZ',
            'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISEIS', 'DIECISIETE', 'DIECIOCHO',
            'DIECINUEVE', 'VEINTE', 'VEINTIUNO', 'VEINTIDOS', 'VEINTITRES', 'VEINTICUATRO',
            'VEINTICINCO', 'VEINTISEIS', 'VEINTISIETE', 'VEINTIOCHO', 'VEINTINUEVE'];
        $d = [3 => 'TREINTA', 4 => 'CUARENTA', 5 => 'CINCUENTA', 6 => 'SESENTA',
            7 => 'SETENTA', 8 => 'OCHENTA', 9 => 'NOVENTA'];
        $c = [1 => 'CIENTO', 2 => 'DOSCIENTOS', 3 => 'TRESCIENTOS', 4 => 'CUATROCIENTOS',
            5 => 'QUINIENTOS', 6 => 'SEISCIENTOS', 7 => 'SETECIENTOS', 8 => 'OCHOCIENTOS',
            9 => 'NOVECIENTOS'];

        $menor1000 = function (int $n) use ($u, $d, $c): string {
            if ($n === 0) return '';
            if ($n === 100) return 'CIEN';
            $txt = '';
            $cent = intdiv($n, 100);
            $resto = $n % 100;
            if ($cent > 0) $txt .= $c[$cent] . ' ';
            if ($resto < 30) {
                $txt .= $u[$resto];
            } else {
                $dec = intdiv($resto, 10);
                $uni = $resto % 10;
                $txt .= $d[$dec] . ($uni ? ' Y ' . $u[$uni] : '');
            }
            return trim($txt);
        };

        $entero = (int) floor($monto);
        $centimos = (int) round(($monto - $entero) * 100);

        if ($entero === 0) {
            $letras = 'CERO';
        } else {
            $letras = '';
            $millones = intdiv($entero, 1000000);
            $miles = intdiv($entero % 1000000, 1000);
            $cientos = $entero % 1000;

            if ($millones > 0) {
                $letras .= ($millones === 1 ? 'UN MILLON' : $menor1000($millones) . ' MILLONES') . ' ';
            }
            if ($miles > 0) {
                $letras .= ($miles === 1 ? 'MIL' : $menor1000($miles) . ' MIL') . ' ';
            }
            if ($cientos > 0) {
                $letras .= $menor1000($cientos);
            }
            $letras = trim($letras);
        }

        return $letras . ' CON ' . str_pad((string) $centimos, 2, '0', STR_PAD_LEFT) . '/100 SOLES';
    }
}
$montoLetras = montoEnLetras((float) $invoice->total);

/* ── OPCIONALES ── */
// $qrUrl            → imagen del QR (URL o data:image/png;base64,...)
// $invoice->observaciones → texto de "Información Adicional"
$infoAdicional = trim((string) ($invoice->observaciones ?? ''));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?= h($tituloDocumento . ' ' . $numeroDocumento) ?></title>
    <style>
    /* ── BASE: NEGRO Y NEGRITA PARA TÉRMICA ── */
    * {
        color: #000000 !important;
        border-color: #000000 !important;
        font-weight: bold !important;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
        background-color: #fff;
        margin: 0 auto;
        padding: 4px;
        font-size: 8px;
        width: 54mm;

        -webkit-font-smoothing: none !important;
        -moz-osx-font-smoothing: none !important;
        font-smoothing: none !important;
        text-shadow: none !important;
    }

    @page { margin: 0; }

    /* ── UTILIDADES ── */
    .text-center { text-align: center; }
    .text-right  { text-align: right; }
    .text-left   { text-align: left; }

    /* ── ENCABEZADO: logo a la izquierda, datos de la empresa a la derecha ── */
    .header {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 3px;
    }
    .header td { vertical-align: middle; padding: 0; }
    .logo-cell { width: 30%; padding-right: 3px !important; }
    .logo-cell img { width: 100%; height: auto; display: block; }
    .company-name {
        font-size: 8.5px;
        line-height: 1.15;
        margin-bottom: 1px;
        text-transform: uppercase;
    }
    .subtitle {
        font-size: 6.5px;
        line-height: 1.25;
        text-transform: uppercase;
    }

    /* ── CAJA DEL DOCUMENTO (bordes redondeados) ── */
    .doc-box {
        border: 1px solid #000000;
        border-radius: 8px;
        padding: 2px 4px;
        margin: 3px 0;
        text-align: center;
        font-size: 8px;
        line-height: 1.3;
    }

    /* ── DATOS DEL CLIENTE ── */
    .client-data {
        font-size: 7.5px;
        line-height: 1.35;
        margin-bottom: 3px;
    }

    /* ── TABLA DE ITEMS ── */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 7.5px;
        border-top: 1px solid #000000;
        border-bottom: 1px solid #000000;
        margin-bottom: 3px;
    }
    .items-table th {
        padding: 2px 0;
        font-size: 6.5px;
        border-bottom: 1px solid #000000;
        vertical-align: bottom;
    }
    .items-table td {
        padding: 1px 0;
        vertical-align: top;
    }

    /* ── TOTALES + QR ── */
    .totals-wrap {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 2px;
    }
    .totals-wrap td { vertical-align: middle; }
    .qr-cell { width: 26%; padding-right: 3px; }
    .qr-cell img { width: 100%; height: auto; display: block; }

    .totals-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 7.5px;
    }
    .totals-table td { padding: 1px 0; }

    /* ── MONTO EN LETRAS ── */
    .son {
        border-top: 1px solid #000000;
        border-bottom: 1px solid #000000;
        padding: 2px 0;
        text-align: center;
        font-size: 7px;
        margin-bottom: 2px;
    }

    /* ── FORMA DE PAGO ── */
    .forma-pago {
        font-size: 7.5px;
        line-height: 1.3;
        margin-bottom: 2px;
    }

    /* ── INFO ADICIONAL ── */
    .info-adicional {
        text-align: center;
        font-size: 7px;
        line-height: 1.3;
        margin-bottom: 2px;
    }

    /* ── FOOTER ── */
    .footer {
        text-align: center;
        font-size: 6px;
        line-height: 1.25;
        margin-top: 2px;
    }
    .footer .gracias { font-size: 7px; margin-bottom: 1px; }
    </style>
</head>

<body>

    <!-- ENCABEZADO -->
    <table class="header">
        <tr>
            <?php if (!empty($logoUrl)): ?>
                <td class="logo-cell"><img src="<?= h($logoUrl) ?>" alt="Logo"></td>
            <?php endif; ?>
            <td>
                <div class="company-name"><?= h($invoice->company->razon_social) ?></div>
                <div class="subtitle">R.U.C. : <?= h($invoice->company->ruc) ?></div>
                <div class="subtitle"><?= h($invoice->company->direccion) ?></div>
                <?php if (!empty($invoice->company->telefono)): ?>
                    <div class="subtitle">Telf. : <?= h($invoice->company->telefono) ?></div>
                <?php endif; ?>
                <?php if (!empty($invoice->company->email)): ?>
                    <div class="subtitle" style="text-transform: none;"><?= h($invoice->company->email) ?></div>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- TÍTULO + NÚMERO -->
    <div class="doc-box">
        <div><?= h($tituloDocumento) ?></div>
        <div>Nº <?= h($numeroDocumento) ?></div>
    </div>

    <!-- DATOS DEL CLIENTE -->
    <div class="client-data">
        <div>Cliente : <?= h($invoice->cliente_nombre) ?></div>
        <div><?= $etiquetaDoc ?> : <?= h($invoice->cliente_numero) ?></div>
        <div>Fecha : <?= h($invoice->created?->format('d/m/Y H:i')) ?> &nbsp; Moneda : SOLES</div>
    </div>

    <!-- ITEMS -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="text-left" style="width: 14%;">CANT.</th>
                <th class="text-center" style="width: 42%;">PRODUCTO</th>
                <th class="text-right" style="width: 20%;">P.U.</th>
                <th class="text-right" style="width: 24%;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($invoice->invoice_items as $item): ?>
                <tr>
                    <td class="text-left"><?= number_format((float) $item->cantidad, 2) ?></td>
                    <td class="text-left">
                        <?= h($item->descripcion) ?>
                        <?php if ((string) ($item->tip_afe_igv ?? '10') === '20'): ?>
                            <span style="font-size: 6px;">(EXON.)</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-right"><?= number_format((float) $item->precio_unitario, 2) ?></td>
                    <td class="text-right"><?= number_format((float) $item->total, 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- TOTALES + QR -->
    <table class="totals-wrap">
        <tr>
            <?php if (!empty($qrUrl)): ?>
                <td class="qr-cell"><img src="<?= h($qrUrl) ?>" alt="QR"></td>
            <?php endif; ?>
            <td>
                <table class="totals-table">
                    <?php if ($totalDescuentos > 0): ?>
                        <tr>
                            <td class="text-left">Total Desc. Item</td>
                            <td class="text-right">: S/ <?= number_format($totalDescuentos, 2) ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if ($montoGravado > 0): ?>
                        <tr>
                            <td class="text-left">Venta Gravada</td>
                            <td class="text-right">: S/ <?= number_format($montoGravado, 2) ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if ($montoExonerado > 0): ?>
                        <tr>
                            <td class="text-left">Venta Exonerada</td>
                            <td class="text-right">: S/ <?= number_format($montoExonerado, 2) ?></td>
                        </tr>
                    <?php endif; ?>

                    <tr>
                        <td class="text-left">Total I.G.V.</td>
                        <td class="text-right">: S/ <?= number_format((float) $invoice->igv, 2) ?></td>
                    </tr>

                    <tr>
                        <td class="text-left">Total Precio de Venta</td>
                        <td class="text-right">: S/ <?= number_format((float) $invoice->total, 2) ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- MONTO EN LETRAS -->
    <div class="son">SON: <?= h($montoLetras) ?></div>

    <!-- FORMA DE PAGO -->
    <div class="forma-pago">
        <div>Forma de Pago : Contado</div>
        <?php if (!empty($paymentMethods)): ?>
            <table class="totals-table">
                <?php foreach ($paymentMethods as $metodo => $monto): ?>
                    <tr>
                        <td class="text-left"><?= h($metodo) ?></td>
                        <td class="text-right">: S/ <?= number_format($monto, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>

    <!-- INFORMACIÓN ADICIONAL -->
    <?php if ($infoAdicional !== ''): ?>
        <div class="info-adicional">
            <div>Información Adicional</div>
            <div><?= nl2br(h($infoAdicional)) ?></div>
        </div>
    <?php endif; ?>

    <!-- FOOTER -->
    <div class="footer">
        <?php if ($esInterno): ?>
            <div class="gracias">GRACIAS POR SU PREFERENCIA</div>
            <div>Documento: Nota de Venta</div>
            <div>Uso: Constancia interna</div>
            <div>No enviada a SUNAT</div>
        <?php else: ?>
            <div class="gracias">"GRACIAS POR SU PREFERENCIA"</div>
            <div>Representación impresa de la <?= h($tituloDocumento) ?>.</div>
            <div>Puede consultar este documento en www.sunat.gob.pe con su clave SOL.</div>
        <?php endif; ?>
    </div>

</body>

</html>