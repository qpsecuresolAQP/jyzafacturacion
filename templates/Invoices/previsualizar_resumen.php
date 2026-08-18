<!-- templates/Invoices/previsualizar_resumen.php -->
<div class="container py-4" style="max-width:900px">

    <h4 class="fw-bold mb-1">
        <i class="fas fa-paper-plane"></i> Resumen Diario — Confirmación
    </h4>
    <p class="text-muted mb-4">
        Empresa: <strong><?= h($company->razon_social) ?></strong>
    </p>

    <?php if (empty($gruposPorFecha)): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            No hay boletas pendientes de resumen.
        </div>
        <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>

    <?php else: ?>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Hay boletas pendientes agrupadas en <strong><?= count($gruposPorFecha) ?></strong> fecha(s).
            Cada fecha se envía como un resumen independiente a SUNAT y
            <strong>no se puede deshacer</strong>.
        </div>

        <?php foreach ($gruposPorFecha as $fechaKey => $boletasDelDia): ?>
            <?php
                $totalGeneral = 0;
                foreach ($boletasDelDia as $b) {
                    $totalGeneral += (float) $b->total;
                }
            ?>
            <div class="card mb-4">
                <div class="card-header bg-light fw-semibold d-flex justify-content-between align-items-center">
                    <span>
                        🧾 Boletas del <?= h((new \DateTime($fechaKey))->format('d/m/Y')) ?>
                        (<?= count($boletasDelDia) ?>)
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Serie-Correlativo</th>
                                <th>Cliente</th>
                                <th>DNI</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($boletasDelDia as $b): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= $b->id ?></span></td>
                                <td><?= h($b->serie) ?>-<?= str_pad((string)$b->correlativo, 8, '0', STR_PAD_LEFT) ?></td>
                                <td><?= h($b->cliente_nombre) ?></td>
                                <td><?= h($b->cliente_numero) ?></td>
                                <td class="text-end">S/ <?= number_format((float)$b->total, 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total del día:</td>
                                <td class="text-end fw-bold">S/ <?= number_format($totalGeneral, 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <?php if ($fechaKey === $fechaMasAntigua): ?>
                        <a href="<?= $this->Url->build([
                                'action' => 'enviarResumenDiario',
                                '?' => [
                                    'company_id' => $companyId,
                                    'fecha'      => $fechaKey,
                                ]
                            ]) ?>"
                           class="btn btn-success btn-sm confirmar-envio"
                           onclick="this.classList.add('disabled'); this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Enviando...';">
                            <i class="fas fa-paper-plane"></i> Confirmar y Enviar (<?= h((new \DateTime($fechaKey))->format('d/m/Y')) ?>)
                        </a>
                    <?php else: ?>
                        <button type="button" class="btn btn-secondary btn-sm" disabled title="Debes enviar primero el resumen más antiguo">
                            <i class="fas fa-lock"></i> Envía primero el del <?= h((new \DateTime($fechaMasAntigua))->format('d/m/Y')) ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>

    <?php endif; ?>
</div>