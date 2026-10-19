<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario</title>
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
            font-size: 9px;
        }

        .table th {
            background: #f3f5f7;
            color: #222;
            border: 1px solid #cfd6dd;
            padding: 5px;
            text-align: left;
        }

        .table td {
            border: 1px solid #e0e0e0;
            padding: 5px;
            vertical-align: top;
        }

        .table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .muted {
            color: #777;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            color: #fff;
        }

        .badge-agotado { background: #dc3545; }
        .badge-bajo { background: #ffc107; color: #222; }
        .badge-normal { background: #198754; }

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
        <div class="title">Reporte de Inventario</div>
        <div class="subtitle">Generado el <?= date('d/m/Y H:i') ?> · el estado se mide con el stock vigente (sin lotes vencidos)</div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <span class="label">Total Productos</span>
                <span class="value"><?= (int) $totalProductos ?></span>
            </td>
            <td>
                <span class="label">Inventario al costo</span>
                <span class="value">S/ <?= number_format($valorInventario, 2) ?></span>
            </td>
            <td>
                <span class="label">A precio de venta</span>
                <span class="value">S/ <?= number_format($valorVenta, 2) ?></span>
            </td>
            <td>
                <span class="label">Stock Bajo</span>
                <span class="value"><?= (int) $totalBajos ?></span>
            </td>
            <td>
                <span class="label">Agotados</span>
                <span class="value"><?= (int) $totalAgotados ?></span>
            </td>
            <td>
                <span class="label">Reponer hasta el mínimo</span>
                <span class="value">S/ <?= number_format($presupuestoReposicion, 2) ?></span>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 10%;">Categoría</th>
                <th style="width: 17%;">Producto</th>
                <th style="width: 7%;">Código</th>
                <th style="width: 10%;">Proveedor</th>
                <th style="width: 7%;">P. Compra</th>
                <th style="width: 7%;">P. Venta</th>
                <th style="width: 6%;">Margen %</th>
                <th style="width: 6%;">Stock total</th>
                <th style="width: 6%;">Vigente</th>
                <th style="width: 6%;">Vencido</th>
                <th style="width: 5%;">Mínimo</th>
                <th style="width: 8%;">Próx. venc.</th>
                <th style="width: 8%;">Valor costo</th>
                <th style="width: 6%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $p): ?>
                    <tr>
                        <td><?= h($p->categoria_nombre ?: '-') ?></td>
                        <td><?= h($p->nombre) ?></td>
                        <td><?= h($p->codigo ?: '-') ?></td>
                        <td><?= h($p->proveedor_nombre ?: '-') ?></td>
                        <td>S/ <?= number_format((float) $p->precio_compra, 2) ?></td>
                        <td>S/ <?= number_format((float) $p->precio_venta, 2) ?></td>
                        <td><?= number_format((float) $p->margen_porcentaje, 1) ?>%</td>
                        <td><?= number_format((float) $p->stock, 2) ?></td>
                        <td><?= number_format($p->stock_vigente, 2) ?></td>
                        <td<?= $p->stock_vencido > 0 ? ' style="color:#dc3545;font-weight:bold;"' : '' ?>><?= number_format($p->stock_vencido, 2) ?></td>
                        <td><?= number_format((float) $p->stock_minimo, 2) ?></td>
                        <td<?= $p->prox_vencimiento && $p->prox_vencimiento->isPast() ? ' style="color:#dc3545;font-weight:bold;"' : '' ?>><?= $p->prox_vencimiento ? $p->prox_vencimiento->format('d/m/Y') : '-' ?></td>
                        <td>S/ <?= number_format((float) $p->stock * (float) $p->precio_compra, 2) ?></td>
                        <td>
                            <span class="badge badge-<?= strtolower($p->estado_stock) ?>">
                                <?= h($p->estado_stock) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="14" class="muted" style="text-align:center; padding: 14px;">No hay productos para mostrar.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Reporte de inventario generado automáticamente
    </div>
</body>
</html>
