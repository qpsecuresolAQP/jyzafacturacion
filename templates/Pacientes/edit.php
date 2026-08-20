<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Paciente $paciente
 * @var string[]|\Cake\Collection\CollectionInterface $users
 */
?>
<div class="container mt-4 mb-4">
    <?= $this->Form->create($paciente, ['class' => 'row g-3']) ?>

    <div class="col-12 mb-4">
        <h3 class="text-info"><i class="fas fa-user-edit"></i> Editar Paciente</h3>
    </div>

    <div class="col-md-6 mb-3">
        <?= $this->Form->control('nombre', ['class' => 'form-control', 'id' => 'nombre', 'label' => 'Nombre']) ?>
    </div>
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('apellido', ['class' => 'form-control', 'id' => 'apellido', 'label' => 'Apellido']) ?>
    </div>
    <div class="col-md-6 mb-3">
        <?= $this->Form->control('telefono_celular', ['class' => 'form-control', 'label' => 'Teléfono Celular', 'pattern' => '^[0-9]{1,22}$', 'maxlength' => '22' , 'title' => 'Solo se permiten números']) ?>
    </div>

<?php if (!empty($historiaClinica->id)) : ?>
<div class="col-md-6 mb-3">
    <label class="form-label" for="historia-clinica-dni">DNI/Carnet de Extranjeria</label>
    <div class="input-group">
        <?= $this->Form->control('historia_clinica.dni', [
            'class' => 'form-control',
            'id' => 'historia-clinica-dni',
            'label' => false,
            'value' => $paciente->historias_clinica->dni ?? '',
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
    <?= $this->Form->control('historia_clinica.fecha_nacimiento', ['class' => 'form-control', 'type' => 'date' ,'label' => 'Fecha de Nacimiento', 'id' => 'fecha-nacimiento', 'empty' => true, 'value' => $paciente->historias_clinica->fecha_nacimiento ?? '', 'max' => date('Y-m-d')]) ?>
</div>
<div class="col-md-6 mb-3">
    <?= $this->Form->control('historia_clinica.edad', ['class' => 'form-control', 'label' => 'Edad', 'value' => $paciente->historias_clinica->edad ?? '', 'id' => 'edad']) ?>
</div>
<div class="col-md-6 mb-3">
    <?= $this->Form->control('historia_clinica.direccion', ['class' => 'form-control', 'label' => 'Dirrecion', 'value' => $paciente->historias_clinica->direccion ?? '', 'id' => 'direccion']) ?>
</div>
<div class="col-md-6 mb-3">
    <?= $this->Form->control('historia_clinica.sexo', [
        'type' => 'select',
        'options' => ['M' => 'Masculino', 'F' => 'Femenino'],
        'empty' => 'Seleccione...',
        'class' => 'form-control',
        'label' => 'Sexo',
        'default' => $paciente->historias_clinica->sexo ?? ''// Carga el valor actual del paciente
    ]); ?>
</div>
<div class="col-md-6 mb-3">
    <?= $this->Form->control('historia_clinica.tipo_orden', ['class' => 'form-control', 'label' => 'Tratamiento de interes', 'value' => $paciente->historias_clinica->tipo_orden ?? '']) ?>
</div>
<div class="col-md-6 mb-3">
    <?= $this->Form->control('historia_clinica.user_id', ['options' => $users, 'class' => 'form-control', 'label' => 'Usuario que registra', 'empty' => true, 'value' => $paciente->historias_clinica->user_id ?? '']) ?>
</div>
<div class="col-md-6 mb-3">
    <?= $this->Form->control('historia_clinica.como_entero', ['class' => 'form-control', 'label' => 'Como Enteró', 'value' => $paciente->historias_clinica->como_entero ?? '']) ?>
</div>
<div class="col-md-6 mb-3">
    <?= $this->Form->control('historia_clinica.ocupacion', ['class' => 'form-control', 'label' => 'Ocupación', 'value' => $paciente->historias_clinica->ocupacion ?? '']) ?>
</div>
<div class="col-md-6 mb-3">
    <?= $this->Form->control('historia_clinica.obs_administrativas', ['class' => 'form-control', 'label' => 'Observaciones Administrativas', 'value' => $paciente->historias_clinica->obs_administrativas ?? '']) ?>
</div>
<?php else : ?>
    <p class="text-center text-muted">Este paciente no tiene una historia clínica registrada.</p>
<?php endif; ?>

    <div class="col-12 text-center mt-3">
        <?= $this->Form->button(__('Guardar Cambios'), ['type' => 'submit', 'class' => 'btn btn-primary']) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>

<script>
    const urlConsultarDni = '<?= $this->Url->build(['controller' => 'Pacientes', 'action' => 'consultarDniApi']) ?>';

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