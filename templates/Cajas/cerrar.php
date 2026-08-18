<div class="container py-4">

    <h3 class="mb-4">
        Cerrar Caja
    </h3>

    <div class="alert alert-info">
        Efectivo esperado:
        <strong>
            S/ <?= number_format($esperado, 2) ?>
        </strong>
    </div>

    <?= $this->Form->create() ?>

    <table class="table table-bordered text-center align-middle">

        <thead>
            <tr>
                <th>Tipo</th>
                <th>Valor</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach ($denominaciones as $d): ?>

            <tr>

                <td><?= h($d['tipo']) ?></td>

                <td>
                    S/ <?= number_format($d['valor'], 2) ?>
                </td>

                <td width="150">

                    <input
                        type="number"
                        min="0"
                        value="0"
                        class="form-control cantidad"
                        data-valor="<?= $d['valor'] ?>"
                        name="denominaciones[<?= $d['id'] ?>]"
                    >

                </td>

                <td>
                    S/
                    <span class="subtotal">0.00</span>
                </td>

            </tr>

            <?php endforeach; ?>

        </tbody>

        <tfoot>

            <tr class="table-success fw-bold">

                <td colspan="3" class="text-end">
                    TOTAL REAL EN CAJA
                </td>

                <td>
                    S/ <span id="total">0.00</span>
                </td>

            </tr>

        </tfoot>

    </table>

    <input
        type="hidden"
        name="monto_cierre"
        id="monto_cierre"
    >

    <button class="btn btn-danger">
        Cerrar Caja
    </button>

    <?= $this->Form->end() ?>

</div>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const inputs = document.querySelectorAll('.cantidad');

    const totalSpan = document.getElementById('total');

    const hidden = document.getElementById('monto_cierre');

    function calcular() {

        let total = 0;

        inputs.forEach(input => {

            const valor = parseFloat(input.dataset.valor);

            const cantidad = parseInt(input.value || 0);

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
        input.addEventListener('input', calcular);
    });

    calcular();

});

</script>