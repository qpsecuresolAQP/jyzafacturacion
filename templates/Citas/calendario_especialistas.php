<link rel="stylesheet" href="<?= $this->Url->assetUrl('plugins/jquery-ui/jquery-ui.min.css') ?>">
<script src="<?= $this->Url->assetUrl('plugins/jquery-ui/jquery-ui.min.js') ?>"></script>
<?= $this->Html->script('https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js?v=' . time()) ?>
<?= $this->Html->charset() ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="csrfToken" content="<?= $this->request->getAttribute('csrfToken');
?>">


<div class="container-fluid">


        <div class="row align-items-start g-3">
            <!-- Selectores -->
            <div class="col-lg-4 order-lg-1 order-1 ">
                <!-- Selector de fecha -->
                <div class="form-group">
                    <label for="datePicker"><strong>Seleccionar fecha:</strong></label>
                    <input type="date" id="datePicker" class="form-control"
                                 value="<?= $fecha ?? date('Y-m-d') ?>">
                </div>

                <!-- Información de la fecha seleccionada -->
                <div class="alert alert-info" role="alert">
                        <small id="fechaInfo"></small>
                </div>
            </div>

            <!-- Mini-calendarios -->
            <div class="col-lg-4 order-lg-2 order-2">
                <div id="miniCalendars" class="calendars-container"></div>
            </div>
        </div>

    <!-- Leyenda de estados -->
    <div class="form-group">
        <label><strong>Estados de las citas:</strong></label>
        <div class="estado-leyenda">
            <div class="estado-item">
                <span class="estado-color bg-citado"></span> Citado
            </div>
            <div class="estado-item">
                <span class="estado-color bg-confirmado"></span> Confirmado
            </div>
            <div class="estado-item">
                <span class="estado-color bg-recepcion"></span> En Recepcion
            </div>
            <div class="estado-item">
                <span class="estado-color bg-consultorio"></span> En Consultorio
            </div>
            <div class="estado-item">
                <span class="estado-color bg-cabina"></span> En Cabina
            </div>
            <div class="estado-item">
                <span class="estado-color bg-finalizado"></span> Finalizado
            </div>
            <div class="estado-item">
                <span class="estado-color bg-programar"></span> Programar
            </div>
            <div class="estado-item">
                <span class="estado-color bg-cancelado"></span> Cancelado
            </div>
            <div class="estado-item">
                <span class="estado-color bg-sos"></span> SOS
            </div>
        </div>
    </div>

    <a href="#" class="openModal" style="display: none;"></a>

    <!-- Contenedor del calendario con scroll horizontal -->
    <div class="calendar-container">
        <table class="table table-bordered table-sm" id="calendarioEspecialistas">
            <thead>
                <tr id="headerRow">
                    <th class="hora-column"><strong>Hora</strong></th>
                    <!-- Las columnas de especialistas se generarán dinámicamente con JS -->
                </tr>
            </thead>
            <tbody id="tableBody">
                <!-- Las filas se generarán dinámicamente con JS -->
            </tbody>
        </table>
    </div>

</div>

<!-- Menú contextual -->
<div id="contextMenu" class="dropdown-menu" style="display: none; position: absolute;">
    <a class="dropdown-item view-history" href="#">Ver Historia</a>
    <a class="dropdown-item ver-appointment" href="#">Ver Cita</a>
    <a class="dropdown-item edit-appointment" href="#">Editar Cita</a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item confirmado-appointment" href="#">Confirmado</a>
    <a class="dropdown-item recepcion-appointment" href="#">En Recepcion</a>
    <a class="dropdown-item consultorio-appointment" href="#">En Consultorio</a>
    <a class="dropdown-item cabina-appointment" href="#">En Cabina</a>
    <a class="dropdown-item programar-appointment" href="#">Programar</a>
    <a class="dropdown-item sos-appointment" href="#">SOS</a>
    <a class="dropdown-item finalizado-appointment" href="#">Finalizado</a>
    <a class="dropdown-item cancelado-appointment" href="#">Cancelado</a>
    <a class="dropdown-item reset-status" href="#">Restablecer Cita</a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item marcar-hora-llegada" href="#">Marcar Hora de Llegada</a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item whatsapp-confirmation" href="#">Confirmación por WhatsApp</a>
    <a class="dropdown-item whatsapp-message" href="#">Escribir por WhatsApp</a>
</div>

<!-- Estilos CSS -->
<style>

    /* Contenedor principal del calendario */
    .calendar-container {
        margin-top: 20px;
    }

    .calendar-container table {
        width: 100%;
        table-layout: fixed;
    }

    /* Encabezado sticky */
    #calendarioEspecialistas thead {
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 20;
    }

    #calendarioEspecialistas thead th {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    .table-bordered {
        border: 1px solid #adcbe2; /* azul medio */
    }

    .table-bordered td,
    .table-bordered th {
        border: 1px solid #afd0e5;
    }

    /* Contenedor de la leyenda */
    .estado-leyenda {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 5px;
    }

    /* Elemento de la leyenda */
    .estado-item {
        display: flex;
        align-items: center;
        font-size: 14px;
    }

    /* Icono de estado */
    .estado-color {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 5px;
    }

    /* Colores de estado */
    .bg-citado { background-color: rgba(212, 163, 32, 0.85); }
    .bg-confirmado { background-color: rgba(75, 192, 192, 0.7); }
    .bg-consultorio { background-color: rgba(153, 102, 255, 0.7); }
    .bg-finalizado { background-color: #808080; }
    .bg-cancelado {  background-color: rgba(220, 53, 69, 0.85); }
    .bg-recepcion { background-color: rgba(230, 126, 34, 0.85); }
    .bg-programar { background-color: rgba(255, 99, 132, 0.7);}
    .bg-cabina { background-color: rgba(0, 123, 115, 0.85);}
    .bg-sos {  background-color: rgba(220, 53, 69, 0.85); }

    /* Celdas del calendario */
    .calendar-cell {
        position: relative;
        height: 40px;
        cursor: pointer;
        text-align: center;
        vertical-align: top;
        font-size: 12px;
        padding: 5px;
        min-width: 0;
        overflow: visible;
    }

    /* Contenedor de la tabla - necesario para posicionamiento absolute */
    #tableBody {
        position: relative;
    }

    /* Estilo general de la tabla */
    .table th, .table td {
        text-align: center;
        vertical-align: middle;
    }

    #calendarioEspecialistas th:not(.hora-column),
    #calendarioEspecialistas td.calendar-cell {
        min-width: 0;
        overflow: visible;
    }

    #calendarioEspecialistas th:not(.hora-column) {
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    /* Columna de horas */
    .hora-column {
        min-width: 80px;
        max-width: 80px;
        position: sticky;
        left: 0;
        background-color: #f8f9fa;
        font-weight: bold;
        z-index: 10;
    }

    .calendar-cell {
        position: relative;
    }

    /* Elementos de citas dentro de las celdas */
    .cita-item {
        margin: 2px 0;
        padding: 5px;
        border-radius: 4px;
        font-size: 11px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: normal;
        color: white;
        font-weight: bold;
        cursor: pointer;
        display: block;
        position: relative;
        word-break: break-word;
        z-index: 20;
    }
    .cita-item strong {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%;
    }
    .cita-item.ui-draggable-dragging {
        opacity: 0.7;
        z-index: 9999;
    }
    .calendar-cell.ui-droppable-hover {
        background: #b2f7b8 !important;
    }

    .horario-disponible {
        background-color: #D0E8F2 !important;
        font-weight: bold;
    }

    .horario-bloqueado {
        background-color: rgba(222, 227, 230, 0.8) !important;
        font-weight: bold;
    }

    .cita-ocupada {
        position: relative;
    }

    /* Celdas que continúan una cita multicelda */
    .cita-ocupada-continuacion {
        background-color: rgba(200, 200, 200, 0.1);
        border-top: 0 !important;
        border-bottom: 0 !important;
    }

    /* Celdas que forman parte de una cita multicelda */
    .cita-ocupada:not(:has(.cita-item)) {
        background-color: rgba(200, 200, 200, 0.1);
        border-top: 0;
        border-bottom: 0;
    }

    /* Colores y estilos para los diferentes estados de las citas */
    .estado-pendiente {
        background-color: rgba(212, 163, 32, 0.85);
    }

    .estado-confirmado {
        background-color: rgba(34, 138, 138, 0.7);
    }

    .estado-consultorio {
        background-color: rgba(130, 91, 207, 0.7);
    }

    .estado-finalizado {
        background-color: #808080;
    }

    .estado-recepcion {
        background-color: rgba(230, 126, 34, 0.85);
    }

    .estado-programar {
        background-color: rgba(255, 99, 132, 0.7);
    }

    .estado-cabina {
        background-color: rgba(0, 123, 115, 0.85);
        color: white;
    }

    .estado-sos {
        background-color: rgba(220, 53, 69, 0.85);
    }

    .estado-cancelado {
        display: none;
    }

    /* Hora de llegada */
    .hora-llegada {
        display: inline-block;
        padding: 1px 5px;
        border-radius: 3px;
        color: white;
        text-align: center;
        cursor: pointer;
        font-size: 10px;
        margin-top: 2px;
    }

    /* Menú contextual */
    #contextMenu {
        display: none;
        position: absolute;
        z-index: 1000;
        background: white;
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        font-size: 14px;
        margin-top:-28px;
    }

    #contextMenu a {
        display: block;
        color: #333;
        padding: 5px 10px;
        text-decoration: none;
    }

    #contextMenu a:hover {
        background: #f0f0f0;
    }

    /* Media query para dispositivos pequeños */
    @media (max-width: 768px) {
        .calendar-cell {
            height: 50px;
            font-size: 11px;
            min-width: 100px;
        }

        .cita-item {
            font-size: 10px;
        }

        .hora-column {
            min-width: 70px;
            max-width: 70px;
            font-size: 12px;
        }

        #contextMenu {
            font-size: 12px;
        }
    }

    @media (max-width: 576px) {
        .calendar-cell {
            font-size: 9px;
            height: 40px;
            min-width: 80px;
        }

        .cita-item {
            font-size: 8px;
            padding: 3px;
        }

        .hora-column {
            min-width: 60px;
            max-width: 60px;
            font-size: 10px;
        }

        #contextMenu {
            font-size: 10px;
        }
    }

    /* Minicalendarios */
    .calendars-container {
      display: flex;
      flex-wrap: nowrap;
      gap: 12px;
      margin-top: 10px;
    }

    .mini-calendar {
      font-family: Arial, sans-serif;
      font-size: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      padding: 6px;
      width: 140px;
      flex-shrink: 0;
    }

    .mini-calendar .mc-header {
      text-align: center;
      font-weight: 600;
      margin-bottom: 6px;
      text-transform: capitalize;
      font-size: 12px;
    }

    .mini-calendar .mc-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 2px;
    }

    .mini-calendar .mc-dow,
    .mini-calendar .mc-day,
    .mini-calendar .mc-blank {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 22px;
      border-radius: 4px;
    }

    .mini-calendar .mc-dow {
      font-size: 10px;
      font-weight: 600;
    }

    .mini-calendar .mc-day {
      cursor: pointer;
      transition: background 0.2s;
      font-size: 11px;
    }

    .mini-calendar .mc-day:hover {
      background: #0d6efd;
      color: #fff;
    }

    .mini-calendar .mc-blank {
      visibility: hidden;
      pointer-events: none;
    }

    /* Día seleccionado */
    .mini-calendar .mc-selected {
      background: #198754 !important;
      color: #fff !important;
      font-weight: bold;
      border: 1px solid #145c32;
    }

    /* Día actual (hoy en Lima) */
    .mini-calendar .mc-today {
      border: 1px solid #0d6efd;
      font-weight: bold;
    }

    /* --- Responsivo --- */
    @media (max-width: 576px) {
      .mini-calendar {
        width: 132px;
        font-size: 10px;
        margin-bottom: 15px;
      }

      .mini-calendar .mc-header {
        font-size: 11px;
      }

      .mini-calendar .mc-dow,
      .mini-calendar .mc-day {
        font-size: 9px;
        height: 20px;
      }
    }

</style>

<script>
    $(document).ready(function () {
        // Los permisos se leen desde la BD a través de $rolesPermitidosIds del controller
        const rolesPermitidos = <?= json_encode($rolesPermitidosIds); ?>;
        const userRole = <?= json_encode($usuarioRolId); ?>;

        // Horarios de atención por doctor (id => [{dia_semana, hora_inicio, hora_fin}, ...]), usado para validar drag & drop
        let horariosPorDoctor = {};

        // Detectar dispositivo táctil para deshabilitar el drag and drop (evita mover citas por error en móvil/tablet)
        const esDispositivoTactil = ('ontouchstart' in window) || navigator.maxTouchPoints > 0;

        // Verifica que la hora de inicio de la cita esté dentro de algún bloque de horario del doctor
        function citaCabeEnHorario(doctorId, diaSemana, horaInicio) {
            const horarios = horariosPorDoctor[doctorId] || [];
            return horarios.some(horario =>
                horario.dia_semana == diaSemana &&
                horaInicio >= horario.hora_inicio &&
                horaInicio <= horario.hora_fin
            );
        }

        // Pequeño delay para asegurar que Sortable.js esté listo
        setTimeout(function() {
            const fechaInicial = $('#datePicker').val();
            actualizarCalendario(fechaInicial);

            $('#datePicker').on('change', function () {
                const fechaSeleccionada = $(this).val();
                actualizarCalendario(fechaSeleccionada);
            });

        }, 100);

        // Función para actualizar el calendario con especialistas y citas
        function actualizarCalendario(fechaSeleccionada) {
            if (!fechaSeleccionada) return;

            const fechaObj = new Date(`${fechaSeleccionada}T00:00:00`);
            const opcionesFormato = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const fechaFormateada = fechaObj.toLocaleDateString('es-ES', opcionesFormato);
            $('#fechaInfo').text(`Mostrando citas del ${fechaFormateada}`);

            $.ajax({
                url: '<?= $this->Url->build(["controller" => "Citas", "action" => "fetchEspecialistas"]); ?>',
                type: 'GET',
                data: { fecha: fechaSeleccionada },
                success: function (data) {
                    if (!data.especialistas || !Array.isArray(data.especialistas)) {
                        console.error("Error: La respuesta no contiene especialistas válidos", data);
                        alert('Error al cargar los especialistas.');
                        return;
                    }

                    $('#headerRow').find('th:not(.hora-column)').remove();
                    $('#tableBody').empty();

                    const especialistas = data.especialistas;
                    const citas = data.citas || [];

                    especialistas.forEach(function (especialista) {
                        const th = $(`<th data-doctor-id="${especialista.id}" title="${especialista.nombre}">${especialista.nombre}</th>`);
                        $('#headerRow').append(th);
                    });

                    for (let hour = 8; hour <= 19; hour++) {
                        for (let minute = 0; minute < 60; minute += 15) {
                            const timeString = String(hour).padStart(2, '0') + ':' + String(minute).padStart(2, '0');
                            const tr = $(`<tr data-hour="${hour}" data-minute="${minute}"></tr>`);

                            tr.append(`<td class="hora-column">${timeString}</td>`);

                            especialistas.forEach(function (especialista) {
                                const cell = $(`<td class="calendar-cell" data-hour="${hour}" data-minute="${minute}" data-doctor-id="${especialista.id}"></td>`);
                                tr.append(cell);
                            });

                            $('#tableBody').append(tr);
                        }
                    }

                    // Hacer celdas vacías clickeables con doble click para agregar cita
                    $(document).off('dblclick', '.calendar-cell').on('dblclick', '.calendar-cell', function (e) {
                        if (!$(this).hasClass('horario-disponible')) {
                            return;
                        }

                        const cita = $(this).find('.cita-item');

                        if (cita.length === 0 && !$(this).hasClass('cita-ocupada-continuacion')) {
                            const hour = $(this).data('hour');
                            const minute = $(this).data('minute');
                            const doctorId = $(this).data('doctor-id');
                            const fecha = $('#datePicker').val();
                            const hora = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}:00`;
                            const fechaHora = `${fecha} ${hora}`;
                            const url = '<?= $this->Url->build(["controller" => "Citas", "action" => "add"]); ?>' + `?fecha_hora=${encodeURIComponent(fechaHora)}&doctor_id=${doctorId}&return_to=calendarioEspecialistas&fecha=${fecha}`;
                            $('.openModal').attr('href', url).click();
                        }
                    });

                    $('.calendar-cell').removeClass('horario-disponible horario-bloqueado');

                    // Guardamos los horarios de cada doctor para poder validar la duración completa de una cita al soltarla
                    horariosPorDoctor = {};

                    // Para cada especialista, obtener sus horarios y bloqueos y aplicar disponibilidad
                    especialistas.forEach(function(especialista) {
                        $.ajax({
                            url: '<?= $this->Url->build(["controller" => "HorariosDoctores", "action" => "obtenerHorarios"]); ?>',
                            method: 'GET',
                            data: { doctor_id: especialista.id },
                            dataType: 'json',
                            success: function(horarioData) {
                                const horarios = (horarioData.success && horarioData.horarios) ? horarioData.horarios : [];
                                horariosPorDoctor[especialista.id] = horarios;

                                $.ajax({
                                    url: '<?= $this->Url->build(["controller" => "HorariosBloqueos", "action" => "obtenerBloqueosPorFecha"]); ?>',
                                    method: 'GET',
                                    data: { fecha: fechaSeleccionada, doctor_id: especialista.id },
                                    dataType: 'json',
                                    success: function(bloqueosData) {
                                        const bloqueos = bloqueosData.bloqueos || [];

                                        $(`.calendar-cell[data-doctor-id="${especialista.id}"]`).each(function() {
                                            const $cell = $(this);
                                            const hour = $cell.data('hour');
                                            const minute = $cell.data('minute');

                                            const fechaLima = new Date(`${fechaSeleccionada}T00:00:00-05:00`);
                                            let diaSemana = fechaLima.getDay();

                                            const horaFormato = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}:00`;

                                            const horarioValido = horarios.some(horario =>
                                                horario.dia_semana == diaSemana &&
                                                horaFormato >= horario.hora_inicio &&
                                                horaFormato <= horario.hora_fin
                                            );

                                            if (!horarioValido) {
                                                $cell.removeClass('horario-disponible horario-bloqueado');
                                                return;
                                            }

                                            const estaBloqueada = bloqueos.some(bloqueo =>
                                                horaFormato >= bloqueo.hora_inicio && horaFormato < bloqueo.hora_fin
                                            );

                                            if (estaBloqueada) {
                                                $cell.addClass('horario-bloqueado').removeClass('horario-disponible');
                                            } else {
                                                $cell.addClass('horario-disponible').removeClass('horario-bloqueado');
                                            }
                                        });
                                    },
                                    error: function() {
                                        console.error('Error al obtener bloqueos del especialista:', especialista.id);
                                    }
                                });
                            },
                            error: function() {
                                console.error('Error al obtener horarios del especialista:', especialista.id);
                            }
                        });
                    });

                    // Llenar las citas en las celdas correspondientes
                    citas.forEach(function (cita) {
                        if (!cita.estado || !cita.paciente || !cita.duracion_minutos) {
                            return;
                        }

                        // Las citas canceladas no ocupan espacio en el calendario
                        if (cita.estado.toLowerCase() === 'cancelado') {
                            return;
                        }

                        const parts = cita.fecha_hora.split(/[- :]/);
                        const hour = parseInt(parts[3], 10);
                        const minute = parseInt(parts[4], 10);
                        const duration = parseInt(cita.duracion_minutos, 10);
                        const doctorId = cita.doctor_id;
                        const tratamientosStr = cita.tratamientos && cita.tratamientos.length
                            ? cita.tratamientos.map(t => t.nombre).join(', ')
                            : 'Sin tratamiento';
                        let cell = $(`.calendar-cell[data-hour="${hour}"][data-minute="${minute}"][data-doctor-id="${doctorId}"]`);

                        if (cell.length > 0) {
                            const rowsOccupied = duration / 15;

                            let estadoClass = '';
                            switch (cita.estado.toLowerCase()) {
                                case 'pendiente': estadoClass = 'estado-pendiente'; break;
                                case 'confirmado': estadoClass = 'estado-confirmado'; break;
                                case 'en_consultorio': estadoClass = 'estado-consultorio'; break;
                                case 'finalizado': estadoClass = 'estado-finalizado'; break;
                                case 'cancelado': estadoClass = 'estado-cancelado'; break;
                                case 'en_recepcion': estadoClass = 'estado-recepcion'; break;
                                case 'en_cabina': estadoClass = 'estado-cabina'; break;
                                case 'programar': estadoClass = 'estado-programar'; break;
                                case 'sos': estadoClass = 'estado-sos'; break;
                                default: estadoClass = 'estado-desconocido'; break;
                            }

                            const citaElement = $(`
                                <div class="cita-item ${estadoClass}"
                                    title="${cita.paciente}&#10;Motivo: ${cita.motivo ? cita.motivo.replace(/"/g, '&quot;') : 'Sin motivo'}&#10;Tratamientos: ${tratamientosStr}"
                                    data-paciente-nombre="${cita.paciente}"
                                    data-cita-id="${cita.id}"
                                    data-paciente-id="${cita.paciente_id}"
                                    data-telefono-celular="${cita.telefono_celular || ''}"
                                    data-fecha-hora="${cita.fecha_hora}"
                                    data-doctor="${cita.doctor}"
                                    data-doctor-id="${cita.doctor_id}"
                                    data-hora-llegada="${cita.hora_llegada || ''}"
                                    data-duracion="${cita.duracion_minutos || 0}"
                                    data-estado="${cita.estado}">
                                    <strong>${cita.paciente}</strong><br>
                                </div>
                            `);

                            citaElement.css({
                                "position": "absolute",
                                "top": "5%",
                                "left": "10%",
                                "height": `calc(${(duration / 15) * 100}% - 10px)`,
                                "width": "80%",
                                "border-radius": "6px",
                                "padding": "5px",
                                "z-index": "10",
                                "cursor": esDispositivoTactil ? "pointer" : "grab",
                                "touch-action": esDispositivoTactil ? "auto" : "none"
                            });

                            cell.append(citaElement);

                            cell.addClass('cita-ocupada');

                            if (rowsOccupied > 1) {
                                let currentHour = hour;
                                let currentMinute = minute;
                                let remainingRows = rowsOccupied - 1;

                                while (remainingRows > 0) {
                                    currentMinute += 15;
                                    if (currentMinute >= 60) {
                                        currentMinute = 0;
                                        currentHour++;
                                    }

                                    let nextCell = $(`.calendar-cell[data-hour="${currentHour}"][data-minute="${currentMinute}"][data-doctor-id="${doctorId}"]`);
                                    if (nextCell.length > 0) {
                                        nextCell.addClass('cita-ocupada-continuacion');
                                    }

                                    remainingRows--;
                                }
                            }
                        }
                    });

                    // Inicializar Sortable.js para drag and drop
                    if (typeof Sortable !== 'undefined' && !esDispositivoTactil) {
                        $('.calendar-cell').each(function() {
                            new Sortable(this, {
                                group: 'citas',
                                animation: 150,
                                ghostClass: 'ui-draggable-dragging',
                                forceFallback: false,
                                onEnd: function (evt) {
                                    const citaElement = $(evt.item);
                                    const citaId = citaElement.data('cita-id');
                                    const newCell = $(evt.to);
                                    const newHour = newCell.data('hour');
                                    const newMinute = newCell.data('minute');
                                    const newDoctorId = newCell.data('doctor-id');
                                    const fecha = $('#datePicker').val();

                                    if (!citaId || newCell.find('.cita-item').length > 1) {
                                        evt.from.appendChild(evt.item);
                                        alert('No se puede soltar aquí. La celda debe estar vacía.');
                                        return;
                                    }

                                    const fechaLima = new Date(`${fecha}T00:00:00-05:00`);
                                    const diaSemana = fechaLima.getDay();
                                    const horaInicio = `${String(newHour).padStart(2, '0')}:${String(newMinute).padStart(2, '0')}:00`;

                                    if (!citaCabeEnHorario(newDoctorId, diaSemana, horaInicio)) {
                                        evt.from.appendChild(evt.item);
                                        alert('❌ El especialista no tiene horario de atención asignado en ese rango de hora.');
                                        return;
                                    }

                                    $.ajax({
                                        url: '<?= $this->Url->build(["controller" => "Citas", "action" => "moverCita"]); ?>',
                                        type: 'POST',
                                        headers: {
                                            'X-CSRF-Token': '<?= $this->request->getAttribute("csrfToken"); ?>'
                                        },
                                        data: {
                                            cita_id: citaId,
                                            fecha: fecha,
                                            hora: `${newHour}:${newMinute.toString().padStart(2, '0')}`,
                                            doctor_id: newDoctorId
                                        },
                                        success: function (response) {
                                            if (response.success) {
                                                alert('✅ Cita movida correctamente');
                                                actualizarCalendario(fecha);
                                            } else {
                                                const errorMsg = response.error || 'Error desconocido';
                                                alert('❌ ' + errorMsg);
                                                actualizarCalendario(fecha);
                                            }
                                        },
                                        error: function (xhr, status, error) {
                                            alert('❌ Error al mover la cita: ' + error);
                                            actualizarCalendario(fecha);
                                        }
                                    });
                                }
                            });
                        });
                    }
                },
                error: function () {
                    alert('Error al cargar las citas y especialistas.');
                }
            });
        }

        // Manejadores de eventos del menú contextual
        $(document).on('click', '.cita-item', function (event) {
            event.preventDefault();
            event.stopPropagation();

            const citaId = $(this).data('cita-id');
            const pacienteId = $(this).data('paciente-id');

            if (!citaId) {
                alert('No se pudo identificar la cita.');
                return;
            }

            $('#contextMenu').hide();
            $('#contextMenu')
                .css({
                    display: 'block',
                    left: event.pageX + 'px',
                    top: event.pageY + 'px'
                })
                .data('cita-id', citaId)
                .data('paciente-id', pacienteId);
        });

        $(document).on('click', function (event) {
            if (!$(event.target).closest('#contextMenu').length) {
                $('#contextMenu').hide();
            }
        });

        $(document).on('click', '.view-history', function (e) {
            e.preventDefault();
            const pacienteId = $('#contextMenu').data('paciente-id');

            if (!pacienteId) {
                alert('No se pudo obtener el ID del paciente.');
                return;
            }

            const viewUrl = '<?= $this->Url->build(['controller' => 'Pacientes', 'action' => 'view']); ?>';
            const fullUrl = viewUrl + '/' + pacienteId;
            window.open(fullUrl, '_blank');
            $('#contextMenu').hide();
        });

        $(document).on('click', '.ver-appointment', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');

            if (!citaId) {
                alert('No se pudo identificar la cita.');
                return;
            }

            const viewUrl = '<?= $this->Url->build(['controller' => 'Citas', 'action' => 'view']); ?>';
            const queryParams = `/${citaId}`;
            $('.openModal').attr('href', viewUrl + queryParams).click();
            $('#contextMenu').hide();
        });

        $(document).on('click', '.edit-appointment', function (e) {
            e.preventDefault();
            if (!rolesPermitidos.includes(userRole)) {
                alert('No tienes permiso para editar citas.');
                return;
            }
            const citaId = $('#contextMenu').data('cita-id');

            if (!citaId) {
                alert('No se pudo identificar la cita para editar.');
                return;
            }

            const editUrl = '<?= $this->Url->build(['controller' => 'Citas', 'action' => 'edit']); ?>';
            const queryParams = `/${citaId}?return_to=calendarioEspecialistas`;
            $('.openModal').attr('href', editUrl + queryParams).click();
            $('#contextMenu').hide();
        });

        function cambiarEstadoCita(citaId, nuevoEstado) {
            $.ajax({
                url: '<?= $this->Url->build(['controller' => 'Citas', 'action' => 'changeStatus']); ?>',
                type: 'POST',
                headers: {
                    'X-CSRF-Token': '<?= $this->request->getAttribute("csrfToken"); ?>'
                },
                data: { id: citaId, estado: nuevoEstado },
                success: function (response) {
                    if (response.success) {
                        alert('Estado actualizado a: ' + nuevoEstado);
                        const fechaSeleccionada = $('#datePicker').val();
                        actualizarCalendario(fechaSeleccionada);
                    } else {
                        alert('No se pudo actualizar el estado. Inténtalo de nuevo.');
                    }
                },
                error: function () {
                    alert('Error al actualizar el estado.');
                }
            });
            $('#contextMenu').hide();
        }

        $(document).on('click', '.confirmado-appointment', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');
            if (!citaId) { alert('No se pudo identificar la cita.'); return; }
            cambiarEstadoCita(citaId, 'confirmado');
        });

        $(document).on('click', '.consultorio-appointment', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');
            if (!citaId) { alert('No se pudo identificar la cita.'); return; }
            cambiarEstadoCita(citaId, 'en_consultorio');
        });

        $(document).on('click', '.recepcion-appointment', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');
            if (!citaId) { alert('No se pudo identificar la cita.'); return; }
            cambiarEstadoCita(citaId, 'en_recepcion');
        });

        $(document).on('click', '.cabina-appointment', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');
            if (!citaId) { alert('No se pudo identificar la cita.'); return; }
            cambiarEstadoCita(citaId, 'en_cabina');
        });

        $(document).on('click', '.programar-appointment', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');
            if (!citaId) { alert('No se pudo identificar la cita.'); return; }
            cambiarEstadoCita(citaId, 'programar');
        });

        $(document).on('click', '.sos-appointment', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');
            if (!citaId) { alert('No se pudo identificar la cita.'); return; }
            cambiarEstadoCita(citaId, 'sos');
        });

        $(document).on('click', '.finalizado-appointment', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');
            if (!citaId) { alert('No se pudo identificar la cita.'); return; }
            cambiarEstadoCita(citaId, 'finalizado');
        });

        $(document).on('click', '.cancelado-appointment', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');

            if (!citaId) { alert('No se pudo identificar la cita.'); return; }

            const confirmacion = confirm('¿Estás seguro de que deseas cancelar esta cita?');
            if (confirmacion) {
                cambiarEstadoCita(citaId, 'cancelado');
            }
        });

        $(document).on('click', '.reset-status', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');

            if (!citaId) { alert('No se pudo identificar la cita.'); return; }

            $.ajax({
                url: '<?= $this->Url->build(["controller" => "Citas", "action" => "restablecerCita"]); ?>',
                type: 'POST',
                headers: {
                    'X-CSRF-Token': '<?= $this->request->getAttribute("csrfToken"); ?>'
                },
                data: {
                    cita_id: citaId
                },
                success: function(response) {
                    alert('✅ Cita restablecida a pendiente y hora de llegada eliminada.');
                    const fechaSeleccionada = $('#datePicker').val();
                    actualizarCalendario(fechaSeleccionada);
                },
                error: function() {
                    alert('❌ Error al restablecer la cita.');
                }
            });
            $('#contextMenu').hide();
        });

        $(document).on('click', '.marcar-hora-llegada', function (e) {
            e.preventDefault();

            const citaId = $('#contextMenu').data('cita-id');
            const horaActual = new Date().toLocaleTimeString('it-IT');

            const cita = $('.cita-item[data-cita-id="' + citaId + '"]');
            const fechaHora = cita.data('fecha-hora');
            const horaLlegadaExistente = cita.data('hora-llegada');

            if (!cita.length || !fechaHora) {
                alert('❌ No se pudo obtener los datos de la cita.');
                $('#contextMenu').hide();
                return;
            }

            const fechaCita = fechaHora.split(' ')[0];
            const fechaActualLima = new Date();
            const fechaLimaFormateada = new Date(fechaActualLima.getTime() - (fechaActualLima.getTimezoneOffset() * 60000));
            const fechaActual = fechaLimaFormateada.toISOString().split('T')[0];

            if (fechaCita !== fechaActual) {
                alert('❌ Solo se puede marcar la hora de llegada para citas del día de hoy.');
                $('#contextMenu').hide();
                return;
            }

            if (horaLlegadaExistente && horaLlegadaExistente !== 'N/A') {
                const confirmar = confirm('⚠️ Esta cita ya tiene una hora de llegada registrada. ¿Desea actualizarla?');
                if (!confirmar) {
                    $('#contextMenu').hide();
                    return;
                }
            }

            $.ajax({
                url: '<?= $this->Url->build(["controller" => "Citas", "action" => "marcarHoraLlegada"]); ?>',
                type: 'POST',
                headers: {
                    'X-CSRF-Token': '<?= $this->request->getAttribute("csrfToken"); ?>'
                },
                data: {
                    cita_id: citaId,
                    hora_llegada: horaActual
                },
                dataType: 'json',
                success: function(response) {
                    alert('✅ Se registró la hora de llegada: ' + horaActual);
                    const fechaSeleccionada = $('#datePicker').val();
                    actualizarCalendario(fechaSeleccionada);
                },
                error: function(xhr, status, error) {
                    alert('❌ Error al registrar la hora de llegada.');
                }
            });

            $('#contextMenu').hide();
        });

        $(document).on('click', '.whatsapp-confirmation', function (e) {
            e.preventDefault();
            const pacienteId = $('#contextMenu').data('paciente-id');
            const citaId = $('#contextMenu').data('cita-id');

            if (!pacienteId || !citaId) {
                alert('No se pudo identificar la cita o el paciente.');
                return;
            }

            const cita = $('.cita-item[data-cita-id="' + citaId + '"]');
            const pacienteNombre = cita.data('paciente-nombre');
            const telefonoCelular = cita.data('telefono-celular');
            const fechaHora = cita.data('fecha-hora');
            const doctorNombre = cita.data('doctor');

            if (!telefonoCelular || !fechaHora || !doctorNombre) {
                alert('Faltan datos de la cita o paciente.');
                return;
            }

            const fechaObj = new Date(fechaHora);
            const dia = String(fechaObj.getDate()).padStart(2, '0');
            const mes = String(fechaObj.getMonth() + 1).padStart(2, '0');
            const anio = String(fechaObj.getFullYear()).slice(-2);

            let horas = fechaObj.getHours();
            const minutos = String(fechaObj.getMinutes()).padStart(2, '0');
            const ampm = horas >= 12 ? 'PM' : 'AM';
            horas = horas % 12 || 12;

            const horaFormateada = `${horas.toString().padStart(2, '0')}:${minutos} ${ampm}`;
            const fechaFormateada = `${dia}/${mes}/${anio}`;

            let mssg = `Hola%20${pacienteNombre},%20te%20recordamos%20tu%20cita%20el%20día%20${fechaFormateada}%20a%20las%20${horaFormateada}%20con%20el%20Especialista%20${doctorNombre}.%20*Por%20favor%20confirmar%20su%20asistencia.%20Gracias!*`;

            const whatsappUrl = `https://api.whatsapp.com/send?phone=51${telefonoCelular}&text=${mssg}`;
            window.open(whatsappUrl, "_blank");
            $('#contextMenu').hide();
        });

        $(document).on('click', '.whatsapp-message', function (e) {
            e.preventDefault();
            const pacienteId = $('#contextMenu').data('paciente-id');
            const citaId = $('#contextMenu').data('cita-id');

            if (!pacienteId || !citaId) {
                alert('No se pudo identificar la cita o el paciente.');
                return;
            }

            const cita = $(`.cita-item[data-cita-id="${citaId}"]`);
            const telefonoCelular = cita.data('telefono-celular');

            if (!telefonoCelular) {
                alert('No se encontró el número de teléfono del paciente.');
                return;
            }

            const whatsappUrl = `https://wa.me/51${telefonoCelular}`;
            window.open(whatsappUrl, '_blank');
            $('#contextMenu').hide();
        });

        // Función para generar minicalendarios
        function generateMiniCalendar(month, year, container) {
          const locale = "es-PE";
          const tz = "America/Lima";

          const root = document.createElement("div");
          root.className = "mini-calendar";

          const header = document.createElement("div");
          header.className = "mc-header";
          header.textContent = new Date(year, month, 1)
            .toLocaleString(locale, { month: "long", year: "numeric", timeZone: tz });
          root.appendChild(header);

          const dowRow = document.createElement("div");
          dowRow.className = "mc-grid";
          ["lun","mar","mié","jue","vie","sáb","dom"].forEach(label => {
            const d = document.createElement("div");
            d.className = "mc-dow";
            d.textContent = label;
            dowRow.appendChild(d);
          });
          root.appendChild(dowRow);

          const grid = document.createElement("div");
          grid.className = "mc-grid";

          const firstDay = (new Date(year, month, 1).getDay() + 6) % 7;
          const daysInMonth = new Date(year, month + 1, 0).getDate();

          for (let i = 0; i < firstDay; i++) {
            const blank = document.createElement("div");
            blank.className = "mc-blank";
            grid.appendChild(blank);
          }

          const datePicker = document.getElementById("datePicker");

          const nowLima = new Date(new Date().toLocaleString("en-US", { timeZone: tz }));

          for (let day = 1; day <= daysInMonth; day++) {
            const cell = document.createElement("div");
            cell.className = "mc-day";
            cell.textContent = day;

            if (nowLima.getFullYear() === year &&
                nowLima.getMonth() === month &&
                nowLima.getDate() === day) {
              cell.classList.add("mc-today");
            }

            cell.addEventListener("click", () => {
              const monthStr = String(month + 1).padStart(2, "0");
              const dayStr = String(day).padStart(2, "0");
              const newDate = `${year}-${monthStr}-${dayStr}`;

              datePicker.value = newDate;

              document.querySelectorAll(".mini-calendar .mc-day")
                .forEach(d => d.classList.remove("mc-selected"));

              cell.classList.add("mc-selected");

              $("#datePicker").trigger("change");
            });

            grid.appendChild(cell);
          }

          root.appendChild(grid);
          container.appendChild(root);
        }

        // Renderizar calendarios (mes actual y siguiente en Lima)
        const tz = "America/Lima";
        const now = new Date(new Date().toLocaleString("en-US", { timeZone: tz }));
        const container = document.getElementById("miniCalendars");

        generateMiniCalendar(now.getMonth(), now.getFullYear(), container);
        generateMiniCalendar(
          (now.getMonth() + 1) % 12,
          now.getMonth() === 11 ? now.getFullYear() + 1 : now.getFullYear(),
          container
        );

        const currentDatePicker = document.getElementById("datePicker");
        const currentDate = new Date(currentDatePicker.value);
        document.querySelectorAll(".mini-calendar .mc-day").forEach(day => {
          if (parseInt(day.textContent) === currentDate.getDate()) {
            const header = day.parentElement.previousElementSibling;
            const headerText = header.textContent;
            const monthYear = new Date(currentDatePicker.value);
            if (headerText.includes(monthYear.toLocaleString("es-PE", { month: "long" }))) {
              day.classList.add("mc-selected");
            }
          }
        });
    });
</script>
