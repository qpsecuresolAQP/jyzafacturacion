<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\PagosDoctoresHistorial> $pagosHistorial
 * @var array $doctores
 */
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0">
            <i class="fas fa-history"></i> Historial de Pagos a Doctores
        </h3>

        <div class="d-flex flex-wrap gap-2">
            <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Reporte
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Doctor</label>
                    <select name="doctor_id" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($doctores as $id => $nombre): ?>
                            <option value="<?= $id ?>" <?= ((string)$doctorId === (string)$id) ? 'selected' : '' ?>>
                                <?= h($nombre) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-info w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive card">
        <table class="table table-striped mb-0">
            <thead class="bg-dark text-white">
                <tr>
                    <th>Fecha de Registro</th>
                    <th>Doctor</th>
                    <th>Período Cubierto</th>
                    <th>Comprobantes</th>
                    <th>Monto Pagado</th>
                    <th>Registrado por</th>
                    <th>Observaciones</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pagosHistorial as $pago): ?>
                    <tr>
                        <td><?= h($pago->created?->format('Y-m-d H:i')) ?></td>
                        <td><?= h(trim(($pago->doctore->nombre ?? '') . ' ' . ($pago->doctore->apellido ?? ''))) ?></td>
                        <td><?= h($pago->fecha_desde) ?> a <?= h($pago->fecha_hasta) ?></td>
                        <td><?= (int) $pago->total_comprobantes ?></td>
                        <td><strong>S/ <?= number_format((float) $pago->monto_total, 2) ?></strong></td>
                        <td><?= h($pago->user->username ?? '-') ?></td>
                        <td><?= h($pago->observaciones ?: '-') ?></td>
                        <td class="text-center">
                            <a href="<?= $this->Url->build(['action' => 'historialDetalle', $pago->id]) ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Ver Detalle
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($pagosHistorial)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">No hay pagos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="paginator mt-3">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('Primero')) ?>
            <?= $this->Paginator->prev('< ' . __('Anterior')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('Siguiente') . ' >') ?>
            <?= $this->Paginator->last(__('Último') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registro(s) de un total de {{count}}')) ?></p>
    </div>
</div>
