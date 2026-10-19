<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ProductoLote $lote
 * @var \App\Model\Entity\Producto $producto
 */
$numero = (string) ($this->request->getData('lote') ?? $lote->lote ?? '');
$fecha = (string) ($this->request->getData('fecha_vencimiento') ?? ($lote->fecha_vencimiento ? $lote->fecha_vencimiento->format('Y-m-d') : ''));
?>
<?php $this->assign('title', 'Editar lote'); ?>
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h3 class="text-info"><i class="fas fa-edit"></i> Editar lote</h3>
            <p class="text-muted">
                Producto: <strong><?= h($producto->nombre) ?></strong>
                &middot; Stock del lote: <strong><?= $this->Number->format($lote->stock) ?> <?= h($producto->unidad_nombre ?? 'unid.') ?></strong>
            </p>

            <div class="alert alert-info small">
                Use esto para completar los datos de un lote (por ejemplo el stock anterior al control por lotes) o corregir un
                error de digitación. La cantidad no cambia: para eso use <strong>Ajustar</strong> en el historial.
                Cada cambio queda registrado con su usuario.
            </div>

            <?= $this->Form->create(null, ['class' => 'row g-3']) ?>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="lote">Número de lote <?= $producto->requiere_vencimiento ? '<span class="text-danger">*</span>' : '' ?></label>
                    <input type="text" name="lote" id="lote" class="form-control" maxlength="50" value="<?= h($numero) ?>" <?= $producto->requiere_vencimiento ? 'required' : '' ?>>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="fecha_vencimiento">Fecha de vencimiento <?= $producto->requiere_vencimiento ? '<span class="text-danger">*</span>' : '' ?></label>
                    <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" value="<?= h($fecha) ?>" <?= $producto->requiere_vencimiento ? 'required' : '' ?>>
                    <small class="text-muted">Si pone una fecha pasada, el lote pasa a "vencido" y deja de venderse.</small>
                </div>
                <div class="col-12 text-center">
                    <?= $this->Form->button(__('Guardar cambios'), ['class' => 'btn btn-info']) ?>
                    <?= $this->Html->link(__('Cancelar'), ['action' => 'historial', $producto->id], ['class' => 'btn btn-secondary ms-2']) ?>
                </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
