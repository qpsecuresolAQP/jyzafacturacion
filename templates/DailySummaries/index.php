<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0">
            <i class="fas fa-calendar-check"></i> Resúmenes Diarios
        </h3>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= $this->Url->build(['controller' => 'Invoices', 'action' => 'index']) ?>"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver a Comprobantes
            </a>
        </div>
    </div>

    <!-- TABLA -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="bg-info text-white">
                    <tr>
                        <th>#ID</th>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Correlativo</th>
                        <th>Empresa</th>
                        <th>Ticket</th>
                        <th>Estado</th>
                        <th>Código SUNAT</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dailySummaries as $ds): ?>
                    <tr>
                        <td><span class="badge bg-secondary">#<?= $ds->id ?></span></td>
                        <td><code><?= h($ds->nombre) ?></code></td>
                        <td><?= $ds->fecha->format('d/m/Y') ?></td>
                        <td><?= h($ds->correlativo) ?></td>
                        <td><?= h($ds->company->razon_social ?? '-') ?></td>
                        <td>
                            <?php if (!empty($ds->ticket)): ?>
                                <small class="text-muted font-monospace"><?= h($ds->ticket) ?></small>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $colorDs = 'secondary';
                            if ($ds->estado === 'ACEPTADO')           $colorDs = 'success';
                            elseif ($ds->estado === 'RECHAZADO')      $colorDs = 'danger';
                            elseif ($ds->estado === 'ENVIADO')        $colorDs = 'info';
                            elseif ($ds->estado === 'GENERADO')       $colorDs = 'warning';
                            elseif ($ds->estado === 'PENDIENTE_REINTENTO') $colorDs = 'warning';
                            ?>
                            <span class="badge bg-<?= $colorDs ?>">
                                <?= h($ds->estado) ?>
                            </span>
                        </td>
                        <td><?= h($ds->codigo_sunat ?: '-') ?></td>
                        <td class="text-center">
                            <a href="<?= $this->Url->build(['action' => 'view', $ds->id]) ?>"
                               class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAGINADOR -->
    <div class="paginator mt-3">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('Primero')) ?>
            <?= $this->Paginator->prev('< ' . __('Anterior')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('Siguiente') . ' >') ?>
            <?= $this->Paginator->last(__('Último') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registro(s) de {{count}}')) ?></p>
    </div>

</div>