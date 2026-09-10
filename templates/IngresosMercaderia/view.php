<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\IngresosMercaderium $ingreso
 */
$sim = $ingreso->moneda === 'DOLARES' ? '$' : 'S/';
?>

<?php $this->assign('title', 'Ingreso de Mercadería #' . $ingreso->id); ?>

<div class="container-fluid mt-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h3 class="text-info mb-0">
            <i class="fas fa-truck-loading"></i> Ingreso #<?= $this->Number->format($ingreso->id) ?>
            <span class="badge <?= $ingreso->estado === 'REGISTRADO' ? 'badge-success' : 'badge-danger' ?> ms-2">
                <?= $ingreso->estado === 'REGISTRADO' ? 'Registrado' : 'Anulado' ?>
            </span>
        </h3>
        <div class="d-flex gap-2">
            <?= $this->Html->link('<i class="fas fa-list"></i> Ver Lista', ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-outline-secondary btn-sm']) ?>
            <?php if ($ingreso->estado === 'REGISTRADO' && $this->Permisos->tiene('IngresosMercaderia', 'anular')): ?>
                <?= $this->Form->postLink(
                    '<i class="fas fa-ban"></i> Anular Ingreso',
                    ['action' => 'anular', $ingreso->id],
                    ['escape' => false, 'class' => 'btn btn-danger btn-sm', 'confirm' => '¿Anular este ingreso? Se revertirá el stock sumado a cada producto.']
                ) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><strong>Documento</strong></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-2"><strong>Proveedor:</strong><br><?= h($ingreso->proveedore->nombre ?? '—') ?>
                    <?= !empty($ingreso->proveedore->ruc) ? '<br><small class="text-muted">RUC ' . h($ingreso->proveedore->ruc) . '</small>' : '' ?>
                </div>
                <div class="col-md-2 mb-2"><strong>Documento:</strong><br><?= h($ingreso->tipo_doc) ?> <?= h(trim(($ingreso->serie ? $ingreso->serie . '-' : '') . ($ingreso->numero ?: ''))) ?></div>
                <div class="col-md-2 mb-2"><strong>Moneda:</strong><br><?= h($ingreso->moneda) ?></div>
                <div class="col-md-2 mb-2"><strong>Fecha Ingreso:</strong><br><?= $ingreso->fecha_ingreso ? $ingreso->fecha_ingreso->format('d/m/Y H:i') : '—' ?></div>
                <div class="col-md-2 mb-2"><strong>Fecha Factura:</strong><br><?= $ingreso->fecha_factura ? $ingreso->fecha_factura->format('d/m/Y') : '—' ?></div>
                <div class="col-md-4 mb-2"><strong>Almacén:</strong><br><?= h($ingreso->almacen) ?></div>
                <div class="col-md-4 mb-2"><strong>Registrado por:</strong><br><?= h($ingreso->user->username ?? '—') ?></div>
                <div class="col-md-4 mb-2"><strong>Fecha registro:</strong><br><?= $ingreso->created ? $ingreso->created->format('d/m/Y H:i') : '—' ?></div>
            </div>
            <?php if (!empty($ingreso->observacion)): ?>
                <div class="mt-2"><strong>Observación:</strong> <?= nl2br(h($ingreso->observacion)) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><strong>Productos ingresados</strong></div>
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="bg-info text-white">
                    <tr>
                        <th>Producto</th>
                        <th class="text-end">Cantidad</th>
                        <th class="text-end">P. Unit. (neto)</th>
                        <th class="text-end">Subtotal (neto)</th>
                        <th class="text-end">IGV</th>
                        <th class="text-end">Total</th>
                        <th>Lote</th>
                        <th>Vencimiento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ingreso->ingresos_mercaderia_detalle as $d): ?>
                        <tr>
                            <td><?= h($d->producto->nombre ?? ('Producto #' . $d->producto_id)) ?></td>
                            <td class="text-end"><?= $this->Number->format($d->cantidad) ?></td>
                            <td class="text-end"><?= $sim ?> <?= number_format((float)$d->precio_unitario, 4) ?></td>
                            <td class="text-end"><?= $sim ?> <?= number_format((float)$d->subtotal, 2) ?></td>
                            <td class="text-end"><?= $sim ?> <?= number_format((float)$d->igv, 2) ?></td>
                            <td class="text-end"><?= $sim ?> <?= number_format((float)$d->total, 2) ?></td>
                            <td><?= h($d->lote ?: '—') ?></td>
                            <td><?= $d->fecha_vencimiento ? $d->fecha_vencimiento->format('d/m/Y') : '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="3" class="text-end">Totales</td>
                        <td class="text-end"><?= $sim ?> <?= number_format((float)$ingreso->subtotal, 2) ?></td>
                        <td class="text-end"><?= $sim ?> <?= number_format((float)$ingreso->igv, 2) ?></td>
                        <td class="text-end"><?= $sim ?> <?= number_format((float)$ingreso->total, 2) ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
