<?php $this->assign('title', 'Reporte de Pacientes y Campañas'); ?>

<?= $this->Form->create(null, ['type' => 'get', 'class' => 'mb-3']) ?>
<fieldset>
    <legend>Filtrar por Rango de Fechas y Doctor</legend>
    <div class="row">
        <div class="col">
            <?= $this->Form->control('start_date', [
                'type' => 'date',
                'label' => 'Fecha Inicio',
                'class' => 'form-control',
                'value' => $startDate ?? ''
            ]) ?>
        </div>
        <div class="col">
            <?= $this->Form->control('end_date', [
                'type' => 'date',
                'label' => 'Fecha Fin',
                'class' => 'form-control',
                'value' => $endDate ?? ''
            ]) ?>
        </div>
        <div class="col">
            <?= $this->Form->control('campana_id', [
                'type' => 'select',
                'options' => $campañas,
                'empty' => 'Seleccione Camapañas',
                'label' => 'Camapñas',
                'class' => 'form-control',
                'value' => $campanaId ?? ''
            ]) ?>
        </div>
    </div>
</fieldset>
<?= $this->Form->button('Generar Reporte', ['class' => 'btn btn-primary mt-2', 'id' => 'generarReporteBtn']) ?>
<?= $this->Form->button('Exportar a Excel', ['class' => 'btn btn-success mt-2', 'id' => 'exportExcelBtn']) ?>
<?= $this->Form->end() ?>

<h3 class="mt-4">Previsualización del Reporte</h3>
<iframe id="previewIframe" style="width: 100%; height: 600px; border: 1px solid #ccc;"></iframe>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Actualizar previsualización al cambiar filtros
        document.querySelector('form').addEventListener('submit', function (event) {
            event.preventDefault();
            const campanaId = document.querySelector('select[name="campana_id"]').value;
            const startDate = document.querySelector('input[name="start_date"]').value;
            const endDate = document.querySelector('input[name="end_date"]').value;

            if (!startDate || !endDate) {
                alert('Por favor, ingrese ambas fechas: Fecha de Inicio y Fecha Fin.');
                return false;
            }

            if (startDate > endDate) {
                alert('La Fecha de Inicio no puede ser mayor que la Fecha Fin.');
                return false;
            }

            const url = `<?= $this->Url->build(['controller' => 'VistaPacientesCampanas', 'action' => 'exportarPdf']) ?>?campana_id=${campanaId}&start_date=${startDate}&end_date=${endDate}`;
            document.getElementById('previewIframe').src = url;
        });

        document.getElementById('exportPdfBtn').addEventListener('click', function () {
            const campanaId = document.querySelector('select[name="campana_id"]').value;
            const startDate = document.querySelector('input[name="start_date"]').value;
            const endDate = document.querySelector('input[name="end_date"]').value;
            
            if (!startDate || !endDate) {
                alert('Por favor, ingrese ambas fechas: Fecha de Inicio y Fecha Fin.');
                return;
            }
            const url = `<?= $this->Url->build(['controller' => 'VistaPacientesCampanas', 'action' => 'exportarPdf']) ?>?campana_id=${campanaId}&start_date=${startDate}&end_date=${endDate}&download=1`;
            window.location.href = url;
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('exportExcelBtn').addEventListener('click', function (event) {
            event.preventDefault();
    
            const startDate = document.querySelector('input[name="start_date"]').value;
            const endDate = document.querySelector('input[name="end_date"]').value;
    
            if (!startDate || !endDate) {
                alert('Por favor, seleccione un rango de fechas antes de exportar.');
                return;
            }
    
            const form = document.querySelector('form');
            const formData = new FormData(form);
    
            const params = new URLSearchParams();
            for (const pair of formData) {
                params.append(pair[0], pair[1]);
            }
    
            const url = `<?= $this->Url->build(['controller' => 'VistaPacientesCampanas', 'action' => 'exportarExcel']) ?>?${params.toString()}`;
            window.location.href = url;
        });
    });    
</script>