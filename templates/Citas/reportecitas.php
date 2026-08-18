<div class="citas reportecitas content">
<?php $this->assign('title', 'Reporte de Citas'); ?>

    <?= $this->Form->create(null, ['type' => 'get', 'id' => 'reporteCitasForm']) ?>
    <fieldset>
        <legend>Filtrar por Rango de Fechas y Doctor</legend>
        <div class="row">
            <div class="col">
                <?= $this->Form->control('fecha_inicio', [
                    'type' => 'date',
                    'label' => 'Fecha Inicio',
                    'value' => $fechaInicio ? $fechaInicio->format('Y-m-d') : '',
                    'class' => 'form-control',
                    'id' => 'fechaInicio'
                ]) ?>
            </div>
            <div class="col">
                <?= $this->Form->control('fecha_fin', [
                    'type' => 'date',
                    'label' => 'Fecha Fin',
                    'value' => $fechaFin ? $fechaFin->format('Y-m-d') : '',
                    'class' => 'form-control',
                    'id' => 'fechaFin'
                ]) ?>
            </div>
            <div class="col">
                <?= $this->Form->control('doctor_id', [
                    'type' => 'select',
                    'label' => 'Doctor',
                    'options' => $doctores, // Pasar la lista de doctores desde el controlador
                    'empty' => 'Todos los doctores',
                    'value' => $doctorId ?? '', // Mantener seleccionado el valor previo
                    'class' => 'form-control',
                ]) ?>
            </div>
            <div class="col">
                <?= $this->Form->control('user_id', [
                    'type' => 'select',
                    'label' => 'Usuario',
                    'options' => $usuarios, // Aqu¨ª debes pasar la lista de usuarios desde el controlador
                    'empty' => 'Todos los usuarios',
                    'value' => $userId ?? '', // Mantener seleccionado el valor previo
                    'class' => 'form-control',
                    'id' => 'userId' // ID para el campo del usuario
                ]) ?>
            </div>
            <!-- Filtro de estado -->
            <div class="col">
                <?= $this->Form->control('estado', [
                    'type' => 'select',
                    'label' => 'Estado',
                    'options' => $estados, // Lista de estados pasados desde el controlador
                    'empty' => 'Todos los estados',
                    'value' => $estadoFiltro ?? '', // Mantener seleccionado el valor previo
                    'class' => 'form-control',
                    'id' => 'estado'
                ]) ?>
            </div>

        </div>
    </fieldset>
    <?= $this->Form->button('Generar Reporte', ['class' => 'btn btn-primary mt-2', 'id' => 'generarReporteBtn']) ?>
     <!-- <?= $this->Html->link('Exportar a Excel', '#', [
    'class' => 'btn btn-success mt-2 ms-2',
    'id' => 'exportarExcelBtn'
]) ?>-->

    <?= $this->Form->end() ?>

    <h3 class="mt-4">Previsualizaci&oacute;n del Reporte</h3>
    <iframe id="previewIframe" style="width: 100%; height: 600px; border: 1px solid #ccc;"></iframe>
</div>

<script>
    document.getElementById('reporteCitasForm').addEventListener('submit', function (event) {
        event.preventDefault();

        const fechaInicio = document.getElementById('fechaInicio').value;
        const fechaFin = document.getElementById('fechaFin').value;
        const doctorId = document.getElementById('doctor-id').value;
        const userId = document.getElementById('userId').value;
        const estado = document.getElementById('estado').value;
        if (!fechaInicio || !fechaFin) {
            alert('Por favor, ingrese ambas fechas: Fecha de Inicio y Fecha Fin.');
            return;
        }

        if (fechaInicio > fechaFin) {
            alert('La Fecha de Inicio no puede ser mayor que la Fecha Fin.');
            return;
        }

        // Construir la URL para el iframe
         const url = `<?= $this->Url->build([
            'controller' => 'Citas',
            'action' => 'exportarReportePdf',
        ]) ?>?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&doctor_id=${doctorId}&user_id=${userId}&estado=${estado}`;

        // Establecer la URL en el iframe
        document.getElementById('previewIframe').src = url;
    });
</script>

<script>
    document.getElementById('exportarExcelBtn').addEventListener('click', function (event) {
    event.preventDefault();

    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFin = document.getElementById('fechaFin').value;
    const doctorId = document.getElementById('doctor-id').value;
    const userId = document.getElementById('userId').value;
    const estado = document.getElementById('estado').value;

    if (!fechaInicio || !fechaFin) {
        alert('Por favor, ingrese ambas fechas: Fecha de Inicio y Fecha Fin.');
        return;
    }

    if (fechaInicio > fechaFin) {
        alert('La Fecha de Inicio no puede ser mayor que la Fecha Fin.');
        return;
    }

    const excelUrl = `<?= $this->Url->build([
        'controller' => 'Citas',
        'action' => 'exportarReporteExcel',
    ]) ?>?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&doctor_id=${doctorId}&user_id=${userId}&estado=${estado}`;

    window.open(excelUrl, '_blank');
});

</script>

