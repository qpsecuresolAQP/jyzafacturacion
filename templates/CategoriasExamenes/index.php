<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\CategoriasExamene> $categoriasExamenes
 * @var string $estadoFiltro
 */
?>
<?php $this->assign('title', 'Categorías de Exámenes'); ?>

<div class="categorias-examenes index content">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="btn-group" role="group">
            <?= $this->Html->link('Activos', ['action' => 'index', '?' => ['estado' => 'activos']], [
                'class' => 'btn btn-sm ' . ($estadoFiltro === 'activos' ? 'btn-info' : 'btn-outline-info'),
            ]) ?>
            <?= $this->Html->link('Inactivos', ['action' => 'index', '?' => ['estado' => 'inactivos']], [
                'class' => 'btn btn-sm ' . ($estadoFiltro === 'inactivos' ? 'btn-info' : 'btn-outline-info'),
            ]) ?>
            <?= $this->Html->link('Todos', ['action' => 'index', '?' => ['estado' => 'todos']], [
                'class' => 'btn btn-sm ' . ($estadoFiltro === 'todos' ? 'btn-info' : 'btn-outline-info'),
            ]) ?>
        </div>

        <?= $this->Html->link(__('Añadir Categoría'), ['action' => 'add'], ['class' => 'btn btn-info openModal']) ?>
    </div>
    <div class="contenedor principal">
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'N°') ?></th>
                        <th><?= $this->Paginator->sort('nombre', 'Nombre') ?></th>
                        <th><?= $this->Paginator->sort('estado', 'Estado') ?></th>
                        <th class="actions text-dark"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categoriasExamenes as $categoria): ?>
                    <tr>
                        <td><?= $this->Number->format($categoria->id) ?></td>
                        <td><?= h($categoria->nombre) ?></td>
                        <td>
                            <span class="badge bg-<?= $categoria->estado ? 'success' : 'secondary' ?>">
                                <?= $categoria->estado ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="actions text-center">
                            <?= $this->Html->link(
                                '<i class="fas fa-eye"></i>',
                                ['action' => 'view', $categoria->id],
                                ['escape' => false, 'title' => 'Ver', 'class' => 'btn btn-info btn-sm openModal']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i>',
                                ['action' => 'edit', $categoria->id],
                                ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm openModal']
                            ) ?>
                            <?php if ($categoria->estado): ?>
                                <?= $this->Form->postLink(
                                    '<i class="fas fa-ban"></i>',
                                    ['action' => 'delete', $categoria->id],
                                    ['escape' => false, 'title' => 'Desactivar', 'class' => 'btn btn-danger btn-sm', 'confirm' => __('¿Seguro que desea desactivar la categoría "{0}"?', $categoria->nombre)]
                                ) ?>
                            <?php else: ?>
                                <?= $this->Form->postLink(
                                    '<i class="fas fa-check-circle"></i>',
                                    ['action' => 'reactivar', $categoria->id],
                                    ['escape' => false, 'title' => 'Reactivar', 'class' => 'btn btn-success btn-sm', 'confirm' => __('¿Reactivar la categoría "{0}"?', $categoria->nombre)]
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
