<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Recordatorio> $recordatorios
 */
?>

<div class="recordatorios index content">
    <div class="container-fluid mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Gestión de Recordatorios</h3>
                <a href="<?= $this->Url->build(['action' => 'add']) ?>" class="btn btn-light btn-sm">+ Nuevo Recordatorio</a>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Paciente</th>
                            <th>Fecha inicio</th>
                            <th>Duración estimada</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recordatorios as $recordatorio): ?>
                            <tr>
                                <td><?= h($recordatorio->titulo) ?></td>
                                <td><?= $recordatorio->paciente ? h($recordatorio->paciente->nombre . ' ' . $recordatorio->paciente->apellido) : '-' ?></td>
                                <td><?= h($recordatorio->fecha_inicio) ?></td>
                                <td><?= h($recordatorio->duracion_estimada) ?: '-' ?></td>
                                <td>
                                    <span class="badge bg-<?= $recordatorio->estado_control === 'A' ? 'success' : 'secondary' ?>">
                                        <?= $recordatorio->estado_control === 'A' ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <?= $this->Html->link('Ver', ['action' => 'view', $recordatorio->id], ['class' => 'btn btn-sm btn-outline-info']) ?>
                                    <?= $this->Html->link('Editar', ['action' => 'edit', $recordatorio->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                                    <?= $this->Form->postLink('Eliminar', ['action' => 'delete', $recordatorio->id], ['confirm' => '¿Desactivar este recordatorio?', 'class' => 'btn btn-sm btn-outline-danger']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>