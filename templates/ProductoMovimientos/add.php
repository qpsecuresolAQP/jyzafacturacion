<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Producto $producto
 * @var \App\Model\Entity\ProductoMovimiento $movimiento
 */
?>
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <?= $this->Form->create($movimiento, ['class' => 'row g-3']) ?>

            <div class="col-12 mb-4">
                <h3 class="text-info"><i class="fas fa-boxes"></i> Registrar Movimiento de Stock</h3>
                <p class="text-muted mb-0">
                    Producto: <strong><?= h($producto->nombre) ?></strong>
                    &middot; Stock actual: <strong><?= $this->Number->format($producto->stock) ?></strong>
                </p>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Tipo de Movimiento</label>
                <select name="tipo" class="form-select" required>
                    <option value="ingreso">Ingreso (compra, devolución, etc.)</option>
                    <option value="egreso">Egreso (merma, uso interno, etc.)</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Cantidad</label>
                <input type="number" name="cantidad" step="0.01" min="0.01" class="form-control" required>
            </div>

            <div class="col-12 mb-3">
                <label class="form-label fw-semibold">Motivo <span class="text-muted fw-normal">(opcional)</span></label>
                <textarea name="motivo" class="form-control" rows="2" placeholder="Ejemplo: Compra a proveedor, producto vencido, ajuste de conteo físico..."></textarea>
            </div>

            <div class="col-12 text-center">
                <?= $this->Form->button(__('Registrar Movimiento'), ['class' => 'btn btn-info']) ?>
                <?= $this->Html->link(
                    __('Cancelar'),
                    ['controller' => 'CategoriasProductos', 'action' => 'view', $producto->categoria_producto_id],
                    ['class' => 'btn btn-secondary ms-2']
                ) ?>
            </div>

            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
