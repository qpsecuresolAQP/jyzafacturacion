<div class="card shadow mb-4">
    <div class="card-header bg-info">
        <h3 class="card-title mb-0">Historial de Exámenes Físicos</h3>
    </div>
    <div class="card-body table-responsive">
        <table class="table  table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>Edad</th>
                    <th>Peso (kg)</th>
                    <th>Altura (cm)</th>
                    <th>Temperatura (°C)</th>
                    <th>Presión Arterial</th>
                    <th>Frecuencia Cardíaca (bpm)</th>
                    <th>Saturación (%)</th>
                    <th>Glicemia (mg/dL)</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fisicosHistorias as $fisicoHistoria): ?>
                <tr>
                    <td><?= h($fisicoHistoria->examen->edad ?? '-') ?></td>
                    <td><?= h($fisicoHistoria->examen->peso ?? '-') ?></td>
                    <td><?= h($fisicoHistoria->examen->altura ?? '-') ?></td>
                    <td><?= h($fisicoHistoria->examen->temperatura ?? '-') ?></td>
                    <td><?= h($fisicoHistoria->examen->presion ?? '-') ?></td>
                    <td><?= h($fisicoHistoria->examen->frecuencia_cardiaca ?? '-') ?></td>
                    <td><?= h($fisicoHistoria->examen->saturacion ?? '-') ?></td>
                    <td><?= h($fisicoHistoria->examen->glicemina ?? '-') ?></td>
                    <td><?= h($fisicoHistoria->created->format('d-m-Y H:i:s')) ?></td>            
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
