<?php $this->assign('title', 'Reporte de Consultas y Procedimientos'); ?>

<?= $this->Form->create(null, ['type' => 'get', 'class' => 'mb-3']) ?>
<fieldset>
    <legend>Filtrar por Tipo, DNI y Fechas</legend>
    <div class="row">
        <div class="col-md-3">
            <?= $this->Form->control('start_date', [
                'type' => 'date',
                'label' => 'Fecha Inicio',
                'class' => 'form-control',
                'value' => $startDate ?? ''
            ]) ?>
        </div>
        <div class="col-md-3">
            <?= $this->Form->control('end_date', [
                'type' => 'date',
                'label' => 'Fecha Fin',
                'class' => 'form-control',
                'value' => $endDate ?? ''
            ]) ?>
        </div>
        <div class="col-md-3">
            <?= $this->Form->control('tipo', [
                'type' => 'select',
                'options' => ['consulta' => 'Consultas', 'procedimiento' => 'Procedimientos'],
                'empty' => 'Todos',
                'label' => 'Tipo de Registro',
                'class' => 'form-control',
                'value' => $tipo ?? ''
            ]) ?>
        </div>
        <div class="col-md-3">
            <?= $this->Form->control('dni', [
                'type' => 'text',
                'label' => 'DNI del Paciente',
                'class' => 'form-control',
                'value' => $dni ?? ''
            ]) ?>
        </div>
    </div>
</fieldset>
<?= $this->Form->button('Generar Reporte', ['class' => 'btn btn-primary mt-2']) ?>
<?= $this->Form->button('Exportar a Excel', ['class' => 'btn btn-success mt-2', 'id' => 'exportExcelBtn']) ?>
<?= $this->Form->end() ?>

<h3 class="mt-4">Previsualización del Reporte</h3>
<iframe id="previewIframe" style="width: 100%; height: 600px; border: 1px solid #ccc;"></iframe>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelector('form').addEventListener('submit', function (event) {
            event.preventDefault();

            const tipo = document.querySelector('select[name="tipo"]').value;
            const dni = document.querySelector('input[name="dni"]').value;
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

            const url = `<?= $this->Url->build(['controller' => 'VistaConsultasProcedimientos', 'action' => 'exportarPdf']) ?>?tipo=${tipo}&dni=${dni}&start_date=${startDate}&end_date=${endDate}`;
            document.getElementById('previewIframe').src = url;
        });

        document.getElementById('exportExcelBtn').addEventListener('click', function (event) {
            event.preventDefault();

            const tipo = document.querySelector('select[name="tipo"]').value;
            const dni = document.querySelector('input[name="dni"]').value;
            const startDate = document.querySelector('input[name="start_date"]').value;
            const endDate = document.querySelector('input[name="end_date"]').value;

            if (!startDate || !endDate) {
                alert('Por favor, seleccione un rango de fechas antes de exportar.');
                return;
            }

            const url = `<?= $this->Url->build(['controller' => 'VistaConsultasProcedimientos', 'action' => 'exportarExcel']) ?>?tipo=${tipo}&dni=${dni}&start_date=${startDate}&end_date=${endDate}`;
            window.location.href = url;
        });
    });
</script>
