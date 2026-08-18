<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?= $this->Html->meta('icon', 'dientito.png', ['type' => 'icon']) ?>
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?= $this->fetch('title') ?> - PoliDent</title>

    <!-- Custom fonts for this template-->
    <link rel="stylesheet" href="<?= $this->Url->assetUrl('vendor/fontawesome-free/css/all.min.css') ?>" type="text/css">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link rel="stylesheet" href="<?= $this->Url->assetUrl('css/sb-admin-2.min.css') ?>">

    <?= $this->fetch('css') ?>
    <!-- Se añadio la clase openModal para crear modales de manera global -->
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-info sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'home']) ?>">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">PoliDent</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Navigation items for entities -->
            <li class="nav-item">
                <?= $this->Html->link(
                    '<i class="fas fa-users"></i><span>Pacientes</span>',
                    ['controller' => 'Pacientes', 'action' => 'index'],
                    ['class' => 'nav-link', 'escape' => false]
                ) ?>
            </li>
            <li class="nav-item">
                <?= $this->Html->link(
                    '<i class="fas fa-file-invoice"></i><span>Presupuestos</span>',
                    ['controller' => 'Presupuestos', 'action' => 'index'],
                    ['class' => 'nav-link', 'escape' => false]
                ) ?>
            </li>
            <li class="nav-item">
                <?= $this->Html->link(
                    '<i class="fas fa-cogs"></i><span>Tratamientos</span>',
                    ['controller' => 'Tratamientos', 'action' => 'index'],
                    ['class' => 'nav-link', 'escape' => false]
                ) ?>
            </li>

            <li class="nav-item">
                <?= $this->Html->link(
                    '<i class="fas fa-tooth"></i><span>Odontograma</span>',
                    ['controller' => 'Odontograma', 'action' => 'index'],
                    ['class' => 'nav-link', 'escape' => false]
                ) ?>
            </li>
            <li class="nav-item">
                <?= $this->Html->link(
                    '<i class="fas fa-user"></i><span>Doctores</span>',
                    ['controller' => 'Doctores', 'action' => 'index'],
                    ['class' => 'nav-link', 'escape' => false]
                ) ?>
            </li>
            <li class="nav-item">
                <?= $this->Html->link(
                    '<i class="fas fa-user"></i><span>Citas</span>',
                    ['controller' => 'Citas', 'action' => 'index'],
                    ['class' => 'nav-link', 'escape' => false]
                ) ?>
            </li>
            <li class="nav-item">
                <?= $this->Html->link(
                    '<i class="fas fa-user"></i><span>Consultas</span>',
                    ['controller' => 'registrosConsultas', 'action' => 'index'],
                    ['class' => 'nav-link', 'escape' => false]
                ) ?>
            </li>
            <li class="nav-item">
                <?= $this->Html->link(
                    '<i class="fas fa-user"></i><span>Usuarios</span>',
                    ['controller' => 'Users', 'action' => 'index'],
                    ['class' => 'nav-link', 'escape' => false]
                ) ?>
            </li>
            <li class="nav-item">
                <?= $this->Html->link(
                    '<i class="fas fa-user"></i><span>Reportes Presupuestos</span>',
                    ['controller' => 'ReportesPacientes', 'action' => 'index'],
                    ['class' => 'nav-link', 'escape' => false]
                ) ?>
            </li>

            <li class="nav-item">
                <?= $this->Html->link(
                    '<i class="fas fa-user"></i><span>Reportes Tratamiento</span>',
                    ['controller' => 'ReportesTratamientos', 'action' => 'index'],
                    ['class' => 'nav-link', 'escape' => false]
                ) ?>
            </li>
            <li class="nav-item">
                <?= $this->Html->link(
                    '<i class="fas fa-calendar-alt"></i><span>Reporte de Citas</span>',
                    ['controller' => 'Citas', 'action' => 'reportecitas'],
                    ['class' => 'nav-link', 'escape' => false]
                ) ?>
            </li>
            <li class="nav-item">
                <?= $this->Html->link(
                    '<i class="fas fa-calendar-alt"></i><span>Subida de imágenes</span>',
                    ['controller' => 'ArchivosPacientes', 'action' => 'index'], // Especifica explícitamente la acción 'index'
                    ['class' => 'nav-link', 'escape' => false]
                ) ?>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
             
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button> -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3 fixed-top-left">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- User Information -->
                        <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">PoliDent</span>
                            <img class="img-profile rounded-circle" src="<?= $this->Url->build('/dientito.png') ?>">
                        </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <?= $this->Html->link(
                                    '<i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Cerrar Sesión',
                                    ['controller' => 'Users', 'action' => 'logout'],
                                    ['class' => 'dropdown-item', 'escape' => false]
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <div class="container-fluid">
                    <?= $this->fetch('content') ?>
                </div>
            </div>

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Sistema para clinica dental PoliDent</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <a class="scroll-to-top rounded bg-primary " href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

        <!-- Modal Pequeño -->
        <div class="modal fade" id="modalSm" tabindex="-1" role="dialog" aria-labelledby="modalLabelSm" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="modalLabelLg">
                <img src="<?= $this->Url->image('logoClinica.png') ?>" alt="Logo Clínica" style="max-height: 50px;">
            </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modalContentSm"></div> <!-- Contenedor para contenido dinámico -->
            </div>
            </div>
        </div>
        </div>

        <!-- Modal Mediano -->
        <div class="modal fade" id="modalMd" tabindex="-1" role="dialog" aria-labelledby="modalLabelMd" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="modalLabelLg">
                <img src="<?= $this->Url->image('logoClinica.png') ?>" alt="Logo Clínica" style="max-height: 50px;">
            </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modalContentMd"></div> <!-- Contenedor para contenido dinámico -->
            </div>
            </div>
        </div>
        </div>

        <!-- Modal Grande -->
        <div class="modal fade" id="modalLg" tabindex="-1" role="dialog" aria-labelledby="modalLabelLg" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="modalLabelLg">
                <img src="<?= $this->Url->image('logoClinica.png') ?>" alt="Logo Clínica" style="max-height: 50px;">
            </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modalContentLg"></div> <!-- Contenedor para contenido dinámico -->
            </div>
            </div>
        </div>
        </div>

        <!-- Modal Extra Grande -->
        <div class="modal fade" id="modalXl" tabindex="-1" role="dialog" aria-labelledby="modalLabelXl" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabelLg">
                    <img src="<?= $this->Url->image('logoClinica.png') ?>" alt="Logo Clínica" style="max-height: 50px;">
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modalContentXl"></div> <!-- Contenedor para contenido dinámico -->
            </div>
            </div>
        </div>
        </div>
            

    <!-- jQuery and Bootstrap scripts -->
    <script src="<?= $this->Url->assetUrl('vendor/jquery/jquery.min.js') ?>"></script>
    <script src="<?= $this->Url->assetUrl('vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= $this->Url->assetUrl('vendor/jquery-easing/jquery.easing.min.js') ?>"></script>
    <script src="<?= $this->Url->assetUrl('js/sb-admin-2.min.js') ?>"></script>
    <script src="<?= $this->Url->assetUrl('vendor/chart.js/Chart.min.js') ?>"></script>
    <!-- generales -->
    
    
    <script>
        $(document).ready(function() {
    // Modal pequeño
    $('.openModalSm').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            success: function(response) {
                $('#modalContentSm').html(response);
                $('#modalSm').modal('show');
            },
            error: function() {
                alert('Error al cargar el contenido para el modal pequeño.');
            }
        });
    });

    // Modal mediano
    $('.openModalMd').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            success: function(response) {
                $('#modalContentMd').html(response);
                $('#modalMd').modal('show');
            },
            error: function() {
                alert('Error al cargar el contenido para el modal mediano.');
            }
        });
    });

    // Modal grande (openModal)
    $('.openModal').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            success: function(response) {
                $('#modalContentLg').html(response);
                $('#modalLg').modal('show');
                initializePacienteSearch(); // Inicializar el buscador
            },
            error: function() {
                alert('Error al cargar el contenido para el modal grande.');
            }
        });
    });



    // Modal extra grande
    $('.openModalXl').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            success: function(response) {
                $('#modalContentXl').html(response);
                $('#modalXl').modal('show');
            },
            error: function() {
                alert('Error al cargar el contenido para el modal extra grande.');
            }
        });
    });
});


    </script>

</body>

</html>
<!-- Estilos CSS adicionales -->
<style>
    .table{
        border-radius: 10px;
    }
    .table th, .table td {
        white-space: nowrap;
    }
    
    thead th a {
        color: white !important;
        text-decoration: none;
    }

    .actions .skiti {
        visibility: visible !important;
        font-size: 16px;
        padding: 5px;
        color: #f1f1f1;
       
        
    }

    /* Estilo para la columna de acciones */
    .table .actions {
        position: sticky;
        right: 0;
        background-color: #111;

        text-align: center;
        z-index: 10;
    }  
</style>
<script>
    // Inicializa el buscador de pacientes
    function initializePacienteSearch() {
        const basePath = "<?= $this->Url->build('/', ['fullBase' => true]) ?>"; // URL base completa
        const searchInput = document.getElementById('searchPaciente');
        const searchButton = document.getElementById('searchButton');
        const resultsContainer = document.getElementById('pacienteResults');
        const pacienteIdField = document.getElementById('pacienteId');

        // Verificar que los elementos del buscador existan
        if (!searchInput || !searchButton || !resultsContainer || !pacienteIdField) {
            console.warn('Elementos del buscador no encontrados.');
            return;
        }

        // Manejar el botón "Buscar"
        searchButton.addEventListener('click', function () {
            const query = searchInput.value.trim();
            if (query.length < 2) {
                resultsContainer.innerHTML = '<div class="list-group-item text-danger">Por favor, ingrese al menos 2 caracteres.</div>';
                return;
            }

            // Realizar la solicitud AJAX para buscar pacientes
            fetch(`${basePath}pacientes/buscarPaciente?q=${encodeURIComponent(query)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    resultsContainer.innerHTML = ''; // Limpiar resultados previos

                    if (data.length === 0) {
                        resultsContainer.innerHTML = '<div class="list-group-item">No se encontraron resultados</div>';
                        return;
                    }

                    // Mostrar resultados
                    data.forEach(paciente => {
                        const item = document.createElement('div');
                        item.className = 'list-group-item list-group-item-action';
                        item.textContent = paciente.nombre;
                        item.addEventListener('click', () => {
                            searchInput.value = paciente.nombre; // Mostrar nombre seleccionado
                            pacienteIdField.value = paciente.id; // Guardar ID seleccionado
                            resultsContainer.innerHTML = ''; // Limpiar resultados
                        });
                        resultsContainer.appendChild(item);
                    });
                })
                .catch(error => {
                    console.error('Error al buscar pacientes:', error);
                    resultsContainer.innerHTML = '<div class="list-group-item text-danger">Error al buscar pacientes</div>';
                });
        });
    }

    // Inicializa la validación del formulario
    function initializeFormValidation() {
        const pacienteIdField = document.getElementById('pacienteId');
        const submitButton = document.getElementById('submitButton');

        // Verificar que los elementos del formulario existan
        if (!pacienteIdField || !submitButton) {
            console.warn('Elementos de validación no encontrados.');
            return;
        }

        // Habilitar o deshabilitar el botón "Guardar" según el valor de pacienteId
        const validateForm = () => {
            submitButton.disabled = pacienteIdField.value.trim() === '';
        };

        // Validar el formulario cuando se selecciona un paciente
        document.getElementById('pacienteResults').addEventListener('click', function (event) {
            if (event.target && event.target.classList.contains('list-group-item')) {
                validateForm();
            }
        });

        // Validar el formulario al cargar la página o modal
        validateForm();
    }

    // Inicializar las funcionalidades al cargar la página
    document.addEventListener('DOMContentLoaded', function () {
        initializePacienteSearch();
        initializeFormValidation();
    });

    // Reutilizar la inicialización para modales cargados dinámicamente
    $(document).on('shown.bs.modal', '#modalLg', function () {
        initializePacienteSearch();
        initializeFormValidation();
    });
</script>



<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= $this->Url->build(['controller' => 'Pages', 'action' => 'home']) ?>" class="brand-link">
        <img src="dist/img/AdminLTELogo.png" alt="PoliDent Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">PoliDent</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">Alexander Pierce</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Custom Navigation Items -->
                <li class="nav-item">
                    <?= $this->Html->link(
                        '<i class="fas fa-users"></i><span>Pacientes</span>',
                        ['controller' => 'Pacientes', 'action' => 'index'],
                        ['class' => 'nav-link', 'escape' => false]
                    ) ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->link(
                        '<i class="fas fa-file-invoice"></i><span>Presupuestos</span>',
                        ['controller' => 'Presupuestos', 'action' => 'index'],
                        ['class' => 'nav-link', 'escape' => false]
                    ) ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->link(
                        '<i class="fas fa-cogs"></i><span>Tratamientos</span>',
                        ['controller' => 'Tratamientos', 'action' => 'index'],
                        ['class' => 'nav-link', 'escape' => false]
                    ) ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->link(
                        '<i class="fas fa-tooth"></i><span>Odontograma</span>',
                        ['controller' => 'Odontograma', 'action' => 'index'],
                        ['class' => 'nav-link', 'escape' => false]
                    ) ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->link(
                        '<i class="fas fa-user"></i><span>Doctores</span>',
                        ['controller' => 'Doctores', 'action' => 'index'],
                        ['class' => 'nav-link', 'escape' => false]
                    ) ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->link(
                        '<i class="fas fa-user"></i><span>Citas</span>',
                        ['controller' => 'Citas', 'action' => 'index'],
                        ['class' => 'nav-link', 'escape' => false]
                    ) ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->link(
                        '<i class="fas fa-user"></i><span>Consultas</span>',
                        ['controller' => 'registrosConsultas', 'action' => 'index'],
                        ['class' => 'nav-link', 'escape' => false]
                    ) ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->link(
                        '<i class="fas fa-user"></i><span>Usuarios</span>',
                        ['controller' => 'Users', 'action' => 'index'],
                        ['class' => 'nav-link', 'escape' => false]
                    ) ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->link(
                        '<i class="fas fa-user"></i><span>Reportes Presupuestos</span>',
                        ['controller' => 'ReportesPacientes', 'action' => 'index'],
                        ['class' => 'nav-link', 'escape' => false]
                    ) ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->link(
                        '<i class="fas fa-user"></i><span>Reportes Tratamiento</span>',
                        ['controller' => 'ReportesTratamientos', 'action' => 'index'],
                        ['class' => 'nav-link', 'escape' => false]
                    ) ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->link(
                        '<i class="fas fa-calendar-alt"></i><span>Reporte de Citas</span>',
                        ['controller' => 'Citas', 'action' => 'reportecitas'],
                        ['class' => 'nav-link', 'escape' => false]
                    ) ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->link(
                        '<i class="fas fa-calendar-alt"></i><span>Subida de imágenes</span>',
                        ['controller' => 'ArchivosPacientes', 'action' => 'index'],
                        ['class' => 'nav-link', 'escape' => false]
                    ) ?>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>