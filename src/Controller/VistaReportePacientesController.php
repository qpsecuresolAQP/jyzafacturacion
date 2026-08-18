<?php

declare(strict_types=1);

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class VistaReportePacientesController extends AppController
{
    private $VistaReportePacientes;

    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $currentAction = $this->request->getParam('action');
        
        $this->verificarPermisoORedireccionar('VistaReportePacientes', $currentAction);
    }

    public function initialize(): void
    {
        parent::initialize();
        $this->VistaReportePacientes = $this->fetchTable('VistaReportePacientes');
    }

    public function index()
    {
        // Cargar listas de usuarios y departamentos
        $usuariosTable = $this->fetchTable('Users');
        $departamentosTable = $this->fetchTable('Departamentos');

        $usuarios = $usuariosTable->find('list', keyField: 'id', valueField: 'username')->toArray();
        $departamentos = $departamentosTable->find('list', keyField: 'id', valueField: 'nombre')->toArray();

        // Obtener filtros
        $usuarioId = $this->request->getQuery('usuario_id');
        $departamentoId = $this->request->getQuery('departamento_id');
        $startDate = $this->request->getQuery('start_date');
        $endDate = $this->request->getQuery('end_date');

        // Construcción de la consulta
        $query = $this->VistaReportePacientes->find();

        // Filtrar por fecha de modificación
        if (!empty($startDate) && !empty($endDate)) {
            try {
                $fechaInicio = new \DateTime($startDate, new \DateTimeZone('America/Lima'));
                $fechaFin = new \DateTime($endDate, new \DateTimeZone('America/Lima'));
                $fechaFin->modify('+1 day -1 second');

                $query->where([
                    'modified >=' => $fechaInicio->format('Y-m-d H:i:s'),
                    'modified <=' => $fechaFin->format('Y-m-d H:i:s'),
                ]);
            } catch (\Exception $e) {
                $this->Flash->error('Formato de fechas inválido.');
                return $this->redirect($this->referer());
            }
        }

        // Filtro por Usuario (quién registró al paciente)
        if (!empty($usuarioId)) {
            $query->where(['usuario_id' => $usuarioId]);
        }

        // Filtro por Departamento del paciente
        if (!empty($departamentoId)) {
            $query->where(['departamento_id' => $departamentoId]);
        }

        $reportes = $this->paginate($query);

        $this->set(compact('reportes', 'usuarios', 'departamentos', 'usuarioId', 'departamentoId', 'startDate', 'endDate'));
    }

    public function exportarPdf()
    {
        // Obtener parámetros
        $usuarioId = $this->request->getQuery('usuario_id');
        $departamentoId = $this->request->getQuery('departamento_id');
        $startDate = $this->request->getQuery('start_date');
        $endDate = $this->request->getQuery('end_date');

        if (empty($startDate) || empty($endDate)) {
            $this->Flash->error('Por favor, ingrese ambas fechas: Fecha de Inicio y Fecha Fin.');
            return $this->redirect(['action' => 'index']);
        }

        if ($startDate > $endDate) {
            $this->Flash->error('La Fecha de Inicio no puede ser mayor que la Fecha Fin.');
            return $this->redirect(['action' => 'index']);
        }

        try {
            $fechaInicio = new \DateTime($startDate, new \DateTimeZone('America/Lima'));
            $fechaFin = new \DateTime($endDate, new \DateTimeZone('America/Lima'));
            $fechaFin->modify('+1 day -1 second');
        } catch (\Exception $e) {
            $this->Flash->error('Formato de fechas inválido.');
            return $this->redirect(['action' => 'index']);
        }

        // Obtener reportes desde la vista
        $query = $this->VistaReportePacientes->find()
            ->where([
                'modified >=' => $fechaInicio->format('Y-m-d H:i:s'),
                'modified <=' => $fechaFin->format('Y-m-d H:i:s'),
            ]);

        // Aplicar filtro por usuario_id si está presente
        if (!empty($usuarioId)) {
            $query->where(['usuario_id' => $usuarioId]);
        }

        // Aplicar filtro por departamento_id si está presente
        if (!empty($departamentoId)) {
            $query->where(['departamento_id' => $departamentoId]);
        }

        $reportes = $query->toArray();

        // Obtener la cantidad de pacientes por departamento
        $departamentosQuery = $this->VistaReportePacientes->find()
            ->select([
                'departamento_id',
                'nombre' => 'VistaReportePacientes.nombre_departamento',
                'total_pacientes' => $query->func()->count('*')
            ])
            ->where([
                'modified >=' => $fechaInicio->format('Y-m-d H:i:s'),
                'modified <=' => $fechaFin->format('Y-m-d H:i:s'),
            ]);

        // Aplicar filtro por departamento_id si está presente
        if (!empty($departamentoId)) {
            $departamentosQuery->where(['departamento_id' => $departamentoId]);
        }

        $departamentosQuery->group(['departamento_id', 'VistaReportePacientes.nombre_departamento']);
        $departamentos = $departamentosQuery->toArray();

        // Obtener la cantidad de pacientes registrados por usuario
        $usuariosQuery = $this->VistaReportePacientes->find()
            ->select([
                'usuario_id',
                'nombre' => 'VistaReportePacientes.nombre_usuario',
                'total_pacientes' => $query->func()->count('*')
            ])
            ->where([
                'modified >=' => $fechaInicio->format('Y-m-d H:i:s'),
                'modified <=' => $fechaFin->format('Y-m-d H:i:s'),
            ]);

        // Aplicar filtro por usuario_id si está presente
        if (!empty($usuarioId)) {
            $usuariosQuery->where(['usuario_id' => $usuarioId]);
        }

        $usuariosQuery->group(['usuario_id', 'VistaReportePacientes.nombre_usuario']);
        $usuarios = $usuariosQuery->toArray();

        // Renderizar PDF
        $this->viewBuilder()->disableAutoLayout();
        $this->set(compact('reportes', 'departamentos', 'usuarios', 'startDate', 'endDate'));
        $html = $this->render('export_pdf')->getBody()->__toString();

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->response
            ->withType('application/pdf')
            ->withStringBody($dompdf->output());
    }

    public function exportarExcel()
    {
        $this->autoRender = false; // Evita que CakePHP busque una vista
    
        $usuarioId = $this->request->getQuery('usuario_id');
        $departamentoId = $this->request->getQuery('departamento_id');
        $startDate = $this->request->getQuery('start_date');
        $endDate = $this->request->getQuery('end_date');
    
        if (empty($startDate) || empty($endDate)) {
            $this->Flash->error('Por favor, ingrese ambas fechas: Fecha de Inicio y Fecha Fin.');
            return $this->redirect(['action' => 'index']);
        }
    
        if ($startDate > $endDate) {
            $this->Flash->error('La Fecha de Inicio no puede ser mayor que la Fecha Fin.');
            return $this->redirect(['action' => 'index']);
        }
    
        try {
            $fechaInicio = new \DateTime($startDate, new \DateTimeZone('America/Lima'));
            $fechaFin = new \DateTime($endDate, new \DateTimeZone('America/Lima'));
            $fechaFin->modify('+1 day -1 second');
        } catch (\Exception $e) {
            $this->Flash->error('Formato de fechas inválido.');
            return $this->redirect(['action' => 'index']);
        }
    
        // Construcción de la consulta asegurando que si no hay filtros, se obtienen todos los datos
        $query = $this->VistaReportePacientes->find()
            ->where([
                'modified >=' => $fechaInicio->format('Y-m-d H:i:s'),
                'modified <=' => $fechaFin->format('Y-m-d H:i:s'),
            ]);
    
        if (!empty($usuarioId)) {
            $query->where(['usuario_id' => $usuarioId]);
        }
    
        if (!empty($departamentoId)) {
            $query->where(['departamento_id' => $departamentoId]);
        }
    
        $reportes = $query->toArray();
    
        if (empty($reportes)) {
            $this->Flash->error('No se encontraron registros para exportar.');
            return $this->redirect(['action' => 'index']);
        }
    
        // Crear archivo de Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte de Pacientes');
    
        // Encabezados
        $headers = ['Nombre', 'Apellido', 'Fecha Modificación', 'Usuario Registró', 'Departamento'];
        $sheet->fromArray($headers, null, 'A1');
    
        // Aplicar negrita a los encabezados
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
    
        // Llenar datos
        $row = 2;
        foreach ($reportes as $reporte) {
            $sheet->setCellValue("A$row", $reporte->nombre_paciente);
            $sheet->setCellValue("B$row", $reporte->apellido_paciente);
            $sheet->setCellValue("C$row", $reporte->modified);
            $sheet->setCellValue("D$row", $reporte->nombre_usuario);
            $sheet->setCellValue("E$row", $reporte->nombre_departamento);
            $row++;
        }
    
        // Ajustar tamaño de columnas automáticamente
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    
        // Generar archivo
        $fileName = 'Reporte_Pacientes.xlsx';
        $tempFile = TMP . $fileName;
    
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);
    
        // Forzar descarga
        return $this->response
            ->withType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->withFile($tempFile, ['download' => true, 'delete' => true]);
    }    
}

