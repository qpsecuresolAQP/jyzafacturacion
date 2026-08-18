<?php

declare(strict_types=1);

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Cake\ORM\TableRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class VistaReporteConsultasDoctoresController extends AppController
{
    private $VistaReporteConsultasDoctores;

    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $currentAction = $this->request->getParam('action');
        
        $this->verificarPermisoORedireccionar('VistaReporteConsultasDoctores', $currentAction);
    }

    public function initialize(): void
    {
        parent::initialize();
        $this->VistaReporteConsultasDoctores = $this->fetchTable('VistaReporteConsultasDoctores');
    }

    public function index()
    {
        $roles = [1]; // Ajustar según los roles permitidos
        if (!in_array($this->request->getAttribute('identity')->rol, $roles)) {
            $this->Flash->error(__('Acceso no autorizado.'));
            return $this->redirect($this->referer());
        }

        // Cargar lista de doctores
        $doctoresTable = $this->fetchTable('Doctores');
        $doctores = $doctoresTable->find('list', keyField: 'id', valueField: 'nombre')->toArray();

        // Obtener filtros
        $doctorId = $this->request->getQuery('doctor_id');
        $startDate = $this->request->getQuery('start_date');
        $endDate = $this->request->getQuery('end_date');

        // Construcción de la consulta
        $query = $this->VistaReporteConsultasDoctores->find();

        // Filtrar por fecha de consulta
        if (!empty($startDate) && !empty($endDate)) {
            try {
                $fechaInicio = new \DateTime($startDate, new \DateTimeZone('America/Lima'));
                $fechaFin = new \DateTime($endDate, new \DateTimeZone('America/Lima'));
                $fechaFin->modify('+1 day -1 second');

                $query->where([
                    'fecha_consulta >=' => $fechaInicio->format('Y-m-d H:i:s'),
                    'fecha_consulta <=' => $fechaFin->format('Y-m-d H:i:s'),
                ]);
            } catch (\Exception $e) {
                $this->Flash->error('Formato de fechas inválido.');
                return $this->redirect($this->referer());
            }
        }

        // Filtro por Doctor
        if (!empty($doctorId)) {
            $query->where(['doctor_id' => $doctorId]);
        }

        $reportes = $this->paginate($query);
        $this->set(compact('reportes', 'doctores', 'doctorId', 'startDate', 'endDate'));
    }

    public function exportarPdf()
    {
        // Obtener parámetros
        $doctorId = $this->request->getQuery('doctor_id');
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
        $query = $this->VistaReporteConsultasDoctores->find()
            ->where([
                'fecha_consulta >=' => $fechaInicio->format('Y-m-d H:i:s'),
                'fecha_consulta <=' => $fechaFin->format('Y-m-d H:i:s'),
            ]);

        if (!empty($doctorId)) {
            $query->where(['doctor_id' => $doctorId]);
        }

        $reportes = $query->toArray();

        // 🟢 Obtener cantidad de consultas por doctor
        $doctoresQuery = $this->VistaReporteConsultasDoctores->find()
            ->select([
                'doctor_id',
                'nombre' => 'doctor_nombre',
                'total_consultas' => $query->func()->count('*')
            ])
            ->where([
                'fecha_consulta >=' => $fechaInicio->format('Y-m-d H:i:s'),
                'fecha_consulta <=' => $fechaFin->format('Y-m-d H:i:s'),
            ]);

        if (!empty($doctorId)) {
            $doctoresQuery->where(['doctor_id' => $doctorId]);
        }

        $doctoresQuery->group(['doctor_id', 'doctor_nombre']);
        $doctores = $doctoresQuery->toArray();

        // Renderizar PDF
        $this->viewBuilder()->disableAutoLayout();
        $this->set(compact('reportes', 'doctores', 'startDate', 'endDate'));
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
        $doctorId = $this->request->getQuery('doctor_id');
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
        $query = $this->VistaReporteConsultasDoctores->find()
            ->where([
                'fecha_consulta >=' => $fechaInicio->format('Y-m-d H:i:s'),
                'fecha_consulta <=' => $fechaFin->format('Y-m-d H:i:s'),
            ]);

        if (!empty($doctorId)) {
            $query->where(['doctor_id' => $doctorId]);
        }

        $reportes = $query->toArray();

        // Crear archivo Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Reporte Consultas");

        // Encabezados
        $sheet->setCellValue('A1', 'Doctor');
        $sheet->setCellValue('B1', 'Fecha de Consulta');
        $sheet->setCellValue('C1', 'Total de Consultas');

        // Llenar datos
        $row = 2;
        foreach ($reportes as $reporte) {
            $sheet->setCellValue('A' . $row, $reporte->doctor_nombre);
            $sheet->setCellValue('B' . $row, $reporte->fecha_consulta);
            $sheet->setCellValue('C' . $row, $reporte->total_consultas);
            $row++;
        }

        // Descargar el archivo
        $writer = new Xlsx($spreadsheet);
        $fileName = 'reporte_consultas.xlsx';

        $response = $this->response->withType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withDownload($fileName);

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return $response->withStringBody($excelOutput);
    }
}

