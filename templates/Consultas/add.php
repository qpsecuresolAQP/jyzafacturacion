<div class="container mt-4">
    <h2 class="mb-3 text-info">Nueva Consulta</h2>
    
    <?= $this->Form->create($consulta, ['class' => 'needs-validation']) ?>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Paciente <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="text" id="searchPaciente" class="form-control"
                    placeholder="Ingrese nombre o apellido"
                    value="<?= $pacienteSeleccionado ? h($pacienteSeleccionado->nombre . ' ' . $pacienteSeleccionado->apellido) : '' ?>"
                    <?= $pacienteSeleccionado ? 'readonly' : '' ?>>
                <button type="button" id="searchButton" class="btn btn-primary" <?= $pacienteSeleccionado ? 'disabled' : '' ?>>
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
            <div id="pacienteResults" class="list-group mt-1"></div>
            <?= $this->Form->hidden('paciente_id', [
                'id' => 'pacienteId',
                'value' => $pacienteSeleccionado ? $pacienteSeleccionado->id : '',
            ]) ?>
        </div>
        <div class="col-md-6 mb-3">
            <?= $this->Form->hidden('user_id', ['value' => $usuario->id]) ?>
<?php
if ($usuario->rol_id == 2 && !empty($consulta->doctor_id)) {
    echo $this->Form->control('doctor_id', [
        'type' => 'hidden',
        'value' => $consulta->doctor_id
    ]);
} else {
    echo $this->Form->control('doctor_id', [
        'label' => 'Doctor',
        'options' => $doctores,
        'class' => 'form-control',
        'empty' => 'Seleccione un doctor'
    ]);
}
?>
        </div>
    </div>

            <!-- SECCIÓN DE RECETAS -->
            <hr class="my-4">
            <h5 class="mb-4 text-info"><i class="fas fa-prescription-bottle"></i> Agregar Receta</h5>
            
            <div class="card border-info mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-pills"></i> Datos de la Receta</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="receta-nombre" class="form-label fw-bold">Nombre de Receta</label>
                            <input type="text" id="receta-nombre" class="form-control" placeholder="Ej: Antibióticos">

                        </div>
                        <div class="col-md-8">
                            <label for="receta-descripcion" class="form-label fw-bold">Descripción</label>
                            <input type="text" id="receta-descripcion" class="form-control" placeholder="Ej: Tratamiento para infección bacteriana">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="receta-notas" class="form-label fw-bold">Notas Especiales</label>
                            <textarea id="receta-notas" class="form-control" rows="2" placeholder="Ej: Tomar todos los medicamentos juntos al desayuno..."></textarea>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- NUEVOS CAMPOS DE RECETA (COMENTADO - DE MOMENTO NO NECESARIO) -->
                    <!-- <h6 class="mb-3 fw-bold"><i class="fas fa-file-medical"></i> Información Adicional de la Receta</h6>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="receta-tipo-usuario" class="form-label fw-bold">Tipo de Usuario</label>
                            <select id="receta-tipo-usuario" class="form-control">
                                <option value="">Seleccionar...</option>
                                <option value="DEMANDA">DEMANDA</option>
                                <option value="SIS">SIS</option>
                                <option value="INTERVENCIÓN SANITARIA">INTERVENCIÓN SANITARIA</option>
                                <option value="SOAT">SOAT</option>
                                <option value="OTROS">OTROS</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="receta-tipo-atencion" class="form-label fw-bold">Tipo de Atención</label>
                            <select id="receta-tipo-atencion" class="form-control">
                                <option value="">Seleccionar...</option>
                                <option value="CONSULTA EXTERNA">CONSULTA EXTERNA</option>
                                <option value="EMERGENCIA">EMERGENCIA</option>
                                <option value="HOSPITALIZACIÓN">HOSPITALIZACIÓN</option>
                                <option value="PARTICULAR">PARTICULAR</option>
                                <option value="CAMA">CAMA</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="receta-especialidad-medica" class="form-label fw-bold">Especialidad Médica</label>
                            <select id="receta-especialidad-medica" class="form-control">
                                <option value="">Seleccionar...</option>
                                <option value="CIRUGÍA">CIRUGÍA</option>
                                <option value="GINECO-OBSTETRICIA">GINECO-OBSTETRICIA</option>
                                <option value="MEDICINA">MEDICINA</option>
                                <option value="ONCOLOGÍA">ONCOLOGÍA</option>
                                <option value="PEDIATRÍA">PEDIATRÍA</option>
                                <option value="NEONATO">NEONATO</option>
                                <option value="DENTAL">DENTAL</option>
                            </select>
                        </div>
                    </div> -->

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="receta-h-cl" class="form-label fw-bold">H. Cl.</label>
                            <input type="text" id="receta-h-cl" class="form-control" placeholder="Referencia H. Cl.">
                        </div>
                        <div class="col-md-3 d-none">
                            <label for="receta-sis" class="form-label fw-bold">SIS</label>
                            <input type="text" id="receta-sis" class="form-control" placeholder="Número SIS">
                        </div>
                        <div class="col-md-3 d-none">
                            <label for="receta-particular" class="form-label fw-bold">Particular</label>
                            <input type="text" id="receta-particular" class="form-control" placeholder="Referencia Particular">
                        </div>
                        <div class="col-md-3">
                            <label for="receta-peso" class="form-label fw-bold">Peso (kg)</label>
                            <input type="number" id="receta-peso" class="form-control" step="0.01" placeholder="Ej: 65.50">
                        </div>
                        <div class="col-md-6">
                            <label for="receta-valido-hasta" class="form-label fw-bold">Válido Hasta</label>
                            <input type="date" id="receta-valido-hasta" class="form-control">
                        </div>
                    </div>



                    <!-- Entrada manual de medicamentos -->
                    <h6 class="mb-3 fw-bold"><i class="fas fa-pills"></i> Agregar Medicamentos</h6>
                    
                    <!-- Búsqueda de medicamentos en BD -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="position-relative">
                                <label for="med-buscar" class="form-label fw-bold">
                                    <i class="fas fa-search"></i> Buscar Medicamento en BD (opcional)
                                </label>
                                <input 
                                    type="text" 
                                    id="med-buscar" 
                                    class="form-control" 
                                    placeholder="Busca por código o nombre (ej: IBU, Ibuprofeno)..." 
                                    autocomplete="off"
                                >
                                <div id="med-suggestions" class="list-group position-absolute w-100" style="display: none; z-index: 1050; max-height: 300px; overflow-y: auto;"></div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                💡 <strong>Tip:</strong> Al seleccionar un medicamento de la base de datos, se completarán automáticamente los campos. También puedes ingresar datos manualmente en los campos de abajo.
                            </small>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="med-codigo" class="form-label fw-bold">Código</label>
                            <input type="text" id="med-codigo" class="form-control" placeholder="Ej: IBU" maxlength="50">
                        </div>
                        <div class="col-md-9">
                            <label for="med-nombre" class="form-label fw-bold">Nombre del Medicamento <span class="text-danger">*</span></label>
                            <input type="text" id="med-nombre" class="form-control" placeholder="Ej: Ibuprofeno" maxlength="255">
                        </div>
                    </div>

                    <!-- Detalles del medicamento -->
                    <div id="med-details" class="card bg-light border-info mb-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-clipboard-list"></i> Detalles del Medicamento</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <label class="form-label fw-bold">Concentración</label>
                                    <input type="text" id="med-concentracion" class="form-control" placeholder="Ej: 500mg" maxlength="100">
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <label class="form-label fw-bold">Cantidad <span class="text-danger">*</span></label>
                                    <input type="number" id="med-cantidad" class="form-control" min="1" placeholder="Número de unidades">
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <label class="form-label fw-bold">Forma Farmacéutica <span class="text-danger">*</span></label>
                                    <select id="med-forma" class="form-control">
                                        <option value="">Seleccionar...</option>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <label class="form-label fw-bold">Vía Administración <span class="text-danger">*</span></label>
                                    <select id="med-via" class="form-control">
                                        <option value="">Seleccionar...</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <label class="form-label fw-bold">Duración (días)</label>
                                    <input type="number" id="med-duracion" class="form-control" min="1" placeholder="Ej: 7">
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <label class="form-label fw-bold">Dosis</label>
                                    <input type="text" id="med-dosis" class="form-control" maxlength="50" placeholder="Ej: 500mg cada 8h">
                                </div>
                                <div class="col-lg-6 col-md-12">
                                    <label class="form-label fw-bold">Frecuencia</label>
                                    <textarea id="med-indicaciones" class="form-control" rows="2" placeholder="Ej: Tomar con agua, después de las comidas, no mezclar con lácteos..."></textarea>
                                </div>
                            </div>

                            <div class="row mt-3 pt-3 border-top">
                                <div class="col-12 d-flex flex-wrap botones-medicamento justify-content-end align-items-center">
                                    <button type="button" class="btn btn-outline-secondary btn-accion-medicamento" onclick="limpiarFormularioMedicamento();">
                                        <i class="fas fa-times-circle"></i> 
                                        <span>Limpiar</span>
                                    </button>
                                    <button type="button" class="btn btn-success btn-accion-medicamento btn-primary-accion" onclick="agregarMedicaReceta()">
                                        <i class="fas fa-check-circle"></i> 
                                        <span>Agregar a Receta</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de medicamentos agregados -->
                    <div id="medicamentos-tabla-container" style="display: none;" class="mb-4">
                        <h6 class="mb-3 fw-bold"><i class="fas fa-check-circle"></i> Medicamentos Agregados</h6>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width: 200px;">Medicamento</th>
                                        <th style="min-width: 80px;">Conc.</th>
                                        <th style="min-width: 70px;">Cant.</th>
                                        <th style="min-width: 100px;">Forma</th>
                                        <th style="min-width: 100px;">Vía</th>
                                        <th style="min-width: 60px;">Días</th>
                                        <th style="min-width: 100px;">Dosis</th>
                                        <th style="min-width: 200px;">Indicaciones</th>
                                        <th style="min-width: 60px;" class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="medicamentos-tbody">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Campo oculto para los medicamentos en JSON -->
                    <input type="hidden" name="medicamentos_receta" id="medicamentos-json" value="[]">
                    <input type="hidden" name="receta_datos" id="receta-datos-json" value="{}">
                </div>
            </div>

    <!-- INICIAMENTE COMENTADOS -->
    <!-- <div class="row">
        <div class="col-md-12">
            <?= $this->Form->control('motivo', ['label' => 'Motivo de consulta', 'class' => 'form-control mb-2', 'rows' => 2]) ?>
            <?= $this->Form->control('examen_fisico', ['label' => 'Examen fisico', 'class' => 'form-control mb-2', 'rows' => 2]) ?>
        </div>
    </div> -->

    <div class="row">
        <div class="col-md-12">
            <?= $this->Form->control('diagnostico', ['label' => 'Diagnostico', 'class' => 'form-control mb-2', 'rows' => 2]) ?>
        </div>
    </div>

    <!-- <div class="row">
        <div class="col-md-12">
            <?= $this->Form->control('prescripcion', ['label' => 'Prescripción', 'class' => 'form-control mb-2', 'rows' => 2]) ?>
            <?= $this->Form->control('orden_medico', ['label' => 'Orden Médica', 'class' => 'form-control mb-2', 'rows' => 2]) ?>
        </div>
    </div> -->

    <div class="row">
        <div class="col-md-12">
            <div class="mb-3 position-relative">
                <label for="cie-search">CIE-10</label>
                <input type="text" id="cie-search" class="form-control" placeholder="Escribe para buscar..." autocomplete="off">
                
                <div id="cie-suggestions" class="list-group position-absolute w-100" style="display: block; z-index: 1000; overflow-y: auto;"></div>
            </div>
            
            <div id="selected-cies"></div>
        </div>
    </div>

    <div class="col-12 text-center mt-3 mb-2">
        <?= $this->Form->button(__('Guardar Consulta'), ['type' => 'button', 'class' => 'btn btn-primary', 'onclick' => 'guardarRecetaDatos()']) ?>
<?php if (!empty($historia)): ?>
    <?= $this->Html->link(__('Cancelar'), ['controller' => 'Pacientes', 'action' => 'view', $historia->paciente_id], ['class' => 'btn btn-secondary']) ?>
<?php else: ?>
    <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
<?php endif; ?>
    </div>
    
    <?= $this->Form->end() ?>
</div>
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
    if (!nuevosArchivos.length) return;

    nuevosArchivos.forEach(nuevoArchivo => {
        if (archivosSeleccionados.some(file => file.name === nuevoArchivo.name)) {
            // Evita duplicados por nombre (opcional, podrías usar algo más robusto si quieres)
            console.warn("Archivo duplicado:", nuevoArchivo.name);
            return;
        }

        archivosSeleccionados.push(nuevoArchivo);
    });

    actualizarListaVisual();
    actualizarInputFinal();

    // Resetea el input para poder volver a subir los mismos archivos si se desea
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


    function initializeCieSearch(context) {
        if (context.dataset.cieLogicInitialized) return;
        context.dataset.cieLogicInitialized = true;
        console.log("initializeCieSearch ejecutado en:", context);

        const searchInput = context.querySelector("#cie-search");
        const suggestionsList = context.querySelector("#cie-suggestions");
        const selectedCies = context.querySelector("#selected-cies");

        if (!searchInput || !suggestionsList || !selectedCies) {
            console.warn("Elementos de búsqueda CIE no encontrados en:", context);
            return;
        }

        searchInput.addEventListener("input", function () {
            const query = searchInput.value.trim().toLowerCase();

            if (query.length < 2) {
                suggestionsList.style.display = "none";
                return;
            }

            fetch("<?= $this->Url->build(['controller' => 'Consultas', 'action' => 'buscarCie']) ?>?q=" + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    suggestionsList.innerHTML = "";

                    if (data.length === 0) {
                        suggestionsList.style.display = "none";
                        return;
                    }

                    const limitedData = data.slice(0, 10);

                    limitedData.forEach(item => {
                        const div = document.createElement("div");
                        div.classList.add("list-group-item", "list-group-item-action", "cie-item");
                        div.textContent = `${item.clave} - ${item.descripcion}`;
                        div.dataset.id = item.id;

                        div.addEventListener("click", function () {
                            addSelectedCie(item);
                            suggestionsList.style.display = "none";
                        });

                        suggestionsList.appendChild(div);
                    });

                    suggestionsList.style.display = "block";
                })
                .catch(error => console.error("Error en la búsqueda:", error));
        });

        function addSelectedCie(item) {
            const existingSpans = document.getElementById('selected-cies').querySelectorAll('.badge[data-cie-json]');
            for (let span of existingSpans) {
                try {
                    const jsonValue = JSON.parse(span.getAttribute('data-cie-json'));
                    if (jsonValue.id === item.id) {
                        return;
                    }
                } catch (e) {
                    console.error('Error parsing CIE data:', e);
                }
            }

            const jsonData = JSON.stringify({ id: item.id, clave: item.clave });
            
            const span = document.createElement("span");
            span.setAttribute("data-cie-json", jsonData);
            span.textContent = `${item.clave} - ${item.descripcion} `;
            span.classList.add("badge", "bg-info", "text-white", "me-2", "p-2", "d-inline-block", "mb-2");
            
            const removeBtn = document.createElement("button");
            removeBtn.type = "button";
            removeBtn.className = "btn-close btn-close-white ms-1";
            removeBtn.style.fontSize = "0.75rem";

            removeBtn.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                span.remove();
            });

            span.appendChild(removeBtn);
            document.getElementById('selected-cies').appendChild(span);

            searchInput.value = "";
        }
    }
    
function runInitializers(context) {
    if (!(context instanceof HTMLElement)) return;

    if (context.dataset.initialized === "1") {
        console.log("Ya inicializado:", context);
        return;
    }

    initializeFormLogic(context);
    initializeCieSearch(context);

    context.dataset.initialized = "1";
    console.log("Inicializado:", context);
}

// Al cargar la página
document.addEventListener("DOMContentLoaded", function () {
    runInitializers(document.body);
    console.log("Init: pagina");
});

// Cuando se abre un modal -> siempre inicializar si no está marcado
$(document).on('shown.bs.modal', function (e) {
    const modalContent = e.target.querySelector('.modal-content');
    if (modalContent) {
        runInitializers(modalContent); // ahora siempre revisa el flag
        console.log("Init: modal");
    }
});

// Inicializar contenido nuevo tras AJAX
$(document).on('ajaxComplete', function () {
    const newContent = document.querySelectorAll('[data-init]:not([data-initialized="1"])');
    newContent.forEach(el => {
        runInitializers(el);
        console.log("Init: ajax ->", el);
    });
});

// Al cerrar el modal -> quitar flag y limpiar TinyMCE
$(document).on('hidden.bs.modal', function (e) {
    const modalContent = e.target.querySelector('.modal-content');
    if (modalContent) {
        delete modalContent.dataset.formLogicInitialized;
        delete modalContent.dataset.recipeLogicInitialized;
        delete modalContent.dataset.cieLogicInitialized;
        delete modalContent.dataset.initialized;
    }

});

// ============= LÓGICA DE RECETAS Y MEDICAMENTOS =============

// Variables globales para almacenar datos de receta
let medicamentosAgregados = [];
let formasFarmaceuticas = [];
let viasAdministracion = [];
let medicamentoSeleccionado = null;
let medicamentosBaseDatos = [];

// Cargar formas y vías al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DOMContentLoaded ===');
    // Esperar un poco para que los elementos estén disponibles
    setTimeout(() => {
        cargarFormasYVias();
        inicializarBuscadorMedicamentos();
    }, 100);
});

function cargarFormasYVias() {
    console.log('Iniciando cargarFormasYVias()');
    
    // Verificar que los elementos existan
    const selectForma = document.getElementById('med-forma');
    const selectVia = document.getElementById('med-via');
    
    console.log('med-forma existe:', !!selectForma);
    console.log('med-via existe:', !!selectVia);
    
    // Formas farmacéuticas
    fetch('<?= $this->Url->build(['controller' => 'FormasFarmaceuticas', 'action' => 'getAll']) ?>')
        .then(r => {
            console.log('Respuesta Formas:', r.status, r.ok);
            if (!r.ok) throw new Error('Error cargando formas farmacéuticas: ' + r.status);
            return r.json();
        })
        .then(data => {
            console.log('Formas cargadas:', data);
            formasFarmaceuticas = Array.isArray(data) ? data : [];
            const select = document.getElementById('med-forma');
            if (select) {
                select.innerHTML = '<option value="">Seleccionar...</option>';
                formasFarmaceuticas.forEach(f => {
                    select.innerHTML += `<option value="${f.id}">${f.nombre}</option>`;
                });
            } else {
                console.error('med-forma no encontrado');
            }
        })
        .catch(e => console.error('Error en formas:', e));

    // Vías de administración
    console.log('Iniciando fetch de vías...');
    fetch('<?= $this->Url->build(['controller' => 'ViasAdministracion', 'action' => 'getAll']) ?>')
        .then(r => {
            console.log('Respuesta Vías:', r.status, r.ok);
            if (!r.ok) throw new Error('Error cargando vías de administración: ' + r.status);
            return r.json();
        })
        .then(data => {
            console.log('Vías cargadas:', data);
            viasAdministracion = Array.isArray(data) ? data : [];
            console.log('viasAdministracion array:', viasAdministracion);
            const select = document.getElementById('med-via');
            console.log('Elemento med-via:', select);
            if (select) {
                console.log('Limpiando select y agregando opciones...');
                select.innerHTML = '<option value="">Seleccionar...</option>';
                console.log('Número de vías:', viasAdministracion.length);
                viasAdministracion.forEach(v => {
                    console.log('Agregando vía:', v.id, v.nombre);
                    select.innerHTML += `<option value="${v.id}">${v.nombre}</option>`;
                });
                console.log('HTML del select:', select.innerHTML);
            } else {
                console.error('med-via no encontrado al cargar vías');
            }
        })
        .catch(e => console.error('Error en vías:', e));
}

// Función para buscar medicamentos en la BD
function inicializarBuscadorMedicamentos() {
    console.log('Iniciando inicializarBuscadorMedicamentos()');
    
    const buscarInput = document.getElementById('med-buscar');
    const sugerenciasDiv = document.getElementById('med-suggestions');
    
    if (!buscarInput || !sugerenciasDiv) {
        console.warn('Elementos de búsqueda de medicamentos no encontrados');
        return;
    }
    
    buscarInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        if (query.length < 2) {
            sugerenciasDiv.style.display = 'none';
            return;
        }
        
        // Buscar medicamentos por código o nombre
        fetch('<?= $this->Url->build(['controller' => 'Medicamentos', 'action' => 'buscar']) ?>?q=' + encodeURIComponent(query))
            .then(r => {
                if (!r.ok) throw new Error('Error en búsqueda: ' + r.status);
                return r.json();
            })
            .then(data => {
                console.log('Medicamentos encontrados:', data);
                medicamentosBaseDatos = Array.isArray(data) ? data : [];
                
                sugerenciasDiv.innerHTML = '';
                
                if (medicamentosBaseDatos.length === 0) {
                    sugerenciasDiv.style.display = 'none';
                    return;
                }
                
                medicamentosBaseDatos.forEach(med => {
                    const div = document.createElement('div');
                    div.className = 'list-group-item list-group-item-action med-suggestion-item';
                    div.innerHTML = `
                        <strong>${med.codigo || '(sin código)'}</strong> - ${med.nombre}<br>
                        <small class="text-muted">Concentración: ${med.concentracion || 'N/A'}</small>
                    `;
                    div.style.cursor = 'pointer';
                    
                    div.addEventListener('click', function() {
                        seleccionarMedicamento(med);
                    });
                    
                    sugerenciasDiv.appendChild(div);
                });
                
                sugerenciasDiv.style.display = 'block';
            })
            .catch(e => console.error('Error en búsqueda de medicamentos:', e));
    });
    
    // Cerrar sugerencias al hacer click fuera
    document.addEventListener('click', function(e) {
        if (e.target.id !== 'med-buscar') {
            sugerenciasDiv.style.display = 'none';
        }
    });
}

// Función para seleccionar un medicamento y autocompletar
function seleccionarMedicamento(medicamento) {
    console.log('Medicamento seleccionado:', medicamento);
    
    // Llenar campos automáticamente
    document.getElementById('med-codigo').value = medicamento.codigo || '';
    document.getElementById('med-nombre').value = medicamento.nombre || '';
    document.getElementById('med-concentracion').value = medicamento.concentracion || '';
    
    // Limpiar búsqueda
    document.getElementById('med-buscar').value = '';
    document.getElementById('med-suggestions').style.display = 'none';
    
    // Guardar referencia al medicamento seleccionado
    medicamentoSeleccionado = medicamento;
    
    // Enfocar en cantidad para que continúe ingresando datos
    document.getElementById('med-cantidad').focus();
}

// Entrada manual - no se requiere inicialización de buscador
// Los campos se rellenan directamente por el usuario

function agregarMedicaReceta() {
    const nombre = document.getElementById('med-nombre').value.trim();
    const codigo = document.getElementById('med-codigo').value.trim();
    const concentracion = document.getElementById('med-concentracion').value.trim();
    const cantidad = document.getElementById('med-cantidad').value;
    const formaId = document.getElementById('med-forma').value;
    const viaId = document.getElementById('med-via').value;
    const duracion = document.getElementById('med-duracion').value;
    const dosis = document.getElementById('med-dosis').value;
    const indicaciones = document.getElementById('med-indicaciones').value;

    if (!nombre) {
        alert('Ingresa el nombre del medicamento');
        return;
    }

    if (!cantidad || !formaId || !viaId) {
        alert('Completa los campos obligatorios (Cantidad, Forma, Vía)');
        return;
    }

    const formaNombre = formasFarmaceuticas.find(f => f.id == formaId)?.nombre || '';
    const viaNombre = viasAdministracion.find(v => v.id == viaId)?.nombre || '';

    medicamentosAgregados.push({
        nombre: nombre,
        codigo: codigo || '',
        nombre_medicamento: nombre,
        codigo_medicamento: codigo || '',
        concentracion: concentracion || '',
        cantidad: cantidad,
        forma_id: formaId,
        forma_nombre: formaNombre,
        via_id: viaId,
        via_nombre: viaNombre,
        duracion_dias: duracion,
        dosis: dosis,
        observaciones: indicaciones
    });

    actualizarTablaReceta();
    limpiarFormularioMedicamento();
}

function actualizarTablaReceta() {
    const container = document.getElementById('medicamentos-tabla-container');
    const tbody = document.getElementById('medicamentos-tbody');

    if (medicamentosAgregados.length === 0) {
        container.style.display = 'none';
        return;
    }

    tbody.innerHTML = '';
    medicamentosAgregados.forEach((med, idx) => {
        const tr = document.createElement('tr');
        const indicacionesText = med.observaciones ? med.observaciones.substring(0, 50) + (med.observaciones.length > 50 ? '...' : '') : '—';
        tr.innerHTML = `
            <td>
                <strong class="text-primary">${med.codigo}</strong><br>
                <span class="text-muted">${med.nombre}</span>
            </td>
            <td class="text-center"><small>${med.concentracion || '—'}</small></td>
            <td class="text-center"><strong>${med.cantidad}</strong></td>
            <td><small>${med.forma_nombre}</small></td>
            <td><small>${med.via_nombre}</small></td>
            <td class="text-center"><small>${med.duracion_dias || '—'}</small></td>
            <td class="text-center"><small>${med.dosis || '—'}</small></td>
            <td><small class="text-muted" title="${med.observaciones || ''}">${indicacionesText}</small></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarMedicamento(${idx})" title="Eliminar medicamento">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
    container.style.display = 'block';
}

function eliminarMedicamento(idx) {
    medicamentosAgregados.splice(idx, 1);
    actualizarTablaReceta();
}

function limpiarFormularioMedicamento() {
    document.getElementById('med-codigo').value = '';
    document.getElementById('med-nombre').value = '';
    document.getElementById('med-concentracion').value = '';
    document.getElementById('med-cantidad').value = '';
    document.getElementById('med-forma').value = '';
    document.getElementById('med-via').value = '';
    document.getElementById('med-duracion').value = '';
    document.getElementById('med-dosis').value = '';
    document.getElementById('med-indicaciones').value = '';
    document.getElementById('med-nombre').focus();
}

function sincronizarCies() {
    // Obtener todos los badges visibles en selected-cies
    const selectedCiesContainer = document.getElementById('selected-cies');
    const badges = selectedCiesContainer.querySelectorAll('.badge[data-cie-json]');
    
    // Eliminar todos los inputs ocultos existentes
    const inputsAntiguos = selectedCiesContainer.querySelectorAll('input[name="cie_data[]"]');
    inputsAntiguos.forEach(input => input.remove());
    
    // Crear nuevos inputs basándose en los badges visibles
    badges.forEach(badge => {
        const jsonData = badge.getAttribute('data-cie-json');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'cie_data[]';
        input.value = jsonData;
        selectedCiesContainer.appendChild(input);
    });
}

function guardarRecetaDatos() {
    // Sincronizar CIEs antes de guardar
    sincronizarCies();
    
    const nombre = document.getElementById('receta-nombre').value;
    const descripcion = document.getElementById('receta-descripcion').value;
    const notas = document.getElementById('receta-notas').value;
    // COMENTADO - Campos no necesarios de momento
    // const tipoUsuario = document.getElementById('receta-tipo-usuario').value;
    // const tipoAtencion = document.getElementById('receta-tipo-atencion').value;
    // const especialidadMedica = document.getElementById('receta-especialidad-medica').value;
    const hCl = document.getElementById('receta-h-cl').value;
    const sis = document.getElementById('receta-sis').value;
    const particular = document.getElementById('receta-particular').value;
    const peso = document.getElementById('receta-peso').value;
    const validoHasta = document.getElementById('receta-valido-hasta').value;
    
    if (!nombre && medicamentosAgregados.length === 0) {
        return; // Si no hay nada, no guardar
    }

    // Generar indicaciones consolidadas basadas en medicamentos
    let indicacionesConsolidadas = '';
    if (medicamentosAgregados.length > 0) {
        indicacionesConsolidadas = medicamentosAgregados.map((med, idx) => {
            const conc = med.concentracion ? ` ${med.concentracion}` : '';
            const duracion = med.duracion_dias ? ` - DURACIÓN: ${med.duracion_dias} días` : '';
            const obs = med.observaciones ? ` - NOTA: ${med.observaciones}` : '';
            return `${med.nombre}${conc} (${med.cantidad} unidades)${duracion}${obs}`;
        }).join(' | ');
    }

    // Guardar datos en campos ocultos para enviar con el formulario
    document.getElementById('medicamentos-json').value = JSON.stringify(medicamentosAgregados);
    document.getElementById('receta-datos-json').value = JSON.stringify({
        nombre: nombre,
        descripcion: descripcion,
        notas: notas,
        // COMENTADO - Campos no necesarios de momento
        // tipo_usuario: tipoUsuario,
        // tipo_atencion: tipoAtencion,
        // especialidad_medica: especialidadMedica,
        h_cl: hCl,
        sis: sis,
        particular: particular,
        peso: peso,
        valido_hasta: validoHasta,
        indicaciones_consolidadas: indicacionesConsolidadas
    });

    // Enviar el formulario
    document.querySelector('form').submit();
}
</script>

<style>

    #cie-suggestions .cie-item {
        /* color: white; */
        color: #212529;
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    #cie-suggestions .cie-item:hover {
        /* background-color: #6c757d; */
        background-color: #e2e6ea;
    }

    /* Estilos para búsqueda de medicamentos */
    #med-suggestions .med-suggestion-item {
        color: #212529;
        background-color: #f8f9fa;
        padding: 10px 12px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid #dee2e6;
    }

    #med-suggestions .med-suggestion-item:hover {
        background-color: #e7f3ff;
        border-color: #0dcaf0;
        box-shadow: 0 2px 4px rgba(13, 202, 240, 0.2);
    }

    #med-suggestions .med-suggestion-item strong {
        color: #0dcaf0;
        font-size: 0.95rem;
    }
    .textarea-box {
        padding: 8px 10px;
        min-height: 120px;
        overflow-y: auto;
        resize: vertical;
        border: 1px solid #ced4da;
        border-radius: 4px;
        /* white-space: pre-wrap; */
        outline: none;
         font-family: Calibri, Arial, sans-serif;
    }

    /* Estilos para la sección de recetas */
    #med-suggestions .list-group-item {
        color: #212529;
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    #med-suggestions .list-group-item:hover {
        background-color: #e2e6ea;
    }

    #med-suggestions .list-group-item strong {
        color: #1976d2;
        font-size: 1rem;
    }

    .card.border-info {
        border-width: 2px;
        box-shadow: 0 0 10px rgba(23, 162, 184, 0.1);
    }
    

    .bg-success {
        background-color: #28a745 !important;
    }

    .bg-info {
        background-color: #17a2b8 !important;
    }

    #medicamentos-tabla-container .table {
        margin-bottom: 0;
        font-size: 0.95rem;
    }

    #medicamentos-tabla-container .table tbody tr {
        border-bottom: 1px solid #dee2e6;
        transition: background-color 0.2s ease;
    }

    #medicamentos-tabla-container .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    #medicamentos-tabla-container .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        white-space: nowrap;
        padding: 12px 8px;
    }

    #medicamentos-tabla-container .table td {
        vertical-align: middle;
        padding: 10px 8px;
    }

    .form-label {
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .fw-bold {
        font-weight: 600 !important;
    }

    /* Estilos para botones de acciones */
    .btn-accion {
        padding: 12px 28px;
        font-size: 1rem;
        font-weight: 500;
        border-radius: 6px;
        min-width: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-accion:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        
    }

    .btn-accion i {
        font-size: 1.1rem;
    }

    .btn-success.btn-accion {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success.btn-accion:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .btn-outline-secondary.btn-accion {
        color: #6c757d;
        border: 2px solid #6c757d;
    }

    .btn-outline-secondary.btn-accion:hover {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
    }

    /* Estilos mejorados para botones de medicamentos */
    .btn-accion-medicamento {
        padding: 9px 22px;
        font-size: 0.9rem;
        font-weight: 500;
        border-radius: 6px;
        min-width: 160px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
        white-space: nowrap;
    }

    .btn-accion-medicamento:hover {
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.16);
        transform: translateY(-3px);
    }

    .btn-accion-medicamento i {
        font-size: 1.15rem;
    }

    .btn-primary-accion {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }

    .btn-primary-accion:hover {
        background-color: #218838;
        border-color: #1e7e34;
        color: white;
    }

    .btn-outline-secondary.btn-accion-medicamento {
        color: #6c757d;
        border: 2px solid #6c757d;
        background-color: transparent;
    }

    .btn-outline-secondary.btn-accion-medicamento:hover {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
    }

    /* Contenedor de botones con separación */
    .botones-medicamento {
        gap: 1.2rem;
    }

    /* Responsive para botones */
    @media (max-width: 768px) {
        .botones-medicamento {
            gap: 1rem;
        }

        .btn-accion-medicamento {
            padding: 10px 20px;
            font-size: 0.9rem;
            min-width: 150px;
            gap: 6px;
        }

        .btn-accion-medicamento i {
            font-size: 1rem;
        }
    }

    @media (max-width: 576px) {
        .btn-accion-medicamento {
            padding: 10px 16px;
            font-size: 0.85rem;
            min-width: auto;
            flex: 1 1 calc(50% - 10px);
            max-width: 100%;
        }

        .btn-accion-medicamento span {
            display: inline;
        }

        .d-flex.flex-wrap.gap-5 {
            gap: 1rem !important;
        }
    }

    /* Diseño minimalista para detalles del medicamento */
    #med-details {
        background-color: transparent !important;
        border: none !important;
        border-top: 1px solid #e9ecef !important;
        padding: 1.5rem 0 0.5rem !important;
        margin: 1.5rem 0 0 !important;
    }

    #med-details h6 {
        color: #f8f9fa;
        font-size: 0.9rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    #med-details .form-control,
    #med-details .form-select {
        background-color: #fafbfc;
        border: 1px solid #e1e4e8;
        border-radius: 4px;
    }

    #med-details .form-control:focus,
    #med-details .form-select:focus {
        background-color: #fff;
        border-color: #17a2b8;
        box-shadow: 0 0 0 0.15rem rgba(23, 162, 184, 0.25);
    }

</style>