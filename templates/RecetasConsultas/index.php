<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\RecetasConsulta> $recetasConsultas
 */
?>
<div class="recetasConsultas index content">
    <div class="contenedor principal">
        <h3 class="text-info"><i class="fas fa-cogs me-2"></i> Recetas Consultas</h3>
        
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'N° de Consulta') ?></th>
                        <th><?= $this->Paginator->sort('consulta_id', 'Consulta') ?></th>
                        <th><?= $this->Paginator->sort('receta_id', 'Receta') ?></th>
                        <th><?= $this->Paginator->sort('observaciones', 'Observaciones') ?></th>
                        <th><?= $this->Paginator->sort('created', 'Creado') ?></th>
                        <th><?= $this->Paginator->sort('modified', 'Modificado') ?></th>
                        <th class="actions"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recetasConsultas as $recetasConsulta): ?>
                    <tr>
                        <td><?= $this->Number->format($recetasConsulta->id) ?></td>
                        <td><?= $recetasConsulta->hasValue('consulta') ? $this->Html->link($recetasConsulta->consulta->motivo, ['controller' => 'Consultas', 'action' => 'view', $recetasConsulta->consulta->id]) : '' ?></td>
                        <td><?= $recetasConsulta->hasValue('receta') ? $this->Html->link($recetasConsulta->receta->nombre, ['controller' => 'Recetas', 'action' => 'view', $recetasConsulta->receta->id]) : '' ?></td>
                        <td><?= h($recetasConsulta->observaciones) ?></td>
                        <td><?= h($recetasConsulta->created) ?></td>
                        <td><?= h($recetasConsulta->modified) ?></td>
                        <td class="actions text-center">
                            <!-- Íconos personalizados para acciones -->
                            <?= $this->Html->link(
                                '<i class="fas fa-eye"></i>',
                                ['action' => 'view', $recetasConsulta->id],
                                ['escape' => false, 'title' => 'Ver', 'class' => 'btn btn-info btn-sm openModal']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i>',
                                ['action' => 'edit', $recetasConsulta->id],
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