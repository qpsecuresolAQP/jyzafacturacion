
<!-- Formulario de búsqueda -->
<?= $this->Form->create(null, ['type' => 'get', 'class' => 'mb-3 me-3']) ?>
<div class="input-group mt-2">
    <?= $this->Form->control('search', [
        'label' => false,
        'placeholder' => 'Buscar por: Apellido/Nombre',
        'value' => $searchTerm ?? '',
        'class' => 'form-control',
    ]) ?>
    <button class="btn btn-info mx-2" type="submit">Buscar</button>
</div>
<div class="input-group  mt-2">
    <?= $this->Form->control('searchDni', [
        'label' => false,
        'placeholder' => 'Buscar por: Dni',
        'value' => $searchTermDni ?? '',
        'class' => 'form-control',
    ]) ?>
    <button class="btn btn-info mx-2 " type="submit">Buscar</button>
</div>
<div class="input-group mt-2">
    <?= $this->Form->control('searchTelefono', [
        'label' => false,
        'placeholder' => 'Buscar por: Celular',
        'value' => $searchTelefono ?? '',
        'class' => 'form-control',
    ]) ?>
    <button class="btn btn-info mx-2" type="submit">Buscar</button>
</div>
<?= $this->Form->end() ?>

<div class="pacientes index content">
            <?php if (!empty($this->request->getQuery('con_historia'))): ?>
            <!-- Ya está filtrado → mostrar botón para volver -->
            <?= $this->Html->link('Mostrar todos', ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
        <?php else: ?>
            <!-- No está filtrado → mostrar botón para aplicar filtro -->
            <?= $this->Html->link('Mostrar pacientes con Historia Clínica', ['action' => 'index', '?' => ['con_historia' => 1]], ['class' => 'btn btn-info']) ?>
        <?php endif; ?>
    <?= $this->Html->link(__('Añadir Paciente'), ['action' => 'add'], ['class' => 'button float-right btn btn-info']) ?>
   <div class="contenedor principal">
    <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'ID') ?></th>
                        <th><?= $this->Paginator->sort('apellido', 'Apellidos') ?></th>
                        <th><?= $this->Paginator->sort('nombre', 'Nombres') ?></th>
                        <th><?= $this->Paginator->sort('dni', 'DNI') ?></th>
                        <th><?= $this->Paginator->sort('telefono_celular', 'Celular') ?></th>
                        <th><?= $this->Paginator->sort('departamento_id', 'Procedencia') ?></th>
                        <th><?= $this->Paginator->sort('created', 'Fecha de Creación') ?></th>
                        <th class="actions text-dark"><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pacientes as $paciente): ?>
                    <tr>
                        <td><?= $this->Number->format($paciente->id) ?></td>
                        <td><?= h($paciente->apellido) ?></td>
                        <td><?= h($paciente->nombre) ?></td>
                        <td><?= h($paciente->historias_clinica->dni ?? '') ?></td>
                        <td><?= h($paciente->telefono_celular) ?></td>
                        <td><?= !empty($paciente->historias_clinica->direccion)? h($paciente->historias_clinica->direccion) : 'Sin Lugar de dirrecion' ?></td>
                        <td><?= h($paciente->created->format('d/m/Y')) ?></td>
                        <td class="actions text-center">
                            <!-- Íconos personalizados para acciones -->
                            <?= $this->Html->link(
                                '<i class="fas fa-eye"></i>',
                                ['action' => 'view', $paciente->id],
                                ['escape' => false, 'title' => 'Ver', 'class' => 'btn btn-info btn-sm']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-edit"></i>',
                                ['action' => 'edit', $paciente->id],
                                ['escape' => false, 'title' => 'Editar', 'class' => 'btn btn-warning btn-sm']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="fas fa-file-pdf"></i>',
                                ['action' => 'exportPacientePdf', $paciente->id],
                                ['escape' => false, 'title' => 'Exportar a PDF', 'class' => 'btn btn-danger btn-sm']
                            ) ?>
                            <?php if ($this->Permisos->tiene('Pacientes', 'delete')): ?>
                            <?= $this->Form->postLink(
                                '<i class="fas fa-trash"></i>',
                                ['action' => 'delete', $paciente->id],
                                ['escape' => false, 'title' => 'Eliminar', 'class' => 'btn btn-danger btn-sm', 'confirm' => __('¿Estás seguro de que quieres eliminar el paciente {0}?', $paciente->nombre)]
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