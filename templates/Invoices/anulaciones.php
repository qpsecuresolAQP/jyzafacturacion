<div class="container py-4">
    <?= $this->Flash->render() ?>

    <div class="mb-4">
        <h2 class="fw-bold text-danger">
            <i class="fas fa-ban"></i>
            Centro de Anulaciones SUNAT
        </h2>

        <div class="alert alert-warning shadow-sm mt-3">
            <strong>Importante:</strong><br>

            • Solo aparecen comprobantes ACEPTADOS.<br>
            • Máximo 24 horas desde emisión.<br>
            • Facturas → Comunicación de Baja (RA).<br>
            • Boletas → Resumen Diario de Anulación.
        </div>
    </div>

    <!-- FACTURAS -->
    <div class="card shadow-sm border-0 mb-5">

        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">
                <i class="fas fa-file-invoice"></i>
                Facturas anulables
            </h5>
        </div>

        <div class="card-body">

            <?php if ($facturas->count()): ?>

                <div class="row">

                    <?php foreach ($facturas as $f): ?>

                    <div class="col-md-6 mb-3">

                        <div class="border rounded p-3 h-100 bg-light">

                            <div class="d-flex justify-content-between">
                                <strong>
                                    <?= h($f->serie . '-' . $f->correlativo) ?>
                                </strong>

                                <span class="badge bg-success">
                                    ACEPTADO
                                </span>
                            </div>

                            <hr>

                            <div>
                                <strong>Cliente:</strong><br>
                                <?= h($f->cliente_nombre) ?>
                            </div>

                            <div class="mt-2">
                                <strong>Total:</strong>
                                S/ <?= number_format($f->total, 2) ?>
                            </div>

                            <div class="mt-2 text-muted small">
                                <?= $f->created->format('d/m/Y H:i') ?>
                            </div>

                            <div class="mt-3">

                                <a href="<?= $this->Url->build([
                                    'action' => 'anular',
                                    $f->id
                                ]) ?>"
                                   class="btn btn-danger w-100"
                                   onclick="return confirm('¿Seguro de anular esta FACTURA en SUNAT?\n\nSe registrará automáticamente el reembolso en Caja.')">

                                    <i class="fas fa-trash"></i>
                                    Anular Factura

                                </a>

                            </div>

                        </div>

                    </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="alert alert-secondary mb-0">
                    No hay facturas anulables.
                </div>

            <?php endif; ?>

        </div>

    </div>

    <!-- BOLETAS -->
    <div class="card shadow-sm border-0">

        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-receipt"></i>
                Boletas para resumen de anulación
                <small class="ml-2">(hasta <?= (int) $diasAnulacion ?> días desde su emisión)</small>
            </h5>
        </div>

        <div class="card-body">

            <div class="alert alert-light border small">
                SUNAT permite anular una boleta hasta <strong><?= (int) $diasAnulacion ?> días calendario</strong> después de emitida.
                Solo se pueden seleccionar boletas <strong>aceptadas</strong>: si su resumen diario aún figura como
                <em>enviado</em>, consulte primero el resumen para confirmar su estado.
                Si selecciona boletas de distintos días, se envía un resumen de anulación por cada día.
            </div>

            <?php if ($boletas->count()): ?>

            <input type="search" id="buscarBoletaAnular" class="form-control mb-3"
                   placeholder="Buscar boleta por número (ej. B002-371) o cliente...">

            <?= $this->Form->create(null) ?>

            <div class="row" id="listaBoletasAnular">

                <?php foreach ($boletas as $b): ?>
                <?php
                $anulable = $b->estado === 'ACEPTADO';
                $venceAnulacion = (clone $b->created)->modify('+' . (int) $diasAnulacion . ' days');
                ?>

                <div class="col-md-6 mb-3 item-boleta-anular"
                     data-buscar="<?= h(mb_strtolower($b->serie . '-' . $b->correlativo . ' ' . $b->cliente_nombre . ' ' . $b->cliente_numero)) ?>">

                    <label class="border rounded p-3 d-block h-100 shadow-sm <?= $anulable ? 'bg-light' : 'bg-white text-muted' ?>"
                           style="cursor:<?= $anulable ? 'pointer' : 'default' ?>;">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>

                                <div class="font-weight-bold">
                                    <?= h($b->serie . '-' . $b->correlativo) ?>
                                    <?php if (!$anulable): ?>
                                        <span class="badge badge-warning ml-1">Resumen sin confirmar</span>
                                    <?php endif; ?>
                                </div>

                                <div class="small text-muted">
                                    <?= h($b->cliente_nombre) ?>
                                </div>

                            </div>

                            <?php if ($anulable): ?>
                                <input type="checkbox"
                                       name="boletas[]"
                                       value="<?= $b->id ?>"
                                       style="width:20px; height:20px;">
                            <?php endif; ?>

                        </div>

                        <hr>

                        <div>
                            <strong>Total:</strong>
                            S/ <?= number_format($b->total, 2) ?>
                        </div>

                        <div class="small text-muted mt-2">
                            Emitida: <?= $b->created->format('d/m/Y H:i') ?>
                            · Anulable hasta: <strong><?= $venceAnulacion->format('d/m/Y') ?></strong>
                        </div>

                        <?php if (!$anulable): ?>
                            <div class="small mt-2">
                                <?php if ($b->daily_summary): ?>
                                    El resumen <?= h($b->daily_summary->nombre) ?> figura como
                                    <strong><?= h($b->daily_summary->estado) ?></strong>.
                                    <?= $this->Html->link(
                                        'Consultar resumen',
                                        ['action' => 'consultarResumen', $b->daily_summary->id],
                                        [
                                            'class' => 'btn btn-sm btn-outline-primary ml-1 btn-consultar-resumen',
                                            'title' => 'Puede tardar hasta medio minuto si SUNAT está lento',
                                        ]
                                    ) ?>
                                <?php else: ?>
                                    Sin resumen asociado.
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    </label>

                </div>

                <?php endforeach; ?>

            </div>

            <div class="alert alert-info mt-3">
                Las boletas seleccionadas serán enviadas
                en un nuevo resumen diario de anulación SUNAT.
                Se registrará automáticamente el reembolso de cada una en Caja.
            </div>

            <button type="submit"
                    id="btnGenerarAnulacion"
                    class="btn btn-primary btn-lg w-100"
                    onclick="return confirm('Seguro de enviar estas BOLETAS para anulación SUNAT?\n\nSe registrará automáticamente el reembolso en Caja por cada una.')">

                <i class="fas fa-paper-plane"></i>
                Generar Resumen de Anulación

            </button>

            <?= $this->Form->end() ?>

            <?php else: ?>

                <div class="alert alert-secondary mb-0">
                    No hay boletas disponibles para anular.
                </div>

            <?php endif; ?>

        </div>

    </div>
    <!-- BOLETAS PENDIENTES (aún no enviadas a SUNAT) -->
    <div class="card shadow-sm border-0 mt-4">

        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="fas fa-clock"></i>
                Boletas pendientes de envío (aún no enviadas a SUNAT)
            </h5>
        </div>

        <div class="card-body">

            <div class="alert alert-info">
                Estas boletas todavía no fueron incluidas en un Resumen Diario, así que SUNAT no sabe que existen.
                Se pueden anular directamente aquí, sin Resumen de Anulación.
            </div>

            <?php if ($boletasPendientes->count()): ?>

                <div class="row">

                    <?php foreach ($boletasPendientes as $bp): ?>

                    <div class="col-md-6 mb-3">

                        <div class="border rounded p-3 h-100 bg-light">

                            <div class="d-flex justify-content-between">
                                <strong>
                                    <?= h($bp->serie . '-' . $bp->correlativo) ?>
                                </strong>

                                <span class="badge bg-warning text-dark">
                                    PENDIENTE DE ENVÍO
                                </span>
                            </div>

                            <hr>

                            <div>
                                <strong>Cliente:</strong><br>
                                <?= h($bp->cliente_nombre) ?>
                            </div>

                            <div class="mt-2">
                                <strong>Total:</strong>
                                S/ <?= number_format($bp->total, 2) ?>
                            </div>

                            <div class="mt-2 text-muted small">
                                <?= $bp->created->format('d/m/Y H:i') ?>
                            </div>

                            <div class="mt-3">

                                <a href="<?= $this->Url->build([
                                    'action' => 'anularBoletaPendiente',
                                    $bp->id
                                ]) ?>"
                                   class="btn btn-danger w-100"
                                   onclick="return confirm('¿Anular esta boleta antes de enviarla a SUNAT?\n\nSe registrará automáticamente el reembolso en Caja.')">

                                    <i class="fas fa-trash"></i>
                                    Anular (aún no enviada)

                                </a>

                            </div>

                        </div>

                    </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="alert alert-secondary mb-0">
                    No hay boletas pendientes de envío.
                </div>

            <?php endif; ?>

        </div>

    </div>

    <!-- RECIBOS INTERNOS -->
    <div class="card shadow-sm border-0 mt-4">

        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">
                <i class="fas fa-file-alt"></i>
                Notas de Venta anulables
            </h5>
        </div>

        <div class="card-body">

            <?php if ($recibos->count()): ?>

                <div class="row">

                    <?php foreach ($recibos as $r): ?>

                    <div class="col-md-6 mb-3">

                        <div class="border rounded p-3 h-100 bg-light">

                            <div class="d-flex justify-content-between">
                                <strong>
                                        <?= h($r->serie . '-' . $r->correlativo) ?>
                                </strong>
                                <span class="badge bg-secondary">
                                    NOTA DE VENTA
                                </span>
                            </div>

                            <hr>

                            <div>
                                <strong>Cliente:</strong><br>
                                <?= h($r->cliente_nombre) ?>
                            </div>

                            <div class="mt-2">
                                <strong>Total:</strong>
                                S/ <?= number_format($r->total, 2) ?>
                            </div>

                            <div class="mt-2 text-muted small">
                                <?= $r->created->format('d/m/Y H:i') ?>
                            </div>

                            <div class="mt-3">
                                <a href="<?= $this->Url->build([
                                    'action' => 'anularReciboInterno',
                                    $r->id
                                ]) ?>"
                                class="btn btn-secondary w-100"
                                onclick="return confirm('¿Seguro de anular esta NOTA DE VENTA?\n\nSe registrará automáticamente el reembolso en Caja.')">
                                    <i class="fas fa-trash"></i>
                                    Anular Nota de Venta
                                </a>
                            </div>

                        </div>

                    </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="alert alert-secondary mb-0">
                    No hay Notas de Venta anulables.
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>
<script>
// Evita reenviar a SUNAT por doble clic y muestra que se está esperando respuesta.
(function () {
    const btn = document.getElementById('btnGenerarAnulacion');
    if (btn && btn.form) {
        btn.form.addEventListener('submit', function () {
            setTimeout(function () {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando a SUNAT, no cierre esta página...';
            }, 0);
        });
    }
    document.querySelectorAll('.btn-consultar-resumen').forEach(function (a) {
        a.addEventListener('click', function (e) {
            if (a.classList.contains('disabled')) { e.preventDefault(); return; }
            a.classList.add('disabled');
            a.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Consultando...';
        });
    });
})();

// Filtra las tarjetas de boletas por número o cliente.
(function () {
    const input = document.getElementById('buscarBoletaAnular');
    if (!input) return;
    input.addEventListener('input', function () {
        const q = input.value.trim().toLowerCase();
        document.querySelectorAll('#listaBoletasAnular .item-boleta-anular').forEach(function (el) {
            el.style.display = !q || el.dataset.buscar.indexOf(q) !== -1 ? '' : 'none';
        });
    });
})();
</script>
