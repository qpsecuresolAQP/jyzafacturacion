<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\PaquetesPago $paquetePago
 * @var array $historiasClinicas
 */
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><?= __('Nuevo Paquete de Pago') ?></h1>
        </div>
    </div>

    <?= $this->Form->create($paquetePago, ['class' => 'form-horizontal', 'id' => 'paquete-form', 'data-ajax' => 'true']) ?>
    
    <div class="card">
        <div class="card-body">
            <!-- Información del Paquete -->
            <h5 class="card-title mb-4"><?= __('Información del Paquete') ?></h5>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="historia_id"><?= __('Historia Clínica') ?></label>
                    <select id="historia_id" name="historia_id" class="form-select" required>
                        <option value="">-- Seleccionar Historia Clínica --</option>
                        <?php foreach ($historiasClinicas as $h): ?>
                            <option value="<?= $h->id ?>" <?= $paquetePago->historia_id == $h->id ? 'selected' : '' ?>>
                                <?= (!empty($h->paciente)) 
                                    ? h($h->paciente->nombre . ' ' . $h->paciente->apellido)
                                    : 'Sin paciente asignado' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="nombre"><?= __('Nombre del Paquete') ?></label>
                    <input type="text" id="nombre" name="nombre" class="form-control" 
                           placeholder="Ej: Terapia Física, Limpieza Dental"
                           value="<?= h($paquetePago->nombre ?? '') ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label" for="descripcion"><?= __('Descripción') ?></label>
                    <textarea id="descripcion" name="descripcion" class="form-control" 
                              rows="3" placeholder="Descripción detallada del paquete"><?= h($paquetePago->descripcion ?? '') ?></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="num_sesiones"><?= __('Número de Sesiones') ?></label>
                    <input type="number" id="num_sesiones" name="num_sesiones" class="form-control" 
                           min="1" placeholder="Cantidad de sesiones"
                           value="<?= h($paquetePago->num_sesiones ?? '') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="precio_total"><?= __('Precio Total') ?></label>
                    <input type="number" id="precio_total" name="precio_total" class="form-control precio-total-input" 
                           step="0.01" placeholder="0.00"
                           value="<?= h($paquetePago->precio_total ?? '') ?>" required>
                </div>
            </div>

            <!-- Forma de Pago -->
            <hr class="my-4">
            <h5 class="card-title mb-4"><?= __('Forma de Pago') ?></h5>
            <br>
            <br>
            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_pago" id="pago_contado" 
                           value="contado" checked onchange="cambiarTipoPago('contado')">
                    <label class="form-check-label" for="pago_contado">
                        <strong><?= __('Al Contado') ?></strong> (Pago único 100% del paquete)
                    </label>
                </div>

                <!-- Sección Contado -->
                <div id="seccion-contado" class="mt-3 ms-4">
                    <div class="mb-3" style="max-width: 400px;">
                        <label class="form-label" for="fecha_recordatorio"><?= __('Fecha del Recordatorio') ?></label>
                        <input type="date" id="fecha_recordatorio" name="fecha_recordatorio" 
                               class="form-control" value="<?= $paquetePago->fecha_recordatorio ? $paquetePago->fecha_recordatorio->format('Y-m-d') : '' ?>">
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_pago" id="pago_partes" 
                           value="partes" onchange="cambiarTipoPago('partes')">
                    <label class="form-check-label" for="pago_partes">
                        <strong><?= __('En Partes') ?></strong> (Múltiples cuotas)
                    </label>
                </div>

                <!-- Sección Partes (oculta por defecto) -->
                <div id="seccion-partes" class="mt-3 ms-4" style="display: none;">
                    <div id="cuotas-container">
                        <div class="cuota-item card mb-3 border-start border-4 border-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-title mb-0"><?= __('Cuota #') ?><span class="numero-cuota">1</span></h6>
                                    <button type="button" class="btn btn-sm btn-outline-danger eliminar-cuota-btn">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><?= __('Título Referencial') ?></label>
                                        <input type="text" name="cuotas[0][titulo_referencial]" 
                                               class="form-control" 
                                               placeholder="Ej: Cuota N° 01 (60%)"
                                               value="">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label"><?= __('Monto') ?></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="cuotas[0][monto]" 
                                                   class="form-control monto-input" 
                                                   step="0.01" 
                                                   placeholder="0.00"
                                                   value=""
                                                   data-cuota-index="0">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label"><?= __('Porcentaje') ?></label>
                                        <div class="input-group">
                                            <input type="number" class="form-control porcentaje-input" 
                                                   min="0" max="100" placeholder="0"
                                                   data-cuota-index="0"
                                                   readonly>
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><?= __('Fecha de Recordatorio') ?></label>
                                        <input type="date" name="cuotas[0][fecha_recordatorio]" 
                                               class="form-control" 
                                               value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botón para agregar más cuotas -->
                    <button type="button" id="agregar-cuota-btn" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-plus-circle"></i> <?= __('Agregar Otra Cuota') ?>
                    </button>

                    <!-- Resumen de Cuotas -->
                    <div class="alert alert-light border mt-4">
                        <h6><?= __('Resumen') ?></h6>
                        <p class="mb-1"><?= __('Precio Total del Paquete: ') ?> <strong><span id="precio-total-display">$0.00</span></strong></p>
                        <p class="mb-1"><?= __('Total de Cuotas: ') ?> <strong><span id="total-cuotas">$0.00</span></strong></p>
                        <p class="mb-0">
                            <span id="diferencia-alerta" class="badge bg-warning" style="display: none;">
                                <?= __('⚠️ Diferencia: $0.00') ?>
                            </span>
                            <span id="diferencia-ok" class="badge bg-success" style="display: none;">
                                <?= __('✓ Cuotas correctas') ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-end gap-2">
                <?= $this->Form->button(__('Guardar'), ['type' => 'submit', 'class' => 'btn btn-primary']) ?>
                <?php
                    // Determinar paciente asociado para redirigir al cancelar
                    $pacienteId = null;
                    if (!empty($paquetePago->historia_id) && !empty($historiasClinicas)) {
                        foreach ($historiasClinicas as $h) {
                            if ($h->id == $paquetePago->historia_id) {
                                if (!empty($h->paciente->id)) {
                                    $pacienteId = $h->paciente->id;
                                } elseif (!empty($h->paciente_id)) {
                                    $pacienteId = $h->paciente_id;
                                }
                                break;
                            }
                        }
                    }

                    if ($pacienteId) {
                        echo $this->Html->link(__('Cancelar'), ['controller' => 'Pacientes', 'action' => 'view', $pacienteId], ['class' => 'btn btn-secondary']);
                    } else {
                        echo $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary']);
                    }
                ?>
            </div>
    </div>

    <?= $this->Form->end() ?>
</div>

<script>
let cuotasCount = 0;

// Cambiar tipo de pago
function cambiarTipoPago(tipo) {
    const seccionContado = document.getElementById('seccion-contado');
    const seccionPartes = document.getElementById('seccion-partes');

    if (!seccionContado || !seccionPartes) {
        return;
    }

    if (tipo === 'contado') {
        seccionContado.style.display = 'block';
        seccionPartes.style.display = 'none';
        _toggleCuotasInputs(false);
    } else {
        seccionContado.style.display = 'none';
        seccionPartes.style.display = 'block';
        _toggleCuotasInputs(true);
        actualizarPrecioDisplay();
        actualizarTotales();
    }
}

function _toggleCuotasInputs(enabled) {
    const seccionPartes = document.getElementById('seccion-partes');

    if (!seccionPartes) {
        return;
    }

    seccionPartes.querySelectorAll('input, select, textarea').forEach(el => {
        el.disabled = !enabled;
    });
}

function agregarCuota() {
    const container = document.getElementById('cuotas-container');

    if (!container) {
        return;
    }

    const nuevoIndex = cuotasCount;

    const html = `
        <div class="cuota-item card mb-3 border-start border-4 border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0">Cuota #<span class="numero-cuota">${nuevoIndex + 1}</span></h6>
                    <button type="button" class="btn btn-sm btn-outline-danger eliminar-cuota-btn">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Título Referencial</label>
                        <input
                            type="text"
                            name="cuotas[${nuevoIndex}][titulo_referencial]"
                            class="form-control"
                            placeholder="Ej: Cuota N° ${nuevoIndex + 1}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Monto</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input
                                type="number"
                                name="cuotas[${nuevoIndex}][monto]"
                                class="form-control monto-input"
                                step="0.01"
                                placeholder="0.00"
                                data-cuota-index="${nuevoIndex}">
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Porcentaje</label>
                        <div class="input-group">
                            <input
                                type="number"
                                class="form-control porcentaje-input"
                                readonly
                                data-cuota-index="${nuevoIndex}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Recordatorio</label>
                        <input
                            type="date"
                            name="cuotas[${nuevoIndex}][fecha_recordatorio]"
                            class="form-control">
                    </div>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);

    cuotasCount++;

    actualizarNumerosCuotas();
    actualizarTotales();
}

function eliminarCuotaByElement(btn) {
    const item = btn.closest('.cuota-item');

    if (document.querySelectorAll('.cuota-item').length > 1) {
        item.remove();
        actualizarNumerosCuotas();
        actualizarTotales();
    } else {
        alert('Debe haber al menos una cuota');
    }
}

function actualizarNumerosCuotas() {
    const container = document.getElementById('cuotas-container');

    if (!container) {
        return;
    }

    let idx = 0;

    container.querySelectorAll('.cuota-item').forEach((item, position) => {

        const numero = item.querySelector('.numero-cuota');

        if (numero) {
            numero.textContent = position + 1;
        }

        item.querySelectorAll('input, select').forEach(el => {

            if (el.name && el.name.match(/^cuotas\[\d+\]/)) {
                el.name = el.name.replace(
                    /^cuotas\[\d+\]/,
                    `cuotas[${idx}]`
                );
            }

            if (el.dataset.cuotaIndex !== undefined) {
                el.dataset.cuotaIndex = idx;
            }
        });

        idx++;
    });

    cuotasCount = idx;
}

function actualizarPrecioDisplay() {

    const precioTotalInput =
        document.querySelector('input[name="precio_total"]');

    if (!precioTotalInput) {
        return;
    }

    const precioTotal =
        parseFloat(precioTotalInput.value) || 0;

    const display =
        document.getElementById('precio-total-display');

    if (display) {
        display.textContent = '$' + precioTotal.toFixed(2);
    }
}

function actualizarTotales() {

    const precioTotalInput =
        document.querySelector('input[name="precio_total"]');

    if (!precioTotalInput) {
        return;
    }

    const precioTotal =
        parseFloat(precioTotalInput.value) || 0;

    let totalCuotas = 0;

    const montoInputs =
        document.querySelectorAll('.monto-input');

    const porcentajeInputs =
        document.querySelectorAll('.porcentaje-input');

    montoInputs.forEach((input, index) => {

        const monto =
            parseFloat(input.value) || 0;

        totalCuotas += monto;

        const porcentaje =
            precioTotal > 0
                ? ((monto / precioTotal) * 100).toFixed(2)
                : 0;

        if (porcentajeInputs[index]) {
            porcentajeInputs[index].value = porcentaje;
        }
    });

    const totalCuotasEl =
        document.getElementById('total-cuotas');

    if (totalCuotasEl) {
        totalCuotasEl.textContent =
            '$' + totalCuotas.toFixed(2);
    }

    const precioDisplay =
        document.getElementById('precio-total-display');

    if (precioDisplay) {
        precioDisplay.textContent =
            '$' + precioTotal.toFixed(2);
    }

    const diferencia =
        precioTotal - totalCuotas;

    const diferenciaBadge =
        document.getElementById('diferencia-alerta');

    const diferenciaOk =
        document.getElementById('diferencia-ok');

    if (!diferenciaBadge || !diferenciaOk) {
        return;
    }

    if (Math.abs(diferencia) > 0.01) {

        diferenciaBadge.style.display = 'inline-block';
        diferenciaBadge.innerHTML =
            '⚠️ Diferencia: $' +
            Math.abs(diferencia).toFixed(2);

        diferenciaOk.style.display = 'none';

    } else {

        diferenciaBadge.style.display = 'none';
        diferenciaOk.style.display = 'inline-block';
    }
}

function inicializarPaquetePago() {

    actualizarNumerosCuotas();

    actualizarPrecioDisplay();

    actualizarTotales();

    const tipoSeleccionado =
        document.querySelector(
            'input[name="tipo_pago"]:checked'
        );

    if (tipoSeleccionado) {

        cambiarTipoPago(tipoSeleccionado.value);

        _toggleCuotasInputs(
            tipoSeleccionado.value === 'partes'
        );
    }
}

// Eventos delegados (funcionan en modal y página normal)
document.addEventListener('click', function(e) {

    const addBtn =
        e.target.closest('#agregar-cuota-btn');

    if (addBtn) {
        e.preventDefault();
        agregarCuota();
        return;
    }

    const deleteBtn =
        e.target.closest('.eliminar-cuota-btn');

    if (deleteBtn) {
        e.preventDefault();
        eliminarCuotaByElement(deleteBtn);
        return;
    }
});

document.addEventListener('input', function(e) {

    if (e.target.matches('.monto-input')) {
        actualizarTotales();
    }

    if (e.target.matches('.precio-total-input')) {
        actualizarPrecioDisplay();
        actualizarTotales();
    }
});

document.addEventListener('change', function(e) {

    if (e.target.matches('input[name="tipo_pago"]')) {
        cambiarTipoPago(e.target.value);
    }
});

// Evitar que la rueda del mouse modifique inputs numéricos
document.addEventListener('wheel', function (e) {
    if (
        document.activeElement &&
        document.activeElement.type === 'number'
    ) {
        e.preventDefault();
    }
}, { passive: false });

// Inicialización normal
document.addEventListener('DOMContentLoaded', function() {
    inicializarPaquetePago();
});

// AJAX Modal
const paqueteForm =
    document.getElementById('paquete-form');

if (paqueteForm) {

    paqueteForm.addEventListener('submit', async function(e) {

        if (
            this.closest('.modal') ||
            this.dataset.ajax === 'true'
        ) {

            e.preventDefault();

            const url =
                this.action || window.location.href;

            const formData =
                new FormData(this);

            try {

                const response = await fetch(url, {
                    method: this.method || 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data =
                    await response.json();

                if (data.redirectUrl) {
                    window.location.href =
                        data.redirectUrl;
                    return;
                }

            } catch (error) {

                console.error(
                    'Error AJAX:',
                    error
                );
            }

            this.submit();
        }
    });
}
</script>