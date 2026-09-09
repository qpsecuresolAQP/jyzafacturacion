<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\CategoriaProducto> $categoriasProductos
 * @var string $searchTerm
 */
?>

<?php $this->assign('title', 'Categorías de Productos'); ?>

<div class="categorias-productos index content">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <?= $this->Form->create(null, ['type' => 'get', 'class' => 'd-flex gap-2']) ?>
            <?= $this->Form->control('search', [
                'label' => false,
                'value' => $searchTerm,
                'placeholder' => 'Buscar por nombre...',
                'class' => 'form-control form-control-sm',
                'templates' => ['inputContainer' => '{{content}}'],
            ]) ?>
            <button type="submit" class="btn btn-sm btn-outline-info"><i class="fas fa-search"></i></button>
            <?php if ($searchTerm !== ''): ?>
                <?= $this->Html->link('<i class="fas fa-times"></i>', ['action' => 'index'], [
                    'escape' => false,
                    'class' => 'btn btn-sm btn-outline-secondary',
                    'title' => 'Limpiar búsqueda',
                ]) ?>
            <?php endif; ?>
        <?= $this->Form->end() ?>

        <!-- Botón Agregar (solo si tiene permiso) -->
        <?php if ($this->Permisos->tiene('CategoriasProductos', 'add')): ?>
            <?= $this->Html->link(__('Añadir Categoría'), ['action' => 'add'], ['class' => 'btn btn-info openModal']) ?>
        <?php endif; ?>
    </div>

    <div class="contenedor principal">
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'ID') ?></th>
                        <th><?= $this->Paginator->sort('nombre', 'Nombre') ?></th>
                        <th><?= $this->Paginator->sort('descripcion', 'Descripción') ?></th>
                        <th><?= $this->Paginator->sort('estado', 'Estado') ?></th>
                        <th class="actions text-dark"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categoriasProductos as $categoria): ?>
                    <tr>
                        <td><?= $this->Number->format($categoria->id) ?></td>
                        <td><?= h($categoria->nombre) ?></td>
                        <td><?= h($categoria->descripcion ?: '-') ?></td>
                        <td>
                            <?php
                                $activo = (int)$categoria->estado === 1;
                                $badge  = $activo ? 'badge-success' : 'badge-danger';
                                $label  = $activo ? 'Activo' : 'Inactivo';
                                echo '<span class="badge ' . $badge . '">' . h($label) . '</span>';
                            ?>
                        </td>

                        <td class="actions text-center">
                            <!-- Ver (solo si tiene permiso) -->
                            <?php if ($this->Permisos->tiene('CategoriasProductos', 'view')): ?>
                                <?= $this->Html->link(
                                    '<i class="fas fa-eye"></i>',
                                    ['action' => 'view', $categoria->id],
                                    ['escape' => false, 'title' => 'Ver', 'class' => 'btn btn-info btn-sm']
                                ) ?>
                            <?php endif; ?>

                            <!-- Editar (solo si tiene permiso) -->
                            <?php if ($this->Permisos->tiene('CategoriasProductos', 'edit')): ?>
                                <?= $this->Html->link(
                                    '<i class="fas fa-edit"></i>',
                                    ['action' => 'edit', $categoria->id],
                                    ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm openModal']
                                ) ?>
                            <?php endif; ?>

                            <!-- Activar/Inactivar (solo si tiene permiso) -->
                            <?php $activo = (int)$categoria->estado === 1; ?>
                            <?php if ($activo && $this->Permisos->tiene('CategoriasProductos', 'delete')): ?>
                                <?= $this->Form->postLink(
                                    '<i class="fas fa-times"></i>',
                                    ['action' => 'delete', $categoria->id],
                                    ['escape' => false, 'title' => 'Inactivar', 'class' => 'btn btn-danger btn-sm', 'confirm' => '¿Estás seguro? La categoría será inactivada']
                                ) ?>
                            <?php elseif (!$activo && $this->Permisos->tiene('CategoriasProductos', 'reactivar')): ?>
                                <?= $this->Form->postLink(
                                    '<i class="fas fa-check"></i>',
                                    ['action' => 'reactivar', $categoria->id],
                                    ['escape' => false, 'title' => 'Activar', 'class' => 'btn btn-success btn-sm', 'confirm' => '¿Estás seguro? La categoría será activada']
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