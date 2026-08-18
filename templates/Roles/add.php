<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role $role
 * @var array $permisosAgrupados
 */
?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-9">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Crear Nuevo Rol</h3>
                    </div>
                    <?= $this->Form->create($role) ?>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nombre">Nombre del Rol</label>
                            <?= $this->Form->text('nombre', ['class' => 'form-control', 'placeholder' => 'Ej: Médico, Administrador, Secretaria']) ?>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <?= $this->Form->textarea('descripcion', ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Describe el propósito de este rol']) ?>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Selecciona los Permisos por Módulo</label>
                            
                            <div class="row">
                                <?php foreach ($permisosAgrupados as $controller => $permisos): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card card-outline card-info">
                                        <div class="card-header">
                                            <h5 class="card-title">
                                                <input type="checkbox" class="controller-check" data-controller="<?= h($controller) ?>" />
                                                <?= h($controller) ?>
                                            </h5>
                                        </div>
                                        <div class="card-body p-2">
                                            <?php foreach ($permisos as $permisoId => $permiso): ?>
                                            <div class="custom-control custom-checkbox">
                                                <?= $this->Form->checkbox("permisos._ids.{$permisoId}", [
                                                    'hiddenField' => false,
                                                    'class' => "custom-control-input permiso-check",
                                                    'data-controller' => h($controller),
                                                    'id' => "permiso-{$permisoId}"
                                                ]) ?>
                                                <label class="custom-control-label" for="permiso-<?= $permisoId ?>">
                                                    <small><?= h($permiso->action) ?></small>
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
                    <div class="card-footer">
                        <?= $this->Form->button('Guardar Rol', ['class' => 'btn btn-primary']) ?>
                        <?= $this->Html->link('Cancelar', ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Información</h3>
                    </div>
                    <div class="card-body">
                        <p>Un rol es un conjunto de permisos que define qué acciones puede realizar un usuario en el sistema.</p>
                        <hr />
                        <p><strong>Pasos:</strong></p>
                        <ol>
                            <li>Ingresa un nombre único para el rol</li>
                            <li>Añade una descripción clara</li>
                            <li>Selecciona los permisos específicos</li>
                            <li>Haz clic en "Guardar Rol"</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Marcar todos los permisos de un controlador
document.querySelectorAll('.controller-check').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const controller = this.getAttribute('data-controller');
        const permisos = document.querySelectorAll(`.permiso-check[data-controller="${controller}"]`);
        permisos.forEach(permiso => {
            permiso.checked = this.checked;
        });
    });
});

// Actualizar estado del checkbox "Seleccionar todo" cuando se cambian permisos
document.querySelectorAll('.permiso-check').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const controller = this.getAttribute('data-controller');
        const controllerCheckbox = document.querySelector(`.controller-check[data-controller="${controller}"]`);
        const permisos = document.querySelectorAll(`.permiso-check[data-controller="${controller}"]`);
        const todosChecked = Array.from(permisos).every(p => p.checked);
        controllerCheckbox.checked = todosChecked;
    });
});
</script>
