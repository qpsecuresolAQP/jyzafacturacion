<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Presupuesto> $presupuestos
 * @var string $searchTerm
 */
?>

<!-- Formulario de búsqueda -->
<?= $this->Form->create(null, ['type' => 'get', 'class' => 'mb-3 me-3']) ?>
<div class="input-group">
    <?= $this->Form->control('search', [ 
        'label' => false,
        'placeholder' => 'Buscar por: Nombre/Apellido',
        'value' => $searchTerm ?? '',
        'class' => 'form-control',
    ]) ?>
    <button class="btn btn-info mx-2" type="submit">Buscar</button>
</div>
<?= $this->Html->link(__('Añadir Presupuesto'), ['action' => 'add'], ['class' => 'button float-right btn btn-info openModal d-block d-sm-inline-block mt-3 mt-sm-0']) ?>

<?= $this->Form->end() ?>
<div class="presupuestos index content">

    <div class="contenedor principal">
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'N° de Presupuesto') ?></th>
                        <th><?= $this->Paginator->sort('paciente_id', 'Paciente') ?></th>
                        <th><?= $this->Paginator->sort('total', 'Total') ?></th>
                        <th><?= $this->Paginator->sort('created', 'Creado') ?></th>
                        <th><?= $this->Paginator->sort('modified', 'Modificado') ?></th>
                        <th class="actions text-dark"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($presupuestos as $presupuesto): ?>
                    <tr>
                        <td><?= $this->Number->format($presupuesto->id) ?></td>
<td class="text-black">
<?php
if (!empty($presupuesto->historia_id) &&
    !empty($presupuesto->historias_clinica) &&
    !empty($presupuesto->historias_clinica->paciente)) {

    echo $this->Html->link(
        h($presupuesto->historias_clinica->paciente->nombre),
        [
            'controller' => 'Pacientes',
            'action' => 'view',
            $presupuesto->historias_clinica->paciente_id
        ],
        ['class' => 'text-black openModal']
    );

} else {

    echo h($presupuesto->nombre_apellido ?: 'Nueva Proforma');

}
?>
</td>

                        <td><?= $presupuesto->total !== null ? $this->Number->currency($presupuesto->total, 'S/. ') : '&nbsp;' ?></td>
                        <td><?= h($presupuesto->created) ?></td>
                        <td><?= h($presupuesto->modified) ?></td>
                        <td class="actions text-center">
                            <!-- Íconos personalizados para acciones -->
                            <?= $this->Html->link(
                                '<i class="fas fa-eye"></i>',
                                ['action' => 'view', $presupuesto->id],
                                ['escape' => false, 'title' => 'Ver', 'class' => 'btn btn-info btn-sm openModal']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i>',
                                ['action' => 'edit', $presupuesto->id],
                                ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm openModal']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-file-pdf"></i>',
                                ['action' => 'exportPresupuestoPdf', $presupuesto->id],
                                ['escape' => false, 'title' => 'Exportar a PDF', 'class' => 'btn btn-danger btn-sm']
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
