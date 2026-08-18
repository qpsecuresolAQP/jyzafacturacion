<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FormasFarmaceutica $formaFarmaceutica
 */
?>

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- Alerta de permiso -->
            <?php if (!$this->Permisos->tiene('FormasFarmaceuticas', 'edit')): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-lock"></i> No tienes permisos para editar formas farmacéuticas.
                </div>
            <?php else: ?>
                <?= $this->Form->create($formaFarmaceutica, ['class' => 'row g-3']) ?>

                <!-- Título -->
                <div class="col-12 mb-4">
                    <h3 class="text-info"><i class="fas fa-capsules"></i> Editar Forma Farmacéutica</h3>
                </div>

                <!-- Nombre -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('nombre', [
                        'label'       => 'Nombre',
                        'type'        => 'text',
                        'class'       => 'form-control',
                        'required'    => true,
                        'placeholder' => 'Ej: CÁPSULA, TABLETA, JARABE'
                    ]) ?>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i> Nombre de la forma farmacéutica (único)
                    </small>
                </div>

                <!-- Descripción -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('descripcion', [
                        'label'       => 'Descripción',
                        'type'        => 'textarea',
                        'class'       => 'form-control',
                        'rows'        => 3,
                        'placeholder' => 'Ej: Forma sólida en cápsula de gelatina'
                    ]) ?>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i> Detalles adicionales (opcional)
                    </small>
                </div>

                <!-- Activa -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('activa', [
                        'label' => 'Activa',
                        'type'  => 'checkbox',
                    ]) ?>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i> Marca esta casilla si la forma está disponible
                    </small>
                </div>

                <!-- Botones -->
                <div class="col-12 text-center">
                    <?= $this->Form->button(__('Actualizar Forma Farmacéutica'), ['class' => 'btn btn-info']) ?>
                    <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary ms-2']) ?>
                </div>

                <?= $this->Form->end() ?>
            <?php endif; ?>
        </div>
    </div>
</div>