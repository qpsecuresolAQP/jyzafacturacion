<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CategoriaProducto $categoriaProducto
 */
?>

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- Alerta de permiso -->
            <?php if (!$this->Permisos->tiene('CategoriasProductos', 'add')): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-lock"></i> No tienes permisos para agregar categorías de productos.
                </div>
            <?php else: ?>
                <?= $this->Form->create($categoriaProducto, ['class' => 'row g-3']) ?>

                <!-- Título -->
                <div class="col-12 mb-4">
                    <h3 class="text-info"><i class="fas fa-tag"></i> Agregar Categoría de Producto</h3>
                </div>

                <!-- Campo: Nombre -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('nombre', [
                        'label'       => 'Nombre de la Categoría',
                        'class'       => 'form-control',
                        'placeholder' => 'Ejemplo: Electrónica',
                        'required'    => true
                    ]) ?>
                </div>

                <!-- Campo: Descripción -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('descripcion', [
                        'label'       => 'Descripción',
                        'class'       => 'form-control',
                        'type'        => 'text',
                        'placeholder' => 'Descripción breve de la categoría',
                    ]) ?>
                </div>

                <!-- Campo: Estado -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('estado', [
                        'label'   => 'Activo',
                        'type'    => 'checkbox',
                        'checked' => true
                    ]) ?>
                </div>

                <!-- Botones -->
                <div class="col-12 text-center">
                    <?= $this->Form->button(__('Guardar Categoría'), ['class' => 'btn btn-info']) ?>
                    <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary ms-2']) ?>
                </div>

                <?= $this->Form->end() ?>
            <?php endif; ?>
        </div>
    </div>
</div>