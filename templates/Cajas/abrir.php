<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

        <h3 class="fw-bold mb-0">
            <i class="fas fa-cash-register"></i>
            Abrir Caja
        </h3>

        <a href="<?= $this->Url->build(['action' => 'index']) ?>"
           class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>

    </div>

    <div class="card shadow-sm border-0">

        <div class="card-header bg-white fw-bold">
            Datos para apertura
        </div>

        <div class="card-body">
            <div class="card-body">

    <?php if (!empty($cajaAbierta)): ?>

        <div class="alert alert-danger shadow-sm">

            <h5 class="fw-bold mb-2">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Ya existe una caja abierta
            </h5>

            <div>

                La caja
                <strong>
                    <?= h($cajaAbierta->nombre) ?>
                </strong>
                actualmente se encuentra abierta.

            </div>

        </div>

    <?php endif; ?>

    <?= $this->Form->create(null, [
        'class' => 'row g-3'
    ]) ?>

            <?= $this->Form->create(null, [
                'class' => 'row g-3'
            ]) ?>

            <!-- ALERTA -->
            <div class="col-12">

                <div class="alert alert-light border">

                    Selecciona la caja física y cuenta el dinero inicial disponible.

                </div>

            </div>

            <!-- CAJA -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Caja física
                </label>

                <select class="form-select"
                        id="selector_caja"
                        required>

                    <option value="">
                        Seleccione
                    </option>

                    <?php foreach ($cajasFisicas as $nombre => $codigo): ?>

                        <option
                            value="<?= h($codigo) ?>"
                            data-nombre="<?= h($nombre) ?>"
                            <?= ($cajaFisicaUnica === $codigo) ? 'selected' : '' ?>
                        >
                            <?= h($nombre) ?> - <?= h($codigo) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <!-- NOMBRE -->
            <div class="col-md-3">

                <label class="form-label fw-semibold">
                    Nombre caja
                </label>

                <input
                    type="text"
                    name="nombre"
                    id="nombre"
                    class="form-control bg-light"
                    readonly
                    required
                >

            </div>

            <!-- CODIGO -->
            <div class="col-md-3">

                <label class="form-label fw-semibold">
                    Código caja
                </label>

                <input
                    type="text"
                    name="codigo"
                    id="codigo"
                    class="form-control bg-light"
                    readonly
                    required
                >

            </div>

            <!-- USUARIO -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Usuario responsable
                </label>

                <select name="user_id"
                        class="form-select"
                        required>

                    <option value="">
                        Seleccione
                    </option>

                    <?php foreach ($usuarios as $id => $nombre): ?>

                        <option value="<?= $id ?>"
                                <?= ($userIdActual === $id) ? 'selected' : '' ?>>
                            <?= h($nombre) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <!-- TABLA DINERO -->
            <div class="col-12">

                <div class="card border shadow-sm">

                    <div class="card-header bg-light fw-bold">
                        💵 Dinero inicial en caja
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered align-middle text-center">

                                <thead class="table-light">

                                    <tr>
                                        <th>Tipo</th>
                                        <th>Valor</th>
                                        <th width="150">Cantidad</th>
                                        <th>Subtotal</th>
                                    </tr>

                                </thead>

                                <tbody>

                                <?php foreach ($denominaciones as $d): ?>

                                    <tr>

                                        <td>
                                            <?= h($d->tipo) ?>
                                        </td>

                                        <td>
                                            S/ <?= number_format($d->valor, 2) ?>
                                        </td>

                                        <td>

                                            <input
                                                type="number"
                                                min="0"
                                                value="0"
                                                class="form-control cantidad-denominacion text-center"
                                                data-valor="<?= $d->valor ?>"
                                                name="denominaciones[<?= $d->id ?>]"
                                            >

                                        </td>

                                        <td>

                                            S/
                                            <span class="subtotal">
                                                0.00
                                            </span>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                                </tbody>

                                <tfoot>

                                    <tr class="table-info fw-bold">

                                        <td colspan="3" class="text-end">
                                            TOTAL APERTURA
                                        </td>

                                        <td>
                                            S/
                                            <span id="total_apertura">
                                                0.00
                                            </span>
                                        </td>

                                    </tr>

                                </tfoot>

                            </table>

                        </div>

                        <!-- MONTO REAL -->
                        <input
                            type="hidden"
                            name="monto_inicial"
                            id="monto_inicial_hidden"
                        >

                    </div>

                </div>

            </div>

            <!-- OBS -->
            <div class="col-md-6">

                <label class="form-label fw-semibold">
                    Observación
                </label>

                <input
                    type="text"
                    name="observacion"
                    class="form-control"
                    placeholder="Opcional"
                >

            </div>

            <!-- BOTONES -->
            <div class="col-12 text-center mt-4">

                <?php if (empty($cajaAbierta)): ?>

    <button class="btn btn-success px-5">

        <i class="fas fa-check-circle"></i>

        Abrir Caja

    </button>

<?php else: ?>

    <button
        class="btn btn-secondary px-5"
        disabled
    >

        <i class="fas fa-lock"></i>

        Caja ya abierta

    </button>

<?php endif; ?>

                <a href="<?= $this->Url->build(['action' => 'index']) ?>"
                   class="btn btn-outline-secondary ms-2 px-4">

                    Cancelar

                </a>

            </div>

            <?= $this->Form->end() ?>

        </div>

    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded', function () {

    // =========================
    // SELECTOR CAJA
    // =========================

    const selector = document.getElementById('selector_caja');

    const nombre = document.getElementById('nombre');

    const codigo = document.getElementById('codigo');

    selector.addEventListener('change', function () {

        const selected = selector.options[
            selector.selectedIndex
        ];

        nombre.value = selected.dataset.nombre || '';

        codigo.value = selected.value || '';
    });

    // Si hay caja preseleccionada, llenar los campos automáticamente
    if (selector.value) {
        selector.dispatchEvent(new Event('change'));
    }

    // =========================
    // CALCULO DINERO
    // =========================

    const inputs = document.querySelectorAll(
        '.cantidad-denominacion'
    );

    const totalSpan = document.getElementById(
        'total_apertura'
    );

    const hidden = document.getElementById(
        'monto_inicial_hidden'
    );

    function calcularTotal() {

        let total = 0;

        inputs.forEach(input => {

            const valor = parseFloat(
                input.dataset.valor || 0
            );

            const cantidad = parseInt(
                input.value || 0
            );

            const subtotal = valor * cantidad;

            input.closest('tr')
                .querySelector('.subtotal')
                .textContent = subtotal.toFixed(2);

            total += subtotal;
        });

        totalSpan.textContent = total.toFixed(2);

        hidden.value = total.toFixed(2);
    }

    inputs.forEach(input => {

        input.addEventListener(
            'input',
            calcularTotal
        );

    });

    calcularTotal();

});

</script>