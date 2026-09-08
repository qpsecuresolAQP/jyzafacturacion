<?php
/**
 * @var \App\View\AppView $this
 * @var array $doctores
 * @var mixed $doctorId
 * @var string $fechaDesde
 * @var string $fechaHasta
 * @var array $comprobantes
 * @var float $totalPendiente
 */
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0">
            <i class="fas fa-check-circle"></i> Registrar Pago a Doctor
        </h3>

        <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Reporte
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Doctor</label>
                    <select name="doctor_id" class="form-select" required>
                        <option value="">-- Seleccionar doctor --</option>
                        <?php foreach ($doctores as $id => $nombre): ?>
                            <option value="<?= $id ?>" <?= ((string)$doctorId === (string)$id) ? 'selected' : '' ?>>
                                <?= h($nombre) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha desde</label>
                    <input type="date" name="fecha_desde" value="<?= h($fechaDesde) ?>" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha hasta</label>
                    <input type="date" name="fecha_hasta" value="<?= h($fechaHasta) ?>" class="form-control">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-info w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Método de Pago</label>
                    <select name="metodo_pago" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($metodosPagoDisponibles as $metodo): ?>
                            <option value="<?= h($metodo) ?>" <?= $metodoPago === $metodo ? 'selected' : '' ?>>
                                <?= h($metodo) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($doctorId)): ?>
        <div class="alert alert-info">
            Selecciona un doctor y un rango de fechas para ver los métodos de pago pendientes de liquidar.
        </div>
    <?php elseif (empty($comprobantes)): ?>
        <div class="alert alert-secondary">
            No hay métodos de pago pendientes de liquidar para este doctor en el rango seleccionado.
        </div>
    <?php else: ?>
        <?= $this->Form->create(null, ['url' => ['action' => 'guardarPago'], 'id' => 'form-registrar-pago']) ?>
            <?= $this->Form->hidden('doctor_id', ['value' => $doctorId]) ?>
            <?= $this->Form->hidden('fecha_desde', ['value' => $fechaDesde]) ?>
            <?= $this->Form->hidden('fecha_hasta', ['value' => $fechaHasta]) ?>

            <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="check-todos">
                    <label class="form-check-label" for="check-todos">Seleccionar todos los métodos de pago</label>
                </div>
                <span class="text-muted"><?= count($comprobantes) ?> comprobante(s) pendiente(s)</span>
            </div>

            <?php foreach ($comprobantes as $comp): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <!-- CONCEPTOS (tratamientos) -->
                        <div class="mb-3">
                            <h6 class="text-muted mb-2"><i class="fas fa-briefcase-medical"></i> Conceptos</h6>
                            <?php if (!empty($comp['conceptos'])): ?>
                                <ul class="list-unstyled mb-0">
                                    <?php foreach ($comp['conceptos'] as $concepto): ?>
                                        <li class="d-flex justify-content-between align-items-center border-bottom py-1">
                                            <span><?= h($concepto['descripcion']) ?> <span class="text-muted">(x<?= number_format((float) $concepto['cantidad'], 0) ?>)</span></span>
                                            <span class="text-end">
                                                <span class="text-muted">S/ <?= number_format((float) $concepto['total'], 2) ?></span>
                                                <span class="text-success ms-2" title="Pago al doctor por este concepto">
                                                    <i class="fas fa-arrow-right small"></i> S/ <?= number_format((float) $concepto['pago_doctor_item'], 2) ?>
                                                </span>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <span class="text-muted">Sin conceptos registrados.</span>
                            <?php endif; ?>

                            <?php if (!empty($comp['distribuciones'])): ?>
                                <div class="mt-2">
                                    <?php foreach ($comp['distribuciones'] as $dist): ?>
                                        <span class="badge bg-<?= $dist['tipo'] === 'LABORATORIO' ? 'warning text-dark' : 'secondary' ?> me-1">
                                            <i class="fas fa-flask"></i>
                                            <?= h($dist['tipo']) ?><?= $dist['laboratorio'] ? ' (' . h($dist['laboratorio']) . ')' : '' ?>:
                                            S/ <?= number_format((float) $dist['monto'], 2) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- CLIENTE -->
                        <div class="mb-2">
                            <i class="fas fa-user text-muted"></i> <strong><?= h($comp['cliente']) ?></strong>
                        </div>

                        <!-- COMPROBANTE Y FECHA -->
                        <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2 pb-2 border-bottom">
                            <div>
                                <span class="fw-bold fs-6"><?= h($comp['comprobante']) ?></span>
                                <span class="text-muted ms-2"><?= h($comp['fecha']->format('Y-m-d H:i')) ?></span>
                                <span class="badge bg-secondary ms-2"><?= h($comp['estado']) ?></span>
                            </div>
                            <div class="fw-bold">
                                Total pendiente: S/ <?= number_format((float) $comp['total_pagar'], 2) ?>
                            </div>
                        </div>

                        <!-- MÉTODOS DE PAGO (uno por línea) -->
                        <h6 class="text-muted mb-2"><i class="fas fa-wallet"></i> Métodos de pago pendientes</h6>
                        <?php foreach ($comp['metodos'] as $mov): ?>
                            <label class="d-flex justify-content-between align-items-center border rounded px-3 py-2 mb-2 metodo-check-label" style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox" class="form-check-input check-movimiento mt-0" name="caja_movimiento_ids[]"
                                        value="<?= (int) $mov['caja_movimiento_id'] ?>"
                                        data-monto="<?= (float) $mov['pago_doctor'] ?>"
                                        data-metodo="<?= h($mov['metodo_pago']) ?>">
                                    <span class="badge bg-info text-dark"><?= h($mov['metodo_pago']) ?></span>
                                    <span class="text-muted">Recibido: S/ <?= number_format((float) $mov['monto_recibido'], 2) ?></span>
                                    <span class="text-muted">| Base doctor: S/ <?= number_format((float) $mov['base_doctor'], 2) ?></span>
                                    <?php if (($mov['modo_pago'] ?? 'PORCENTAJE') === 'FIJO'): ?>
                                        <span class="badge bg-secondary">Tarifa fija</span>
                                    <?php else: ?>
                                        <span class="text-muted">| <?= number_format((float) $mov['porcentaje'], 2) ?>%</span>
                                    <?php endif; ?>
                                </div>
                                <strong class="text-success">S/ <?= number_format((float) $mov['pago_doctor'], 2) ?></strong>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="card mb-3">
                <div class="card-body">
                    <label class="form-label">Observaciones (opcional)</label>
                    <?= $this->Form->control('observaciones', [
                        'type' => 'textarea',
                        'label' => false,
                        'class' => 'form-control',
                        'rows' => 2,
                        'placeholder' => 'Ej: Pago parcial, solo efectivo de este período',
                    ]) ?>
                </div>
            </div>

            <div class="alert alert-warning">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                    <strong>Total por método de pago (seleccionados):</strong>
                </div>
                <div id="totales-por-metodo" class="d-flex flex-wrap gap-2 mb-3">
                    <span class="text-muted">Ningún método seleccionado.</span>
                </div>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 border-top">
                    <div>
                        <strong>Total general:</strong>
                        <span id="total-seleccionado">S/ 0.00</span>
                    </div>
                    <button type="submit" class="btn btn-success" id="btn-guardar-pago" disabled>
                        <i class="fas fa-check-circle"></i> Confirmar Pago
                    </button>
                </div>
            </div>
        <?= $this->Form->end() ?>
    <?php endif; ?>
</div>

<style>
.metodo-check-label:has(input:checked) {
    background-color: #d1e7dd;
    border-color: #0f5132 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkTodos = document.getElementById('check-todos');
    const checksMovimiento = document.querySelectorAll('.check-movimiento');
    const totalSeleccionadoEl = document.getElementById('total-seleccionado');
    const totalesPorMetodoEl = document.getElementById('totales-por-metodo');
    const btnGuardar = document.getElementById('btn-guardar-pago');
    const form = document.getElementById('form-registrar-pago');

    function actualizarTotal() {
        let total = 0;
        let seleccionados = 0;
        const porMetodo = {};

        checksMovimiento.forEach(chk => {
            if (chk.checked) {
                const monto = parseFloat(chk.dataset.monto || 0);
                const metodo = chk.dataset.metodo || 'OTROS';
                total += monto;
                seleccionados++;
                porMetodo[metodo] = (porMetodo[metodo] || 0) + monto;
            }
        });

        totalSeleccionadoEl.textContent = 'S/ ' + total.toFixed(2);
        if (btnGuardar) btnGuardar.disabled = seleccionados === 0;

        if (totalesPorMetodoEl) {
            const metodos = Object.keys(porMetodo);
            if (metodos.length === 0) {
                totalesPorMetodoEl.innerHTML = '<span class="text-muted">Ningún método seleccionado.</span>';
            } else {
                totalesPorMetodoEl.innerHTML = metodos.map(metodo => `
                    <span class="badge bg-info text-dark fs-6 py-2 px-3">
                        ${metodo}: S/ ${porMetodo[metodo].toFixed(2)}
                    </span>
                `).join('');
            }
        }
    }

    if (checkTodos) {
        checkTodos.addEventListener('change', function () {
            checksMovimiento.forEach(chk => { chk.checked = checkTodos.checked; });
            actualizarTotal();
        });
    }

    checksMovimiento.forEach(chk => {
        chk.addEventListener('change', actualizarTotal);
    });

    if (form) {
        form.addEventListener('submit', function (e) {
            const seleccionados = document.querySelectorAll('.check-movimiento:checked').length;
            if (seleccionados === 0) {
                alert('Selecciona al menos un método de pago para registrar el pago.');
                e.preventDefault();
                return false;
            }
            const total = totalSeleccionadoEl.textContent;
            if (!confirm(`¿Confirmas el pago de ${total} por ${seleccionados} método(s) de pago seleccionado(s)?`)) {
                e.preventDefault();
                return false;
            }
            if (btnGuardar) {
                btnGuardar.disabled = true;
                btnGuardar.innerText = 'Guardando...';
            }
        });
    }

    actualizarTotal();
});
</script>
