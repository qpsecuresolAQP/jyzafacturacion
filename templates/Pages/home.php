<?php $this->assign('title', 'Resumen del panel'); ?>

<!-- Chart.js cargado directamente aquí para garantizar que esté disponible antes del script de gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid mt-4 px-4">

    <!-- ══════════════════════════════════════════
         SECCIÓN 2: Gráficos
    ══════════════════════════════════════════ -->
    <h6 class="text-muted fw-semibold mb-3 text-uppercase" style="letter-spacing:.08em; font-size:.72rem;">
        <i class="fas fa-chart-bar me-1"></i> Estadísticas
    </h6>
    <div class="row g-3">

        <!-- Gráfico 1: Tratamientos Más Realizados -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <i class="fas fa-stethoscope text-muted me-2"></i>
                    <strong>Tratamientos Más Realizados</strong>
                    <span class="text-muted ms-1" style="font-size:.8rem;">· Últimos 7 días</span>
                </div>
                <div class="card-body chart-container">
                    <canvas id="chartTratamientosUltimos7Dias"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico 2: Ingresos por Tratamiento -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <i class="fas fa-coins text-warning me-2"></i>
                    <strong>Ingresos por Tratamiento</strong>
                    <span class="text-muted ms-1" style="font-size:.8rem;">· Mes actual</span>
                </div>
                <div class="card-body chart-container">
                    <canvas id="chartIngresosPorTratamiento"></canvas>
                </div>
            </div>
        </div>

    </div><!-- /row gráficos -->

    <?php
    $seccionesTopVendidos = [
        'producto' => ['titulo' => 'Productos Más Vendidos', 'icono' => 'fa-box', 'unidad' => 'Unidades vendidas'],
        'tratamiento' => ['titulo' => 'Tratamientos Más Vendidos', 'icono' => 'fa-stethoscope', 'unidad' => 'Veces realizado'],
        'examen' => ['titulo' => 'Exámenes Más Vendidos', 'icono' => 'fa-vial', 'unidad' => 'Veces realizado'],
    ];
    $datosTopVendidosMesActual = [
        'producto' => $productosVendidosMesActual,
        'tratamiento' => $tratamientosVendidosMesActual,
        'examen' => $examenesVendidosMesActual,
    ];
    ?>

    <?php foreach ($seccionesTopVendidos as $tipo => $config): ?>
    <!-- ══════════════════════════════════════════
         <?= h($config['titulo']) ?> (comparativo)
    ══════════════════════════════════════════ -->
    <h6 class="text-muted fw-semibold mb-3 mt-4 text-uppercase" style="letter-spacing:.08em; font-size:.72rem;">
        <i class="fas <?= h($config['icono']) ?> me-1"></i> <?= h($config['titulo']) ?>
    </h6>
    <div class="row g-3">

        <!-- Izquierda: mes actual, fijo -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <i class="fas <?= h($config['icono']) ?> text-primary me-2"></i>
                    <strong>Mes Actual</strong>
                    <span class="text-muted ms-1" style="font-size:.8rem;" id="labelMesActual_<?= h($tipo) ?>"></span>
                </div>
                <div class="card-body chart-container">
                    <canvas id="chartMesActual_<?= h($tipo) ?>"></canvas>
                </div>
                <div class="table-responsive border-top">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr><th>Nombre</th><th class="text-end">Cantidad</th><th class="text-end">Ingresos (S/)</th></tr>
                        </thead>
                        <tbody id="tablaMesActual_<?= h($tipo) ?>"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Derecha: mes seleccionable, para comparar -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <i class="fas <?= h($config['icono']) ?> text-secondary me-2"></i>
                        <strong>Comparar con</strong>
                    </div>
                    <input type="month" id="selectorMesComparar_<?= h($tipo) ?>" class="form-control form-control-sm" style="width: auto;">
                </div>
                <div class="card-body chart-container">
                    <canvas id="chartMesComparar_<?= h($tipo) ?>"></canvas>
                </div>
                <div class="table-responsive border-top">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr><th>Nombre</th><th class="text-end">Cantidad</th><th class="text-end">Ingresos (S/)</th></tr>
                        </thead>
                        <tbody id="tablaMesComparar_<?= h($tipo) ?>"></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div><!-- /row <?= h($tipo) ?> -->
    <?php endforeach; ?>
</div><!-- /container -->

<style>
    .chart-container {
        position: relative;
        width: 100%;
        height: 320px;
    }

    .chart-container canvas {
        max-height: 320px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ── Datos desde PHP ─────────────────────────────────────────────────────────
        const tratamientosUltimos7Dias = <?= json_encode($tratamientosUltimos7Dias) ?>;
        const ingresosPorTratamiento = <?= json_encode($ingresosPorTratamiento) ?>;
        const mesActualEtiqueta = <?= json_encode($mesActualEtiqueta) ?>;
        const urlTopVendidosMes = <?= json_encode($this->Url->build(['controller' => 'Pages', 'action' => 'topVendidosMes'])) ?>;
        const topVendidosMesActual = <?= json_encode($datosTopVendidosMesActual) ?>;

        // ── Paleta de colores ───────────────────────────────────────────────────────
        const coloresFijos = [
            'rgba(54, 162, 235, 0.7)', // Azul
            'rgba(255, 99, 132, 0.7)', // Rojo
            'rgba(255, 206, 86, 0.7)', // Amarillo
            'rgba(75, 192, 192, 0.7)', // Verde agua
            'rgba(153, 102, 255, 0.7)', // Morado
            'rgba(255, 159, 64, 0.7)', // Naranja
            'rgba(201, 203, 207, 0.7)', // Gris claro
            'rgba(0, 200, 83, 0.7)', // Verde
            'rgba(0, 123, 255, 0.7)', // Azul oscuro
            'rgba(220, 53, 69, 0.7)', // Rojo intenso
        ];

        // Helper: asigna colores según posición del array
        const assignColors = (arr) => arr.map((_, i) => coloresFijos[i % coloresFijos.length]);

        // Helper: opciones base reutilizables para gráficos de barras.
        // Nota: Chart.js con datasets muy cortos (2-3 barras, valores
        // enteros pequeños) puede ignorar min/beginAtZero si el máximo del
        // dataset no deja margen para un "paso" (step) que llegue hasta 0.
        // Forzamos max explícito (mayor al dato más alto) para garantizar
        // que el eje siempre arranque en 0 y las barras muestren su altura
        // real proporcional.
        const defaultBarOptions = (maxDato) => {
            const maxNum = Math.max(1, Number(maxDato) || 0);
            // Redondeamos el techo hacia arriba a un múltiplo "bonito" y
            // fijamos también el stepSize, para que Chart.js no pueda
            // recalcular por su cuenta un rango distinto al que pedimos.
            const pasoBase = Math.max(1, Math.ceil(maxNum / 5));
            const maxSeguro = pasoBase * 5;
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        min: 0,
                        max: maxSeguro,
                        ticks: {
                            precision: 0,
                            stepSize: pasoBase
                        }
                    }
                }
            };
        };

        // Helper: máximo de un array de items (por su .total), o 0 si vacío
        const maxDeItems = (items) => items.reduce((max, i) => Math.max(max, Number(i.total) || 0), 0);

        // ── Gráfico 1: Tratamientos Más Realizados (Últimos 7 días) ────────────────
        new Chart(document.getElementById('chartTratamientosUltimos7Dias'), {
            type: 'bar',
            data: {
                labels: tratamientosUltimos7Dias.map(i => i.nombre ?? 'Desconocido'),
                datasets: [{
                    label: 'Cantidad',
                    data: tratamientosUltimos7Dias.map(i => Number(i.total)),
                    backgroundColor: assignColors(tratamientosUltimos7Dias)
                }]
            },
            options: defaultBarOptions(maxDeItems(tratamientosUltimos7Dias))
        });

        // ── Gráfico 2: Ingresos por Tratamiento (Mes actual) ────────────────────────
        new Chart(document.getElementById('chartIngresosPorTratamiento'), {
            type: 'bar',
            data: {
                labels: ingresosPorTratamiento.map(i => i.nombre ?? 'Desconocido'),
                datasets: [{
                    label: 'Ingresos (S/)',
                    data: ingresosPorTratamiento.map(i => Number(i.total)),
                    backgroundColor: assignColors(ingresosPorTratamiento)
                }]
            },
            options: defaultBarOptions(maxDeItems(ingresosPorTratamiento))
        });

        // ── Gráficos: Más Vendidos por tipo (comparativo) ───────────────────────────
        function formatearMes(mesYm) {
            if (!mesYm) return '';
            const [anio, mes] = mesYm.split('-');
            const fecha = new Date(Number(anio), Number(mes) - 1, 1);
            return fecha.toLocaleDateString('es-PE', { month: 'long', year: 'numeric' });
        }

        function renderTopVendidosChart(canvasId, chartRef, items) {
            if (chartRef.instance) {
                chartRef.instance.destroy();
            }

            chartRef.instance = new Chart(document.getElementById(canvasId), {
                type: 'bar',
                data: {
                    labels: items.map(i => i.nombre ?? 'Desconocido'),
                    datasets: [{
                        label: 'Cantidad',
                        data: items.map(i => Number(i.total)),
                        backgroundColor: assignColors(items)
                    }]
                },
                options: defaultBarOptions(maxDeItems(items))
            });
        }

        // Escapa texto antes de insertarlo como HTML (evita XSS con nombres de la BD)
        const escapeHtml = (str) => String(str).replace(/[&<>"']/g, (c) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));

        // Llena la tabla de Nombre / Cantidad / Ingresos (S/) debajo de cada gráfico
        function renderTablaVendidos(tbodyId, items) {
            const tbody = document.getElementById(tbodyId);
            if (!tbody) {
                return;
            }
            if (!items.length) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-2">Sin datos</td></tr>';
                return;
            }
            tbody.innerHTML = items.map(i => {
                const cantidad = Number(i.total) || 0;
                const ingresos = Number(i.ingresos ?? 0);
                return '<tr>'
                    + '<td>' + escapeHtml(i.nombre ?? 'Desconocido') + '</td>'
                    + '<td class="text-end">' + cantidad + '</td>'
                    + '<td class="text-end">S/ ' + ingresos.toFixed(2) + '</td>'
                    + '</tr>';
            }).join('');
        }

        ['producto', 'tratamiento', 'examen'].forEach(function (tipo) {
            // Gráfico izquierdo: mes actual, fijo
            const labelMesActualEl = document.getElementById('labelMesActual_' + tipo);
            if (labelMesActualEl) {
                labelMesActualEl.textContent = '· ' + formatearMes(mesActualEtiqueta);
            }
            const chartMesActualRef = {};
            const itemsMesActual = topVendidosMesActual[tipo] ?? [];
            renderTopVendidosChart('chartMesActual_' + tipo, chartMesActualRef, itemsMesActual);
            renderTablaVendidos('tablaMesActual_' + tipo, itemsMesActual);

            // Gráfico derecho: mes seleccionable, para comparar
            const selectorMes = document.getElementById('selectorMesComparar_' + tipo);
            if (!selectorMes) {
                return;
            }
            const chartCompararRef = {};

            function cargarMesComparar(mesYm) {
                fetch(urlTopVendidosMes + '?tipo=' + encodeURIComponent(tipo) + '&mes=' + encodeURIComponent(mesYm), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(res => res.json())
                    .then(data => {
                        const items = (data.ok && Array.isArray(data.items)) ? data.items : [];
                        renderTopVendidosChart('chartMesComparar_' + tipo, chartCompararRef, items);
                        renderTablaVendidos('tablaMesComparar_' + tipo, items);
                    })
                    .catch(() => {
                        renderTopVendidosChart('chartMesComparar_' + tipo, chartCompararRef, []);
                        renderTablaVendidos('tablaMesComparar_' + tipo, []);
                    });
            }

            // Por defecto, comparar con el mes anterior al actual
            const [anioActual, mesActualNum] = mesActualEtiqueta.split('-').map(Number);
            const fechaMesAnterior = new Date(anioActual, mesActualNum - 2, 1);
            const mesAnteriorYm = fechaMesAnterior.getFullYear() + '-' + String(fechaMesAnterior.getMonth() + 1).padStart(2, '0');

            selectorMes.value = mesAnteriorYm;
            selectorMes.max = mesActualEtiqueta;
            cargarMesComparar(mesAnteriorYm);

            selectorMes.addEventListener('change', function () {
                if (this.value) {
                    cargarMesComparar(this.value);
                }
            });
        });

    }); // fin DOMContentLoaded
</script>
