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
     * Una sola consulta por bloque de comprobantes (no una por comprobante), para que
     * los rangos de fechas grandes no saturen la base de datos.
     *
     * @param iterable<object> $invoices Entidades con id y caja_id
     * @return array<int, array<int, array{metodo:string,monto:float}>>
     */
    private function buildPagosPorComprobante(iterable $invoices): array
    {
        $CajaMovimientos = $this->fetchTable('CajaMovimientos');

        $pagosPorComprobante = [];
        $cajaPorComprobante = [];
        foreach ($invoices as $inv) {
            $pagosPorComprobante[$inv->id] = [];
            $cajaPorComprobante[$inv->id] = $inv->caja_id;
        }

        foreach (array_chunk(array_keys($pagosPorComprobante), 1000) as $ids) {
            $movimientos = $CajaMovimientos->find()
                ->where(['CajaMovimientos.invoice_id IN' => $ids])
                ->orderBy(['CajaMovimientos.id' => 'ASC'])
                ->all();

            foreach ($movimientos as $mov) {
                $cajaEsperada = $cajaPorComprobante[$mov->invoice_id] ?? null;
                if (!empty($cajaEsperada) && (int) $mov->caja_id !== (int) $cajaEsperada) {
                    continue;
                }
                $pagosPorComprobante[$mov->invoice_id][] = [
                    'metodo' => $mov->metodo_pago ?? 'EFECTIVO',
                    'monto' => (float) ($mov->monto_recibido ?? 0),
                ];
            }
        }

        return $pagosPorComprobante;
    }

    /**
     * Filtros del reporte. Sin fechas se usa el día de hoy (es lo que muestra el
     * formulario), así lo que se ve en los campos es lo que realmente se consulta.
     *
     * @return array{0: ?string, 1: string, 2: string}
     */
    private function filtros(): array
    {
        $tipo = $this->request->getQuery('tipo');
        $valida = fn($f) => is_string($f) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $f) && strtotime($f) !== false;
        $desde = $this->request->getQuery('desde');
        $hasta = $this->request->getQuery('hasta');
        $desde = $valida($desde) ? $desde : date('Y-m-d');
        $hasta = $valida($hasta) ? $hasta : date('Y-m-d');
        if ($desde > $hasta) {
            [$desde, $hasta] = [$hasta, $desde];
        }

        return [$tipo, $desde, $hasta];
    }

    /**
     * Comprobantes del período y tipo elegidos (incluye anulados; los totales los excluyen).
     */
    private function consultaComprobantes(?string $tipo, string $desde, string $hasta)
    {
        $query = $this->fetchTable('Invoices')->find()
            ->order(['Invoices.created' => 'DESC']);

        if ($tipo === 'BOLETA') {
            $query->where(['Invoices.tipo_doc' => '03']);
        } elseif ($tipo === 'FACTURA') {
            $query->where(['Invoices.tipo_doc' => '01']);
        } elseif ($tipo === 'RI') {
            $query->where(['Invoices.estado' => 'RECIBO_INTERNO']);
        }

        $query->where([
            'DATE(Invoices.created) >=' => $desde,
            'DATE(Invoices.created) <=' => $hasta,
        ]);

        return $query;
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
        [$tipo, $desde, $hasta] = $this->filtros();

        $query = $this->consultaComprobantes($tipo, $desde, $hasta);

        // Listado paginado: con rangos grandes no se carga todo de golpe.
        $invoices = $this->paginate(
            (clone $query)->contain(['Companies', 'Pacientes', 'HistoriasClinicas', 'Doctores']),
            ['limit' => 25, 'maxLimit' => 100]
        );

        // Totales de TODO el período (no solo de la página). Los comprobantes
        // anulados no cuentan como venta.
        $vigentes = (clone $query)->where(['Invoices.estado !=' => 'ANULADO']);

        $totalBoletas = (clone $vigentes)->where(['Invoices.tipo_doc' => '03'])->count();
        $totalFacturas = (clone $vigentes)->where(['Invoices.tipo_doc' => '01'])->count();
        $totalRecibos = (clone $vigentes)->where(['Invoices.estado' => 'RECIBO_INTERNO'])->count();
        $totalComprobantes = (clone $vigentes)->count();
        $totalVentas = (float) ((clone $vigentes)
            ->select(['total_sum' => $query->func()->sum('Invoices.total')])
            ->disableHydration()
            ->first()['total_sum'] ?? 0);
        $totalAnulados = (clone $query)->where(['Invoices.estado' => 'ANULADO'])->count();

        // Análisis de pagos de todo el período (sin anulados)
        $pagosPorTipo = [];
        $totalPorTipo = [];
        $resumenPagos = [];

        try {
            $paraPagos = (clone $vigentes)->select(['Invoices.id', 'Invoices.caja_id'])->all();
            $resumenPagos = $this->buildResumenPagos($this->buildPagosPorComprobante($paraPagos));

            foreach ($resumenPagos as $metodo => $datos) {
                $pagosPorTipo[$metodo] = $datos['cantidad'];
                $totalPorTipo[$metodo] = $datos['total'];
            }
        } catch (\Exception $e) {
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
            'totalAnulados',
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
        [$tipo, $desde, $hasta] = $this->filtros();

        $invoices = (clone $this->consultaComprobantes($tipo, $desde, $hasta))
            ->contain(['Companies', 'Pacientes', 'HistoriasClinicas', 'Doctores'])
            ->toArray();
        // Los anulados se listan (con su estado) pero no suman en totales ni pagos.
        $vigentes = array_values(array_filter($invoices, fn($i) => $i->estado !== 'ANULADO'));
        $totalVentas = array_sum(array_map(fn($i) => (float) $i->total, $vigentes));

        $pagosPorComprobante = $this->buildPagosPorComprobante($invoices);
        $resumenPagos = $this->buildResumenPagos(
            array_intersect_key($pagosPorComprobante, array_flip(array_map(fn($i) => $i->id, $vigentes)))
        );

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
        [$tipo, $desde, $hasta] = $this->filtros();

        $invoices = (clone $this->consultaComprobantes($tipo, $desde, $hasta))
            ->contain(['Companies', 'Pacientes', 'HistoriasClinicas', 'Doctores'])
            ->toArray();
        // Los anulados se listan (con su estado) pero no suman en totales ni pagos.
        $vigentes = array_values(array_filter($invoices, fn($i) => $i->estado !== 'ANULADO'));
        $totalVentas = array_sum(array_map(fn($i) => (float) $i->total, $vigentes));

        $pagosPorComprobante = $this->buildPagosPorComprobante($invoices);
        $resumenPagos = $this->buildResumenPagos(
            array_intersect_key($pagosPorComprobante, array_flip(array_map(fn($i) => $i->id, $vigentes)))
        );

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
            $tipo_label = $inv->tipo_doc === '01' ? 'Factura' : ($inv->tipo_doc === '03' ? 'Boleta' : 'Nota de Venta');
            
            // Contar por tipo (los anulados no suman)
            if ($inv->estado !== 'ANULADO') {
                if (!isset($totalPorTipo[$tipo_label])) {
                    $totalPorTipo[$tipo_label] = ['cantidad' => 0, 'total' => 0];
                }
                $totalPorTipo[$tipo_label]['cantidad']++;
                $totalPorTipo[$tipo_label]['total'] += (float)$inv->total;
            }

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
        $sheet->setCellValue("E" . ($row + 1), '(excluye anulados)');
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
