<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Documento $documento
 */
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card p-4 shadow-none border-0">
            <h4 class="mb-0 text-center"><?= h($documento->descripcion) ?></h4>
        </div>
        <div class="card-body">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th class="text-center ">Historia Clínica</th>
                    <td class="text-center ">
                        <?php if (!empty($documento->historias_clinica) && !empty($documento->historias_clinica->paciente)): ?>
                            <?= $this->Html->link(
                                h($documento->historias_clinica->paciente->nombre),
                                ['controller' => 'Pacientes', 'action' => 'view', $documento->historias_clinica->paciente->id],
                                ['class' => 'text-info']
                            ) ?>
                        <?php else: ?>
                            <span class="text-muted">No asociado</span>
                        <?php endif; ?>
                    </td>
                </tr>

                <tr>
                    <th class="text-center ">Descripción</th>
                    <td class="text-center "><?= h($documento->descripcion) ?></td>
                </tr>

                <tr>
                    <th class="text-center ">Tipo</th>
                    <td class="text-center "><?= h($documento->tipo) ?></td>
                </tr>

                <tr>
                    <th class="text-center ">ID</th>
                    <td class="text-center "><?= $this->Number->format($documento->id) ?></td>
                </tr>

                <tr>
                    <th class="text-center ">Creado</th>
                    <td class="text-center "><?= h($documento->created) ?></td>
                </tr>

                <tr>
                    <th class="text-center ">Modificado</th>
                    <td class="text-center "><?= h($documento->modified) ?></td>
                </tr>
            </tbody>
        </table>

            <!-- Visualización del archivo -->
            <div class="mt-4">
                <h5><?= __('Archivo') ?></h5>
                <div class="border rounded p-3 text-center">
                    <?php
                    $rutaArchivo = $documento->ruta_archivo;
                    $extension = strtolower(pathinfo($rutaArchivo, PATHINFO_EXTENSION));
                    if (in_array($extension, ['png', 'jpg', 'jpeg'])):
                    ?>
                        <img src="<?= $this->Url->build('/' . h($rutaArchivo), ['fullBase' => true]) ?>" 
                             alt="Imagen del archivo" class="img-fluid rounded shadow-sm">
                    <?php elseif ($extension === 'pdf'): ?>
                        <iframe src="<?= $this->Url->build('/' . h($rutaArchivo), ['fullBase' => true]) ?>" 
                                class="w-100 rounded shadow-sm" style="height: 400px;" frameborder="0"></iframe>
                    <?php else: ?>
                        <p><i class="fas fa-file-alt fa-2x text-muted"></i></p>
                        <p class="text-muted">Archivo no visualizable</p>
                        <?= $this->Html->link('Descargar', '/' . h($rutaArchivo), [
                            'download' => true,
                            'class' => 'btn btn-outline-primary'
                        ]) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .table-bordered th, .table-bordered td {
        border: 1px solid #dddddd; /* Color de borde más claro */
    }
</style>