<?php
$numeroDocumento = 'CAJA-' . $caja->codigo . '-' . ($caja->fecha?->format('Ymd') ?? date('Ymd'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= h('Detalle de Caja - ' . $caja->nombre) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 15px;
            font-size: 11px;
            color: #333;
        }

        /* ── HEADER ── */
        .header {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin: 0 0 5px 0;
        }

        .header-info {
            font-size: 10px;
            color: #666;
            margin: 2px 0;
            line-height: 1.4;
        }

        /* ── INFO BOX ── */
        .info-box {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 15px;
            font-size: 10px;
        }

        .info-box-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 6px;
            font-size: 11px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            border-bottom: 1px solid #eee;
            padding-bottom: 3px;
        }

        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-label {
            font-weight: bold;
            color: #555;
        }

        .info-value {
            text-align: right;
            color: #333;
        }

        /* ── SECTION ── */
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            background-color: #4a90e2;
            color: #fff;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        /* ── TABLA ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table.summary-table th {
            background-color: #e8f1f8;
            color: #333;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 10px;
        }

        table.summary-table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            background-color: #fff;
            font-size: 10px;
        }

        table.summary-table tbody tr:nth-child(even) td {
            background-color: #f9f9f9;
        }

        table.summary-table tfoot td {
            background-color: #e8f1f8;
            font-weight: bold;
            border: 1px solid #ddd;
            text-align: right;
            padding: 6px 8px;
            font-size: 10px;
        }

        /* ── MONEY DISPLAY ── */
        .money-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 8px;
            margin-bottom: 15px;
        }

        .money-box {
            background-color: #f0f7ff;
            border: 1px solid #4a90e2;
            border-radius: 4px;
            padding: 8px;
            text-align: center;
            font-size: 9px;
        }

        .money-box-title {
            font-size: 9px;
            color: #555;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .money-box-amount {
            font-size: 13px;
            font-weight: bold;
            color: #4a90e2;
        }

        /* ── FOOTER ── */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #666;
        }

        /* ── UTILS ── */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-success { color: #28a745; }
        .text-warning { color: #ffc107; }
        .text-danger { color: #dc3545; }
        .fw-bold { font-weight: bold; }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <div class="header-title">DETALLE DE CAJA</div>
        <div class="header-info"><strong>Caja:</strong> <?= h($caja->nombre) ?> (<?= h($caja->codigo) ?>)</div>
        <div class="header-info"><strong>Usuario:</strong> <?= h($caja->user->name ?? $caja->user->username ?? 'N/A') ?></div>
        <div class="header-info"><strong>Fecha:</strong> <?= h($caja->fecha?->format('d/m/Y H:i') ?? date('d/m/Y H:i')) ?></div>
        <div class="header-info"><strong>Estado:</strong> <?= h($caja->estado) ?></div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════ -->
    <!-- MONEDAS AL INICIO -->
    <!-- ═════════════════════════════════════════════════════════════════ -->
    <div class="section">
        <div class="section-title"> MONEDAS AL INICIO DE CAJA</div>
        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Monto Inicial:</span>
                <span class="info-value fw-bold" style="color: #28a745;">S/ <?= number_format((float)$caja->monto_inicial, 2) ?></span>
            </div>
        </div>

        <?php if (!empty($denominacionesApertura->toList())): ?>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Denominación</th>
                        <th style="width: 20%; text-align: center;">Cantidad</th>
                        <th style="width: 20%; text-align: right;">Valor Unit.</th>
                        <th style="width: 20%; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($denominacionesApertura as $den): ?>
                        <tr>
                            <td>S/ <?= number_format($den->valor, 2) ?></td>
                            <td style="text-align: center;"><?= $den->cantidad ?></td>
                            <td style="text-align: right;">S/ <?= number_format($den->valor, 2) ?></td>
                            <td style="text-align: right;">S/ <?= number_format($den->subtotal, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">TOTAL APERTURA</td>
                        <td style="text-align: right;">S/ <?= number_format($totalApertura, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: #999; font-size: 10px;">No hay registro de denominaciones de apertura</p>
        <?php endif; ?>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════ -->
    <!-- MONEDAS AL CIERRE DE CAJA -->
    <!-- ═════════════════════════════════════════════════════════════════ -->
    <?php if (!empty($denominacionesCierre->toList())): ?>
        <div class="section">
            <div class="section-title"> MONEDAS AL CIERRE DE CAJA</div>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Denominación</th>
                        <th style="width: 20%; text-align: center;">Cantidad</th>
                        <th style="width: 20%; text-align: right;">Valor Unit.</th>
                        <th style="width: 20%; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($denominacionesCierre as $den): ?>
                        <tr>
                            <td>S/ <?= number_format($den->valor, 2) ?></td>
                            <td style="text-align: center;"><?= $den->cantidad ?></td>
                            <td style="text-align: right;">S/ <?= number_format($den->valor, 2) ?></td>
                            <td style="text-align: right;">S/ <?= number_format($den->subtotal, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">TOTAL CIERRE</td>
                        <td style="text-align: right;">S/ <?= number_format($totalCierre, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>

    <!-- ═════════════════════════════════════════════════════════════════ -->
    <!-- MOVIMIENTOS POR MÉTODO DE PAGO -->
    <!-- ═════════════════════════════════════════════════════════════════ -->
    <div class="section">
        <div class="section-title"> MOVIMIENTOS POR MÉTODO DE PAGO</div>
        <div class="money-grid">
            <div class="money-box">
                <div class="money-box-title">Efectivo</div>
                <div class="money-box-amount">S/ <?= number_format($totalEfectivo, 2) ?></div>
            </div>
            <div class="money-box">
                <div class="money-box-title">Yape</div>
                <div class="money-box-amount">S/ <?= number_format($totalYape, 2) ?></div>
            </div>
            <div class="money-box">
                <div class="money-box-title">Tarjeta</div>
                <div class="money-box-amount">S/ <?= number_format($totalTarjeta, 2) ?></div>
            </div>
            <div class="money-box">
                <div class="money-box-title">Transferencia</div>
                <div class="money-box-amount">S/ <?= number_format($totalTransferencia, 2) ?></div>
            </div>
            <div class="money-box">
                <div class="money-box-title">Plin</div>
                <div class="money-box-amount">S/ <?= number_format($totalPlin, 2) ?></div>
            </div>
            <div class="money-box">
                <div class="money-box-title">Otros</div>
                <div class="money-box-amount">S/ <?= number_format($totalOtros, 2) ?></div>
            </div>
        </div>

        <table class="summary-table">
            <tfoot>
                <tr>
                    <td style="width: 70%;">TOTAL DE INGRESOS POR MOVIMIENTOS</td>
                    <td>S/ <?= number_format($totalGeneral, 2) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════ -->
    <!-- INGRESOS EXTERNOS -->
    <!-- ═════════════════════════════════════════════════════════════════ -->
    <?php if (!empty($ingresosExternos->toList())): ?>
        <div class="section">
            <div class="section-title"> INGRESOS EXTERNOS</div>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Descripción</th>
                        <th style="width: 25%;">Fecha</th>
                        <th style="width: 25%; text-align: right;">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ingresosExternos as $ingreso): ?>
                        <tr>
                            <td><?= h($ingreso->descripcion ?? 'Sin descripción') ?></td>
                            <td><?= h($ingreso->created?->format('d/m/Y H:i') ?? 'N/A') ?></td>
                            <td class="text-right">S/ <?= number_format((float)$ingreso->monto, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">TOTAL INGRESOS EXTERNOS</td>
                        <td class="text-right">S/ <?= number_format($totalIngresosExternos, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>

    <!-- ═════════════════════════════════════════════════════════════════ -->
    <!-- EGRESOS EXTERNOS -->
    <!-- ═════════════════════════════════════════════════════════════════ -->
    <?php if (!empty($egresosExternos->toList())): ?>
        <div class="section">
            <div class="section-title"> EGRESOS EXTERNOS</div>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Descripción</th>
                        <th style="width: 25%;">Fecha</th>
                        <th style="width: 25%; text-align: right;">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($egresosExternos as $egreso): ?>
                        <tr>
                            <td><?= h($egreso->descripcion ?? 'Sin descripción') ?></td>
                            <td><?= h($egreso->created?->format('d/m/Y H:i') ?? 'N/A') ?></td>
                            <td class="text-right">S/ <?= number_format((float)$egreso->monto, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">TOTAL EGRESOS EXTERNOS</td>
                        <td class="text-right">S/ <?= number_format($totalEgresosExternos, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>

    <!-- ═════════════════════════════════════════════════════════════════ -->
    <!-- REEMBOLSOS POR ANULACIÓN -->
    <!-- ═════════════════════════════════════════════════════════════════ -->
    <?php if (!empty($reembolsosAnulacion->toList())): ?>
        <div class="section">
            <div class="section-title"> REEMBOLSOS POR ANULACIÓN</div>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Descripción</th>
                        <th style="width: 15%;">Método</th>
                        <th style="width: 25%;">Fecha</th>
                        <th style="width: 20%; text-align: right;">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reembolsosAnulacion as $reembolso): ?>
                        <tr>
                            <td><?= h($reembolso->descripcion ?? 'Sin descripción') ?></td>
                            <td><?= h(strtoupper($reembolso->metodo_pago ?? 'EFECTIVO')) ?></td>
                            <td><?= h($reembolso->created?->format('d/m/Y H:i') ?? 'N/A') ?></td>
                            <td class="text-right">S/ <?= number_format((float)$reembolso->monto, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">TOTAL REEMBOLSOS POR ANULACIÓN</td>
                        <td class="text-right">S/ <?= number_format($totalReembolsosAnulacion, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>

    <!-- ═════════════════════════════════════════════════════════════════ -->
    <!-- MOVIMIENTOS DE CAJA -->
    <!-- ═════════════════════════════════════════════════════════════════ -->
    <?php if (!empty($caja->caja_movimientos)): ?>
        <div class="section">
            <div class="section-title"> MOVIMIENTOS DE CAJA</div>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th style="width: 35%;">Descripción</th>
                        <th style="width: 20%;">Método Pago</th>
                        <th style="width: 20%;">Fecha</th>
                        <th style="width: 25%; text-align: right;">Monto Recibido</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($caja->caja_movimientos as $mov): ?>
                        <tr>
                            <td><?= h($mov->descripcion ?? ($mov->invoice->numero_documento ?? 'Movimiento')) ?></td>
                            <td><?= h($mov->metodo_pago) ?></td>
                            <td><?= h($mov->created?->format('d/m/Y H:i') ?? 'N/A') ?></td>
                            <td class="text-right">S/ <?= number_format((float)$mov->monto_recibido, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- ═════════════════════════════════════════════════════════════════ -->
    <!-- RESUMEN FINAL -->
    <!-- ═════════════════════════════════════════════════════════════════ -->
    <div class="section">
        <div class="section-title"> RESUMEN FINAL</div>
        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Monto Inicial:</span>
                <span class="info-value fw-bold">S/ <?= number_format((float)$caja->monto_inicial, 2) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Apertura (Denominaciones):</span>
                <span class="info-value fw-bold">S/ <?= number_format($totalApertura, 2) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Cierre (Denominaciones):</span>
                <span class="info-value fw-bold">S/ <?= number_format($totalCierre, 2) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Movimientos (Efectivo):</span>
                <span class="info-value fw-bold">S/ <?= number_format($totalEfectivo, 2) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">+ Ingresos Externos:</span>
                <span class="info-value fw-bold">S/ <?= number_format($totalIngresosExternos, 2) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">- Egresos Externos (gastos):</span>
                <span class="info-value fw-bold">S/ <?= number_format($totalEgresosExternos, 2) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">- Reembolsos por Anulación:</span>
                <span class="info-value fw-bold">S/ <?= number_format($totalReembolsosAnulacion, 2) ?></span>
            </div>
            <div class="info-row" style="border-bottom: 2px solid #4a90e2; margin-top: 8px; padding-top: 8px;">
                <span class="info-label" style="font-size: 12px;">EFECTIVO ESPERADO EN CAJA:</span>
                <span class="info-value fw-bold text-success" style="font-size: 13px; color: #28a745;">S/ <?= number_format($efectivoEsperadoEnCaja, 2) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label" style="font-size: 12px;">TOTAL ESPERADO (todos los métodos):</span>
                <span class="info-value fw-bold text-success" style="font-size: 13px; color: #28a745;">S/ <?= number_format($totalEsperadoGeneral, 2) ?></span>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p>Documento generado el <?= date('d/m/Y H:i:s') ?></p>
    </div>

</body>
</html>
