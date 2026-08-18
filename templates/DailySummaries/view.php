<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0">
            <i class="fas fa-calendar-check"></i> Detalle del Resumen Diario
        </h3>
        <a href="<?= $this->Url->build(['action' => 'index']) ?>"
           class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <!-- DATOS DEL RESUMEN -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white fw-bold">
            <i class="fas fa-file-invoice"></i> Información del Resumen
        </div>
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <b>ID:</b> #<?= $summary->id ?>
                </div>
                <div class="col-md-4">
                    <b>Nombre:</b> <code><?= h($summary->nombre) ?></code>
                </div>
                <div class="col-md-4">
                    <b>Correlativo:</b> <?= h($summary->correlativo) ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <b>Fecha:</b> <?= $summary->fecha->format('d/m/Y') ?>
                </div>
                <div class="col-md-4">
                    <b>Empresa:</b> <?= h($summary->company->razon_social ?? '-') ?>
                </div>
                <div class="col-md-4">
                    <b>Creado:</b> <?= $summary->created->format('d/m/Y H:i') ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <b>Estado:</b>
                    <?php
                    $colorS = 'secondary';
                    if ($summary->estado === 'ACEPTADO')                $colorS = 'success';
                    elseif ($summary->estado === 'RECHAZADO')           $colorS = 'danger';
                    elseif ($summary->estado === 'ENVIADO')             $colorS = 'info';
                    elseif ($summary->estado === 'GENERADO')            $colorS = 'warning';
                    elseif ($summary->estado === 'PENDIENTE_REINTENTO') $colorS = 'warning';
                    ?>
                    <span class="badge bg-<?= $colorS ?> fs-6">
                        <?= h($summary->estado) ?>
                    </span>
                </div>
                <div class="col-md-4">
                    <b>Código SUNAT:</b> <?= h($summary->codigo_sunat ?: '-') ?>
                </div>
                <div class="col-md-4">
                    <b>Ticket:</b>
                    <?php if (!empty($summary->ticket)): ?>
                        <span class="font-monospace text-muted small"><?= h($summary->ticket) ?></span>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($summary->descripcion_sunat)): ?>
            <div class="alert alert-light border mt-2">
                <b>Descripción SUNAT:</b> <?= h($summary->descripcion_sunat) ?>
            </div>
            <?php endif; ?>

            <!-- ARCHIVOS XML / CDR -->
            <div class="d-flex gap-2 mt-3">
                <?php if (!empty($summary->xml_path)): ?>
                    <a href="<?= $this->Url->build('/' . ltrim($summary->xml_path, '/')) ?>"
                       target="_blank" class="btn btn-dark btn-sm">
                        <i class="fas fa-code"></i> Descargar XML
                    </a>
                <?php endif; ?>

                <?php if (!empty($summary->cdr_path)): ?>
                    <a href="<?= $this->Url->build('/' . ltrim($summary->cdr_path, '/')) ?>"
                       target="_blank" class="btn btn-secondary btn-sm">
                        <i class="fas fa-file-archive"></i> Descargar CDR
                    </a>
                <?php endif; ?>

                <!-- Botón consultar CDR si está ENVIADO y tiene ticket -->
                <?php if ($summary->estado === 'ENVIADO' && !empty($summary->ticket)): ?>
                    <a href="<?= $this->Url->build(['action' => 'consultarCdr', $summary->id]) ?>"
                       class="btn btn-warning btn-sm"
                       onclick="this.classList.add('disabled'); this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Consultando...';">
                        <i class="fas fa-sync"></i> Consultar CDR en SUNAT
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- BOLETAS INCLUIDAS -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white fw-bold">
            <i class="fas fa-receipt"></i>
            Boletas incluidas
            <span class="badge bg-white text-dark ms-2"><?= count($summary->invoices) ?></span>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#ID</th>
                        <th>Serie-Correlativo</th>
                        <th>Cliente</th>
                        <th>DNI</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">IGV</th>
                        <th class="text-end">Total</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalGeneral = 0;
                    foreach ($summary->invoices as $inv):
                        $totalGeneral += (float)$inv->total;
                        $colorInv = 'secondary';
                        if ($inv->estado === 'ACEPTADO')           $colorInv = 'success';
                        elseif ($inv->estado === 'RECHAZADO')      $colorInv = 'danger';
                        elseif ($inv->estado === 'EN_RESUMEN')     $colorInv = 'info';
                        elseif ($inv->estado === 'PENDIENTE_RESUMEN') $colorInv = 'warning';
                    ?>
                    <tr>
                        <td><span class="badge bg-secondary">#<?= $inv->id ?></span></td>
                        <td>
                            <code><?= h($inv->serie) ?>-<?= str_pad((string)$inv->correlativo, 8, '0', STR_PAD_LEFT) ?></code>
                        </td>
                        <td><?= h($inv->cliente_nombre) ?></td>
                        <td><?= h($inv->cliente_numero) ?></td>
                        <td class="text-end">S/ <?= number_format((float)$inv->subtotal, 2) ?></td>
                        <td class="text-end">S/ <?= number_format((float)$inv->igv, 2) ?></td>
                        <td class="text-end"><strong>S/ <?= number_format((float)$inv->total, 2) ?></strong></td>
                        <td>
                            <span class="badge bg-<?= $colorInv ?>">
                                <?= h($inv->estado) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <!-- Ver boleta -->
                            <a href="<?= $this->Url->build(['controller' => 'Invoices', 'action' => 'view', $inv->id]) ?>"
                               class="btn btn-info btn-sm" title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </a>
                            <!-- XML de la boleta (el del resumen) -->
                            <?php if (!empty($summary->xml_path)): ?>
                                <a href="<?= $this->Url->build('/' . ltrim($summary->xml_path, '/')) ?>"
                                   target="_blank" class="btn btn-dark btn-sm" title="XML del resumen">
                                    <i class="fas fa-code"></i>
                                </a>
                            <?php endif; ?>
                            <!-- CDR de la boleta -->
                            <?php if (!empty($inv->cdr_path)): ?>
                                <a href="<?= $this->Url->build('/' . ltrim($inv->cdr_path, '/')) ?>"
                                   target="_blank" class="btn btn-secondary btn-sm" title="CDR">
                                    <i class="fas fa-file-archive"></i>
                                </a>
                            <?php endif; ?>
                            <!-- PDF -->
                            <a href="<?= $this->Url->build(['controller' => 'Invoices', 'action' => 'pdf', $inv->id]) ?>"
                               target="_blank" class="btn btn-danger btn-sm" title="PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="6" class="text-end fw-bold">Total general:</td>
                        <td class="text-end fw-bold">S/ <?= number_format($totalGeneral, 2) ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- ACCIONES -->
    <div class="d-flex gap-2">
        <a href="<?= $this->Url->build(['action' => 'index']) ?>"
           class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
        <a href="<?= $this->Url->build(['controller' => 'Invoices', 'action' => 'index']) ?>"
           class="btn btn-outline-primary btn-sm">
            <i class="fas fa-receipt"></i> Ir a Comprobantes
        </a>
    </div>

</div>