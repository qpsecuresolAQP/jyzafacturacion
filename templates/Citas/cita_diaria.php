<div class="container-fluid">
    <h2>Citas Diarias</h2>
    
    <!-- Meta tag para CSRF token -->
    <meta name="csrfToken" content="<?= $this->request->getAttribute('csrfToken') ?>">
    
    <?php
    // Detectar si hay citas SOS activas (no finalizadas ni canceladas)
    $sosCitas = [];
    foreach ($citas as $citaCheck) {
        if ($citaCheck->estado === 'sos' && $citaCheck->estado !== 'finalizado' && $citaCheck->estado !== 'cancelado') {
            $sosCitas[] = $citaCheck;
        }
    }
    ?>

    <?php if (!empty($sosCitas)): ?>
        <div style="background-color: red; color: white; font-weight: bold; text-align: center; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
            ¡SOS! Atención urgente requerida.
        </div>

        <!-- Listado solo de citas SOS -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm" style="background-color: #ffe6e6;">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Paciente</th>
                        <th>Tratamientos</th>
                        <th>Duración</th>
                        <th>Tipo Atención</th>
                        <th>Estado</th>
                        <th>Hora de Llegada</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sosCitas as $cita): ?>
                        <tr>
                            <td><?= h($cita->fecha_hora->format('H:i')) ?></td>
                            <td>
                                <?= !empty($cita->paciente)
                                    ? $this->Html->link(
                                        h($cita->paciente->nombre . ' ' . $cita->paciente->apellido),
                                        ['controller' => 'Pacientes', 'action' => 'view', $cita->paciente->id],
                                        ['class' => 'text-decoration-none', 'target' => '_blank']
                                    )
                                    : 'No asignado'
                                ?>
                            </td>
                            <td>
                                <?php if (!empty($cita->citas_tratamientos)): ?>
                                    <ul style="padding-left: 20px; margin: 0;">
                                        <?php foreach ($cita->citas_tratamientos as $ct): ?>
                                            <li><?= h($ct->tratamiento->nombre ?? 'Tratamiento no encontrado') ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    No registrado
                                <?php endif; ?>
                            </td>
                            <td><?= $cita->duracion_minutos ? h($cita->duracion_minutos) . ' min' : 'No registrado' ?></td>
                            <td><?= $cita->tipo === 'C' ? 'Consulta' : ($cita->tipo === 'P' ? 'Procedimiento' : h($cita->tipo)) ?></td>
                            <td>
                                <span class="badge text-white p-2 rounded" style="background-color: red;">
                                    <?= h($cita->estado) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php
                                    if (!empty($cita->hora_llegada)) {
                                        $horaCita = $cita->fecha_hora instanceof \Cake\I18n\FrozenTime ? $cita->fecha_hora : new \Cake\I18n\FrozenTime($cita->fecha_hora);
                                        $horaLlegada = $cita->hora_llegada instanceof \Cake\I18n\FrozenTime ? $cita->hora_llegada : new \Cake\I18n\FrozenTime($cita->hora_llegada);
                                        $minutesDiff = $horaLlegada->diffInMinutes($horaCita, false);
                                        echo ($minutesDiff >= -5) ? 'bg-success' : 'bg-danger';
                                    } else {
                                        echo 'bg-secondary';
                                    }
                                ?> text-white p-2 rounded">
                                    <?= h($cita->hora_llegada ? $cita->hora_llegada->format('h:i a') : 'No registrado') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    
    <!-- Selector de odontólogos -->
    <div class="form-group">
        <label for="doctorSelect">Especialistas:</label>
        <?= $this->Form->create(null, ['type' => 'get']) ?>
        <?= $this->Form->select('doctor_id', $doctores, [
            'id' => 'doctorSelect',
            'class' => 'form-control',
            'empty' => '-- Seleccione un especialista --',
            'value' => $doctorId,
            'onchange' => 'this.form.submit()',
            'disabled' => ($usuario->rol_id == 2) // Se deshabilita si el usuario es doctor (rol_id = 2)
        ]) ?>
        <?= $this->Form->end() ?>
    </div>

    <!-- Indicador de última actualización -->
    <div class="mb-2">
        <small class="text-muted">
            Última actualización: <span id="ultima-actualizacion"><?= date('H:i:s') ?></span>
           
        </small>
    </div>

    <!-- Tabla de Citas (contenedor que se actualizará) -->
    <div id="tabla-citas-container">
        <?= $this->element('tabla_citas', ['citas' => $citas, 'usuario' => $usuario]) ?>
    </div>
</div>

<script>
// Obtener el rol del usuario actual desde PHP
const userRole = <?= json_encode($usuarioRolId); ?>;

// Si el usuario es doctor (rol_id = 2), bloquear el select de doctores
if (userRole === 2) {
    document.getElementById('doctorSelect').disabled = true;
}

function confirmarInicio() {
    return confirm("¿Estás seguro de registrar el inicio de la consulta?");
}

function confirmarFinalizar() {
    return confirm("¿Estás seguro de finalizar la consulta?");
}

// Función para actualizar la tabla de citas
function actualizarTablaCitas() {
    const indicadorCarga = document.getElementById('indicador-carga');
    const ultimaActualizacion = document.getElementById('ultima-actualizacion');
    
    
    // Obtener el doctor_id actual del selector
    const doctorSelect = document.getElementById('doctorSelect');
    const doctorId = doctorSelect ? doctorSelect.value : '';
    
    // Obtener el token CSRF
    const csrfToken = document.querySelector('meta[name="csrfToken"]')?.getAttribute('content') || 
                     document.querySelector('input[name="_csrfToken"]')?.value;
    
    // Realizar petición AJAX con GET (evita problemas de CSRF)
    const url = '<?= $this->Url->build(['controller' => 'Citas', 'action' => 'actualizarTablaCitas']) ?>' + 
                '?doctor_id=' + encodeURIComponent(doctorId);
    
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Actualizar solo el contenido de la tabla
        document.getElementById('tabla-citas-container').innerHTML = html;
        
        // Actualizar hora de última actualización
        const ahora = new Date();
        ultimaActualizacion.textContent = ahora.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        
        // Ocultar indicador de carga
        
    })
    .catch(error => {
        console.error('Error al actualizar la tabla:', error);
        
    });
}

let intervalId = setInterval(actualizarTablaCitas, 15000); // Actualiza cada minuto
let ocultoDesde = null;

document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        ocultoDesde = Date.now();

        // Detener después de 1 minutos de estar oculto
        setTimeout(() => {
            if (document.hidden && Date.now() - ocultoDesde >= 15000) {
                clearInterval(intervalId);
                intervalId = null; // Importante para poder reactivarlo
            }
        }, 15000);
    } else {
        // Al volver, actualizar inmediatamente y reanudar intervalo
        if (!intervalId) {
            actualizarTablaCitas(); // ⬅️ Actualiza en cuanto vuelve
            intervalId = setInterval(actualizarTablaCitas, 15000);
        }
        ocultoDesde = null;
    }
});

// Limpiar intervalo al salir de la página
window.addEventListener('beforeunload', function() {
    clearInterval(intervalId);
});
</script>