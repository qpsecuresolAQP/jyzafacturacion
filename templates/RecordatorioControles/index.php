<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\RecordatorioControl> $recordatorioControles
 */
?>

<div class="recordatorioControles index content">
    <div class="container-fluid mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Gestión de Controles</h3>
                <div>
                    <?= $this->Html->link('Reportes', ['action' => 'reportes'], ['class' => 'btn btn-light btn-sm']) ?>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Paciente</th>
                            <th>Recordatorio</th>
                            <th>Fecha control</th>
                            <th>Próximo control</th>
                            <th>Enviado</th>
                            <th>Estado</th>
                            <th>Estado del control</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recordatorioControles as $control): ?>
                            <tr>
                                <td><?= $control->recordatorio && $control->recordatorio->paciente ? h($control->recordatorio->paciente->nombre . ' ' . $control->recordatorio->paciente->apellido) : '-' ?></td>
                                <td><?= $control->recordatorio ? h($control->recordatorio->titulo) : '-' ?></td>
                                <td><?= h($control->fecha_control) ?></td>
                                <td><?= h($control->proximo_control) ?: '-' ?></td>
                                <td><?= $control->recordatorio_enviado ? 'Sí' : 'No' ?></td>
                                <td><?= $control->estado === 'C' ? 'Citado' : ($control->estado === 'P' ? 'Pendiente' : '-') ?></td>
                                <td><?= $control->estado_control === 'A' ? 'Activo' : 'Inactivo' ?></td>
                                <td class="text-end">
                                    <?= $this->Html->link('Ver', ['action' => 'view', $control->id], ['class' => 'btn btn-sm btn-outline-info']) ?>
                                    <?= $this->Html->link('Editar', ['action' => 'edit', $control->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                                    <?= $this->Form->postLink('Eliminar', ['action' => 'delete', $control->id], ['confirm' => '¿Desactivar este control?', 'class' => 'btn btn-sm btn-outline-danger']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>