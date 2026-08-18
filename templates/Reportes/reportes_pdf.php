<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Comprobantes</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 10px;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            margin: 10px 0;
        }
        .info-header {
            margin-bottom: 15px;
            font-size: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }
        .info-header p {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .tipo-header {
            background-color: #e8e8e8;
            font-weight: bold;
        }
        .metodos-pago {
            background-color: #fff9f0;
            font-size: 9px;
            padding: 3px;
            margin-top: 2px;
            border-left: 2px solid #ff9800;
        }
        .total-row {
            background-color: #f2f2f2;
            font-weight: bold;
            border-top: 2px solid #000;
        }
        .resumen {
            margin-top: 20px;
            font-size: 10px;
        }
        .resumen-item {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>REPORTE DE COMPROBANTES</h1>
    
    <!-- DEBUG: Mostrar qué hay en pagosPorComprobante -->
    <!-- <?php echo 'DEBUG: ' . json_encode($pagosPorComprobante); ?> -->
    
    <div class="info-header">
        <p><strong>Período:</strong> 
            <?php 
                if ($fechaInicio && $fechaFin): 
                    echo $fechaInicio->format('d/m/Y') . ' - ' . $fechaFin->format('d/m/Y');
                else:
                    echo 'Todos los registros';
                endif;
            ?>
        </p>
        <p><strong>Filtro:</strong> 
            <?php 
                if ($tipo === 'BOLETA'): 
                    echo 'Boletas (Tipo 03)';
                elseif ($tipo === 'FACTURA'):
                    echo 'Facturas (Tipo 01)';
                elseif ($tipo === 'RI'):
                    echo 'Recibos Internos';
                else:
                    echo 'Todos los Comprobantes';
                endif;
            ?>
        </p>
        <p><strong>Total Comprobantes:</strong> <?= count($invoices) ?></p>
        <p><strong>Total Ventas:</strong> S/ <?= number_format($totalVentas, 2) ?></p>
        <p><strong>Generado:</strong> <?= date('d/m/Y H:i:s') ?></p>
    </div>

    <?php if (!empty($resumenPagos)): ?>
    <div class="info-header" style="margin-top: 10px;">
        <p><strong>Resumen por Método de Pago:</strong></p>
        <table>
            <thead>
                <tr>
                    <th>Método</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resumenPagos as $metodo => $datos): ?>
                    <tr>
                        <td><?= h($metodo) ?></td>
                        <td><?= (int)$datos['cantidad'] ?></td>
                        <td>S/ <?= number_format((float)$datos['total'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Serie</th>
                <th>Número</th>
                <th>Tipo</th>
                <th>Cliente</th>
                <th>Total (S/)</th>
                <th>Estado</th>
                <th>Fecha/Hora</th>
                <th>Métodos de Pago</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($invoices as $inv): ?>
                <?php 
                    $tipo_label = $inv->tipo_doc === '01' ? 'Factura' : ($inv->tipo_doc === '03' ? 'Boleta' : 'Recibo');
                    $total = number_format((float)$inv->total, 2);
                    $serie = $inv->serie ?: 'RI';
                    $numero = $inv->correlativo ?: $inv->id;
                    $pagos = $pagosPorComprobante[$inv->id] ?? [];
                ?>
                <tr>
                    <td><?= $inv->id ?></td>
                    <td><?= h($serie) ?></td>
                    <td><?= h($numero) ?></td>
                    <td><?= h($tipo_label) ?></td>
                    <td><?= h($inv->cliente_nombre) ?></td>
                    <td style="text-align: right;">S/ <?= $total ?></td>
                    <td><?= h($inv->estado) ?></td>
                    <td><?= $inv->created->format('d/m/Y H:i') ?></td>
                    <td>
                        <?php if (!empty($pagos)): ?>
                            <div class="metodos-pago">
                                <?php foreach ($pagos as $pago): ?>
                                    <div><?= h($pago['metodo']) ?>: S/ <?= number_format($pago['monto'], 2) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <span style="color: #999;">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align: right;">TOTAL:</td>
                <td style="text-align: right;">S/ <?= number_format($totalVentas, 2) ?></td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>

    <div class="resumen">
        <p class="resumen-item"><strong>Total de comprobantes:</strong> <?= count($invoices) ?></p>
        <p class="resumen-item"><strong>Promedio por comprobante:</strong> S/ <?= number_format(count($invoices) > 0 ? $totalVentas / count($invoices) : 0, 2) ?></p>
        <p class="resumen-item">Documento generado automáticamente por el sistema</p>
    </div>
</body>
</html>
