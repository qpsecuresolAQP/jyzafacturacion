<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\HistoriasClinica> $historiasClinicas
 */
?>

<div class="historiasClinicas index content">
    <?= $this->Html->link(__('Añadir Historia Clínica'), ['action' => 'add'], ['class' => 'button float-right btn btn-info openModal']) ?>
    <div class="contenedor principal">
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'N° de Historia') ?></th>
                        <th><?= $this->Paginator->sort('paciente_id', 'Paciente') ?></th>
                        <th><?= $this->Paginator->sort('medicacion', 'Medicacion') ?></th>
                        <th><?= $this->Paginator->sort('alergias', 'Alergias') ?></th>
                        <th><?= $this->Paginator->sort('enfermedades', 'Enfermedades') ?></th>
                        <th><?= $this->Paginator->sort('created', 'Creado') ?></th>
                        <th><?= $this->Paginator->sort('modified', 'Modificado') ?></th>
                        <th class="actions text-dark"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historiasClinicas as $historiasClinica): ?>
                    <tr>
                        <td><?= $this->Number->format($historiasClinica->id) ?></td>
                        <td><?= $historiasClinica->hasValue('paciente') ? $this->Html->link($historiasClinica->paciente->nombre, ['controller' => 'Pacientes', 'action' => 'view', $historiasClinica->paciente->id]) : '' ?></td>
                        <td><?= h($historiasClinica->medicacion) ?></td>
                        <td><?= h($historiasClinica->alergias) ?></td>
                        <td><?= h($historiasClinica->enfermedades) ?></td>
                        <td><?= h($historiasClinica->created) ?></td>
                        <td><?= h($historiasClinica->modified) ?></td>
                        <td class="actions text-center">
                            <!-- Íconos personalizados para acciones -->
                            <?= $this->Html->link(
                                '<i class="fas fa-eye"></i>',
                                ['action' => 'view', $historiasClinica->id],
                                ['escape' => false, 'title' => 'Ver', 'class' => 'btn btn-info btn-sm openModal']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i>',
                                ['action' => 'edit', $historiasClinica->id],
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