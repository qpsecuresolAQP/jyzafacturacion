<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Receta> $recetas
 */
?>

<!-- Formulario de búsqueda -->
<?= $this->Form->create(null, ['type' => 'get', 'class' => 'mb-3 me-3']) ?>
<div class="input-group">
    <?= $this->Form->control('search', [
        'label' => false,
        'placeholder' => 'Buscar por: Nombre de Receta',
        'value' => $searchTerm ?? '',
        'class' => 'form-control',
    ]) ?>
    <button class="btn btn-info mx-2" type="submit">Buscar</button>
</div>
<?= $this->Form->end() ?>

<div class="recetas index content">
    <?= $this->Html->link(__('Añadir Receta'), ['action' => 'add'], ['class' => 'button float-right btn btn-info']) ?>
    <div class="contenedor principal">
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'N° de Receta') ?></th>
                        <th><?= $this->Paginator->sort('nombre', 'Nombre') ?></th>
                        <th><?= $this->Paginator->sort('created', 'Creado') ?></th>
                        <th><?= $this->Paginator->sort('modified', 'Modificado') ?></th>
                        <th class="actions text-dark"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recetas as $receta): ?>
                    <tr>
                        <td><?= $this->Number->format($receta->id) ?></td>
                        <td><?= h($receta->nombre) ?></td>
                        <td><?= h($receta->created) ?></td>
                        <td><?= h($receta->modified) ?></td>
                        <td class="actions text-center">
                            <!-- Íconos personalizados para acciones -->
                            <?= $this->Html->link(
                                '<i class="fas fa-eye"></i>',
                                ['action' => 'view', $receta->id],
                                ['escape' => false, 'title' => 'Ver', 'class' => 'btn btn-info btn-sm openModal']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i>',
                                ['action' => 'edit', $receta->id],
                                ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm']
                            ) ?>
                            <!-- <?= $this->Html->link(
                                '<i class="fas fa-file-pdf"></i>',
                                ['action' => 'exportRecetaPdf', $receta->id],
                                ['escape' => false, 'title' => 'Descargar Receta', 'class' => 'btn btn-danger btn-sm']
                            ) ?> -->
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
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