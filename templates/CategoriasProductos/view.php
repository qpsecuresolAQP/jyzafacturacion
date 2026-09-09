<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CategoriaProducto $categoriaProducto
 * @var array $productosActivos
 * @var array $productosInactivos
 */
?>
<div class="container-fluid mt-4 mb-4">
    <div class="row">
        <div class="col-12 col-xl-10 offset-xl-1">
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

            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                <h5 class="text-info mb-0">Productos en esta categoría</h5>
                <?php if (!empty($productosActivos)): ?>
                    <div class="d-flex gap-2">
                        <input type="text" id="buscarProductoCategoria" class="form-control form-control-sm" placeholder="Buscar por nombre o código..." style="min-width: 240px;">
                        <button type="button" id="limpiarBuscarProductoCategoria" class="btn btn-sm btn-outline-secondary" title="Limpiar búsqueda">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            <div class="table-responsive">
                <table class="table table-striped mt-2" style="table-layout: fixed; width: 100%; min-width: 760px;">
                    <colgroup>
                        <col style="width: 32%;">
                        <col style="width: 13%;">
                        <col style="width: 12%;">
                        <col style="width: 10%;">
                        <col style="width: 10%;">
                        <col style="width: 23%;">
                    </colgroup>
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
                    <tbody id="tablaProductosCategoria">
                        <?php if (empty($productosActivos)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No hay productos activos en esta categoría.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($productosActivos as $producto): ?>
                                <tr data-nombre="<?= h(mb_strtolower($producto->nombre)) ?>" data-codigo="<?= h(mb_strtolower((string)$producto->codigo)) ?>">
                                    <td class="text-truncate" style="max-width: 0;" title="<?= h($producto->nombre) ?>"><?= h($producto->nombre) ?></td>
                                    <td class="text-truncate" style="max-width: 0;"><?= h($producto->codigo ?: '-') ?></td>
                                    <td><?= number_format((float)$producto->precio, 2) ?></td>
                                    <td><?= $this->Number->format($producto->stock) ?></td>
                                    <td>
                                        <span class="badge badge-success">Activo</span>
                                    </td>
                                    <td class="text-center text-nowrap">
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
                                        <?php if ($this->Permisos->tiene('Productos', 'delete')): ?>
                                            <?= $this->Form->postLink(
                                                '<i class="fas fa-trash"></i>',
                                                ['controller' => 'Productos', 'action' => 'delete', $producto->id],
                                                ['escape' => false, 'title' => 'Eliminar', 'class' => 'btn btn-danger btn-sm', 'confirm' => '¿Estás seguro? El producto "' . $producto->nombre . '" será eliminado de esta categoría.']
                                            ) ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <p id="sinResultadosProductoCategoria" class="text-center text-muted py-3" hidden>Ningún producto coincide con la búsqueda.</p>
            </div>

            <?php if (!empty($productosInactivos)): ?>
                <div class="mt-4">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="collapse" data-target="#productosEliminados" aria-expanded="false">
                        <i class="fas fa-trash-restore"></i> Ver productos eliminados (<?= count($productosInactivos) ?>)
                    </button>
                    <div class="collapse mt-2" id="productosEliminados">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped" style="table-layout: fixed; width: 100%; min-width: 640px;">
                                <colgroup>
                                    <col style="width: 35%;">
                                    <col style="width: 15%;">
                                    <col style="width: 15%;">
                                    <col style="width: 15%;">
                                    <col style="width: 20%;">
                                </colgroup>
                                <thead class="bg-secondary text-white">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Código</th>
                                        <th>Precio (S/)</th>
                                        <th>Stock</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($productosInactivos as $producto): ?>
                                        <tr>
                                            <td class="text-truncate" style="max-width: 0;" title="<?= h($producto->nombre) ?>"><?= h($producto->nombre) ?></td>
                                            <td class="text-truncate" style="max-width: 0;"><?= h($producto->codigo ?: '-') ?></td>
                                            <td><?= number_format((float)$producto->precio, 2) ?></td>
                                            <td><?= $this->Number->format($producto->stock) ?></td>
                                            <td class="text-center text-nowrap">
                                                <?php if ($this->Permisos->tiene('Productos', 'reactivar')): ?>
                                                    <?= $this->Form->postLink(
                                                        '<i class="fas fa-undo"></i> Reactivar',
                                                        ['controller' => 'Productos', 'action' => 'reactivar', $producto->id],
                                                        ['escape' => false, 'title' => 'Reactivar', 'class' => 'btn btn-success btn-sm', 'confirm' => '¿Reactivar el producto "' . $producto->nombre . '"?']
                                                    ) ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <?= $this->Html->link(__('Volver a la Lista'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($categoriaProducto->productos)): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('buscarProductoCategoria');
    var btnLimpiar = document.getElementById('limpiarBuscarProductoCategoria');
    var filas = document.querySelectorAll('#tablaProductosCategoria tr[data-nombre]');
    var sinResultados = document.getElementById('sinResultadosProductoCategoria');

    function filtrar() {
        var termino = input.value.trim().toLowerCase();
        var visibles = 0;

        filas.forEach(function (fila) {
            var coincide = termino === ''
                || fila.dataset.nombre.indexOf(termino) !== -1
                || fila.dataset.codigo.indexOf(termino) !== -1;
            fila.hidden = !coincide;
            if (coincide) visibles++;
        });

        sinResultados.hidden = visibles !== 0;
    }

    input.addEventListener('input', filtrar);
    btnLimpiar.addEventListener('click', function () {
        input.value = '';
        filtrar();
        input.focus();
    });
});
</script>
<?php endif; ?>
