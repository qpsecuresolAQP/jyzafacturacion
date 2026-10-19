<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Producto $producto
 * @var iterable<\App\Model\Entity\ProductoMovimiento> $movimientos
 * @var int $totalMovimientos
 * @var iterable<\App\Model\Entity\ProductoLote> $lotes
 * @var iterable<\App\Model\Entity\ProductoLoteCambio> $cambios
 */

use App\Model\Table\ProductoMovimientosTable;

$unidad = $producto->unidad_nombre ?? 'unid.';
$origenes = [
    'INGRESO' => 'Ingreso de mercadería',
    'MOVIMIENTO' => 'Movimiento manual',
    'DEVOLUCION' => 'Devolución',
    'STOCK_ANTERIOR' => 'Stock anterior a lotes',
];
$puedeMover = $this->Permisos->tiene('ProductoMovimientos', 'add');
$puedeEditarLote = $this->Permisos->tiene('ProductoMovimientos', 'editarLote');
?>
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-12">
            <div class="mb-4 mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h3 class="text-info mb-1"><i class="fas fa-history"></i> Historial de Movimientos</h3>
                    <p class="text-muted mb-0">
                        Producto: <strong><?= h($producto->nombre) ?></strong>
                        &middot; Stock actual: <strong><?= $this->Number->format($producto->stock) ?> <?= h($unidad) ?></strong>
                    </p>
                </div>
                <?php if ($puedeMover): ?>
                    <?= $this->Html->link('<i class="fas fa-plus"></i> Registrar movimiento', ['action' => 'add', $producto->id], ['escape' => false, 'class' => 'btn btn-info']) ?>
                <?php endif; ?>
            </div>

            <!-- ═══ Lotes con stock ═══ -->
            <h5>Lotes con stock</h5>
            <div class="table-responsive mb-4">
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Lote</th>
                            <th>Vencimiento</th>
                            <th class="text-end">Stock</th>
                            <th>Origen</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lotes as $lote): ?>
                            <tr class="<?= $lote->vencido ? 'table-danger' : '' ?>">
                                <td><?= $lote->lote ? h($lote->lote) : '<span class="text-muted">Sin número</span>' ?></td>
                                <td>
                                    <?php if ($lote->fecha_vencimiento): ?>
                                        <?= h($lote->fecha_vencimiento->format('d/m/Y')) ?>
                                        <?php $dias = $lote->dias_para_vencer; ?>
                                        <?php if ($lote->vencido): ?>
                                            <span class="badge bg-danger">Vencido hace <?= abs((int) $dias) ?> d</span>
                                        <?php elseif ($dias <= 90): ?>
                                            <span class="badge bg-warning text-dark">Vence en <?= (int) $dias ?> d</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Sin vencimiento</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end"><?= $this->Number->format($lote->stock) ?></td>
                                <td><small class="text-muted"><?= h($origenes[$lote->origen] ?? $lote->origen) ?></small></td>
                                <td class="text-end text-nowrap">
                                    <?php if ($puedeMover): ?>
                                        <?= $this->Html->link('<i class="fas fa-clipboard-check"></i> Ajustar', ['action' => 'add', $producto->id, '?' => ['tipo' => 'ajuste', 'lote' => $lote->id]], ['escape' => false, 'class' => 'btn btn-outline-primary btn-sm', 'title' => 'Corregir la cantidad tras un conteo físico']) ?>
                                        <?php if ($lote->vencido): ?>
                                            <?= $this->Html->link('<i class="fas fa-trash-alt"></i> Dar de baja', ['action' => 'add', $producto->id, '?' => ['tipo' => 'egreso', 'motivo' => 'VENCIDO', 'lote' => $lote->id]], ['escape' => false, 'class' => 'btn btn-outline-danger btn-sm']) ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($puedeEditarLote): ?>
                                        <?= $this->Html->link('<i class="fas fa-edit"></i> Editar lote', ['action' => 'editarLote', $lote->id], ['escape' => false, 'class' => 'btn btn-outline-secondary btn-sm', 'title' => 'Corregir número o vencimiento']) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if ($lotes->isEmpty()): ?>
                            <tr><td colspan="5" class="text-center text-muted">Sin stock.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- ═══ Movimientos ═══ -->
            <h5>Movimientos</h5>
            <div class="table-responsive">
                <table class="table table-striped table-sm mt-2">
                    <thead class="bg-info text-white">
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Motivo</th>
                            <th class="text-end">Cantidad</th>
                            <th>Lote(s)</th>
                            <th class="text-end">Stock ant.</th>
                            <th class="text-end">Stock nuevo</th>
                            <th>Referencia</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movimientos as $mov): ?>
                            <?php $esIngreso = $mov->tipo === 'ingreso'; ?>
                            <tr>
                                <td class="text-nowrap"><?= $mov->created ? h($mov->created->format('d/m/Y H:i')) : '-' ?></td>
                                <td>
                                    <span class="badge <?= $esIngreso ? 'badge-success bg-success' : 'badge-danger bg-danger' ?>">
                                        <?= $esIngreso ? 'Ingreso' : 'Egreso' ?>
                                    </span>
                                </td>
                                <td>
                                    <?= h(ProductoMovimientosTable::etiquetaMotivo($mov->motivo_codigo)) ?>
                                    <?php if ($mov->motivo): ?>
                                        <div class="small text-muted"><?= h($mov->motivo) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end"><?= $this->Number->format($mov->cantidad) ?></td>
                                <td class="small">
                                    <?php if (!empty($mov->producto_movimiento_lotes)): ?>
                                        <?php foreach ($mov->producto_movimiento_lotes as $ml): ?>
                                            <div>
                                                <?= h($ml->producto_lote->lote ?: 'Sin número') ?>
                                                <?php if ($ml->producto_lote->fecha_vencimiento): ?>
                                                    <span class="text-muted">(vence <?= h($ml->producto_lote->fecha_vencimiento->format('d/m/Y')) ?>)</span>
                                                <?php endif; ?>
                                                : <strong><?= $this->Number->format($ml->cantidad) ?></strong>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end"><?= $this->Number->format($mov->stock_anterior) ?></td>
                                <td class="text-end"><?= $this->Number->format($mov->stock_nuevo) ?></td>
                                <td class="small text-nowrap">
                                    <?php if ($mov->invoice_id && $this->Permisos->tiene('Invoices', 'view')): ?>
                                        <?= $this->Html->link(h(($mov->invoice->serie ?: 'RI') . '-' . ($mov->invoice->correlativo ?: $mov->invoice_id)), ['controller' => 'Invoices', 'action' => 'view', $mov->invoice_id]) ?>
                                    <?php elseif ($mov->ingreso_mercaderia_id && $this->Permisos->tiene('IngresosMercaderia', 'view')): ?>
                                        <?= $this->Html->link('Ingreso #' . (int) $mov->ingreso_mercaderia_id, ['controller' => 'IngresosMercaderia', 'action' => 'view', $mov->ingreso_mercaderia_id]) ?>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= h($mov->user->username ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if ((int) $totalMovimientos === 0): ?>
                            <tr><td colspan="9" class="text-center text-muted">Sin movimientos registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ((int) $totalMovimientos > 500): ?>
                <p class="small text-muted">Se muestran los 500 movimientos más recientes de <?= (int) $totalMovimientos ?>.</p>
            <?php endif; ?>

            <!-- ═══ Correcciones de lotes ═══ -->
            <?php if (!$cambios->isEmpty()): ?>
                <h5 class="mt-4">Correcciones de lotes</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr><th>Fecha</th><th>Lote</th><th>Dato</th><th>Antes</th><th>Después</th><th>Usuario</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cambios as $c): ?>
                                <tr>
                                    <td class="text-nowrap"><?= $c->created ? h($c->created->format('d/m/Y H:i')) : '-' ?></td>
                                    <td><?= h($c->producto_lote->lote ?: 'Sin número') ?></td>
                                    <td><?= $c->campo === 'lote' ? 'Número de lote' : 'Vencimiento' ?></td>
                                    <td><?= h($c->valor_anterior ?? '—') ?></td>
                                    <td><?= h($c->valor_nuevo ?? '—') ?></td>
                                    <td><?= h($c->user->username ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <?= $this->Html->link(
                    __('Volver a la Categoría'),
                    ['controller' => 'CategoriasProductos', 'action' => 'view', $producto->categoria_producto_id],
                    ['class' => 'btn btn-secondary']
                ) ?>
            </div>
        </div>
    </div>
</div>
