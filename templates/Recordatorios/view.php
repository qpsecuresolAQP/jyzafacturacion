<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Recordatorio $recordatorio
 */
?>

<div class="recordatorios view content">
    <div class="container-fluid mt-4 mb-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <h3 class="text-info mb-4">Detalle de Recordatorio</h3>

                <div class="card mb-4">
                    <div class="card-body row g-3">
                        <div class="col-md-12"><strong>Paciente:</strong> <?= $recordatorio->paciente ? h($recordatorio->paciente->nombre . ' ' . $recordatorio->paciente->apellido) : '-' ?></div>
                        <div class="col-md-12"><strong>Título:</strong> <?= h($recordatorio->titulo) ?></div>
                        <div class="col-md-6"><strong>Fecha inicio:</strong> <?= h($recordatorio->fecha_inicio) ?></div>
                        <div class="col-md-6"><strong>Duración estimada:</strong> <?= h($recordatorio->duracion_estimada) ?: '-' ?></div>
                        <div class="col-12"><strong>Observación:</strong><div class="mt-1"> <?= !empty($recordatorio->observacion) ? nl2br(h($recordatorio->observacion)) : '<em class="text-muted">Sin observación</em>' ?></div></div>
                        <div class="col-md-12"><strong>Estado:</strong> <?= $recordatorio->estado_control === 'A' ? 'Activo' : 'Inactivo' ?></div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="mb-0">Controles</h4>
                    <?= $this->Html->link('+ Nuevo Control', ['controller' => 'RecordatorioControles', 'action' => 'add', '?' => ['recordatorio_id' => $recordatorio->id]], ['class' => 'btn btn-info btn-sm']) ?>
                </div>

                <?php if (!empty($recordatorio->recordatorio_controles)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Fecha control</th>
                                    <th>Próximo control</th>
                                    <th>Detalles</th>
                                    <th>Productos</th>
                                    <th>Observaciones</th>
                                    <th>Enviado</th>
                                    <th>Estado</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recordatorio->recordatorio_controles as $control): ?>
                                    <tr>
                                        <td><?= h($control->fecha_control) ?></td>
                                        <td><?= h($control->proximo_control) ?: '-' ?></td>
                                        <td><?= h(mb_strimwidth((string)$control->detalles, 0, 40, '...')) ?: '-' ?></td>
                                        <td><?= h(mb_strimwidth((string)$control->productos_utilizados, 0, 40, '...')) ?: '-' ?></td>
                                        <td><?= h(mb_strimwidth((string)$control->observaciones, 0, 40, '...')) ?: '-' ?></td>
                                        <td><?= $control->recordatorio_enviado ? 'Sí' : 'No' ?></td>
                                        <td><?= $control->estado_control === 'A' ? 'Activo' : 'Inactivo' ?></td>
                                        <td class="text-end">
                                            <?= $this->Html->link('Ver', ['controller' => 'RecordatorioControles', 'action' => 'view', $control->id], ['class' => 'btn btn-sm btn-outline-info']) ?>
                                            <?= $this->Html->link('Editar', ['controller' => 'RecordatorioControles', 'action' => 'edit', $control->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                                            <?= $this->Form->postLink('Eliminar', ['controller' => 'RecordatorioControles', 'action' => 'delete', $control->id], ['confirm' => '¿Desactivar este control?', 'class' => 'btn btn-sm btn-outline-danger']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light border">No hay controles registrados aún.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>