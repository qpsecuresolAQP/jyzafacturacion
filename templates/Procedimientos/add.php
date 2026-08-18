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
        <h3 class="text-info"><i class="fas fa-plus-circle"></i> Registrar Nuevo Procedimiento</h3>
    </div>

    <!-- Campo Doctor -->
    <div class="col-md-6 mb-3">
        <?php
        if ($usuario->rol == 3 && !empty($procedimiento->doctor_id)) {
            echo $this->Form->control('doctor_id', [
                'type' => 'hidden',
                'value' => $procedimiento->doctor_id
            ]);
        } else {
            echo $this->Form->control('doctor_id', [
                'label' => 'Doctor Responsable',
                'class' => 'form-control',
                'options' => $doctores,
                'empty' => 'Seleccione un doctor',
            ]);
        }
        ?>
    </div>

    <div class="col-md-6 mb-3">
        <?= $this->Form->control('historia_id', [
            'label' => 'Paciente',
            'class' => 'form-control',
            'options' => $historias,
            'disabled' => ($procedimiento->historia_id) ? true : false
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

    <!-- Subida de documentos -->
    <div class="col-md-12 mb-3">
        <h5>Agregar Documentos</h5>
        <?= $this->Form->control('documentos_temp', [
            'type' => 'file',
            'label' => false,
            'class' => 'form-control',
            'id' => 'documentosTemp',
            'multiple' => true
        ]) ?>
        <small class="form-text text-muted">Seleccione uno o más archivos (imágenes, PDF, etc.)</small>
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label fw-semibold">Archivos seleccionados:</label>
        <ul id="lista-documentos" class="list-group"></ul>
    </div>

    <!-- Input oculto real que se enviará -->
    <input type="file" name="documentos[]" id="documentosFinal" style="display:none;" multiple>

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

<style>
    .list-group-item {
        border-left: 4px solid #0dcaf0;
        padding: 10px;
    }

    .btn {
        font-weight: 600;
        border-radius: 0.375rem;
    }

    .form-control:focus {
        border-color: #0dcaf0;
        box-shadow: 0 0 0 0.2rem rgba(13, 202, 240, 0.25);
    }
</style>

<script>
    var initializeFormLogic = (context) => {
        if (context.dataset.formLogicInitialized) return;
        context.dataset.formLogicInitialized = true;

        const inputTemp = context.querySelector('#documentosTemp');
        const inputFinal = context.querySelector('#documentosFinal');
        const lista = context.querySelector('#lista-documentos');

        if (!inputTemp || !inputFinal || !lista) return;

        let archivosSeleccionados = [];

        inputTemp.addEventListener('change', function () {
            const nuevosArchivos = Array.from(this.files);
            if (!nuevosArchivos.length) return;

            nuevosArchivos.forEach(nuevoArchivo => {
                if (archivosSeleccionados.some(file => file.name === nuevoArchivo.name)) {
                    console.warn("Archivo duplicado:", nuevoArchivo.name);
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
                btnEliminar.textContent = 'Eliminar';
                btnEliminar.className = 'btn btn-sm btn-danger';
                btnEliminar.type = 'button';
                btnEliminar.onclick = function (e) {
                    e.preventDefault();
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

    document.addEventListener("DOMContentLoaded", function () {
        initializeFormLogic(document);
    });

    if (typeof $ !== 'undefined') {
        $(document).on('shown.bs.modal', function (e) {
            const modalContent = e.target.querySelector('.modal-content');
            if (modalContent) {
                initializeFormLogic(modalContent);
            }
        });
    }
</script>