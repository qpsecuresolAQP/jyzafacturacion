<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\ProductoMovimiento> $movimientos
 * @var array $resumen
 * @var float $totalPerdida
 * @var string $desde
 * @var string $hasta
 */

use App\Model\Table\ProductoMovimientosTable;

$soles = fn($v) => 'S/ ' . number_format((float) $v, 2);
$movimientos = collection($movimientos)->toList();
?>
<?php $this->assign('title', 'Pérdidas y bajas'); ?>
<div class="container-fluid mt-4 mb-4">
    <div class="d-flex justify-content-between align-items-end mb-3 flex-wrap gap-2">
        <div>
            <h3 class="text-info mb-0"><i class="fas fa-chart-line"></i> Pérdidas y bajas de stock</h3>
            <small class="text-muted">Mercadería vencida, mermas, roturas y faltantes de inventario, valorizadas al costo del momento.</small>
        </div>
        <?= $this->Form->create(null, ['type' => 'get', 'class' => 'd-flex gap-2 align-items-end']) ?>
            <div>
                <label class="form-label mb-0 small">Desde</label>
                <input type="date" name="desde" value="<?= h($desde) ?>" class="form-control form-control-sm">
            </div>
            <div>
                <label class="form-label mb-0 small">Hasta</label>
                <input type="date" name="hasta" value="<?= h($hasta) ?>" class="form-control form-control-sm">
            </div>
            <button type="submit" class="btn btn-sm btn-outline-info"><i class="fas fa-filter"></i> Ver</button>
        <?= $this->Form->end() ?>
    </div>

    <div class="row mb-3">
        <div class="col-md-4 mb-2">
            <div class="card border-danger h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">Pérdida total del período</h6>
                    <h3 class="text-danger mb-0"><?= $soles($totalPerdida) ?></h3>
                    <small class="text-muted">vencidos + mermas + roturas + faltantes</small>
                </div>
            </div>
        </div>
        <div class="col-md-8 mb-2">
            <div class="card h-100">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr><th>Motivo</th><th class="text-end">Movimientos</th><th class="text-end">Unidades</th><th class="text-end">Valor a costo</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resumen as $r): ?>
                                <tr>
                                    <td>
                                        <?= h($r['etiqueta']) ?>
                                        <?php if (!$r['perdida']): ?><small class="text-muted">(no se cuenta como pérdida)</small><?php endif; ?>
                                    </td>
                                    <td class="text-end"><?= (int) $r['movimientos'] ?></td>
                                    <td class="text-end"><?= $this->Number->format($r['unidades']) ?></td>
                                    <td class="text-end <?= $r['perdida'] ? 'text-danger fw-bold' : '' ?>"><?= $soles($r['valor']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($resumen)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">Sin bajas ni pérdidas en este período.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><strong>Detalle</strong></div>
        <div class="table-responsive">
            <table class="table table-sm table-striped mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Motivo</th>
                        <th>Lote(s)</th>
                        <th class="text-end">Cantidad</th>
                        <th class="text-end">Costo unit.</th>
                        <th class="text-end">Valor</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movimientos as $m): ?>
                        <tr>
                            <td class="text-nowrap"><?= $m->created ? h($m->created->format('d/m/Y H:i')) : '-' ?></td>
                            <td>
                                <?= $this->Html->link(h($m->producto->nombre ?? ('Producto #' . $m->producto_id)), ['action' => 'historial', $m->producto_id]) ?>
                            </td>
                            <td>
                                <?= h(ProductoMovimientosTable::etiquetaMotivo($m->motivo_codigo)) ?>
                                <?php if ($m->motivo): ?><div class="small text-muted"><?= h($m->motivo) ?></div><?php endif; ?>
                            </td>
                            <td class="small">
                                <?php foreach ($m->producto_movimiento_lotes as $ml): ?>
                                    <div>
                                        <?= h($ml->producto_lote->lote ?: 'Sin número') ?>
                                        <?php if ($ml->producto_lote->fecha_vencimiento): ?>
                                            <span class="text-muted">(vence <?= h($ml->producto_lote->fecha_vencimiento->format('d/m/Y')) ?>)</span>
                                        <?php endif; ?>
                                        : <?= $this->Number->format($ml->cantidad) ?>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (empty($m->producto_movimiento_lotes)): ?><span class="text-muted">—</span><?php endif; ?>
                            </td>
                            <td class="text-end"><?= $this->Number->format($m->cantidad) ?></td>
                            <td class="text-end"><?= number_format((float) $m->costo_unitario, 4) ?></td>
                            <td class="text-end"><?= $soles((float) $m->cantidad * (float) $m->costo_unitario) ?></td>
                            <td><?= h($m->user->username ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($movimientos)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-3">Sin movimientos en este período.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
