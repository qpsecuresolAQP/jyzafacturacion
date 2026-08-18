<?php $this->assign('title', 'Resumen del panel de Citas'); ?>

<!-- Chart.js cargado directamente aquí para garantizar que esté disponible antes del script de gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid mt-4 px-4">

    <!-- ══════════════════════════════════════════
         SECCIÓN 1: Tarjetas de información (2×2)
    ══════════════════════════════════════════ -->
    <h6 class="text-muted fw-semibold mb-3 text-uppercase" style="letter-spacing:.08em; font-size:.72rem;">
        <i class="fas fa-th-large me-1"></i> Información del Día
    </h6>
    <div class="row g-3 mb-4">

        <!-- Citas del Día -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <i class="fas fa-calendar-check text-primary me-2"></i>
                    <strong>Citas del Día</strong>
                </div>
                <div class="card-body">
                    <?php if (!empty($citasDelDia)): ?>
                        <div class="text-center py-2">
                            <div class="display-4 text-primary fw-bold mb-1">
                                <?= count($citasDelDia) ?>
                            </div>
                            <p class="text-muted mb-2">
                                <?= count($citasDelDia) === 1 ? 'Cita programada' : 'Citas programadas' ?>
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Próxima a las <?= reset($citasDelDia)->fecha_hora->format('H:i') ?>
                            </small>
                        </div>
                        <hr class="my-3">
                        <ul class="list-unstyled mb-0" style="font-size:.85rem;">
                            <?php foreach (array_slice($citasDelDia, 0, 3) as $c): ?>
                                <li class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-primary-subtle text-primary" style="min-width:48px;">
                                        <?= $c->fecha_hora->format('H:i') ?>
                                    </span>
                                    <span class="text-truncate">
                                        <?= h(($c->paciente->nombre ?? '') . ' ' . ($c->paciente->apellido ?? '')) ?>
                                    </span>
                                    <?php if (!empty($c->doctor->nombre)): ?>
                                        <span class="text-muted ms-auto text-nowrap">
                                            <i class="fas fa-user-md fa-xs"></i> <?= h($c->doctor->nombre) ?>
                                        </span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                            <?php if (count($citasDelDia) > 3): ?>
                                <li class="text-muted text-center" style="font-size:.78rem;">
                                    + <?= count($citasDelDia) - 3 ?> más
                                </li>
                            <?php endif; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-check text-muted" style="font-size:2rem;"></i>
                            <p class="text-muted mt-2 mb-0">No hay citas programadas</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Pacientes para Programar -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <i class="fas fa-calendar-alt text-warning me-2"></i>
                    <strong>Pacientes para Programar</strong>
                    <span class="badge bg-warning text-dark ms-2" style="font-size:.72rem;">Últimas 15</span>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($citasPorProgramar)): ?>
                        <div class="list-group list-group-flush" style="max-height:340px; overflow-y:auto;">
                            <?php foreach ($citasPorProgramar as $cita): ?>
                                <div class="list-group-item px-3 py-2">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="fw-semibold text-truncate" style="font-size:.88rem;">
                                                <i class="fas fa-user fa-xs" style="color:#ff6b6b;"></i>
                                                <?= h(($cita->paciente->nombre ?? '') . ' ' . ($cita->paciente->apellido ?? '')) ?>
                                            </div>
                                            <div class="text-muted" style="font-size:.78rem;">
                                                <?= $cita->paciente->telefono_celular ? h($cita->paciente->telefono_celular) : 'Sin teléfono' ?>
                                                &nbsp;·&nbsp;
                                                <?= $cita->fecha_hora->format('d/m/Y H:i') ?>
                                            </div>
                                            <?php if ($cita->motivo): ?>
                                                <div class="text-secondary text-truncate" style="font-size:.78rem;">
                                                    <?= h($cita->motivo) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($cita->paciente->telefono_celular): ?>
                                            <a href="https://wa.me/51<?= preg_replace('/[^0-9]/', '', $cita->paciente->telefono_celular) ?>?text=Hola%20te%20contactamos%20para%20programar%20tu%20cita%20m%C3%A9dica.%20Por%20favor%20confirma%20tu%20disponibilidad."
                                                target="_blank"
                                                class="btn btn-sm btn-success flex-shrink-0"
                                                title="Programar por WhatsApp">
                                                <i class="fab fa-whatsapp"></i> Programar
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center py-2 border-top">
                            <small class="text-muted">Mostrando últimas 15 citas</small>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-check text-muted" style="font-size:2rem;"></i>
                            <p class="text-muted mt-2 mb-0">No hay pacientes por programar</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

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

        <!-- Recordatorios -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <span>
                        <i class="fas fa-bell text-info me-2"></i>
                        <strong>Recordatorios</strong>
                    </span>
                    <!-- <?= $this->Html->link('Ver todos', ['controller' => 'RecordatorioControles', 'action' => 'index'], ['class' => 'btn btn-sm btn-outline-primary']) ?> -->
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recordatoriosPendientesEn5Dias)): ?>
                        <div class="list-group list-group-flush" style="max-height:340px; overflow-y:auto;">
                            <?php foreach ($recordatoriosPendientesEn5Dias as $r): ?>
                                <div class="list-group-item px-3 py-2">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="fw-semibold text-truncate" style="font-size:.88rem;">
                                                <i class="fas fa-bell fa-xs text-info"></i>
                                                <?= h(trim(($r['paciente_nombre'] ?? $r['nombre_paciente'] ?? '') . ' ' . ($r['paciente_apellido'] ?? $r['apellido_paciente'] ?? ''))) ?>
                                            </div>
                                            <div class="text-muted" style="font-size:.78rem;">
                                                <?= !empty($r['proximo_control']) ? h($r['proximo_control']) : 'Sin fecha' ?>
                                                &nbsp;·&nbsp;
                                                <?= h(mb_strimwidth($r['recordatorio_titulo'] ?? ($r['descripcion'] ?? ''), 0, 60, '...')) ?>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1 flex-shrink-0">
                                            <?php if (!empty($r['paciente_id'])): ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-user"></i>',
                                                    ['controller' => 'Pacientes', 'action' => 'view', $r['paciente_id']],
                                                    ['class' => 'btn btn-sm btn-outline-primary', 'escape' => false, 'title' => 'Ir al paciente']
                                                ) ?>
                                            <?php endif; ?>
                                            <?php if (!empty($r['paciente_telefono'])): ?>
                                                <a href="https://wa.me/51<?= preg_replace('/[^0-9]/', '', $r['paciente_telefono']) ?>?text=<?= rawurlencode('Hola, le recordamos su próximo control.') ?>"
                                                    target="_blank"
                                                    class="btn btn-sm btn-success"
                                                    title="WhatsApp">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?= $this->Html->link(
                                                '<i class="fas fa-eye"></i>',
                                                ['controller' => 'RecordatorioControles', 'action' => 'view', $r['id']],
                                                ['class' => 'btn btn-sm btn-outline-info openModal', 'escape' => false, 'title' => 'Ver control']
                                            ) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center py-2 border-top">
                            <small class="text-muted">Mostrando recordatorios de controles</small>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="fas fa-bell text-muted" style="font-size:1.8rem;"></i>
                            <p class="text-muted mt-2 mb-0">No hay recordatorios</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Próximos 7 días -->
        <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-bell text-danger me-2"></i>
                            <strong>Pagos Próximos (7 días)</strong>
                        </span>
                    </div>

                    <div class="card-body p-0">
                        <?php
                        $totalProximos =
                            count($recordatoriosPagoProximos['contado'] ?? []) +
                            count($recordatoriosPagoProximos['partes'] ?? []);
                        ?>

                        <?php if ($totalProximos > 0): ?>
                            <?php
                            // ── Agrupar contado y cuotas por paciente ──────────────────────
                            $proxGrupoPaciente = [];
                            foreach ($recordatoriosPagoProximos['contado'] ?? [] as $paquete) {
                                $pid  = $paquete->historias_clinica->paciente->id;
                                $nom  = $paquete->historias_clinica->paciente->nombre . ' ' . $paquete->historias_clinica->paciente->apellido;
                                $tel  = $paquete->historias_clinica->paciente->telefono_celular ?? '';
                                if (!isset($proxGrupoPaciente[$pid])) {
                                    $proxGrupoPaciente[$pid] = ['nombre' => $nom, 'telefono' => $tel, 'items' => []];
                                }
                                $proxGrupoPaciente[$pid]['items'][] = [
                                    'tipo'   => 'contado',
                                    'label'  => $paquete->nombre,
                                    'monto'  => $paquete->precio_total,
                                    'fecha'  => $paquete->fecha_recordatorio?->format('d-m-Y'),
                                    'estado' => $paquete->estado === 'pendiente' ? 'Pendiente' : 'Parcial',
                                    'badge'  => $paquete->estado === 'pendiente' ? 'warning' : 'info',
                                    'view_id'=> $paquete->id,
                                    'ctrl'   => 'PaquetesPagos',
                                    'sub'    => null,
                                ];
                            }
                            foreach ($recordatoriosPagoProximos['partes'] ?? [] as $cuota) {
                                $pid  = $cuota->paquetes_pago->historias_clinica->paciente->id;
                                $nom  = $cuota->paquetes_pago->historias_clinica->paciente->nombre . ' ' . $cuota->paquetes_pago->historias_clinica->paciente->apellido;
                                $tel  = $cuota->paquetes_pago->historias_clinica->paciente->telefono_celular ?? '';
                                if (!isset($proxGrupoPaciente[$pid])) {
                                    $proxGrupoPaciente[$pid] = ['nombre' => $nom, 'telefono' => $tel, 'items' => []];
                                }
                                $proxGrupoPaciente[$pid]['items'][] = [
                                    'tipo'   => 'cuota',
                                    'label'  => $cuota->paquetes_pago->nombre,
                                    'monto'  => $cuota->monto,
                                    'fecha'  => $cuota->fecha_recordatorio?->format('d-m-Y'),
                                    'estado' => 'Pendiente',
                                    'badge'  => 'warning text-dark',
                                    'view_id'=> $cuota->paquetes_pago_id,
                                    'ctrl'   => 'PaquetesPagos',
                                    'sub'    => $cuota->titulo_referencial,
                                ];
                            }
                            ?>
                            <div class="list-group list-group-flush" style="max-height:340px; overflow-y:auto;">
                                <?php foreach ($proxGrupoPaciente as $pid => $grupo): ?>
                                    <?php
                                    // Construir mensaje WhatsApp con todas las cuotas del paciente
                                    $msgLineas = ['Estimado/a ' . $grupo['nombre'] . ', le recordamos desde *SpazioDentale* los siguientes pagos pendientes:'];
                                    $totalMonto = 0;
                                    foreach ($grupo['items'] as $item) {
                                        $tipoLabel = $item['tipo'] === 'contado' ? 'Contado' : 'Cuota';
                                        $detalle   = $item['sub'] ? ' (' . $item['sub'] . ')' : '';
                                        $msgLineas[] = '• [' . $tipoLabel . '] ' . $item['label'] . $detalle . ' - S/ ' . number_format($item['monto'], 2) . ' · ' . ($item['fecha'] ?? 'Sin fecha');
                                        $totalMonto += $item['monto'];
                                    }
                                    $msgLineas[] = '';
                                    $msgLineas[] = 'Total pendiente: *S/ ' . number_format($totalMonto, 2) . '*';
                                    $msgLineas[] = 'Por favor, comuníquese con nosotros para coordinar su pago. ¡Gracias!';
                                    $waMsg = rawurlencode(implode("\n", $msgLineas));
                                    $waTel = preg_replace('/[^0-9]/', '', $grupo['telefono']);
                                    ?>
                                    <div class="list-group-item px-3 py-2">
                                        <div class="d-flex align-items-start justify-content-between gap-2">
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="fw-semibold text-truncate mb-1" style="font-size:.88rem;">
                                                    <i class="fas fa-user fa-xs text-danger"></i>
                                                    <?= h($grupo['nombre']) ?>
                                                    <?php if ($grupo['telefono']): ?>
                                                        <span class="text-muted fw-normal ms-1" style="font-size:.76rem;"><?= h($grupo['telefono']) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php foreach ($grupo['items'] as $item): ?>
                                                    <div class="d-flex align-items-center gap-1 mb-1" style="font-size:.78rem;">
                                                        <span class="badge bg-<?= $item['tipo'] === 'contado' ? 'success-subtle text-success' : 'info-subtle text-info' ?>"><?= $item['tipo'] === 'contado' ? 'Contado' : 'Cuota' ?></span>
                                                        <span class="text-truncate text-muted"><?= h($item['label']) ?><?= $item['sub'] ? ' · ' . h($item['sub']) : '' ?></span>
                                                        <span class="ms-auto text-nowrap fw-semibold"><?= $this->Number->currency($item['monto']) ?></span>
                                                        <span class="badge bg-<?= $item['badge'] ?>"><?= $item['estado'] ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                                <?php if (count($grupo['items']) > 1): ?>
                                                    <div style="font-size:.76rem;" class="text-end text-muted mt-1">
                                                        Total: <strong><?= $this->Number->currency($totalMonto) ?></strong>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="d-flex flex-column gap-1 flex-shrink-0">
                                                <?php if ($grupo['telefono']): ?>
                                                    <a href="https://wa.me/51<?= $waTel ?>?text=<?= $waMsg ?>"
                                                        target="_blank"
                                                        class="btn btn-sm btn-success"
                                                        title="Recordatorio de pago por WhatsApp">
                                                        <i class="fab fa-whatsapp"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php if (!empty($pid)): ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-user"></i>',
                                                    ['controller' => 'Pacientes', 'action' => 'view', $pid, '#' => 'recordatorios-pago'],
                                                    ['class' => 'btn btn-sm btn-outline-primary', 'escape' => false, 'title' => 'Ir al paciente']
                                                ) ?>
                                            <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="text-center py-2 border-top">
                                <small class="text-muted">
                                    <?= count($recordatoriosPagoProximos['contado'] ?? []) ?> contado(s) +
                                    <?= count($recordatoriosPagoProximos['partes'] ?? []) ?> cuota(s)
                                </small>
                            </div>

                        <?php else: ?>
                            <div class="text-center py-3">
                                <i class="fas fa-check-circle text-success" style="font-size:1.8rem;"></i>
                                <p class="text-muted mt-2 mb-0">No hay pagos próximos</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Todos los pendientes -->
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-credit-card text-warning me-2"></i>
                            <strong>Todos los Pagos Pendientes</strong>
                        </span>
                    </div>

                    <div class="card-body p-0">
                        <?php
                        $totalPendientes =
                            count($recordatoriosPagoTodos['contado'] ?? []) +
                            count($recordatoriosPagoTodos['partes'] ?? []);
                        ?>

                        <?php if ($totalPendientes > 0): ?>
                            <?php
                            // ── Agrupar contado y cuotas por paciente ──────────────────────
                            $todosGrupoPaciente = [];
                            foreach ($recordatoriosPagoTodos['contado'] ?? [] as $paquete) {
                                $pid  = $paquete->historias_clinica->paciente->id;
                                $nom  = $paquete->historias_clinica->paciente->nombre . ' ' . $paquete->historias_clinica->paciente->apellido;
                                $tel  = $paquete->historias_clinica->paciente->telefono_celular ?? '';
                                if (!isset($todosGrupoPaciente[$pid])) {
                                    $todosGrupoPaciente[$pid] = ['nombre' => $nom, 'telefono' => $tel, 'items' => []];
                                }
                                $todosGrupoPaciente[$pid]['items'][] = [
                                    'tipo'   => 'contado',
                                    'label'  => $paquete->nombre,
                                    'monto'  => $paquete->precio_total,
                                    'fecha'  => $paquete->fecha_recordatorio?->format('d-m-Y'),
                                    'estado' => $paquete->estado === 'pendiente' ? 'Pendiente' : 'Parcial',
                                    'badge'  => $paquete->estado === 'pendiente' ? 'warning' : 'info',
                                    'view_id'=> $paquete->id,
                                    'ctrl'   => 'PaquetesPagos',
                                    'sub'    => null,
                                ];
                            }
                            foreach ($recordatoriosPagoTodos['partes'] ?? [] as $cuota) {
                                $pid  = $cuota->paquetes_pago->historias_clinica->paciente->id;
                                $nom  = $cuota->paquetes_pago->historias_clinica->paciente->nombre . ' ' . $cuota->paquetes_pago->historias_clinica->paciente->apellido;
                                $tel  = $cuota->paquetes_pago->historias_clinica->paciente->telefono_celular ?? '';
                                if (!isset($todosGrupoPaciente[$pid])) {
                                    $todosGrupoPaciente[$pid] = ['nombre' => $nom, 'telefono' => $tel, 'items' => []];
                                }
                                $todosGrupoPaciente[$pid]['items'][] = [
                                    'tipo'   => 'cuota',
                                    'label'  => $cuota->paquetes_pago->nombre,
                                    'monto'  => $cuota->monto,
                                    'fecha'  => $cuota->fecha_recordatorio?->format('d-m-Y'),
                                    'estado' => 'Pendiente',
                                    'badge'  => 'warning text-dark',
                                    'view_id'=> $cuota->paquetes_pago_id,
                                    'ctrl'   => 'PaquetesPagos',
                                    'sub'    => $cuota->titulo_referencial,
                                ];
                            }
                            ?>
                            <div class="list-group list-group-flush" style="max-height:340px; overflow-y:auto;">
                                <?php foreach ($todosGrupoPaciente as $pid => $grupo): ?>
                                    <?php
                                    // id del paciente
                                    $pacienteId = $pid;
                                    // Construir mensaje WhatsApp con todas las cuotas del paciente
                                    $msgLineas = ['Estimado/a ' . $grupo['nombre'] . ', le recordamos desde *SpazioDentale* los siguientes pagos pendientes:'];
                                    $totalMonto = 0;
                                    foreach ($grupo['items'] as $item) {
                                        $tipoLabel = $item['tipo'] === 'contado' ? 'Contado' : 'Cuota';
                                        $detalle   = $item['sub'] ? ' (' . $item['sub'] . ')' : '';
                                        $msgLineas[] = '• [' . $tipoLabel . '] ' . $item['label'] . $detalle . ' - S/ ' . number_format($item['monto'], 2) . ' · ' . ($item['fecha'] ?? 'Sin fecha');
                                        $totalMonto += $item['monto'];
                                    }
                                    $msgLineas[] = '';
                                    $msgLineas[] = 'Total pendiente: *S/ ' . number_format($totalMonto, 2) . '*';
                                    $msgLineas[] = 'Por favor, comuníquese con nosotros para coordinar su pago. ¡Gracias!';
                                    $waMsg = rawurlencode(implode("\n", $msgLineas));
                                    $waTel = preg_replace('/[^0-9]/', '', $grupo['telefono']);
                                    ?>
                                    <div class="list-group-item px-3 py-2">
                                        <div class="d-flex align-items-start justify-content-between gap-2">
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="fw-semibold text-truncate mb-1" style="font-size:.88rem;">
                                                    <i class="fas fa-user fa-xs text-danger"></i>
                                                    <?= h($grupo['nombre']) ?>
                                                    <?php if ($grupo['telefono']): ?>
                                                        <span class="text-muted fw-normal ms-1" style="font-size:.76rem;"><?= h($grupo['telefono']) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php foreach ($grupo['items'] as $item): ?>
                                                    <div class="d-flex align-items-center gap-1 mb-1" style="font-size:.78rem;">
                                                        <span class="badge bg-<?= $item['tipo'] === 'contado' ? 'success-subtle text-success' : 'info-subtle text-info' ?>"><?= $item['tipo'] === 'contado' ? 'Contado' : 'Cuota' ?></span>
                                                        <span class="text-truncate text-muted"><?= h($item['label']) ?><?= $item['sub'] ? ' · ' . h($item['sub']) : '' ?></span>
                                                        <span class="ms-auto text-nowrap fw-semibold"><?= $this->Number->currency($item['monto']) ?></span>
                                                        <span class="badge bg-<?= $item['badge'] ?>"><?= $item['estado'] ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                                <?php if (count($grupo['items']) > 1): ?>
                                                    <div style="font-size:.76rem;" class="text-end text-muted mt-1">
                                                        Total: <strong><?= $this->Number->currency($totalMonto) ?></strong>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="d-flex flex-column gap-1 flex-shrink-0">
                                                <?php if ($grupo['telefono']): ?>
                                                    <a href="https://wa.me/51<?= $waTel ?>?text=<?= $waMsg ?>"
                                                        target="_blank"
                                                        class="btn btn-sm btn-success"
                                                        title="Recordatorio de pago por WhatsApp">
                                                        <i class="fab fa-whatsapp"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php if (!empty($pid)): ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-user"></i>',
                                                    ['controller' => 'Pacientes', 'action' => 'view', $pid, '#' => 'recordatorios-pago'],
                                                    ['class' => 'btn btn-sm btn-outline-primary', 'escape' => false, 'title' => 'Ir al paciente']
                                                ) ?>
                                            <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="text-center py-2 border-top">
                                <small class="text-muted">
                                    <?= count($recordatoriosPagoTodos['contado'] ?? []) ?> contado(s) +
                                    <?= count($recordatoriosPagoTodos['partes'] ?? []) ?> cuota(s)
                                </small>
                            </div>

                        <?php else: ?>
                            <div class="text-center py-3">
                                <i class="fas fa-credit-card text-muted" style="font-size:1.8rem;"></i>
                                <p class="text-muted mt-2 mb-0">No hay pagos pendientes</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
        </div>

    </div><!-- /row tarjetas info -->
    <!-- ══════════════════════════════════════════
         SECCIÓN 2: Gráficos (2 por fila)
    ══════════════════════════════════════════ -->
    <h6 class="text-muted fw-semibold mb-3 text-uppercase" style="letter-spacing:.08em; font-size:.72rem;">
        <i class="fas fa-chart-bar me-1"></i> Estadísticas
    </h6>
    <div class="row g-3">

        <!-- Gráfico 1: Citas por Usuario Hoy -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <i class="fas fa-chart-bar text-primary me-2"></i>
                    <strong>Citas por Usuario</strong>
                    <span class="text-muted ms-1" style="font-size:.8rem;">· Hoy</span>
                </div>
                <div class="card-body chart-container">
                    <canvas id="chartCitasPorUsuarioHoy"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico 2: Citas por Estado Hoy -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <i class="fas fa-chart-pie text-warning me-2"></i>
                    <strong>Citas por Estado</strong>
                    <span class="text-muted ms-1" style="font-size:.8rem;">· Hoy (sin campañas)</span>
                </div>
                <div class="card-body chart-container">
                    <canvas id="chartCitasPorEstadoHoy"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico 3: Citas Finalizadas por Usuario Mensual -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <i class="fas fa-chart-line text-success me-2"></i>
                    <strong>Citas Finalizadas por Usuario</strong>
                    <span class="text-muted ms-1" style="font-size:.8rem;">· Mes actual (sin campañas)</span>
                </div>
                <div class="card-body chart-container">
                    <canvas id="chartFinalizadasMensual"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico 4: Pacientes Nuevos vs Frecuentes -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <i class="fas fa-user-friends text-info me-2"></i>
                    <strong>Pacientes Nuevos vs Frecuentes</strong>
                    <span class="text-muted ms-1" style="font-size:.8rem;">· Mes actual</span>
                </div>
                <div class="card-body chart-container">
                    <canvas id="chartPacientesNuevosFrecuentes"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico 5: Tiempo Promedio Llegada → Inicio -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <i class="fas fa-stopwatch text-danger me-2"></i>
                    <strong>Tiempo Promedio: Llegada → Inicio</strong>
                    <span class="text-muted ms-1" style="font-size:.8rem;">· Mes actual</span>
                </div>
                <div class="card-body chart-container">
                    <canvas id="chartPromedioLlegadaInicio"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico 6: Citas Finalizadas por Doctor -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <i class="fas fa-user-md text-secondary me-2"></i>
                    <strong>Citas Finalizadas por Doctor</strong>
                    <span class="text-muted ms-1" style="font-size:.8rem;">· Mes actual</span>
                </div>
                <div class="card-body chart-container">
                    <canvas id="chartCitasFinalizadasPorDoctor"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico 7: Tratamientos Más Realizados -->
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

        <!-- Gráfico 8: Ingresos por Tratamiento -->
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
        const citasPorUsuarioHoy = <?= json_encode($citasPorUsuarioHoy) ?>;
        const citasPorEstadoHoy = <?= json_encode($citasPorEstadoHoy) ?>;
        const citasFinalizadasMensual = <?= json_encode($citasFinalizadasMensual) ?>;
        const citasFinalizadasPorDoctor = <?= json_encode($citasFinalizadasPorDoctor) ?>;
        const pacientesFrecuentesCount = <?= (int)$pacientesFrecuentesCount ?>;
        const pacientesNuevos = <?= (int)$pacientesNuevos ?>;
        const promedioMinutos = <?= (float)$promedioMinutos ?>;

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
            'rgba(255, 193, 7, 0.7)', // Amarillo dorado
            'rgba(40, 167, 69, 0.7)', // Verde oscuro
            'rgba(108, 117, 125, 0.7)', // Gris medio
            'rgba(102, 16, 242, 0.7)', // Violeta fuerte
            'rgba(232, 62, 140, 0.7)', // Rosa fuerte
            'rgba(13, 110, 253, 0.7)' // Azul brillante
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

        // ── Gráfico 1: Citas por Usuario (Hoy) ─────────────────────────────────────
        new Chart(document.getElementById('chartCitasPorUsuarioHoy'), {
            type: 'bar',
            data: {
                labels: citasPorUsuarioHoy.map(i => i.user?.username ?? 'Desconocido'),
                datasets: [{
                    label: 'Citas',
                    data: citasPorUsuarioHoy.map(i => i.total),
                    backgroundColor: assignColors(citasPorUsuarioHoy)
                }]
            },
            options: defaultBarOptions()
        });

        // ── Gráfico 2: Citas por Estado (Hoy - sin campañas) ───────────────────────
        new Chart(document.getElementById('chartCitasPorEstadoHoy'), {
            type: 'doughnut',
            data: {
                labels: citasPorEstadoHoy.map(i => i.estado),
                datasets: [{
                    label: 'Citas',
                    data: citasPorEstadoHoy.map(i => i.total),
                    backgroundColor: [
                        '#b38f00', // pendiente
                        '#006400', // confirmado
                        '#4b0082', // en_consultorio
                        '#808080', // finalizado
                        '#8b0000', // cancelado
                        '#e67e22', // en_recepcion
                        '#ff6384', // programar
                        '#007b73', // cabina
                        '#ff0000', // sos
                        '#d4a320' // citado
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // ── Gráfico 3: Citas Finalizadas por Usuario (Mes actual - sin campañas) ────
        new Chart(document.getElementById('chartFinalizadasMensual'), {
            type: 'bar',
            data: {
                labels: citasFinalizadasMensual.map(i => i.user?.username ?? 'Desconocido'),
                datasets: [{
                    label: 'Finalizadas',
                    data: citasFinalizadasMensual.map(i => i.total),
                    backgroundColor: assignColors(citasFinalizadasMensual)
                }]
            },
            options: defaultBarOptions()
        });

        // ── Gráfico 4: Pacientes Nuevos vs Frecuentes (Mes actual) ─────────────────
        new Chart(document.getElementById('chartPacientesNuevosFrecuentes'), {
            type: 'doughnut',
            data: {
                labels: ['Frecuentes', 'Nuevos'],
                datasets: [{
                    data: [pacientesFrecuentesCount, pacientesNuevos],
                    backgroundColor: ['rgba(255, 205, 86, 0.8)', 'rgba(75, 192, 192, 0.8)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Pacientes Nuevos vs Frecuentes del Mes'
                    }
                }
            }
        });

        // ── Gráfico 5: Tiempo Promedio Llegada → Inicio (Mes actual) ───────────────
        new Chart(document.getElementById('chartPromedioLlegadaInicio'), {
            type: 'bar',
            data: {
                labels: ['Promedio (minutos)'],
                datasets: [{
                    label: 'Minutos',
                    data: [promedioMinutos],
                    backgroundColor: 'rgba(255, 206, 86, 0.7)'
                }]
            },
            options: defaultBarOptions()
        });

        // ── Gráfico 6: Citas Finalizadas por Doctor (Mes actual) ───────────────────
        new Chart(document.getElementById('chartCitasFinalizadasPorDoctor'), {
            type: 'bar',
            data: {
                labels: citasFinalizadasPorDoctor.map(i => i.doctor_nombre ?? 'Desconocido'),
                datasets: [{
                    label: 'Citas Finalizadas',
                    data: citasFinalizadasPorDoctor.map(i => i.total),
                    backgroundColor: assignColors(citasFinalizadasPorDoctor)
                }]
            },
            options: defaultBarOptions()
        });

        // ── Gráfico 7: Tratamientos Más Realizados (Últimos 7 días) ────────────────
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

        // ── Gráfico 8: Ingresos por Tratamiento (Mes actual) ────────────────────────
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