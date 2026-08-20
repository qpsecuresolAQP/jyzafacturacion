<?php $this->assign('title', 'Resumen del panel'); ?>

<!-- Chart.js cargado directamente aquí para garantizar que esté disponible antes del script de gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid mt-4 px-4">

    <!-- ══════════════════════════════════════════
         SECCIÓN 1: Tarjetas de información
    ══════════════════════════════════════════ -->
    <h6 class="text-muted fw-semibold mb-3 text-uppercase" style="letter-spacing:.08em; font-size:.72rem;">
        <i class="fas fa-th-large me-1"></i> Información del Día
    </h6>
    <div class="row g-3 mb-4">

        <!-- Cumpleaños Hoy -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <i class="fas fa-birthday-cake text-danger me-2"></i>
                    <strong>Cumpleaños Hoy</strong>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($cumpleañosHoy)): ?>
                        <div class="list-group list-group-flush" style="max-height:280px; overflow-y:auto;">
                            <?php foreach ($cumpleañosHoy as $historia): ?>
                                <div class="list-group-item px-3 py-2">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <div>
                                            <div class="fw-semibold" style="font-size:.88rem;">
                                                <i class="fas fa-user text-success fa-xs"></i>
                                                <?= h($historia->paciente->nombre . ' ' . $historia->paciente->apellido) ?>
                                            </div>
                                            <div class="text-muted" style="font-size:.78rem;">
                                                <?= $historia->paciente->telefono_celular
                                                    ? h($historia->paciente->telefono_celular)
                                                    : 'Sin teléfono registrado' ?>
                                            </div>
                                        </div>
                                        <?php if ($historia->paciente->telefono_celular): ?>
                                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $historia->paciente->telefono_celular) ?>?text=Hola%20desde%20SpazioDentale%20les%20deseamos%20un%20feliz%20y%20bendecido%20cumplea%C3%B1os%20en%20uni%C3%B3n%20familiar%20%F0%9F%8E%82%20Recordarle%20que%20tenemos%20promociones%20para%20usted%20por%20este%20d%C3%ADa%20%E2%9D%A4%EF%B8%8F"
                                                target="_blank"
                                                class="btn btn-sm btn-success flex-shrink-0"
                                                title="Enviar felicitación por WhatsApp">
                                                <i class="fab fa-whatsapp"></i> WhatsApp
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-birthday-cake text-muted" style="font-size:2rem;"></i>
                            <p class="text-muted mt-2 mb-0">No hay cumpleaños hoy</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div><!-- /row tarjetas info -->
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

        // Helper: opciones base reutilizables para gráficos de barras
        const defaultBarOptions = () => ({
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        });

        // ── Gráfico 1: Tratamientos Más Realizados (Últimos 7 días) ────────────────
        new Chart(document.getElementById('chartTratamientosUltimos7Dias'), {
            type: 'bar',
            data: {
                labels: tratamientosUltimos7Dias.map(i => i.nombre ?? 'Desconocido'),
                datasets: [{
                    label: 'Cantidad',
                    data: tratamientosUltimos7Dias.map(i => i.total),
                    backgroundColor: assignColors(tratamientosUltimos7Dias)
                }]
            },
            options: defaultBarOptions()
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
            options: defaultBarOptions()
        });

    }); // fin DOMContentLoaded
</script>
