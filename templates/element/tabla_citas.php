<?php
// Element: src/Template/Element/tabla_citas.php
?>
<style>
    /* Asegura que la tabla no se corte */
    .table-responsive {
        overflow-x: auto;
        position: relative;
    }
    
    /* Hace sticky la ��ltima celda (Acciones) */
    .table td:last-child,
    .table th:last-child {
        position: sticky;
        right: 0;
        background-color: #d6d6d6;
        color: black !important;
        z-index: 2;
        min-width: 150px;
        text-align: center;
        box-shadow: -2px 0 5px rgba(0,0,0,0.1);
    }
    #tabla-citas-container td.motivo-col {
        max-width: 250px;
        min-width: 250px;
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

</style>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <?php if ($usuario->rol != 3): ?>
                    <th>Doctor</th>
                <?php endif; ?>
                <th>Hora</th>
                <th>Paciente</th>
                <th>Motivo</th>
                <th>Tratamientos</th>
                <th>Duracion</th>
                <th>Atención</th>
                <th>Estado</th>
                <th>Hora de Llegada</th>
                <th>Inicio de Consulta</th>
                <th>Fin de Consulta</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($citas as $cita): ?>
                <?php if ($cita->estado !== 'finalizado' && $cita->estado !== 'cancelado'): ?>
                    <tr>
                        <?php if ($usuario->rol != 3): ?>
                            <td>
                                <?= !empty($cita->doctor_id) && !empty($doctores[$cita->doctor_id])
                                    ? h($doctores[$cita->doctor_id])
                                    : 'No asignado'
                                ?>
                            </td>
                        <?php endif; ?>
                        <td><?= h($cita->fecha_hora->format('H:i')) ?></td>
                        <td>
                            <?php
                                $paciente = null;
                                if (!empty($cita->pacientes)) {
                                    $paciente = $cita->pacientes;
                                } elseif (!empty($cita->paciente)) {
                                    $paciente = $cita->paciente;
                                }
                            ?>
                            <?= !empty($paciente)
                                ? $this->Html->link(
                                    h($paciente->nombre . ' ' . $paciente->apellido),
                                    ['controller' => 'Pacientes', 'action' => 'view', $paciente->id],
                                    ['class' => 'text-decoration-none text-info', 'target' => '_blank']
                                )
                                : 'No asignado'
                            ?>
                        </td>

                        <td class="motivo-col">
                            <?= !empty($cita->motivo) ? h($cita->motivo) : 'No registrado' ?>
                        </td>

                        <?php if (!empty($cita->citas_tratamientos)): ?>
                            <td>
                                <ul style="padding-left: 20px; margin: 0;">
                                    <?php foreach ($cita->citas_tratamientos as $ct): ?>
                                        <li><?= h($ct->tratamiento->nombre ?? 'Tratamiento no encontrado') ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                        <?php else: ?>
                            <td>No registrado</td>
                        <?php endif; ?>

                        <td><?= $cita->duracion_minutos ? h($cita->duracion_minutos) . ' min' : 'No registrado' ?></td>
                        <td>
                            <?php if ($cita->tipo === 'C'): ?>
                                <span style="background-color: rgb(100, 170, 245); padding: 2px 6px; border-radius: 4px;">Consulta</span>
                            <?php elseif ($cita->tipo === 'P'): ?>
                                <span style="background-color: #a06815ff; padding: 2px 6px; border-radius: 4px;">Procedimiento</span>
                            <?php else: ?>
                                <?= h($cita->tipo) ?>
                            <?php endif; ?>
                        </td>



                        <td>
                            <span class="badge text-white p-2 rounded"
                                style="
                                    <?php 
                                        switch ($cita->estado) {
                                            case 'pendiente':
                                                echo 'background-color: #b38f00;';
                                                break;
                                            case 'confirmado':
                                                echo 'background-color: #006400;';
                                                break;
                                            case 'en_consultorio':
                                                echo 'background-color: #4b0082;';
                                                break;
                                            case 'en_recepcion':
                                                echo 'background-color: #e67e22;';
                                                break;
                                            case 'en_cabina':
                                                echo 'background-color: #007b73;';
                                                break;
                                            case 'programar':
                                                echo 'background-color: #ff6384;';
                                                break;
                                            case 'sos':
                                                echo 'background-color: #ff0000;';
                                                break;
                                            case 'finalizado':
                                                echo 'background-color: #808080;';
                                                break;
                                            case 'cancelado':
                                                echo 'background-color: #8b0000;';
                                                break;
                                            default:
                                                echo 'background-color: #6c757d;';
                                                break;
                                        }
                                    ?>
                                ">
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

                        <td><?= h($cita->hora_inicio_consulta ?: 'No registrado') ?></td>
                        <td><?= h($cita->hora_fin_consulta ?: 'No registrado') ?></td>
                        <td>
                            <?php if (!$cita->hora_inicio_consulta): ?>
                                <?= $this->Html->link(
                                    'Registrar Inicio', 
                                    ['action' => 'registrarInicio', $cita->id], 
                                    ['class' => 'btn btn-primary', 'onclick' => 'return confirmarInicio();']
                                ) ?>
                            <?php endif; ?>
                            
                            <?php if ($cita->hora_inicio_consulta && !$cita->hora_fin_consulta): ?>
                                <?= $this->Html->link(
                                    'Finalizar Consulta', 
                                    ['action' => 'finalizarConsulta', $cita->id], 
                                    ['class' => 'btn btn-danger', 'onclick' => 'return confirmarFinalizar();']
                                ) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>