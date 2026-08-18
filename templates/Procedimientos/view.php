<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Procedimiento $procedimiento
 */
?>
<div class="container mt-4 mb-4">
    <div class="row">
        <!-- Encabezado -->
        <div class="col-12 mb-4">
            <h3 class="text-info"><i class="fas fa-eye"></i> Detalle del Procedimiento</h3>
        </div>

        <!-- Información del Procedimiento -->

        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Doctor Responsable</label>
            <div class="form-control">
                <?= $procedimiento->doctore ? h($procedimiento->doctore->nombre . ' ' . $procedimiento->doctore->apellido) : 'No asignado' ?>
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Paciente</label>
            <div class="form-control">
                <?= $procedimiento->historias_clinica && $procedimiento->historias_clinica->paciente
                    ? h($procedimiento->historias_clinica->paciente->nombre . ' ' . $procedimiento->historias_clinica->paciente->apellido)
                    : 'No asignado' ?>
            </div>
        </div>
        
        <div class="col-12 mb-3">
            <label class="form-label fw-bold">Descripción del Procedimiento</label>
            <div class="form-control" style="min-height: 100px;"><?= $this->Text->autoParagraph(h($procedimiento->procedimiento)) ?></div>
        </div>

        <?php if (!empty($procedimiento->documentos_procedimientos)): ?>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-secondary text-dark">
                <tr>
                    <th scope="col" class="text-center">Previsualización</th>
                    <th scope="col" class="text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($procedimiento->documentos_procedimientos as $docProcedimiento): ?>
                    <?php $documento = $docProcedimiento->documento; ?>
                    <?php if ($documento): ?>
                        <tr>
                            <td class="text-center">
                                <?php
                                    $rutaArchivo = '/' . h($documento->ruta_archivo);
                                    $extension = strtolower(pathinfo($documento->ruta_archivo, PATHINFO_EXTENSION));
                                ?>
                                <?php if (in_array($extension, ['png', 'jpg', 'jpeg'])): ?>
                                    <img src="<?= $this->Url->build($rutaArchivo, ['fullBase' => true]) ?>" 
                                         alt="Imagen" class="img-thumbnail border border-light" style="max-width: 100px;">
                                <?php elseif ($extension === 'pdf'): ?>
                                    <iframe src="<?= $this->Url->build($rutaArchivo, ['fullBase' => true]) ?>" 
                                            style="width: 100px; height: 100px;" frameborder="0" class="border border-light"></iframe>
                                <?php else: ?>
                                    <i class="fas fa-file-alt text-secondary fs-4"></i> 
                                    <span class="text-muted">Archivo no visualizable</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?= $this->Html->link(
                                    '<i class="fas fa-eye me-1"></i> Ver',
                                    $rutaArchivo,
                                    [
                                        'escape' => false,
                                        'target' => '_blank',
                                        'class' => 'btn btn-info btn-sm me-1'
                                    ]
                                ) ?>

                                <?= $this->Html->link(
                                    '<i class="fas fa-download me-1"></i> Descargar',
                                    $rutaArchivo,
                                    [
                                        'escape' => false,
                                        'download' => true,
                                        'class' => 'btn btn-success btn-sm'
                                    ]
                                ) ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-secondary mt-3 text-center">
        <i class="fas fa-info-circle me-2"></i>No hay documentos adjuntos.
    </div>
<?php endif; ?>

        <!-- Botones -->
        <div class="col-12 text-center mt-3">
            <?= $this->Html->link('Editar', ['action' => 'edit', $procedimiento->id], ['class' => 'btn btn-primary me-2']) ?>
        </div>
    </div>
</div>