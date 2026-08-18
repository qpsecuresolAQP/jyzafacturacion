<?php
/**
 * @var \App\View\AppView $this
 * @var array $permisosAgrupados
 */
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-shield-alt"></i> Crear Rol Personalizado</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'home']) ?>">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?= $this->Url->build(['controller' => 'Roles', 'action' => 'index']) ?>">Roles</a></li>
                    <li class="breadcrumb-item active">Crear Personalizado</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Datos del Rol</h3>
                    </div>

                    <?= $this->Form->create(null, ['id' => 'formCrearRol']) ?>
                    <div class="card-body">
                        <!-- Información del Rol -->
                        <div class="form-group">
                            <label for="nombre"><i class="fas fa-tag"></i> Nombre del Rol *</label>
                            <?= $this->Form->text('nombre', [
                                'class' => 'form-control',
                                'placeholder' => 'Ej: Asistente Administrativo',
                                'required' => true,
                                'maxlength' => 50
                            ]) ?>
                            <small class="form-text text-muted">Nombre único para identificar este rol</small>
                        </div>

                        <div class="form-group">
                            <label for="descripcion"><i class="fas fa-align-left"></i> Descripción</label>
                            <?= $this->Form->textarea('descripcion', [
                                'class' => 'form-control',
                                'placeholder' => 'Describe brevemente el propósito de este rol',
                                'rows' => 3,
                                'maxlength' => 150
                            ]) ?>
                            <small class="form-text text-muted">Opcional - ayuda a recordar el propósito del rol</small>
                        </div>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Información</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>¿Cómo crear un rol?</strong>
                            <ol class="small mb-0">
                                <li>Ingresa el nombre del rol</li>
                                <li>Describe su propósito (opcional)</li>
                                <li>Selecciona los permisos</li>
                                <li>Haz clic en "Guardar Rol"</li>
                            </ol>
                        </div>

                        <div class="alert alert-warning">
                            <strong>Nota:</strong> Los permisos seleccionados se heredarán a los usuarios que tengan este rol asignado.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Permisos -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-key"></i> Selecciona los Permisos</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool btn-sm" id="selectAll" title="Seleccionar todos">
                                <i class="fas fa-check-square"></i> Todos
                            </button>
                            <button type="button" class="btn btn-tool btn-sm" id="deselectAll" title="Deseleccionar todos">
                                <i class="fas fa-square"></i> Ninguno
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-lightbulb"></i> Selecciona los permisos específicos que tendrá este rol. Usa los botones "Todos" y "Ninguno" para facilitar tu selección.
                        </div>

                        <div class="row" id="permisosContainer">
                            <?php foreach ($permisosAgrupados as $controller => $permisos): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card card-outline card-secondary">
                                        <div class="card-header">
                                            <h5 class="card-title m-0">
                                                <strong><?= $controller ?></strong>
                                                <small class="text-muted">(<?= count($permisos) ?> permisos)</small>
                                            </h5>
                                        </div>
                                        <div class="card-body p-2">
                                            <?php foreach ($permisos as $permiso): ?>
                                                <div class="custom-control custom-checkbox mb-2">
                                                    <?= $this->Form->checkbox('permisos[]', [
                                                        'id' => 'permiso_' . $permiso->id,
                                                        'value' => $permiso->id,
                                                        'class' => 'custom-control-input permiso-checkbox',
                                                        'form' => 'formCrearRol'
                                                    ]) ?>
                                                    <label class="custom-control-label" for="permiso_<?= $permiso->id ?>">
                                                        <code class="text-primary"><?= $permiso->action ?></code>
                                                        <small class="text-muted"><?= $permiso->id ?></small>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="row mt-3 mb-3">
            <div class="col-12">
                <div class="btn-group" role="group">
                    <button type="submit" form="formCrearRol" class="btn btn-success btn-lg">
                        <i class="fas fa-save"></i> Guardar Rol
                    </button>
                    <?= $this->Html->link(
                        '<i class="fas fa-times"></i> Cancelar',
                        ['action' => 'index'],
                        ['class' => 'btn btn-secondary btn-lg', 'escape' => false]
                    ) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Botón: Seleccionar todos
    document.getElementById('selectAll').addEventListener('click', function() {
        document.querySelectorAll('.permiso-checkbox').forEach(checkbox => {
            checkbox.checked = true;
        });
    });

    // Botón: Deseleccionar todos
    document.getElementById('deselectAll').addEventListener('click', function() {
        document.querySelectorAll('.permiso-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
    });
});
</script>

<style>
    .card-header {
        padding: 0.5rem 1rem;
    }

    .card-body {
        padding: 1rem;
    }

    .custom-control-label {
        cursor: pointer;
        padding-left: 5px;
        user-select: none;
    }

    .card-outline.card-secondary .card-header {
        border-top-color: #6c757d;
    }

    .card-outline.card-secondary > .card-header {
        color: #495057;
        background-color: #f8f9fa;
    }

    .btn-group {
        gap: 10px;
    }

    .alert {
        margin-bottom: 1rem;
    }

    code {
        background-color: #f4f4f4;
        padding: 2px 6px;
        border-radius: 3px;
        font-weight: bold;
    }

    .permiso-checkbox {
        cursor: pointer;
    }
</style>
