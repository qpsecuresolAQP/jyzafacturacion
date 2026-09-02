<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Examene $examene
 * @var array $categoriasExamenes
 * @var array $laboratorios
 */
?>
<div class="container mt-4 mb-4">
    <?= $this->Form->create($examene, ['class' => 'row g-3']) ?>

    <div class="col-12 mb-4">
        <h3 class="text-info"><i class="fas fa-flask"></i> Editar Examen</h3>
    </div>

    <div class="col-md-6 mb-3">
        <?= $this->Form->control('categoria_examen_id', [
            'label'   => 'Categoría',
            'type'    => 'select',
            'options' => $categoriasExamenes,
            'class'   => 'form-control',
        ]) ?>
    </div>

    <div class="col-md-6 mb-3">
        <?= $this->Form->control('nombre', [
            'label' => 'Nombre del Examen',
            'class' => 'form-control',
            'placeholder' => 'Ej: Hemograma completo',
        ]) ?>
    </div>

    <div class="col-md-6 mb-3">
        <?= $this->Form->control('muestra', [
            'label' => 'Tipo de Muestra',
            'class' => 'form-control',
            'placeholder' => 'Ej: Tubo tapa morada',
        ]) ?>
    </div>

    <div class="col-md-6 mb-3">
        <?= $this->Form->control('precio', [
            'label' => 'Precio al Paciente (S/)',
            'class' => 'form-control',
            'type' => 'number',
            'step' => '0.01',
            'min' => '0',
            'id' => 'precio-input',
        ]) ?>
        <small class="text-muted">Este es el monto que se cobra al paciente y se usa al facturar.</small>
    </div>

    <div class="col-md-6 mb-3">
        <?= $this->Form->control('precio_convenio', [
            'label' => 'Precio de Convenio / Costo Laboratorio (S/)',
            'class' => 'form-control',
            'type' => 'number',
            'step' => '0.01',
            'min' => '0',
        ]) ?>
        <small class="text-muted">Lo que se le paga al laboratorio (informativo, no se cobra al paciente).</small>
    </div>

    <div class="col-md-6 mb-3">
        <?= $this->Form->control('laboratorio_id', [
            'label'   => 'Laboratorio',
            'type'    => 'select',
            'options' => $laboratorios,
            'class'   => 'form-control',
            'empty'   => '-- Ninguno --',
        ]) ?>
        <small class="text-muted">Laboratorio al que se le debe pagar el precio de convenio de este examen.</small>
    </div>

    <div class="col-md-3 mb-3">
        <label for="comision-medico-porcentaje" class="form-label">% sobre precio (opcional)</label>
        <input type="number" id="comision-medico-porcentaje" class="form-control"
            step="0.01" min="0" max="100" placeholder="Ej: 20">
        <small class="text-muted">Solo ayuda a calcular el monto; no se guarda.</small>
    </div>

    <div class="col-md-3 mb-3">
        <?= $this->Form->control('comision_medico', [
            'label' => 'Comisión Médico (S/)',
            'class' => 'form-control',
            'type' => 'number',
            'step' => '0.01',
            'min' => '0',
            'id' => 'comision-medico-input',
        ]) ?>
        <small class="text-muted">Monto fijo que se paga al doctor por cada examen realizado. Deja en 0 si no corresponde.</small>
    </div>

    <div class="col-md-6 mb-3">
        <?= $this->Form->control('gasto_materiales', [
            'label' => 'Gasto en Materiales (S/)',
            'class' => 'form-control',
            'type' => 'number',
            'step' => '0.01',
            'min' => '0',
            'placeholder' => 'Ej: 5.00',
        ]) ?>
        <small class="text-muted">Costo estimado de los materiales usados para este examen (opcional).</small>
    </div>

    <div class="col-md-6 mb-3">
        <?= $this->Form->control('estado', [
            'label' => 'Activo',
            'type'  => 'checkbox',
        ]) ?>
    </div>

    <div class="col-12 text-center">
        <?= $this->Form->button(__('Guardar Cambios'), ['class' => 'btn btn-info', 'id' => 'submitButton']) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary ms-2']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>

<script>
    (function () {
        const form = document.querySelector("form");
        const btnGuardar = document.getElementById("submitButton");

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

        const precioInput = document.getElementById("precio-input");
        const porcentajeInput = document.getElementById("comision-medico-porcentaje");
        const comisionInput = document.getElementById("comision-medico-input");

        function calcularComisionPorPorcentaje() {
            const precio = parseFloat(precioInput?.value || 0);
            const porcentaje = parseFloat(porcentajeInput?.value || 0);
            if (!comisionInput || !porcentajeInput?.value) return;
            comisionInput.value = (precio * porcentaje / 100).toFixed(2);
        }

        if (porcentajeInput) {
            porcentajeInput.addEventListener("input", calcularComisionPorPorcentaje);
        }
        if (precioInput) {
            precioInput.addEventListener("input", calcularComisionPorPorcentaje);
        }

        // Evita que la ruedita del mouse cambie el valor de los campos numéricos
        // por error al hacer scroll de la página con el cursor encima.
        // preventDefault() en un listener "wheel" no-pasivo bloquea por completo
        // el incremento/decremento nativo del input; se registra por delegación
        // en document (capture) para cubrir los inputs number de este formulario
        // sin depender de encontrar el <form> correcto entre varios en la página.
        document.addEventListener("wheel", function (event) {
            const target = event.target;
            if (target && target.tagName === "INPUT" && target.type === "number" && document.activeElement === target) {
                event.preventDefault();
            }
        }, { capture: true, passive: false });
    })();
</script>
