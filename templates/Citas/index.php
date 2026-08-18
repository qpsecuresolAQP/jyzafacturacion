<?= $this->Html->script('https://code.jquery.com/jquery-3.6.0.min.js?v=' . time()) ?>
<?= $this->Html->script('https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js?v=' . time()) ?>
<?= $this->Html->css('https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css?v=' . time()) ?>
<?= $this->Html->charset() ?> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="csrfToken" content="<?= $this->request->getAttribute('csrfToken');
?>">


<div class="container">

    <div class="row align-items-start g-3">
      <!-- Selectores -->
      <div class="col-lg-4 order-lg-1 order-1 ">
        <!-- Selector de odontólogos -->
        <div class="form-group">
            <label for="doctorSelect">Especialistas:</label>
            <?= $this->Form->select('doctor_id', $doctores, [
                'id' => 'doctorSelect',
                'class' => 'form-control',
                'value' => $doctorId ?? (reset($doctores) ? key($doctores) : ''), // Por defecto el primer doctor
            ]) ?>
        </div>
    
        <!-- Selector de fecha -->
        <div class="form-group">
          <label for="datePicker">Seleccionar fecha:</label>
          <input type="date" id="datePicker" class="form-control" 
                 value="<?= $fecha ?? date('Y-m-d') ?>">
        </div>
      </div>
    
      <!-- Mini-calendarios -->
      <div class="col-lg-4 order-lg-2 order-2">
        <div id="miniCalendars" class="calendars-container"></div>
      </div>
    </div>

    <!-- Leyenda de estados -->
    <div class="form-group">
        <label>Estados de las citas:</label>
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
    <!-- Botón para agregar cita -->
    
    <a href="#" class="openModal" style="display: none;"></a>

    <!-- Contenedor del calendario -->
    <!-- Contenedor del calendario con soporte para scroll horizontal -->
    <?php
// Configura el idioma para las fechas
setlocale(LC_TIME, 'Spanish_Peru', 'es-PE', 'es_ES.UTF-8', 'es_ES', 'es');
$formatter = new IntlDateFormatter('es_PE', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
$formatter->setPattern('EEEE d/MM'); // Día de la semana y día/mes
?>
<div class="calendar-container">
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th></th>
                <!-- <?php for ($i = 0; $i < 7; $i++): ?>
                    <th data-fecha="<?= date('Y-m-d', strtotime("+" . ($i + 1) . " days")) ?>">
                        <?= date('l d/m', strtotime("+" . ($i + 1) . " days")) ?>
                    </th>
                <?php endfor; ?> -->
                <?php for ($i = 0; $i < 7; $i++): ?>
                    <th data-fecha="<?= date('Y-m-d', strtotime("+$i days")) ?>">
                        <?= ucfirst($formatter->format(new DateTime("+$i days"))) ?>
                    </th>
                <?php endfor; ?>

            </tr>
        </thead>
        <tbody>
            <?php for ($hour = 8; $hour <= 19; $hour++): ?>
                <?php for ($minute = 0; $minute < 60; $minute += 15): ?>
                    <tr>
                        <td><?= date('h:i A', strtotime("$hour:$minute")) ?></td>
                        <?php for ($i = 0; $i < 7; $i++): ?>
                            <td class="calendar-cell" data-hour="<?= $hour ?>" data-minute="<?= $minute ?>" data-day="<?= $i ?>"></td>
                        <?php endfor; ?>
                    </tr>
                <?php endfor; ?>
            <?php endfor; ?>
        </tbody>

    </table>
</div>
</div>

<!-- Menú contextual -->
<div id="contextMenu" class="dropdown-menu" style="display: none; position: absolute;">
    <a class="dropdown-item view-history" href="#">Ver Historia</a>
    <a class="dropdown-item ver-appointment" href="#">Ver Cita</a>
    <a class="dropdown-item edit-appointment" href="#">Editar Cita</a>
    <!-- <a class="dropdown-item citado-appointment" href="#">Citado</a> -->
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
    <!-- <div class="dropdown-divider"></div>
    <a class="dropdown-item delete-status" href="#">Eliminar</a> -->
</div>

<!-- Estilos CSS -->
<style>

.calendars-container {
  display: flex;
  flex-wrap: nowrap;
  gap: 12px; 
  margin-top: 10px;
}

.mini-calendar {
  font-family: Arial, sans-serif;
  font-size: 10px;          /* texto más compacto */
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
  background: #198754 !important; /* verde Bootstrap success */
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
    /* Contenedor principal del calendario */
    .calendar-container {
        
        margin-top: 20px;
    }
    
.table-bordered {
  border: 1px solid #adcbe2; /* azul medio */
}
.table-bordered td,
.table-bordered th {
  border: 1px solid #afd0e5;
}

    /* para la parte no disponible */
    /* .calendar-cell:not(.horario-disponible) {
        cursor: not-allowed;
    } */
    
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
.calendar-cell {
  position: relative; /* Para que posición absoluta en hijo sea relativa a esta */
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
        height: 60px;
        cursor: pointer;
        text-align: center;
        vertical-align: middle;
        font-size: 14px; /* Tamaño base del texto */
    }

    /* Estilo general de la tabla */
    .table th, .table td {
        text-align: center;
        vertical-align: middle;
    }


    /* Elementos de citas dentro de las celdas */
    .cita-item {
        margin: 2px 0;
        padding: 5px;
        border-radius: 4px;
        font-size: 12px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: white;
        font-weight: bold;
    }
    .horario-disponible {
        background-color: #D0E8F2 !important;
        /* color: #111; */
        font-weight: bold;
    }

    .horario-bloqueado {
        /* background-color: rgba(222, 227, 230, 0.8) !important; */
        font-weight: bold;
    }

.cita-ocupada {
    position: relative;
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
    background-color: #808080; /* Gris */
}
.estado-recepcion {
   background-color: rgba(230, 126, 34, 0.85); /* Amarillo mostaza suave */
}
.estado-programar {
    background-color: rgba(255, 99, 132, 0.7);
}
.estado-cabina {
    background-color: rgba(0, 123, 115, 0.85); /* Verde azulado */
    color: white;
}
.estado-sos {
    background-color: rgba(220, 53, 69, 0.85);
}
.estado-cancelado {
    display: none;
}

/* Íconos visuales para estados */
.estado-pendiente::before {
    content: '\f017'; /* Reloj */
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    margin-right: 5px;
}

.estado-confirmado::before {
    content: '\f00c'; /* Check */
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    margin-right: 5px;
}

.estado-consultorio::before {
    content: '\f0f0'; /* Estetoscopio */
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    margin-right: 5px;
}

.estado-finalizado::before {
    content: '\f111'; /* Círculo */
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    margin-right: 5px;
}
.estado-recepcion::before {
    content: '\f0c0'; /* Usuarios (representa interacción en recepción) */
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    margin-right: 5px;
}
.estado-programar::before {
    content: '\f073'; /* Icono calendario */
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    margin-right: 5px;
}
.estado-cabina::before {
    content: '\f2c2'; /* Cámara / Cabina (representativo) */
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    margin-right: 5px;
}
/* Icono para el estado SOS */
.estado-sos::before {
    content: '\f12a'; /* Icono de exclamación */
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    margin-right: 5px;
}
.estado-cancelado::before {
    /* content: '\f057'; 
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    margin-right: 5px; */
    display:none;
}


    /* Barra fija para selectores de doctor y fecha */
    .fixed-top-bar {
        background-color: #f8f9fa; /* Fondo claro */
        border-bottom: 1px solid #ddd; /* Línea separadora */
        padding: 10px 15px;
        z-index: 1030; /* Encima del contenido */
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
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


    .hora-llegada {
    display: inline-block;
    padding: 1px 5px;
    border-radius: 5px;
    color: white;
    text-align: center;
    cursor: pointer;
}


    /* Media query para dispositivos pequeños */
    @media (max-width: 768px) {
        .calendar-cell {
            height: 40px; /* Reducir altura */
            font-size: 12px; /* Reducir texto */
        }

        .cita-item {
            font-size: 10px; /* Reducir texto en citas */
        }

        .fixed-top-bar {
            font-size: 14px; /* Ajustar tamaño de texto */
            padding: 8px 10px; /* Reducir padding */
        }

        #contextMenu {
            font-size: 12px; /* Reducir tamaño del menú */
        }
    }

    /* Media query para dispositivos muy pequeños */
    @media (max-width: 576px) {
        .calendar-cell {
            font-size: 10px; /* Reducir texto aún más */
        }

        .cita-item {
            font-size: 9px; /* Reducir texto de las citas */
        }

        .fixed-top-bar {
            font-size: 12px; /* Ajustar texto */
            padding: 6px 8px; /* Padding más pequeño */
        }

        #contextMenu {
            font-size: 10px; /* Ajustar texto */
        }
    }
    
</style>


<script>
    $(document).ready(function () {
        // Los permisos se leen desde la BD a través de $rolesPermitidosIds del controller
        const rolesPermitidos = <?= json_encode($rolesPermitidosIds); ?>;
        const userRole = <?= json_encode($usuarioRolId); ?>;

        // Si el usuario es doctor (rol_id = 2), bloquear el select de doctores
        if (userRole === 2) {
            $('#doctorSelect').prop('disabled', true);
        }

         $(document).on('click', '.cita-item', function (event) {
            event.preventDefault();
            event.stopPropagation(); // Detener propagación para evitar cerrar el menú inmediatamente

            const citaId = $(this).data('cita-id');
            const pacienteId = $(this).data('paciente-id');

            if (!citaId) {
                alert('No se pudo identificar la cita.');
                return;
            }

            // Ocultar otros menús antes de mostrar el nuevo
            $('#contextMenu').hide();

            // Mostrar el menú contextual en la posición del cursor
            $('#contextMenu')
                .css({
                    display: 'block',
                    left: event.pageX + 'px',
                    top: event.pageY + 'px'
                })
                .data('cita-id', citaId)
                .data('paciente-id', pacienteId);
        });

        // Ocultar menú contextual al hacer clic fuera
        $(document).on('click', function (event) {
            // Ocultar el menú si el clic no es en el menú
            if (!$(event.target).closest('#contextMenu').length) {
                $('#contextMenu').hide();
            }
        });

        // Opciones del menú contextual
        // manejo del click en la opcion para marcar hora de llegada
        $(document).on('click', '.marcar-hora-llegada', function (e) {
            e.preventDefault();

            const citaId = $('#contextMenu').data('cita-id');
            const horaActual = new Date().toLocaleTimeString('it-IT'); // Formato HH:mm:ss

            // Busca los datos de la cita desde la tabla renderizada
            const cita = $('.cita-item[data-cita-id="' + citaId + '"]');
            const fechaHora = cita.data('fecha-hora'); 
            const horaLlegadaExistente = cita.data('hora-llegada'); 
            
            console.log("citaId:", citaId);
            console.log("Elemento cita encontrado:", cita.length > 0);
            console.log("hora_llegada: ", horaLlegadaExistente);
            console.log("Fecha hora:", fechaHora);

            // Validación: verificar que se encontró la cita
            if (!cita.length || !fechaHora) {
                alert('❌ No se pudo obtener los datos de la cita. Por favor, intenta nuevamente.');
                console.error('Cita no encontrada o faltan datos. citaId:', citaId, 'fechaHora:', fechaHora);
                $('#contextMenu').hide();
                return;
            }

            // Extrae la fecha en formato YYYY-MM-DD
            const fechaCita = fechaHora.split(' ')[0];
            console.log("Fecha de la cita:", fechaCita);

            // Fecha actual en Lima (UTC -5)
            const fechaActualLima = new Date();
            const fechaLimaFormateada = new Date(fechaActualLima.getTime() - (fechaActualLima.getTimezoneOffset() * 60000));
            const fechaActual = fechaLimaFormateada.toISOString().split('T')[0];
            console.log("Fecha actual en Lima (formateada):", fechaActual);

            // Validación de fecha
            if (fechaCita !== fechaActual) {
                alert('❌ Solo se puede marcar la hora de llegada para citas del día de hoy.');
                $('#contextMenu').hide();
                return;
            }

            // Validación si ya tiene una hora registrada
            if (horaLlegadaExistente && horaLlegadaExistente !== 'N/A') {
                const confirmar = confirm('⚠️ Esta cita ya tiene una hora de llegada registrada. ¿Desea actualizarla?');
                if (!confirmar) {
                    $('#contextMenu').hide();
                    return;
                }
            }

            const doctorId = $('#doctorSelect').val();
            const fecha = $('#datePicker').val();

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
                    console.log("Respuesta del servidor:", response);
                    alert('✅ Se registró la hora de llegada: ' + horaActual);
                     // Actualizar el calendario después de mover la cita
                    const doctorId = $('#doctorSelect').val();
                    const dateNew = $('#datePicker').val();
                    actualizarCitas(dateNew, doctorId);     
                    // window.location.href = `<?= $this->Url->build(["controller" => "Citas", "action" => "index"]); ?>?doctor_id=${doctorId}&fecha=${fecha}`;
                },
                error: function(xhr, status, error) {
                    console.error("Error completo:", xhr);
                    console.error("Status:", status);
                    console.error("Error:", error);
                    console.error("Response text:", xhr.responseText);
                    alert('❌ Error al registrar la hora de llegada.\nEstatus: ' + status + '\nError: ' + error);
                }
            });

            $('#contextMenu').hide();
        });

        // Manejo del clic en la opción "Ver Historia" del menú contextual
       $(document).on('click', '.view-history', function (e) {
            e.preventDefault();
            const pacienteId = $('#contextMenu').data('paciente-id');

            if (!pacienteId) {
                alert('No se pudo obtener el ID del paciente.');
                return;
            }

            // Actualiza dinámicamente el enlace `openModal` para ver historia
            const viewUrl = '<?= $this->Url->build(['controller' => 'Pacientes', 'action' => 'view']); ?>';
            // const queryParams = `/${pacienteId}`;
            const fullUrl = viewUrl + '/' + pacienteId;
            
            window.open(fullUrl, '_blank');

            // Ocultar el menú contextual después de seleccionar la opción
            $('#contextMenu').hide();
        });

        // Ver cita: Mostrar detalles de la cita en un modal
        $(document).on('click', '.ver-appointment', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');

            if (!citaId) {
                alert('No se pudo identificar la cita.');
                return;
            }

            // Actualiza dinámicamente el enlace `openModal` para ver cita
            const viewUrl = '<?= $this->Url->build(['controller' => 'Citas', 'action' => 'view']); ?>';
            const queryParams = `/${citaId}`;

            // Configurar y disparar el enlace del modal
            $('.openModal').attr('href', viewUrl + queryParams).click();

            // Ocultar el menú contextual después de seleccionar la opción
            $('#contextMenu').hide();
        });

        // Confirmar cita: Cambiar estado a "Confirmado"
        $(document).on('click', '.confirmado-appointment', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');

            if (!citaId) {
                alert('No se pudo identificar la cita.');
                return;
            }

            // Llamada AJAX para actualizar el estado
            cambiarEstadoCita(citaId, 'confirmado');
        });

        // Marcar como en consultorio: Cambiar estado a "En Consultorio"
        $(document).on('click', '.consultorio-appointment', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');

            if (!citaId) {
                alert('No se pudo identificar la cita.');
                return;
            }

            // Llamada AJAX para actualizar el estado
            cambiarEstadoCita(citaId, 'en_consultorio');
        });
        // Marcar como en recepción: Cambiar estado a "Recepción"
        $(document).on('click', '.recepcion-appointment', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');

            if (!citaId) {
                alert('No se pudo identificar la cita.');
                return;
            }

            // Llamada AJAX para actualizar el estado
            cambiarEstadoCita(citaId, 'en_recepcion');
        });
        // Marcar como en cabina: Cambiar estado a "En Cabina"
        $(document).on('click', '.cabina-appointment', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');

            if (!citaId) {
                alert('No se pudo identificar la cita.');
                return;
            }

            // Llamada AJAX para actualizar el estado
            cambiarEstadoCita(citaId, 'en_cabina');
        });
        // Marcar como programar: Cambiar estado a "Programar"
        $(document).on('click', '.programar-appointment', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');

            if (!citaId) {
                alert('No se pudo identificar la cita.');
                return;
            }

            // Llamada AJAX para actualizar el estado
            cambiarEstadoCita(citaId, 'programar');
        });

        // Marcar como SOS
        $(document).on('click', '.sos-appointment', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');

            if (!citaId) {
                alert('No se pudo identificar la cita.');
                return;
            }

            cambiarEstadoCita(citaId, 'sos');
        });

        
        // Marcar como finalizado: Cambiar estado a "Finalizado"
        $(document).on('click', '.finalizado-appointment', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');

            if (!citaId) {
                alert('No se pudo identificar la cita.');
                return;
            }

            // Llamada AJAX para actualizar el estado
            cambiarEstadoCita(citaId, 'finalizado');
        });
// asd
        // Cancelar cita: Cambiar estado a "Cancelado"
        // $(document).on('click', '.cancelado-appointment', function () {
        //     const citaId = $('#contextMenu').data('cita-id');

        //     if (!citaId) {
        //         alert('No se pudo identificar la cita.');
        //         return;
        //     }

        //     // Llamada AJAX para actualizar el estado
        //     cambiarEstadoCita(citaId, 'cancelado');
        // });
        // Cancelar cita: Cambiar estado a "Cancelado" con confirmación
        $(document).on('click', '.cancelado-appointment', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');

            if (!citaId) {
                alert('No se pudo identificar la cita.');
                return;
            }

            // Mostrar un mensaje de confirmación antes de proceder con la cancelación
            const confirmacion = confirm('¿Estás seguro de que deseas cancelar esta cita?');

            if (confirmacion) {
                // Llamada AJAX para actualizar el estado a "cancelado"
                cambiarEstadoCita(citaId, 'cancelado');
                desmarcarCeldaConCita(citaId);
            } else {
                console.log('Cancelación abortada por el usuario.');
            }
        });


        // Restablecer cita: Cambiar estado a "Pendiente"
        // $(document).on('click', '.reset-status', function () {
        //     const citaId = $('#contextMenu').data('cita-id');

        //     if (!citaId) {
        //         alert('No se pudo identificar la cita.');
        //         return;
        //     }
        //     cambiarEstadoCita(citaId, 'pendiente');
        // });

        $(document).on('click', '.reset-status', function (e) {
            e.preventDefault();
            const citaId = $('#contextMenu').data('cita-id');

            if (!citaId) {
                alert('No se pudo identificar la cita.');
                return;
            }

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
                    const doctorId = $('#doctorSelect').val();
                    const fecha = $('#datePicker').val();
                    actualizarCitas(fecha, doctorId);
                },
                error: function() {
                    alert('❌ Error al restablecer la cita.');
                }
            });
            $('#contextMenu').hide();
        });

        // Eliminar cita: Cambiar estado a "Eliminado"
        $(document).on('click', '.delete-status', function () {
            const citaId = $('#contextMenu').data('cita-id'); // Obtén el ID de la cita desde el menú contextual

            if (!citaId) {
                alert('No se pudo identificar la cita.');
                return;
            }

            // Mostrar un mensaje de confirmación antes de proceder con la eliminación
            const confirmacion = confirm('¿Estás seguro de que deseas eliminar esta cita? Esta acción no se puede deshacer.');

            if (confirmacion) {
                // Llamada AJAX para actualizar el estado a "eliminado"
                cambiarEstadoCita(citaId, 'eliminado');
            } else {
                console.log('Eliminación cancelada por el usuario.');
            }
        });

        // Función genérica para cambiar el estado de una cita
        function cambiarEstadoCita(citaId, nuevoEstado) {
            $.ajax({
                url: '<?= $this->Url->build(['controller' => 'Citas', 'action' => 'changeStatus']); ?>',
                type: 'POST',
                headers: {
                    'X-CSRF-Token': '<?= $this->request->getAttribute("csrfToken"); ?>' // Incluye el token CSRF
                },
                data: { id: citaId, estado: nuevoEstado },
                success: function (response) {
                    if (response.success) {
                        alert('Estado actualizado a: ' + nuevoEstado);
                        
                        // Obtener los valores actuales de doctor y fecha
                        const doctorId = $('#doctorSelect').val();
                        const fechaSeleccionada = $('#datePicker').val();

                        // Actualizar el calendario sin recargar la página
                        if (doctorId && fechaSeleccionada) {
                            actualizarCitas(fechaSeleccionada, doctorId);
                        }
                    } else {
                        alert('No se pudo actualizar el estado. Inténtalo de nuevo.');
                    }
                },
                error: function () {
                    alert('Error al actualizar el estado.');
                }
            });

            // Ocultar el menú contextual después de la acción
            $('#contextMenu').hide();
        }

// Función para desmarcar la celda donde estaba la cita cancelada
function desmarcarCeldaConCita(citaId) {
    // Buscar la celda que contiene la cita cancelada
    const celda = $(`.calendar-cell .cita-item[data-cita-id="${citaId}"]`).closest('.calendar-cell');

    if (celda.length) {
        // Eliminar la clase 'cita-ocupada' de la celda correspondiente
        celda.removeClass('cita-ocupada');
        
        // Eliminar el elemento de la cita visualmente
        $(`.cita-item[data-cita-id="${citaId}"]`).remove();

        console.log(`Celda de cita ${citaId} desmarcada como ocupada y elemento de cita eliminado`);
    }
}


        $(document).on('click', '.whatsapp-confirmation', function (e) {
            e.preventDefault();
            const pacienteId = $('#contextMenu').data('paciente-id');
            const citaId = $('#contextMenu').data('cita-id');

            if (!pacienteId || !citaId) {
                alert('No se pudo identificar la cita o el paciente.');
                return;
            }

            // Busca los datos de la cita desde la tabla renderizada
            const cita = $('.cita-item[data-cita-id="' + citaId + '"]');
            const pacienteNombre = cita.data('paciente-nombre');
            const telefonoCelular = cita.data('telefono-celular');
            const fechaHora = cita.data('fecha-hora'); // formato: "2025-07-10 09:15:00"
            const doctorNombre = cita.data('doctor');

            if (!telefonoCelular || !fechaHora || !doctorNombre) {
                alert('Faltan datos de la cita o paciente.');
                return;
            }

            // Formatear fecha y hora
            const fechaObj = new Date(fechaHora);
            const dia = String(fechaObj.getDate()).padStart(2, '0');
            const mes = String(fechaObj.getMonth() + 1).padStart(2, '0');
            const anio = String(fechaObj.getFullYear()).slice(-2);

            let horas = fechaObj.getHours();
            const minutos = String(fechaObj.getMinutes()).padStart(2, '0');
            const ampm = horas >= 12 ? 'PM' : 'AM';
            horas = horas % 12 || 12; // 0 -> 12

            const horaFormateada = `${horas.toString().padStart(2, '0')}:${minutos} ${ampm}`;
            const fechaFormateada = `${dia}/${mes}/${anio}`;

            // Mensaje final con formato solicitado
         // Mensaje normal
     let mensaje = `Hola ${pacienteNombre}, te saluda SpazioDentale recordándote tu cita el *día ${fechaFormateada} a las ${horaFormateada}* con el Dr. ${doctorNombre}.\n\n*¿Me confirma su asistencia por favor?* Gracias! 💙😉✨`;
 let mssg = `Hola%20${pacienteNombre},%20SpazioDentale%20te%20recuerda%20tu%20cita%20el%20día%20${fechaFormateada}%20a%20las%20${horaFormateada}%20con%20el%20Especialista.%20${doctorNombre}.%20*Por%20favor%20confirmar%20su%20asistencia.%20Gracias!*%20%F0%9F%91%8D%F0%9F%A6%B7%F0%9F%98%89`;
    // Pre-codificar
    const mensajeCodificado = encodeURIComponent(mensaje);

    // Abrir WhatsApp con el mensaje ya codificado (sin volver a encodeURIComponent)
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

            // Busca los datos de la cita desde la tabla renderizada
            const cita = $(`.cita-item[data-cita-id="${citaId}"]`);
            const telefonoCelular = cita.data('telefono-celular'); // Número del paciente

            if (!telefonoCelular) {
                alert('No se encontró el número de teléfono del paciente.');
                return;
            }

            // Redirigir directamente al número de WhatsApp del paciente
            const whatsappUrl = `https://wa.me/51${telefonoCelular}`;

            // Abrir el enlace en una nueva pestaña
            window.open(whatsappUrl, '_blank');
            $('#contextMenu').hide();
        });


        
        // Manejo del clic en la opción "Editar Cita" del menú contextual
        $(document).on('click', '.edit-appointment', function (e) {
            e.preventDefault();
            if (!rolesPermitidos.includes(userRole)) {
                alert('No tienes permiso para agregar citas.');
                return; // Bloquear la acción si el rol no es permitido
            }
            const citaId = $('#contextMenu').data('cita-id'); // Obtener el ID de la cita desde el menú contextual

            if (!citaId) {
                alert('No se pudo identificar la cita para editar.');
                return;
            }

            // Actualiza dinámicamente el enlace `openModal` para editar cita
            const editUrl = '<?= $this->Url->build(['controller' => 'Citas', 'action' => 'edit']); ?>';
            const queryParams = `/${citaId}`;

            // Configurar y disparar el enlace del modal
            $('.openModal').attr('href', editUrl + queryParams).click();

            // Ocultar el menú contextual después de seleccionar la opción
            $('#contextMenu').hide();
        });

// Función para actualizar el encabezado del calendario con las fechas seleccionadas
function actualizarEncabezado(fechaSeleccionada) {
    const fechaInicio = new Date(`${fechaSeleccionada}T00:00:00`);

    $('thead tr th').each(function (index) {
        if (index === 0) return; // Omitir la primera columna (horarios)
        const nuevaFecha = new Date(fechaInicio);
        nuevaFecha.setDate(fechaInicio.getDate() + (index - 1));
        const opcionesFormato = { weekday: 'long', day: '2-digit', month: '2-digit' };
        $(this).text(nuevaFecha.toLocaleDateString('es-ES', opcionesFormato));
        $(this).attr('data-fecha', nuevaFecha.toISOString().split('T')[0]);

        // Obtener el número del día de la semana (0=Domingo, 1=Lunes, ..., 6=Sábado)
        const diaSemana = nuevaFecha.getDay();
        $(this).attr('data-dia-semana', diaSemana);
    });
}


       

function actualizarCitas(fechaSeleccionada, doctorId) {
    if (!doctorId) return;

    $.ajax({
    url: '<?= $this->Url->build(["controller" => "Citas", "action" => "fetchCitas"]); ?>',
    type: 'GET',
    data: { fecha: fechaSeleccionada, doctor_id: doctorId },
    success: function (citas) {
        console.log("Citas recibidas:", citas);

        // Validar que citas sea un array
        if (!Array.isArray(citas)) {
            console.error("Error: La respuesta no es un array válido", citas);
            if (citas.error) {
                alert('Error: ' + citas.error);
            }
            return;
        }

        $('.calendar-cell').empty().removeClass('cita-ocupada');
        

        citas.forEach(function (cita) {
            if (!cita.estado || !cita.paciente || !cita.duracion_minutos) {
                console.log("Cita con datos incompletos:", cita);
                return;
            }

            const parts = cita.fecha_hora.split(/[- :]/);
            const citaDate = new Date(parts[0], parts[1] - 1, parts[2], parts[3], parts[4]);
            const hour = citaDate.getHours();
            const minute = citaDate.getMinutes();
            const duration = parseInt(cita.duracion_minutos, 10); // Duración en minutos
            const cellDate = `${parts[0]}-${parts[1]}-${parts[2]}`;

            console.log(`Procesando cita de ${cita.paciente} desde ${hour}:${minute}, duración: ${duration} min`);

            $('thead tr th').each(function (index) {
                if (index === 0) return;
                const dia = $(this).attr('data-fecha');

                if (dia === cellDate) {
                    let cells = [];
                    let remainingMinutes = duration;
                    let currentHour = hour;
                    let currentMinute = minute;

                    while (remainingMinutes > 0) {
                        let cell = $(`.calendar-cell[data-hour="${currentHour}"][data-minute="${currentMinute}"][data-day="${index - 1}"]`);
                        if (cell.length > 0) {
                            cells.push(cell);
                        }

                        currentMinute += 15;
                        if (currentMinute >= 60) {
                            currentMinute = 0;
                            currentHour++;
                        }
                        remainingMinutes -= 15;
                    }

                    if (cells.length > 0) {
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
                                title="${cita.paciente}&#10;${cita.motivo ? cita.motivo.replace(/"/g, '&quot;') : 'Sin motivo'}"
                                data-paciente-nombre="${cita.paciente}"
                                data-cita-id="${cita.id}"
                                data-paciente-id="${cita.paciente_id}"
                                data-telefono-celular="${cita.telefono_celular || ''}"
                                data-fecha-hora="${cita.fecha_hora}"
                                data-doctor="${cita.doctor}"
                                data-hora-llegada="${cita.hora_llegada || ''}"
                                data-duracion="${cita.duracion_minutos || 0}"
                                 data-estado="${cita.estado}">
                                 <i class="icono-cita"></i> <span class="nombre-paciente">${cita.paciente}</span><br>
                                <small class="hora-llegada">${cita.hora_llegada ? cita.hora_llegada : 'N/A'}</small> <br>
                            </div>
                        `);

                          // --- HACER LA CITA DRAGGABLE ---
                          citaElement.attr('draggable', 'true');

                            citaElement.on('dragstart', function (e) {
                                e.originalEvent.dataTransfer.setData('text/plain', $(this).attr('data-cita-id'));
                            });

                            // --- FIN DRAGGABLE ---
                        // Extraer la hora y minuto de fecha_hora
                        const partsFechaHora = cita.fecha_hora.split(" ")[1].split(":");
                        const citaHora = parseInt(partsFechaHora[0], 10);  // Hora de fecha_hora
                        const citaMinuto = parseInt(partsFechaHora[1], 10); // Minuto de fecha_hora

                        // Verificar si existe hora_llegada
                        let horaLlegada = 0;
                        let minutoLlegada = 0;
                        let diferenciaEnMinutos = 0;

                        if (cita.hora_llegada) {
                            // Extraer la hora y minuto de hora_llegada
                            const partsHoraLlegada = cita.hora_llegada.split(":");
                            horaLlegada = parseInt(partsHoraLlegada[0], 10); // Hora de hora_llegada
                            minutoLlegada = parseInt(partsHoraLlegada[1], 10); // Minuto de hora_llegada

                            console.log(`Comparando cita a las ${citaHora}:${citaMinuto} con llegada a las ${horaLlegada}:${minutoLlegada}`);

                            // Calcular la diferencia en minutos entre la hora de la cita y la hora de llegada
                            const citaFecha = new Date();
                            citaFecha.setHours(citaHora, citaMinuto);

                            const llegadaFecha = new Date();
                            llegadaFecha.setHours(horaLlegada, minutoLlegada);

                            // Calcular la diferencia en milisegundos
                            const diferenciaEnMilisegundos = llegadaFecha - citaFecha;
                            diferenciaEnMinutos = Math.floor(diferenciaEnMilisegundos / (1000 * 60));

                            console.log(`Diferencia en minutos: ${diferenciaEnMinutos}`);
                        } else {
                            // Si no hay hora de llegada, se marca como "N/A" y se asignan colores de acuerdo al estado
                            console.log("No hay hora de llegada");
                        }

                        // Si la diferencia es de 5 minutos o menos, se marca como verde (a tiempo o pequeña tardanza)
                        if (diferenciaEnMinutos <= 5 && cita.hora_llegada) {
                            $('.hora-llegada', citaElement).css('background-color', 'green');
                        } else if (cita.hora_llegada) {
                            // Si es mayor a 5 minutos, se marca como rojo (tarde)
                            $('.hora-llegada', citaElement).css('background-color', 'red');
                        } else {
                            // Si no hay hora de llegada, no se marca ningún color
                            $('.hora-llegada', citaElement).css('background-color', 'transparent');
                        }

                        citaElement.css({
                            "position": "absolute",
                            "top": "5%",  
                            "left": "10%",  
                            "height": `calc(${(duration / 15) * 100}% - 10px)`,  
                            "width": "80%",  
                            "border-radius": "6px",  
                            "padding": "5px",
                            "z-index": "10"
                        });

                        cells[0].append(citaElement);
                        if (!cita.tiene_historia_clinica_dni) {
                            $('.nombre-paciente', citaElement).css('color', '#1a1a1a');
                        }
                        // cells.forEach(cell => cell.addClass('cita-ocupada'));
                         // **No marcar la celda como ocupada si el , podemos permitir solapamiento aqui, en este caso lo evitamos estado es "cancelado"**
                        if (cita.estado.toLowerCase() !== 'cancelado') {
                            cells.forEach(cell => cell.addClass('cita-ocupada'));
                        }


                    }
                }
            });
        });
    },
    error: function () {
        console.log("Error al cargar las citas.");
    }
});
}
// evento drag
   
$(document).ready(function () {
    $(document).on('dragover', '.calendar-cell, .cita-item', function (e) {
        e.preventDefault();
    });

    // Permitir que los eventos drop también se capturen en los elementos cita-item
    // Variable para evitar procesamiento múltiple del mismo drop
    let lastProcessedCitaId = null;
    let lastProcessTime = 0;
    
    $(document).on('drop', '.calendar-cell, .cita-item', function (e) {
        e.preventDefault();
        e.stopPropagation(); // Detener la propagación del evento
        
        const citaId = e.originalEvent.dataTransfer.getData('text/plain');
        const currentTime = new Date().getTime();
        
        // Evitar procesamiento duplicado si es la misma cita en menos de 1 segundo
        if (citaId === lastProcessedCitaId && (currentTime - lastProcessTime) < 1000) {
            console.log("Evitando procesamiento duplicado de drop para cita ID:", citaId);
            return;
        }
        // Registrar esta cita y hora para control de duplicados
        lastProcessedCitaId = citaId;
        lastProcessTime = currentTime;
        
        console.log("Evento drop detectado.");
        console.log("Cita ID arrastrada:", citaId);
        // Determinar la ubicación real donde se soltó la cita
        let targetHour, targetMinute, targetDay;

        // Verificar si estamos en una cita o en una celda de calendario
        const isCitaEl = $(this).hasClass('cita-item');
        
        if (isCitaEl || $(this).closest('.cita-item').length > 0) {
            // Caso 1: Soltamos sobre una cita existente
            console.log("Detectado drop sobre cita existente");
            
            // Obtener el elemento de la cita y su posición
            const citaEl = isCitaEl ? $(this) : $(this).closest('.cita-item');
            const citaRect = citaEl[0].getBoundingClientRect();
            
            // Calcular la posición relativa vertical dentro de la cita
            const relativeY = e.originalEvent.clientY - citaRect.top;
            const totalHeight = citaRect.height;
            
            // Convertir posición a fracción (0-1) de la altura total de la cita
            const relativeFraction = relativeY / totalHeight;
            
            // Obtener datos de la celda base donde empieza la cita
            const baseCell = citaEl.closest('.calendar-cell');
            const baseHour = parseInt(baseCell.attr('data-hour'), 10);
            const baseMinute = parseInt(baseCell.attr('data-minute'), 10);
            const duracionCitaExistente = parseInt(citaEl.data('duracion'), 10);
            
            // Convertir la fracción relativa en minutos desde el inicio de la cita
            const minutesFromStart = Math.floor(relativeFraction * duracionCitaExistente);
            
            // Redondear al intervalo de 15 minutos más cercano
            const roundedMinutes = Math.floor(minutesFromStart / 15) * 15;
            
            // Calcular la hora y minuto objetivo
            targetMinute = baseMinute + roundedMinutes;
            targetHour = baseHour;
            
            // Ajustar si los minutos exceden 60
            while (targetMinute >= 60) {
                targetMinute -= 60;
                targetHour++;
            }
            

            targetDay = baseCell.attr('data-day');
            
            console.log(`Drop en cita existente: posición relativa ${relativeFraction.toFixed(2)}, minutos desde inicio: ${minutesFromStart}, hora objetivo: ${targetHour}:${targetMinute}`);
        } else {
            // Caso 2: Soltamos directamente en una celda de calendario
            console.log("Detectado drop sobre celda de calendario");
            
            const cell = $(this);
            targetHour = parseInt(cell.attr('data-hour'), 10);
            targetMinute = parseInt(cell.attr('data-minute'), 10);
            targetDay = cell.attr('data-day');
        }
        
        // Buscar la celda calendario correspondiente a la posición calculada
        const targetCell = $(`.calendar-cell[data-hour="${targetHour}"][data-minute="${targetMinute}"][data-day="${targetDay}"]`);
        
        if (targetCell.length === 0) {
            console.error(`No se encontró celda para la hora ${targetHour}:${targetMinute} en el día ${targetDay}`);
            return;
        }
        
        console.log(`Celda objetivo identificada: hora=${targetHour}, minuto=${targetMinute}, día=${targetDay}`);
        
        const newDate = $('thead tr th').eq(parseInt(targetDay) + 1).attr('data-fecha');
        console.log("Nueva fecha:", newDate, "| Hora:", targetHour, ":", targetMinute, "| Día índice:", targetDay);

        // Obtener el elemento de la cita que se está moviendo
        const citaElement = $(`.cita-item[data-cita-id="${citaId}"]`);
        
        if (citaElement.length === 0) {
            console.error('Elemento de cita no encontrado');
            return;
        }

        const citaDuration = parseInt(citaElement.data('duracion'), 10);
        console.log("Duración de la cita:", citaDuration, "min");

        // SOLUCIÓN: Identificar todas las celdas actualmente ocupadas por la cita que estamos moviendo
        // Esto nos permitirá ignorarlas al verificar disponibilidad
        const celdasDeMiCitaActual = [];
        $(`.calendar-cell`).each(function() {
            const cell = $(this);
            const citasEnCelda = cell.find(`.cita-item[data-cita-id="${citaId}"]`);
            if (citasEnCelda.length > 0) {
                // Esta celda contiene la cita inicial
                celdasDeMiCitaActual.push(cell);
                
                // Añadir también las celdas adicionales que ocupa esta cita
                const horaInicial = parseInt(cell.attr('data-hour'), 10);
                const minutoInicial = parseInt(cell.attr('data-minute'), 10);
                const diaInicial = cell.attr('data-day');
                let tiempoRestante = citaDuration - 15; // Menos los primeros 15 minutos
                let horaActual = horaInicial;
                let minutoActual = minutoInicial + 15;
                
                // Ajustar si los minutos exceden 60
                if (minutoActual >= 60) {
                    minutoActual = 0;
                    horaActual++;
                }
                
                // Añadir las celdas adicionales que ocupa la cita
                while (tiempoRestante > 0) {
                    const celdaAdicional = $(`.calendar-cell[data-hour="${horaActual}"][data-minute="${minutoActual}"][data-day="${diaInicial}"]`);
                    if (celdaAdicional.length > 0) {
                        celdasDeMiCitaActual.push(celdaAdicional);
                    }
                    
                    minutoActual += 15;
                    if (minutoActual >= 60) {
                        minutoActual = 0;
                        horaActual++;
                    }
                    tiempoRestante -= 15;
                }
            }
        });
        
        console.log(`La cita actual ocupa ${celdasDeMiCitaActual.length} celdas que ignoraremos en la verificación.`);

        // Verificar disponibilidad de todas las celdas necesarias
        let cells = [];
        let remainingMinutes = citaDuration;
        let currentHour = targetHour;
        let currentMinute = targetMinute;

        let allFree = true;
        console.log("Verificando disponibilidad de celdas...");

        while (remainingMinutes > 0) {
            const cell = $(`.calendar-cell[data-hour="${currentHour}"][data-minute="${currentMinute}"][data-day="${targetDay}"]`);
            
            if (cell.length > 0) {
                if (!cell.hasClass('horario-disponible')) {
                    console.warn(`Celda ${currentHour}:${currentMinute} no está en horario disponible.`);
                    allFree = false;
                    break;
                }
                // Añadir más logs para depuración
                console.log(`Comprobando celda ${currentHour}:${currentMinute}:`);
                console.log(`  - ¿Tiene clase cita-ocupada?: ${cell.hasClass('cita-ocupada')}`);
                
                // SOLUCIÓN: Verificar si esta celda es parte de la cita que estamos moviendo
                const esCeldaDeMiCitaActual = celdasDeMiCitaActual.some(miCelda => {
                    return miCelda.attr('data-hour') === cell.attr('data-hour') && 
                           miCelda.attr('data-minute') === cell.attr('data-minute') && 
                           miCelda.attr('data-day') === cell.attr('data-day');
                });
                
                console.log(`  - ¿Es parte de mi cita actual?: ${esCeldaDeMiCitaActual}`);
                
                // Una celda se considera ocupada si:
                // 1. Tiene la clase 'cita-ocupada'
                // 2. Y NO es parte de la cita que estamos moviendo actualmente
                if (cell.hasClass('cita-ocupada') && !esCeldaDeMiCitaActual) {
                    console.log(`Celda ${currentHour}:${currentMinute} ocupada por otra cita`);
                    allFree = false;
                    break;
                }
                cells.push(cell);
            } else {
                console.warn(`Celda no encontrada en ${currentHour}:${currentMinute}.`);
                allFree = false;
                break;
            }

            currentMinute += 15;
            if (currentMinute >= 60) {
                currentMinute = 0;
                currentHour++;
            }
            remainingMinutes -= 15;
        }

        if (!allFree || cells.length === 0) {
            console.log('No se puede mover la cita. Horario no disponible o una o más celdas están ocupadas.');
            alert('No se puede mover la cita. Horario no disponible o una o más celdas están ocupadas.');
            return;
        }
        
        // Depuración adicional
        console.log(`Moviendo cita a: ${targetHour}:${targetMinute}, usando ${cells.length} celdas`);
        cells.forEach((cell, idx) => {
            console.log(`Celda ${idx}: hora=${cell.attr('data-hour')}, minuto=${cell.attr('data-minute')}`);
        });

        // SOLUCIÓN: Usar las celdas identificadas previamente
        // Eliminar la clase ocupada de las celdas anteriores que tenían la cita
        console.log(`Eliminando marca 'ocupada' de ${celdasDeMiCitaActual.length} celdas anteriores`);
        celdasDeMiCitaActual.forEach(cell => cell.removeClass('cita-ocupada'));

        // Si llegamos aquí, es seguro mover la cita
        console.log("Todas las celdas están disponibles. Moviendo cita...");
        citaElement.detach();

        // Estilo
        citaElement.css({
            "position": "absolute",
            "top": "5%",
            "left": "10%",
            "height": `calc(${(citaDuration / 15) * 100}% - 10px)`,
            "width": "80%",
            "border-radius": "6px",
            "padding": "5px",
            "z-index": "10"
        });

        // Agrega a la nueva celda
        cells[0].append(citaElement);

        // Marca las nuevas celdas como ocupadas
        cells.forEach(cell => cell.addClass('cita-ocupada'));
        console.log("Cita colocada visualmente. Enviando datos al servidor...");

        const csrfToken = $('meta[name="csrfToken"]').attr('content');
        $.ajax({
            url: '<?= $this->Url->build(["controller" => "Citas", "action" => "actualizarHora"]); ?>',
            method: 'POST',
            headers: {
                'X-CSRF-Token': csrfToken
            },
            data: {
                cita_id: citaId,
                nueva_fecha: newDate,
                nueva_hora: `${String(targetHour).padStart(2, '0')}:${String(targetMinute).padStart(2, '0')}`
            },
            success: function (response) {
                alert('Cita actualizada correctamente');
                // Actualizar el calendario después de mover la cita
                const doctorId = $('#doctorSelect').val();
                const dateNew = $('#datePicker').val();
                actualizarCitas(dateNew, doctorId);                
            },
            error: function (xhr, status, error) {
                alert('Hubo un error al actualizar la cita');
            }
        });
    });
});
// fin drag


        // Manejo del selector de doctores
        $('#doctorSelect').on('change', function () {
            const doctorId = $(this).val();
            const fechaSeleccionada = $('#datePicker').val();
            if (doctorId && fechaSeleccionada) {
                actualizarEncabezado(fechaSeleccionada);
                actualizarCitas(fechaSeleccionada, doctorId);
            }
        });

        // Manejo del selector de fecha
        $('#datePicker').on('change', function () {
            const fechaSeleccionada = $(this).val();
            const doctorId = $('#doctorSelect').val();
            if (doctorId && fechaSeleccionada) {
                actualizarEncabezado(fechaSeleccionada);
                actualizarCitas(fechaSeleccionada, doctorId);
                actualizarHorariosDisponibles();
            }
        });

        // Inicializa el calendario con la fecha y doctor seleccionados
        const fechaInicial = $('#datePicker').val();
        const doctorInicial = $('#doctorSelect').val();
        if (doctorInicial && fechaInicial) {
            actualizarEncabezado(fechaInicial);
            actualizarCitas(fechaInicial, doctorInicial);
        }

        let horariosDoctorActual = []; // Variable global para almacenar los horarios

$(document).ready(function () {
    const doctorId = $('#doctorSelect').val();
    if (doctorId) {
        cargarHorariosDoctor(doctorId);
    }

    // Evento: Cuando se cambia la fecha, recalcular horarios disponibles
    $('#fecha-selector').on('change', function () {
        actualizarHorariosDisponibles();
    });

    // Evento: Cuando se selecciona un doctor, cargar sus horarios
    $('#doctorSelect').on('change', function () {
        const doctorId = $(this).val();
        cargarHorariosDoctor(doctorId);
    });
});

// 🔹 Función para obtener los horarios del doctor seleccionado
function cargarHorariosDoctor(doctorId) {
    if (!doctorId) {
        horariosDoctorActual = []; // Resetear si no hay doctor seleccionado
        actualizarHorariosDisponibles(); // Limpiar los horarios resaltados
        return;
    }

    $.ajax({
        url: '<?= $this->Url->build(["controller" => "HorariosDoctores", "action" => "obtenerHorarios"]); ?>',
        method: 'GET',
        data: { doctor_id: doctorId },
        dataType: 'json',
        success: function (response) {
            console.log('Respuesta completa de la API:', response);

            if (response.success && Array.isArray(response.horarios) && response.horarios.length > 0) {
                horariosDoctorActual = response.horarios;
                console.log('Horarios cargados:', horariosDoctorActual);
            } else {
                console.warn('No se encontraron horarios configurados para este doctor.');
                alert('No se encontraron horarios configurados para este doctor.');
                horariosDoctorActual = [];
            }

            actualizarHorariosDisponibles(); // Recalcular después de obtener los datos
        },
        error: function (xhr, status, error) {
            console.error('Error al cargar los horarios:', status, error);
            alert('Error al cargar los horarios del doctor.');
            horariosDoctorActual = [];
            actualizarHorariosDisponibles();
        }
    });
}

// 🔹 Función para resaltar los horarios disponibles en la tabla
function actualizarHorariosDisponibles() {
    const doctorId = $('#doctorSelect').val();
    if (!doctorId) return;

    // Obtener las fechas visibles en el calendario
    const fechas = [];
    $('thead tr th').each(function (index) {
        if (index === 0) return;
        const fecha = $(this).attr('data-fecha');
        if (fecha && !fechas.includes(fecha)) {
            fechas.push(fecha);
        }
    });

    // Para cada fecha visible, obtener todos los bloqueos en una sola llamada
    fechas.forEach(fecha => {
        $.ajax({
            url: '<?= $this->Url->build(["controller" => "HorariosBloqueos", "action" => "obtenerBloqueosPorFecha"]); ?>',
            method: 'GET',
            data: {
                fecha: fecha,
                doctor_id: doctorId
            },
            dataType: 'json',
            success: function (response) {
                // Procesar todos los bloqueos recibidos
                const bloqueos = response.bloqueos || [];
                
                // Iterar sobre todas las celdas de esa fecha
                $('thead tr th').each(function (thIndex) {
                    if (thIndex === 0) return;
                    const fechaColumna = $(this).attr('data-fecha');
                    if (fechaColumna !== fecha) return;

                    // Procesar todas las celdas de esta columna (fecha)
                    $('.calendar-cell').each(function () {
                        const $cell = $(this);
                        const dayIndex = $cell.data('day');
                        const hour = $cell.data('hour');
                        const minute = $cell.data('minute');

                        // Verificar si esta celda pertenece a esta columna
                        if ($(`thead tr th:eq(${dayIndex + 1})`).attr('data-fecha') !== fecha) return;

                        // Convertir la fecha a objeto Date en la zona horaria de Lima
                        const fechaLima = new Date(`${fecha}T00:00:00-05:00`);
                        let diaSemana = fechaLima.getDay();

                        // Verificar si este horario está disponible en el día correspondiente
                        const horarioValido = horariosDoctorActual.some(horario =>
                            horario.dia_semana === diaSemana &&
                            `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}:00` >= horario.hora_inicio &&
                            `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}:00` <= horario.hora_fin
                        );

                        // Si el horario recurrente no es válido, limpiar clases
                        if (!horarioValido) {
                            $cell.removeClass('horario-disponible horario-bloqueado');
                            return;
                        }

                        // Verificar si esta hora está bloqueada
                        const horaFormato = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}:00`;
                        const estaBloqueada = bloqueos.some(bloqueo =>
                            horaFormato >= bloqueo.hora_inicio && horaFormato < bloqueo.hora_fin
                        );

                        if (estaBloqueada) {
                            $cell.addClass('horario-bloqueado').removeClass('horario-disponible');
                        } else {
                            $cell.addClass('horario-disponible').removeClass('horario-bloqueado');
                        }
                    });
                });
            },
            error: function () {
                console.error('Error al obtener bloqueos para fecha:', fecha);
            }
        });
    });
}


// 4️⃣ Evento: Doble clic en el calendario para crear una cita
$(document).on('dblclick', '.calendar-cell', function () {
    if (!rolesPermitidos.includes(userRole)) {
        alert('No tienes permiso para agregar citas.');
        return;
    }

    const hour = $(this).data('hour');
    const minute = $(this).data('minute');
    const dayIndex = $(this).data('day');
    const doctorId = $('#doctorSelect').val();
    const fechaBase = $('#datePicker').val();

    if (!doctorId || !fechaBase) {
        alert('Por favor, seleccione un odontólogo y una fecha primero.');
        return;
    }

    // Crear fecha en UTC-5 (Lima)
    const fechaSeleccionada = new Date(`${fechaBase}T00:00:00-05:00`); // Fijar la zona horaria
    fechaSeleccionada.setDate(fechaSeleccionada.getDate() + dayIndex);
     // Verificar si la fecha seleccionada es anterior a la fecha actual
     const fechaActual = new Date();
    fechaActual.setHours(0, 0, 0, 0); 
     console.log('fecha select', fechaSeleccionada);
     console.log('fecha Actual', fechaActual);
    //revisar esta parte
    if (fechaSeleccionada < fechaActual) {
        alert('La fecha seleccionada no puede ser anterior a la fecha actual.');
        return;
    }
    // Convertir a formato de fecha YYYY-MM-DD
    const fechaFinal = fechaSeleccionada.toISOString().split('T')[0];
    
    // Construir hora en formato HH:MM:SS
    const horaFinal = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}:00`;

    // 🔹 **Validar que la hora seleccionada esté dentro del horario permitido**
    const horarioValido = horariosDoctorActual.some(horario => {
        return (
            horario.dia_semana === fechaSeleccionada.getUTCDay() && // Comparar con UTC-5
            horaFinal >= horario.hora_inicio && 
            horaFinal <= horario.hora_fin
        );
    });

    if (!horarioValido) {
        alert('El horario seleccionado está fuera del rango permitido para este doctor.');
        return;
    }

    // 🔹 **Verificar si la cita ya existe antes de abrir el modal**
    $.ajax({
        url: '<?= $this->Url->build(["controller" => "Citas", "action" => "verificarDisponibilidad"]); ?>',
        method: 'GET',
        data: {
            fecha_hora: `${fechaFinal} ${horaFinal}`,
            doctor_id: doctorId
        },
        success: function (response) {
            if (response.disponible) {
                const modalUrl = '<?= $this->Url->build(["controller" => "Citas", "action" => "add"]); ?>';
                const queryParams = `?fecha_hora=${fechaFinal} ${horaFinal}&doctor_id=${doctorId}`;
                $('.openModal').attr('href', modalUrl + queryParams).click();
            } else {
                alert('El horario seleccionado ya está ocupado. Por favor, elige otro horario.');
            }
        },
        error: function () {
            alert('Hubo un error al verificar la disponibilidad.');
        }
    });
});




        if (!rolesPermitidos.includes(userRole)) {
            // Deshabilitar el selector de odontólogos
            $('#doctorSelect').prop('disabled', true);  
        }
    });
 
</script>

<script>
function generateMiniCalendar(month, year, container) {
  const locale = "es-PE"; 
  const tz = "America/Lima"; 

  const root = document.createElement("div");
  root.className = "mini-calendar";

  // Encabezado con mes y año
  const header = document.createElement("div");
  header.className = "mc-header";
  header.textContent = new Date(year, month, 1)
    .toLocaleString(locale, { month: "long", year: "numeric", timeZone: tz });
  root.appendChild(header);

  // Encabezados de días
  const dowRow = document.createElement("div");
  dowRow.className = "mc-grid";
  ["lun","mar","mié","jue","vie","sáb","dom"].forEach(label => {
    const d = document.createElement("div");
    d.className = "mc-dow";
    d.textContent = label;
    dowRow.appendChild(d);
  });
  root.appendChild(dowRow);

  // Contenedor de días
  const grid = document.createElement("div");
  grid.className = "mc-grid";

  const firstDay = (new Date(year, month, 1).getDay() + 6) % 7; // Lunes=0
  const daysInMonth = new Date(year, month + 1, 0).getDate();

  // Espacios antes del 1
  for (let i = 0; i < firstDay; i++) {
    const blank = document.createElement("div");
    blank.className = "mc-blank";
    grid.appendChild(blank);
  }

  const datePicker = document.getElementById("datePicker");
  const selectedDate = new Date(datePicker.value);

  // Día actual en Lima
  const nowLima = new Date(new Date().toLocaleString("en-US", { timeZone: tz }));

  // Días del mes
  for (let day = 1; day <= daysInMonth; day++) {
    const cell = document.createElement("div");
    cell.className = "mc-day";
    cell.textContent = day;

    const cellDate = new Date(year, month, day);

    // Marcar como hoy en Lima
    if (nowLima.getFullYear() === year &&
        nowLima.getMonth() === month &&
        nowLima.getDate() === day) {
      cell.classList.add("mc-today");
    }

    // Click en día
    cell.addEventListener("click", () => {
      const monthStr = String(month + 1).padStart(2, "0");
      const dayStr = String(day).padStart(2, "0");
      const newDate = `${year}-${monthStr}-${dayStr}`;

      datePicker.value = newDate;

      // Limpiar selección en TODOS los calendarios
      document.querySelectorAll(".mini-calendar .mc-day")
        .forEach(d => d.classList.remove("mc-selected"));

      // Marcar seleccionado
      cell.classList.add("mc-selected");

      // Disparar lógica existente
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
</script>

