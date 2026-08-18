<?php
declare(strict_types=1);

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Panel de Inventario Controller
 *
 * @property \App\Model\Table\VistaReporteProductosTable $VistaReporteProductos
 */
class PanelInventarioController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $currentAction = $this->request->getParam('action');

        $this->verificarPermisoORedireccionar('PanelInventario', $currentAction);
    }

    public function index()
    {
        $productos = $this->fetchTable('VistaReporteProductos')->find()
            ->where(['activo' => 1])
            ->all();

        $totalProductos = $productos->count();
        $valorInventario = (float) $productos->sumOf('valor_inventario');
        $totalAgotados = $productos->filter(fn($p) => $p->estado_stock === 'AGOTADO')->count();
        $totalBajos = $productos->filter(fn($p) => $p->estado_stock === 'BAJO')->count();
        $totalNormales = $totalProductos - $totalAgotados - $totalBajos;

        // Productos por categoría, para el gráfico de barras
        $porCategoria = [];
        foreach ($productos as $p) {
            $cat = $p->categoria_nombre ?: 'Sin categoría';
            $porCategoria[$cat] = ($porCategoria[$cat] ?? 0) + 1;
        }
        arsort($porCategoria);

        // Top 10 productos con menor stock relativo (stock respecto a su mínimo),
        // solo entre los que sí tienen mínimo configurado.
        $topBajoStock = $productos
            ->filter(fn($p) => (float) $p->stock_minimo > 0)
            ->map(function ($p) {
                return [
                    'nombre' => $p->nombre,
                    'stock' => (float) $p->stock,
                    'stock_minimo' => (float) $p->stock_minimo,
                    'ratio' => (float) $p->stock / (float) $p->stock_minimo,
                ];
            })
            ->sortBy('ratio', SORT_ASC)
            ->take(10)
            ->toList();

        // Listado de alertas: agotados y bajos, para la tabla de reposición.
        // Cada fila incluye cuánto costaría reponer ese producto hasta su
        // stock mínimo, al precio de compra actual.
        $alertas = $productos
            ->filter(fn($p) => $p->estado_stock !== 'NORMAL')
            ->map(function ($p) {
                $cantidadReponer = max(0, (float) $p->stock_minimo - (float) $p->stock);
                $p->cantidad_reponer = $cantidadReponer;
                $p->costo_reposicion = round($cantidadReponer * (float) $p->precio_compra, 2);

                return $p;
            })
            ->sortBy('estado_stock', SORT_ASC)
            ->toList();

        $presupuestoReposicion = $this->calcularPresupuestoReposicion($productos);

        $this->set(compact(
            'totalProductos',
            'valorInventario',
            'totalAgotados',
            'totalBajos',
            'totalNormales',
            'porCategoria',
            'topBajoStock',
            'alertas',
            'presupuestoReposicion'
        ));
    }

    public function exportarExcel()
    {
        $productos = $this->fetchTable('VistaReporteProductos')->find()
            ->where(['activo' => 1])
            ->orderByAsc('categoria_nombre')
            ->orderByAsc('nombre')
            ->all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventario');

        $headerStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF0DCAF0'],
            ],
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'border' => ['allBorders' => ['borderStyle' => 'thin']],
        ];

        $headers = ['Categoría', 'Producto', 'Código', 'Proveedor', 'Precio Compra', 'Precio Venta', 'Margen %', 'Stock', 'Stock Mínimo', 'Valor Inventario', 'Estado'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];

        foreach ($columns as $idx => $col) {
            $sheet->setCellValue($col . '1', $headers[$idx]);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
        }

        $row = 2;
        foreach ($productos as $p) {
            $sheet->setCellValue('A' . $row, $p->categoria_nombre ?: '-');
            $sheet->setCellValue('B' . $row, $p->nombre);
            $sheet->setCellValue('C' . $row, $p->codigo ?: '-');
            $sheet->setCellValue('D' . $row, $p->proveedor_nombre ?: '-');
            $sheet->setCellValue('E' . $row, (float) $p->precio_compra);
            $sheet->setCellValue('F' . $row, (float) $p->precio_venta);
            $sheet->setCellValue('G' . $row, (float) $p->margen_porcentaje);
            $sheet->setCellValue('H' . $row, (float) $p->stock);
            $sheet->setCellValue('I' . $row, (float) $p->stock_minimo);
            $sheet->setCellValue('J' . $row, (float) $p->valor_inventario);
            $sheet->setCellValue('K' . $row, $p->estado_stock);
            $row++;
        }

        foreach ($columns as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'inventario_' . date('Ymd_His') . '.xlsx';

        $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($tmpFile);

        return $this->response->withFile($tmpFile, [
            'download' => true,
            'name' => $fileName,
            'delete' => true,
        ]);
    }

    public function exportarPdf()
    {
        $productos = $this->fetchTable('VistaReporteProductos')->find()
            ->where(['activo' => 1])
            ->orderByAsc('categoria_nombre')
            ->orderByAsc('nombre')
            ->all();

        $totalProductos = $productos->count();
        $valorInventario = (float) $productos->sumOf('valor_inventario');
        $totalAgotados = $productos->filter(fn($p) => $p->estado_stock === 'AGOTADO')->count();
        $totalBajos = $productos->filter(fn($p) => $p->estado_stock === 'BAJO')->count();
        $presupuestoReposicion = $this->calcularPresupuestoReposicion($productos);

        $this->viewBuilder()->disableAutoLayout();
        $this->set(compact('productos', 'totalProductos', 'valorInventario', 'totalAgotados', 'totalBajos', 'presupuestoReposicion'));
        $html = $this->render('exportar_pdf')->getBody()->__toString();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'inventario_' . date('Ymd_His') . '.pdf';

        return $this->response
            ->withType('application/pdf')
            ->withStringBody($dompdf->output())
            ->withHeader('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    /**
     * Suma, para los productos con stock BAJO o AGOTADO, cuánto costaría
     * reponer cada uno hasta su stock mínimo al precio de compra actual.
     */
    private function calcularPresupuestoReposicion(\Cake\Collection\CollectionInterface $productos): float
    {
        $total = 0.0;
        foreach ($productos as $p) {
            if ($p->estado_stock === 'NORMAL') {
                continue;
            }
            $cantidadReponer = max(0, (float) $p->stock_minimo - (float) $p->stock);
            $total += $cantidadReponer * (float) $p->precio_compra;
        }

        return round($total, 2);
    }

    public function reporteGanancias()
    {
        $fechaHoy = date('Y-m-d');
        $fechaDesde = $this->request->getQuery('fecha_desde') ?: date('Y-m-01');
        $fechaHasta = $this->request->getQuery('fecha_hasta') ?: $fechaHoy;

        $datos = $this->buildReporteGanancias($fechaDesde, $fechaHasta);

        $this->set(compact('fechaDesde', 'fechaHasta') + $datos);
    }

    public function exportarGananciasExcel()
    {
        $fechaDesde = $this->request->getQuery('fecha_desde') ?: date('Y-m-01');
        $fechaHasta = $this->request->getQuery('fecha_hasta') ?: date('Y-m-d');

        $datos = $this->buildReporteGanancias($fechaDesde, $fechaHasta);
        $filas = $datos['filas'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ganancia Productos');

        $headerStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF0DCAF0'],
            ],
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'border' => ['allBorders' => ['borderStyle' => 'thin']],
        ];

        $sheet->setCellValue('A1', 'Reporte de Ganancia por Ventas de Productos');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells('A1:G1');

        $sheet->setCellValue('A2', 'Período: ' . $fechaDesde . ' al ' . $fechaHasta);
        $sheet->mergeCells('A2:G2');

        $headers = ['Producto', 'Código', 'Cantidad Vendida', 'Total Vendido', 'Costo Total', 'Ganancia', 'Margen %'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

        foreach ($columns as $idx => $col) {
            $sheet->setCellValue($col . '4', $headers[$idx]);
            $sheet->getStyle($col . '4')->applyFromArray($headerStyle);
        }

        $row = 5;
        foreach ($filas as $f) {
            $sheet->setCellValue('A' . $row, $f['nombre']);
            $sheet->setCellValue('B' . $row, $f['codigo']);
            $sheet->setCellValue('C' . $row, $f['cantidad_vendida']);
            $sheet->setCellValue('D' . $row, $f['total_vendido']);
            $sheet->setCellValue('E' . $row, $f['costo_total']);
            $sheet->setCellValue('F' . $row, $f['ganancia']);
            $sheet->setCellValue('G' . $row, $f['margen_porcentaje']);
            $row++;
        }

        $sheet->setCellValue('A' . ($row + 1), 'TOTAL');
        $sheet->getStyle('A' . ($row + 1))->getFont()->setBold(true);
        $sheet->setCellValue('D' . ($row + 1), $datos['totalVendido']);
        $sheet->setCellValue('E' . ($row + 1), $datos['totalCosto']);
        $sheet->setCellValue('F' . ($row + 1), $datos['totalGanancia']);
        $sheet->getStyle('D' . ($row + 1) . ':F' . ($row + 1))->getFont()->setBold(true);

        foreach ($columns as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'ganancia_productos_' . str_replace('-', '', $fechaDesde) . '_' . str_replace('-', '', $fechaHasta) . '.xlsx';

        $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($tmpFile);

        return $this->response->withFile($tmpFile, [
            'download' => true,
            'name' => $fileName,
            'delete' => true,
        ]);
    }

    public function exportarGananciasPdf()
    {
        $fechaDesde = $this->request->getQuery('fecha_desde') ?: date('Y-m-01');
        $fechaHasta = $this->request->getQuery('fecha_hasta') ?: date('Y-m-d');

        $datos = $this->buildReporteGanancias($fechaDesde, $fechaHasta);

        $this->viewBuilder()->disableAutoLayout();
        $this->set(compact('fechaDesde', 'fechaHasta') + $datos);
        $html = $this->render('exportar_ganancias_pdf')->getBody()->__toString();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'ganancia_productos_' . str_replace('-', '', $fechaDesde) . '_' . str_replace('-', '', $fechaHasta) . '.pdf';

        return $this->response
            ->withType('application/pdf')
            ->withStringBody($dompdf->output())
            ->withHeader('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    /**
     * Construye el reporte de ganancia por ventas de productos en un rango de
     * fechas: agrupa invoice_items (tipo 'producto') de facturas no anuladas
     * por producto, y calcula el costo/ganancia usando el precio_compra
     * ACTUAL del producto (no hay costo histórico guardado por venta).
     */
    private function buildReporteGanancias(string $fechaDesde, string $fechaHasta): array
    {
        $InvoiceItems = $this->fetchTable('InvoiceItems');

        $items = $InvoiceItems->find()
            ->contain(['Invoices', 'Productos'])
            ->innerJoinWith('Invoices')
            ->where([
                'InvoiceItems.tipo_item' => 'producto',
                'InvoiceItems.producto_id IS NOT' => null,
                'Invoices.estado !=' => 'ANULADO',
                'DATE(Invoices.created) >=' => $fechaDesde,
                'DATE(Invoices.created) <=' => $fechaHasta,
            ])
            ->all();

        $porProducto = [];
        foreach ($items as $item) {
            if (empty($item->producto)) {
                continue;
            }

            $productoId = $item->producto_id;
            if (!isset($porProducto[$productoId])) {
                $porProducto[$productoId] = [
                    'nombre' => $item->producto->nombre,
                    'codigo' => $item->producto->codigo ?: '-',
                    'precio_compra' => (float) $item->producto->precio_compra,
                    'cantidad_vendida' => 0.0,
                    'total_vendido' => 0.0,
                ];
            }

            $porProducto[$productoId]['cantidad_vendida'] += (float) $item->cantidad;
            $porProducto[$productoId]['total_vendido'] += (float) $item->total;
        }

        $filas = [];
        $totalVendido = 0.0;
        $totalCosto = 0.0;
        $totalGanancia = 0.0;

        foreach ($porProducto as $p) {
            $costoTotal = round($p['cantidad_vendida'] * $p['precio_compra'], 2);
            $ganancia = round($p['total_vendido'] - $costoTotal, 2);
            $margenPct = $costoTotal > 0 ? round(($ganancia / $costoTotal) * 100, 2) : 0.0;

            $filas[] = [
                'nombre' => $p['nombre'],
                'codigo' => $p['codigo'],
                'cantidad_vendida' => $p['cantidad_vendida'],
                'total_vendido' => round($p['total_vendido'], 2),
                'costo_total' => $costoTotal,
                'ganancia' => $ganancia,
                'margen_porcentaje' => $margenPct,
            ];

            $totalVendido += $p['total_vendido'];
            $totalCosto += $costoTotal;
            $totalGanancia += $ganancia;
        }

        usort($filas, fn($a, $b) => $b['ganancia'] <=> $a['ganancia']);

        return [
            'filas' => $filas,
            'totalVendido' => round($totalVendido, 2),
            'totalCosto' => round($totalCosto, 2),
            'totalGanancia' => round($totalGanancia, 2),
        ];
    }
}
