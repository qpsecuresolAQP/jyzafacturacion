<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Examene> $examenes
 * @var string $searchTerm
 * @var string $estadoFiltro
 */
?>
<?php $this->assign('title', 'Exámenes'); ?>

<div class="examenes index content">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="btn-group" role="group">
            <?= $this->Html->link('Activos', ['action' => 'index', '?' => ['estado' => 'activos', 'search' => $searchTerm]], [
                'class' => 'btn btn-sm ' . ($estadoFiltro === 'activos' ? 'btn-info' : 'btn-outline-info'),
            ]) ?>
            <?= $this->Html->link('Inactivos', ['action' => 'index', '?' => ['estado' => 'inactivos', 'search' => $searchTerm]], [
                'class' => 'btn btn-sm ' . ($estadoFiltro === 'inactivos' ? 'btn-info' : 'btn-outline-info'),
            ]) ?>
            <?= $this->Html->link('Todos', ['action' => 'index', '?' => ['estado' => 'todos', 'search' => $searchTerm]], [
                'class' => 'btn btn-sm ' . ($estadoFiltro === 'todos' ? 'btn-info' : 'btn-outline-info'),
            ]) ?>
        </div>

        <?= $this->Form->create(null, ['type' => 'get', 'class' => 'd-flex gap-2']) ?>
            <?= $this->Form->hidden('estado', ['value' => $estadoFiltro]) ?>
            <?= $this->Form->control('search', [
                'label' => false,
                'value' => $searchTerm,
                'placeholder' => 'Buscar examen...',
                'class' => 'form-control',
            ]) ?>
            <?= $this->Form->button('Buscar', ['class' => 'btn btn-info']) ?>
        <?= $this->Form->end() ?>

        <?= $this->Html->link(__('Añadir Examen'), ['action' => 'add'], ['class' => 'btn btn-info openModal']) ?>
    </div>

    <div class="contenedor principal">
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'N°') ?></th>
                        <th>Categoría</th>
                        <th><?= $this->Paginator->sort('nombre', 'Nombre') ?></th>
                        <th><?= $this->Paginator->sort('muestra', 'Muestra') ?></th>
                        <th><?= $this->Paginator->sort('precio', 'Precio Paciente (S/)') ?></th>
                        <th><?= $this->Paginator->sort('precio_convenio', 'Convenio Lab. (S/)') ?></th>
                        <th>Laboratorio</th>
                        <th><?= $this->Paginator->sort('comision_medico', 'Comisión Médico (S/)') ?></th>
                        <th><?= $this->Paginator->sort('estado', 'Estado') ?></th>
                        <th class="actions text-dark"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($examenes as $examen): ?>
                    <tr>
                        <td><?= $this->Number->format($examen->id) ?></td>
                        <td><?= h($examen->categorias_examene->nombre ?? '-') ?></td>
                        <td><?= h($examen->nombre) ?></td>
                        <td><?= h($examen->muestra ?: '-') ?></td>
                        <td>S/ <?= number_format((float) $examen->precio, 2) ?></td>
                        <td class="text-muted">S/ <?= number_format((float) $examen->precio_convenio, 2) ?></td>
                        <td><?= h($examen->laboratorio->nombre ?? '-') ?></td>
                        <td>
                            <?php if ($examen->comision_medico > 0): ?>
                                S/ <?= number_format((float) $examen->comision_medico, 2) ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= $examen->estado ? 'success' : 'secondary' ?>">
                                <?= $examen->estado ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="actions text-center">
                            <?= $this->Html->link(
                                '<i class="fas fa-eye"></i>',
                                ['action' => 'view', $examen->id],
                                ['escape' => false, 'title' => 'Ver', 'class' => 'btn btn-info btn-sm openModal']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i>',
                                ['action' => 'edit', $examen->id],
                                ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm openModal']
                            ) ?>
                            <?php if ($examen->estado): ?>
                                <?= $this->Form->postLink(
                                    '<i class="fas fa-ban"></i>',
                                    ['action' => 'delete', $examen->id],
                                    ['escape' => false, 'title' => 'Desactivar', 'class' => 'btn btn-danger btn-sm', 'confirm' => __('¿Seguro que desea desactivar el examen "{0}"?', $examen->nombre)]
                                ) ?>
                            <?php else: ?>
                                <?= $this->Form->postLink(
                                    '<i class="fas fa-check-circle"></i>',
                                    ['action' => 'reactivar', $examen->id],
                                    ['escape' => false, 'title' => 'Reactivar', 'class' => 'btn btn-success btn-sm', 'confirm' => __('¿Reactivar el examen "{0}"?', $examen->nombre)]
                                ) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
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
