<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tratamiento $tratamiento
 */
?>

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- Alerta de permiso -->
            <?php if (!$this->Permisos->tiene('Tratamientos', 'add')): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-lock"></i> No tienes permisos para registrar servicios.
                </div>
            <?php else: ?>
                <?= $this->Form->create($tratamiento, ['class' => 'row g-3']) ?>

                <!-- Título -->
                <div class="col-12 mb-4">
                    <h3 class="text-info"><i class="fas fa-briefcase-medical"></i> Registrar Nuevo Servicio</h3>
                </div>

                <!-- Nombre -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('nombre', [
                        'label'       => 'Nombre',
                        'class'       => 'form-control',
                        'placeholder' => 'Ej: Dermatología, Limpieza facial',
                        'required'    => true
                    ]) ?>
                </div>

                <!-- Descripción -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('descripcion', [
                        'label'       => 'Descripción',
                        'type'        => 'textarea',
                        'class'       => 'form-control',
                        'placeholder' => 'Detalles y características del servicio',
                        'rows'        => 4
                    ]) ?>
                </div>

                <!-- Costo -->
                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('costo', [
                        'label'       => 'Costo (S/)',
                        'class'       => 'form-control',
                        'placeholder' => 'Ej: 150.00',
                        'type'        => 'number',
                        'step'        => '0.01',
                        'min'         => '0',
                        'required'    => true
                    ]) ?>
                </div>

                <!-- Monto fijo a pagar al doctor -->
                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('monto_fijo_pago', [
                        'label'       => 'Monto Fijo a Pagar (S/)',
                        'class'       => 'form-control',
                        'placeholder' => 'Ej: 20.00',
                        'type'        => 'number',
                        'step'        => '0.01',
                        'min'         => '0',
                    ]) ?>
                    <small class="text-muted">
                        Solo aplica a doctores con modo de pago "Monto fijo por servicio".
                        Deja en 0 si no corresponde.
                    </small>
                </div>

                <!-- Gasto en materiales -->
                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('gasto_materiales', [
                        'label'       => 'Gasto en Materiales (S/)',
                        'class'       => 'form-control',
                        'placeholder' => 'Ej: 15.00',
                        'type'        => 'number',
                        'step'        => '0.01',
                        'min'         => '0',
                    ]) ?>
                    <small class="text-muted">
                        Costo estimado de los materiales usados en este servicio. Se descuenta del costo
                        para calcular la utilidad real (costo − pago doctor − materiales).
                    </small>
                </div>

                <!-- Botones -->
                <div class="col-12 text-center">
                    <?= $this->Form->button(__('Guardar Servicio'), ['class' => 'btn btn-info', 'id' => 'btnGuardar']) ?>
                    <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary ms-2']) ?>
                </div>

                <?= $this->Form->end() ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("form");
        const btnGuardar = document.getElementById("btnGuardar");

        if (form && btnGuardar) {
            form.addEventListener("submit", function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    return;
                }
                btnGuardar.disabled = true;
                btnGuardar.innerText = "Guardando...";
            });
        }
    });
</script>