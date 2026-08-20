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

</div>

   
    <div class="mt-4 overflow-auto" style="white-space: nowrap;">
    <?php $this->loadHelper('Permisos'); ?>
    <ul class="nav nav-tabs mt-4 flex-column flex-sm-row">
    <li class="nav-item">
        <a class="nav-link active text-center mb-2 mx-2" data-toggle="tab" href="#datos">Datos administrativos</a>
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

