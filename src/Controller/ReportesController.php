<?php
declare(strict_types=1);

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportesController extends AppController
{
    /**
     * Construye el mapa de métodos de pago por comprobante filtrando por caja cuando exista.
     *
     * @param array<int, object> $invoices
     * @return array<int, array<int, array{metodo:string,monto:float}>>
     */
    private function buildPagosPorComprobante(array $invoices): array
    {
        $CajaMovimientos = $this->fetchTable('CajaMovimientos');

        $pagosPorComprobante = [];
        foreach ($invoices as $inv) {
            $pagosPorComprobante[$inv->id] = [];

            $query = $CajaMovimientos->find()
                ->where(['CajaMovimientos.invoice_id' => $inv->id])
                ->orderBy(['CajaMovimientos.id' => 'ASC']);

            if (!empty($inv->caja_id)) {
                $query->where(['CajaMovimientos.caja_id' => $inv->caja_id]);
            }

            foreach ($query->all() as $mov) {
                $pagosPorComprobante[$inv->id][] = [
                    'metodo' => $mov->metodo_pago ?? 'EFECTIVO',
                    'monto' => (float)($mov->monto_recibido ?? 0),
                ];
            }
        }

        return $pagosPorComprobante;
    }

    /**
     * Resume pagos por método de pago, sumando cantidad e importe total.
     *
     * @param array<int, array<int, array{metodo:string,monto:float}>> $pagosPorComprobante
     * @return array<string, array{cantidad:int,total:float}>
     */
    private function buildResumenPagos(array $pagosPorComprobante): array
    {
        $resumen = [];

        foreach ($pagosPorComprobante as $pagos) {
            foreach ($pagos as $pago) {
                $metodo = strtoupper(trim((string)($pago['metodo'] ?? 'EFECTIVO')));
                $monto = (float)($pago['monto'] ?? 0);

                if (!isset($resumen[$metodo])) {
                    $resumen[$metodo] = [
                        'cantidad' => 0,
                        'total' => 0.0,
                    ];
                }

                $resumen[$metodo]['cantidad']++;
                $resumen[$metodo]['total'] += $monto;
            }
        }

        return $resumen;
    }

    public function index()
    {
        $Invoices = $this->fetchTable('Invoices');

        $tipo = $this->request->getQuery('tipo');
        $desde = $this->request->getQuery('desde');
        $hasta = $this->request->getQuery('hasta');

        $query = $Invoices->find()
            ->contain(['Companies', 'Pacientes', 'HistoriasClinicas', 'Doctores'])
            ->order(['Invoices.created' => 'DESC']);

        // 🎯 FILTRO TIPO
        if ($tipo === 'BOLETA') {
            $query->where(['Invoices.tipo_doc' => '03']);
        } elseif ($tipo === 'FACTURA') {
            $query->where(['Invoices.tipo_doc' => '01']);
        } elseif ($tipo === 'RI') {
            $query->where(['Invoices.estado' => 'RECIBO_INTERNO']);
        }

        // 📅 FILTRO FECHAS
        if (!empty($desde)) {
            $query->where(['DATE(Invoices.created) >=' => $desde]);
        }

        if (!empty($hasta)) {
            $query->where(['DATE(Invoices.created) <=' => $hasta]);
        }

        $invoices = $this->paginate($query);

        // 📊 KPIs (CLONAR QUERY BIEN EN CAKEPHP)
        $baseQuery = clone $query;

        $totalBoletas = (clone $baseQuery)
            ->where(['Invoices.tipo_doc' => '03'])
            ->count();

        $totalFacturas = (clone $baseQuery)
            ->where(['Invoices.tipo_doc' => '01'])
            ->count();

        $totalRecibos = (clone $baseQuery)
            ->where(['Invoices.estado' => 'RECIBO_INTERNO'])
            ->count();

        $totalVentas = (clone $baseQuery)
            ->select(['total_sum' => $baseQuery->func()->sum('total')])
            ->first()
            ->total_sum ?? 0;

        $totalComprobantes = (clone $baseQuery)->count();

        // 💳 ANÁLISIS DE PAGOS
        $pagosPorTipo = [];
        $totalPorTipo = [];
        $resumenPagos = [];

        try {
            $pagosPorComprobante = $this->buildPagosPorComprobante($invoices->toArray());
            $resumenPagos = $this->buildResumenPagos($pagosPorComprobante);

            foreach ($resumenPagos as $metodo => $datos) {
                $pagosPorTipo[$metodo] = $datos['cantidad'];
                $totalPorTipo[$metodo] = $datos['total'];
            }
        } catch (\Exception $e) {
            // Si hay error al cargar movimientos, continuar sin ellos
            $pagosPorTipo = [];
            $totalPorTipo = [];
            $resumenPagos = [];
        }

        $this->set(compact(
            'invoices',
            'totalBoletas',
            'totalFacturas',
            'totalRecibos',
            'totalVentas',
            'totalComprobantes',
            'pagosPorTipo',
            'totalPorTipo',
            'resumenPagos',
            'tipo',
            'desde',
            'hasta'
        ));
    }

    /**
     * Exportar reporte a PDF con Dompdf
     */
    public function exportarPdf()
    {
        $Invoices = $this->fetchTable('Invoices');
        $tipo = $this->request->getQuery('tipo');
        $desde = $this->request->getQuery('desde');
        $hasta = $this->request->getQuery('hasta');

        $query = $Invoices->find()
            ->contain(['Companies', 'Pacientes', 'HistoriasClinicas', 'Doctores'])
            ->order(['Invoices.created' => 'DESC']);

        // 🎯 FILTRO TIPO
        if ($tipo === 'BOLETA') {
            $query->where(['Invoices.tipo_doc' => '03']);
        } elseif ($tipo === 'FACTURA') {
            $query->where(['Invoices.tipo_doc' => '01']);
        } elseif ($tipo === 'RI') {
            $query->where(['Invoices.estado' => 'RECIBO_INTERNO']);
        }

        // 📅 FILTRO FECHAS
        if (!empty($desde)) {
            $query->where(['DATE(Invoices.created) >=' => $desde]);
        }

        if (!empty($hasta)) {
            $query->where(['DATE(Invoices.created) <=' => $hasta]);
        }

        $invoices = $query->toArray();
        $totalVentas = array_sum(array_map(fn($i) => (float)$i->total, $invoices));

        $pagosPorComprobante = $this->buildPagosPorComprobante($invoices);
        $resumenPagos = $this->buildResumenPagos($pagosPorComprobante);

        // Convertir a DateTime si es string
        $fechaInicio = $desde ? new \DateTime($desde) : null;
        $fechaFin = $hasta ? new \DateTime($hasta) : null;

        // Pasar datos a la vista
        $this->viewBuilder()->disableAutoLayout();
        $this->set(compact('invoices', 'totalVentas', 'tipo', 'desde', 'hasta', 'fechaInicio', 'fechaFin', 'pagosPorComprobante', 'resumenPagos'));
        
        // Renderizar vista y obtener HTML
        $html = $this->render('reportes_pdf')->getBody()->__toString();

        // Configurar y generar PDF
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Reporte_' . ($tipo ?: 'Todos') . '_' . date('Y-m-d_His') . '.pdf';

        return $this->response->withType('application/pdf')
            ->withHeader('Content-Disposition', "attachment; filename=\"$filename\"")
            ->withStringBody($dompdf->output());
    }

    /**
     * Exportar reporte a Excel con PhpSpreadsheet
     */
    public function exportarExcel()
    {
        $Invoices = $this->fetchTable('Invoices');
        $tipo = $this->request->getQuery('tipo');
        $desde = $this->request->getQuery('desde');
        $hasta = $this->request->getQuery('hasta');

        $query = $Invoices->find()
            ->contain(['Companies', 'Pacientes', 'HistoriasClinicas', 'Doctores'])
            ->order(['Invoices.created' => 'DESC']);

        // 🎯 FILTRO TIPO
        if ($tipo === 'BOLETA') {
            $query->where(['Invoices.tipo_doc' => '03']);
        } elseif ($tipo === 'FACTURA') {
            $query->where(['Invoices.tipo_doc' => '01']);
        } elseif ($tipo === 'RI') {
            $query->where(['Invoices.estado' => 'RECIBO_INTERNO']);
        }

        // 📅 FILTRO FECHAS
        if (!empty($desde)) {
            $query->where(['DATE(Invoices.created) >=' => $desde]);
        }

        if (!empty($hasta)) {
            $query->where(['DATE(Invoices.created) <=' => $hasta]);
        }

        $invoices = $query->toArray();
        $totalVentas = array_sum(array_map(fn($i) => (float)$i->total, $invoices));

        $pagosPorComprobante = $this->buildPagosPorComprobante($invoices);
        $resumenPagos = $this->buildResumenPagos($pagosPorComprobante);

        // Crear hoja Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte de Comprobantes');

        // Encabezados
        $headers = ['ID', 'Serie', 'Número', 'Tipo', 'Cliente', 'Total (S/)', 'Estado', 'Fecha/Hora', 'Métodos de Pago'];
        $sheet->fromArray($headers, null, 'A1');

        // Estilo de encabezados
        $headerStyle = $sheet->getStyle('A1:I1');
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerStyle->getFill()->getStartColor()->setARGB('FFE0E0E0');

        // Datos
        $row = 2;
        $totalPorTipo = [];

        foreach ($invoices as $inv) {
            $tipo_label = $inv->tipo_doc === '01' ? 'Factura' : ($inv->tipo_doc === '03' ? 'Boleta' : 'Recibo');
            
            // Contar por tipo
            if (!isset($totalPorTipo[$tipo_label])) {
                $totalPorTipo[$tipo_label] = ['cantidad' => 0, 'total' => 0];
            }
            $totalPorTipo[$tipo_label]['cantidad']++;
            $totalPorTipo[$tipo_label]['total'] += (float)$inv->total;

            // Métodos de pago formateados
            $pagos = $pagosPorComprobante[$inv->id] ?? [];
            $pagoTexto = '';
            if (!empty($pagos)) {
                foreach ($pagos as $pago) {
                    $pagoTexto .= $pago['metodo'] . ': S/ ' . number_format($pago['monto'], 2) . "\n";
                }
            }

            $sheet->setCellValue("A{$row}", $inv->id);
            $sheet->setCellValue("B{$row}", $inv->serie ?: 'RI');
            $sheet->setCellValue("C{$row}", $inv->correlativo ?: $inv->id);
            $sheet->setCellValue("D{$row}", $tipo_label);
            $sheet->setCellValue("E{$row}", $inv->cliente_nombre);
            $sheet->setCellValue("F{$row}", (float)$inv->total);
            $sheet->setCellValue("G{$row}", $inv->estado);
            $sheet->setCellValue("H{$row}", $inv->created->format('d/m/Y H:i'));
            $sheet->setCellValue("I{$row}", trim($pagoTexto));
            
            // Ajustar altura para múltiples líneas
            if (!empty($pagos)) {
                $sheet->getRowDimension($row)->setRowHeight(count($pagos) * 15);
            }
            
            $row++;
        }

        // Resumen por método de pago
        $row++;
        $sheet->setCellValue("A{$row}", 'Resumen por Método de Pago');
        $row++;
        $sheet->setCellValue("A{$row}", 'Método');
        $sheet->setCellValue("B{$row}", 'Cantidad');
        $sheet->setCellValue("C{$row}", 'Total (S/)');
        $row++;

        foreach ($resumenPagos as $metodo => $datos) {
            $sheet->setCellValue("A{$row}", $metodo);
            $sheet->setCellValue("B{$row}", $datos['cantidad']);
            $sheet->setCellValue("C{$row}", $datos['total']);
            $row++;
        }

        // Dejar una fila vacía
        $row++;

        // Totales por tipo
        $sheet->setCellValue("A{$row}", 'Totales por Tipo');
        $row++;
        $sheet->setCellValue("D{$row}", 'Tipo');
        $sheet->setCellValue("E{$row}", 'Cantidad');
        $sheet->setCellValue("F{$row}", 'Total (S/)');
        $row++;

        foreach ($totalPorTipo as $tipo_nombre => $datos) {
            $sheet->setCellValue("D{$row}", $tipo_nombre);
            $sheet->setCellValue("E{$row}", $datos['cantidad']);
            $sheet->setCellValue("F{$row}", $datos['total']);
            $row++;
        }

        // Fila de total general
        $row++;
        $sheet->setCellValue("E{$row}", 'TOTAL GENERAL');
        $sheet->setCellValue("F{$row}", $totalVentas);
        $totalStyle = $sheet->getStyle("E{$row}:F{$row}");
        $totalStyle->getFont()->setBold(true);

        // Ajustar ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(35);

        // Habilitar ajuste de texto en columna de métodos de pago
        $sheet->getStyle("I:I")->getAlignment()->setWrapText(true);

        // Guardar y enviar
        $writer = new Xlsx($spreadsheet);
        $filename = 'Reporte_' . ($tipo ?: 'Todos') . '_' . date('Y-m-d_His') . '.xlsx';

        $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($tmpFile);

        return $this->response->withFile($tmpFile, [
            'download' => true,
            'name' => $filename,
            'delete' => true,
        ]);
    }
}
