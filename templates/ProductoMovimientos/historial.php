<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Producto $producto
 * @var iterable<\App\Model\Entity\ProductoMovimiento> $movimientos
 */
?>
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="mb-4 mt-3">
                <h3 class="text-info"><i class="fas fa-history"></i> Historial de Movimientos</h3>
                <p class="text-muted mb-0">
                    Producto: <strong><?= h($producto->nombre) ?></strong>
                    &middot; Stock actual: <strong><?= $this->Number->format($producto->stock) ?></strong>
                </p>
            </div>

            <div class="table-responsive">
                <table class="table table-striped mt-2">
                    <thead class="bg-info text-white">
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Stock Anterior</th>
                            <th>Stock Nuevo</th>
                            <th>Motivo</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($movimientos)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Sin movimientos registrados.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($movimientos as $mov): ?>
                                <tr>
                                    <td><?= $mov->created ? $mov->created->format('d/m/Y H:i') : '-' ?></td>
                                    <td>
                                        <?php $esIngreso = $mov->tipo === 'ingreso'; ?>
                                        <span class="badge <?= $esIngreso ? 'badge-success' : 'badge-danger' ?>">
                                            <?= $esIngreso ? 'Ingreso' : 'Egreso' ?>
                                        </span>
                                    </td>
                                    <td><?= $this->Number->format($mov->cantidad) ?></td>
                                    <td><?= $this->Number->format($mov->stock_anterior) ?></td>
                                    <td><?= $this->Number->format($mov->stock_nuevo) ?></td>
                                    <td><?= h($mov->motivo) ?></td>
                                    <td><?= h($mov->user->username ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

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
