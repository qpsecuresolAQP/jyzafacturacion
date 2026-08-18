<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ganancia por Ventas de Productos</title>
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

        .text-right { text-align: right; }
        .muted { color: #777; }

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
        <div class="title">Ganancia por Ventas de Productos</div>
        <div class="subtitle">Del <?= h($fechaDesde) ?> al <?= h($fechaHasta) ?></div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <span class="label">Total Vendido</span>
                <span class="value">S/ <?= number_format($totalVendido, 2) ?></span>
            </td>
            <td>
                <span class="label">Costo Total</span>
                <span class="value">S/ <?= number_format($totalCosto, 2) ?></span>
            </td>
            <td>
                <span class="label">Ganancia Total</span>
                <span class="value">S/ <?= number_format($totalGanancia, 2) ?></span>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 28%;">Producto</th>
                <th style="width: 12%;">Código</th>
                <th style="width: 14%;">Cant. Vendida</th>
                <th style="width: 14%;">Total Vendido</th>
                <th style="width: 14%;">Costo Total</th>
                <th style="width: 12%;">Ganancia</th>
                <th style="width: 6%;">Margen %</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($filas)): ?>
                <?php foreach ($filas as $f): ?>
                    <tr>
                        <td><?= h($f['nombre']) ?></td>
                        <td><?= h($f['codigo']) ?></td>
                        <td class="text-right"><?= number_format($f['cantidad_vendida'], 2) ?></td>
                        <td class="text-right">S/ <?= number_format($f['total_vendido'], 2) ?></td>
                        <td class="text-right">S/ <?= number_format($f['costo_total'], 2) ?></td>
                        <td class="text-right">S/ <?= number_format($f['ganancia'], 2) ?></td>
                        <td class="text-right"><?= number_format($f['margen_porcentaje'], 1) ?>%</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="muted" style="text-align:center; padding: 14px;">No se vendieron productos en el rango seleccionado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Generado el <?= date('Y-m-d H:i') ?>
    </div>
</body>
</html>
