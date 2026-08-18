<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Paciente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa; /* fondo claro de Bootstrap */
            color: #212529; /* texto estándar Bootstrap */
        }
        .profile-card {
            background-color: #ffffff; /* blanco para tarjetas */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05); /* sombra sutil */
        }
        .tab-content, .form-section {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        .nav-tabs .nav-link {
            color: #495057; /* texto gris oscuro */
            background-color: #e9ecef; /* gris claro */
            border: none;
            border-radius: 5px;
            margin-right: 5px;
        }
        .nav-tabs .nav-link.active {
            background-color: #ffffff;
            color: #212529;
            font-weight: 500;
        }
        .form-control {
            background-color: #ffffff;
            border: 1px solid #ced4da;
            color: #212529;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #dee2e6;
        }
       
            .text-formateado{
                white-space: pre-wrap;
                border: 1px solid #ccc;
                padding: 10px;
            }
            .sin-desborde{
                white-space: pre-line;
            }
        .btn-purple {
            background-color: #6f42c1; /* Morado tipo Bootstrap (purple) */
            color: white;
        }

        .btn-purple:hover {
            background-color: #5a32a3;
            color:white;
        }

        /* Estilos para el collapse de recetas */
        .btn-collapse-recipes {
            color: #6c757d;
            padding: 0.5rem 0;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            box-shadow: none;
        }

        .btn-collapse-recipes:hover {
            color: #495057;
            background-color: transparent;
            text-decoration: none;
        }

        .btn-collapse-recipes:not(.collapsed) {
            color: #17a2b8;
        }

        .btn-collapse-recipes .chevron {
            display: inline-block;
            transition: transform 0.3s ease;
        }

        .btn-collapse-recipes.collapsed .chevron {
            transform: rotate(-90deg);
        }

        .btn-collapse-recipes:focus {
            outline: none;
            box-shadow: none;
        }

        /* Responsive labels */
        @media (max-width: 576px) {
            label {
                font-size: 0.85rem !important;
                word-break: break-word;
            }
            h5 {
                font-size: 1rem !important;
                word-break: break-word;
            }
            h3 {
                font-size: 1.25rem !important;
                word-break: break-word;
            }
            /* Responsive para tabla de citas */
            #citas .container {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            #citas .table {
                font-size: 0.80rem !important;
            }
            #citas .btn {
                font-size: 0.75rem !important;
                padding: 0.25rem 0.5rem !important;
                margin: 2px !important;
            }
            #citas th {
                font-size: 0.80rem !important;
                padding: 0.5rem !important;
                word-break: break-word;
            }
            #citas td {
                padding: 0.5rem !important;
                word-break: break-word;
            }
        }
    </style>

</head>


<body>
<div class="container mt-4">
<div class="profile-card d-flex flex-column flex-md-row align-items-center text-center text-md-left">
    <!-- Ícono -->
    <!-- <div class="mb-3 mb-md-0 mr-md-3 d-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 100px; height: 100px;">
        <i class="fas fa-user text-secondary" style="font-size: 2.5rem;"></i>
    </div> -->
    <?php
        $iconoGenero = 'fa-user'; // Por defecto (no especificado o masculino)
        if (($paciente->historias_clinica->sexo ?? '') === 'F') {
            $iconoGenero = 'fa-female';
        } elseif (($paciente->historias_clinica->sexo ?? '') === 'M') {
            $iconoGenero = 'fa-user';
        }
    ?>
    <div class="mb-3 mb-md-0 mr-md-3 d-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 100px; height: 100px;">
        <i class="fas <?= $iconoGenero ?> text-secondary" style="font-size: 2.5rem;"></i>
    </div>

    <!-- Info principal -->
    <div class="order-1 order-md-0 mb-3 mb-md-0">
        <h4 class="mb-0"><?= h($paciente->nombre) ?> <?= h($paciente->apellido) ?></h4>
        <p class="mb-0">Género: <?= ($paciente->historias_clinica->sexo ?? '') == 'M' ? 'Masculino' : (($paciente->historias_clinica->sexo ?? '') == 'F' ? 'Femenino' : 'No especificado') ?></p>
        <p class="mb-0">Edad: <?= h($paciente->historias_clinica->edad ?? '') ?></p>
        <!-- <p class="mb-0">Tto. Interes: <?= h($paciente->historias_clinica->tipo_orden ?? '') ?></p> -->
        <p class="mb-0">Teléfono: <?= h($paciente->telefono_celular) ?></p>
    </div>

    <!-- Reacciones adversas -->
    <div class="order-2 mt-3 mt-md-0 ml-md-auto text-center text-md-right">
        <h5>Reacciones adversas a medicamentos</h5>
        <p style="white-space: pre-line;"><?= h($paciente->historias_clinica->alergias ?? '') ?></p>
    </div>
</div>

   
    <div class="mt-4 overflow-auto" style="white-space: nowrap;">
    <?php $this->loadHelper('Permisos'); ?>
    <ul class="nav nav-tabs mt-4 flex-column flex-sm-row">
    <li class="nav-item">
        <a class="nav-link active text-center mb-2 mx-2" data-toggle="tab" href="#datos">Datos administrativos</a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-center mb-2 mx-2" data-toggle="tab" href="#antecedentes">Antecedentes médicos</a>
    </li>

    <?php if ($this->Permisos->tiene('Consultas', 'index')): ?>
    <li class="nav-item">
        <a class="nav-link text-center mb-2 mx-2" data-toggle="tab" href="#consultas">Recetas y Procedimientos</a>
    </li>
    <?php endif; ?>

    <li class="nav-item">
        <a class="nav-link text-center mb-2 mx-2" data-toggle="tab" href="#recordatorios">Recordatorios</a>
    </li>
    <?php if ($this->Permisos->tiene('Presupuestos', 'index')): ?>
    <li class="nav-item">
        <a class="nav-link text-center mb-2 mx-2" data-toggle="tab" href="#presupuestos">Presupuestos</a>
    </li>
    <?php endif; ?>
    <?php if ($this->Permisos->tiene('Invoices', 'view')): ?>
    <li class="nav-item">
        <a class="nav-link text-center mb-2 mx-2" data-toggle="tab" href="#comprobantes">Comprobantes</a>
    </li>
    <?php endif; ?>
    
    <?php if ($this->Permisos->tiene('PaquetesPagos', 'index') && !empty($paciente->historias_clinica)): ?>
    <li class="nav-item">
        <a class="nav-link text-center mb-2 mx-2" data-toggle="tab" href="#recordatorios-pago">Recordatorios de Pago</a>
    </li>
    <?php endif; ?>
    
    <?php if ($this->Permisos->tiene('Citas', 'index')): ?>
    <li class="nav-item">
        <a class="nav-link text-center mb-2 mx-2" data-toggle="tab" href="#citas">Citas del Paciente</a>
    </li>
    <?php endif; ?>

</ul>



            <div class="tab-content mt-3">
        <!-- Pestaña 1: Datos administrativos -->
        <div id="datos" class="tab-pane fade show active">
    <div class="form-section">
        <?php if (!empty($paciente->historias_clinica)) : ?>
            <h5 class="mb-4 sin-desborde">INFORMACIÓN PERSONAL</h5>
            <div class="row">
                <div class="col-md-12 mb-2">
                    <label>Nombres</label>
                    <input type="text" class="form-control" value="<?= h($paciente->nombre) ?>" readonly>
                </div>
                <div class="col-md-12 mb-2">
                    <label>Apellidos</label>
                    <input type="text" class="form-control" value="<?= h($paciente->apellido) ?>" readonly>
                </div>
                <div class="col-md-6 mb-2">
                    <label>DNI/Carnet de extranjeria</label>
                    <input type="text" class="form-control" value="<?= h($paciente->historias_clinica->dni) ?>" readonly>
                </div>
                <div class="col-md-6 mb-2">
                    <label>Fecha de Nacimiento</label>
                    <input type="text" class="form-control" value="<?= h($paciente->historias_clinica->fecha_nacimiento) ?>" readonly>
                </div>
                <div class="col-md-6 mb-2">
                    <label>Sexo</label>
                    <input type="text" class="form-control" 
                        value="<?= $paciente->historias_clinica->sexo === 'M' ? 'Masculino' : ($paciente->historias_clinica->sexo === 'F' ? 'Femenino' : 'No especificado') ?>" 
                        readonly>
                </div>                    
                <div class="col-md-6 mb-2">
                    <label>Edad</label>
                    <input type="text" class="form-control" value="<?= h($paciente->historias_clinica->edad) ?>" readonly>
                </div>
                <div class="col-md-12 mb-2">
                    <label>Procedencia Distrito</label>
                    <input type="text" class="form-control" value="<?= !empty($paciente->historias_clinica->departamento->nombre) ? h($paciente->historias_clinica->departamento->nombre) : '—' ?>" readonly>
                </div>
                <div class="col-md-12 mb-2">
                    <label>Direccion</label>
                    <input type="text" class="form-control" value="<?= !empty($paciente->historias_clinica->direccion) ? h($paciente->historias_clinica->direccion) : '—' ?>" readonly>
                </div>
                <div class="col-md-12 mb-2">
                    <label>Ocupación</label>
                    <input type="text" class="form-control" value="<?= !empty($paciente->historias_clinica->ocupacion) ? h($paciente->historias_clinica->ocupacion) : '—' ?>" readonly>
                </div>
                <div class="col-md-12 mb-2">
                    <label>Teléfono Celular</label>
                    <input type="text" class="form-control" value="<?= !empty($paciente->telefono_celular) ? h($paciente->telefono_celular) : '—' ?>" readonly>
                </div>
            </div>

            <h5 class="mt-4 mb-4">INFORMACIÓN CRM</h5>
            <div class="row">
                <?php
                $campanas = [];

                if (!empty($paciente->citas)) {
                    foreach ($paciente->citas as $cita) {
                        if (!empty($cita->campana)) {
                            $campanas[] = h($cita->campana->nombre);
                        }
                    }
                }

                $campanasTexto = !empty($campanas) ? implode(' • ', $campanas) : 'N/A';
                ?>
                <div class="col-md-12 mb-2">
                    <label>Campañas</label>
                    <input type="text" class="form-control" value="<?= $campanasTexto ?>" readonly>
                </div>
                <div class="col-md-12 mb-2">
                      <label>Tto. de interes</label>
                        <input type="text" class="form-control" value="<?= h($paciente->historias_clinica->tipo_orden ?? '') ?>" readonly>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label>Responsable</label>
                        <input type="text" class="form-control" value="<?= h($paciente->historias_clinica->user->username ?? '') ?>" readonly>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label>Agendado por</label>
                        <input type="text" class="form-control" value="<?= h($paciente->historias_clinica->user->username ?? '') ?>" readonly>
                    </div>
                </div>
                <h5 class="mt-4 mb-4">OBS ADICIONALES - SOS</h5>
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <input type="text" class="form-control" value="<?= !empty($paciente->historias_clinica->obs_administrativas) ? h($paciente->historias_clinica->obs_administrativas) : '&nbsp;' ?>" readonly>
                </div>
            </div>
        <?php else : ?>
            <p class="text-center sin-desborde">No hay historia clinica registrada para este paciente.</p>
            <div class="text-center mt-3">
                <?= $this->Html->link(
                    __('Agregar Historia Clínica'),
                    ['controller' => 'HistoriasClinicas', 'action' => 'add', $paciente->id],
                    ['class' => 'btn btn-info openModal', 'target' => '_blank']
                ) ?>
            </div>
        <?php endif; ?>
    </div>
</div>
        
        <!-- Pestaña 2: Antecedentes médicos -->
        <div id="antecedentes" class="tab-pane fade">
            <div class="form-section">
                <!-- <div class="card-header">
                    <h3>Historias Clínicas del Paciente</h3>
                </div> -->
                <div class="card-body">
                <h4 class="ml-3 sin-desborde">Antecedentes Médicos</h4>
                    <?php if (!empty($paciente->historias_clinica)) : ?>
                           
                            <div class="rounded p-3 mb-4">
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <p class="label-text">Medicacion:</p>
                                    </div>
                                    <div class="col-md-10">
                                    <div class="text-formateado" ><?= h($paciente->historias_clinica->medicacion) ?></div>

                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <p class="label-text" style="white-space: normal; word-break: break-word;">
                                            Reacciones adversas a medicamentos:
                                        </p>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="text-formateado"><?= h($paciente->historias_clinica->alergias) ?></div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <p class="label-text">Enfermedades:</p>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="text-formateado"><?= h($paciente->historias_clinica->enfermedades) ?></div>
                                    </div>
                                </div>
                                <!-- Boton para editar historia -->
                                <div class="text-center mt-3">
                                    <?= $this->Html->link(
                                        __('Editar Antecedentes'),
                                        ['controller' => 'HistoriasClinicas', 'action' => 'edit', $paciente->id],
                                        ['class' => 'btn btn-info openModal', 'target' => '_blank']
                                    ) ?>                        
                                </div>
                                
                                <!-- Examen Físico -->
<h4 class="mt-4 mb-4">Examen Físico</h4>
    <?php if (!empty($paciente->historias_clinica->examenes_fisicos)) : ?>
        <?php 
            // Obtener solo el último examen físico
            $ultimoExamenFisico = end($paciente->historias_clinica->examenes_fisicos); 
        ?>
        <div class="row mb-3">
            <div class="col-md-4">
                <p class="label-text">Edad:</p>
                <div class="form-control"><?= h($ultimoExamenFisico->edad) ?></div>
            </div>
            <div class="col-md-4">
                <p class="label-text">Peso (kg):</p>
                <div class="form-control"><?= h($ultimoExamenFisico->peso) ?></div>
            </div>
            <div class="col-md-4">
                <p class="label-text">Altura (cm):</p>
                <div class="form-control"><?= h($ultimoExamenFisico->altura) ?></div>
            </div>
            <div class="col-md-4">
                <p class="label-text">Temperatura (°C):</p>
                <div class="form-control"><?= h($ultimoExamenFisico->temperatura) ?></div>
            </div>
            <div class="col-md-4">
                <p class="label-text">Escala EVA:</p>
                <div class="form-control"><?= h($ultimoExamenFisico->eva) ?></div>
            </div>
            <div class="col-md-4">
                <p class="label-text">Presión Arterial:</p>
                <div class="form-control"><?= h($ultimoExamenFisico->presion) ?></div>
            </div>
            <div class="col-md-4">
                <p class="label-text">Frecuencia Cardíaca (bpm):</p>
                <div class="form-control"><?= h($ultimoExamenFisico->frecuencia_cardiaca) ?></div>
            </div>
            <div class="col-md-4">
                <p class="label-text">Saturación (%):</p>
                <div class="form-control"><?= h($ultimoExamenFisico->saturacion) ?></div>
            </div>
            <div class="col-md-4">
                <p class="label-text">Glicemia (mg/dL):</p>
                <div class="form-control"><?= h($ultimoExamenFisico->glicemina) ?></div>
            </div>
        </div>
        <!-- Botón para agregar un nuevo Examen Físico -->
        <div class="text-center mt-4">
            <?= $this->Html->link(
                __('Agregar otro Examen'),
                ['controller' => 'ExamenesFisicos', 'action' => 'add', $paciente->historias_clinica->id],
                ['class' => 'btn btn-info openModal', 'target' => '_blank']
            ) ?>
        </div>
        <!-- Botón para ver todas las historias de exámenes físicos -->
        <div class="text-center mt-3">
            <?= $this->Html->link(
                __('Ver Historias'),
                ['controller' => 'FisicosHistorias', 'action' => 'index', '?' => ['historia_id' => $paciente->historias_clinica->id]],
                ['class' => 'btn btn-primary openModalXl', 'target' => '_blank']
            ) ?>
        </div>

    <?php else : ?>
        <p class="text-center text-muted sin-desborde">No hay datos de examen físico registrados.</p>
                <!-- Botón para agregar un nuevo Examen Físico -->
                                <div class="text-center mt-4">
                                    <?= $this->Html->link(
                                        __('Agregar Examen Físico'),
                                        ['controller' => 'ExamenesFisicos', 'action' => 'add', $paciente->historias_clinica->id],
                                        ['class' => 'btn btn-info openModal', 'target' => '_blank']
                                    ) ?>
                                </div>
                                <!-- Botón para ver todas las historias de exámenes físicos -->
                                <div class="text-center mt-3">
                                    <?= $this->Html->link(
                                        __('Ver Historias'),
                                        ['controller' => 'FisicosHistorias', 'action' => 'index', '?' => ['historia_id' => $paciente->historias_clinica->id]],
                                        ['class' => 'btn btn-primary openModalXl', 'target' => '_blank']
                                    ) ?>
                                </div>
    <?php endif; ?>
                            </div>
                            
                            <hr> <!-- Separador entre antecedentes -->
                    <?php else : ?>
                        <p class="text-center sin-desborde">No hay historia clínica registradas para este paciente.</p>
                        <div class="text-center mt-3">
                            <?= $this->Html->link(
                                __('Agregar Historia Clínica'),
                                ['controller' => 'HistoriasClinicas', 'action' => 'add', $paciente->id],
                                ['class' => 'btn btn-info openModal', 'target' => '_blank']
                            ) ?>                        
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        


<!-- Pestaña 4: Consultas y Procedimientos Juntos -->
<?php if ($this->Permisos->tiene('Consultas', 'index')): ?>
<div id="consultas" class="tab-pane fade">
    <div class="form-section">
        <div class="d-flex flex-wrap justify-content-end align-items-center gap-2 mb-3">
            <?php if (!empty($paciente->historias_clinica)): ?>
                <?= $this->Html->link(
                    '+ Nuevo procedimiento',
                    ['controller' => 'Procedimientos', 'action' => 'add', $paciente->historias_clinica->id],
                    ['class' => 'btn btn-info openModal mx-2 mt-2 mt-md-0', 'target' => '_blank']
                ) ?>
                <?= $this->Html->link(
                    '+ Nueva receta',
                    ['controller' => 'Consultas', 'action' => 'add', $paciente->historias_clinica->id],
                    ['class' => 'btn btn-info mx-2 mt-2 mt-md-0', 'target' => '_blank']
                ) ?>
            <?php endif; ?>
        </div>

        <?php
        $eventos = [];

        if (!empty($paciente->historias_clinica)) {
            foreach ($paciente->historias_clinica->consultas ?? [] as $consulta) {
                $eventos[] = [
                    'tipo' => 'consulta',
                    'created' => $consulta->created,
                    'data' => $consulta
                ];
            }

            foreach ($paciente->historias_clinica->procedimientos ?? [] as $procedimiento) {
                $eventos[] = [
                    'tipo' => 'procedimiento',
                    'created' => $procedimiento->created,
                    'data' => $procedimiento
                ];
            }

            usort($eventos, function ($a, $b) {
                return $b['created'] <=> $a['created'];
            });
        }
        
        // Paginación
        $eventosPorPagina = 5;
        $paginaActual = isset($_GET['pag_atencion']) ? max(1, (int)$_GET['pag_atencion']) : 1;
        $totalEventos = count($eventos);
        $totalPaginas = ceil($totalEventos / $eventosPorPagina);
        $paginaActual = min($paginaActual, max(1, $totalPaginas));
        
        $inicio = ($paginaActual - 1) * $eventosPorPagina;
        $eventosPaginados = array_slice($eventos, $inicio, $eventosPorPagina);
        ?>

        <?php if (!empty($eventos)): ?>
            <div id="lista-atenciones">
            <?php $counterInicio = $totalEventos - $inicio; ?>
            <?php foreach ($eventosPaginados as $evento): ?>
                <?php if ($evento['tipo'] === 'consulta'): ?>
                    <?php $consulta = $evento['data']; ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-auto pr-0">
                                    <div style="font-size: 2.5rem; color: #17a2b8;">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </div>
                                <div class="col">
                                    
                                    
                                    <div class="row">
                                        <div class="col-12 col-md-8">
                                            <h6 class="card-title mb-3" style="font-size: 0.95rem;">
                                                <?= h($consulta->motivo) ?>
                                            </h6>
                                            <p class="mb-1" style="font-size: 0.85rem;">
                                                <strong>Receta #<?= $counterInicio ?></strong>
                                            </p>
                                            <p class="mb-1" style="font-size: 0.85rem;">
                                                <strong class="d-md-none">Fec.:</strong><strong class="d-none d-md-inline">Fecha:</strong>
                                                <?= h($consulta->created->format('d-m-Y')) ?>
                                            </p>
                                            <p class="mb-2" style="font-size: 0.85rem;">
                                                <strong class="d-md-none">Esp.:</strong><strong class="d-none d-md-inline">Especialista:</strong>
                                                <?= !empty($consulta->doctore)
                                                    ? h($consulta->doctore->nombre . ' ' . $consulta->doctore->apellido)
                                                    : 'Sin doctor' ?>
                                            </p>

                                            <!-- Recetas asociadas -->
                                            <?php if (!empty($consulta->recetas)): ?>
                                                <div class="mt-2">
                                                    <?php foreach ($consulta->recetas as $receta): ?>
                                                        <div class="mb-1" style="font-size: 0.8rem;">
                                                            <strong>Receta:</strong> <span><?= h($receta->nombre) ?></span>
                                                            <?php 
                                                                $medicamentos = array_map(fn($m) => $m->medicamento->nombre ?? 'Sin nombre', $receta->recetas_medicamentos);
                                                            ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-12 col-md-4 text-right mt-2 mt-md-0">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-pencil-alt"></i>',
                                                    ['controller' => 'Consultas', 'action' => 'edit', $consulta->id],
                                                    ['class' => 'btn btn-outline-info', 'target' => '_blank', 'escape' => false, 'title' => 'Editar']
                                                ) ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-eye"></i>',
                                                    ['controller' => 'Consultas', 'action' => 'view', $consulta->id],
                                                    ['class' => 'btn btn-outline-info openModal', 'target' => '_blank', 'escape' => false, 'title' => 'Ver']
                                                ) ?>
                                                <?php if (!empty($consulta->recetas) && count($consulta->recetas) > 0): ?>
                                                    <?= $this->Html->link(
                                                        '<i class="fas fa-file-pdf"></i>',
                                                        ['controller' => 'Recetas', 'action' => 'exportPdf', $consulta->recetas[0]->id],
                                                        ['class' => 'btn btn-outline-danger', 'target' => '_blank', 'escape' => false, 'title' => 'Descargar Receta PDF']
                                                    ) ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php $procedimiento = $evento['data']; ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-auto pr-0">
                                    <div style="font-size: 2.5rem; color: #28a745;">
                                        <i class="fas fa-stethoscope"></i>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col-12 col-md-8">
                                            <h6 class="card-title mb-1">
                                                Procedimiento #<?= $counterInicio ?>
                                            </h6>
                                            <p class="text-muted mb-2" style="white-space: pre-line; font-size: 0.9rem;">
                                                <?= h($procedimiento->procedimiento ?? 'Sin nombre') ?>
                                            </p>
                                            <p class="mb-0" style="font-size: 0.75rem;">
                                                <strong>Fecha:</strong> <?= h($procedimiento->created?->format('d-m-Y') ?? 'Sin fecha') ?><br class="d-md-none">
                                                <strong class="d-md-none">Dr.:</strong><strong class="d-none d-md-inline">Doctor:</strong>
                                                <?= !empty($procedimiento->doctore)
                                                    ? h($procedimiento->doctore->nombre . ' ' . $procedimiento->doctore->apellido)
                                                    : 'Sin doctor' ?>
                                            </p>
                                        </div>
                                        <div class="col-12 col-md-4 text-right mt-2 mt-md-0">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-pencil-alt"></i>',
                                                    ['controller' => 'Procedimientos', 'action' => 'edit', $procedimiento->id],
                                                    ['class' => 'btn btn-outline-info openModal', 'target' => '_blank', 'escape' => false, 'title' => 'Editar']
                                                ) ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-eye"></i>',
                                                    ['controller' => 'Procedimientos', 'action' => 'view', $procedimiento->id],
                                                    ['class' => 'btn btn-outline-info openModal', 'target' => '_blank', 'escape' => false, 'title' => 'Ver']
                                                ) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php $counterInicio--; ?>
            <?php endforeach; ?>
            </div>
            
            <!-- Paginación -->
            <?php if ($totalPaginas > 1): ?>
                <nav aria-label="Paginación de atenciones" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($paginaActual > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?pag_atencion=<?= $paginaActual - 1 ?>#consultas">Anterior</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                            <li class="page-item <?= ($i === $paginaActual) ? 'active' : '' ?>">
                                <a class="page-link" href="?pag_atencion=<?= $i ?>#consultas"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($paginaActual < $totalPaginas): ?>
                            <li class="page-item">
                                <a class="page-link" href="?pag_atencion=<?= $paginaActual + 1 ?>#consultas">Siguiente</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center mt-5">
                <p class="sin-desborde"><i class="fas fa-exclamation-circle"></i> No hay atenciones ni procedimientos registrados.</p>
            </div>
        <?php endif; ?>
    </div> <!-- cierre de .form-section -->
</div>
<?php endif; ?> <!-- Cierre de Consultas -->

<!-- inicio de consultas -->
<?php if ($this->Permisos->tiene('Citas', 'index')): ?>
<div id="citas" class="tab-pane fade">
    <div class="form-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 sin-desborde">Citas del Paciente</h4>
        </div>
        <div>
                <?php if (!empty($paciente->citas)) : ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered text-center align-middle">
                            <thead class="table">
                                <tr>
                                    <th>Doctor</th>
                                    <th>Fecha y Hora</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Motivo</th>
                                    <!-- <th>Acción</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($paciente->citas as $cita): ?>
                                    <tr>
                                        <td>
                                            <?= !empty($cita->doctore) 
                                                ? h($cita->doctore->nombre . ' ' . $cita->doctore->apellido) 
                                                : 'No asignado' ?>
                                        </td>


                                        <td><?= h($cita->fecha_hora->format('d-m-Y H:i')) ?></td>
                                        <td>
                                            <?= h($cita->tipo === 'C' ? 'Consulta' : ($cita->tipo === 'P' ? 'Procedimiento' : '—')) ?>
                                        </td>

                                        <td><?= h($cita->estado) ?></td>
                                        <td style="max-width: 250px; word-wrap: break-word; white-space: normal;">
                                            <?= h($cita->motivo) ?>
                                        </td>

                                        <!-- <td>
                                            <div class="d-flex flex-column flex-md-row gap-1">
                                                <?= $this->Html->link(
                                                    'Ver',
                                                    ['controller' => 'Citas', 'action' => 'view', $cita->id],
                                                    ['class' => 'btn btn-sm btn-primary openModal']
                                                ) ?>
                                                <?= $this->Html->link(
                                                    'Editar',
                                                    ['controller' => 'Citas', 'action' => 'edit', $cita->id],
                                                    ['class' => 'btn btn-sm btn-primary openModal']
                                                ) ?>
                                                <button 
                                                    class="btn btn-sm btn-success marcar-hora-llegada" 
                                                    data-cita-id="<?= $cita->id ?>"
                                                    data-hora-llegada="<?= $cita->hora_llegada ? $cita->hora_llegada->format('H:i:s') : 'N/A' ?>"
                                                    data-fecha-hora="<?= $cita->fecha_hora->format('Y-m-d H:i:s') ?>"
                                                >
                                                    Hora llegada
                                                </button>
                                                
                                                <button 
                                                    class="btn btn-sm btn-purple cambiar-estado" 
                                                    data-cita-id="<?= $cita->id ?>" 
                                                    data-estado="en_consultorio">
                                                    En consultorio
                                                </button>

                                                
                                                <button 
                                                    class="btn btn-sm btn-warning cambiar-estado" 
                                                    data-cita-id="<?= $cita->id ?>" 
                                                    data-estado="en_recepcion">
                                                    En recepción
                                                </button>
                                            </div>
                                        </td> -->
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <p class="text-center sin-desborde">No hay citas registradas para este paciente.</p>
                <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?> 
<!-- Cierre de Citas -->

<!-- Pestaña: Recordatorios de Pago -->
<?php if ($this->Permisos->tiene('PaquetesPagos', 'index')): ?>
<div id="recordatorios-pago" class="tab-pane fade">
    <div class="form-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 sin-desborde">Recordatorios de Pago</h4>
            <?= $this->Html->link(
                '+ Nuevo Paquete',
                ['controller' => 'PaquetesPagos', 'action' => 'add', '?' => ['historia_id' => $paciente->historias_clinica->id]],
                ['class' => 'btn btn-info openModal']
            ) ?>
        </div>

        <?php if (!empty($paciente->historias_clinica->paquetes_pagos)) : ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo Pago</th>
                            <th>Monto Total</th>
                            <th>Estado</th>
                            <th>Fecha Recordatorio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paciente->historias_clinica->paquetes_pagos as $paquete): ?>
                            <tr>
                                <td>
                                    <span class="badge badge-<?= $paquete->tipo_pago === 'contado' ? 'success' : 'info' ?>">
                                        <?= h($paquete->tipo_pago === 'contado' ? 'Contado' : 'Cuotas') ?>
                                    </span>
                                </td>
                                <td>S/ <?= number_format($paquete->precio_total, 2) ?></td>
                                <td>
                                    <?php 
$estadoClass = match($paquete->estado) {
    'pendiente' => 'warning',
    'cancelado' => 'success',
    default => 'light'
};

$estadoText = match($paquete->estado) {
    'pendiente' => 'Pendiente',
    'cancelado' => 'Pagado',
    default => $paquete->estado
};
                                    ?>
                                    <span class="badge badge-<?= $estadoClass ?>">
                                        <?= $estadoText ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($paquete->tipo_pago === 'contado'): ?>
                                        <?= $paquete->fecha_recordatorio ? h($paquete->fecha_recordatorio->format('d-m-Y')) : '—' ?>
                                    <?php else: ?>
                                        <?php if (!empty($paquete->paquetes_pagos_cuotas)): ?>
                                            <ul class="mb-0 pl-3">
                                                <?php foreach ($paquete->paquetes_pagos_cuotas as $cuota): ?>
                                                    <li>
                                                        <?= $cuota->fecha_recordatorio ? h((
                                                            is_object($cuota->fecha_recordatorio) ? $cuota->fecha_recordatorio->format('d-m-Y') : $cuota->fecha_recordatorio
                                                        )) : '—' ?>
                                                        — S/ <?= number_format($cuota->monto, 2) ?>
                                                        <?php if (!empty($cuota->estado)): ?>
                                                            <span class="text-muted">(<?= h($cuota->estado) ?>)</span>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <?= $this->Html->link(
                                            '<i class="fas fa-eye"></i> Ver',
                                            ['controller' => 'PaquetesPagos', 'action' => 'view', $paquete->id],
                                            ['class' => 'btn btn-sm btn-primary openModal', 'escape' => false]
                                        ) ?>
                                        <?= $this->Html->link(
                                            '<i class="fas fa-edit"></i> Editar',
                                            ['controller' => 'PaquetesPagos', 'action' => 'edit', $paquete->id],
                                            ['class' => 'btn btn-sm btn-secondary openModal', 'escape' => false]
                                        ) ?>
                                        <?= $this->Form->postLink(
                                            '<i class="fas fa-trash"></i> Eliminar',
                                            ['controller' => 'PaquetesPagos', 'action' => 'delete', $paquete->id],
                                            ['class' => 'btn btn-sm btn-danger', 'escape' => false, 'confirm' => '¿Estás seguro de que deseas eliminar este paquete de pago?']
                                        ) ?>
                                        <?php if ($paquete->estado === 'pendiente'): ?>
    <?= $this->Form->postLink(
        '<i class="bi bi-check-circle"></i> Finalizar',
        [
            'controller' => 'PaquetesPagos',
            'action' => 'marcarPagado',
            $paquete->id
        ],
        [
            'class' => 'btn btn-success btn-sm',
            'escape' => false,
            'confirm' => '¿Marcar el paquete como pagado?'
        ]
    ) ?>
<?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <p class="text-center text-muted sin-desborde">No hay paquetes de pago registrados para este paciente.</p>
            <div class="text-center mt-3">
                <?= $this->Html->link(
                    '+ Agregar Paquete de Pago',
                    ['controller' => 'PaquetesPagos', 'action' => 'add', '?' => ['historia_id' => $paciente->historias_clinica->id]],
                    ['class' => 'btn btn-info openModal']
                ) ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
<!-- Cierre de Recordatorios de Pago -->

<!-- Pestaña: Presupuestos y Órdenes -->
<?php if ($this->Permisos->tiene('Presupuestos', 'index')): ?>
    <div id="presupuestos" class="tab-pane fade">
        <div class="form-section">
            <h4 class="mb-3">Presupuestos</h4>
            <!-- Contenido de sub-tabs -->
                <div id="tab-presupuestos" class="tab-pane fade show active">
                    <?php if (!empty($paciente->historias_clinica)) : ?>
                        <div class="d-flex flex-wrap justify-content-end align-items-center gap-2 mb-3">
                            <?= $this->Html->link(
                                '+ Nuevo Presupuesto',
                                ['controller' => 'Presupuestos', 'action' => 'add', '?' => ['historia_id' => $paciente->historias_clinica->id]],
                                ['class' => 'btn btn-info openModal mx-2 mt-2 mt-md-0', 'target' => '_blank']
                            ) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($paciente->historias_clinica->presupuestos)) : ?>
                        <?php foreach ($paciente->historias_clinica->presupuestos as $presupuesto) : ?>
                            <?php
                            $facturado = (float) ($facturadoPorPresupuesto[$presupuesto->id] ?? 0);
                            $pendiente = max(0, (float) $presupuesto->total - $facturado);
                            ?>
                            <div class="card mb-3 p-3">
                                <div class="d-flex flex-column flex-md-row align-items-center">
                                    <div class="mr-md-3 mb-3 mb-md-0">
                                        <i class="fas fa-file-invoice-dollar fa-3x"></i>
                                    </div>
                                    <div class="flex-fill mb-3 mb-md-0">
                                        <h5 class="mb-0 text-break">
                                            Presupuesto #<?= h($presupuesto->id) ?>
                                            <small class="d-block text-muted">Total: S/. <?= number_format($presupuesto->total, 2) ?></small>
                                        </h5>
                                        <p class="mb-1">
                                            <strong>Fecha:</strong> <?= $presupuesto->created ? $presupuesto->created->format('d-m-Y') : 'N/A' ?><br>
                                        </p>
                                        <p class="mb-1">
                                            <span class="badge bg-success">Pagado: S/. <?= number_format($facturado, 2) ?></span>
                                            <?php if ($pendiente > 0): ?>
                                                <span class="badge bg-warning text-dark">Pendiente: S/. <?= number_format($pendiente, 2) ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Sin saldo pendiente</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-center flex-wrap">
                                        <div class="mb-2 mx-2 w-auto w-md-auto">
                                            <?= $this->Html->link(
                                                '<i class="fas fa-eye"></i> Ver Presupuesto',
                                                ['controller' => 'Presupuestos', 'action' => 'view', $presupuesto->id],
                                                ['class' => 'btn btn-info btn-sm openModal w-100 w-md-auto', 'target' => '_blank', 'escape' => false]
                                            ) ?>
                                        </div>
                                        <div class="mb-2 mx-2 w-auto w-md-auto">
                                            <?= $this->Html->link(
                                                '<i class="fas fa-pencil-alt"></i> Editar',
                                                ['controller' => 'Presupuestos', 'action' => 'edit', $presupuesto->id],
                                                ['class' => 'btn btn-warning btn-sm openModal w-100 w-md-auto', 'target' => '_blank', 'escape' => false]
                                            ) ?>
                                        </div>
                                        <div class="mb-2 mx-2 w-auto w-md-auto">
                                            <?= $this->Html->link(
                                                '<i class="fas fa-download"></i> Descargar PDF',
                                                ['controller' => 'Presupuestos', 'action' => 'exportPresupuestoPdf', $presupuesto->id],
                                                ['class' => 'btn btn-danger btn-sm w-100 w-md-auto', 'target' => '_blank', 'escape' => false]
                                            ) ?>
                                        </div>
                                        <?php if ($pendiente > 0): ?>
                                            <div class="mb-2 mx-2 w-auto w-md-auto">
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-file-invoice"></i> Convertir a Factura',
                                                    ['controller' => 'Invoices', 'action' => 'add', '?' => ['presupuesto_id' => $presupuesto->id]],
                                                    ['class' => 'btn btn-success btn-sm w-100 w-md-auto', 'target' => '_blank', 'escape' => false]
                                                ) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="text-center mt-5">
                            <p class="sin-desborde"><i class="fas fa-exclamation-circle"></i> No hay presupuestos registrados para este paciente.</p>
                        </div>
                    <?php endif; ?>
                </div>
        </div>
    </div>
<?php endif; ?> <!-- Cierre de Presupuestos -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        var hash = window.location.hash;
        if (hash) {
            var link = document.querySelector('.nav-link[href="' + hash + '"]');
            if (link) {
                link.click();
                // además asegurar que la pestaña se muestre usando Bootstrap's tab if available
                if (typeof jQuery !== 'undefined' && typeof jQuery(link).tab === 'function') {
                    jQuery(link).tab('show');
                }
            }
        }
    } catch (e) {
        // silencioso
    }
});
</script>

<!-- Pestaña 12: Recordatorios -->
    <div id="recordatorios" class="tab-pane fade">
    <div class="form-section">
        <div class="d-flex flex-wrap justify-content-end align-items-center gap-2 mb-3">
            <?= $this->Html->link(
                '+ Nuevo Recordatorio',
                ['controller' => 'Recordatorios', 'action' => 'add', '?' => ['paciente_id' => $paciente->id]],
                ['class' => 'btn btn-info openModal mx-2 mt-2 mt-md-0']
            ) ?>
        </div>

        <?php
        $recordatoriosLista = [];

        if (!empty($paciente->recordatorios)) {
            foreach ($paciente->recordatorios as $recordatorio) {
                $recordatoriosLista[] = [
                    'created' => $recordatorio->created,
                    'data' => $recordatorio,
                ];
            }

            usort($recordatoriosLista, function ($a, $b) {
                return $b['created'] <=> $a['created'];
            });
        }

        $recordatoriosPorPagina = 5;
        $paginaActualRecordatorios = isset($_GET['pag_recordatorios']) ? max(1, (int)$_GET['pag_recordatorios']) : 1;
        $totalRecordatorios = count($recordatoriosLista);
        $totalPaginasRecordatorios = ceil($totalRecordatorios / $recordatoriosPorPagina);
        $paginaActualRecordatorios = min($paginaActualRecordatorios, max(1, $totalPaginasRecordatorios));

        $inicioRecordatorios = ($paginaActualRecordatorios - 1) * $recordatoriosPorPagina;
        $recordatoriosPaginados = array_slice($recordatoriosLista, $inicioRecordatorios, $recordatoriosPorPagina);
        ?>

        <?php if (!empty($recordatoriosLista)): ?>
            <div id="lista-recordatorios-paciente">
                <?php $telefonoWhatsApp = preg_replace('/[^0-9]/', '', (string)($paciente->telefono_celular ?? '')); ?>
                <?php $counterRecordatorio = $totalRecordatorios - $inicioRecordatorios; ?>
                <?php foreach ($recordatoriosPaginados as $itemRecordatorio): ?>
                    <?php $recordatorioPaciente = $itemRecordatorio['data']; ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="mr-2" style="font-size: 2rem; color: #0d6efd; line-height: 1; flex-shrink: 0;">
                                    <i class="fas fa-bell"></i>
                                </div>

                                <div class="flex-grow-1" style="min-width: 0;">
                                    <h6 class="mb-0" style="font-size: 0.95rem; font-weight: 600;">
                                        Recordatorio #<?= $counterRecordatorio ?>
                                    </h6>
                                    <div class="text-muted mb-2" style="font-size: 0.8rem;">
                                        Inicio: <?= $recordatorioPaciente->fecha_inicio ? $recordatorioPaciente->fecha_inicio->format('d-m-Y') : '(sin fecha)' ?>
                                    </div>

                                    <?php if (!empty($recordatorioPaciente->titulo)): ?>
                                        <div class="mb-2" style="font-size: 0.82rem;">
                                            <strong>Título:</strong>
                                            <span class="text-break"><?= h($recordatorioPaciente->titulo) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($recordatorioPaciente->duracion_estimada)): ?>
                                        <div class="mb-2" style="font-size: 0.82rem;">
                                            <strong>Duración estimada:</strong>
                                            <span class="text-break"><?= h($recordatorioPaciente->duracion_estimada) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($recordatorioPaciente->observacion)): ?>
                                        <div class="mb-2" style="font-size: 0.82rem;">
                                            <strong>Observación:</strong>
                                            <span class="text-break"><?= substr(h($recordatorioPaciente->observacion), 0, 120) ?><?= strlen($recordatorioPaciente->observacion) > 120 ? '...' : '' ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-flex flex-wrap" style="gap: 0.4rem;">
                                        <?= $this->Html->link(
                                            '<i class="fas fa-eye"></i> Ver',
                                            ['controller' => 'Recordatorios', 'action' => 'view', $recordatorioPaciente->id],
                                                ['class' => 'btn btn-sm btn-info openModal', 'escape' => false]
                                        ) ?>
                                        <?= $this->Html->link(
                                            '<i class="fas fa-edit"></i> Editar',
                                            ['controller' => 'Recordatorios', 'action' => 'edit', $recordatorioPaciente->id],
                                                ['class' => 'btn btn-sm btn-warning openModal', 'escape' => false]
                                        ) ?>
                                        <?= $this->Form->postLink(
                                            '<i class="fas fa-trash"></i> Eliminar',
                                            ['controller' => 'Recordatorios', 'action' => 'delete', $recordatorioPaciente->id],
                                            ['class' => 'btn btn-sm btn-danger','escape' => false, 'confirm' => '¿Está seguro de que desea desactivar este recordatorio?']
                                        ) ?>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3">

                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-2" style="gap: 0.4rem;">
                                <h6 class="mb-0" style="font-size: 0.9rem;">
                                    <i class="fas fa-clipboard-check"></i> Controles del Recordatorio
                                </h6>
                                <?= $this->Html->link(
                                    '<i class="fas fa-plus-circle"></i> Agregar Control',
                                    ['controller' => 'RecordatorioControles', 'action' => 'add', '?' => ['recordatorio_id' => $recordatorioPaciente->id]],
                                    ['class' => 'btn btn-sm btn-success openModal', 'escape' => false]
                                ) ?>
                            </div>

                            <?php if (!empty($recordatorioPaciente->recordatorio_controles)): ?>
                                <div class="mt-1">
                                    <?php foreach ($recordatorioPaciente->recordatorio_controles as $controlRecordatorio): ?>
                                        <div class="card card-sm mb-2" style="background-color: #eef7ff; border-left: 4px solid #0d6efd;">
                                            <div class="card-body p-2">
                                                <div class="d-flex flex-wrap justify-content-between align-items-start" style="gap: 0.4rem;">
                                                    <div style="font-size: 0.82rem;">
                                                        <strong>Control:</strong> <?= $controlRecordatorio->fecha_control ? $controlRecordatorio->fecha_control->format('d-m-Y') : '(sin fecha)' ?><br>
                                                        <?php if (!empty($controlRecordatorio->proximo_control)): ?>
                                                            <small class="text-muted"><strong>Próximo:</strong> <?= $controlRecordatorio->proximo_control->format('d-m-Y') ?></small><br>
                                                        <?php endif; ?>
                                                        <?php if (!empty($controlRecordatorio->detalles)): ?>
                                                            <small class="text-muted"><strong>Detalles:</strong> <?= substr(h($controlRecordatorio->detalles), 0, 50) ?><?= strlen($controlRecordatorio->detalles) > 50 ? '...' : '' ?></small>
                                                        <?php endif; ?>
                                                        <br>
                                                        <small>
                                                            <strong>Estado:</strong> <?= $controlRecordatorio->estado === 'C' ? 'Citado' : ($controlRecordatorio->estado === 'P' ? 'Pendiente' : '-') ?>
                                                        </small><br>
                                                    </div>
                                                    <div class="d-flex flex-wrap align-items-center" style="gap: 0.3rem;">
                                                        <?php if (!empty($telefonoWhatsApp)): ?>
                                                            <?php
                                                                $nombreCompleto = trim(($paciente->nombre ?? '') . ' ' . ($paciente->apellido ?? ''));
                                                                $mensajeWhatsApp = rawurlencode('Hola ' . $nombreCompleto . ', te recordamos tu cita del control ' . ($recordatorioPaciente->titulo ?? ''));
                                                                $whatsappUrl = 'https://wa.me/51' . $telefonoWhatsApp . '?text=' . $mensajeWhatsApp;
                                                            ?>
                                                            <a href="<?= h($whatsappUrl) ?>" target="_blank" class="btn btn-sm btn-success" title="Enviar por WhatsApp">
                                                                <i class="fab fa-whatsapp"></i>
                                                            </a>
                                                        <?php endif; ?>

                                                        <div class="custom-control custom-switch mb-0">
                                                            <input type="checkbox"
                                                                   class="custom-control-input"
                                                                   id="estadoControlSwitch<?= $controlRecordatorio->id ?>"
                                                                   <?= $controlRecordatorio->estado === 'C' ? 'checked' : '' ?>
                                                                   onchange="document.getElementById('openReminderModal<?= $controlRecordatorio->id ?>').click(); this.checked = false;">
                                                            <label class="custom-control-label" for="estadoControlSwitch<?= $controlRecordatorio->id ?>" style="font-size: 0.78rem;">
                                                                <?= $controlRecordatorio->estado === 'C' ? 'Citado' : 'Pendiente' ?>
                                                            </label>
                                                        </div>

                                                        <a id="openReminderModal<?= $controlRecordatorio->id ?>" href="<?= $this->Url->build(['controller' => 'RecordatorioControles', 'action' => 'reminder', $controlRecordatorio->id]) ?>" class="openModal" style="display:none;">Abrir recordatorio</a>

                                                        <?= $this->Html->link(
                                                            '<i class="fas fa-eye"></i>',
                                                            ['controller' => 'RecordatorioControles', 'action' => 'view', $controlRecordatorio->id],
                                                            ['class' => 'btn btn-sm btn-info openModal', 'escape' => false, 'title' => 'Ver']
                                                        ) ?>
                                                        <?= $this->Html->link(
                                                            '<i class="fas fa-edit"></i>',
                                                            ['controller' => 'RecordatorioControles', 'action' => 'edit', $controlRecordatorio->id],
                                                            ['class' => 'btn btn-sm btn-warning openModal', 'escape' => false, 'title' => 'Editar']
                                                        ) ?>
                                                        <?= $this->Form->postLink(
                                                            '<i class="fas fa-trash"></i>',
                                                            ['controller' => 'RecordatorioControles', 'action' => 'delete', $controlRecordatorio->id],
                                                            ['class' => 'btn btn-sm btn-danger','escape' => false,'title' => 'Eliminar','confirm' => '¿Está seguro?']
                                                        ) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0" style="font-size: 0.85rem;">
                                    <em>Sin controles de recordatorio registrados.</em>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php $counterRecordatorio--; ?>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPaginasRecordatorios > 1): ?>
                <nav class="mt-3">
                    <ul class="pagination justify-content-center flex-wrap" style="gap: 0.2rem;">
                        <?php if ($paginaActualRecordatorios > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?pag_recordatorios=1">Primera</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?pag_recordatorios=<?= $paginaActualRecordatorios - 1 ?>">Anterior</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPaginasRecordatorios; $i++): ?>
                            <li class="page-item <?= $i === $paginaActualRecordatorios ? 'active' : '' ?>">
                                <a class="page-link" href="?pag_recordatorios=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($paginaActualRecordatorios < $totalPaginasRecordatorios): ?>
                            <li class="page-item">
                                <a class="page-link" href="?pag_recordatorios=<?= $paginaActualRecordatorios + 1 ?>">Siguiente</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?pag_recordatorios=<?= $totalPaginasRecordatorios ?>">Última</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center mt-5">
                <p class="sin-desborde"><i class="fas fa-exclamation-circle"></i> No hay recordatorios registrados para este paciente.</p>
                <div class="mt-3">
                    <?= $this->Html->link(
                        '+ Crear Recordatorio',
                        ['controller' => 'Recordatorios', 'action' => 'add', '?' => ['paciente_id' => $paciente->id]],
                        ['class' => 'btn btn-info openModal']
                    ) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Pestaña: Comprobantes -->
<div id="comprobantes" class="tab-pane fade">
    <div class="form-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Comprobantes del Paciente</h4>
            <?= $this->Html->link(
                '+ Nuevo Comprobante',
                ['controller' => 'Invoices', 'action' => 'add', '?' => ['paciente_id' => $paciente->id]],
                ['class' => 'btn btn-info', 'target' => '_blank']
            ) ?>
        </div>

        <?php if (!empty($comprobantes)): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Tipo</th>
                            <th>Serie</th>
                            <th>Correlativo</th>
                            <th>Fecha</th>
                            <th class="text-end">Total</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comprobantes as $comprobante): ?>
                            <tr>
                                <td>
                                    <span class="badge <?= $comprobante->tipo_doc === '01' ? 'bg-primary' : 'bg-secondary' ?>">
                                        <?= $comprobante->tipo_doc === '01' ? 'Factura' : 'Boleta' ?>
                                    </span>
                                </td>
                                <td><?= h($comprobante->serie) ?></td>
                                <td><?= h($comprobante->correlativo) ?></td>
                                <td><?= $comprobante->created ? $comprobante->created->format('d/m/Y') : 'N/A' ?></td>
                                <td class="text-end fw-bold">S/ <?= number_format((float)$comprobante->total, 2) ?></td>
                                <td>
                                    <?php
                                    $color = 'secondary';
                                    if ($comprobante->estado === 'ACEPTADO') $color = 'success';
                                    elseif ($comprobante->estado === 'RECHAZADO') $color = 'danger';
                                    elseif ($comprobante->estado === 'PENDIENTE_RESUMEN') $color = 'warning';
                                    elseif ($comprobante->estado === 'EN_RESUMEN') $color = 'info';
                                    elseif ($comprobante->estado === 'RECIBO_INTERNO') $color = 'dark';
                                    ?>
                                    <span class="badge bg-<?= $color ?>">
                                        <?= h($comprobante->estado) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?= $this->Html->link(
                                        '<i class="fas fa-eye"></i>',
                                        ['controller' => 'Invoices', 'action' => 'view', $comprobante->id],
                                        ['class' => 'btn btn-sm btn-info openModal', 'escape' => false, 'title' => 'Ver Comprobante']
                                    ) ?>
                                    <!-- pdf con target-->
                                    <?= $this->Html->link(
                                        '<i class="fas fa-file-pdf"></i>',
                                        ['controller' => 'Invoices', 'action' => 'pdf', $comprobante->id],
                                        ['class' => 'btn btn-sm btn-danger', 'escape' => false, 'target' => '_blank', 'title' => 'Descargar PDF']
                                    ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center mt-5">
                <p class="sin-desborde"><i class="fas fa-exclamation-circle"></i> No hay comprobantes registrados para este paciente.</p>
                <!-- <div class="mt-3">
                    <?= $this->Html->link(
                        '+ Crear Comprobante',
                        ['controller' => 'Invoices', 'action' => 'add', '?' => ['paciente_id' => $paciente->id]],
                        ['class' => 'btn btn-info openModal', 'target' => '_blank']
                    ) ?>
                </div> -->
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- Cierre de Comprobantes -->




</div> <!-- Cierre de tab-content -->
</div> <!-- Cierre de overflow-auto -->

    <div class="mt-5 text-center">
        <!-- Botón para descargar PDF -->
        <a href="<?= $this->Url->build(['action' => 'exportPacientePdf', $paciente->id]) ?>" class="btn btn-primary mb-3">
            Descargar PDF
        </a>
        <!-- Botón para volver -->
        <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-secondary me-2 mb-3">
            Volver
        </a>
        <!-- Botón Editar Odontograma -->
        <?= $this->Html->link(
                    __('Editar'),
                    ['action' => 'edit', $paciente->id],
                    ['class' => 'btn btn-info me-2 mb-3'] // Espaciado horizontal
                ) ?>
    </div>
</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Script para asegurar que las pestañas funcionen correctamente -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var hash = window.location.hash;
        if (hash) {
            var tabTrigger = document.querySelector('a.nav-link[href="' + hash + '"]');
            if (tabTrigger) {
                $(tabTrigger).tab('show');
            }
        }

        // Manejar toggle de recetas con click directo
        document.querySelectorAll('.toggle-recipes').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('data-receta-target');
                const container = document.getElementById(targetId);
                const isHidden = container.style.display === 'none';
                
                if (isHidden) {
                    // Mostrar
                    container.style.display = 'block';
                    this.classList.remove('collapsed');
                    this.setAttribute('aria-expanded', 'true');
                } else {
                    // Ocultar
                    container.style.display = 'none';
                    this.classList.add('collapsed');
                    this.setAttribute('aria-expanded', 'false');
                }
            });
        });
    });
</script>
</body>
</html>
<!-- para que la redireccion funcione -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const hash = window.location.hash;
        if (hash) {
            const tabTrigger = document.querySelector(`a.nav-link[href="${hash}"]`);
            if (tabTrigger) {
                const tab = new bootstrap.Tab(tabTrigger);
                tab.show();
            }
        }
    });
</script>

<script>
    $(document).on('click', '.marcar-hora-llegada', function (e) {
    e.preventDefault();

    const $btn = $(this);
    const citaId = $btn.data('cita-id');
    const horaLlegadaExistente = $btn.data('hora-llegada');
    const fechaHora = $btn.data('fecha-hora');

    const horaActual = new Date().toLocaleTimeString('it-IT'); // HH:mm:ss
    const fechaCita = fechaHora.split(' ')[0];

    // Fecha actual en Lima (UTC -5)
    const fechaActualLima = new Date();
    const fechaLimaFormateada = new Date(fechaActualLima.getTime() - (fechaActualLima.getTimezoneOffset() * 60000));
    const fechaActual = fechaLimaFormateada.toISOString().split('T')[0];

    if (fechaCita !== fechaActual) {
        alert('❌ Solo se puede marcar la hora de llegada para citas del día de hoy.');
        return;
    }

    if (horaLlegadaExistente && horaLlegadaExistente !== 'N/A') {
        const confirmar = confirm('⚠️ Esta cita ya tiene una hora de llegada registrada. ¿Desea actualizarla?');
        if (!confirmar) {
            return;
        }
    }

    $.ajax({
        url: '<?= $this->Url->build(["controller" => "Citas", "action" => "marcarHoraLlegada"]); ?>',
        type: 'POST',
        headers: {
            'X-CSRF-Token': '<?= $this->request->getAttribute("csrfToken"); ?>'
        },
        data: {
            cita_id: citaId,
            hora_llegada: horaActual
        },
        success: function(response) {
            alert('✅ Se registró la hora de llegada: ' + horaActual);
            window.location.href = window.location.pathname + '#citas';

        },
        error: function() {
            alert('❌ Error al registrar la hora de llegada.');
        }
    });
});

</script>
<script>
    $(document).on('click', '.cambiar-estado', function (e) {
        e.preventDefault();

        const citaId = $(this).data('cita-id');
        const nuevoEstado = $(this).data('estado');

        if (!citaId || !nuevoEstado) {
            alert('❌ No se pudo identificar la cita o el estado.');
            return;
        }

        $.ajax({
            url: '<?= $this->Url->build(['controller' => 'Citas', 'action' => 'changeStatus']); ?>',
            type: 'POST',
            headers: {
                'X-CSRF-Token': '<?= $this->request->getAttribute("csrfToken"); ?>'
            },
            data: { id: citaId, estado: nuevoEstado },
            success: function (response) {
                if (response.success) {
                    alert(`✅ Estado actualizado a: ${nuevoEstado.replace('_', ' ')}`);
                    window.location.href = window.location.pathname + '#citas';
                } else {
                    alert('❌ No se pudo actualizar el estado. Inténtalo de nuevo.');
                }
            },
            error: function () {
                alert('❌ Error al actualizar el estado.');
            }
        });
    });
</script>
<script>
    // Modal grande (openModal)
$('.openModal').on('click', function(e) {
    e.preventDefault();
    var url = $(this).attr('href');
    $.ajax({
        url: url,
        success: function(response) {
            $('#modalContentLg').html(response);
            
            // Abrir modal bloqueando clic fuera y ESC
            $('#modalLg').modal({
                backdrop: 'static',
                 // keyboard: false
            }).modal('show');

            initializePacienteSearch(); // Inicializar el buscador
        },
        error: function() {
            alert('Error al cargar el contenido para el modal grande.');
        }
    });
});

// Activar tab si está en la URL (ej: #ficha_facial)
$(document).ready(function(){
    var hash = window.location.hash;
    if (hash) {
        // Encontrar el tab link que coincida con el hash
        $('a[href="' + hash + '"]').tab('show');
    }
});

</script>

