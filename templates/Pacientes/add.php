<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Paciente $paciente
 * @var \Cake\Collection\CollectionInterface|string[] $departamentos
 * @var \Cake\Collection\CollectionInterface|string[] $campanas
 * @var \Cake\Collection\CollectionInterface|string[] $users
 */
?>
<div class="container mt-4 mb-4">
    <?= $this->Form->create($paciente, ['class' => 'row g-3']) ?>

    <!-- Título -->
    <div class="col-12 mb-4 text-info">
        <h3><i class="fas fa-user-plus"></i> Agregar Paciente</h3>
    </div>

    <!-- Campos de Datos Personales -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('nombre', ['class' => 'form-control', 'id' => 'nombre', 'label' => 'Nombres']) ?>
    </div>
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('apellido', ['class' => 'form-control', 'id' => 'apellido', 'label' => 'Apellidos']) ?>
    </div>
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('telefono_celular', ['class' => 'form-control', 'label' => 'Teléfono Celular', 'pattern' => '^[0-9]{1,22}$', 'maxlength' => '22', 'title' => 'Solo se permiten números']) ?>
    </div>
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('historiaClinica.email', ['class' => 'form-control', 'label' => 'Email']) ?>
    </div>

    <!-- Historia Clínica -->
    <div class="col-12 mb-3 text-info">
        <h4><i class="fas fa-notes-medical"></i> Historia Clínica</h4>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="historia-clinica-dni">DNI/Carnet de Extranjería</label>
        <div class="input-group">
            <?= $this->Form->control('historiaClinica.dni', [
                'class' => 'form-control',
                'id' => 'historia-clinica-dni',
                'label' => false,
                'required' => true,
                'pattern' => '^[0-9]{7,20}$',
                'maxlength' => '20',
                'inputmode' => 'numeric',
                'title' => 'Solo se permiten números'
            ]) ?>
            <button type="button" class="btn btn-outline-primary" id="btn-buscar-dni">
                <i class="fas fa-search"></i> Buscar
            </button>
        </div>
        <div id="dni-help" class="form-text mt-1"></div>
    </div>
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('historiaClinica.fecha_nacimiento', ['class' => 'form-control', 'empty' => true, 'type' => 'date', 'id' => 'fecha-nacimiento', 'label' => 'Fecha de Nacimiento', 'max' => date('Y-m-d')]) ?>
    </div>
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('historiaClinica.edad', ['class' => 'form-control', 'id' => 'edad', 'label' => 'Edad', 'inputmode' => 'numeric', 'title' => 'Solo se permiten números']) ?>
    </div>
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('historiaClinica.sexo', [
            'type' => 'select',
            'options' => ['M' => 'Masculino', 'F' => 'Femenino'],
            'empty' => 'Seleccione el Género',
            'class' => 'form-control',
            'label' => 'Sexo'
        ]); ?>
    </div>
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('historiaClinica.departamento_id', [
            'options' => $departamentos,
            'class' => 'form-control',
            'empty' => 'Seleccione Departamento',
            'label' => 'Procedencia'
        ]) ?>
    </div>

    <!-- Direccion -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('historiaClinica.direccion', ['class' => 'form-control', 'label' => 'Dirección']) ?>
    </div>

    <!-- Datos de la Cita -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('historiaClinica.tipo_orden', ['class' => 'form-control', 'label' => 'Tratamiento de interés']) ?>
    </div>

    <!-- Usuario que registra -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('historiaClinica.user_id', [
            'options' => $users,
            'value' => $usuario->id,
            'disabled' => true,
            'class' => 'form-control',
            'label' => 'Usuario que registra'
        ]) ?>
        <?= $this->Form->hidden('historiaClinica.user_id', ['value' => $usuario->id]) ?>
    </div>

    <!-- Datos Adicionales -->
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('historiaClinica.como_entero', ['class' => 'form-control', 'label' => '¿Cómo se enteró?']) ?>
    </div>
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('historiaClinica.ocupacion', ['class' => 'form-control', 'label' => 'Ocupación']) ?>
    </div>
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('historiaClinica.obs_administrativas', ['class' => 'form-control', 'label' => 'Observaciones Administrativas']) ?>
    </div>
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('historiaClinica.apoderado', ['class' => 'form-control', 'label' => 'Apoderado']) ?>
    </div>
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('historiaClinica.parentesco', ['class' => 'form-control', 'label' => 'Parentesco']) ?>
    </div>
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('historiaClinica.medicacion', ['class' => 'form-control', 'label' => 'Medicación']) ?>
    </div>
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('historiaClinica.alergias', ['class' => 'form-control', 'label' => 'Reacciones adversas a medicamentos']) ?>
    </div>
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('historiaClinica.enfermedades', ['class' => 'form-control', 'label' => 'Enfermedades']) ?>
    </div>
    <div class="col-md-12 mb-3">
        <?= $this->Form->control('historiaClinica.recomendado', ['class' => 'form-control', 'label' => 'Recomendado por']) ?>
    </div>

    <!-- Botones -->
    <div class="col-12 text-center mt-3">
        <?= $this->Form->button('Guardar Paciente', ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnGuardar']) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>

<script>
    const urlConsultarDni = '<?= $this->Url->build(['action' => 'consultarDniApi']) ?>';

    document.getElementById('btn-buscar-dni').addEventListener('click', async function() {
        const dniField = document.getElementById('historia-clinica-dni');
        const dniHelp = document.getElementById('dni-help');
        const dni = (dniField.value || '').trim();

        if (!/^\d{8}$/.test(dni)) {
            dniHelp.innerHTML = '<span class="text-danger">DNI debe tener exactamente 8 dígitos.</span>';
            return;
        }

        dniHelp.innerHTML = '<span class="text-muted">Consultando DNI...</span>';

        try {
            const res = await fetch(urlConsultarDni + '?dni=' + encodeURIComponent(dni), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            if (!data.ok) {
                dniHelp.innerHTML = `<span class="text-danger">${data.error || data.message || 'No se pudo consultar el DNI.'}</span>`;
                return;
            }

            document.getElementById('nombre').value = data.nombres || '';
            document.getElementById('apellido').value = [data.apellido_paterno, data.apellido_materno].filter(Boolean).join(' ');

            dniHelp.innerHTML = '<span class="text-success">DNI encontrado correctamente.</span>';
        } catch (e) {
            dniHelp.innerHTML = '<span class="text-danger">Error al consultar DNI.</span>';
        }
    });

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
        const form = document.querySelector("form");
        const btnGuardar = document.getElementById("btnGuardar");

        form.addEventListener("submit", function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                return;
            }
            btnGuardar.disabled = true;
            btnGuardar.innerText = "Guardando...";
        });
    });
</script>
