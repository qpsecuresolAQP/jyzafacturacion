<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CategoriaProducto $categoriaProducto
 */
?>
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3 flex-wrap gap-2">
                <h3 class="text-info mb-0"><i class="fas fa-box"></i> Categoría: <?= h($categoriaProducto->nombre) ?></h3>

                <?php if ($this->Permisos->tiene('Productos', 'add')): ?>
                    <?= $this->Html->link(
                        '<i class="fas fa-plus"></i> Añadir Producto',
                        [
                            'controller' => 'Productos',
                            'action' => 'add',
                            '?' => ['categoria_producto_id' => $categoriaProducto->id],
                        ],
                        ['escape' => false, 'class' => 'btn btn-info btn-sm openModal']
                    ) ?>
                <?php endif; ?>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Descripción:</strong></div>
                <div class="col-md-9"><?= h($categoriaProducto->descripcion ?: '-') ?></div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3"><strong>Estado:</strong></div>
                <div class="col-md-9">
                    <?php $activo = (int)$categoriaProducto->estado === 1; ?>
                    <span class="badge <?= $activo ? 'badge-success' : 'badge-danger' ?>">
                        <?= $activo ? 'Activo' : 'Inactivo' ?>
                    </span>
                </div>
            </div>

            <h5 class="text-info">Productos en esta categoría</h5>
            <div class="table-responsive">
                <table class="table table-striped mt-2">
                    <thead class="bg-info text-white">
                        <tr>
                            <th>Nombre</th>
                            <th>Código</th>
                            <th>Precio (S/)</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categoriaProducto->productos)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No hay productos registrados en esta categoría.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categoriaProducto->productos as $producto): ?>
                                <tr>
                                    <td><?= h($producto->nombre) ?></td>
                                    <td><?= h($producto->codigo ?: '-') ?></td>
                                    <td><?= number_format((float)$producto->precio, 2) ?></td>
                                    <td><?= $this->Number->format($producto->stock) ?></td>
                                    <td>
                                        <?php $prodActivo = (int)$producto->estado === 1; ?>
                                        <span class="badge <?= $prodActivo ? 'badge-success' : 'badge-danger' ?>">
                                            <?= $prodActivo ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($this->Permisos->tiene('Productos', 'edit')): ?>
                                            <?= $this->Html->link(
                                                '<i class="fas fa-edit"></i>',
                                                ['controller' => 'Productos', 'action' => 'edit', $producto->id],
                                                ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm openModal']
                                            ) ?>
                                        <?php endif; ?>
                                        <?php if ($this->Permisos->tiene('ProductoMovimientos', 'add')): ?>
                                            <?= $this->Html->link(
                                                '<i class="fas fa-boxes"></i>',
                                                ['controller' => 'ProductoMovimientos', 'action' => 'add', $producto->id],
                                                ['escape' => false, 'title' => 'Registrar Ingreso/Egreso', 'class' => 'btn btn-info btn-sm openModal']
                                            ) ?>
                                        <?php endif; ?>
                                        <?php if ($this->Permisos->tiene('ProductoMovimientos', 'historial')): ?>
                                            <?= $this->Html->link(
                                                '<i class="fas fa-history"></i>',
                                                ['controller' => 'ProductoMovimientos', 'action' => 'historial', $producto->id],
                                                ['escape' => false, 'title' => 'Historial de Movimientos', 'class' => 'btn btn-secondary btn-sm openModal']
                                            ) ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-4">
                <?= $this->Html->link(__('Volver a la Lista'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
            </div>
        </div>
    </div>
</div>
