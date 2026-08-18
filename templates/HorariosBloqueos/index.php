<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\HorariosBloqueo> $horariosBloqueos
 * @var iterable<\App\Model\Entity\HorariosDoctor> $horariosDoctores
 * @var array $doctores
 * @var array $doctoresSet
 */
?>

<style>
    .bloqueos-header {
        background-color: #f8f9fa;
        border-bottom: 3px solid #00a8cc;
        padding: 12px 0;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-left: 20px;
        padding-right: 20px;
    }

    .bloqueos-header h2 {
        color: #003366;
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .doctor-selector {
        margin-bottom: 20px;
    }

    .calendar-bloqueos {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 15px;
        margin-bottom: 30px;
        overflow-x: auto;
        user-select: none;
    }

    .calendar-grid-bloqueos {
        display: grid;
        grid-template-columns: 80px repeat(7, 1fr);
        gap: 1px;
        background-color: #ddd;
        padding: 1px;
        min-width: 800px;
    }

    .hora-cell {
        background-color: #f5f5f5;
        padding: 8px;
        font-weight: bold;
        font-size: 11px;
        text-align: center;
        border: 1px solid #ddd;
    }

    .dia-header {
        background-color: #003366;
        color: white;
        padding: 10px;
        font-weight: bold;
        text-align: center;
        border: 1px solid #003366;
        font-size: 12px;
    }

    .celda-bloqueo {
        background-color: #e8f4f8;
        border: 1px solid #ddd;
        padding: 4px;
        font-size: 10px;
        text-align: center;
        cursor: pointer;
        position: relative;
        min-height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s;
    }

    .celda-bloqueada {
        background-color: #ff6b6b;
        color: white;
        font-weight: bold;
        border: 1px solid #cc5555;
    }

    .celda-bloqueada:hover {
        background-color: #ff5252;
    }

    .celda-seleccionada {
        background-color: #4ecdc4 !important;
        color: white !important;
        font-weight: bold;
        border: 2px solid #2a9d8f !important;
        box-shadow: inset 0 0 5px rgba(0,0,0,0.2);
    }

    .celda-bloqueo:hover {
        background-color: #d0e8f0;
    }

    .celda-arrastrando {
        background-color: #4ecdc4 !important;
        color: white !important;
        font-weight: bold;
        border: 2px solid #2a9d8f !important;
        box-shadow: inset 0 0 5px rgba(0,0,0,0.2);
    }

    .info-bloqueo {
        background-color: #fff3cd;
        border: 2px solid #ffc107;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
        display: none;
    }

    .info-bloqueo.activo {
        display: block;
    }

    .btn-crear-bloqueo {
        background-color: #ff6b6b;
        border: none;
        color: white;
    }

    .btn-crear-bloqueo:hover {
        background-color: #ff5252;
        color: white;
    }

    .navegacion-bloqueos {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .navegacion-bloqueos button {
        padding: 6px 12px;
        background-color: #e8e8e8;
        color: #333;
        border: 1px solid #ccc;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.2s;
    }

    .navegacion-bloqueos button:hover {
        background-color: #00a8cc;
        color: white;
        border-color: #00a8cc;
    }

    #semana-mostrada {
        color: #333;
        margin-left: 15px;
        font-weight: 600;
        font-size: 14px;
        min-width: 200px;
        text-align: center;
    }

    .mes-mostrado {
        color: #00a8cc;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 15px;
        text-align: center;
    }

    .bloqueos-tabla {
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        margin-top: 30px;
    }
</style>

<?php
// Inicializar $doctoresSet y preparar datos antes de renderizar atributos
if (is_array($horariosBloqueos)) {
    $horariosBloqueosList = $horariosBloqueos;
} elseif (is_object($horariosBloqueos)) {
    $horariosBloqueosList = method_exists($horariosBloqueos, 'items') 
        ? $horariosBloqueos->items()->toList() 
        : $horariosBloqueos->toList();
} else {
    $horariosBloqueosList = [];
}

if (is_array($horariosDoctores)) {
    $horariosDoctoresList = $horariosDoctores;
} elseif (is_object($horariosDoctores)) {
    $horariosDoctoresList = method_exists($horariosDoctores, 'items')
        ? $horariosDoctores->items()->toList()
        : $horariosDoctores->toList();
} else {
    $horariosDoctoresList = [];
}

// Usar la variable $doctores del controlador como base principal
$doctoresSet = [];
if (!empty($doctores)) {
    if (is_array($doctores)) {
        foreach ($doctores as $doctor) {
            $doctoresSet[$doctor->id] = $doctor;
        }
    } else {
        foreach ($doctores as $doctor) {
            $doctoresSet[$doctor->id] = $doctor;
        }
    }
}
?>

<div class="container-fluid mt-4" 
    data-horarios-bloqueos='<?php echo json_encode(array_map(function($h) {
        return [
            'doctor_id' => (int)$h->doctor_id,
            'fecha' => $h->fecha->format('Y-m-d'),
            'hora_inicio' => trim((string)$h->hora_inicio),
            'hora_fin' => trim((string)$h->hora_fin),
            'motivo' => isset($h->motivo) ? trim((string)$h->motivo) : ''
        ];
    }, $horariosBloqueosList)); ?>'
    data-horarios-doctores='<?php echo json_encode(array_map(function($h) {
        return [
            'doctor_id' => (int)$h->doctor_id,
            'dia_semana' => (int)$h->dia_semana,
            'hora_inicio' => trim((string)$h->hora_inicio),
            'hora_fin' => trim((string)$h->hora_fin)
        ];
    }, $horariosDoctoresList)); ?>'
    data-doctores-info='<?php echo json_encode(array_values(array_map(function($d) {
        return [
            'id' => (int)$d->id,
            'nombre' => trim((string)$d->nombre),
            'apellido' => trim((string)$d->apellido)
        ];
    }, $doctoresSet))); ?>'>
    <div class="mes-mostrado" id="mes-mostrado">Enero 2026</div>
    
    <div class="bloqueos-header">
        <h2><i class="fas fa-lock"></i> Bloqueo de Horarios</h2>
        <div class="navegacion-bloqueos">
            <button id="btn-anterior-semana" class="btn btn-sm"><i class="fas fa-chevron-left"></i></button>
            <button id="btn-hoy-semana" class="btn btn-sm">Hoy</button>
            <button id="btn-siguiente-semana" class="btn btn-sm"><i class="fas fa-chevron-right"></i></button>
            <span id="semana-mostrada">Cargando...</span>
        </div>
    </div>

    <!-- Selector de Doctor -->
    <div class="doctor-selector">
        <label class="form-label fw-bold">Selecciona un Doctor:</label>
        <select id="doctor-selector-bloqueos" class="form-control form-control-sm" style="max-width: 300px;">
            <option value="">-- Selecciona un Doctor --</option>
            <?php foreach ($doctoresSet as $doctor): ?>
                <option value="<?= $doctor->id ?>">
                    <?= h($doctor->nombre . ' ' . $doctor->apellido) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="background-color: #e7f3ff; border-left: 4px solid #2196F3; padding: 10px; margin-bottom: 15px; border-radius: 4px; font-size: 13px; color: #0c5aa0;">
        <i class="fas fa-mouse"></i> <strong>Cómo usar:</strong> Haz click y arrastra sobre los horarios para seleccionar múltiples bloques de 15 minutos. Los horarios disponibles están resaltados en verde.
    </div>

    <!-- Información de Bloqueo Seleccionado -->
    <div class="info-bloqueo" id="info-bloqueo">
        <h5 id="info-titulo">Crear Bloqueo</h5>
        <div id="info-detalles" style="margin-bottom: 15px; padding: 10px; background: white; border-radius: 5px;"></div>
        <form id="formBloqueHorario" method="post" action="<?= $this->Url->build(['action' => 'add']) ?>">
            <?= $this->Form->hidden('_csrfToken', ['value' => $this->request->getAttribute('csrfToken')]) ?>
            <input type="hidden" name="doctor_id" id="bloqueo-doctor-id" required>
            <input type="hidden" name="fecha" id="bloqueo-fecha-hidden" required>
            
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Hora Inicio:</label>
                    <input type="time" class="form-control form-control-sm" name="hora_inicio" id="bloqueo-hora-inicio" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Hora Fin:</label>
                    <input type="time" class="form-control form-control-sm" name="hora_fin" id="bloqueo-hora-fin" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Motivo (Opcional):</label>
                    <input type="text" class="form-control form-control-sm" name="motivo" placeholder="Ej: Almuerzo, Reunión, etc.">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 text-end">
                    <button type="button" class="btn btn-secondary btn-sm" id="btn-cancelar-bloqueo-info">Cancelar</button>
                    <button type="submit" class="btn btn-crear-bloqueo btn-sm">
                        <i class="fas fa-save"></i> Crear Bloqueo
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Calendario Semanal -->
    <div class="calendar-bloqueos" id="calendar-bloqueos">
        <div class="calendar-grid-bloqueos" id="calendar-grid-bloqueos">
            <!-- Se llena con JavaScript -->
        </div>
    </div>

    <!-- Tabla de Bloqueos Existentes -->
    <div class="bloqueos-tabla">
        <h4 class="mb-3"><i class="fas fa-list"></i> Bloqueos Registrados</h4>
        
        <?php if (empty($horariosBloqueosList)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No hay bloqueos registrados
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 20%;">Doctor</th>
                            <th style="width: 15%;">Fecha</th>
                            <th style="width: 12%;">Inicio</th>
                            <th style="width: 12%;">Fin</th>
                            <th style="width: 30%;">Motivo</th>
                            <th style="width: 11%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($horariosBloqueosList as $bloqueo): ?>
                        <tr>
                            <td><?= $bloqueo->hasValue('doctore') ? h($bloqueo->doctore->nombre . ' ' . $bloqueo->doctore->apellido) : '-' ?></td>
                            <td><?= $this->Time->format($bloqueo->fecha, 'dd/MM/yyyy') ?></td>
                            <td><?= h($bloqueo->hora_inicio) ?></td>
                            <td><?= h($bloqueo->hora_fin) ?></td>
                            <td><?= !empty($bloqueo->motivo) ? h(strlen($bloqueo->motivo) > 40 ? substr($bloqueo->motivo, 0, 40) . '...' : $bloqueo->motivo) : '-' ?></td>
                            <td>
                                <?= $this->Html->link(
                                    '<i class="fas fa-edit"></i>',
                                    ['action' => 'edit', $bloqueo->id],
                                    ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm openModal']
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener datos desde atributos data del contenedor
    const container = document.querySelector('[data-horarios-bloqueos]');
    const horariosBloqueos = JSON.parse(container.getAttribute('data-horarios-bloqueos'));
    const horariosDoctores = JSON.parse(container.getAttribute('data-horarios-doctores'));
    const doctoresInfo = JSON.parse(container.getAttribute('data-doctores-info'));
    
    const doctorSelector = document.getElementById('doctor-selector-bloqueos');
    const calendarGrid = document.getElementById('calendar-grid-bloqueos');
    const semanaMostrada = document.getElementById('semana-mostrada');
    const mesMostrado = document.getElementById('mes-mostrado');
    const infoBloqueo = document.getElementById('info-bloqueo');
    const btnAnterior = document.getElementById('btn-anterior-semana');
    const btnHoy = document.getElementById('btn-hoy-semana');
    const btnSiguiente = document.getElementById('btn-siguiente-semana');
    const btnCancelar = document.getElementById('btn-cancelar-bloqueo-info');

    let fechaActual = new Date();
    let arrastrandoActivo = false;
    let celdasSeleccionadas = new Set();
    let doctorArrastrando = null;
    let fechaArrastrando = null;

    function obtenerFechaLocal(fecha) {
        const year = fecha.getFullYear();
        const month = String(fecha.getMonth() + 1).padStart(2, '0');
        const day = String(fecha.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function obtenerLunesSemanaSemana(fecha) {
        const day = fecha.getDay();
        const diff = fecha.getDate() - day + (day === 0 ? -6 : 1);
        return new Date(fecha.setDate(diff));
    }

    function iniciarArrastre(event) {
        if (event.button !== 0) return; // Solo botón izquierdo
        
        const doctorId = event.target.getAttribute('data-doctor-id');
        const fecha = event.target.getAttribute('data-fecha');
        
        if (!doctorId || !fecha) return;

        arrastrandoActivo = true;
        limpiarSeleccion();
        doctorArrastrando = doctorId;
        fechaArrastrando = fecha;

        // Agregar la primera celda
        celdasSeleccionadas.add(event.target);
        event.target.classList.add('celda-arrastrando');
    }

    function duranteArrastre(event) {
        if (!arrastrandoActivo) return;

        const celda = event.target;
        const doctorId = celda.getAttribute('data-doctor-id');
        const fecha = celda.getAttribute('data-fecha');

        // Solo agregar si es del mismo doctor y fecha
        if (doctorId === doctorArrastrando && fecha === fechaArrastrando) {
            celdasSeleccionadas.add(celda);
            celda.classList.add('celda-arrastrando');
        }
    }

    function finalizarArrastre(event) {
        if (!arrastrandoActivo) return;

        arrastrandoActivo = false;

        if (celdasSeleccionadas.size === 0) {
            limpiarSeleccion();
            return;
        }

        // Obtener las horas de las celdas seleccionadas
        const horas = Array.from(celdasSeleccionadas)
            .map(celda => celda.getAttribute('data-hora'))
            .sort();

        if (horas.length === 0) {
            limpiarSeleccion();
            return;
        }

        // Calcular hora inicio y fin
        const horaInicio = horas[0];
        const [hiFin, miFin] = horas[horas.length - 1].split(':').map(Number);
        
        // Calcular correctamente la hora fin (suma 15 minutos a la última celda)
        let minutosFin = miFin + 15;
        let horaFin = hiFin;
        
        if (minutosFin >= 60) {
            minutosFin -= 60;
            horaFin += 1;
        }
        
        horaFin = String(horaFin).padStart(2, '0') + ':' + String(minutosFin).padStart(2, '0');

        // Validar que horaFin no exceda las 20:00
        const [hf, mf] = horaFin.split(':').map(Number);
        if (hf > 20 || (hf === 20 && mf > 0)) {
            horaFin = '20:00';
        }

        // Cambiar a celda-seleccionada en lugar de arrastrando
        celdasSeleccionadas.forEach(celda => {
            celda.classList.remove('celda-arrastrando');
            celda.classList.add('celda-seleccionada');
        });

        // Llenar el formulario
        const doctor = doctoresInfo.find(d => d.id == doctorArrastrando);
        if (!doctor) return;

        const fechaObj = new Date(fechaArrastrando + 'T00:00:00');
        const fechaFormato = fechaObj.toLocaleDateString('es-PE', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

        document.getElementById('bloqueo-doctor-id').value = doctorArrastrando;
        document.getElementById('bloqueo-fecha-hidden').value = fechaArrastrando;
        document.getElementById('bloqueo-hora-inicio').value = horaInicio;
        document.getElementById('bloqueo-hora-fin').value = horaFin;

        const infodetalles = document.getElementById('info-detalles');
        infodetalles.innerHTML = `
            <strong>Doctor:</strong> ${doctor.nombre} ${doctor.apellido}<br>
            <strong>Fecha:</strong> ${fechaFormato}<br>
            <strong>Desde:</strong> ${horaInicio} <strong>Hasta:</strong> ${horaFin}<br>
            <strong>Bloques seleccionados:</strong> ${horas.length} × 15 minutos
        `;

        infoBloqueo.classList.add('activo');
        infoBloqueo.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function limpiarSeleccion() {
        celdasSeleccionadas.forEach(celda => {
            celda.classList.remove('celda-arrastrando', 'celda-seleccionada');
        });
        celdasSeleccionadas.clear();
    }

    function generarCalendario(doctorId) {
        calendarGrid.innerHTML = '';
        limpiarSeleccion();

        if (!doctorId) {
            calendarGrid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 20px; color: #999;">Selecciona un doctor para ver su calendario</div>';
            return;
        }

        const lunes = obtenerLunesSemanaSemana(new Date(fechaActual));
        const dias = [];
        const diasSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sab', 'Dom'];
        
        for (let i = 0; i < 7; i++) {
            const fecha = new Date(lunes);
            fecha.setDate(fecha.getDate() + i);
            dias.push({
                fecha: fecha,
                fechaStr: obtenerFechaLocal(fecha),
                diaSemana: diasSemana[i]
            });
        }

        // Actualizar texto de semana mostrada
        const formatoLunes = lunes.toLocaleDateString('es-PE', { day: 'numeric', month: 'short' });
        const formatoDomingo = dias[6].fecha.toLocaleDateString('es-PE', { day: 'numeric', month: 'short', year: 'numeric' });
        semanaMostrada.textContent = `${formatoLunes} - ${formatoDomingo}`;

        // Actualizar mes mostrado
        const mes = lunes.toLocaleDateString('es-PE', { month: 'long', year: 'numeric' });
        mesMostrado.textContent = mes.charAt(0).toUpperCase() + mes.slice(1);

        // Header de horas
        const headerHora = document.createElement('div');
        headerHora.className = 'hora-cell';
        headerHora.textContent = 'HORA';
        calendarGrid.appendChild(headerHora);

        // Headers de días
        dias.forEach(dia => {
            const headerDia = document.createElement('div');
            headerDia.className = 'dia-header';
            headerDia.textContent = dia.diaSemana + '\n' + dia.fecha.getDate();
            calendarGrid.appendChild(headerDia);
        });

        // Generar filas de horas (8 AM a 8 PM)
        for (let h = 8; h < 20; h++) {
            for (let m = 0; m < 60; m += 15) {
                const hora = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
                
                // Celda de hora
                const horaCell = document.createElement('div');
                horaCell.className = 'hora-cell';
                horaCell.textContent = hora;
                calendarGrid.appendChild(horaCell);

                // Celdas de cada día
                dias.forEach(dia => {
                    const celda = document.createElement('div');
                    celda.className = 'celda-bloqueo';
                    celda.setAttribute('data-hora', hora);
                    celda.setAttribute('data-fecha', dia.fechaStr);
                    celda.setAttribute('data-doctor-id', doctorId);
                    
                    // Obtener día de semana numérico (1=Lunes, 7=Domingo)
                    const numDia = dia.fecha.getDay();
                    const diaSemanaNumerico = numDia === 0 ? 7 : numDia;
                    
                    // Convertir hora actual a minutos para comparación
                    const [hActual, mActual] = hora.split(':').map(Number);
                    const minActual = hActual * 60 + mActual;
                    
                    // Verificar si el doctor trabaja en ese día a esa hora
                    const horarioDelDia = horariosDoctores.find(h => {
                        const [hInicio, mInicio] = h.hora_inicio.split(':').map(Number);
                        const [hFin, mFin] = h.hora_fin.split(':').map(Number);
                        const minInicio = hInicio * 60 + mInicio;
                        const minFin = hFin * 60 + mFin;
                        
                        return h.doctor_id == doctorId &&
                               h.dia_semana == diaSemanaNumerico &&
                               minInicio <= minActual &&
                               minFin > minActual;
                    });

                    // Verificar si hay bloqueo
                    const tieneBloqueo = horariosBloqueos.some(b => {
                        const [hInicio, mInicio] = b.hora_inicio.split(':').map(Number);
                        const [hFin, mFin] = b.hora_fin.split(':').map(Number);
                        const minInicio = hInicio * 60 + mInicio;
                        const minFin = hFin * 60 + mFin;
                        
                        return b.doctor_id == doctorId &&
                               b.fecha === dia.fechaStr &&
                               minInicio <= minActual &&
                               minFin > minActual;
                    });

                    if (tieneBloqueo) {
                        celda.classList.add('celda-bloqueada');
                        celda.textContent = 'X';
                    } else if (horarioDelDia) {
                        // Si tiene horario disponible, marcar en verde
                        celda.style.backgroundColor = '#90ee90';
                        celda.style.borderColor = '#70d070';
                        celda.style.cursor = 'grab';
                        
                        // Eventos de drag and drop
                        celda.addEventListener('mousedown', iniciarArrastre);
                        celda.addEventListener('mouseover', duranteArrastre);
                        celda.addEventListener('mouseup', finalizarArrastre);
                    } else {
                        // Si no tiene horario, deshabilitar
                        celda.style.cursor = 'not-allowed';
                        celda.style.opacity = '0.6';
                    }

                    calendarGrid.appendChild(celda);
                });
            }
        }
    }

    doctorSelector.addEventListener('change', function() {
        generarCalendario(this.value);
        infoBloqueo.classList.remove('activo');
    });

    btnAnterior.addEventListener('click', function() {
        fechaActual.setDate(fechaActual.getDate() - 7);
        generarCalendario(doctorSelector.value);
    });

    btnHoy.addEventListener('click', function() {
        fechaActual = new Date();
        generarCalendario(doctorSelector.value);
    });

    btnSiguiente.addEventListener('click', function() {
        fechaActual.setDate(fechaActual.getDate() + 7);
        generarCalendario(doctorSelector.value);
    });

    btnCancelar.addEventListener('click', function() {
        limpiarSeleccion();
        infoBloqueo.classList.remove('activo');
    });

    // Prevenir selección de texto durante el arrastre
    document.addEventListener('selectstart', function(event) {
        if (arrastrandoActivo) {
            event.preventDefault();
        }
    });

    // Detener arrastre si sale del navegador
    document.addEventListener('mouseleave', function() {
        if (arrastrandoActivo) {
            arrastrandoActivo = false;
        }
    });

    // Validar formulario antes de enviar
    document.getElementById('formBloqueHorario').addEventListener('submit', function(event) {
        const doctorId = document.getElementById('bloqueo-doctor-id').value;
        const fecha = document.getElementById('bloqueo-fecha-hidden').value;
        const horaInicio = document.getElementById('bloqueo-hora-inicio').value;
        const horaFin = document.getElementById('bloqueo-hora-fin').value;

        // Validar que todos los campos requeridos estén llenos
        if (!doctorId || !fecha || !horaInicio || !horaFin) {
            event.preventDefault();
            alert('Por favor, selecciona un rango de horarios antes de crear el bloqueo.');
            return false;
        }

        // Validar que la fecha no sea anterior a hoy
        const fechaSeleccionada = new Date(fecha + 'T00:00:00');
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);

        if (fechaSeleccionada < hoy) {
            event.preventDefault();
            alert('No puedes bloquear horarios con fecha anterior a hoy.');
            return false;
        }

        // El formulario se enviará normalmente
        return true;
    });
});
</script>
