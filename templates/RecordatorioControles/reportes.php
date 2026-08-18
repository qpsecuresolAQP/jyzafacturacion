<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\RecordatorioControl> $recordatorioControles
 */
?>

<div class="container-fluid py-4">
    <?php
        $queryParams = array_filter([
            'fecha_inicio' => $fechaInicio ?? null,
            'fecha_fin' => $fechaFin ?? null,
            'estado' => $estado ?? null,
        ], static fn($value) => $value !== null && $value !== '');
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="mb-0">
            <i class="fas fa-chart-bar"></i> Reporte de Recordatorios
        </h3>

        <div class="d-flex gap-2">
            <?= $this->Html->link('Exportar PDF', ['action' => 'exportarPdf', '?' => $queryParams], ['class' => 'btn btn-danger', 'target' => '_blank']) ?>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Fecha inicio</label>
                    <input type="date" name="fecha_inicio" value="<?= h($fechaInicio ?? '') ?>" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha fin</label>
                    <input type="date" name="fecha_fin" value="<?= h($fechaFin ?? '') ?>" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="" <?= empty($estado) ? 'selected' : '' ?>>Todos</option>
                        <option value="P" <?= ($estado ?? '') === 'P' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="C" <?= ($estado ?? '') === 'C' ? 'selected' : '' ?>>Citado</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-info w-100">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h6>Pendientes</h6>
                    <h4><?= (int)($resumen['P']['total'] ?? 0) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h6>Citados</h6>
                    <h4><?= (int)($resumen['C']['total'] ?? 0) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h6>Total registros</h6>
                    <h4><?= count($recordatorioControles) ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive card">
        <table class="table table-striped mb-0 align-middle">
            <thead class="bg-info text-white">
                <tr>
                    <th>Paciente</th>
                    <th>Recordatorio</th>
                    <th>Fecha control</th>
                    <th>Próximo control</th>
                    <th>Estado</th>
                    <th>Enviado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recordatorioControles as $control): ?>
                    <?php $paciente = $control->recordatorio->paciente ?? null; ?>
                    <tr>
                        <td><?= $paciente ? h(trim($paciente->nombre . ' ' . $paciente->apellido)) : '-' ?></td>
                        <td><?= $control->recordatorio ? h($control->recordatorio->titulo) : '-' ?></td>
                        <td><?= h($control->fecha_control) ?></td>
                        <td><?= h($control->proximo_control) ?: '-' ?></td>
                        <td>
                            <span class="badge bg-<?= $control->estado === 'C' ? 'success' : 'warning text-dark' ?>">
                                <?= $control->estado === 'C' ? 'Citado' : 'Pendiente' ?>
                            </span>
                        </td>
                        <td><?= $control->recordatorio_enviado ? 'Sí' : 'No' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>