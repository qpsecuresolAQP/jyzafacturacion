<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\CategoriaProducto> $categoriasProductos
 */
?>

<?php $this->assign('title', 'Categorías de Productos'); ?>

<div class="categorias-productos index content">
    <!-- Botón Agregar (solo si tiene permiso) -->
    <?php if ($this->Permisos->tiene('CategoriasProductos', 'add')): ?>
        <?= $this->Html->link(__('Añadir Categoría'), ['action' => 'add'], ['class' => 'button float-right btn btn-info openModal']) ?>
    <?php endif; ?>

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
                            <?php if ($this->Permisos->tiene('CategoriasProductos', 'delete')): ?>
                                <?php
                                    $activo       = (int)$categoria->estado === 1;
                                    $buttonText   = $activo ? '<i class="fas fa-times"></i>' : '<i class="fas fa-check"></i>';
                                    $buttonClass  = $activo ? 'btn btn-danger' : 'btn btn-success';
                                    $buttonTitle  = $activo ? 'Inactivar' : 'Activar';
                                    $confirmMsg   = '¿Estás seguro? La categoría será ' . ($activo ? 'inactivada' : 'activada');
                                ?>
                                <?= $this->Html->link(
                                    $buttonText,
                                    ['action' => 'delete', $categoria->id],
                                    ['escape' => false, 'title' => $buttonTitle, 'class' => $buttonClass . ' btn-sm', 'confirm' => $confirmMsg]
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