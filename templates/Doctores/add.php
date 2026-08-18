<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Doctore $doctore
 */
?>

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <!-- Alerta de permiso -->
            <?php if (!$this->Permisos->tiene('Doctores', 'add')): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-lock"></i> No tienes permisos para registrar especialistas.
                </div>
            <?php else: ?>
                <?= $this->Form->create($doctore, ['class' => 'row g-3']) ?>

                <!-- Título -->
                <div class="col-12 mb-4">
                    <h3 class="text-info"><i class="fas fa-user-md"></i> Registrar Nuevo Especialista</h3>
                </div>

                <!-- Nombre -->
                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('nombre', [
                        'label'       => 'Nombre',
                        'class'       => 'form-control',
                        'placeholder' => 'Ej: Juan',
                        'required'    => true
                    ]) ?>
                </div>

                <!-- Apellido -->
                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('apellido', [
                        'label'       => 'Apellido',
                        'class'       => 'form-control',
                        'placeholder' => 'Ej: Pérez',
                        'required'    => true
                    ]) ?>
                </div>

                <!-- Especialidad -->
                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('especialidad', [
                        'label'       => 'Especialidad',
                        'class'       => 'form-control',
                        'placeholder' => 'Ej: Cardiología',
                        'required'    => true
                    ]) ?>
                </div>

                <!-- Teléfono -->
                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('telefono', [
                        'label'       => 'Teléfono',
                        'class'       => 'form-control',
                        'placeholder' => 'Ej: +51 999 888 777',
                        'type'        => 'tel'
                    ]) ?>
                </div>

                <!-- Email -->
                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('email', [
                        'label'       => 'Correo Electrónico',
                        'class'       => 'form-control',
                        'placeholder' => 'Ej: doctor@ejemplo.com',
                        'type'        => 'email'
                    ]) ?>
                </div>

                <!-- Modo de pago -->
                <div class="col-md-6 mb-3">
                    <label for="modo-pago-select" class="form-label d-block">Modo de Pago</label>
                    <?= $this->Form->select('modo_pago', [
                        'PORCENTAJE' => 'Porcentaje del cobro',
                        'FIJO' => 'Monto fijo por tratamiento',
                    ], [
                        'label' => false,
                        'class' => 'form-select',
                        'default' => 'PORCENTAJE',
                        'id' => 'modo-pago-select',
                    ]) ?>
                </div>

                <!-- Porcentaje de pago -->
                <div class="col-md-6 mb-3" id="wrap-porcentaje-pago">
                    <?= $this->Form->control('porcentaje_pago', [
                        'label'       => 'Porcentaje de Pago (%)',
                        'class'       => 'form-control',
                        'placeholder' => 'Ej: 20',
                        'type'        => 'number',
                        'step'        => '0.01',
                        'min'         => '0',
                        'max'         => '100'
                    ]) ?>
                </div>

                <div class="col-12 mb-3">
                    <div class="alert alert-info small mb-0">
                        <i class="fas fa-info-circle"></i>
                        Si eliges "Monto fijo por tratamiento", podrás configurar las tarifas por cada tratamiento después de guardar, desde "Editar".
                    </div>
                </div>

                <!-- Botones -->
                <div class="col-12 text-center">
                    <?= $this->Form->button(__('Guardar Especialista'), ['class' => 'btn btn-info', 'id' => 'submitButton']) ?>
                    <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary ms-2']) ?>
                </div>

                <?= $this->Form->end() ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    (function () {
        // Este formulario se abre normalmente dentro de un modal cargado vía AJAX
        // (ver templates/layout/default.php, .openModal), donde DOMContentLoaded
        // ya pasó y nunca se vuelve a disparar — no depender de ese evento aquí.
        const form = document.querySelector("form");
        const btnGuardar = document.getElementById("submitButton");
        const modoPagoSelect = document.getElementById("modo-pago-select");
        const wrapPorcentaje = document.getElementById("wrap-porcentaje-pago");

        function toggleModoPago() {
            if (!modoPagoSelect || !wrapPorcentaje) return;
            const esFijo = modoPagoSelect.value === 'FIJO';
            wrapPorcentaje.style.display = esFijo ? 'none' : '';
            if (esFijo) {
                const inputPorcentaje = wrapPorcentaje.querySelector('input');
                if (inputPorcentaje) inputPorcentaje.value = 0;
            }
        }

        if (modoPagoSelect) {
            modoPagoSelect.addEventListener("change", toggleModoPago);
            toggleModoPago();
        }

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
    })();
</script>