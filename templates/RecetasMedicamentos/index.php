<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\RecetaMedicamento> $recetasMedicamentos
 */
?>
<div class="recetasMedicamentos index content">
    <div class="contenedor principal">
        <h3 class="text-info"><i class="fas fa-cogs me-2"></i> Medicamentos en Recetas</h3>
        
        <div class="table-responsive mt-3">
            <table class="table table-striped table-hover">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('id') ?></th>
                        <th><?= $this->Paginator->sort('receta_id', 'Receta') ?></th>
                        <th><?= $this->Paginator->sort('medicamento_id', 'Medicamento') ?></th>
                        <th><?= $this->Paginator->sort('concentracion') ?></th>
                        <th><?= $this->Paginator->sort('cantidad') ?></th>
                        <th><?= $this->Paginator->sort('forma_farmaceutica_id', 'Forma') ?></th>
                        <th><?= $this->Paginator->sort('via_administracion_id', 'Vía') ?></th>
                        <th><?= $this->Paginator->sort('duracion_dias', 'Duración (días)') ?></th>
                        <th class="actions"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recetasMedicamentos as $recetaMedicamento): ?>
                    <tr>
                        <td><?= $this->Number->format($recetaMedicamento->id) ?></td>
                        <td><?= $recetaMedicamento->hasValue('receta') ? $this->Html->link($recetaMedicamento->receta->nombre ?? 'Receta #' . $recetaMedicamento->receta->id, ['controller' => 'Recetas', 'action' => 'view', $recetaMedicamento->receta->id]) : '' ?></td>
                        <td><?= $recetaMedicamento->hasValue('medicamento') ? $this->Html->link($recetaMedicamento->medicamento->nombre, ['controller' => 'Medicamentos', 'action' => 'view', $recetaMedicamento->medicamento->id]) : '' ?></td>
                        <td><?= h($recetaMedicamento->concentracion) ?></td>
                        <td><?= $this->Number->format($recetaMedicamento->cantidad) ?></td>
                        <td><?= $recetaMedicamento->hasValue('forma_farmaceutica') ? h($recetaMedicamento->forma_farmaceutica->nombre) : 'N/A' ?></td>
                        <td><?= $recetaMedicamento->hasValue('via_administracion') ? h($recetaMedicamento->via_administracion->nombre) : 'N/A' ?></td>
                        <td><?= $recetaMedicamento->duracion_dias ? $this->Number->format($recetaMedicamento->duracion_dias) : 'N/A' ?></td>
                        <td class="actions text-center">
                            <?= $this->Html->link(
                                '<i class="fas fa-eye text-info"></i>',
                                ['action' => 'view', $recetaMedicamento->id],
                                ['escape' => false, 'title' => 'Ver', 'class' => 'btn btn-sm btn-outline-info']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit text-warning"></i>',
                                ['action' => 'edit', $recetaMedicamento->id],
                                ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-sm btn-outline-warning']
                            ) ?>
                            <?= $this->Form->postLink(
                                '<i class="fas fa-trash text-danger"></i>',
                                ['action' => 'delete', $recetaMedicamento->id],
                                [
                                    'escape' => false,
                                    'title' => 'Eliminar',
                                    'class' => 'btn btn-sm btn-outline-danger',
                                    'confirm' => __('¿Estás seguro?'),
                                ]
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
        <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registro(s) de {{count}}')) ?></p>
    </div>
</div>
