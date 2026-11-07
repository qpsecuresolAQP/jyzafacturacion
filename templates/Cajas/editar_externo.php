<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Caja $caja
 * @var \App\Model\Entity\CajaIngreso|\App\Model\Entity\CajaEgreso $registro
 * @var bool $esIngreso
 */
$metodos = [
    'EFECTIVO' => '💵 Efectivo',
    'YAPE' => '📱 Yape',
    'TARJETA' => '💳 Tarjeta',
    'TRANSFERENCIA' => '🏦 Transferencia',
    'PLIN' => '🧾 Plin',
    'OTROS' => '📦 Otros',
];
$metodoActual = strtoupper((string)($registro->metodo_pago ?? 'EFECTIVO'));
?>
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <?= $this->Form->create($registro, ['class' => 'row g-3']) ?>

            <div class="col-12 mb-4">
                <h3 class="<?= $esIngreso ? 'text-info' : 'text-danger' ?>">
                    <i class="fas fa-edit"></i> Editar <?= $esIngreso ? 'Ingreso' : 'Egreso' ?> Externo
                </h3>
                <p class="text-muted small mb-0">
                    Caja: <strong><?= h($caja->nombre) ?></strong> ·
                    Registrado: <?= h($registro->created?->format('Y-m-d H:i')) ?>
                </p>
                <?php if ($caja->estado === 'CERRADA'): ?>
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Esta caja ya está cerrada. El cambio modificará sus totales y el efectivo esperado del cierre.
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label fw-semibold">Monto</label>
                <div class="input-group">
                    <span class="input-group-text">S/</span>
                    <input type="number" name="monto" step="0.01" min="0.01"
                           class="form-control form-control-lg"
                           value="<?= number_format((float)$registro->monto, 2, '.', '') ?>"
                           required>
                </div>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label fw-semibold">Método de Pago</label>
                <select name="metodo_pago" class="form-select form-select-lg" required>
                    <?php foreach ($metodos as $valor => $texto): ?>
                        <option value="<?= $valor ?>" <?= $valor === $metodoActual ? 'selected' : '' ?>><?= $texto ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label fw-semibold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3" required><?= h($registro->descripcion) ?></textarea>
            </div>

            <div class="col-12 text-center">
                <button type="submit" class="btn btn-<?= $esIngreso ? 'info' : 'danger' ?> btn-lg px-4">
                    <i class="fas fa-save me-2"></i>Guardar cambios
                </button>
                <?= $this->Html->link('Cancelar', ['action' => 'view', $caja->id], ['class' => 'btn btn-secondary btn-lg ms-2']) ?>
            </div>

            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
