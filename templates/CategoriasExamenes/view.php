<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CategoriasExamene $categoriasExamene
 */
?>
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
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
                <?php if (!empty($categoriasExamene->examenes)): ?>
                    <div class="d-flex gap-2">
                        <input type="text" id="buscarExamenCategoria" class="form-control form-control-sm" placeholder="Buscar por nombre o muestra..." style="min-width: 240px;">
                        <button type="button" id="limpiarBuscarExamenCategoria" class="btn btn-sm btn-outline-secondary" title="Limpiar búsqueda">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            <div class="table-responsive">
                <table class="table table-striped mt-2">
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
                        <?php if (empty($categoriasExamene->examenes)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No hay exámenes registrados en esta categoría.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categoriasExamene->examenes as $examen): ?>
                                <tr data-nombre="<?= h(mb_strtolower($examen->nombre)) ?>" data-muestra="<?= h(mb_strtolower((string)$examen->muestra)) ?>">
                                    <td><?= h($examen->nombre) ?></td>
                                    <td><?= h($examen->muestra ?: '-') ?></td>
                                    <td><?= number_format((float)$examen->precio, 2) ?></td>
                                    <td><?= number_format((float)$examen->precio_convenio, 2) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $examen->estado ? 'success' : 'secondary' ?>">
                                            <?= $examen->estado ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($this->Permisos->tiene('Examenes', 'edit')): ?>
                                            <?= $this->Html->link(
                                                '<i class="fas fa-edit"></i>',
                                                ['controller' => 'Examenes', 'action' => 'edit', $examen->id],
                                                ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm openModal']
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

            <div class="text-center mt-4">
                <?= $this->Html->link(__('Volver a la Lista'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                <?= $this->Html->link(__('Editar Categoría'), ['action' => 'edit', $categoriasExamene->id], ['class' => 'btn btn-primary ms-2']) ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($categoriasExamene->examenes)): ?>
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
