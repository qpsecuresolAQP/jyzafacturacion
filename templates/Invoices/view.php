<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0">
            <i class="fas fa-receipt"></i> Detalle del Comprobante
        </h3>
        <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <!-- DATOS DEL COMPROBANTE -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white fw-bold">
            <i class="fas fa-file-invoice"></i> Información General
        </div>
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <b>Tipo:</b>
                    <?= $invoice->tipo_doc === '01' ? 'Factura' : 'Boleta' ?>
                </div>
                <div class="col-md-4">
                    <b>Serie:</b>
                    <?= h($invoice->serie ?: 'RI') ?>
                </div>
                <div class="col-md-4">
                    <b>Número:</b>
                    <?= h($invoice->correlativo ?: $invoice->id) ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <b>Cliente:</b>
                    <?= h($invoice->cliente_nombre) ?>
                </div>
                <div class="col-md-4">
                    <b>Doc Cliente:</b>
                    <?= h($invoice->cliente_tipo_doc) ?> - <?= h($invoice->cliente_numero) ?>
                </div>
                <div class="col-md-4">
                    <b>Total:</b>
                    <span class="fw-bold">S/ <?= number_format((float)$invoice->total, 2) ?></span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <b>Dirección:</b>
                    <?= h($invoice->cliente_direccion ?: '-') ?>
                </div>
                <div class="col-md-6">
                    <b>Email:</b>
                    <?= h($invoice->cliente_email ?: '-') ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <b>Estado:</b>
                    <?php
                    $color = 'secondary';
                    if ($invoice->estado === 'ACEPTADO') $color = 'success';
                    elseif ($invoice->estado === 'RECHAZADO') $color = 'danger';
                    elseif ($invoice->estado === 'PENDIENTE_RESUMEN') $color = 'warning';
                    elseif ($invoice->estado === 'EN_RESUMEN') $color = 'info';
                    elseif ($invoice->estado === 'RECIBO_INTERNO') $color = 'dark';
                    ?>
                    <span class="badge bg-<?= $color ?>">
                        <?= h($invoice->estado) ?>
                    </span>
                </div>
                <div class="col-md-4">
                    <b>Código SUNAT:</b>
                    <?= h($invoice->codigo_sunat ?: '-') ?>
                </div>
                <div class="col-md-4">
                    <b>Resumen Diario:</b>
                    <?= !empty($invoice->daily_summary_id) ? '#' . h((string)$invoice->daily_summary_id) : '-' ?>
                </div>
            </div>

            <!-- PAGO -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <b>Pago:</b>
                    <?php if (!empty($pagosComprobante) && count($pagosComprobante) > 0): ?>
                        <span class="badge bg-success">PAGADO</span>
                        <div class="mt-2">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Método</th>
                                        <th class="text-end">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pagosComprobante as $movimiento): ?>
                                        <tr>
                                            <td><?= h($movimiento->metodo_pago) ?></td>
                                            <td class="text-end">S/ <?= number_format((float)$movimiento->monto_recibido, 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <span class="badge bg-danger">SIN PAGO</span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($invoice->invoice_distribuciones)): ?>
                <!-- DISTRIBUCIÓN DEL COBRO -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <b>Distribución del Cobro:</b>
                        <div class="mt-2">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Laboratorio</th>
                                        <th class="text-end">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $totalDistribuidoView = 0.0;
                                    foreach ($invoice->invoice_distribuciones as $dist):
                                        $totalDistribuidoView += (float) $dist->monto;
                                    ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-<?= $dist->tipo === 'LABORATORIO' ? 'warning text-dark' : 'secondary' ?>">
                                                    <?= h($dist->tipo) ?>
                                                </span>
                                            </td>
                                            <td><?= h($dist->laboratorio->nombre ?? '-') ?></td>
                                            <td class="text-end">S/ <?= number_format((float) $dist->monto, 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2"><strong>Restante (Doctor/Clínica)</strong></td>
                                        <td class="text-end"><strong>S/ <?= number_format((float) $invoice->total - $totalDistribuidoView, 2) ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($invoice->descripcion_sunat)): ?>
                <div class="alert alert-light border">
                    <b>Descripción SUNAT:</b><br>
                    <?= h($invoice->descripcion_sunat) ?>
                </div>
            <?php endif; ?>

            <?php if ($invoice->tipo_doc === '03' && !empty($invoice->daily_summary_id)): ?>
                <div class="alert alert-info">
                    Esta boleta ya fue asociada al Resumen Diario #<?= h((string)$invoice->daily_summary_id) ?>.
                </div>
            <?php elseif ($invoice->tipo_doc === '03' && $invoice->estado === 'PENDIENTE_RESUMEN'): ?>
                <div class="alert alert-warning">
                    Esta boleta está lista para ser enviada en el Resumen Diario.
                </div>
            <?php endif; ?>

        </div>
    </div>

    <?php if (($invoice->forma_pago ?? null) === 'CREDITO'): ?>
<div class="card card-flush mb-4 border-0 border-top border-5 border-primary">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0 fw-bold text-primary">
            <i class="fas fa-calendar-alt me-2"></i>Cuotas de Crédito
        </h6>
    </div>
    <div class="card-body pt-3 pb-3">
        <?php

        $primeraCuotaPendiente = null;
        foreach ($cuotas as $c) {
            if ($c->estado === 'PENDIENTE') {
                if ($primeraCuotaPendiente === null || $c->numero_cuota < $primeraCuotaPendiente) {
                    $primeraCuotaPendiente = $c->numero_cuota;
                }
            }
        }
        $saldoPendienteTotal = 0.0;
        
        foreach ($cuotas as $c) {
            if ($c->estado === 'PENDIENTE') {
                $saldoPendienteTotal += (float) $c->monto;
            }
        }
        ?>
        <div class="alert alert-primary d-flex justify-content-between align-items-center mb-3">
            <span class="fw-bold">Saldo pendiente total:</span>
            <span class="fw-bold fs-5">S/ <?= number_format($saldoPendienteTotal, 2) ?></span>
        </div>

        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Vencimiento</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Método</th>
                        <th>Pagado el</th>
                        <th class="text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($cuotas as $c): ?>
                    <?php
                        // 1. Obtener 'Y-m-d' de la fecha de vencimiento sin importar el tipo
                       // Reemplazar la sección de obtención de $fechaVencRaw en view.php:
                        if ($c->fecha_vencimiento instanceof \DateTimeInterface) {
                            $fechaVencRaw = $c->fecha_vencimiento->format('Y-m-d');
                            $fechaVenc    = $c->fecha_vencimiento->format('d/m/Y');
                        } else {
                            // Si viene como string string/date, forzar parseo seguro
                            $dObj = new \DateTime((string)$c->fecha_vencimiento, new \DateTimeZone('America/Lima'));
                            $fechaVencRaw = $dObj->format('Y-m-d');
                            $fechaVenc    = $dObj->format('d/m/Y');
                        }

                        // 2. Fecha actual en Lima (YYYY-MM-DD)
                        $hoyStr = (new \DateTime('now', new \DateTimeZone('America/Lima')))->format('Y-m-d');

                        // 3. Evaluaciones exactas
                        $vencida = ($c->estado === 'PENDIENTE' && $fechaVencRaw < $hoyStr);
                        $venceHoy = ($c->estado === 'PENDIENTE' && $fechaVencRaw === $hoyStr);

                        $badgeClass = match ($c->estado) {
                            'PAGADA' => 'bg-success',
                            'ANULADA' => 'bg-secondary',
                            default => $vencida ? 'bg-danger' : ($venceHoy ? 'bg-warning text-dark' : 'bg-light text-dark border'),
                        };
                    ?>
                    <tr>
                        <td><?= h($c->numero_cuota) ?></td>
                        <td>
                            <?= h($fechaVenc) ?>
                            <?php if ($vencida): ?>
                                <span class="badge bg-danger ms-1">Vencida</span>
                            <?php elseif ($venceHoy && $c->estado === 'PENDIENTE'): ?>
                                <span class="badge bg-warning text-dark ms-1">Vence hoy</span>
                            <?php endif; ?>
                        </td>
                        <td>S/ <?= number_format((float)$c->monto, 2) ?></td>
                        <td><span class="badge <?= $badgeClass ?>"><?= h($c->estado) ?></span></td>
                        <td><?= h($c->metodo_pago ?? '-') ?></td>
                        <td>
                            <?= $c->pagado_en instanceof \DateTimeInterface
                                ? h($c->pagado_en->format('d/m/Y H:i'))
                                : '-' ?>
                        </td>
                        <td class="text-end">
                            <?php if ($c->estado === 'PENDIENTE'): ?>
                                <?php if ($c->numero_cuota === $primeraCuotaPendiente): ?>
                                    <button type="button"
                                            class="btn btn-sm btn-success btn-pagar-cuota"
                                            data-cuota-id="<?= h($c->id) ?>"
                                            data-cuota-numero="<?= h($c->numero_cuota) ?>"
                                            data-cuota-monto="<?= number_format((float)$c->monto, 2, '.', '') ?>">
                                        <i class="fas fa-money-bill-wave me-1"></i>Pagar
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-secondary" disabled
                                            title="Debes pagar primero la cuota N° <?= h($primeraCuotaPendiente) ?>">
                                        <i class="fas fa-lock me-1"></i>Pagar
                                    </button>
                                <?php endif; ?>
                            <?php elseif ($c->estado === 'PAGADA'): ?>
                                <a href="<?= $this->Url->build(['action' => 'comprobanteCuota', $c->id]) ?>"
                                target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file-pdf me-1"></i>Comprobante
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: Pagar cuota -->
<div class="modal fade" id="modalPagarCuota" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formPagarCuota" method="post" action="">
                <?= $this->Form->hidden('_method', ['value' => 'POST']) ?>
                <div class="modal-header">
                    <h5 class="modal-title">
                        Pagar cuota N° <span id="modal-cuota-numero"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        Monto a cobrar:
                        <strong class="text-success fs-5">S/ <span id="modal-cuota-monto"></span></strong>
                    </p>
                    <label class="form-label fw-semibold">Método de Pago</label>
                    <select name="metodo_pago" class="form-select form-select-solid" required>
                        <option value="">-- Seleccionar --</option>
                        <option value="EFECTIVO">💵 Efectivo</option>
                        <option value="YAPE">📱 Yape</option>
                        <option value="TARJETA">💳 Tarjeta</option>
                        <option value="TRANSFERENCIA">🏦 Transferencia</option>
                        <option value="PLIN">🧾 Plin</option>
                        <option value="OTROS">📦 Otros</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>Confirmar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('modalPagarCuota');
    const modal = new bootstrap.Modal(modalEl);
    const form = document.getElementById('formPagarCuota');
    const numeroSpan = document.getElementById('modal-cuota-numero');
    const montoSpan = document.getElementById('modal-cuota-monto');

    document.querySelectorAll('.btn-pagar-cuota').forEach(btn => {
        btn.addEventListener('click', function () {
            const cuotaId = this.dataset.cuotaId;
            numeroSpan.textContent = this.dataset.cuotaNumero;
            montoSpan.textContent = Number(this.dataset.cuotaMonto).toFixed(2);
            form.action = '<?= $this->Url->build(['action' => 'pagarCuota']) ?>/' + cuotaId;
            modal.show();
        });
    });

    form.addEventListener('submit', function () {
        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Procesando...';
    });
});
</script>
<?php endif; ?>

    <!-- ITEMS -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white fw-bold">
            <i class="fas fa-pills"></i> Items
        </div>
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="bg-info text-white">
                    <tr>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Descuento</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoice->invoice_items as $item): ?>
                        <tr>
                            <td><?= h($item->descripcion) ?></td>
                            <td><?= h($item->cantidad) ?></td>
                            <td>S/ <?= number_format((float)$item->precio_unitario, 2) ?></td>
                            <td>
                                <?php if (!empty($item->descuento) && (float)$item->descuento > 0): ?>
                                    <?php if ($item->descuento_tipo === 'porcentaje'): ?>
                                        <span class="badge bg-warning text-dark">
                                            −<?= number_format((float)$item->descuento, 2) ?>%
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">
                                            −S/ <?= number_format((float)$item->descuento, 2) ?>
                                        </span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>S/ <?= number_format((float)$item->total, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ACCIONES -->
    <div class="d-flex flex-wrap gap-2 justify-content-center">

        <?php if ($invoice->tipo_doc === 'RI' && $invoice->estado === 'RECIBO_INTERNO'): ?>
            <a href="<?= $this->Url->build(['action' => 'transformarABoleta', $invoice->id]) ?>"
               class="btn btn-warning btn-sm openModal">
                <i class="fas fa-exchange-alt"></i> Convertir a Boleta
            </a>
        <?php endif; ?>

        <?php if ($invoice->tipo_doc === '03' && $invoice->estado === 'PENDIENTE_RESUMEN'): ?>
            <a href="<?= $this->Url->build(['action' => 'anularBoletaPendiente', $invoice->id]) ?>"
               class="btn btn-danger btn-sm"
               onclick="return confirm('¿Anular esta boleta antes de enviarla a SUNAT?\n\nTodavía no fue enviada al Resumen Diario, así que no requiere Resumen de Anulación.\nSe registrará el reembolso en Caja.')">
                <i class="fas fa-trash"></i> Anular (aún no enviada a SUNAT)
            </a>
        <?php endif; ?>

        <?php if ($invoice->xml_path): ?>
            <a href="<?= $this->Url->webroot($invoice->xml_path) ?>" target="_blank" class="btn btn-dark btn-sm">
                <i class="fas fa-code"></i> XML
            </a>
        <?php endif; ?>

        <?php if ($invoice->cdr_path): ?>
           <a href="<?= $this->Url->webroot($invoice->cdr_path) ?>" target="_blank" class="btn btn-info ms-2">
                <i class="fas fa-file"></i> CDR
            </a>
        <?php endif; ?>

        <a href="<?= $this->Url->build(['action' => 'pdf', $invoice->id]) ?>"
           target="_blank"
           class="btn btn-danger btn-sm">
            <i class="fas fa-file-pdf"></i> PDF
        </a>

        <a href="<?= $this->Url->build(['action' => 'index']) ?>"
           class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver
        </a>

        <?php if (!empty($invoice->caja_id)): ?>
            <a href="<?= $this->Url->build(['controller' => 'Cajas', 'action' => 'view', $invoice->caja_id]) ?>"
               class="btn btn-outline-primary btn-sm">
                <i class="fas fa-cash-register"></i> Volver a Caja
            </a>
        <?php endif; ?>

    </div>

</div>