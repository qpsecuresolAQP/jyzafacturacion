<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Documento> $documentos
 */
?>

<div class="documentos index content">
    <!-- Botón para añadir nuevo documento -->
    <?= $this->Html->link(__('Añadir Documento'), ['action' => 'add'], ['class' => 'button float-right btn btn-info openModal']) ?>

    <!-- Título de la página -->
    <div class="contenedor principal">
        
        <!-- Tabla de documentos -->
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'N° de Documento') ?></th>
                        <th><?= $this->Paginator->sort('historia_id', 'Historia ID') ?></th>
                        <th><?= $this->Paginator->sort('descripcion', 'Descripción') ?></th>
                        <th><?= $this->Paginator->sort('tipo', 'Tipo') ?></th>
                        <th><?= $this->Paginator->sort('ruta_archivo', 'Ruta Archivo') ?></th>
                        <th><?= $this->Paginator->sort('created', 'Creado') ?></th>
                        <th><?= $this->Paginator->sort('modified', 'Modificado') ?></th>
                        <th class="actions text-dark"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documentos as $documento): ?>
                    <tr>
                        <td><?= $this->Number->format($documento->id) ?></td>
                        <td><?= h($documento->historia_id) ?></td>
                        <td><?= h($documento->descripcion) ?></td>
                        <td><?= h($documento->tipo) ?></td>
                        <td><?= h($documento->ruta_archivo) ?></td>
                        <td><?= h($documento->created) ?></td>
                        <td><?= h($documento->modified) ?></td>
                        <td class="actions text-center">
                            <!-- Íconos personalizados para acciones -->
                            <?= $this->Html->link(
                                '<i class="fas fa-eye"></i>',
                                ['action' => 'view', $documento->id],
                                ['escape' => false, 'title' => 'Ver', 'class' => 'btn btn-info btn-sm openModal']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i>',
                                ['action' => 'edit', $documento->id],
                                ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm openModal']
                            ) ?>
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