<style>
    .label-text {
        font-weight: bold;
    }
    .data-box {
        border: 1px solid #6c757d;
        border-radius: 5px;
        padding: 5px 10px;
    }
    
    .actions {
        text-align: center;
        margin-top: 30px;
    }
</style>

<div class="container mt-4 mb-4">
    <!-- Header -->
    <div class="col-12 mb-4">
        <h3 class="text-info"><i class="fas fa-user"></i> Información del Usuario</h3>
    </div>

    <!-- Cuerpo -->
    <div>
        <!-- Campo: Nombre de Usuario -->
        <div class="row mb-3">
            <div class="col-3">
                <p class="label-text"><?= __('Nombre de Usuario:') ?></p>
            </div>
            <div class="col-9">
                <div class="data-box"><?= h($user->username) ?></div>
            </div>
        </div>

        <!-- Campo: Rol -->
        <div class="row mb-3">
            <div class="col-3">
                <p class="label-text"><?= __('Rol:') ?></p>
            </div>
            <div class="col-9">
                <div class="data-box">
                    <?php 
                        $roles = [1 => 'Administrador', 2 => 'Especialista', 3 => 'Recepcionista'];
                        echo $roles[$user->rol_id] ?? __('N/A');
                    ?>
                </div>
            </div>
        </div>

        <!-- Campo: Fechas -->
        <div class="row mb-3">
            <div class="col-3">
                <p class="label-text"><?= __('Creado el:') ?></p>
            </div>
            <div class="col-9">
                <div class="data-box"><?= h($user->created) ?></div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-3">
                <p class="label-text"><?= __('Modificado el:') ?></p>
            </div>
            <div class="col-9">
                <div class="data-box"><?= h($user->modified) ?></div>
            </div>
        </div>
    </div>

    <!-- Acciones -->
    <div class="text-center mt-4 actions">
        <!-- Botón Volver (siempre disponible) -->
        <?= $this->Html->link(__('Volver a la Lista'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
        
        <!-- Botón Editar (solo si tiene permiso) -->
        <?php if ($this->Permisos->tiene('Users', 'edit')): ?>
            <?= $this->Html->link(__('Editar'), ['action' => 'edit', $user->id], ['class' => 'btn btn-warning']) ?>
        <?php endif; ?>
        
        <!-- Botón Permisos (solo si tiene permiso) -->
        <?php if ($this->Permisos->tiene('Users', 'permisos')): ?>
            <?= $this->Html->link(__('Gestionar Permisos'), ['action' => 'permisos', $user->id], ['class' => 'btn btn-secondary']) ?>
        <?php endif; ?>
    </div>
</div>
