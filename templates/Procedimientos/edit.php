<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Procedimiento $procedimiento
 */
?>

<div class="container mt-4 mb-4">
    <?= $this->Form->create($procedimiento, ['type' => 'file', 'class' => 'row g-3']) ?>

    <!-- Título -->
    <div class="col-12 mb-4">
        <h3 class="text-info"><i class="fas fa-edit"></i> Editar Procedimiento</h3>
    </div>

    <!-- Campo Doctor -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('doctor_id', [
            'label' => 'Doctor Responsable',
            'class' => 'form-control',
            'options' => $doctores,
            'empty' => 'Seleccione un doctor',
        ]) ?>
    </div>

    <div class="col-md-6 mb-3">
        <?= $this->Form->control('historia_id', [
            'label' => 'Paciente',
            'class' => 'form-control',
            'options' => $historias,
        ]) ?>
    </div>
    
    <!-- Campo Procedimiento -->
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('procedimiento', [
            'label' => 'Nombre del Procedimiento',
            'class' => 'form-control',
            'placeholder' => 'Ejemplo: Limpieza facial'
        ]) ?>
    </div>

<div class="col-md-12 mb-3">
    <h5>Documentos Adjuntos</h5>
    <?php if (!empty($procedimiento->documentos_procedimientos)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Previsualización</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($procedimiento->documentos_procedimientos as $docProcedimiento): ?>
                        <?php $documento = $docProcedimiento->documento; ?>
                        <tr>
                            <td><?= h(basename($documento->ruta_archivo)) ?></td>
                            <td>
                                <?php
                                    $rutaArchivo = '/' . h($documento->ruta_archivo);
                                    $ext = strtolower(pathinfo($documento->ruta_archivo, PATHINFO_EXTENSION));
                                ?>
                                <?php if (in_array($ext, ['jpg', 'jpeg', 'png'])): ?>
                                    <img src="<?= $this->Url->build($rutaArchivo, ['fullBase' => true]) ?>" class="img-thumbnail" style="max-width: 100px;">
                                <?php elseif ($ext === 'pdf'): ?>
                                    <iframe src="<?= $this->Url->build($rutaArchivo, ['fullBase' => true]) ?>" style="width: 100px; height: 100px;" frameborder="0"></iframe>
                                <?php else: ?>
                                    <i class="fas fa-file-alt"></i> Archivo no visualizable
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $this->Html->link('Ver', $rutaArchivo, ['target' => '_blank', 'class' => 'btn btn-info btn-sm']) ?>
                                <?= $this->Html->link('Descargar', $rutaArchivo, ['download' => true, 'class' => 'btn btn-success btn-sm']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No hay documentos adjuntos.</p>
    <?php endif; ?>
</div>

<!-- Subida de nuevos documentos -->
<div class="col-md-12 mx-auto mb-3">
    <?= $this->Form->control('documentos_temp', [
        'type' => 'file',
        'label' => 'Agregar Documento',
        'class' => 'form-control',
        'id' => 'documentosTemp',
        'multiple' => true
    ]) ?>
</div>

<div class="col-md-12 mx-auto mb-3">
    <ul id="lista-documentos" class="list-group mb-3"></ul>
    <input type="file" id="documentosFinal" name="documentos[]" multiple style="display: none;">
</div>
    
    <!-- Botones -->
    <div class="col-12 text-center mt-3">
        <?= $this->Form->button('Guardar Cambios', [
            'type' => 'submit',
            'class' => 'btn btn-info',
            'id' => 'btnGuardar'
        ]) ?>
    </div>

    <?= $this->Form->end() ?>
</div>

<!-- Script para desactivar botón al enviar -->
<script>
    const form = document.querySelector("form");
    const btnGuardar = document.getElementById("btnGuardar");

    if (form && btnGuardar) {
        form.addEventListener("submit", function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                return;
            }
            btnGuardar.disabled = true;
            btnGuardar.innerText = "Guardando...";
        });
    }
</script>
<script>
    var initializeFormLogic = (context) => {
        if (context.dataset.formLogicInitialized) return;
        context.dataset.formLogicInitialized = true;
        console.log("initializeFormLogic ejecutado en:", context);

        const inputTemp = context.querySelector('#documentosTemp');
        const inputFinal = context.querySelector('#documentosFinal');
        const lista = context.querySelector('#lista-documentos');

        if (!inputTemp || !inputFinal || !lista) return;

        let archivosSeleccionados = [];

        inputTemp.addEventListener('change', function () {
    const nuevosArchivos = Array.from(this.files);
    nuevosArchivos.forEach(nuevoArchivo => {
        if (!nuevoArchivo) return;

        if (archivosSeleccionados.some(file => file.name === nuevoArchivo.name)) {
            alert(`El archivo ${nuevoArchivo.name} ya fue agregado.`);
            return;
        }

        archivosSeleccionados.push(nuevoArchivo);
    });

    actualizarListaVisual();
    actualizarInputFinal();
    inputTemp.value = '';
});

        function actualizarListaVisual() {
            lista.innerHTML = '';
            archivosSeleccionados.forEach((file, index) => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.textContent = file.name;

                const btnEliminar = document.createElement('button');
                btnEliminar.textContent = 'x';
                btnEliminar.className = 'btn btn-sm btn-danger';
                btnEliminar.onclick = function () {
                    archivosSeleccionados.splice(index, 1);
                    actualizarListaVisual();
                    actualizarInputFinal();
                };

                li.appendChild(btnEliminar);
                lista.appendChild(li);
            });
        }

        function actualizarInputFinal() {
            const dataTransfer = new DataTransfer();
            archivosSeleccionados.forEach(file => dataTransfer.items.add(file));
            inputFinal.files = dataTransfer.files;
        }
    };
    document.addEventListener('shown.bs.modal', function (event) {
    const modal = event.target;
    const modalContent = modal.querySelector('.modal-content');

    if (!modalContent) {
        console.warn('No se encontró .modal-content dentro del modal');
        return;
    }

    if (!modal.classList.contains('initialized')) {
        initializeFormLogic(modalContent);
        modal.classList.add('initialized');
    }
});

function runInitializers(context) {
    initializeFormLogic(context);
}
// Al cargar la página
document.addEventListener("DOMContentLoaded", function () {
    runInitializers(document);
});

// Cuando se abre un modal
$(document).on('shown.bs.modal', function (e) {
    const modalContent = e.target.querySelector('.modal-content');
    if (modalContent) {
        runInitializers(modalContent);
    }
});

// Si usas AJAX (por ejemplo, al cargar contenido por Pjax, Turbo, etc.)
$(document).on('ajaxComplete', function () {
    runInitializers(document);
});
$(document).on('hidden.bs.modal', function (e) {
    const modalContent = e.target.querySelector('.modal-content');
    if (modalContent) {
        delete modalContent.dataset.formLogicInitialized;
    }
});
</script>
