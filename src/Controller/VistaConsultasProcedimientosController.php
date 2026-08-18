<?php

declare(strict_types=1);

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class VistaConsultasProcedimientosController extends AppController
{
    private $VistaConsultasProcedimientos;

    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $currentAction = $this->request->getParam('action');
        
        $this->verificarPermisoORedireccionar('VistaConsultasProcedimientos', $currentAction);
    }

    public function initialize(): void
    {
        parent::initialize();
        $this->VistaConsultasProcedimientos = $this->fetchTable('VistaConsultasProcedimientos');
    }

    public function index()
    {
        $tipo = $this->request->getQuery('tipo'); // 'consulta' o 'procedimiento'
        $dni = $this->request->getQuery('dni');
        $startDate = $this->request->getQuery('start_date');
        $endDate = $this->request->getQuery('end_date');

        $query = $this->VistaConsultasProcedimientos->find();

        if (!empty($tipo)) {
            if ($tipo === 'consulta') {
                $query->where(['motivo IS NOT' => null]);
            } elseif ($tipo === 'procedimiento') {
                $query->where(['procedimiento IS NOT' => null]);
            }
        }

        if (!empty($dni)) {
            $query->where(['dni LIKE' => '%' . $dni . '%']);
        }

        if (!empty($startDate) && !empty($endDate)) {
            try {
                $fechaInicio = new \DateTime($startDate, new \DateTimeZone('America/Lima'));
                $fechaFin = new \DateTime($endDate, new \DateTimeZone('America/Lima'));
                $fechaFin->modify('+1 day -1 second');

                $query->where([
                    'fecha_registro >=' => $fechaInicio->format('Y-m-d H:i:s'),
                    'fecha_registro <=' => $fechaFin->format('Y-m-d H:i:s'),
                ]);
            } catch (\Exception $e) {
                $this->Flash->error('Formato de fechas inválido.');
                return $this->redirect($this->referer());
            }
        }

        $reportes = $this->paginate($query);
        $this->set(compact('reportes', 'tipo', 'dni', 'startDate', 'endDate'));
    }

    public function exportarPdf()
    {
        $tipo = $this->request->getQuery('tipo');
        $dni = $this->request->getQuery('dni');
        $startDate = $this->request->getQuery('start_date');
        $endDate = $this->request->getQuery('end_date');

        if (empty($startDate) || empty($endDate)) {
            $this->Flash->error('Por favor, ingrese ambas fechas.');
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

        $query = $this->VistaConsultasProcedimientos->find()
            ->where([
                'fecha_registro >=' => $fechaInicio->format('Y-m-d H:i:s'),
                'fecha_registro <=' => $fechaFin->format('Y-m-d H:i:s'),
            ]);

        if (!empty($tipo)) {
            if ($tipo === 'consulta') {
                $query->where(['motivo IS NOT' => null]);
            } elseif ($tipo === 'procedimiento') {
                $query->where(['procedimiento IS NOT' => null]);
            }
        }

        if (!empty($dni)) {
            $query->where(['dni LIKE' => '%' . $dni . '%']);
        }

        $reportes = $query->toArray();

        $this->viewBuilder()->disableAutoLayout();
        $this->set(compact('reportes', 'tipo', 'dni', 'startDate', 'endDate'));

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
        $tipo = $this->request->getQuery('tipo');
        $dni = $this->request->getQuery('dni');
        $startDate = $this->request->getQuery('start_date');
        $endDate = $this->request->getQuery('end_date');

        if (empty($startDate) || empty($endDate)) {
            $this->Flash->error('Por favor, ingrese ambas fechas.');
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

        $query = $this->VistaConsultasProcedimientos->find()
            ->where([
                'fecha_registro >=' => $fechaInicio->format('Y-m-d H:i:s'),
                'fecha_registro <=' => $fechaFin->format('Y-m-d H:i:s'),
            ]);

        if (!empty($tipo)) {
            if ($tipo === 'consulta') {
                $query->where(['motivo IS NOT' => null]);
            } elseif ($tipo === 'procedimiento') {
                $query->where(['procedimiento IS NOT' => null]);
            }
        }

        if (!empty($dni)) {
            $query->where(['dni LIKE' => '%' . $dni . '%']);
        }

        $reportes = $query->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Consultas y Procedimientos");

        $sheet->setCellValue('A1', 'DNI');
        $sheet->setCellValue('B1', 'Nombre');
        $sheet->setCellValue('C1', 'Apellido');
        $sheet->setCellValue('D1', 'Motivo');
        $sheet->setCellValue('E1', 'Procedimiento');
        $sheet->setCellValue('F1', 'Doctor');
        $sheet->setCellValue('G1', 'Fecha');

        $row = 2;
        foreach ($reportes as $reporte) {
            $doctorCompleto = trim("{$reporte->doctor_nombre} {$reporte->doctor_apellido}");
            $sheet->setCellValue('A' . $row, $reporte->dni);
            $sheet->setCellValue('B' . $row, $reporte->nombre);
            $sheet->setCellValue('C' . $row, $reporte->apellido);
            $sheet->setCellValue('D' . $row, $reporte->motivo);
            $sheet->setCellValue('E' . $row, $reporte->procedimiento);
            $sheet->setCellValue('F' . $row, $doctorCompleto);
            $sheet->setCellValue('G' . $row, $reporte->fecha_registro);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'consultas_procedimientos.xlsx';

        $response = $this->response->withType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withDownload($fileName);

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return $response->withStringBody($excelOutput);
    }
}
