<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\HistoriasClinica $historiasClinica
 * @var \Cake\Collection\CollectionInterface|string[] $pacientes
 */
?>

<div class="container mt-4 mb-4">
    <?= $this->Form->create($historiasClinica, ['class' => 'row g-3']) ?>

    <!-- Información de la Historia Clínica -->
    <div class="col-12">
        <h3 class="text-info"><i class="fas fa-file-medical"></i> Agregar Historia Clínica</h3>
    </div>

    <!-- Campo Paciente -->
    <div class="col-md-12 mb-3">
        <?php if (!empty($historiasClinica->paciente_id)) : ?>
            <!-- Campo oculto si el paciente ya está definido -->
            <?= $this->Form->control('paciente_id', [
                'type' => 'hidden',
                'value' => $historiasClinica->paciente_id
            ]) ?>
        <?php else : ?>
            <!-- Campo desplegable si no hay paciente definido -->
            <?= $this->Form->control('paciente_id', [
                'options' => $pacientes, 
                'empty' => true,
                'label' => 'Paciente',
                'class' => 'form-control',
            ]) ?>
        <?php endif; ?>
    </div>    


    <!-- Campo DNI / Carnet-->
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('dni', [
            'label' => 'DNI/Carnet de Extranjeria',
            'class' => 'form-control',
            'placeholder' => 'Número de DNI o Carnet de Extranjería',
            'pattern' => '^[0-9]{7,20}$', 
            'maxlength' => '20',
            'inputmode' => 'numeric',
            'required' => true,
            'title' => 'Solo se permiten números minimo 8 digitos'
        ]) ?>
    </div>

    <!-- Campo Fecha de Nacimiento -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('fecha_nacimiento', [
            'label' => 'Fecha de Nacimiento',
            'type' => 'date',
            'class' => 'form-control',
            'placeholder' => 'Fecha de Nacimiento'
        ]) ?>
    </div>

    <!-- Campo Edad -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('edad', [
            'label' => 'Edad',
            'class' => 'form-control',
            'placeholder' => 'Edad'
        ]) ?>
    </div>

    <!-- Campo Sexo -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('sexo', [
            'type' => 'select',
            'options' => ['M' => 'Masculino', 'F' => 'Femenino'],
            'empty' => 'Seleccione el Genero',
            'label' => 'Sexo',
            'class' => 'form-control'
        ]) ?>
    </div>

    <!-- Campo Tratamiento de interes -->
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('tipo_orden', [
            'label' => 'Tratamiento de interes',
            'class' => 'form-control',
            'placeholder' => 'Detalles sobre el tratamiento'
        ]) ?>
    </div>

    <!-- Campo Usuario que registra -->
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('user_id', [
            'options' => $users,
            'empty' => true,
            'label' => 'Usuario que registra',
            'class' => 'form-control',
            'disabled' => true,
            'value' => $usuario->id
        ]) ?>
        <?= $this->Form->hidden('user_id', ['value' => $usuario->id]) ?>
    </div>

    <!-- Campo Cómo se enteró -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('como_entero', [
            'label' => '¿Cómo se enteró?',
            'class' => 'form-control',
            'placeholder' => 'Detalles sobre cómo se enteró'
        ]) ?>
    </div>

    <!-- Campo Email -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('email', [
            'label' => 'Email',
            'class' => 'form-control',
            'placeholder' => 'Correo Electronico',
            'required' => false
        ]) ?>
    </div>

    <!-- Campo Observaciones Administrativas -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('obs_administrativas', [
            'label' => 'Observaciones Administrativas',
            'class' => 'form-control',
            'placeholder' => 'Detalles sobre observaciones administrativas'
        ]) ?>
    </div>

    <!-- Campo Apoderado -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('apoderado', [
            'label' => 'Apoderado del paciente',
            'class' => 'form-control',
            'placeholder' => 'Apoderado del paciente'
        ]) ?>
    </div>

    <!-- Campo Parentesco -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('parentesco', [
            'label' => 'Parentesco',
            'class' => 'form-control',
            'placeholder' => 'Parentesco con el paciente'
        ]) ?>
    </div>

    <!-- Campo Ocupación -->
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('ocupacion', [
            'label' => 'Ocupación',
            'class' => 'form-control',
            'placeholder' => 'Detalles sobre ocupación'
        ]) ?>
    </div>

    <!-- Campo Recomendado por -->
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('recomendado', [
            'label' => 'Recomendado por',
            'class' => 'form-control',
            'placeholder' => 'Detalles sobre quien lo recomendo'
        ]) ?>
    </div>

    <!-- Botones -->
    <div class="col-12 text-center mt-3">
        <?= $this->Form->button('Guardar Historia Clínica', [
            'type' => 'submit',
            'class' => 'btn btn-primary',
            'id' => 'btnGuardar'
        ]) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>
<script>
    document.getElementById('fecha-nacimiento').addEventListener('change', function() {
        var fechaNacimiento = new Date(this.value);
        var hoy = new Date();
        var edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
        var mes = hoy.getMonth() - fechaNacimiento.getMonth();
    
        if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
            edad--;
        }
    
        document.getElementById('edad').value = edad;
    });

    document.addEventListener("DOMContentLoaded", function() {
        const form = document.querySelector("form"); // Obtiene el formulario
        const btnGuardar = document.getElementById("btnGuardar");

        form.addEventListener("submit", function(event) {
            if (!form.checkValidity()) {
                event.preventDefault(); // Evita que el formulario se envíe si no es válido
                return;
            }
            btnGuardar.disabled = true;
            btnGuardar.innerText = "Guardando...";
        });
    });
</script>