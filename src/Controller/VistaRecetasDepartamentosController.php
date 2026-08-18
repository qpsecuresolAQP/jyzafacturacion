<?php

declare(strict_types=1);

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Cake\ORM\TableRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * VistaRecetasDepartamentos Controller
 *
 */
class VistaRecetasDepartamentosController extends AppController
{
    private $VistaRecetasDepartamentos;

    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $currentAction = $this->request->getParam('action');
        
        $this->verificarPermisoORedireccionar('VistaRecetasDepartamentos', $currentAction);
    }

    public function initialize(): void
    {
        parent::initialize();
        $this->VistaRecetasDepartamentos = $this->fetchTable('VistaRecetasDepartamentos');
    }

    public function index()
    {
        $roles = [1]; // Ajustar según los roles permitidos
        if (!in_array($this->request->getAttribute('identity')->rol, $roles)) {
            $this->Flash->error(__('Acceso no autorizado.'));
            return $this->redirect($this->referer());
        }

        // Cargar lista de departamentos
        $departamentosTable = $this->fetchTable('Departamentos');
        $departamentos = $departamentosTable->find('list', keyField: 'id', valueField: 'nombre')->toArray();

        // Obtener filtros
        $departamentoId = $this->request->getQuery('departamento_id');
        $startDate = $this->request->getQuery('start_date');
        $endDate = $this->request->getQuery('end_date');

        // Construcción de la consulta
        $query = $this->VistaRecetasDepartamentos->find();

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

        // Filtro por Departamento
        if (!empty($departamentoId)) {
            $query->where(['departamento_id' => $departamentoId]);
        }

        $reportes = $this->paginate($query);
        $this->set(compact('reportes', 'departamentos', 'departamentoId', 'startDate', 'endDate'));
    }

    public function exportarPdf()
    {
        // Obtener parámetros
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
        $query = $this->VistaRecetasDepartamentos->find()
            ->where([
                'fecha_consulta >=' => $fechaInicio->format('Y-m-d H:i:s'),
                'fecha_consulta <=' => $fechaFin->format('Y-m-d H:i:s'),
            ]);

        if (!empty($departamentoId)) {
            $query->where(['departamento_id' => $departamentoId]);
        }

        $reportes = $query->toArray();

        // Contar recetas por departamento
        $departamentosQuery = $this->VistaRecetasDepartamentos->find()
            ->select([
                'departamento_id',
                'nombre_departamento',
                'total_recetas' => $this->VistaRecetasDepartamentos->find()->func()->count('*')
            ])
            ->group(['departamento_id', 'nombre_departamento']);

        // (Opcional) Filtrar por un departamento específico
        if (!empty($departamentoId)) {
            $departamentosQuery->where(['departamento_id' => $departamentoId]);
        }

        // Contar recetas totales
        $recetasQuery = $this->VistaRecetasDepartamentos->find()
            ->select([
                'receta_id',
                'nombre_receta',
                'total_recetas_independiente' => $this->VistaRecetasDepartamentos->find()->func()->count('*')
            ])
            ->group(['receta_id']);

        $departamentos = $departamentosQuery->toArray();
        $recetas = $recetasQuery->toArray();

        // Renderizar PDF
        $this->viewBuilder()->disableAutoLayout();
        $this->set(compact('reportes', 'departamentos', 'startDate', 'endDate', 'recetas'));
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
    $query = $this->VistaRecetasDepartamentos->find()
        ->where([
            'fecha_consulta >=' => $fechaInicio->format('Y-m-d H:i:s'),
            'fecha_consulta <=' => $fechaFin->format('Y-m-d H:i:s'),
        ]);

    if (!empty($departamentoId)) {
        $query->where(['departamento_id' => $departamentoId]);
    }

    $reportes = $query->toArray();

    // Crear archivo Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Reporte Recetas Departamento");

    // Encabezados (agregadas Sexo, Edad, Fecha Consulta)
    $sheet->setCellValue('A1', 'Departamento');
    $sheet->setCellValue('B1', 'Paciente');
    $sheet->setCellValue('C1', 'Dni');
    $sheet->setCellValue('D1', 'Receta');
    $sheet->setCellValue('E1', 'Sexo');
    $sheet->setCellValue('F1', 'Edad');
    $sheet->setCellValue('G1', 'Fecha Consulta');

    // Llenar datos
    $row = 2;
    foreach ($reportes as $reporte) {
        $sheet->setCellValue('A' . $row, $reporte->nombre_departamento ?? '');
        $sheet->setCellValue('B' . $row, $reporte->nombre_paciente ?? '');
        $sheet->setCellValue('C' . $row, $reporte->dni_paciente ?? '');
        $sheet->setCellValue('D' . $row, $reporte->nombre_receta ?? '');

        // Sexo (viene de la vista como sexo_paciente)
        $sexo = $reporte->sexo_paciente ?? '';
        $sheet->setCellValue('E' . $row, $sexo);

        // Edad (intenta usar edad_paciente; si no, intenta calcular desde fecha_nacimiento si está disponible)
        $edad = '';
        if (isset($reporte->edad_paciente) && $reporte->edad_paciente !== null) {
            $edad = (string)$reporte->edad_paciente;
        } elseif (!empty($reporte->fecha_nacimiento)) {
            try {
                $dob = new \DateTime($reporte->fecha_nacimiento);
                $now = new \DateTime('now', new \DateTimeZone('America/Lima'));
                $diff = $now->diff($dob);
                $edad = $diff->y;
            } catch (\Exception $e) {
                $edad = '';
            }
        }
        $sheet->setCellValue('F' . $row, $edad);

        // Fecha de consulta: formatear de forma legible en zona America/Lima
        $fechaConsultaStr = '';
        if (!empty($reporte->fecha_consulta)) {
            try {
                // Maneja strings o objetos DateTime
                if ($reporte->fecha_consulta instanceof \DateTimeInterface) {
                    $dt = new \DateTime($reporte->fecha_consulta->format('Y-m-d H:i:s'));
                } else {
                    $dt = new \DateTime($reporte->fecha_consulta);
                }
                $tz = new \DateTimeZone('America/Lima');
                $dt->setTimezone($tz);
                $fechaConsultaStr = $dt->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $fechaConsultaStr = (string)$reporte->fecha_consulta;
            }
        }
        $sheet->setCellValue('G' . $row, $fechaConsultaStr);

        $row++;
    }

    // Descargar el archivo
    $writer = new Xlsx($spreadsheet);
    $fileName = 'reporte_recetas.xlsx';

    $response = $this->response->withType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
        ->withDownload($fileName);

    ob_start();
    $writer->save('php://output');
    $excelOutput = ob_get_clean();

    return $response->withStringBody($excelOutput);
}

}

