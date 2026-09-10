<?php

/**
 * Configuración de Permisos por Ruta
 * Define qué acciones requieren verificación de permisos
 *
 * Formato:
 * 'Controller' => ['action1', 'action2'] // Requieren permisos
 * 'Controller' => [] // Todas las acciones requieren permisos (excepto except)
 */

return [
    // ===== ACCIONES LIBRES (SIN VERIFICACIÓN) =====
    // Estas acciones NO requieren estar en la BD ni ser verificadas
    'except' => [
        // Autenticación
        'Users' => ['login', 'logout'],
        'Pages' => ['display', 'topVendidosMes'],
        'Error' => ['index', 'notFound'],

        // BÚSQUEDAS AJAX (Helpers secundarios)
        //'Pacientes' => ['buscarPaciente'],
        'Pacientes' => [
            'buscarPaciente',
            'buscarPacientesSelect',
            'historial',
            'historialfactura',
            'exportPacientePdf'
        ],

        'Medicamentos' => ['buscar'],
        'Examenes' => ['buscar'],
        'IngresosMercaderia' => ['buscarProducto'],
        'RecetasMedicamentos' => ['buscarMedicamentos'],
        'Recetas' => ['getByConsulta'],
        'Consultas' => ['buscarCie', 'exportConsultasPdf'],

        // OBTENER DATOS (Helpers para UI)
        'HorariosDoctores' => ['obtenerHorarios'],
        'Citas' => ['obtenerHorarios', 'citaDiaria', 'fetchCitas', 'marcarHoraLlegada', 'restablecerCita', 'changeStatus', 'actualizarHora', 'verificarDisponibilidad', 'actualizarTablaCitas', 'exportarReportePdf', 'exportarReporteExcel','registrarInicio','finalizarConsulta', 'fetchEspecialistas', 'moverCita'],
        'HorariosBloqueos' => ['obtenerBloqueosPorFecha'],
        'FormasFarmaceuticas' => ['getAll'],
        'ViasAdministracion' => ['getAll'],

        // BÚSQUEDAS Y DATOS PARA PAQUETES DE PAGO
        'PaquetesPagos' => ['buscarPaquetes', 'recordatoriosPago'],

        // EXPORTAR REPORTES (Acceso libre)
        'VistaConsultasProcedimientos' => ['exportarPdf', 'exportarExcel', 'index'],
        'VistaRecetasDepartamentos' => ['exportarPdf', 'exportarExcel', 'index'],
        'VistaReporteConsultasDoctores' => ['exportarPdf', 'exportarExcel', 'index'],
        'VistaReportePacientes' => ['exportarPdf', 'exportarExcel', 'index'],
        'Presupuestos' => ['exportPresupuestoPdf'],
        'RecordatorioControles' => ['exportarPdf','getByRecordatorio','toggleEstado','reminder'],
        // Permitir ver recordatorio de cumpleaños sin verificación en BD
        'HistoriasClinicas' => ['cumpleanos_recordatorio'],
    ],

    // ===== ACCIONES RESTRINGIDAS (REQUIEREN PERMISO EN BD) =====
    // Solo módulos principales con control de permisos
    'require' => [
        'Campañas' => [],
        'Cajas' => [],
        'Categorias' => [],
        'CategoriasExamenes' => [],
        'CategoriasProductos' => [],
        'Citas' => [],
        'Consultas' => [],
        'Departamentos' => [],
        'Doctores' => [],
        'Documentos' => [],
        'Examenes' => [],
        'ExamenesFisicos' => [],
        'HistoriasClinicas' => [],
        'HorariosBloqueos' => [],
        'HorariosDoctores' => [],
        'IngresosMercaderia' => [],
        'Medicamentos' => [],
        'PaquetesPagos' => [],
        'RecordatorioControles' => [],
        'Recordatorios' => [],
        'Pacientes' => [],
        'PagosDoctores' => [],
        'Permisos' => [],
        'Presupuestos' => [],
        'Procedimientos' => [],
        'Productos' => [],
        'ProductoMovimientos' => [],
        'Recetas' => [],
        'Roles' => [],
        'Tratamientos' => [],
        'Users' => [],
        'ViasAdministracion' => [],
        'VistaConsultasProcedimientos' => [],
        'VistaRecetasDepartamentos' => [],
        'VistaReporteConsultasDoctores' => [],
        'VistaReportePacientes' => [],
    ],
];
