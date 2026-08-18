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
        <div class="subtitle">Generado el <?= date('Y-m-d H:i') ?></div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <span class="label">Total Productos</span>
                <span class="value"><?= (int) $totalProductos ?></span>
            </td>
            <td>
                <span class="label">Valor de Inventario</span>
                <span class="value">S/ <?= number_format($valorInventario, 2) ?></span>
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
                <span class="label">Presupuesto de Reposición</span>
                <span class="value">S/ <?= number_format($presupuestoReposicion, 2) ?></span>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 12%;">Categoría</th>
                <th style="width: 18%;">Producto</th>
                <th style="width: 8%;">Código</th>
                <th style="width: 12%;">Proveedor</th>
                <th style="width: 8%;">P. Compra</th>
                <th style="width: 8%;">P. Venta</th>
                <th style="width: 7%;">Margen %</th>
                <th style="width: 6%;">Stock</th>
                <th style="width: 6%;">Mínimo</th>
                <th style="width: 8%;">Valor Inv.</th>
                <th style="width: 7%;">Estado</th>
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
                        <td><?= number_format((float) $p->stock_minimo, 2) ?></td>
                        <td>S/ <?= number_format((float) $p->valor_inventario, 2) ?></td>
                        <td>
                            <span class="badge badge-<?= strtolower($p->estado_stock) ?>">
                                <?= h($p->estado_stock) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="muted" style="text-align:center; padding: 14px;">No hay productos para mostrar.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        SpazioDentale &mdash; Reporte generado automáticamente
    </div>
</body>
</html>
