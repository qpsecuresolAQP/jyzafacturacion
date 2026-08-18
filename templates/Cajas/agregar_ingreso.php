<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Ingreso $ingreso
 * @var \App\Model\Entity\Caja $caja
 */
?>

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- Alerta de permiso -->
            <?php if (!$this->Permisos->tiene('Ingresos', 'add')): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-lock"></i> No tienes permisos para registrar ingresos.
                </div>
            <?php else: ?>
                <?= $this->Form->create($ingreso, ['class' => 'row g-3']) ?>

                <!-- Título -->
                <div class="col-12 mb-4">
                    <h3 class="text-info"><i class="fas fa-money-bill-wave"></i> Registrar Ingreso Externo</h3>
                </div>

                <!-- Monto -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('monto', [
                        'label'    => 'Monto Recibido',
                        'type'     => 'number',
                        'class'    => 'form-control',
                        'step'     => '0.01',
                        'min'      => '0.01',
                        'required' => true
                    ]) ?>
                </div>

                <!-- Descripción -->
                <div class="col-md-12 mb-3">
                    <?= $this->Form->control('descripcion', [
                        'label'    => 'Descripción',
                        'type'     => 'textarea',
                        'class'    => 'form-control',
                        'rows'     => 3,
                        'required' => true
                    ]) ?>
                </div>
                <!-- NUEVO: Método de pago -->
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-semibold">Método de Pago</label>
                    <select name="metodo_pago" class="form-select form-select-lg" required>
                        <option value="EFECTIVO">💵 Efectivo</option>
                        <option value="YAPE">📱 Yape</option>
                        <option value="TARJETA">💳 Tarjeta</option>
                        <option value="TRANSFERENCIA">🏦 Transferencia</option>
                        <option value="PLIN">🧾 Plin</option>
                        <option value="OTROS">📦 Otros</option>
                    </select>
                </div>

                <!-- Botones -->
                <div class="col-12 text-center">
                    <?= $this->Form->button(__('Guardar Ingreso'), ['class' => 'btn btn-info']) ?>
                    <?= $this->Html->link(
                        __('Cancelar'),
                        ['action' => 'view', $caja->id],
                        ['class' => 'btn btn-secondary ms-2']
                    ) ?>
                </div>

                <?= $this->Form->end() ?>
            <?php endif; ?>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    if (!form) return; // por si el usuario no tiene permiso y el form no se renderiza

    const btnSubmit = form.querySelector('button[type="submit"]');
    if (!btnSubmit) return;

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