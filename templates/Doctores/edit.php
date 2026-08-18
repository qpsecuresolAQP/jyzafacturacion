<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Doctore $doctore
 * @var array $tratamientos
 * @var array $tarifasActuales
 */
?>
<div class="container mt-4 mb-4">
    <?= $this->Form->create($doctore, ['class' => 'row g-3']) ?>

    <!-- Información del Doctor -->
    <div class="col-12 mb-4">
        <h3 class="text-info"><i class="fas fa-user-md"></i> Editar Doctor</h3>
    </div>

    <!-- Campos Nombre y Apellido en la misma fila -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('nombre', [
            'label' => 'Nombre del Doctor',
            'class' => 'form-control',
            'placeholder' => 'Ejemplo: Juan, María'
        ]) ?>
    </div>
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('apellido', [
            'label' => 'Apellido del Doctor',
            'class' => 'form-control',
            'placeholder' => 'Ejemplo: Pérez, García'
        ]) ?>
    </div>

    <!-- Campo Especialidad -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('especialidad', [
            'label' => 'Especialidad',
            'class' => 'form-control',
            'placeholder' => 'Ejemplo: Cardiología, Odontología'
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
            'id' => 'modo-pago-select',
        ]) ?>
    </div>

    <!-- Campo porcentaje de pago -->
    <div class="col-md-6 mb-3" id="wrap-porcentaje-pago">
        <?= $this->Form->control('porcentaje_pago', [
            'label' => 'Porcentaje de Pago (%)',
            'class' => 'form-control',
            'placeholder' => 'Ejemplo: 20',
            'type' => 'number',
            'step' => '0.01',
            'min' => '0',
            'max' => '100'
        ]) ?>
    </div>

    <!-- Campos Teléfono y Email en la misma fila -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('telefono', [
            'label' => 'Teléfono',
            'class' => 'form-control',
            'placeholder' => 'Ejemplo: +52 123 456 7890',
            'type' => 'tel'
        ]) ?>
    </div>
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('email', [
            'label' => 'Correo Electrónico',
            'class' => 'form-control',
            'placeholder' => 'Ejemplo: doctor@ejemplo.com',
            'type' => 'email'
        ]) ?>
    </div>

    <!-- Tarifas fijas por tratamiento (solo si modo_pago = FIJO) -->
    <div class="col-12 mb-3" id="wrap-tarifas-fijas">
        <h5 class="text-info"><i class="fas fa-list"></i> Tarifas Fijas por Tratamiento</h5>
        <p class="text-muted small">
            Por defecto se usa la "Tarifa por Defecto" configurada en cada Tratamiento (aplica a todos los doctores en modo fijo).
            Solo llena "Tarifa Especial" si a este doctor en particular se le paga distinto para ese tratamiento; déjalo vacío para usar el default.
        </p>
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead class="bg-info text-white">
                    <tr>
                        <th>Tratamiento</th>
                        <th>Precio Clínica</th>
                        <th>Tarifa por Defecto</th>
                        <th style="width: 200px;">Tarifa Especial (opcional)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tratamientos as $trat): ?>
                        <tr>
                            <td><?= h($trat->nombre) ?></td>
                            <td>S/ <?= number_format((float) $trat->costo, 2) ?></td>
                            <td>
                                <?= $trat->monto_fijo_pago > 0
                                    ? 'S/ ' . number_format((float) $trat->monto_fijo_pago, 2)
                                    : '<span class="text-muted">Sin default</span>' ?>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">S/</span>
                                    <input type="number" step="0.01" min="0"
                                        name="tarifas[<?= $trat->id ?>]"
                                        class="form-control"
                                        placeholder="<?= number_format((float) $trat->monto_fijo_pago, 2) ?>"
                                        value="<?= isset($tarifasActuales[$trat->id]) ? h($tarifasActuales[$trat->id]) : '' ?>">
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Botones -->
    <div class="col-12 text-center mt-3">
        <?= $this->Form->button(__('Guardar Cambios'), ['class' => 'btn btn-info']) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary me-2']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>

<script>
    (function () {
        // Este formulario se abre normalmente dentro de un modal cargado vía AJAX
        // (ver templates/layout/default.php, .openModal). En ese caso el DOM ya
        // existe en el momento en que este <script> se ejecuta, pero el evento
        // DOMContentLoaded de la página ya pasó hace rato y nunca se vuelve a
        // disparar — por eso NO se puede depender de él aquí.
        const modoPagoSelect = document.getElementById("modo-pago-select");
        const wrapPorcentaje = document.getElementById("wrap-porcentaje-pago");
        const wrapTarifas = document.getElementById("wrap-tarifas-fijas");

        function toggleModoPago() {
            if (!modoPagoSelect) return;
            const esFijo = modoPagoSelect.value === 'FIJO';
            if (wrapPorcentaje) wrapPorcentaje.style.display = esFijo ? 'none' : '';
            if (wrapTarifas) wrapTarifas.style.display = esFijo ? '' : 'none';
            if (esFijo && wrapPorcentaje) {
                const inputPorcentaje = wrapPorcentaje.querySelector('input');
                if (inputPorcentaje) inputPorcentaje.value = 0;
            }
        }

        if (modoPagoSelect) {
            modoPagoSelect.addEventListener("change", toggleModoPago);
            toggleModoPago();
        }
    })();
</script>