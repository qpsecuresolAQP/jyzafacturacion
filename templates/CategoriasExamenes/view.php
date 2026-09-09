<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CategoriasExamene $categoriasExamene
 * @var array $examenesActivos
 * @var array $examenesInactivos
 */
?>
<div class="container-fluid mt-4 mb-4">
    <div class="row">
        <div class="col-12 col-xl-10 offset-xl-1">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3 flex-wrap gap-2">
                <h3 class="text-info mb-0"><i class="fas fa-flask"></i> Categoría: <?= h($categoriasExamene->nombre) ?></h3>

                <?php if ($this->Permisos->tiene('Examenes', 'add')): ?>
                    <?= $this->Html->link(
                        '<i class="fas fa-plus"></i> Añadir Examen',
                        [
                            'controller' => 'Examenes',
                            'action' => 'add',
                            '?' => ['categoria_examen_id' => $categoriasExamene->id],
                        ],
                        ['escape' => false, 'class' => 'btn btn-info btn-sm openModal']
                    ) ?>
                <?php endif; ?>
            </div>

            <div class="row mb-4">
                <div class="col-md-3"><strong>Estado:</strong></div>
                <div class="col-md-9">
                    <span class="badge bg-<?= $categoriasExamene->estado ? 'success' : 'secondary' ?>">
                        <?= $categoriasExamene->estado ? 'Activo' : 'Inactivo' ?>
                    </span>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                <h5 class="text-info mb-0">Exámenes en esta categoría</h5>
                <?php if (!empty($examenesActivos)): ?>
                    <div class="d-flex gap-2">
                        <input type="text" id="buscarExamenCategoria" class="form-control form-control-sm" placeholder="Buscar por nombre o muestra..." style="min-width: 240px;">
                        <button type="button" id="limpiarBuscarExamenCategoria" class="btn btn-sm btn-outline-secondary" title="Limpiar búsqueda">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            <div class="table-responsive">
                <table class="table table-striped mt-2" style="table-layout: fixed; width: 100%; min-width: 760px;">
                    <colgroup>
                        <col style="width: 28%;">
                        <col style="width: 17%;">
                        <col style="width: 13%;">
                        <col style="width: 15%;">
                        <col style="width: 10%;">
                        <col style="width: 17%;">
                    </colgroup>
                    <thead class="bg-info text-white">
                        <tr>
                            <th>Nombre</th>
                            <th>Muestra</th>
                            <th>Precio (S/)</th>
                            <th>Precio Convenio (S/)</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaExamenesCategoria">
                        <?php if (empty($examenesActivos)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No hay exámenes activos en esta categoría.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($examenesActivos as $examen): ?>
                                <tr data-nombre="<?= h(mb_strtolower($examen->nombre)) ?>" data-muestra="<?= h(mb_strtolower((string)$examen->muestra)) ?>">
                                    <td class="text-truncate" style="max-width: 0;" title="<?= h($examen->nombre) ?>"><?= h($examen->nombre) ?></td>
                                    <td class="text-truncate" style="max-width: 0;"><?= h($examen->muestra ?: '-') ?></td>
                                    <td><?= number_format((float)$examen->precio, 2) ?></td>
                                    <td><?= number_format((float)$examen->precio_convenio, 2) ?></td>
                                    <td>
                                        <span class="badge badge-success">Activo</span>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <?php if ($this->Permisos->tiene('Examenes', 'edit')): ?>
                                            <?= $this->Html->link(
                                                '<i class="fas fa-edit"></i>',
                                                ['controller' => 'Examenes', 'action' => 'edit', $examen->id],
                                                ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm openModal']
                                            ) ?>
                                        <?php endif; ?>
                                        <?php if ($this->Permisos->tiene('Examenes', 'delete')): ?>
                                            <?= $this->Form->postLink(
                                                '<i class="fas fa-trash"></i>',
                                                ['controller' => 'Examenes', 'action' => 'delete', $examen->id],
                                                ['escape' => false, 'title' => 'Eliminar', 'class' => 'btn btn-danger btn-sm', 'confirm' => '¿Estás seguro? El examen "' . $examen->nombre . '" será eliminado de esta categoría.']
                                            ) ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <p id="sinResultadosExamenCategoria" class="text-center text-muted py-3" hidden>Ningún examen coincide con la búsqueda.</p>
            </div>

            <?php if (!empty($examenesInactivos)): ?>
                <div class="mt-4">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="collapse" data-target="#examenesEliminados" aria-expanded="false">
                        <i class="fas fa-trash-restore"></i> Ver exámenes eliminados (<?= count($examenesInactivos) ?>)
                    </button>
                    <div class="collapse mt-2" id="examenesEliminados">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped" style="table-layout: fixed; width: 100%; min-width: 640px;">
                                <colgroup>
                                    <col style="width: 35%;">
                                    <col style="width: 15%;">
                                    <col style="width: 15%;">
                                    <col style="width: 15%;">
                                    <col style="width: 20%;">
                                </colgroup>
                                <thead class="bg-secondary text-white">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Muestra</th>
                                        <th>Precio (S/)</th>
                                        <th>Precio Convenio (S/)</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($examenesInactivos as $examen): ?>
                                        <tr>
                                            <td class="text-truncate" style="max-width: 0;" title="<?= h($examen->nombre) ?>"><?= h($examen->nombre) ?></td>
                                            <td class="text-truncate" style="max-width: 0;"><?= h($examen->muestra ?: '-') ?></td>
                                            <td><?= number_format((float)$examen->precio, 2) ?></td>
                                            <td><?= number_format((float)$examen->precio_convenio, 2) ?></td>
                                            <td class="text-center text-nowrap">
                                                <?php if ($this->Permisos->tiene('Examenes', 'reactivar')): ?>
                                                    <?= $this->Form->postLink(
                                                        '<i class="fas fa-undo"></i> Reactivar',
                                                        ['controller' => 'Examenes', 'action' => 'reactivar', $examen->id],
                                                        ['escape' => false, 'title' => 'Reactivar', 'class' => 'btn btn-success btn-sm', 'confirm' => '¿Reactivar el examen "' . $examen->nombre . '"?']
                                                    ) ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <?= $this->Html->link(__('Volver a la Lista'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                <?= $this->Html->link(__('Editar Categoría'), ['action' => 'edit', $categoriasExamene->id], ['class' => 'btn btn-primary ms-2']) ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($examenesActivos)): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('buscarExamenCategoria');
    var btnLimpiar = document.getElementById('limpiarBuscarExamenCategoria');
    var filas = document.querySelectorAll('#tablaExamenesCategoria tr[data-nombre]');
    var sinResultados = document.getElementById('sinResultadosExamenCategoria');

    function filtrar() {
        var termino = input.value.trim().toLowerCase();
        var visibles = 0;

        filas.forEach(function (fila) {
            var coincide = termino === ''
                || fila.dataset.nombre.indexOf(termino) !== -1
                || fila.dataset.muestra.indexOf(termino) !== -1;
            fila.hidden = !coincide;
            if (coincide) visibles++;
        });

        sinResultados.hidden = visibles !== 0;
    }

    input.addEventListener('input', filtrar);
    btnLimpiar.addEventListener('click', function () {
        input.value = '';
        filtrar();
        input.focus();
    });
});
</script>
<?php endif; ?>
