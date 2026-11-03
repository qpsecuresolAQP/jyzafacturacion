<?php
/**
 * @var \App\View\AppView $this
 * @var array $ingresos
 * @var array $porProveedor
 * @var array $totales
 * @var string $desde
 * @var string $hasta
 * @var int $proveedorId
 * @var string $estado
 * @var array $proveedores
 */
$simbolo = ['SOLES' => 'S/', 'DOLARES' => 'US$'];
$qs = ['desde' => $desde, 'hasta' => $hasta, 'proveedor_id' => $proveedorId ?: null, 'estado' => $estado];
$fmt = fn(string $f) => (new \DateTimeImmutable($f))->format('d/m/Y');
$tipos = [
    'producto' => ['Producto', 'bg-primary'],
    'tratamiento' => ['Procedimiento', 'bg-success'],
    'examen' => ['Examen', 'bg-warning text-dark'],
];
?>
<style>
    .detalle-ingreso td, .detalle-ingreso th { font-size: .85rem; padding: .3rem .5rem; }
    .fila-detalle > td { background: #f8f9fa; }
</style>

<?php $this->assign('title', 'Reporte de Compras'); ?>

<div class="container-fluid mt-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h3 class="text-info mb-0"><i class="fas fa-chart-bar"></i> Reporte de Compras</h3>
        <div class="d-flex gap-2">
            <?= $this->Html->link('<i class="fas fa-list"></i> Ver Ingresos', ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-outline-secondary btn-sm']) ?>
            <?= $this->Html->link(
                '<i class="fas fa-file-excel"></i> Exportar Excel',
                ['action' => 'reporte', '?' => array_filter($qs) + ['export' => 'excel']],
                ['escape' => false, 'class' => 'btn btn-success btn-sm']
            ) ?>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <?= $this->Form->create(null, ['type' => 'get', 'class' => 'row g-2 align-items-end']) ?>
                <div class="col-md-2">
                    <label class="form-label mb-0 small">Desde</label>
                    <input type="date" name="desde" value="<?= h($desde) ?>" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-0 small">Hasta</label>
                    <input type="date" name="hasta" value="<?= h($hasta) ?>" class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                    <label class="form-label mb-0 small">Proveedor</label>
                    <select name="proveedor_id" class="form-control form-control-sm">
                        <option value="">-- Todos los proveedores --</option>
                        <?php foreach ($proveedores as $id => $nombre): ?>
                            <option value="<?= (int) $id ?>" <?= $proveedorId === (int) $id ? 'selected' : '' ?>><?= h($nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-0 small">Estado</label>
                    <select name="estado" class="form-control form-control-sm">
                        <?php foreach (['REGISTRADO' => 'Registrados', 'ANULADO' => 'Anulados', 'TODOS' => 'Todos'] as $v => $l): ?>
                            <option value="<?= $v ?>" <?= $estado === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-info flex-fill"><i class="fas fa-filter"></i> Aplicar</button>
                    <?= $this->Html->link('<i class="fas fa-times"></i>', ['action' => 'reporte'], [
                        'escape' => false, 'class' => 'btn btn-sm btn-outline-secondary', 'title' => 'Limpiar filtros',
                    ]) ?>
                </div>
            <?= $this->Form->end() ?>
        </div>
    </div>

    <p class="text-muted small mb-2">
        Periodo: <strong><?= $fmt($desde) ?></strong> al <strong><?= $fmt($hasta) ?></strong>
        <?php if ($proveedorId): ?> · Proveedor: <strong><?= h($proveedores[$proveedorId] ?? '#' . $proveedorId) ?></strong><?php endif; ?>
        <?php if ($estado === 'ANULADO'): ?> · Los ingresos anulados no suman a los totales.<?php endif; ?>
    </p>

    <!-- KPIs -->
    <div class="row mb-3">
        <?php if (empty($totales)): ?>
            <div class="col-12">
                <div class="alert alert-light border mb-0">No hay compras registradas en este periodo.</div>
            </div>
        <?php else: ?>
            <?php foreach ($totales as $moneda => $t): ?>
                <div class="col-md-4 mb-2">
                    <div class="card border-info h-100">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-1"><i class="fas fa-shopping-cart text-info"></i> Total comprado (<?= h($moneda) ?>)</h6>
                            <h3 class="text-info mb-0"><?= $simbolo[$moneda] ?? '' ?> <?= number_format($t['total'], 2) ?></h3>
                            <small class="text-muted">
                                <?= (int) $t['cantidad'] ?> ingreso(s) · Neto <?= number_format($t['subtotal'], 2) ?> · IGV <?= number_format($t['igv'], 2) ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="col-md-4 mb-2">
                <div class="card border-secondary h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1"><i class="fas fa-truck"></i> Proveedores</h6>
                        <h3 class="mb-0"><?= count(array_unique(array_column($porProveedor, 'proveedor_id'))) ?></h3>
                        <small class="text-muted">con compras en el periodo</small>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Resumen por proveedor -->
    <div class="card mb-3">
        <div class="card-header"><strong><i class="fas fa-truck"></i> Compras por proveedor</strong></div>
        <div class="table-responsive">
            <table class="table table-striped mb-0" style="min-width: 760px;">
                <thead class="bg-info text-white">
                    <tr>
                        <th>Proveedor</th>
                        <th class="text-center">Ingresos</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">IGV</th>
                        <th class="text-end">Total</th>
                        <th style="width: 18%;">% del total</th>
                        <th>Último ingreso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($porProveedor)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-3">Sin compras en el periodo.</td></tr>
                    <?php else: ?>
                        <?php foreach ($porProveedor as $p): ?>
                            <?php $sim = $simbolo[$p['moneda']] ?? ''; ?>
                            <tr>
                                <td>
                                    <?= $this->Html->link(h($p['proveedor']), ['action' => 'reporte', '?' => array_filter(['proveedor_id' => $p['proveedor_id']] + $qs)], [
                                        'escape' => false, 'title' => 'Ver solo este proveedor',
                                    ]) ?>
                                    <?php if ($p['proveedor_doc']): ?><br><small class="text-muted"><?= h($p['proveedor_doc']) ?></small><?php endif; ?>
                                </td>
                                <td class="text-center"><?= (int) $p['cantidad'] ?></td>
                                <td class="text-end"><?= $sim ?> <?= number_format($p['subtotal'], 2) ?></td>
                                <td class="text-end"><?= $sim ?> <?= number_format($p['igv'], 2) ?></td>
                                <td class="text-end fw-bold"><?= $sim ?> <?= number_format($p['total'], 2) ?></td>
                                <td>
                                    <div class="progress" style="height: 16px;" title="<?= $p['porcentaje'] ?>% del total en <?= h($p['moneda']) ?>">
                                        <div class="progress-bar bg-info" style="width: <?= min(100, $p['porcentaje']) ?>%;"></div>
                                    </div>
                                    <small class="text-muted"><?= $p['porcentaje'] ?>%</small>
                                </td>
                                <td><?= $p['ultima'] ? $p['ultima']->format('d/m/Y') : '—' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detalle de ingresos -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><strong><i class="fas fa-file-invoice"></i> Ingresos del periodo</strong> <span class="text-muted small">(<?= count($ingresos) ?>)</span></span>
            <?php if (!empty($ingresos)): ?>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="toggle-detalles">
                    <i class="fas fa-compress-alt"></i> Ocultar detalles
                </button>
            <?php endif; ?>
        </div>
        <div class="table-responsive">
            <table class="table table-striped mb-0" style="min-width: 900px;">
                <thead class="bg-info text-white">
                    <tr>
                        <th>Fecha</th>
                        <th>Documento</th>
                        <th>Proveedor</th>
                        <th class="text-center">Ítems</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">IGV</th>
                        <th class="text-end">Total</th>
                        <th>Estado</th>
                        <th class="text-center">Ver</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ingresos)): ?>
                        <tr><td colspan="9" class="text-center text-muted py-3">No hay ingresos para este filtro.</td></tr>
                    <?php else: ?>
                        <?php foreach ($ingresos as $i): ?>
                            <?php $sim = $simbolo[$i['moneda']] ?? ''; ?>
                            <tr class="<?= $i['estado'] === 'ANULADO' ? 'text-muted' : '' ?>">
                                <td><?= $i['fecha'] ? $i['fecha']->format('d/m/Y H:i') : '—' ?></td>
                                <td><?= h($i['documento']) ?></td>
                                <td><?= h($i['proveedor']) ?></td>
                                <td class="text-center"><?= (int) $i['items'] ?></td>
                                <td class="text-end"><?= $sim ?> <?= number_format($i['subtotal'], 2) ?></td>
                                <td class="text-end"><?= $sim ?> <?= number_format($i['igv'], 2) ?></td>
                                <td class="text-end"><?= $sim ?> <?= number_format($i['total'], 2) ?></td>
                                <td>
                                    <span class="badge <?= $i['estado'] === 'ANULADO' ? 'badge-danger' : 'badge-success' ?>"><?= h($i['estado']) ?></span>
                                </td>
                                <td class="text-center">
                                    <?= $this->Html->link('<i class="fas fa-eye"></i>', ['action' => 'view', $i['id']], [
                                        'escape' => false, 'class' => 'btn btn-info btn-sm', 'title' => 'Ver ingreso',
                                    ]) ?>
                                </td>
                            </tr>
                            <?php if (!empty($i['detalle'])): ?>
                                <tr class="fila-detalle">
                                    <td></td>
                                    <td colspan="8">
                                        <table class="table table-sm table-bordered mb-0 bg-white detalle-ingreso">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Ítem</th>
                                                    <th class="text-end">Cantidad</th>
                                                    <th class="text-end">P. Unit. c/IGV</th>
                                                    <th class="text-end">Total</th>
                                                    <th>Lote / Venc.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($i['detalle'] as $d): ?>
                                                    <?php [$tipoTxt, $tipoCls] = $tipos[$d['tipo']] ?? $tipos['producto']; ?>
                                                    <tr>
                                                        <td><span class="badge <?= $tipoCls ?>"><?= $tipoTxt ?></span> <?= h($d['nombre']) ?></td>
                                                        <td class="text-end"><?= $this->Number->format($d['cantidad']) ?></td>
                                                        <td class="text-end"><?= $sim ?> <?= number_format($d['precio_con_igv'], 2) ?></td>
                                                        <td class="text-end"><?= $sim ?> <?= number_format($d['total'], 2) ?></td>
                                                        <td>
                                                            <?= h($d['lote'] ?: '—') ?>
                                                            <?php if ($d['vencimiento']): ?> · <?= $d['vencimiento']->format('d/m/Y') ?><?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
(function () {
    const btn = document.getElementById('toggle-detalles');
    if (!btn) return;
    let visibles = true;
    btn.addEventListener('click', function () {
        visibles = !visibles;
        document.querySelectorAll('.fila-detalle').forEach(tr => { tr.style.display = visibles ? '' : 'none'; });
        btn.innerHTML = visibles
            ? '<i class="fas fa-compress-alt"></i> Ocultar detalles'
            : '<i class="fas fa-expand-alt"></i> Mostrar detalles';
    });
})();
</script>
