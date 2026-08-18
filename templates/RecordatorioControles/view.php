<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RecordatorioControl $recordatorioControl
 */
?>

<div class="recordatorioControles view content">
    <div class="container-fluid mt-4 mb-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <h3 class="text-info mb-4">Detalle de Control</h3>

                <div class="card mb-4">
                    <div class="card-body row g-3">
                        <div class="col-md-12"><strong>Paciente:</strong> <?= $recordatorioControl->recordatorio && $recordatorioControl->recordatorio->paciente ? h($recordatorioControl->recordatorio->paciente->nombre . ' ' . $recordatorioControl->recordatorio->paciente->apellido) : '-' ?></div>
                        <div class="col-md-12"><strong>Recordatorio:</strong> <?= $recordatorioControl->recordatorio ? h($recordatorioControl->recordatorio->titulo) : '-' ?></div>
                        <div class="col-md-6"><strong>Fecha control:</strong> <?= h($recordatorioControl->fecha_control) ?></div>
                        <div class="col-md-6"><strong>Próximo control:</strong> <?= h($recordatorioControl->proximo_control) ?: '-' ?></div>
                        <div class="col-12"><strong>Detalles:</strong><div class="mt-1"><?= !empty($recordatorioControl->detalles) ? nl2br(h($recordatorioControl->detalles)) : '<em class="text-muted">Sin detalles</em>' ?></div></div>
                        <div class="col-12"><strong>Productos utilizados:</strong><div class="mt-1"><?= !empty($recordatorioControl->productos_utilizados) ? nl2br(h($recordatorioControl->productos_utilizados)) : '<em class="text-muted">Sin información</em>' ?></div></div>
                        <div class="col-12"><strong>Observaciones:</strong><div class="mt-1"><?= !empty($recordatorioControl->observaciones) ? nl2br(h($recordatorioControl->observaciones)) : '<em class="text-muted">Sin observaciones</em>' ?></div></div>
                        <div class="col-md-6"><strong>Recordatorio enviado:</strong> <?= $recordatorioControl->recordatorio_enviado ? 'Sí' : 'No' ?></div>
                        <div class="col-md-6"><strong>Estado:</strong> <?= $recordatorioControl->estado === 'C' ? 'Citado' : ($recordatorioControl->estado === 'P' ? 'Pendiente' : '-') ?></div>
                        <div class="col-md-6"><strong>Estado del control:</strong> <?= $recordatorioControl->estado_control === 'A' ? 'Activo' : 'Inactivo' ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>