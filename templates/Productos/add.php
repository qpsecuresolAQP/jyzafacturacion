<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Producto $producto
 * @var array $categorias
 * @var array $proveedores
 */
?>

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <!-- Alerta de permiso -->
            <?php if (!$this->Permisos->tiene('Productos', 'add')): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-lock"></i> No tienes permisos para agregar productos.
                </div>
            <?php else: ?>
                <?= $this->Form->create($producto, ['class' => 'row g-3']) ?>

                <!-- Título -->
                <div class="col-12 mb-4">
                    <h3 class="text-info"><i class="fas fa-box"></i> Agregar Producto</h3>
                </div>

                <!-- Categoría -->
                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('categoria_producto_id', [
                        'label'   => 'Categoría',
                        'options' => $categorias,
                        'empty'   => 'Seleccione una categoría',
                        'class'   => 'form-control',
                        'required' => true
                    ]) ?>
                </div>

                <!-- Nombre -->
                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('nombre', [
                        'label'       => 'Nombre del Producto',
                        'class'       => 'form-control',
                        'placeholder' => 'Ejemplo: Paracetamol 500mg',
                        'required'    => true
                    ]) ?>
                </div>

                <!-- Proveedor -->
                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('proveedor_id', [
                        'label'   => 'Proveedor',
                        'options' => $proveedores,
                        'empty'   => '-- Ninguno --',
                        'class'   => 'form-control',
                    ]) ?>
                </div>

                <!-- Código interno -->
                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('codigo', [
                        'label'       => 'Código Interno',
                        'class'       => 'form-control',
                        'placeholder' => 'Ejemplo: PROD-001',
                    ]) ?>
                </div>

                <!-- Código SUNAT -->
                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('codigo_sunat', [
                        'label'       => 'Código SUNAT',
                        'class'       => 'form-control',
                        'placeholder' => 'Ejemplo: 49111500',
                    ]) ?>
                </div>

                <!-- Descripción -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('descripcion', [
                        'label' => 'Descripción',
                        'type'  => 'textarea',
                        'class' => 'form-control',
                        'rows'  => 2,
                        'placeholder' => 'Descripción breve del producto',
                    ]) ?>
                </div>

                <!-- Unidad -->
                <div class="col-md-4 mb-3">
                    <?= $this->Form->control('unidad', [
                        'label' => 'Unidad',
                        'class' => 'form-control',
                        'value' => 'NIU',
                        'placeholder' => 'Ejemplo: NIU',
                    ]) ?>
                </div>

                <!-- Precio de compra -->
                <div class="col-md-4 mb-3">
                    <?= $this->Form->control('precio_compra', [
                        'label'    => 'Precio de Compra (S/)',
                        'class'    => 'form-control',
                        'type'     => 'number',
                        'step'     => '0.01',
                        'min'      => '0',
                        'required' => true
                    ]) ?>
                </div>

                <!-- Precio -->
                <div class="col-md-4 mb-3">
                    <?= $this->Form->control('precio', [
                        'label'    => 'Precio de Venta (S/)',
                        'class'    => 'form-control',
                        'type'     => 'number',
                        'step'     => '0.01',
                        'min'      => '0',
                        'required' => true
                    ]) ?>
                </div>

                <!-- Stock -->
                <div class="col-md-4 mb-3">
                    <?= $this->Form->control('stock', [
                        'label'    => 'Stock',
                        'class'    => 'form-control',
                        'type'     => 'number',
                        'step'     => '0.01',
                        'min'      => '0',
                        'required' => true
                    ]) ?>
                </div>

                <!-- Stock mínimo -->
                <div class="col-md-4 mb-3">
                    <?= $this->Form->control('stock_minimo', [
                        'label' => 'Stock Mínimo',
                        'class' => 'form-control',
                        'type'  => 'number',
                        'step'  => '0.01',
                        'min'   => '0',
                        'value' => '0'
                    ]) ?>
                </div>

                <!-- Estado -->
                <div class="col-md-4 mb-3">
                    <?= $this->Form->control('estado', [
                        'label'   => 'Activo',
                        'type'    => 'checkbox',
                        'checked' => true
                    ]) ?>
                </div>

                <!-- Botones -->
                <div class="col-12 text-center">
                    <?= $this->Form->button(__('Guardar Producto'), ['class' => 'btn btn-info']) ?>
                    <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary ms-2']) ?>
                </div>

                <?= $this->Form->end() ?>
            <?php endif; ?>
        </div>
    </div>
</div>