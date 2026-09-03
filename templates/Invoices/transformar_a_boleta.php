<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Invoice $invoice
 * @var bool $tieneDatosCliente
 */
?>
<div class="container-fluid px-4 py-3">
    <div class="mb-4">
        <h3 class="text-info mb-2"><i class="fas fa-exchange-alt"></i> Convertir Recibo Interno a Boleta</h3>
        <div class="bg-light border rounded p-3 d-flex flex-wrap gap-4">
            <div>
                <div class="text-muted small">Recibo Interno</div>
                <div class="fw-semibold"><?= h($invoice->serie . '-' . $invoice->correlativo) ?></div>
            </div>
            <div>
                <div class="text-muted small">Total</div>
                <div class="fw-semibold">S/ <?= number_format((float)$invoice->total, 2) ?></div>
            </div>
        </div>
        <p class="text-muted small mt-2 mb-0">
            Se emitirá una Boleta con nueva serie/correlativo (B001). No se afecta caja ni stock,
            ya quedaron registrados cuando se cobró el Recibo Interno.
        </p>
    </div>

    <?= $this->Form->create(null) ?>

    <?php $esCEInicial = $invoice->cliente_tipo_doc === '4'; ?>

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label fw-semibold">Tipo de Documento</label>
            <select name="boleta_tipo_doc" id="boleta_tipo_doc" class="form-select">
                <option value="1" <?= !$esCEInicial ? 'selected' : '' ?>>DNI</option>
                <option value="4" <?= $esCEInicial ? 'selected' : '' ?>>Carnet Extranjería</option>
            </select>
        </div>

        <div class="col-md-8">
            <label class="form-label fw-semibold" id="boleta_doc_label"><?= $esCEInicial ? 'Carnet Extranjería' : 'DNI' ?></label>
            <input type="text" name="boleta_dni" id="boleta_dni" class="form-control"
                   maxlength="<?= $esCEInicial ? 12 : 8 ?>"
                   value="<?= h($invoice->cliente_numero) ?>"
                   placeholder="<?= $esCEInicial ? '123456789' : '12345678' ?>" required>
            <div id="doc-help" class="form-text text-danger"></div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-12">
            <label class="form-label fw-semibold">Nombre Completo</label>
            <input type="text" name="boleta_nombre" class="form-control"
                   value="<?= h($invoice->cliente_nombre) ?>" required>
        </div>
    </div>

    <?php if (!$tieneDatosCliente): ?>
        <div class="alert alert-warning mt-3 mb-0">
            Este Recibo Interno no tiene datos de cliente registrados. Complétalos para poder emitir la Boleta.
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <?= $this->Form->button(__('Convertir a Boleta'), ['class' => 'btn btn-info px-4']) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'view', $invoice->id], ['class' => 'btn btn-secondary ms-2 px-4']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipoSelect = document.getElementById('boleta_tipo_doc');
    const docInput = document.getElementById('boleta_dni');
    const docLabel = document.getElementById('boleta_doc_label');
    const docHelp = document.getElementById('doc-help');
    const form = docInput.closest('form');

    function aplicarTipo() {
        const esCE = tipoSelect.value === '4';
        docLabel.textContent = esCE ? 'Carnet Extranjería' : 'DNI';
        docInput.maxLength = esCE ? 12 : 8;
        docInput.placeholder = esCE ? '123456789' : '12345678';
        docHelp.textContent = '';
    }

    tipoSelect.addEventListener('change', aplicarTipo);
    aplicarTipo();

    form.addEventListener('submit', function (e) {
        const tipo = tipoSelect.value;
        const doc = docInput.value.trim();
        const validDni = tipo === '1' && /^\d{8}$/.test(doc);
        const validCE = tipo === '4' && /^[A-Za-z0-9]{9,12}$/.test(doc);

        if (!validDni && !validCE) {
            e.preventDefault();
            docHelp.textContent = tipo === '4'
                ? 'Carnet Extranjería debe tener entre 9 y 12 caracteres alfanuméricos.'
                : 'El DNI debe tener 8 dígitos.';
        }
    });
});
</script>
