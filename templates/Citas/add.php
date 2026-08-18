<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<div class="container mt-4 mb-4">
    <?= $this->Form->create($cita, ['class' => 'row g-3', 'id' => 'formAgregarCita']) ?>

    <!-- Título del formulario -->
    <div class="col-12 mb-4">
        <h3 class="text-info"><i class="fas fa-calendar-plus"></i> Agregar Cita</h3>
    </div>
    <!-- Usuario Logueado (Solo Lectura) -->
    <div class="col-md-10 mx-auto mb-3">
        <?= $this->Form->control('usuario', [
            'label' => 'Usuario Responsable',
            'class' => 'form-control',
            'value' => $this->request->getAttribute('identity')->username, // Ajusta según el campo del usuario
            'readonly' => true,
        ]) ?>
    </div>
   

    <!-- Selección de Doctor -->
    <!-- <div class="col-md-10 mx-auto mb-3">
        <?= $this->Form->control('doctor_id', [
            'label' => 'Doctor',
            'options' => $doctores,

            'class' => 'form-control',
            'id' => 'doctor_id',
            'empty' => 'Seleccione un doctor',
            'required' => true,
        ]) ?>
    </div> -->
    <?php
    $selectedId = $cita->doctor_id ?? null;
    ?>

    <div class="col-md-10 mx-auto mb-3">
        <?= $this->Form->control('doctor_id', [
            'label' => 'Doctor',
            'options' => $doctores,
            'class' => 'form-control',
            'id' => 'doctor_id',
            'empty' => 'Seleccione un doctor',
            'required' => true,
            'disabled' => true,
            'value' => $selectedId,
        ]) ?>

        <?= $this->Form->hidden('doctor_id', ['value' => $selectedId]) ?>
    </div>
    
    <!-- Alternar entre paciente existente o nuevo -->
    <div class="col-md-10 mx-auto mb-3">
        <label class="form-label">Seleccione una opción</label>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="paciente_opcion" id="pacienteExistente" value="existente" checked>
            <label class="form-check-label" for="pacienteExistente">Paciente Existente</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="paciente_opcion" id="pacienteNuevo" value="nuevo">
            <label class="form-check-label" for="pacienteNuevo">Nuevo Paciente</label>
        </div>
    </div>

    <!-- Buscador de Paciente (Paciente Existente) -->
    <div class="col-md-10 mx-auto mb-3" id="pacienteExistenteForm">
        <label for="searchPaciente" class="form-label">Buscar Paciente</label>
        <div class="input-group">
            <?= $this->Form->text('search_paciente', [
                'label' => false,
                'class' => 'form-control',
                'id' => 'searchPaciente',
                'placeholder' => 'Ingrese el nombre o apellido del paciente',
            ]) ?>
            <button type="button" id="searchButton" class="btn btn-primary">
                <i class="fas fa-search"></i> Buscar
            </button>
        </div>
        <div id="pacienteResults" class="list-group mt-2"></div>
        <?= $this->Form->hidden('paciente_id', ['id' => 'pacienteId']) ?>
    </div>

    <!-- Formulario para Nuevo Paciente -->
    <div class="col-md-10 mx-auto mb-3" id="pacienteNuevoForm" style="display: none;">
        <?= $this->Form->control('nuevo_paciente.nombre', [
            'label' => 'Nombre',
            'class' => 'form-control',
            'placeholder' => 'Ingrese el nombre del paciente',
            'required' => true,
        ]) ?>
        <?= $this->Form->control('nuevo_paciente.apellido', [
            'label' => 'Apellido',
            'class' => 'form-control',
            'placeholder' => 'Ingrese el apellido del paciente',
            'required' => true,
        ]) ?>
        <!-- <?= $this->Form->control('nuevo_paciente.telefono_celular', [
            'label' => 'Teléfono',
            'class' => 'form-control',
            'placeholder' => 'Ingrese el número de teléfono del paciente',
            'required' => true,
        ]) ?> -->
        <?= $this->Form->control('nuevo_paciente.telefono_celular', [
            'label' => 'Teléfono',
            'class' => 'form-control',
            'placeholder' => 'Ingrese el número de teléfono del paciente',
            'required' => true,
            'type' => 'text', // usamos 'text' para controlar mejor
            'maxlength' => 9,
            'pattern' => '[0-9]{9}',
            'title' => 'Ingrese exactamente 9 números sin letras ni espacios'
        ]) ?>

    </div>

    <!-- Fecha y Hora -->
    <div class="col-md-10 mx-auto mb-3">
        <?= $this->Form->control('fecha_hora', [
            'label' => 'Fecha y Hora',
            'type' => 'datetime-local',
            'class' => 'form-control',
            'id' => 'fecha_hora',
            'value' => isset($cita->fecha_hora) ? date('Y-m-d\TH:i:s', strtotime($cita->fecha_hora)) : '',
            'required' => true,
            'readonly' => true,
        ]) ?>
    </div>
    <div id="availabilityMessage"></div>
    <!-- Duración de la Cita -->
    <div class="col-md-10 mx-auto mb-3">
        <?= $this->Form->control('duracion_minutos', [
            'label' => 'Duración de la Cita',
            'type' => 'select',
            'options' => [
                15 => '15 minutos',
                30 => '30 minutos',
                45 => '45 minutos',
                60 => '60 minutos',
                75 => '75 minutos',
                90 => '90 minutos',
                105 => '105 minutos',
                120 => '120 minutos',
                135 => '135 minutos',
                150 => '150 minutos',
                165 => '165 minutos',
                180 => '180 minutos',
            ],
            'default' => 15,
            'class' => 'form-control',
            'required' => true,
            'id' => 'duracion_cita' // Agregamos un id
        ]) ?>
    </div>

    <!-- Campaña -->
    <div class="col-md-10 mx-auto mb-3">
        <?= $this->Form->control('campana_id', [
            'label' => 'Campaña',
            'options' => $campanas,
            'class' => 'form-control',
            'empty' => 'Seleccione una campaña',
        ]) ?>
    </div>

    <!-- Selección del Tipo de Cita -->
    <div class="col-md-10 mx-auto mb-3">
        <?= $this->Form->control('tipo', [
            'label' => 'Tipo de Cita',
            'class' => 'form-control',
            'options' => ['C' => 'Consulta', 'P' => 'Procedimiento'],
            'default' => 'C', // Consulta seleccionada por defecto
        ]) ?>
    </div>


    <!-- Selección de Tratamiento -->
    <div class="col-md-10 mx-auto mb-3">
        <label for="tratamiento-select">Tratamiento</label>
        <div class="input-group">
            <?= $this->Form->select('tratamiento_temp', $tratamientos, [
                'id' => 'tratamiento-select',
                'class' => 'form-control',
                'empty' => 'Selecciona un tratamiento'
            ]) ?>
            <button type="button" id="add-tratamiento" class="btn btn-primary">Agregar</button>
        </div>
    </div>
    
    <!-- Lista de tratamientos agregados -->
    <div class="col-md-10 mx-auto mb-3">
        <ul id="tratamientos-list" class="list-group"></ul>
    </div>
    
    <!-- Motivo de la Cita -->
    <div class="col-md-10 mx-auto mb-3">
        <?= $this->Form->control('motivo', [
            'label' => 'Motivo',
            'type' => 'textarea',
            'rows' => 3,
            'class' => 'form-control',
            'placeholder' => 'Ingrese el motivo de la cita',
        ]) ?>
    </div>

    <!-- Campo oculto para el estado -->
    <?= $this->Form->hidden('estado', ['value' => 'pendiente']) ?>

    <!-- Botones de acción -->
    <div class="col-12 text-center mt-4">
        <?= $this->Form->button(__('Guardar Cita'), ['class' => 'btn btn-info', 'id' => 'submitButton']) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary ms-2']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>

<script>
    // Manejo centralizado de tratamientos - igual que en edit.php
    const initializeTreatments = (context) => {
        const select = document.getElementById('tratamiento-select');
        const list = document.getElementById('tratamientos-list');
        const btnAdd = document.getElementById('add-tratamiento');

        if (!select || !list || !btnAdd) {
            console.log("⚠️ Faltan elementos para tratamientos");
            return;
        }

        // Limpiar eventos previos si ya existen
        btnAdd.removeEventListener('click', addTreatmentHandler);
        list.removeEventListener('click', removeTreatmentHandler);

        // Añadir el event listener para "Agregar tratamiento"
        btnAdd.addEventListener('click', addTreatmentHandler);

        // Añadir el event listener para "Eliminar tratamiento"
        list.addEventListener('click', removeTreatmentHandler);
    };

    function addTreatmentHandler() {
        const select = document.getElementById('tratamiento-select');
        const list = document.getElementById('tratamientos-list');
        const selectedId = select.value;
        const selectedText = select.options[select.selectedIndex].text;

        if (!selectedId) return;

        // Verificar si ya está en la lista
        const existingInput = list.querySelector(`input[value="${selectedId}"]`);
        if (existingInput) {
            alert('Este tratamiento ya fue agregado.');
            return;
        }

        // Crear nuevo ítem (igual que en edit.php)
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        li.innerHTML = `
            ${selectedText}
            <input type="hidden" name="citas_tratamientos[][tratamiento_id]" value="${selectedId}">
            <button type="button" class="btn btn-sm btn-danger remove-tratamiento">Eliminar</button>
        `;
        list.appendChild(li);

        select.value = ''; // Limpiar selección
    }

    function removeTreatmentHandler(e) {
        if (e.target.classList.contains('remove-tratamiento')) {
            e.target.closest('li').remove(); // Eliminar el li que contiene el botón
        }
    }
</script>

<script>
    $(document).ready(function() {
        // Recargar la página manteniendo los parámetros de la URL al cerrar el modal
        $(document).on('hidden.bs.modal', '#modalLg', function () {
            // Verificamos si la variable isChecking está declarada y la eliminamos
            if (typeof window.isChecking !== "undefined") {
                delete window.isChecking;  // Elimina la variable global isChecking
            }
            
            // Verificamos si la variable initializeFormLogic está declarada y la eliminamos
            if (typeof window.initializeFormLogic !== "undefined") {
                delete window.initializeFormLogic;  // Elimina la función global initializeFormLogic
            }
        });

        
    });
</script>



<script>
    
    var isChecking = false; // Flag para evitar llamadas múltiples a checkAvailability

function validateAndCheckAvailability() {
    if (isChecking) {
        return; // Si ya se está ejecutando, no hacer nada
    }
    
    isChecking = true; // Establecemos el flag para indicar que se está ejecutando

    return new Promise((resolve, reject) => {
        const doctorInput = document.getElementById('doctor_id');
        const fechaHoraInput = document.getElementById('fecha_hora');
        const duracionCitaSelect = document.getElementById('duracion_cita');

        const duracion = parseInt(duracionCitaSelect.value, 10);
        const fechaHoraOriginal = new Date(fechaHoraInput.value);
        let nuevaFechaHora = new Date(fechaHoraOriginal);

        console.log("🚦 Inicio de la validación de disponibilidad para la cita.");
        console.log("⏰ Hora original seleccionada:", fechaHoraOriginal);

        const intervals = [];
        if (duracion === 30) {
            intervals.push(15);
            console.log("⏳ Duración 30 minutos, ajustando en 15 minutos.");
        } else if (duracion === 45) {
            intervals.push(15);
            intervals.push(15);
            console.log("⏳ Duración 45 minutos, ajustando 15 minutos iniciales y un segundo ajuste.");
        } else if (duracion === 60) {
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            console.log("⏳ Duración 60 minutos, ajustando 15 minutos tres veces.");
        } else if (duracion === 75) {
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            console.log("⏳ Duración 75 minutos, ajustando 15 minutos cinco veces.");
        } else if (duracion === 90) {
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            console.log("⏳ Duración 90 minutos, ajustando 15 minutos 6 veces.");
        } else if (duracion === 105) {
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            console.log("⏳ Duración 105 minutos, ajustando 15 minutos siete veces.");
        } else if (duracion === 120) {
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            console.log("⏳ Duración 120 minutos, ajustando 15 minutos ocho veces.");
        } else if (duracion === 135) {
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            console.log("⏳ Duración 135 minutos, ajustando 15 minutos nueve veces.");
        } else if (duracion === 150) {
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            console.log("⏳ Duración 150 minutos, ajustando 15 minutos diez veces.");
        } else if (duracion === 165) {
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            console.log("⏳ Duración 165 minutos, ajustando 15 minutos once veces.");
        } else if (duracion === 180) {
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            intervals.push(15);
            console.log("⏳ Duración 180 minutos, ajustando 15 minutos doce veces.");   

        } else if (duracion === 15) {
            intervals.push(0);
            console.log("⏳ Duración 15 minutos, sin ajustes.");
        }

        let alertShown = false;
        let allAvailable = true;

        let validationPromises = intervals.map((interval, index) => {
            return new Promise((innerResolve, innerReject) => {
                if (interval > 0) {
                    nuevaFechaHora.setMinutes(nuevaFechaHora.getMinutes() + interval);
                    console.log(`⏰ Ajustando hora con ${interval} minutos (Iteración ${index + 1}):`, nuevaFechaHora);
                }

                const fechaString = nuevaFechaHora.toLocaleDateString('en-GB', {
                    timeZone: 'America/Lima',
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit'
                });

                const horaString = nuevaFechaHora.toLocaleTimeString('en-GB', {
                    timeZone: 'America/Lima',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });

                const nuevaFechaHoraString = `${fechaString.split('/').reverse().join('-')}T${horaString}`;

                console.log("📅 Verificando disponibilidad para la hora ajustada:", nuevaFechaHoraString);

                checkAvailability(doctorInput.value, nuevaFechaHoraString, duracionCitaSelect.value, function(isAvailable) {
                    if (!isAvailable) {
                        console.warn("❌ No disponible en la hora ajustada:", nuevaFechaHoraString);
                        allAvailable = false;
                    }
                    innerResolve(true);
                });
            });
        });

        Promise.allSettled(validationPromises)
            .then(() => {
                if (!allAvailable && !alertShown) {
                    alertShown = true;
                    alert("⚠️ La cita no puede ser de " + duracion + " minutos, ya está ocupada.");
                }

                console.log("📊 Resultados de las validaciones:", allAvailable ? "✅ Disponible" : "❌ No disponible");

                resolve(allAvailable);
                isChecking = false; // Una vez que todo haya terminado, se puede volver a ejecutar la validación
            })
            .catch(() => {
                console.error("❌ Hubo un error durante la validación.");
                resolve(false);
                isChecking = false;
            });
    });
}

function checkAvailability(doctorId, fechaHora, duracionCita, callback) {
    console.log("🔄 Validando disponibilidad...");
    
    $.ajax({
        url: '<?= $this->Url->build(["controller" => "Citas", "action" => "verificarDisponibilidad"]); ?>',
        type: 'GET',
        data: {
            doctor_id: doctorId,
            fecha_hora: fechaHora
        },
        success: function(response) {
            console.log("🔄 AJAX completado, disponibilidad verificada...");
            console.log("🔄 Respuesta del servidor:", response);
            if (response.disponible) {
                console.log("✅ Doctor y fecha disponibles.");
                if (callback) callback(true);
            } else {
                console.warn("⚠️ La nueva cita no puede ser de esta duración, ya está ocupada.");
                if (callback) callback(false);
            }
        },
        error: function(xhr, status, error) {
            console.error("⚠️ Error en la validación de disponibilidad:", error);
            if (callback) callback(false);
        }
    });
}



    var initializeFormLogic = (context) => {
        console.log("🔄 Inicializando lógica del formulario...");

        // Inicializar tratamientos
        initializeTreatments(context);

        const pacienteNuevoForm = document.getElementById('pacienteNuevoForm');
        const pacienteExistenteForm = document.getElementById('pacienteExistenteForm');
        const pacienteIdField = document.getElementById('pacienteId');
        const submitButton = document.getElementById('submitButton');
        const pacienteNuevoRadio = document.getElementById('pacienteNuevo');
        const pacienteExistenteRadio = document.getElementById('pacienteExistente');
        const searchPacienteField = document.getElementById('searchPaciente');
        const searchButton = document.getElementById('searchButton');
        const pacienteResults = document.getElementById('pacienteResults');
        const doctorInput = document.getElementById('doctor_id');
        const fechaHoraInput = document.getElementById('fecha_hora');
        const duracionCitaSelect = document.getElementById('duracion_cita');

        let fechaHora = fechaHoraInput ? fechaHoraInput.value : null;
        let doctorId = doctorInput ? doctorInput.value : null;

        console.log("📅 Fecha y hora seleccionada:", fechaHora);
        console.log("👨‍⚕️ Doctor seleccionado:", doctorId);
        //para validar el telefono longitud
        const isValidTelefono = (telefono) => {
            return /^\d{9}$/.test(telefono);
        };

        // Verificar que el select esté disponible
if (duracionCitaSelect) {
    console.log("Duración de la cita seleccionada:", duracionCitaSelect.value);

    // Detectar cambios en la selección de duración de la cita
    duracionCitaSelect.addEventListener('change', function() {
        console.log("Duración de la cita cambiada a:", duracionCitaSelect.value);
        // Llamar a la validación de disponibilidad nuevamente
        validateAndCheckAvailability();
    }, { once: true }); // El evento solo se ejecutará una vez
}
        if (!pacienteNuevoForm || !pacienteExistenteForm || !submitButton || !pacienteNuevoRadio || !pacienteExistenteRadio || !searchButton || !pacienteResults) {
            console.warn("⚠️ Faltan elementos en el formulario. Verifica los IDs en el HTML.");
            return; 
        }

        const togglePacienteForms = () => {
            console.log("🔄 Alternando formulario de paciente...");
            if (pacienteNuevoRadio.checked) {
                console.log("🆕 Modo: Paciente Nuevo");
                pacienteNuevoForm.style.display = 'block';
                pacienteExistenteForm.style.display = 'none';
                pacienteNuevoForm.querySelectorAll('input, select, textarea').forEach(input => {
                    input.removeAttribute('disabled');
                    input.setAttribute('required', 'required');
                });
                pacienteIdField.setAttribute('disabled', 'disabled');
                pacienteIdField.removeAttribute('required');
                pacienteIdField.value = '';
            } else {
                console.log("📄 Modo: Paciente Existente");
                pacienteNuevoForm.style.display = 'none';
                pacienteExistenteForm.style.display = 'block';
                pacienteNuevoForm.querySelectorAll('input, select, textarea').forEach(input => {
                    input.setAttribute('disabled', 'disabled');
                    input.removeAttribute('required');
                    input.value = '';
                });
                pacienteIdField.removeAttribute('disabled');
                pacienteIdField.setAttribute('required', 'required');
            }
            validateForm();
        };

        const validateForm = () => {
    console.log("✅ Validando formulario...");
    let isValid = true;

    if (pacienteExistenteRadio.checked) {
        if (!pacienteIdField.value.trim()) {
            isValid = false;
        }
    } else {
        pacienteNuevoForm.querySelectorAll('input:not([disabled])').forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
            }

            if (input.id === 'nuevo-paciente-telefono-celular' && !isValidTelefono(input.value.trim())) {
                isValid = false;
                input.setCustomValidity('El número debe tener exactamente 9 dígitos numéricos.');
            } else {
                input.setCustomValidity('');
            }
        });
    }

    submitButton.disabled = !isValid;
    console.log("🔍 Formulario válido:", isValid);
};


        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector("#pacienteNuevoForm") || document.querySelector("#pacienteExistenteForm");
            const submitButton = document.querySelector("#submitButton");

            if (form && submitButton) {
                submitButton.addEventListener("click", function (event) {
                    event.preventDefault(); // Evita que el formulario se envíe antes de la validación

                    const doctorInput = document.querySelector("#doctor_id");
                    const fechaHoraInput = document.querySelector("#fecha_hora");
                    const duracionCitaSelect = document.querySelector("#duracion_cita");

                    if (doctorInput.value && fechaHoraInput.value && duracionCitaSelect.value) {
                        // Validación AJAX y luego enviar si está disponible
                        validateAndCheckAvailability(function (isAvailable) {
                            if (isAvailable) {
                                const form = document.querySelector("#pacienteNuevoForm") || document.querySelector("#pacienteExistenteForm");
                                if (form) {
                                    form.submit(); // Enviar formulario si está disponible
                                } else {
                                    console.error("⚠️ El formulario no está disponible para enviar.");
                                }
                            } else {
                                console.warn("⛔ El doctor no está disponible en la fecha y hora seleccionada.");
                            }
                        });
                    } else {
                        console.error("⚠️ Faltan datos para validar.");
                    }
                });
            }
        });

        searchButton.addEventListener('click', function () {
            console.log("🔎 Buscando paciente...");
            // pacienteResults.innerHTML = `
            //     <a href="#" class="list-group-item list-group-item-action" data-id="1">Paciente 1 - Juan Pérez</a>
            //     <a href="#" class="list-group-item list-group-item-action" data-id="2">Paciente 2 - María López</a>
            // `;

            pacienteResults.querySelectorAll('a').forEach(item => {
                item.addEventListener('click', function (e) {
                    e.preventDefault();
                    console.log("✅ Paciente seleccionado:", item.textContent);
                    pacienteIdField.value = item.getAttribute('data-id');
                    searchPacienteField.value = item.textContent;
                    pacienteResults.innerHTML = '';
                    validateForm();
                });
            });
        });

        pacienteNuevoRadio.addEventListener('change', togglePacienteForms);
        pacienteExistenteRadio.addEventListener('change', togglePacienteForms);
        pacienteNuevoForm.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('input', validateForm);
        });
        pacienteIdField.addEventListener('input', validateForm);

        togglePacienteForms();
    };

    document.addEventListener('shown.bs.modal', function (event) {
    const modal = event.target;

    // Verifica si ya está inicializado antes de ejecutar la lógica
    if (!modal.classList.contains('initialized')) {
        console.log("🆕 Modal abierto, reinicializando formulario...");
        initializeFormLogic(modal.querySelector('.modal-content'));

        // Marca el modal como inicializado para evitar la re-inicialización
        modal.classList.add('initialized');
    } else {
        console.log("✅ Modal ya está inicializado.");
    }
});


document.addEventListener('DOMContentLoaded', function() {
    initializeFormLogic(document); // Solo al cargar la página
});

$(document).on('shown.bs.modal', '#modalCita', function () {
    initializeFormLogic($(this).find('.modal-content')); // Inicializa solo el contenido del modal
});

// $(document).on('ajaxComplete', function handler() {
//     console.log("🔄 AJAX completado, reinicializando formulario...");
//     initializeFormLogic(document);
//     $(document).off('ajaxComplete', handler);  // Desactiva el evento para que no se repita innecesariamente
// });
$(document).on('ajaxComplete', function() {
    console.log("🔄 AJAX completado, reinicializando formulario...");
    initializeFormLogic(document);
});

// Agregar el listener de eventos para la validación de disponibilidad al hacer clic en el botón de envío
document.getElementById('submitButton').addEventListener('click', function(event) {
    event.preventDefault(); // Detener envío inmediato

    validateAndCheckAvailability().then(function(isValid) {
        if (isValid) {
            const form = document.getElementById('formAgregarCita');
            if (form) {
                form.submit();
            } else {
                console.error("No se encontró el formulario.");
            }
        } else {
            alert("❌ La duración seleccionada no está disponible. Por favor, elija otra.");
            console.log("Validación fallida. El formulario no se enviará.");
        }
    });
});

// ✅ Ejecutar cuando la página cargue - aprender


    $(document).ready(function() {
        $(document).on("submit", "form", function(event) {
            const form = $(this)[0]; 
            const btnGuardar = $(this).find("#submitButton");

            if (!form.checkValidity()) {
                event.preventDefault(); // Evita el envío si hay errores en el formulario
                return;
            }

            btnGuardar.prop("disabled", true).text("Guardando...");
        });

        // Detecta cambios en los archivos dentro del modal o cualquier formulario
        $(document).on("change", "input[type='file']", function() {
            const btnGuardar = $(this).closest("form").find("#submitButton");
            if (this.files.length > 0) {
                btnGuardar.prop("disabled", false);
            }
        });
    });
</script>
