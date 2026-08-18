<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ViaAdministracion $viaAdministracion
 */
?>

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- Alerta de permiso -->
            <?php if (!$this->Permisos->tiene('ViasAdministracion', 'edit')): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-lock"></i> No tienes permisos para editar vías de administración.
                </div>
            <?php else: ?>
                <?= $this->Form->create($viaAdministracion, ['class' => 'row g-3']) ?>

                <!-- Título -->
                <div class="col-12 mb-4">
                    <h3 class="text-info"><i class="fas fa-route"></i> Editar Vía de Administración</h3>
                </div>

                <!-- Nombre -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('nombre', [
                        'label'    => 'Nombre',
                        'type'     => 'text',
                        'class'    => 'form-control',
                        'required' => true
                    ]) ?>
                </div>

                <!-- Descripción -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('descripcion', [
                        'label' => 'Descripción',
                        'type'  => 'textarea',
                        'class' => 'form-control',
                        'rows'  => 3
                    ]) ?>
                </div>

                <!-- Activa -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('activa', [
                        'label' => 'Activa',
                        'type'  => 'checkbox',
                    ]) ?>
                </div>

                <!-- Botones -->
                <div class="col-12 text-center">
                    <?= $this->Form->button(__('Actualizar Vía de Administración'), ['class' => 'btn btn-info']) ?>

                    <?php if ($this->Permisos->tiene('ViasAdministracion', 'delete')): ?>
                        <?= $this->Form->postLink(
                            __('Eliminar'),
                            ['action' => 'delete', $viaAdministracion->id],
                            [
                                'class'   => 'btn btn-danger ms-2',
                                'confirm' => __('¿Estás seguro de que deseas eliminar esta vía de administración?')
                            ]
                        ) ?>
                    <?php endif; ?>

                    <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary ms-2']) ?>
                </div>

                <?= $this->Form->end() ?>
            <?php endif; ?>
        </div>
    </div>
</div>