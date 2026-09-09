<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Producto> $productos
 * @var string $stockFiltro
 * @var array $stockCounts
 * @var string $searchTerm
 */
?>

<?php $this->assign('title', 'Productos'); ?>

<div class="productos index content">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="btn-group" role="group">
            <?= $this->Html->link('Todos', ['action' => 'index', '?' => ['search' => $searchTerm]], [
                'class' => 'btn btn-sm ' . ($stockFiltro === 'todos' ? 'btn-info' : 'btn-outline-info'),
            ]) ?>
            <?= $this->Html->link('Normal (' . $stockCounts['normal'] . ')', ['action' => 'index', '?' => ['stock' => 'normal', 'search' => $searchTerm]], [
                'class' => 'btn btn-sm ' . ($stockFiltro === 'normal' ? 'btn-success' : 'btn-outline-success'),
            ]) ?>
            <?= $this->Html->link('Bajo (' . $stockCounts['bajo'] . ')', ['action' => 'index', '?' => ['stock' => 'bajo', 'search' => $searchTerm]], [
                'class' => 'btn btn-sm ' . ($stockFiltro === 'bajo' ? 'btn-warning' : 'btn-outline-warning'),
            ]) ?>
            <?= $this->Html->link('Agotado (' . $stockCounts['agotado'] . ')', ['action' => 'index', '?' => ['stock' => 'agotado', 'search' => $searchTerm]], [
                'class' => 'btn btn-sm ' . ($stockFiltro === 'agotado' ? 'btn-danger' : 'btn-outline-danger'),
            ]) ?>
        </div>

        <?= $this->Form->create(null, ['type' => 'get', 'class' => 'd-flex gap-2']) ?>
            <?= $this->Form->hidden('stock', ['value' => $stockFiltro]) ?>
            <?= $this->Form->control('search', [
                'label' => false,
                'value' => $searchTerm,
                'placeholder' => 'Buscar por nombre o código...',
                'class' => 'form-control form-control-sm',
                'templates' => ['inputContainer' => '{{content}}'],
            ]) ?>
            <button type="submit" class="btn btn-sm btn-outline-info"><i class="fas fa-search"></i></button>
            <?php if ($searchTerm !== ''): ?>
                <?= $this->Html->link('<i class="fas fa-times"></i>', ['action' => 'index', '?' => ['stock' => $stockFiltro]], [
                    'escape' => false,
                    'class' => 'btn btn-sm btn-outline-secondary',
                    'title' => 'Limpiar búsqueda',
                ]) ?>
            <?php endif; ?>
        <?= $this->Form->end() ?>

        <!-- Botón Agregar (solo si tiene permiso) -->
        <?php if ($this->Permisos->tiene('Productos', 'add')): ?>
            <?= $this->Html->link(__('Añadir Producto'), ['action' => 'add'], ['class' => 'btn btn-info openModal']) ?>
        <?php endif; ?>
    </div>

    <div class="contenedor principal">
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'ID') ?></th>
                        <th><?= $this->Paginator->sort('categoria_producto_id', 'Categoría') ?></th>
                        <th><?= $this->Paginator->sort('nombre', 'Nombre') ?></th>
                        <th><?= $this->Paginator->sort('codigo', 'Código') ?></th>
                        <th><?= $this->Paginator->sort('precio_compra', 'Precio Compra') ?></th>
                        <th><?= $this->Paginator->sort('precio', 'Precio Venta') ?></th>
                        <th>Margen</th>
                        <th><?= $this->Paginator->sort('stock', 'Stock') ?></th>
                        <th>Proveedor</th>
                        <th><?= $this->Paginator->sort('estado', 'Estado') ?></th>
                        <th class="actions text-dark"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?= $this->Number->format($producto->id) ?></td>
                        <td><?= h($producto->categorias_producto->nombre ?? '-') ?></td>
                        <td><?= h($producto->nombre) ?></td>
                        <td><?= h($producto->codigo ?: '-') ?></td>
                        <td>S/ <?= number_format((float)$producto->precio_compra, 2) ?></td>
                        <td>S/ <?= number_format((float)$producto->precio, 2) ?></td>
                        <td>
                            <?php
                                $costo = (float)$producto->precio_compra;
                                $venta = (float)$producto->precio;
                                $margen = $venta - $costo;
                                $margenPct = $costo > 0 ? ($margen / $costo) * 100 : 0;
                                $margenClass = $margen >= 0 ? 'text-success' : 'text-danger';
                            ?>
                            <span class="<?= $margenClass ?>">
                                S/ <?= number_format($margen, 2) ?> (<?= number_format($margenPct, 1) ?>%)
                            </span>
                        </td>
                        <td>
                            <?php
                                $stockActual = (float) $producto->stock;
                                $stockMinimo = (float) $producto->stock_minimo;
                                $stockAgotado = $stockActual <= 0;
                                $stockBajo = !$stockAgotado && $stockMinimo > 0 && $stockActual <= $stockMinimo;
                                $stockClass = $stockAgotado ? 'text-danger fw-bold' : ($stockBajo ? 'text-warning fw-bold' : '');
                            ?>
                            <span class="<?= $stockClass ?>">
                                <?= number_format($stockActual, 2) ?>
                                <?php if ($stockAgotado): ?>
                                    <span class="badge bg-danger">Agotado</span>
                                <?php elseif ($stockBajo): ?>
                                    <span class="badge bg-warning text-dark">Bajo</span>
                                <?php endif; ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($producto->proveedor)): ?>
                                <div><?= h($producto->proveedor->nombre) ?></div>
                                <?php if ($stockAgotado || $stockBajo): ?>
                                    <div class="mt-1">
                                        <?php if (!empty($producto->proveedor->whatsapp)): ?>
                                            <?= $this->Html->link(
                                                '<i class="fab fa-whatsapp"></i>',
                                                'https://wa.me/' . preg_replace('/\D/', '', $producto->proveedor->whatsapp)
                                                    . '?text=' . rawurlencode('Hola, necesito reponer stock de: ' . $producto->nombre),
                                                ['escape' => false, 'target' => '_blank', 'title' => 'Contactar por WhatsApp', 'class' => 'btn btn-success btn-sm']
                                            ) ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                                $activo = (int)$producto->estado === 1;
                                $badge  = $activo ? 'badge-success' : 'badge-danger';
                                $label  = $activo ? 'Activo' : 'Inactivo';
                                echo '<span class="badge ' . $badge . '">' . h($label) . '</span>';
                            ?>
                        </td>

                        <td class="actions text-center">

                            <!-- Editar (solo si tiene permiso) -->
                            <?php if ($this->Permisos->tiene('Productos', 'edit')): ?>
                                <?= $this->Html->link(
                                    '<i class="fas fa-edit"></i>',
                                    ['action' => 'edit', $producto->id],
                                    ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm openModal']
                                ) ?>
                            <?php endif; ?>

                            <!-- Registrar movimiento de stock (solo si tiene permiso) -->
                            <?php if ($this->Permisos->tiene('ProductoMovimientos', 'add')): ?>
                                <?= $this->Html->link(
                                    '<i class="fas fa-boxes"></i>',
                                    ['controller' => 'ProductoMovimientos', 'action' => 'add', $producto->id],
                                    ['escape' => false, 'title' => 'Registrar Ingreso/Egreso', 'class' => 'btn btn-info btn-sm openModal']
                                ) ?>
                            <?php endif; ?>

                            <!-- Historial de movimientos (solo si tiene permiso) -->
                            <?php if ($this->Permisos->tiene('ProductoMovimientos', 'historial')): ?>
                                <?= $this->Html->link(
                                    '<i class="fas fa-history"></i>',
                                    ['controller' => 'ProductoMovimientos', 'action' => 'historial', $producto->id],
                                    ['escape' => false, 'title' => 'Historial de Movimientos', 'class' => 'btn btn-secondary btn-sm openModal']
                                ) ?>
                            <?php endif; ?>

                            <!-- Desactivar (solo si tiene permiso y sigue activo) -->
                            <?php if ($this->Permisos->tiene('Productos', 'delete') && (int)$producto->estado === 1): ?>
                                <?= $this->Form->postLink(
                                    '<i class="fas fa-times"></i>',
                                    ['action' => 'delete', $producto->id],
                                    ['escape' => false, 'title' => 'Desactivar', 'class' => 'btn btn-danger btn-sm', 'confirm' => '¿Estás seguro? El producto "' . $producto->nombre . '" será desactivado.']
                                ) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('Primero')) ?>
            <?= $this->Paginator->prev('< ' . __('Anterior')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('Siguiente') . ' >') ?>
            <?= $this->Paginator->last(__('Último') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registro(s) de un total de {{count}}')) ?></p>
    </div>
</div>