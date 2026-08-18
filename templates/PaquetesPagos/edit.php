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
            <h1><?= __('Editar Paquete de Pago') ?></h1>
        </div>
    </div>

    <?= $this->Form->create($paquetePago, ['class' => 'form-horizontal', 'id' => 'paquete-form', 'data-ajax' => 'true']) ?>
    
    <div class="card">
        <div class="card-body">
            <!-- Información del Paquete -->
            <h5 class="card-title mb-4"><?= __('Información del Paquete') ?></h5>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('historia_id', [
                        'type' => 'select',
                        'options' => collection($historiasClinicas)->combine('id', function($h) {
                            return (!empty($h->paciente)) 
                                ? h($h->paciente->nombre . ' ' . $h->paciente->apellido)
                                : 'Sin paciente asignado';
                        })->toArray(),
                        'empty' => '-- Seleccionar Historia Clínica --',
                        'class' => 'form-select',
                        'label' => 'Historia Clínica'
                    ]) ?>
                </div>
                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('nombre', [
                        'type' => 'text',
                        'class' => 'form-control',
                        'label' => 'Nombre del Paquete'
                    ]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('descripcion', [
                        'type' => 'textarea',
                        'rows' => 3,
                        'class' => 'form-control',
                        'label' => 'Descripción'
                    ]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('num_sesiones', [
                        'type' => 'number',
                        'min' => 1,
                        'class' => 'form-control',
                        'label' => 'Número de Sesiones'
                    ]) ?>
                </div>
                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('precio_total', [
                        'type' => 'number',
                        'step' => '0.01',
                        'class' => 'form-control',
                        'label' => 'Precio Total'
                    ]) ?>
                </div>
            </div>

            <!-- Forma de Pago (Editable) -->
            <hr class="my-4">
            <h5 class="card-title mb-4"><?= __('Forma de Pago') ?></h5>
            <br>
            <br>

            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_pago" id="pago_contado" 
                           value="contado" <?= $paquetePago->tipo_pago === 'contado' ? 'checked' : '' ?> onchange="cambiarTipoPago('contado')">
                    <label class="form-check-label" for="pago_contado">
                        <strong><?= __('Al Contado') ?></strong> (Pago único 100% del paquete)
                    </label>
                </div>

                <!-- Sección Contado -->
                <div id="seccion-contado" class="mt-3 ms-4" <?= $paquetePago->tipo_pago !== 'contado' ? 'style="display: none;"' : '' ?>>
                    <div class="mb-3" style="max-width: 400px;">
                        <label class="form-label" for="fecha_recordatorio"><?= __('Fecha del Recordatorio') ?></label>
                        <input type="date" id="fecha_recordatorio" name="fecha_recordatorio" 
                               class="form-control" 
                               value="<?= $paquetePago->fecha_recordatorio ? (is_object($paquetePago->fecha_recordatorio) ? $paquetePago->fecha_recordatorio->format('Y-m-d') : $paquetePago->fecha_recordatorio) : '' ?>">
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_pago" id="pago_partes" 
                           value="partes" <?= $paquetePago->tipo_pago === 'partes' ? 'checked' : '' ?> onchange="cambiarTipoPago('partes')">
                    <label class="form-check-label" for="pago_partes">
                        <strong><?= __('En Partes') ?></strong> (Múltiples cuotas)
                    </label>
                </div>

                <!-- Sección Partes -->
                <div id="seccion-partes" class="mt-3 ms-4" <?= $paquetePago->tipo_pago !== 'partes' ? 'style="display: none;"' : '' ?>>
                    <div id="cuotas-container">
                        <?php if (!empty($paquetePago->paquetes_pagos_cuotas)): ?>
                            <?php $cuotaIdx = 0; foreach ($paquetePago->paquetes_pagos_cuotas as $cuota): ?>
                                <div class="cuota-item card mb-3 border-start border-4 border-info">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="card-title mb-0"><?= __('Cuota #') ?><span class="numero-cuota"><?= $cuotaIdx + 1 ?></span></h6>
                                            <button type="button" class="btn btn-sm btn-outline-danger eliminar-cuota-btn">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label"><?= __('Título Referencial') ?></label>
                                                <input type="text" name="cuotas[<?= $cuotaIdx ?>][titulo_referencial]" 
                                                       class="form-control" 
                                                       placeholder="Ej: Cuota N° <?= $cuotaIdx + 1 ?>"
                                                       value="<?= h($cuota->titulo_referencial ?? '') ?>">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label"><?= __('Monto') ?></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" name="cuotas[<?= $cuotaIdx ?>][monto]" 
                                                           class="form-control monto-input" 
                                                           step="0.01" 
                                                           placeholder="0.00"
                                                           value="<?= h($cuota->monto ?? '') ?>"
                                                           data-cuota-index="<?= $cuotaIdx ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label"><?= __('Porcentaje') ?></label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control porcentaje-input" 
                                                           min="0" max="100" placeholder="0"
                                                           data-cuota-index="<?= $cuotaIdx ?>"
                                                           readonly>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label"><?= __('Fecha de Recordatorio') ?></label>
                                                <input type="date" name="cuotas[<?= $cuotaIdx ?>][fecha_recordatorio]" 
                                                       class="form-control" 
                                                       value="<?= ($cuota->fecha_recordatorio && is_object($cuota->fecha_recordatorio)) ? $cuota->fecha_recordatorio->format('Y-m-d') : h($cuota->fecha_recordatorio ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php $cuotaIdx++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Botón para agregar más cuotas -->
                    <button type="button" id="agregar-cuota-btn" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-plus-circle"></i> <?= __('Agregar Otra Cuota') ?>
                    </button>

                    <!-- Resumen de Cuotas -->
                    <div class="alert alert-light border mt-4">
                        <h6><?= __('Resumen') ?></h6>
                        <p class="mb-1"><?= __('Precio Total del Paquete: ') ?> <strong><span id="precio-total-display">$<?= number_format($paquetePago->precio_total, 2) ?></span></strong></p>
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
            <?= $this->Form->button(__('Guardar Cambios'), ['type' => 'submit', 'class' => 'btn btn-primary']) ?>
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
                    echo $this->Html->link(__('Cancelar'), ['action' => 'view', $paquetePago->id], ['class' => 'btn btn-secondary']);
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
    if (!seccionPartes) return;
    seccionPartes.querySelectorAll('input, select, textarea').forEach(el => {
        el.disabled = !enabled;
    });
}

function agregarCuota() {
    const container = document.getElementById('cuotas-container');
    if (!container) return;

    const nuevoIndex = cuotasCount;

    const html = `
        <div class="cuota-item card mb-3 border-start border-4 border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0"><?= __('Cuota #') ?><span class="numero-cuota">${nuevoIndex + 1}</span></h6>
                    <button type="button" class="btn btn-sm btn-outline-danger eliminar-cuota-btn">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><?= __('Título Referencial') ?></label>
                        <input type="text" name="cuotas[${nuevoIndex}][titulo_referencial]"
                               class="form-control"
                               placeholder="Ej: Cuota N° ${nuevoIndex + 1}"
                               value="">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label"><?= __('Monto') ?></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="cuotas[${nuevoIndex}][monto]"
                                   class="form-control monto-input"
                                   step="0.01"
                                   placeholder="0.00"
                                   value=""
                                   data-cuota-index="${nuevoIndex}">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label"><?= __('Porcentaje') ?></label>
                        <div class="input-group">
                            <input type="number" class="form-control porcentaje-input"
                                   min="0" max="100" placeholder="0"
                                   data-cuota-index="${nuevoIndex}"
                                   readonly>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><?= __('Fecha de Recordatorio') ?></label>
                        <input type="date" name="cuotas[${nuevoIndex}][fecha_recordatorio]"
                               class="form-control"
                               value="">
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

function eliminarCuotaByElement(elimBtn) {
    const item = elimBtn.closest('.cuota-item');
    if (document.querySelectorAll('.cuota-item').length > 1) {
        item.remove();
        actualizarNumerosCuotas();
        actualizarTotales();
    } else {
        alert('<?= __('Debe haber al menos una cuota') ?>');
    }
}

function actualizarNumerosCuotas() {
    const container = document.getElementById('cuotas-container');
    if (!container) return;

    let idx = 0;
    container.querySelectorAll('.cuota-item').forEach((item, position) => {
        const numero = item.querySelector('.numero-cuota');
        if (numero) {
            numero.textContent = position + 1;
        }

        item.querySelectorAll('input, select').forEach(el => {
            if (el.name && el.name.match(/^cuotas\[\d+\]/)) {
                el.name = el.name.replace(/^cuotas\[\d+\]/, `cuotas[${idx}]`);
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
    const precioTotalInput = document.querySelector('input[name="precio_total"]');
    if (!precioTotalInput) return;

    const precioTotal = parseFloat(precioTotalInput.value) || 0;
    const display = document.getElementById('precio-total-display');
    if (display) {
        display.textContent = '$' + precioTotal.toFixed(2);
    }
}

function actualizarTotales() {
    const precioTotalInput = document.querySelector('input[name="precio_total"]');
    if (!precioTotalInput) return;

    const precioTotal = parseFloat(precioTotalInput.value) || 0;
    let totalCuotas = 0;

    const montoInputs = document.querySelectorAll('.monto-input');
    const porcentajeInputs = document.querySelectorAll('.porcentaje-input');

    montoInputs.forEach((input, index) => {
        const monto = parseFloat(input.value) || 0;
        totalCuotas += monto;

        const porcentaje = precioTotal > 0 ? (monto / precioTotal * 100).toFixed(2) : 0;
        if (porcentajeInputs[index]) {
            porcentajeInputs[index].value = porcentaje;
        }
    });

    const totalCuotasEl = document.getElementById('total-cuotas');
    if (totalCuotasEl) {
        totalCuotasEl.textContent = '$' + totalCuotas.toFixed(2);
    }

    const precioDisplay = document.getElementById('precio-total-display');
    if (precioDisplay) {
        precioDisplay.textContent = '$' + precioTotal.toFixed(2);
    }

    const diferencia = precioTotal - totalCuotas;
    const diferenciaBadge = document.getElementById('diferencia-alerta');
    const diferenciaBadgeOk = document.getElementById('diferencia-ok');

    if (!diferenciaBadge || !diferenciaBadgeOk) return;

    if (Math.abs(diferencia) > 0.01) {
        diferenciaBadge.style.display = 'inline-block';
        diferenciaBadge.innerHTML = '⚠️ <?= __('Diferencia: ') ?>$' + Math.abs(diferencia).toFixed(2);
        diferenciaBadgeOk.style.display = 'none';
    } else {
        diferenciaBadge.style.display = 'none';
        diferenciaBadgeOk.style.display = 'inline-block';
    }
}

function inicializarPaquetePago() {
    actualizarNumerosCuotas();
    actualizarPrecioDisplay();
    actualizarTotales();

    const tipoSeleccionado = document.querySelector('input[name="tipo_pago"]:checked');
    if (tipoSeleccionado) {
        cambiarTipoPago(tipoSeleccionado.value);
        _toggleCuotasInputs(tipoSeleccionado.value === 'partes');
    }
}

// Eventos delegados (funcionan en modal y página normal)
document.addEventListener('click', function(e) {
    const addBtn = e.target.closest('#agregar-cuota-btn');
    if (addBtn) {
        e.preventDefault();
        agregarCuota();
        return;
    }

    const elimBtn = e.target.closest('.eliminar-cuota-btn');
    if (elimBtn) {
        e.preventDefault();
        eliminarCuotaByElement(elimBtn);
        return;
    }
});

document.addEventListener('input', function(e) {
    if (e.target.matches('.monto-input')) {
        actualizarTotales();
    }
    if (e.target.matches('input[name="precio_total"]')) {
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
document.addEventListener('wheel', function(e) {
    if (document.activeElement && document.activeElement.type === 'number') {
        e.preventDefault();
    }
}, { passive: false });

// Inicialización normal
document.addEventListener('DOMContentLoaded', function() {
    inicializarPaquetePago();
});

// AJAX Modal
const paqueteEditForm = document.getElementById('paquete-form');
if (paqueteEditForm) {
    paqueteEditForm.addEventListener('submit', async function(e) {
        if (this.closest('.modal') || this.dataset.ajax === 'true') {
            e.preventDefault();
            const url = this.action || window.location.href;
            const formData = new FormData(this);
            try {
                const resp = await fetch(url, {
                    method: this.method || 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await resp.json();
                if (data && data.redirectUrl) {
                    window.location.href = data.redirectUrl;
                    return;
                }
            } catch (err) {
                console.error('Error envío AJAX:', err);
            }
            this.submit();
        }
    });
}
</script>