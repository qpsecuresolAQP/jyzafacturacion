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
        $productos = $this->cargarInventario();
        $ProductoLotes = $this->fetchTable('ProductoLotes');
        ['totalProductos' => $totalProductos, 'valorInventario' => $valorInventario, 'valorVenta' => $valorVenta,
            'gananciaPotencial' => $gananciaPotencial, 'totalAgotados' => $totalAgotados, 'totalBajos' => $totalBajos,
            'totalNormales' => $totalNormales] = $this->resumenInventario($productos);

        // Lotes vencidos y por vencer (con stock): lo más costoso de ignorar.
        $limite = \Cake\I18n\Date::today()->addDays(\App\Model\Table\ProductoLotesTable::DIAS_ALERTA_VENCIMIENTO);
        $lotesRiesgo = $ProductoLotes->find()
            ->contain(['Productos'])
            ->where([
                'ProductoLotes.stock >' => 0,
                'ProductoLotes.fecha_vencimiento IS NOT' => null,
                'ProductoLotes.fecha_vencimiento <=' => $limite,
                'Productos.estado' => 1,
            ])
            ->orderBy(['ProductoLotes.fecha_vencimiento' => 'ASC', 'ProductoLotes.id' => 'ASC'])
            ->all()
            ->map(function ($l) {
                $l->valor_costo = round((float) $l->stock * (float) $l->producto->precio_compra, 2);

                return $l;
            })
            ->toList();
        $lotesVencidos = array_values(array_filter($lotesRiesgo, fn($l) => $l->vencido));
        $lotesPorVencer = array_values(array_filter($lotesRiesgo, fn($l) => !$l->vencido));
        $valorVencido = round(array_sum(array_column($lotesVencidos, 'valor_costo')), 2);
        $valorPorVencer = round(array_sum(array_column($lotesPorVencer, 'valor_costo')), 2);

        // Top 10 productos con menor stock relativo (stock vigente respecto a su mínimo),
        // solo entre los que sí tienen mínimo configurado.
        $topBajoStock = $productos
            ->filter(fn($p) => (float) $p->stock_minimo > 0)
            ->map(function ($p) {
                return [
                    'nombre' => $p->nombre,
                    'stock' => $p->stock_vigente,
                    'stock_minimo' => (float) $p->stock_minimo,
                    'ratio' => $p->stock_vigente / (float) $p->stock_minimo,
                ];
            })
            ->sortBy('ratio', SORT_ASC)
            ->take(10)
            ->toList();

        // Listado de alertas: agotados primero, luego bajos (el más urgente arriba).
        // Cada fila incluye cuánto costaría reponer hasta el stock mínimo al costo actual.
        $alertas = $productos
            ->filter(fn($p) => $p->estado_stock !== 'NORMAL')
            ->map(function ($p) {
                $cantidadReponer = max(0, (float) $p->stock_minimo - $p->stock_vigente);
                $p->cantidad_reponer = $cantidadReponer;
                $p->costo_reposicion = round($cantidadReponer * (float) $p->precio_compra, 2);

                return $p;
            })
            ->sortBy(fn($p) => ($p->estado_stock === 'AGOTADO' ? 0 : 1) . sprintf('%012.4f', $p->stock_vigente), SORT_ASC)
            ->toList();

        $presupuestoReposicion = $this->resumenInventario($productos)['presupuestoReposicion'];

        $this->set(compact(
            'totalProductos',
            'valorInventario',
            'valorVenta',
            'gananciaPotencial',
            'totalAgotados',
            'totalBajos',
            'totalNormales',
            'topBajoStock',
            'alertas',
            'presupuestoReposicion',
            'lotesVencidos',
            'lotesPorVencer',
            'valorVencido',
            'valorPorVencer'
        ));
    }

    public function exportarExcel()
    {
        $productos = $this->cargarInventario()
            ->sortBy(fn($p) => mb_strtolower(($p->categoria_nombre ?: '') . '|' . $p->nombre), SORT_ASC)
            ->toList();
        $r = $this->resumenInventario($productos);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventario');

        $headerStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF0DCAF0'],
            ],
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
            'border' => ['allBorders' => ['borderStyle' => 'thin']],
        ];

        $sheet->setCellValue('A1', 'Reporte de Inventario — ' . date('d/m/Y H:i'));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->setCellValue('A2', sprintf(
            'Productos: %d · Inventario al costo: S/ %s · A precio de venta: S/ %s · Agotados: %d · Stock bajo: %d · Reponer hasta el mínimo: S/ %s',
            $r['totalProductos'],
            number_format($r['valorInventario'], 2),
            number_format($r['valorVenta'], 2),
            $r['totalAgotados'],
            $r['totalBajos'],
            number_format($r['presupuestoReposicion'], 2)
        ));
        $sheet->setCellValue('A3', 'El estado se mide con el stock vigente (sin lotes vencidos). El valor de inventario incluye el stock vencido.');

        $headers = ['Categoría', 'Producto', 'Código', 'Proveedor', 'Precio Compra', 'Precio Venta', 'Margen %', 'Stock Total', 'Stock Vigente', 'Stock Vencido', 'Stock Mínimo', 'Próx. Vencimiento', 'Valor Inventario (costo)', 'Estado'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];
        $fila0 = 5;

        foreach ($columns as $idx => $col) {
            $sheet->setCellValue($col . $fila0, $headers[$idx]);
            $sheet->getStyle($col . $fila0)->applyFromArray($headerStyle);
        }

        $row = $fila0 + 1;
        foreach ($productos as $p) {
            $sheet->setCellValue('A' . $row, $p->categoria_nombre ?: '-');
            $sheet->setCellValue('B' . $row, $p->nombre);
            $sheet->setCellValue('C' . $row, $p->codigo ?: '-');
            $sheet->setCellValue('D' . $row, $p->proveedor_nombre ?: '-');
            $sheet->setCellValue('E' . $row, (float) $p->precio_compra);
            $sheet->setCellValue('F' . $row, (float) $p->precio_venta);
            $sheet->setCellValue('G' . $row, (float) $p->margen_porcentaje);
            $sheet->setCellValue('H' . $row, (float) $p->stock);
            $sheet->setCellValue('I' . $row, $p->stock_vigente);
            $sheet->setCellValue('J' . $row, $p->stock_vencido);
            $sheet->setCellValue('K' . $row, (float) $p->stock_minimo);
            $sheet->setCellValue('L' . $row, $p->prox_vencimiento ? $p->prox_vencimiento->format('d/m/Y') : '-');
            $sheet->setCellValue('M' . $row, round((float) $p->stock * (float) $p->precio_compra, 2));
            $sheet->setCellValue('N' . $row, $p->estado_stock);
            if ($p->stock_vencido > 0) {
                $sheet->getStyle('J' . $row)->getFont()->getColor()->setARGB('FFDC3545');
            }
            $row++;
        }

        $sheet->setCellValue('A' . ($row + 1), 'TOTAL');
        $sheet->setCellValue('M' . ($row + 1), round($r['valorInventario'], 2));
        $sheet->getStyle('A' . ($row + 1) . ':N' . ($row + 1))->getFont()->setBold(true);

        foreach ($columns as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('C' . ($fila0 + 1));

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
        $productos = $this->cargarInventario()
            ->sortBy(fn($p) => mb_strtolower(($p->categoria_nombre ?: '') . '|' . $p->nombre), SORT_ASC)
            ->toList();

        $this->viewBuilder()->disableAutoLayout();
        $this->set(['productos' => $productos] + $this->resumenInventario($productos));
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
     * Productos activos con su situación real de stock. Lo usan el panel, el PDF y el Excel,
     * para que los tres muestren siempre las mismas cifras.
     *
     * El stock vencido no se puede vender: agotado / bajo se miden con el stock vigente
     * (un producto con 50 unidades vencidas y 0 vigentes está agotado).
     */
    private function cargarInventario(): \Cake\Collection\CollectionInterface
    {
        $productos = $this->fetchTable('VistaReporteProductos')->find()
            ->where(['activo' => 1])
            ->all();

        $ids = $productos->map(fn($p) => (int) $p->producto_id)->toList();
        $ProductoLotes = $this->fetchTable('ProductoLotes');
        $vigente = $ProductoLotes->stockVigentePorProducto($ids);

        // Fecha de vencimiento más próxima entre los lotes que aún tienen stock.
        $proximos = [];
        if ($ids) {
            $filas = $ProductoLotes->find()
                ->select(['producto_id', 'prox' => 'MIN(fecha_vencimiento)'])
                ->where(['producto_id IN' => $ids, 'stock >' => 0, 'fecha_vencimiento IS NOT' => null])
                ->group(['producto_id'])
                ->disableHydration()
                ->all();
            foreach ($filas as $f) {
                $proximos[(int) $f['producto_id']] = \Cake\I18n\Date::parse($f['prox']);
            }
        }

        return $productos->map(function ($p) use ($vigente, $proximos) {
            $p->stock_vigente = (float) ($vigente[(int) $p->producto_id] ?? 0);
            $p->stock_vencido = max(0.0, round((float) $p->stock - $p->stock_vigente, 2));
            $p->prox_vencimiento = $proximos[(int) $p->producto_id] ?? null;
            $minimo = (float) $p->stock_minimo;
            $p->estado_stock = $p->stock_vigente <= 0 ? 'AGOTADO'
                : ($minimo > 0 && $p->stock_vigente <= $minimo ? 'BAJO' : 'NORMAL');

            return $p;
        })->compile();
    }

    /**
     * Totales del inventario. El presupuesto de reposición suma, para cada producto agotado
     * o bajo, lo que costaría llevarlo a su stock mínimo (desde el stock vigente) al costo actual.
     *
     * @param iterable $productos Salida de cargarInventario()
     */
    private function resumenInventario(iterable $productos): array
    {
        $productos = collection($productos);
        $totalProductos = $productos->count();
        $valorInventario = (float) $productos->sumOf(fn($p) => (float) $p->stock * (float) $p->precio_compra);
        $valorVenta = (float) $productos->sumOf(fn($p) => (float) $p->stock * (float) $p->precio_venta);
        $totalAgotados = $productos->filter(fn($p) => $p->estado_stock === 'AGOTADO')->count();
        $totalBajos = $productos->filter(fn($p) => $p->estado_stock === 'BAJO')->count();
        $presupuesto = (float) $productos
            ->filter(fn($p) => $p->estado_stock !== 'NORMAL')
            ->sumOf(fn($p) => round(max(0.0, (float) $p->stock_minimo - $p->stock_vigente) * (float) $p->precio_compra, 2));

        return [
            'totalProductos' => $totalProductos,
            'valorInventario' => $valorInventario,
            'valorVenta' => $valorVenta,
            'gananciaPotencial' => $valorVenta - $valorInventario,
            'totalAgotados' => $totalAgotados,
            'totalBajos' => $totalBajos,
            'totalNormales' => $totalProductos - $totalAgotados - $totalBajos,
            'presupuestoReposicion' => round($presupuesto, 2),
        ];
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
     * por producto, y calcula el costo/ganancia con el costo que tenía cada
     * producto al venderse (movimiento de stock de la venta). Solo las ventas
     * anteriores a ese registro usan el costo actual del producto.
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

        // Costo promedio por unidad con el que salió cada producto en cada venta.
        $costosVenta = [];
        $facturaIds = $items->map(fn($it) => (int) $it->invoice_id)->toList();
        if ($facturaIds) {
            $movs = $this->fetchTable('ProductoMovimientos')->find()
                ->select(['invoice_id', 'producto_id', 'cantidad', 'costo_unitario'])
                ->where([
                    'invoice_id IN' => array_unique($facturaIds),
                    'motivo_codigo' => 'VENTA',
                    'costo_unitario IS NOT' => null,
                ])
                ->disableHydration()
                ->all();
            $acum = [];
            foreach ($movs as $m) {
                $k = $m['invoice_id'] . '-' . $m['producto_id'];
                $acum[$k]['cant'] = ($acum[$k]['cant'] ?? 0) + (float) $m['cantidad'];
                $acum[$k]['costo'] = ($acum[$k]['costo'] ?? 0) + (float) $m['cantidad'] * (float) $m['costo_unitario'];
                $acum[$k]['inv'] = (int) $m['invoice_id'];
                $acum[$k]['prod'] = (int) $m['producto_id'];
            }
            foreach ($acum as $a) {
                if ($a['cant'] > 0) {
                    $costosVenta[$a['inv']][$a['prod']] = $a['costo'] / $a['cant'];
                }
            }
        }

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
                    'cantidad_vendida' => 0.0,
                    'total_vendido' => 0.0,
                    'costo_total' => 0.0,
                ];
            }

            // La cantidad de la factura viene en la presentación vendida (cajas, blísters…);
            // el costo se mide siempre en la unidad base del producto.
            $cantidadBase = $item->cantidad_base !== null
                ? (float) $item->cantidad_base
                : (float) $item->cantidad * max(1, (int) $item->factor);
            // Costo real al momento de la venta (se guarda en el movimiento de stock);
            // las ventas antiguas sin ese dato usan el costo actual del producto.
            $costoUnitario = $costosVenta[$item->invoice_id][$productoId] ?? (float) $item->producto->precio_compra;

            $porProducto[$productoId]['cantidad_vendida'] += $cantidadBase;
            $porProducto[$productoId]['total_vendido'] += (float) $item->total;
            $porProducto[$productoId]['costo_total'] += round($cantidadBase * $costoUnitario, 4);
        }

        $filas = [];
        $totalVendido = 0.0;
        $totalCosto = 0.0;
        $totalGanancia = 0.0;

        foreach ($porProducto as $p) {
            $costoTotal = round($p['costo_total'], 2);
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
