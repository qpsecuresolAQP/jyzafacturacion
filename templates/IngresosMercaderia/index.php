<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\IngresosMercaderium> $ingresos
 * @var string $searchTerm
 * @var string $estadoFiltro
 */
?>

<?php $this->assign('title', 'Ingreso de Mercadería'); ?>

<div class="ingresos-mercaderia index content">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="btn-group" role="group">
            <?= $this->Html->link('Registrados', ['action' => 'index', '?' => ['estado' => 'REGISTRADO', 'search' => $searchTerm]], [
                'class' => 'btn btn-sm ' . ($estadoFiltro === 'REGISTRADO' ? 'btn-info' : 'btn-outline-info'),
            ]) ?>
            <?= $this->Html->link('Anulados', ['action' => 'index', '?' => ['estado' => 'ANULADO', 'search' => $searchTerm]], [
                'class' => 'btn btn-sm ' . ($estadoFiltro === 'ANULADO' ? 'btn-info' : 'btn-outline-info'),
            ]) ?>
            <?= $this->Html->link('Todos', ['action' => 'index', '?' => ['estado' => 'TODOS', 'search' => $searchTerm]], [
                'class' => 'btn btn-sm ' . ($estadoFiltro === 'TODOS' ? 'btn-info' : 'btn-outline-info'),
            ]) ?>
        </div>

        <?= $this->Form->create(null, ['type' => 'get', 'class' => 'd-flex gap-2']) ?>
            <?= $this->Form->hidden('estado', ['value' => $estadoFiltro]) ?>
            <?= $this->Form->control('search', [
                'label' => false,
                'value' => $searchTerm,
                'placeholder' => 'Proveedor, RUC, serie o número...',
                'class' => 'form-control form-control-sm',
                'templates' => ['inputContainer' => '{{content}}'],
            ]) ?>
            <button type="submit" class="btn btn-sm btn-outline-info"><i class="fas fa-search"></i></button>
            <?php if ($searchTerm !== ''): ?>
                <?= $this->Html->link('<i class="fas fa-times"></i>', ['action' => 'index', '?' => ['estado' => $estadoFiltro]], [
                    'escape' => false,
                    'class' => 'btn btn-sm btn-outline-secondary',
                    'title' => 'Limpiar búsqueda',
                ]) ?>
            <?php endif; ?>
        <?= $this->Form->end() ?>

        <div class="d-flex gap-2">
            <?php if ($this->Permisos->tiene('IngresosMercaderia', 'vencimientos')): ?>
                <?= $this->Html->link('<i class="fas fa-calendar-times"></i> Vencimientos', ['action' => 'vencimientos'], ['escape' => false, 'class' => 'btn btn-outline-warning']) ?>
            <?php endif; ?>
            <?php if ($this->Permisos->tiene('IngresosMercaderia', 'add')): ?>
                <?= $this->Html->link('<i class="fas fa-plus"></i> Registrar Ingreso', ['action' => 'add'], ['escape' => false, 'class' => 'btn btn-success']) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="contenedor principal">
        <div class="table-responsive">
            <table class="table table-striped mt-3" style="table-layout: fixed; width: 100%; min-width: 900px;">
                <colgroup>
                    <col style="width: 7%;">
                    <col style="width: 12%;">
                    <col style="width: 28%;">
                    <col style="width: 15%;">
                    <col style="width: 12%;">
                    <col style="width: 10%;">
                    <col style="width: 8%;">
                    <col style="width: 8%;">
                </colgroup>
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'N°') ?></th>
                        <th>Documento</th>
                        <th><?= $this->Paginator->sort('Proveedores.nombre', 'Proveedor') ?></th>
                        <th><?= $this->Paginator->sort('fecha_ingreso', 'Fecha Ingreso') ?></th>
                        <th class="text-end"><?= $this->Paginator->sort('total', 'Total') ?></th>
                        <th>Estado</th>
                        <th>Almacén</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($ingresos->count() === 0): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">No hay ingresos registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach ($ingresos as $ing): ?>
                            <tr<?= $ing->estado === 'ANULADO' ? ' class="table-secondary text-muted"' : '' ?>>
                                <td><?= $this->Number->format($ing->id) ?></td>
                                <td class="text-truncate" style="max-width: 0;">
                                    <?= h($ing->tipo_doc) ?><br>
                                    <small><?= h(trim(($ing->serie ? $ing->serie . '-' : '') . ($ing->numero ?: '—'))) ?></small>
                                </td>
                                <td class="text-truncate" style="max-width: 0;" title="<?= h($ing->proveedore->nombre ?? '') ?>">
                                    <?= h($ing->proveedore->nombre ?? '—') ?>
                                    <?php if (!empty($ing->proveedore->ruc)): ?>
                                        <br><small><?= h($ing->proveedore->ruc) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= $ing->fecha_ingreso ? $ing->fecha_ingreso->format('d/m/Y H:i') : '—' ?></td>
                                <td class="text-end">
                                    <?= $ing->moneda === 'DOLARES' ? '$' : 'S/' ?> <?= number_format((float)$ing->total, 2) ?>
                                </td>
                                <td>
                                    <span class="badge <?= $ing->estado === 'REGISTRADO' ? 'badge-success' : 'badge-danger' ?>">
                                        <?= $ing->estado === 'REGISTRADO' ? 'Registrado' : 'Anulado' ?>
                                    </span>
                                </td>
                                <td class="text-truncate" style="max-width: 0;"><?= h($ing->almacen) ?></td>
                                <td class="text-center text-nowrap">
                                    <?php if ($this->Permisos->tiene('IngresosMercaderia', 'view')): ?>
                                        <?= $this->Html->link(
                                            '<i class="fas fa-eye"></i>',
                                            ['action' => 'view', $ing->id],
                                            ['escape' => false, 'title' => 'Ver detalle', 'class' => 'btn btn-info btn-sm']
                                        ) ?>
                                    <?php endif; ?>
                                    <?php if ($ing->estado === 'REGISTRADO' && $this->Permisos->tiene('IngresosMercaderia', 'anular')): ?>
                                        <?= $this->Form->postLink(
                                            '<i class="fas fa-ban"></i>',
                                            ['action' => 'anular', $ing->id],
                                            ['escape' => false, 'title' => 'Anular', 'class' => 'btn btn-danger btn-sm', 'confirm' => '¿Anular este ingreso? Se revertirá el stock sumado.']
                                        ) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('Primero')) ?>
            <?= $this->Paginator->prev('< ' . __('Anterior')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('Siguiente') . ' >') ?>
            <?= $this->Paginator->last(__('Último') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registro(s) de un total de {{count}}')) ?></p>
    </div>
</div>
