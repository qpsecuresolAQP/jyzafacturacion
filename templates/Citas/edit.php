<div class="container mt-4 mb-4">
    <div class="citas form content">
        <?= $this->Form->create($cita, ['class' => 'row g-3', 'id' => 'formEditarCita']) ?>

        <!-- Título del formulario -->
        <div class="col-12 mb-4">
            <h3 class="text-info"><i class="fas fa-calendar-plus"></i> Editar Cita</h3>
        </div>
        <input  type="hidden" id="cita-id" value="<?= h($cita->id) ?>">


        <!-- Selección de Doctor -->
        <div class="col-md-10 mx-auto mb-3">
            <?= $this->Form->control('doctor_id', [
                'label' => 'Doctor',
                'options' => $doctores,
                'class' => 'form-control',
                'required' => true,
                //   'id' => 'doctor_id',
            ]) ?>
        </div>

        <!-- Selección de Paciente -->
        <div class="col-md-10 mx-auto mb-3">
            <?= $this->Form->control('paciente_id', [
                'label' => 'Paciente',
                'options' => $Pacientes,
                'class' => 'form-control',
                'required' => true,
            ]) ?>
        </div>

        <!-- Fecha -->
        <div class="col-md-4 mx-auto mb-3">
            <?= $this->Form->control('fecha', [
                'label' => 'Fecha',
                'type' => 'date',
                'class' => 'form-control',
                'required' => true,
                'id' => 'fecha',
                'value' => isset($cita->fecha_hora) ? $cita->fecha_hora->format('Y-m-d') : '',
                'data-fecha-original' => isset($cita->fecha_hora) ? $cita->fecha_hora->format('Y-m-d') : ''
            ]) ?>
        </div>

        <!-- Hora -->
        <div class="col-md-4 mx-auto mb-3">
            <?= $this->Form->control('hora', [
                'label' => 'Hora',
                'type' => 'time',
                'class' => 'form-control',
                'required' => true,
                'id' => 'hora',
                'step' => '900', // Solo intervalos de 15 minutos
                'value' => isset($cita->fecha_hora) ? $cita->fecha_hora->format('H:i') : '',
                'data-hora-original' => isset($cita->fecha_hora) ? $cita->fecha_hora->format('H:i') : ''
            ]) ?>
        </div>
        
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
        'class' => 'form-control',
        'required' => true,
        'id' => 'duracion_cita',
        'data-original-duration' => $cita->duracion_minutos // Duración original de la DB
    ]) ?>
</div>
        <!-- campo campaña-->
        <div class="col-md-10 mx-auto mb-3">
            <?= $this->Form->control('campana_id', [
                'label' => 'Campaña',
                'options' => $campanas,
                'class' => 'form-control',
                'empty' => 'Selecciona una campaña',
                'required' => false,
            ]) ?>
        </div>

        <!-- Selección del Tipo de Cita -->
        <div class="col-md-10 mx-auto mb-3">
            <?= $this->Form->control('tipo', [
                'label' => 'Tipo de Cita',
                'class' => 'form-control',
                'options' => ['C' => 'Consulta', 'P' => 'Procedimiento'],
                'required' => true
            ]) ?>
        </div>

        <!-- Motivo de la Cita -->
        <div class="col-md-10 mx-auto mb-3">
            <?= $this->Form->control('motivo', [
                'label' => 'Motivo',
                'class' => 'form-control',
            ]) ?>
        </div>
       <!-- hora de llegada -->
        <div class="col-md-4 mx-auto mb-3"> 
            <?= $this->Form->control('hora_llegada', [
                'label' => 'Hora de Llegada',
                'type' => 'time',
                'class' => 'form-control',
                'required' => false,
                'id' => 'hora_llegada',
                'value' => isset($cita->hora_llegada) && $cita->hora_llegada !== null 
                    ? $cita->hora_llegada->format('H:i') 
                    : null, // Sin valor si no tiene hora llegada
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

        <!-- Tratamientos -->
        <div class="col-md-10 mx-auto mb-3">
                    <ul id="tratamientos-list" class="list-group">
<?php foreach ($tratamientosAsociados as $asociado): ?>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <?= h($asociado->tratamiento->nombre) ?>
        <input type="hidden" name="citas_tratamientos[][tratamiento_id]" value="<?= $asociado->tratamiento_id ?>">
        <button type="button" class="btn btn-sm btn-danger remove-tratamiento">Eliminar</button>
    </li>
<?php endforeach; ?>
</ul>
        </div>

        <!-- Estado de la Cita -->
        <div class="col-md-10 mx-auto mb-3">
            <?= $this->Form->control('estado', [
                'label' => 'Estado',
                'options' => [
                    'pendiente' => 'Citado',
                    'confirmado' => 'Confirmado',
                    'cancelado' => 'Cancelado',
                    'en_consultorio' => 'En Consultorio',
                    'en_recepcion' => 'En Recepción',
                    'en_cabina' => 'En Cabina',
                    'programar' => 'Programar',
                    'sos' => 'SOS',
                    'finalizado' => 'Finalizado',
                ],
                'class' => 'form-control',
                'required' => true,
                'default' => $cita->estado, // Estado actual preseleccionado
                'empty' => false, // Evita la opción vacía en la lista desplegable
            ]) ?>
        </div>


        <!-- Campo oculto para enviar la fecha y hora combinadas -->
        <?= $this->Form->hidden('fecha_hora', ['id' => 'fechaHora']) ?>

        <!-- Botones de acción -->
        <div class="col-12 text-center mt-4">
            <?= $this->Form->button(__('Guardar Cambios'), ['class' => 'btn btn-info', 'id' => 'submitButton']) ?>
            <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary ms-2']) ?>
        </div>

        <?= $this->Form->end() ?>
    </div>
</div>
<script>
    function initializeTratamientos() {
    const select = document.getElementById('tratamiento-select');
    const list = document.getElementById('tratamientos-list');
    const btnAdd = document.getElementById('add-tratamiento');

    if (!select || !list || !btnAdd) return; // Si no existen los elementos, salir

    // Limpiar eventos previos si ya existen
    btnAdd.removeEventListener('click', addTreatmentHandler);
    list.removeEventListener('click', removeTreatmentHandler);

    // Añadir el event listener para "Agregar tratamiento"
    btnAdd.addEventListener('click', addTreatmentHandler);

    // Añadir el event listener para "Eliminar tratamiento"
    list.addEventListener('click', removeTreatmentHandler);
}

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

    // Crear nuevo ítem
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
    function verificaDuracionCita(fechaHora, doctorId) {
        
        const citaIdInput = document.querySelector("#cita-id");
        const citaId = citaIdInput ? citaIdInput.value : null;
        return $.ajax({
            url: '<?= $this->Url->build(["controller" => "Citas", "action" => "verificarDisponibilidad"]); ?>',
            method: 'GET',
            data: {
                fecha_hora: fechaHora,
                doctor_id: doctorId,
                cita_id: citaId // Añadido el citaId a los parámetros de la solicitud
            }
        }).then(response => {
            return response.disponible; // Retorna `true` si está disponible, `false` si está ocupado
        }).catch(error => {
            return false; // En caso de error, asumimos que no está disponible
        });
    }
    function verificarDuracion(fechaHora, doctorId) {
        

        return $.ajax({
            url: '<?= $this->Url->build(["controller" => "Citas", "action" => "verificarDisponibilidad"]); ?>',
            method: 'GET',
            data: {
                fecha_hora: fechaHora,
                doctor_id: doctorId,
            }
        }).then(response => {
            const disponible = response.disponible;
            return disponible; // Retorna true o false
        }).catch(error => {
            
            return false; // En caso de error, asumimos que no está disponible
        });
    }



function initializeForm() {
    initializeTratamientos();

    // Obtener elementos del formulario y validar su existencia
    const fechaInput = document.querySelector('#fecha');
    const horaInput = document.querySelector('#hora');
    const form = document.querySelector('#formEditarCita');
    const submitButton = document.querySelector('#submitButton');
    const fechaHoraInput = document.querySelector('#fechaHora');
    const doctorInput = document.querySelector('#doctor-id');
    const duracionCitaSelect = document.getElementById('duracion_cita');

    let validacionDuracionE = true;
//    aqui inicia la validacion

if (duracionCitaSelect) {
    // 🛠️ Función para formatear fecha y hora en formato ISO local
    function formatFechaHoraLocal(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        const h = String(date.getHours()).padStart(2, '0');
        const min = String(date.getMinutes()).padStart(2, '0');
        const s = String(date.getSeconds()).padStart(2, '0');
        return `${y}-${m}-${d}T${h}:${min}:${s}`;
    }

    // 🛠️ Función para verificar disponibilidad y mostrar alerta si no está disponible
    function verificarYMostrarAlerta(fechaHora, doctorId) {
        return verificarDuracion(fechaHora, doctorId).then(disponible => {
            if (!disponible) {
                
            }
            return disponible;
        });
    }
    function verificarYMostrarAlertaCambio(fechaHora, doctorId) {
        return verificarDuracionCita(fechaHora, doctorId).then(disponible => {
            if (!disponible) {
                
            }
            return disponible;
        });
    }

    // 🎯 Evento para cuando cambia la duración
    duracionCitaSelect.addEventListener('change', function () {
    ejecutarValidacion();
});
    
        function ejecutarValidacion() {
        const duracionNueva = parseInt(duracionCitaSelect.value, 10);
        const duracionAntigua = parseInt(duracionCitaSelect.dataset.originalDuration, 10);

        const fechaSeleccionada = fechaInput.value;
        const horaSeleccionada = horaInput.value.slice(0, 5); // Solo 'HH:mm'
        const fechaOriginal = fechaInput.dataset.fechaOriginal;
        const horaOriginal = horaInput.dataset.horaOriginal;
        const doctorId = document.getElementById('doctor-id').value;


        const fechaHoraSelec = `${fechaSeleccionada}T${horaSeleccionada.endsWith(":00") ? horaSeleccionada : horaSeleccionada + ":00"}`;
        

        const mismaFechaHora = fechaSeleccionada === fechaOriginal && horaSeleccionada === horaOriginal;
        const fechaHoraBase = new Date(fechaHoraSelec);

        if (mismaFechaHora) {
            

            if (duracionNueva > duracionAntigua) {
                const diferencia = duracionNueva - duracionAntigua;
                

                const promises = [];

                const bloquesPorDuracion = {
                    15: [],
                    30: [15],
                    45: [15, 30],
                    60: [15, 30, 45],
                    75: [15, 30, 45, 60],
                    90: [15, 30, 45, 60, 75],
                    105: [15, 30, 45, 60, 75, 90],
                    120: [15, 30, 45, 60, 75, 90, 105],
                    135: [15, 30, 45, 60, 75, 90, 105, 120],
                    150: [15, 30, 45, 60, 75, 90, 105, 120, 135],
                    165: [15, 30, 45, 60, 75, 90, 105, 120, 135, 150],
                    180: [15, 30, 45, 60, 75, 90, 105, 120, 135, 150, 165],
                };
                const bloquesOriginal = bloquesPorDuracion[duracionAntigua] || [];
                    const bloquesNueva = bloquesPorDuracion[duracionNueva] || [];

                    // Solo los bloques que antes no existían
                    const nuevosBloques = bloquesNueva.filter(min => !bloquesOriginal.includes(min));

                    for (const minutos of nuevosBloques) {
                        const nuevaFecha = new Date(fechaHoraBase);
                        nuevaFecha.setMinutes(nuevaFecha.getMinutes() + minutos);
                        const formateada = formatFechaHoraLocal(nuevaFecha);
                       
                        promises.push(verificarYMostrarAlerta(formateada, doctorId));
                    }

                Promise.all(promises).then(results => {
                    if (results.some(result => result === false)) {
                        validacionDuracionE = false;
                        alert(`⚠️ Una o más franjas no están disponibles para la fecha y hora seleccionada.`);
                        
                    } else {
                        validacionDuracionE = true; 
                        
                    }
                });
            } else {
                validacionDuracionE = true;
                
            }

        } else {
            

            const bloques = duracionNueva / 15;
            const promises = [];

            for (let i = 1; i < bloques; i++) {
                const nuevaFecha = new Date(fechaHoraBase);
                nuevaFecha.setMinutes(nuevaFecha.getMinutes() + 15 * i);
                const formateada = formatFechaHoraLocal(nuevaFecha);
                
                promises.push(verificaDuracionCita(formateada, doctorId));
            }

            if (bloques === 1) {
                validacionDuracionE = true;
                
            } else {
                Promise.all(promises).then(results => {
                    if (results.some(r => r === false)) {
                        validacionDuracionE = false;
                        alert('⚠️ Una o más franjas no están disponibles para este nuevo horario.');
                    } else {
                        validacionDuracionE = true;
                        
                    }
                });
            }
        }
    

    // fin de la ejecutarvalidacion
    }
}


// aqui termina la validacion de duracion

    if (!fechaInput || !horaInput || !form || !submitButton || !fechaHoraInput || !doctorInput) {
        
        return;
    }

    
    doctorInput.addEventListener('change', function () {
        
        cargarHorariosDoctor();
    });
    // ✅ Obtener la fecha actual en la zona horaria de Perú (UTC-5)
    const today = new Date();
    const options = { timeZone: 'America/Lima', year: 'numeric', month: '2-digit', day: '2-digit' };
    const minDatePeru = new Intl.DateTimeFormat('en-GB', options).format(today); // Formato dd/mm/yyyy

    // Convertir "31/01/2025" a "2025-01-31"
    const [day, month, year] = minDatePeru.split('/');
    const minDateFormatted = `${year}-${month}-${day}`; // Formato YYYY-MM-DD
    fechaInput.setAttribute('min', minDateFormatted);

    let horariosDoctor = []; // Lista de horarios disponibles

    // ✅ Función para cargar los horarios del doctor
    function cargarHorariosDoctor() {
        const doctorId = doctorInput.value;
        if (!doctorId) {
            console.warn("⚠️ No se encontró un doctor asignado.");
            return;
        }

        

        $.ajax({
            url: '<?= $this->Url->build(["controller" => "HorariosDoctores", "action" => "obtenerHorarios"]); ?>',
            method: 'GET',
            data: { doctor_id: doctorId },
            dataType: 'json',
            success: function (response) {
               

                if (response.success && Array.isArray(response.horarios) && response.horarios.length > 0) {
                    horariosDoctor = response.horarios.map(horario => ({
                        dia: horario.dia_semana,
                        inicio: horario.hora_inicio,
                        fin: horario.hora_fin
                    }));

                    
                } else {
                    horariosDoctor = [];
                    
                    alert('No hay horarios configurados para este doctor.');
                }
            },
            error: function (xhr, status, error) {
                
                alert('Error al cargar los horarios del doctor.');
            }
        });
    }

    // ✅ Ejecutar la carga de horarios cuando la página cargue
    cargarHorariosDoctor();

    // ✅ Función para obtener el día de la semana (0 = Domingo, 6 = Sábado)
    // ✅ Función para obtener el día de la semana en la zona horaria de Lima
function obtenerDiaSemana(fecha) {
    const fechaUTC = new Date(`${fecha}T00:00:00Z`); // Convertimos la fecha a UTC
    const opciones = { weekday: 'short', timeZone: 'America/Lima' }; // Aseguramos zona horaria de Lima
    const diaSemanaTexto = new Intl.DateTimeFormat('es-PE', opciones).format(fechaUTC); // "lun", "mar", etc.

    // Convertir texto a índice numérico (0 = Domingo, 6 = Sábado)
    const diasSemana = { "dom": 1, "lun": 2, "mar": 3, "mié": 4, "jue": 5, "vie": 6, "sáb": 0 };
    return diasSemana[diaSemanaTexto.toLowerCase()];
}

function validarHorario() {
    const fechaSeleccionada = fechaInput.value; // Fecha YYYY-MM-DD
    const horaSeleccionada = horaInput.value; // Hora HH:mm

    if (!fechaSeleccionada || !horaSeleccionada) return;

    const diaSemana = obtenerDiaSemana(fechaSeleccionada);


    // ✅ Buscar si el doctor atiende en ese día
     const horariosDelDia = horariosDoctor.filter(horario => horario.dia === diaSemana);

    if (horariosDelDia.length === 0) {
        alert("⛔ El doctor no atiende este día.");
        fechaInput.focus();
        return;
    }

    // ✅ Convertimos las horas a comparación
 
 const horaSeleccionadaFormateada = horaSeleccionada + ":00";

    // ✅ Verificar si la hora seleccionada cae en algún rango
    const estaDentroDeUnHorario = horariosDelDia.some(horario => {
        return horaSeleccionadaFormateada >= horario.inicio && horaSeleccionadaFormateada <= horario.fin;
    });

    if (!estaDentroDeUnHorario) {
        const rangos = horariosDelDia.map(h => `${h.inicio} a ${h.fin}`).join('\n');
        alert(`⛔ El doctor solo atiende en estos rangos:\n${rangos}`);
        return;
    }

}


    // ✅ Escuchar cambios en los campos
    // fechaInput.addEventListener("change", validarHorario);
    // horaInput.addEventListener("blur", validarHorario);
    // Mantener validación actual para fecha y hora
fechaInput.addEventListener("change", function () {
    validarHorario();   // Mantener la validación de horario al cambiar la fecha
    ejecutarValidacion();  // Ejecutar la validación adicional para la fecha y hora
});

horaInput.addEventListener("blur", function () {
    validarHorario();   // Mantener la validación de horario al perder el enfoque en la hora
    ejecutarValidacion();  // Ejecutar la validación adicional para la fecha y hora
});

    // ✅ Validación al hacer clic en "Guardar"
    submitButton.addEventListener('click', function (event) {
        event.preventDefault(); // Evitar envío antes de validar
        if (!validacionDuracionE) {
            alert('⚠️ La duración seleccionada no es válida.');
            return;
        }
        const selectedDate = fechaInput.value; // Fecha YYYY-MM-DD
        const selectedTime = horaInput.value; // Hora HH:mm

        if (!selectedDate || !selectedTime) {
            alert('❌ Por favor, selecciona una fecha y hora.');
            return;
        }

        // ✅ Validar que la fecha no sea menor a hoy
        if (selectedDate < minDateFormatted) {
            alert('❌ Por favor, selecciona una fecha futura.');
            fechaInput.focus();
            return;
        }

        // ✅ Validar intervalo de 15 minutos
        const [hour, minute] = selectedTime.split(':').map(num => parseInt(num, 10));
        if (![0, 15, 30, 45].includes(minute)) {
            alert('❌ Selecciona una hora en intervalos de 15 minutos (ejemplo: 9:00, 9:15, 9:30).');
            horaInput.focus();
            return;
        }

        // ✅ Validar que la hora esté dentro del horario permitido
       const diaSemana = obtenerDiaSemana(selectedDate);
const horariosDelDia = horariosDoctor.filter(horario => horario.dia === diaSemana);

const horaSeleccionadaFormat = selectedTime + ":00";

const estaDentroDeHorario = horariosDelDia.some(horario =>
    horaSeleccionadaFormat >= horario.inicio && horaSeleccionadaFormat <= horario.fin
);

if (!estaDentroDeHorario) {
    const rangos = horariosDelDia.length > 0
        ? horariosDelDia.map(h => `${h.inicio} a ${h.fin}`).join('\n')
        : "Ninguno";

    alert(`❌ La hora seleccionada no está disponible. El doctor atiende en los siguientes rangos:\n${rangos}`);

    setTimeout(() => {
        horaInput.focus();
    }, 100);

    return;
}

        
        // ✅ Verificar disponibilidad en la base de datos antes de enviar
        const fechaHora = `${selectedDate}T${selectedTime.endsWith(":00") ? selectedTime : selectedTime + ":00"}`;

        const citaIdInput = document.querySelector("#cita-id");
        const citaId = citaIdInput ? citaIdInput.value : null;

        if (!citaId) {
            
        }
        

        verificarDisponibilidad(fechaHora, doctorInput.value)
            .then(disponible => {
                if (!disponible) {
                    alert("❌ La hora seleccionada ya está ocupada. Por favor, elige otra.");
                    setTimeout(() => {
                        horaInput.focus();
                    }, 100);
                    return;
                }

                // ✅ Si todo está bien, asignar la fecha y enviar el formulario
                fechaHoraInput.value = fechaHora;
                
                
                setTimeout(() => form.submit(), 100);
            })
            .catch(error => console.error("Error verificando disponibilidad:", error));
    });
    // 🔍 Función para verificar disponibilidad con AJAX
    function verificarDisponibilidad(fechaHora, doctorId) {
        
        const citaIdInput = document.querySelector("#cita-id");
        const citaId = citaIdInput ? citaIdInput.value : null;
        return $.ajax({
            url: '<?= $this->Url->build(["controller" => "Citas", "action" => "verificarDisponibilidad"]); ?>',
            method: 'GET',
            data: {
                fecha_hora: fechaHora,
                doctor_id: doctorId,
                cita_id: citaId // Añadido el citaId a los parámetros de la solicitud
            }
        }).then(response => {
            return response.disponible; // Retorna `true` si está disponible, `false` si está ocupado
        }).catch(error => {
            
            return false; // En caso de error, asumimos que no está disponible
        });
    }

   
}

// ✅ Ejecutar cuando la página cargue
document.addEventListener('DOMContentLoaded', initializeForm);

// ✅ Ejecutar cuando el modal se abre
$(document).on('shown.bs.modal', '#modalCita', initializeForm);

// ✅ Re-ejecutar tras cualquier carga AJAX
// $(document).on('ajaxComplete', initializeForm);
$(document).on('ajaxComplete', function handler() {
    initializeForm();
    $(document).off('ajaxComplete', handler); // 🔴 Removemos el evento después de ejecutarlo una vez
});

</script>

