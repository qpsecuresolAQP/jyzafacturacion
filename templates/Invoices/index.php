<div class="container-fluid py-4">
    <!-- HEADER CON BOTONES -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0">
            <i class="fas fa-receipt"></i> Gestión de Comprobantes
        </h3>

        <div class="d-flex flex-wrap gap-2">
            <a href="<?= $this->Url->build(['controller' => 'DailySummaries', 'action' => 'index']) ?>" class="btn btn-outline-dark">
                <i class="fas fa-calendar-check"></i> Resúmenes
            </a>

            <a href="<?= $this->Url->build(['action' => 'anulaciones']) ?>" class="btn btn-outline-danger">
                <i class="fas fa-ban"></i> Anulaciones
            </a>
            <a href="<?= $this->Url->build(['action' => 'add']) ?>" class="btn btn-info">
                <i class="fas fa-plus-circle"></i> Nuevo Comprobante
            </a>

            <a href="<?= $this->Url->build(['controller' => 'Cajas', 'action' => 'index']) ?>" class="btn btn-outline-primary">
                <i class="fas fa-cash-register"></i> Caja
            </a>

            <a href="<?= $this->Url->build(['controller' => 'PagosDoctores', 'action' => 'index']) ?>" class="btn btn-outline-warning">
                <i class="fas fa-money-bill-wave"></i> Pagos Doc.
            </a>

            <a href="<?= $this->Url->build(['controller' => 'PagosLaboratorios', 'action' => 'index']) ?>" class="btn btn-outline-warning">
                <i class="fas fa-flask"></i> Pagos Lab.
            </a>

            <!-- AHORA (va a la vista de confirmación) -->
<a href="<?= $this->Url->build([
    'action' => 'previsualizarResumen',
    '?' => ['company_id' => 1, 'fecha' => date('Y-m-d')]
]) ?>" class="btn btn-outline-success">
    <i class="fas fa-paper-plane"></i> Resumen Diario
</a>

        </div>
    </div>

    <!-- FILTRO POR TIPO -->
    <div class="mb-3 d-flex gap-2 flex-wrap">
        <a href="<?= $this->Url->build(['action' => 'index']) ?>"
        class="btn btn-sm <?= (!$this->request->getQuery('tipo') && !$this->request->getQuery('credito')) ? 'btn-dark' : 'btn-outline-dark' ?>">
            Todos
        </a>
        <a href="<?= $this->Url->build(['action' => 'index', '?' => ['tipo' => 'RI']]) ?>"
        class="btn btn-sm <?= $this->request->getQuery('tipo') === 'RI' ? 'btn-dark' : 'btn-outline-dark' ?>">
            Recibos Internos
        </a>
        <a href="<?= $this->Url->build(['action' => 'index', '?' => ['tipo' => '03']]) ?>"
        class="btn btn-sm <?= $this->request->getQuery('tipo') === '03' ? 'btn-warning' : 'btn-outline-warning' ?>">
            Boletas
        </a>
        <a href="<?= $this->Url->build(['action' => 'index', '?' => ['tipo' => '01']]) ?>"
        class="btn btn-sm <?= $this->request->getQuery('tipo') === '01' ? 'btn-primary' : 'btn-outline-primary' ?>">
            Facturas
        </a>
        <a href="<?= $this->Url->build(['action' => 'index', '?' => ['credito' => '1']]) ?>"
        class="btn btn-sm <?= $this->request->getQuery('credito') === '1' ? 'btn-danger' : 'btn-outline-danger' ?>">
            <i class="fas fa-clock"></i> Crédito Pendiente
        </a>
        <a href="<?= $this->Url->build(['action' => 'index', '?' => ['sin_doctor' => '1']]) ?>"
        class="btn btn-sm <?= $this->request->getQuery('sin_doctor') === '1' ? 'btn-secondary' : 'btn-outline-secondary' ?>">
            <i class="fas fa-user-md"></i> Sin Doctor Asignado
        </a>
    </div>

    <!-- BUSCADOR POR CLIENTE -->
    <div class="mb-3">
        <?= $this->Form->create(null, ['type' => 'get', 'class' => 'd-flex gap-2 flex-wrap']) ?>
            <div style="max-width: 320px; flex: 1;">
                <?= $this->Form->control('cliente', [
                    'type' => 'text',
                    'label' => false,
                    'value' => $this->request->getQuery('cliente'),
                    'placeholder' => 'Buscar por nombre de cliente...',
                    'class' => 'form-control',
                    'templates' => ['inputContainer' => '{{content}}'],
                ]) ?>
            </div>

            <div style="max-width: 220px; flex: 1;">
                <?= $this->Form->control('documento', [
                    'type' => 'text',
                    'label' => false,
                    'value' => $this->request->getQuery('documento'),
                    'placeholder' => 'Buscar por DNI/RUC...',
                    'class' => 'form-control',
                    'templates' => ['inputContainer' => '{{content}}'],
                ]) ?>
            </div>

            <?php if ($this->request->getQuery('tipo')): ?>
                <?= $this->Form->hidden('tipo', ['value' => $this->request->getQuery('tipo')]) ?>
            <?php endif; ?>
            <?php if ($this->request->getQuery('credito')): ?>
                <?= $this->Form->hidden('credito', ['value' => $this->request->getQuery('credito')]) ?>
            <?php endif; ?>
            <?php if ($this->request->getQuery('sin_doctor')): ?>
                <?= $this->Form->hidden('sin_doctor', ['value' => $this->request->getQuery('sin_doctor')]) ?>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Buscar
            </button>

            <?php if ($this->request->getQuery('cliente') || $this->request->getQuery('documento')): ?>
                <?php
                $paramsSinCliente = array_filter([
                    'tipo' => $this->request->getQuery('tipo'),
                    'credito' => $this->request->getQuery('credito'),
                    'sin_doctor' => $this->request->getQuery('sin_doctor'),
                ]);
                ?>
                <a href="<?= $this->Url->build(['action' => 'index', '?' => $paramsSinCliente]) ?>"
                class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            <?php endif; ?>
        <?= $this->Form->end() ?>
    </div>

    <!-- TABLA DE COMPROBANTES -->
    <div class="table-responsive card">
        <table class="table table-striped mb-0 align-middle">

            <thead class="bg-info text-white">
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Serie</th>
                    <th>Número</th>
                    <th>Tipo</th>
                    <th>Cliente</th>
                    <th>Documento</th>
                    <th>Doctor</th>
                    <th>Total</th>
                    <th>Pago</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($invoices as $inv): ?>
                <?php
                    $esCredito = ($inv->forma_pago ?? 'CONTADO') === 'CREDITO';
                    $saldoPendiente = 0.0;
                    $proximaCuota = null;
                    $cuotaVencidaHoy = false;

                   if ($esCredito && !empty($inv->invoice_cuotas)) {
    $hoy = date('Y-m-d');
    foreach ($inv->invoice_cuotas as $cuota) {
        if ($cuota->estado === 'PENDIENTE') {
            $saldoPendiente += (float) $cuota->monto;
            $fechaCuota = $cuota->fecha_vencimiento_iso; // ← usa el accessor normalizado
            if ($fechaCuota) {
                if ($proximaCuota === null || $fechaCuota < $proximaCuota) {
                    $proximaCuota = $fechaCuota;
                }
                if ($fechaCuota <= $hoy) {
                    $cuotaVencidaHoy = true;
                }
            }
        }
    }
}
                ?>
                <tr class="<?= $cuotaVencidaHoy ? 'table-danger' : '' ?>">

                    <td>
                        <span class="badge bg-secondary">#<?= $inv->id ?></span>
                    </td>

                    <td>
                        <small><?= $inv->created ? $inv->created->format('d/m/Y H:i') : '-' ?></small>
                    </td>

                    <td><?= h($inv->serie ?: 'RI') ?></td>

                    <td><?= h($inv->correlativo ?: $inv->id) ?></td>

                    <td>
                        <?php
                        if ($inv->tipo_doc === '01') {
                            echo '<span class="badge bg-primary">Factura</span>';
                        } elseif ($inv->tipo_doc === '03') {
                            echo '<span class="badge bg-warning text-dark">Boleta</span>';
                        } else {
                            echo '<span class="badge bg-dark">Recibo Interno</span>';
                        }
                        ?>
                    </td>

                    <td><?= h($inv->cliente_nombre) ?></td>

                    <td><?= h($inv->cliente_numero ?: '-') ?></td>

                    <td>
                        <?php if ($inv->doctor_id && $inv->doctore): ?>
                            <span class="badge bg-success-subtle text-success border border-success">
                                <i class="fas fa-user-md"></i> <?= h(trim($inv->doctore->nombre . ' ' . $inv->doctore->apellido)) ?>
                            </span>
                        <?php else: ?>
                            <span class="badge bg-light text-muted border">Sin asignar</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <strong>S/ <?= number_format((float)$inv->total, 2) ?></strong>
                        <?php if (!empty($inv->invoice_distribuciones)): ?>
                            <?php
                            $totLab = 0.0;
                            $totMat = 0.0;
                            foreach ($inv->invoice_distribuciones as $dist) {
                                if ($dist->tipo === 'LABORATORIO') {
                                    $totLab += (float) $dist->monto;
                                } else {
                                    $totMat += (float) $dist->monto;
                                }
                            }
                            ?>
                            <div class="mt-1">
                                <?php if ($totLab > 0): ?>
                                    <span class="badge bg-warning text-dark" title="Distribuido a laboratorio(s)">
                                        <i class="fas fa-flask"></i> Lab: S/ <?= number_format($totLab, 2) ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($totMat > 0): ?>
                                    <span class="badge bg-secondary" title="Distribuido a materiales">
                                        Mat: S/ <?= number_format($totMat, 2) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if ($esCredito): ?>
                            <span class="badge bg-info text-dark">
                                <i class="fas fa-hourglass-half"></i> CRÉDITO
                            </span>
                            <?php if ($saldoPendiente > 0): ?>
                                <div class="mt-1">
                                    <span class="badge <?= $cuotaVencidaHoy ? 'bg-danger' : 'bg-secondary' ?>">
                                        Debe: S/ <?= number_format($saldoPendiente, 2) ?>
                                    </span>
                                    <?php if ($proximaCuota): ?>
                                        <br>
                                        <small class="<?= $cuotaVencidaHoy ? 'text-danger fw-bold' : 'text-muted' ?>">
                                            Vence: <?= (new \DateTime($proximaCuota))->format('d/m/Y') ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="mt-1">
                                    <span class="badge bg-success">Cancelado</span>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle"></i> CONTADO
                            </span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php
                        $color = 'secondary';
                        if ($inv->estado === 'ACEPTADO') $color = 'success';
                        elseif ($inv->estado === 'RECHAZADO') $color = 'danger';
                        elseif ($inv->estado === 'PENDIENTE_RESUMEN') $color = 'warning';
                        elseif ($inv->estado === 'EN_RESUMEN') $color = 'info';
                        elseif ($inv->estado === 'RECIBO_INTERNO') $color = 'dark';
                        ?>
                        <span class="badge bg-<?= $color ?>">
                            <?= h($inv->estado) ?>
                        </span>
                    </td>

                    <td class="text-center">

                        <a href="<?= $this->Url->build(['action' => 'view', $inv->id]) ?>"
                           class="btn btn-info btn-sm">
                           <i class="fas fa-eye"></i>
                        </a>

                        <button type="button"
                                class="btn btn-sm <?= $inv->doctor_id ? 'btn-outline-success' : 'btn-outline-secondary' ?>"
                                title="<?= ($inv->doctor_id && $inv->doctore) ? 'Doctor: ' . h(trim($inv->doctore->nombre . ' ' . $inv->doctore->apellido)) : 'Asignar doctor' ?>"
                                data-toggle="modal"
                                data-target="#modalAsignarDoctor<?= $inv->id ?>">
                           <i class="fas fa-user-md"></i>
                        </button>

                        <?php if ($esCredito && $saldoPendiente > 0): ?>
                            <a href="<?= $this->Url->build(['action' => 'view', $inv->id]) ?>#cuotas"
                               class="btn btn-warning btn-sm"
                               title="Ir a cobrar cuota pendiente">
                               <i class="fas fa-hand-holding-usd"></i> Cobrar
                            </a>
                        <?php endif; ?>

                        <?php if ($inv->xml_path): ?>
                            <a href="<?= $this->Url->build('/' . ltrim($inv->xml_path, '/')) ?>" target="_blank"
                               class="btn btn-secondary btn-sm">
                               <i class="fas fa-code"></i> XML
                            </a>
                        <?php endif; ?>

                        <?php if ($inv->cdr_path): ?>
                            <a href="<?= $this->Url->build('/' . ltrim($inv->cdr_path, '/')) ?>" target="_blank"
                               class="btn btn-secondary btn-sm">
                               <i class="fas fa-file"></i> CDR
                            </a>
                        <?php endif; ?>

                        <a href="<?= $this->Url->build(['action' => 'pdf', $inv->id]) ?>"
                           target="_blank"
                           class="btn btn-danger btn-sm">
                           <i class="fas fa-file-pdf"></i>
                        </a>

                        <?php if ($inv->tipo_doc === 'RI' && $inv->estado === 'RECIBO_INTERNO'): ?>
                            <a href="<?= $this->Url->build(['action' => 'transformarABoleta', $inv->id]) ?>"
                            class="btn btn-warning btn-sm openModal" title="Convertir a Boleta">
                                <i class="fas fa-exchange-alt"></i>
                            </a>
                            <a href="<?= $this->Url->build(['action' => 'anularReciboInterno', $inv->id]) ?>"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('¿Anular Recibo Interno #<?= $inv->correlativo ?>?\n\n⚠️ Recuerda registrar el EGRESO en Caja.')">
                                <i class="fas fa-trash"></i>
                            </a>
                        <?php endif; ?>

                        <?php if ($inv->tipo_doc === '03' && $inv->estado === 'PENDIENTE_RESUMEN'): ?>
                            <a href="<?= $this->Url->build(['action' => 'anularBoletaPendiente', $inv->id]) ?>"
                            class="btn btn-danger btn-sm" title="Anular (aún no enviada a SUNAT)"
                            onclick="return confirm('¿Anular esta boleta antes de enviarla a SUNAT?\n\nTodavía no fue enviada al Resumen Diario.\nSe registrará el reembolso en Caja.')">
                                <i class="fas fa-trash"></i>
                            </a>
                        <?php endif; ?>

                    </td>

                </tr>

                <!-- MODAL: Asignar Doctor -->
                <div class="modal fade" id="modalAsignarDoctor<?= $inv->id ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <?= $this->Form->create(null, [
                                'url' => ['action' => 'asignarDoctor', $inv->id],
                            ]) ?>
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-user-md"></i> Asignar Doctor — Comprobante #<?= $inv->id ?>
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <label class="form-label fw-semibold">Doctor Responsable</label>
                                <select name="doctor_id" class="form-select">
                                    <option value="">-- Sin doctor asignado --</option>
                                    <?php foreach ($doctores as $docId => $docLabel): ?>
                                        <option value="<?= $docId ?>" <?= (int) $inv->doctor_id === (int) $docId ? 'selected' : '' ?>>
                                            <?= h($docLabel) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </div>
                            <?= $this->Form->end() ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </tbody>

        </table>
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
        <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registro(s) de un total de {{count}}')) ?></p>
    </div>
</div>