<?php

declare(strict_types=1);

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Cake\ORM\TableRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


/**
 * VistaPacientesCampanas Controller
 *
 * @property \App\Model\Table\VistaPacientesCampanasTable $VistaPacientesCampanas
 */
class VistaPacientesCampanasController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $currentAction = $this->request->getParam('action');
        
        if (!$this->Permisos->tienePermiso('VistaPacientesCampanas', $currentAction)) {
            $this->Flash->error(__('No tienes permisos para acceder a esta acción'));
            throw new \Cake\Http\Exception\ForbiddenException();
        }
    }
    private $VistaPacientesCampanas;

    public function initialize(): void
    {
        parent::initialize();
        $this->VistaPacientesCampanas = $this->fetchTable('VistaPacientesCampanas');
    }

    public function index()
    {
        $roles = [1]; // Ajusta según tus necesidades
        if (!in_array($this->request->getAttribute('identity')->rol, $roles)) {
            $this->Flash->error(__('Acceso no autorizado.'));
            return $this->redirect($this->referer());
        }

        // Filtros por campaña y fechas
        $campanaId = $this->request->getQuery('campana_id');
        $startDate = $this->request->getQuery('start_date');
        $endDate = $this->request->getQuery('end_date');

        // Obtener lista de campañas
        $campanasTable = $this->fetchTable('Campañas');
        $campañas = $campanasTable->find('list', 
            keyField :'id',
            valueField :'nombre'
        )->toArray();

        $query = $this->VistaPacientesCampanas->find();

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

        if (!empty($campanaId)) {
            $query->where(['campana_id' => $campanaId]);
        }

        $reportes = $this->paginate($query);
        $this->set(compact('reportes', 'campañas', 'campanaId', 'startDate', 'endDate'));
    }

    public function exportarPdf()
    {
        $campanaId = $this->request->getQuery('campana_id');
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

        $query = $this->VistaPacientesCampanas->find()
            ->where([
                'fecha_consulta >=' => $fechaInicio->format('Y-m-d H:i:s'),
                'fecha_consulta <=' => $fechaFin->format('Y-m-d H:i:s'),
            ]);

        if (!empty($campanaId)) {
            $query->where(['campana_id' => $campanaId]);
        }

        $reportes = $query->toArray();

        $this->viewBuilder()->disableAutoLayout();
        $this->set(compact('reportes', 'startDate', 'endDate'));

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
        $campanaId = $this->request->getQuery('campana_id');
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

        $query = $this->VistaPacientesCampanas->find()
            ->where([
                'fecha_consulta >=' => $fechaInicio->format('Y-m-d H:i:s'),
                'fecha_consulta <=' => $fechaFin->format('Y-m-d H:i:s'),
            ]);

        if (!empty($campanaId)) {
            $query->where(['campana_id' => $campanaId]);
        }

        $reportes = $query->toArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Pacientes por Campaña");

        $sheet->setCellValue('A1', 'Nombre');
        $sheet->setCellValue('B1', 'Apellido');
        $sheet->setCellValue('C1', 'DNI');
        $sheet->setCellValue('D1', 'Campaña');
        $sheet->setCellValue('E1', 'Fecha Nacimiento');
        $sheet->setCellValue('F1', 'Ocupación');
        $sheet->setCellValue('G1', 'Fehca de cita');
        
        $row = 2;
        foreach ($reportes as $reporte) {
            $sheet->setCellValue('A' . $row, $reporte->nombre);
            $sheet->setCellValue('B' . $row, $reporte->apellido);
            $sheet->setCellValue('C' . $row, $reporte->dni);
            $sheet->setCellValue('D' . $row, $reporte->nombre_campana);
            $sheet->setCellValue('E' . $row, $reporte->fecha_nacimiento);
            $sheet->setCellValue('F' . $row, $reporte->ocupacion);
            $sheet->setCellValue('G' . $row, $reporte->fecha_consulta);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'pacientes_campañas.xlsx';

        $response = $this->response->withType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withDownload($fileName);

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return $response->withStringBody($excelOutput);
    }
}
