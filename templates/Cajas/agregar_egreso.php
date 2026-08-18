<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">

            <?php if (!empty($montoPrelleno)): ?>
            <div class="alert alert-warning border-2 mb-4">
                <i class="fas fa-undo me-2"></i>
                <strong>Reembolso por anulación</strong><br>
                Se registrará un egreso de <strong>S/ <?= number_format($montoPrelleno, 2) ?></strong>.
                Selecciona el método por el que se devuelve el dinero al cliente.
            </div>
            <?php endif; ?>

            <?= $this->Form->create($egreso, ['class' => 'row g-3']) ?>

            <div class="col-12 mb-4">
                <h3 class="text-info">
                    <i class="fas fa-money-bill-wave"></i> Registrar Egreso
                </h3>
                <p class="text-muted small">Caja: <strong><?= h($caja->nombre) ?></strong></p>
            </div>

            <!-- Monto -->
            <div class="col-md-12 mb-3">
                <label class="form-label fw-semibold">Monto</label>
                <div class="input-group">
                    <span class="input-group-text">S/</span>
                    <input type="number" name="monto" step="0.01" min="0.01"
                           class="form-control form-control-lg"
                           value="<?= $montoPrelleno > 0 ? number_format($montoPrelleno, 2, '.', '') : '' ?>"
                           required>
                </div>
            </div>

            <!-- Método de reembolso -->
            <div class="col-md-12 mb-3">
                <label class="form-label fw-semibold">Método de Reembolso</label>
                <select name="metodo_pago" class="form-select form-select-lg" required>
                    <option value="EFECTIVO">💵 Efectivo</option>
                    <option value="YAPE">📱 Yape</option>
                    <option value="TARJETA">💳 Tarjeta</option>
                    <option value="TRANSFERENCIA">🏦 Transferencia</option>
                    <option value="PLIN">🧾 Plin</option>
                    <option value="OTROS">📦 Otros</option>
                </select>
            </div>

            <!-- Descripción -->
            <div class="col-md-12 mb-3">
                <label class="form-label fw-semibold">Motivo / Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3" required><?= h($descripcionPrelleno) ?></textarea>
            </div>

            <!-- Botones -->
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-danger btn-lg px-4">
                    <i class="fas fa-save me-2"></i>Registrar Egreso
                </button>
                <?= $this->Html->link('Cancelar', ['action' => 'view', $caja->id], ['class' => 'btn btn-secondary btn-lg ms-2']) ?>
            </div>

            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const btnSubmit = form.querySelector('button[type="submit"]');
    let isSubmitting = false;

    form.addEventListener('submit', function (e) {
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }

        if (!form.checkValidity()) {
            return; // deja que el navegador muestre los campos requeridos
        }

        isSubmitting = true;
        btnSubmit.disabled = true;
        btnSubmit.dataset.originalText = btnSubmit.innerHTML;
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
    });

    // Si el usuario vuelve con el botón "Atrás" del navegador, reactivar
    window.addEventListener('pageshow', function () {
        isSubmitting = false;
        btnSubmit.disabled = false;
        if (btnSubmit.dataset.originalText) {
            btnSubmit.innerHTML = btnSubmit.dataset.originalText;
        }
    });
});
</script>