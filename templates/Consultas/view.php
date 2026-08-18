<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Consulta $consulta
 */
 header('Content-Type: text/html; charset=utf-8');
?>

<div class="container mt-4">
    <div class="card p-4 shadow-none border-0">
        <!-- Header -->
        <h6 class="card-title mb-3" style="font-size: 0.95rem;">
            <?= h($consulta->motivo) ?>
        </h6>
        <p class="mb-1" style="font-size: 0.85rem;">
            <strong>Consulta #<?= h($consulta->id) ?></strong>
        </p>
        <p class="mb-1" style="font-size: 0.85rem;">
            <strong class="d-md-none">Fec.:</strong><strong class="d-none d-md-inline">Fecha:</strong>
            <?= $consulta->created ? h($consulta->created->format('d-m-Y')) : 'N/A' ?>
        </p>
        <p class="mb-3" style="font-size: 0.85rem;">
            <strong class="d-md-none">Esp.:</strong><strong class="d-none d-md-inline">Especialista:</strong>
            <?= !empty($consulta->doctore)
                ? h($consulta->doctore->nombre . ' ' . $consulta->doctore->apellido)
                : 'Sin doctor' ?>
        </p>

        <!-- Diagn�1�7�1�7stico -->
        <div class="border-top pt-3 mt-3">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Diagnostico</label>
                    <div style="white-space: pre-line; padding: 0.75rem; background-color: #f8f9fa; border-radius: 0.25rem;">
                        <?= h($consulta->diagnostico) ?>
                    </div>
                </div>
            </div>

            <!-- CIEs -->
            <div class="row mt-3">
                <div class="col-md-12">
                    <label class="fw-bold">CIE-10</label>
                    <?php if (!empty($consulta->consultas_cie)) : ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($consulta->consultas_cie as $cie) : ?>
                                <?php
                                    $clave = '';
                                    $descripcion = '';

                                    if (!empty($cie->diagnosticoscie10)) {
                                        $clave = $cie->diagnosticoscie10->clave;
                                        $descripcion = $cie->diagnosticoscie10->descripcion;
                                    } elseif (!empty($cie->categoriascie10)) {
                                        $clave = $cie->categoriascie10->clave;
                                        $descripcion = $cie->categoriascie10->descripcion;
                                    }
                                ?>

                                <?php if ($clave && $descripcion) : ?>
                                    <li class="list-group-item">
                                        <strong><?= h($clave) ?></strong> - <?= h($descripcion) ?>
                                    </li>
                                <?php else : ?>
                                    <li class="list-group-item text-warning">CIE no disponible</li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <p class="text-muted">No hay CIEs registrados.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recetas asociadas -->
            <?php if (!empty($consulta->recetas)): ?>
                <div class="row mt-4 pt-3 border-top">
                    <div class="col-md-12">
                        <h5 class="mb-3"><i class="fas fa-prescription-bottle"></i> Recetas</h5>
                        <?php foreach ($consulta->recetas as $receta): ?>
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-pills me-2"></i><strong>Receta:</strong> <?= h($receta->nombre) ?></h6>
                                </div>
                                <div class="card-body">
                                    <!-- Descripci�1�7�1�7n -->
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label class="fw-bold" style="font-size: 0.9rem;">Descripcion:</label>
                                            <div style="white-space: pre-line; padding: 0.75rem; background-color: #f8f9fa; border-radius: 0.25rem;">
                                                <?= h($receta->descripcion) ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Informaci�1�7�1�7n de la receta -->
                                    <!-- <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label class="fw-bold" style="font-size: 0.85rem;">Tipo Usuario:</label>
                                            <p style="font-size: 0.85rem;"><?= h($receta->tipo_usuario) ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="fw-bold" style="font-size: 0.85rem;">Tipo Atenci�1�7�1�7n:</label>
                                            <p style="font-size: 0.85rem;"><?= h($receta->tipo_atencion) ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="fw-bold" style="font-size: 0.85rem;">Especialidad:</label>
                                            <p style="font-size: 0.85rem;"><?= h($receta->especialidad_medica) ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="fw-bold" style="font-size: 0.85rem;">V�1�7�1�7lido hasta:</label>
                                            <p style="font-size: 0.85rem;"><?= $receta->valido_hasta ? h($receta->valido_hasta) : 'N/A' ?></p>
                                        </div>
                                    </div> -->

                                    <!-- Datos adicionales -->
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label class="fw-bold" style="font-size: 0.85rem;">H. Cl.:</label>
                                            <p style="font-size: 0.85rem;"><?= h($receta->h_cl) ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="fw-bold" style="font-size: 0.85rem;">SIS:</label>
                                            <p style="font-size: 0.85rem;"><?= h($receta->sis) ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="fw-bold" style="font-size: 0.85rem;">Particular:</label>
                                            <p style="font-size: 0.85rem;"><?= h($receta->particular) ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="fw-bold" style="font-size: 0.85rem;">Peso (kg):</label>
                                            <p style="font-size: 0.85rem;"><?= !empty($receta->peso) ? h($receta->peso) : 'N/A' ?></p>
                                        </div>
                                    </div>

                                    <!-- Notas -->
                                    <?php if (!empty($receta->notas)): ?>
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label class="fw-bold" style="font-size: 0.9rem;">Notas Especiales:</label>
                                                <div style="white-space: pre-line; padding: 0.75rem; background-color: #fffacd; border-radius: 0.25rem;">
                                                    <?= h($receta->notas) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Medicamentos -->
                                    <?php if (!empty($receta->recetas_medicamentos)): ?>
                                        <div class="row mt-3 pt-3 border-top">
                                            <div class="col-md-12">
                                                <h6 class="mb-3"><i class="fas fa-pills"></i> Medicamentos Agregados</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover table-bordered align-middle">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Medicamento</th>
                                                                <th>Conc.</th>
                                                                <th>Cant.</th>
                                                                <th>Forma</th>
                                                                <th>Viasa</th>
                                                                <th>Dias</th>
                                                                <th>dosis</th>
                                                                <th>Indicaciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($receta->recetas_medicamentos as $recMed): ?>
                                                                <tr>
                                                                    <td>
                                                                        <?php 
                                                                            // Mostrar código si existe
                                                                            $codigo = $recMed->medicamento ? $recMed->medicamento->codigo : ($recMed->codigo_medicamento ?? '');
                                                                            $nombre = $recMed->medicamento ? $recMed->medicamento->nombre : ($recMed->nombre_medicamento ?? 'N/A');
                                                                        ?>
                                                                        <?php if ($codigo): ?>
                                                                            <strong><?= h($codigo) ?></strong><br>
                                                                        <?php endif; ?>
                                                                        <?= h($nombre) ?>
                                                                    </td>
                                                                    <td><?= h($recMed->concentracion ?? 'N/A') ?></td>
                                                                    <td><?= h($recMed->cantidad) ?></td>
                                                                    <td><?= !empty($recMed->formas_farmaceutica) ? h($recMed->formas_farmaceutica->nombre) : 'N/A' ?></td>
                                                                    <td><?= !empty($recMed->vias_administracion) ? h($recMed->vias_administracion->nombre) : 'N/A' ?></td>
                                                                    <td><?= h($recMed->duracion_dias) ?></td>
                                                                    <td><?= h($recMed->dosis) ?></td>
                                                                    <td style="white-space: pre-line; font-size: 0.85rem;"><?= h($recMed->observaciones) ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="row mt-3 pt-3 border-top">
                                            <div class="col-md-12">
                                                <p class="text-muted text-center"><i class="fas fa-info-circle"></i> No hay medicamentos agregados a esta receta.</p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i><strong>Sin recetas</strong> - No hay recetas asociadas a esta consulta.
                </div>
            <?php endif; ?>
        </div>

        
    </div>
</div>

<style>
    .table-bordered th, .table-bordered td {
        border: 1px solid #dddddd;
    }
</style>