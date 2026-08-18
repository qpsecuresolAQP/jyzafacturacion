<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- Alertas de permiso -->
            <?php if (!$this->Permisos->tiene('Users', 'edit')): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-lock"></i> No tienes permisos para editar usuarios.
                </div>
                <div class="text-center mt-3">
                    <?= $this->Html->link(__('Volver'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                </div>
            <?php else: ?>
                <?= $this->Form->create($user, ['class' => 'row g-3']) ?>

                <!-- Información del Usuario -->
                <div class="col-12 mb-4">
                    <h3 class="text-info"><i class="fas fa-user-edit"></i> Editar Usuario</h3>
                </div>

                <!-- Campo: Username -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('username', [
                        'label' => 'Nombre de Usuario',
                        'class' => 'form-control',
                        'placeholder' => 'Ejemplo: usuario123',
                        'required' => true
                    ]) ?>
                </div>

                <!-- Campo: Password -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('password', [
                        'label' => 'Contraseña (dejar en blanco para mantener la actual)',
                        'type' => 'password',
                        'class' => 'form-control',
                        'placeholder' => 'Ingrese una nueva contraseña si desea cambiarla',
                        'required' => false,
                    ]) ?>
                </div>

                <!-- Campo: Rol (con valores 1, 2, 3) -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('rol_id', [
                        'label' => 'Rol del Usuario',
                        'class' => 'form-control',
                        'type' => 'select',
                        'options' => $roles,
                        'value' => $user->rol_id,
                        'required' => true
                    ]) ?>
                </div>

                <!-- Botones -->
                <div class="col-12 text-center">
                    <?= $this->Form->button(__('Guardar Cambios'), ['class' => 'btn btn-info']) ?>
                    <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary ms-2']) ?>
                </div>

                <?= $this->Form->end() ?>
            <?php endif; ?>
        </div>
    </div>
</div>
