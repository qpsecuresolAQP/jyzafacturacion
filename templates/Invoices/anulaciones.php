<div class="container py-4">

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
            </h5>
        </div>

        <div class="card-body">

            <?php if ($boletas->count()): ?>

            <?= $this->Form->create(null) ?>

            <div class="row">

                <?php foreach ($boletas as $b): ?>

                <div class="col-md-6 mb-3">

                    <label class="border rounded p-3 d-block h-100 bg-light shadow-sm"
                           style="cursor:pointer;">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>

                                <div class="fw-bold">
                                    <?= h($b->serie . '-' . $b->correlativo) ?>
                                </div>

                                <div class="small text-muted">
                                    <?= h($b->cliente_nombre) ?>
                                </div>

                            </div>

                            <input type="checkbox"
                                   name="boletas[]"
                                   value="<?= $b->id ?>"
                                   style="width:20px; height:20px;">

                        </div>

                        <hr>

                        <div>
                            <strong>Total:</strong>
                            S/ <?= number_format($b->total, 2) ?>
                        </div>

                        <div class="small text-muted mt-2">
                            <?= $b->created->format('d/m/Y H:i') ?>
                        </div>

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
    <!-- RECIBOS INTERNOS -->
    <div class="card shadow-sm border-0 mt-4">

        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">
                <i class="fas fa-file-alt"></i>
                Recibos Internos anulables
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
                                    RECIBO INTERNO
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
                                onclick="return confirm('¿Seguro de anular este RECIBO INTERNO?\n\nSe registrará automáticamente el reembolso en Caja.')">
                                    <i class="fas fa-trash"></i>
                                    Anular Recibo Interno
                                </a>
                            </div>

                        </div>

                    </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="alert alert-secondary mb-0">
                    No hay Recibos Internos anulables.
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>