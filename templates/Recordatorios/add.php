<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Recordatorio $recordatorio
 * @var \Cake\Collection\CollectionInterface $pacientes
 */
?>

<div class="container mt-4 mb-4">
	<div class="row">
		<div class="col-md-10 offset-md-1">
			<?php if ($recordatorio->isNew() && !$this->Permisos->tiene('Recordatorios', 'add')): ?>
				<div class="alert alert-danger" role="alert">
					<i class="fas fa-lock"></i> No tienes permisos para registrar recordatorios.
				</div>
			<?php elseif (!$recordatorio->isNew() && !$this->Permisos->tiene('Recordatorios', 'edit')): ?>
				<div class="alert alert-danger" role="alert">
					<i class="fas fa-lock"></i> No tienes permisos para editar recordatorios.
				</div>
			<?php else: ?>
				<?= $this->Form->create($recordatorio, ['class' => 'row g-3']) ?>

				<div class="col-12 mb-4">
					<h3 class="text-info">
						<i class="fas fa-bell"></i>
						<?= $recordatorio->isNew() ? 'Nuevo Recordatorio' : 'Editar Recordatorio' ?>
					</h3>
				</div>

				<div class="col-md-12 mb-3">
					<label class="form-label">Paciente <span class="text-danger">*</span></label>
					<div class="input-group">
						<input type="text" id="searchPaciente" class="form-control"
							placeholder="Ingrese nombre o apellido del paciente"
							value="<?= $pacienteSeleccionado ? h($pacienteSeleccionado->nombre . ' ' . $pacienteSeleccionado->apellido) : '' ?>"
							<?= $pacienteSeleccionado ? 'readonly' : '' ?>>
						<button type="button" id="searchButton" class="btn btn-primary" <?= $pacienteSeleccionado ? 'disabled' : '' ?>>
							<i class="fas fa-search"></i> Buscar
						</button>
					</div>
					<div id="pacienteResults" class="list-group mt-1"></div>
					<?= $this->Form->hidden('paciente_id', [
						'id' => 'pacienteId',
						'value' => $pacienteSeleccionado ? $pacienteSeleccionado->id : '',
						'required' => true,
					]) ?>
				</div>

				<div class="col-md-12 mb-3">
					<?= $this->Form->control('titulo', [
						'label' => 'Título del control',
						'class' => 'form-control',
						'required' => true,
						'placeholder' => 'Ej: Control mensual'
					]) ?>
				</div>

				<div class="col-md-6 mb-3">
					<?= $this->Form->control('fecha_inicio', [
						'label' => 'Fecha inicio',
						'type' => 'date',
						'class' => 'form-control',
						'required' => true,
						'value' => date('Y-m-d')
					]) ?>
				</div>

				<div class="col-md-6 mb-3">
					<?= $this->Form->control('duracion_estimada', [
						'label' => 'Duración estimada',
						'class' => 'form-control',
						'placeholder' => 'Ej: 30 días / 3 sesiones'
					]) ?>
				</div>

				<div class="col-md-12 mb-3">
					<?= $this->Form->control('observacion', [
						'label' => 'Observación',
						'type' => 'textarea',
						'rows' => 4,
						'class' => 'form-control',
						'placeholder' => 'Comentarios generales'
					]) ?>
				</div>

				<div class="col-12 text-center">
					<?= $this->Form->button(
						$recordatorio->isNew() ? __('Guardar Recordatorio') : __('Actualizar Recordatorio'),
						['class' => 'btn btn-info']
					) ?>
				</div>

				<?= $this->Form->end() ?>
			<?php endif; ?>
		</div>
	</div>
</div>