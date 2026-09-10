<?php
/**
 * @var \App\View\AppView $this
 * @var array $filas
 * @var array $resumen
 * @var int $diasProximo
 * @var string $filtro
 * @var string $searchTerm
 */
$badge = [
    'vencido' => ['cls' => 'badge-danger', 'icon' => 'fa-times-circle', 'txt' => 'Vencido'],
    'proximo' => ['cls' => 'badge-warning', 'icon' => 'fa-exclamation-triangle', 'txt' => 'Por vencer'],
    'vigente' => ['cls' => 'badge-success', 'icon' => 'fa-check-circle', 'txt' => 'Vigente'],
];
$qsBase = ['dias' => $diasProximo, 'search' => $searchTerm];
?>

<?php $this->assign('title', 'Vencimientos de Mercadería'); ?>

<div class="container-fluid mt-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h3 class="text-info mb-0"><i class="fas fa-calendar-times"></i> Reporte de Vencimientos</h3>
        <div class="d-flex gap-2">
            <?= $this->Html->link('<i class="fas fa-list"></i> Ver Ingresos', ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-outline-secondary btn-sm']) ?>
            <?= $this->Html->link(
                '<i class="fas fa-file-excel"></i> Exportar Excel',
                ['action' => 'vencimientos', '?' => $qsBase + ['filtro' => $filtro, 'export' => 'excel']],
                ['escape' => false, 'class' => 'btn btn-success btn-sm']
            ) ?>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1"><i class="fas fa-times-circle text-danger"></i> Vencidos</h6>
                    <h3 class="text-danger mb-0"><?= (int) $resumen['vencido'] ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1"><i class="fas fa-exclamation-triangle text-warning"></i> Por vencer (≤ <?= (int) $diasProximo ?> días)</h6>
                    <h3 class="text-warning mb-0"><?= (int) $resumen['proximo'] ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1"><i class="fas fa-check-circle text-success"></i> Vigentes</h6>
                    <h3 class="text-success mb-0"><?= (int) $resumen['vigente'] ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="d-flex justify-content-between align-items-end mb-3 flex-wrap gap-2">
        <div class="btn-group" role="group">
            <?php foreach (['todos' => 'Todos', 'vencido' => 'Vencidos', 'proximo' => 'Por vencer', 'vigente' => 'Vigentes'] as $k => $lbl): ?>
                <?= $this->Html->link($lbl, ['action' => 'vencimientos', '?' => $qsBase + ['filtro' => $k]], [
                    'class' => 'btn btn-sm ' . ($filtro === $k ? 'btn-info' : 'btn-outline-info'),
                ]) ?>
            <?php endforeach; ?>
        </div>

        <?= $this->Form->create(null, ['type' => 'get', 'class' => 'd-flex gap-2 align-items-end']) ?>
            <?= $this->Form->hidden('filtro', ['value' => $filtro]) ?>
            <div>
                <label class="form-label mb-0 small">Umbral "por vencer" (días)</label>
                <input type="number" name="dias" min="1" value="<?= (int) $diasProximo ?>" class="form-control form-control-sm" style="width: 90px;">
            </div>
            <div>
                <label class="form-label mb-0 small">Buscar</label>
                <input type="text" name="search" value="<?= h($searchTerm) ?>" placeholder="Producto, código o lote" class="form-control form-control-sm">
            </div>
            <button type="submit" class="btn btn-sm btn-outline-info"><i class="fas fa-filter"></i> Aplicar</button>
            <?php if ($searchTerm !== '' || $diasProximo !== 60): ?>
                <?= $this->Html->link('<i class="fas fa-times"></i>', ['action' => 'vencimientos', '?' => ['filtro' => $filtro]], [
                    'escape' => false, 'class' => 'btn btn-sm btn-outline-secondary', 'title' => 'Limpiar',
                ]) ?>
            <?php endif; ?>
        <?= $this->Form->end() ?>
    </div>

    <div class="alert alert-light border small">
        <i class="fas fa-info-circle"></i>
        "Cant. ingresada" es lo que entró en ese lote/vencimiento. "Stock actual" es el total del producto hoy
        (el sistema no lleva stock separado por lote): si el stock actual es menor que lo ingresado en un lote vencido,
        probablemente ya se vendió o consumió parte.
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped mb-0" style="min-width: 980px;">
                <thead class="bg-info text-white">
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Lote</th>
                        <th>Vencimiento</th>
                        <th class="text-end">Días</th>
                        <th>Estado</th>
                        <th class="text-end">Cant. ingresada</th>
                        <th class="text-end">Stock actual</th>
                        <th>Proveedor</th>
                        <th class="text-center">Ingreso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($filas)): ?>
                        <tr><td colspan="10" class="text-center text-muted py-4">No hay lotes con fecha de vencimiento para este filtro.</td></tr>
                    <?php else: ?>
                        <?php foreach ($filas as $f): ?>
                            <?php $b = $badge[$f['estado']]; ?>
                            <tr>
                                <td>
                                    <?= h($f['producto']) ?>
                                    <?php if ($f['codigo']): ?><br><small class="text-muted"><?= h($f['codigo']) ?></small><?php endif; ?>
                                </td>
                                <td><?= h($f['categoria']) ?></td>
                                <td><?= h($f['lote']) ?></td>
                                <td><?= $f['fecha_vencimiento']->format('d/m/Y') ?></td>
                                <td class="text-end">
                                    <?php if ($f['dias_restantes'] < 0): ?>
                                        <span class="text-danger">hace <?= abs($f['dias_restantes']) ?></span>
                                    <?php else: ?>
                                        <?= $f['dias_restantes'] ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $b['cls'] ?>"><i class="fas <?= $b['icon'] ?>"></i> <?= $b['txt'] ?></span>
                                </td>
                                <td class="text-end"><?= $this->Number->format($f['cantidad_ingresada']) ?></td>
                                <td class="text-end <?= $f['stock_actual'] <= 0 ? 'text-muted' : '' ?>">
                                    <?= $this->Number->format($f['stock_actual']) ?>
                                </td>
                                <td><?= h($f['proveedor']) ?></td>
                                <td class="text-center">
                                    <?= $this->Html->link('#' . $f['ingreso_id'], ['action' => 'view', $f['ingreso_id']], ['class' => 'btn btn-outline-info btn-sm']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
