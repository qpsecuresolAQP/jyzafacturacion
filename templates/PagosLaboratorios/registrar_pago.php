<?php
/**
 * @var \App\View\AppView $this
 * @var array $laboratorios
 * @var mixed $laboratorioId
 * @var string $fechaDesde
 * @var string $fechaHasta
 * @var array $distribuciones
 * @var float $totalPendiente
 */
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0">
            <i class="fas fa-check-circle"></i> Registrar Pago a Laboratorio
        </h3>

        <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Reporte
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Laboratorio</label>
                    <select name="laboratorio_id" class="form-select" required>
                        <option value="">-- Seleccionar laboratorio --</option>
                        <?php foreach ($laboratorios as $id => $nombre): ?>
                            <option value="<?= $id ?>" <?= ((string)$laboratorioId === (string)$id) ? 'selected' : '' ?>>
                                <?= h($nombre) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha desde</label>
                    <input type="date" name="fecha_desde" value="<?= h($fechaDesde) ?>" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Fecha hasta</label>
                    <input type="date" name="fecha_hasta" value="<?= h($fechaHasta) ?>" class="form-control">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-info w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($laboratorioId)): ?>
        <div class="alert alert-info">
            Selecciona un laboratorio y un rango de fechas para ver las distribuciones pendientes de pagar.
        </div>
    <?php elseif (empty($distribuciones)): ?>
        <div class="alert alert-secondary">
            No hay distribuciones pendientes de pagar para este laboratorio en el rango seleccionado.
        </div>
    <?php else: ?>
        <?= $this->Form->create(null, ['url' => ['action' => 'guardarPago'], 'id' => 'form-registrar-pago']) ?>
            <?= $this->Form->hidden('laboratorio_id', ['value' => $laboratorioId]) ?>
            <?= $this->Form->hidden('fecha_desde', ['value' => $fechaDesde]) ?>
            <?= $this->Form->hidden('fecha_hasta', ['value' => $fechaHasta]) ?>

            <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="check-todos">
                    <label class="form-check-label" for="check-todos">Seleccionar todas las distribuciones</label>
                </div>
                <span class="text-muted"><?= count($distribuciones) ?> distribución(es) pendiente(s)</span>
            </div>

            <div class="card mb-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 align-middle">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th style="width: 40px;"></th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Comprobante</th>
                                    <th>Descripción</th>
                                    <th class="text-end">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($distribuciones as $dist): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input check-distribucion" name="invoice_distribucion_ids[]"
                                                value="<?= (int) $dist['invoice_distribucion_id'] ?>"
                                                data-monto="<?= (float) $dist['monto'] ?>">
                                        </td>
                                        <td><?= h($dist['fecha']->format('Y-m-d H:i')) ?></td>
                                        <td><?= h($dist['cliente']) ?></td>
                                        <td><?= h($dist['comprobante']) ?></td>
                                        <td><?= h($dist['descripcion']) ?></td>
                                        <td class="text-end"><strong>S/ <?= number_format((float) $dist['monto'], 2) ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <label class="form-label">Observaciones (opcional)</label>
                    <?= $this->Form->control('observaciones', [
                        'type' => 'textarea',
                        'label' => false,
                        'class' => 'form-control',
                        'rows' => 2,
                        'placeholder' => 'Ej: Pago parcial de este período',
                    ]) ?>
                </div>
            </div>

            <div class="alert alert-warning">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <strong>Total seleccionado:</strong>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkTodos = document.getElementById('check-todos');
    const checksDistribucion = document.querySelectorAll('.check-distribucion');
    const totalSeleccionadoEl = document.getElementById('total-seleccionado');
    const btnGuardar = document.getElementById('btn-guardar-pago');
    const form = document.getElementById('form-registrar-pago');

    function actualizarTotal() {
        let total = 0;
        let seleccionados = 0;

        checksDistribucion.forEach(chk => {
            if (chk.checked) {
                total += parseFloat(chk.dataset.monto || 0);
                seleccionados++;
            }
        });

        totalSeleccionadoEl.textContent = 'S/ ' + total.toFixed(2);
        if (btnGuardar) btnGuardar.disabled = seleccionados === 0;
    }

    if (checkTodos) {
        checkTodos.addEventListener('change', function () {
            checksDistribucion.forEach(chk => { chk.checked = checkTodos.checked; });
            actualizarTotal();
        });
    }

    checksDistribucion.forEach(chk => {
        chk.addEventListener('change', actualizarTotal);
    });

    if (form) {
        form.addEventListener('submit', function (e) {
            const seleccionados = document.querySelectorAll('.check-distribucion:checked').length;
            if (seleccionados === 0) {
                alert('Selecciona al menos una distribución para registrar el pago.');
                e.preventDefault();
                return false;
            }
            const total = totalSeleccionadoEl.textContent;
            if (!confirm(`¿Confirmas el pago de ${total} por ${seleccionados} comprobante(s) seleccionado(s)?`)) {
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
