<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Ingreso de Mercadería: registro de compras a proveedores que suman stock.
 * Cada ingreso guarda su documento (factura/boleta/guía), sus líneas de
 * producto (con lote y vencimiento), actualiza el stock y el precio de
 * compra de cada producto, y deja un producto_movimientos de tipo 'ingreso'
 * por línea para el historial.
 */
class IngresosMercaderiaController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $currentAction = $this->request->getParam('action');

        $this->verificarPermisoORedireccionar('IngresosMercaderia', $currentAction);
    }

    public function index()
    {
        $searchTerm = trim((string) $this->request->getQuery('search', ''));
        $estadoFiltro = (string) $this->request->getQuery('estado', 'REGISTRADO');

        $query = $this->IngresosMercaderia->find()
            ->contain(['Proveedores'])
            ->order(['IngresosMercaderia.fecha_ingreso' => 'DESC', 'IngresosMercaderia.id' => 'DESC']);

        if (in_array($estadoFiltro, ['REGISTRADO', 'ANULADO'], true)) {
            $query->where(['IngresosMercaderia.estado' => $estadoFiltro]);
        }
        // 'TODOS' no aplica filtro

        if ($searchTerm !== '') {
            $query->where(function ($exp) use ($searchTerm) {
                $like = '%' . strtolower($searchTerm) . '%';
                return $exp->or([
                    'LOWER(Proveedores.nombre) LIKE' => $like,
                    'LOWER(Proveedores.ruc) LIKE' => $like,
                    'LOWER(IngresosMercaderia.numero) LIKE' => $like,
                    'LOWER(IngresosMercaderia.serie) LIKE' => $like,
                ]);
            });
        }

        $ingresos = $this->paginate($query);

        $this->set(compact('ingresos', 'searchTerm', 'estadoFiltro'));
    }

    public function view($id = null)
    {
        $ingreso = $this->IngresosMercaderia->get($id, contain: [
            'Proveedores',
            'Users',
            'IngresosMercaderiaDetalle' => ['Productos'],
        ]);

        $this->set(compact('ingreso'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    public function add()
    {
        $ingreso = $this->IngresosMercaderia->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $items = $data['items'] ?? [];

            // IGV configurable; por defecto 18%.
            $igvPct = 0.18;

            $subtotal = 0.0;
            $igvTotal = 0.0;
            $lineasValidas = [];

            foreach ($items as $row) {
                $productoId = (int) ($row['producto_id'] ?? 0);
                $cantidad = (float) ($row['cantidad'] ?? 0);
                // El precio que ingresa el usuario es el UNITARIO CON IGV
                // (tal como figura en la factura del proveedor).
                $precioConIgv = (float) ($row['precio_unitario'] ?? 0);

                if ($productoId <= 0 || $cantidad <= 0) {
                    continue;
                }

                // Valor neto = se le quita el IGV al precio con IGV.
                $precioNeto = round($precioConIgv / (1 + $igvPct), 4);

                $lineaBase = round($cantidad * $precioNeto, 2);
                $lineaTotal = round($cantidad * $precioConIgv, 2);
                $lineaIgv = round($lineaTotal - $lineaBase, 2);

                $subtotal += $lineaBase;
                $igvTotal += $lineaIgv;

                $lineasValidas[] = [
                    'producto_id' => $productoId,
                    'cantidad' => $cantidad,
                    // En el detalle se guarda el valor neto unitario.
                    'precio_unitario' => $precioNeto,
                    // Y el costo unitario CON IGV, que es lo que se guarda en
                    // productos.precio_compra (desembolso real por unidad).
                    'precio_unitario_con_igv' => round($precioConIgv, 4),
                    'subtotal' => $lineaBase,
                    'igv' => $lineaIgv,
                    'total' => $lineaTotal,
                    'lote' => trim((string) ($row['lote'] ?? '')) ?: null,
                    'fecha_vencimiento' => !empty($row['fecha_vencimiento']) ? $row['fecha_vencimiento'] : null,
                ];
            }

            if (empty($lineasValidas)) {
                $this->Flash->error('Agrega al menos un producto al ingreso.');
                return $this->prepararFormulario($ingreso);
            }

            $subtotal = round($subtotal, 2);
            $igvTotal = round($igvTotal, 2);

            $ingreso = $this->IngresosMercaderia->patchEntity($ingreso, [
                'proveedor_id' => $data['proveedor_id'] ?? null,
                'almacen' => trim((string) ($data['almacen'] ?? 'Almacen Principal')) ?: 'Almacen Principal',
                'tipo_doc' => $data['tipo_doc'] ?? 'FACTURA',
                'serie' => trim((string) ($data['serie'] ?? '')) ?: null,
                'numero' => trim((string) ($data['numero'] ?? '')) ?: null,
                'moneda' => $data['moneda'] ?? 'SOLES',
                'fecha_ingreso' => $data['fecha_ingreso'] ?? null,
                'fecha_factura' => !empty($data['fecha_factura']) ? $data['fecha_factura'] : null,
                'observacion' => trim((string) ($data['observacion'] ?? '')) ?: null,
                'subtotal' => $subtotal,
                'igv' => $igvTotal,
                'total' => round($subtotal + $igvTotal, 2),
                'estado' => 'REGISTRADO',
                'usuario_id' => $this->Authentication->getIdentity()?->id,
            ]);

            if ($ingreso->getErrors()) {
                $this->Flash->error('Revisa los datos de la cabecera del ingreso.');
                return $this->prepararFormulario($ingreso);
            }

            $Productos = $this->IngresosMercaderia->IngresosMercaderiaDetalle->Productos;
            $ProductoMovimientos = $this->fetchTable('ProductoMovimientos');
            $conn = $this->IngresosMercaderia->getConnection();

            $ok = $conn->transactional(function () use ($ingreso, $lineasValidas, $Productos, $ProductoMovimientos) {
                if (!$this->IngresosMercaderia->save($ingreso)) {
                    return false;
                }

                foreach ($lineasValidas as $linea) {
                    $producto = $Productos->get($linea['producto_id']);

                    $detalle = $this->IngresosMercaderia->IngresosMercaderiaDetalle->newEntity([
                        'ingreso_mercaderia_id' => $ingreso->id,
                        'producto_id' => $linea['producto_id'],
                        'cantidad' => $linea['cantidad'],
                        'precio_unitario' => $linea['precio_unitario'],
                        'subtotal' => $linea['subtotal'],
                        'igv' => $linea['igv'],
                        'total' => $linea['total'],
                        'lote' => $linea['lote'],
                        'fecha_vencimiento' => $linea['fecha_vencimiento'],
                    ]);
                    if (!$this->IngresosMercaderia->IngresosMercaderiaDetalle->save($detalle)) {
                        return false;
                    }

                    $stockAnterior = (float) $producto->stock;
                    $stockNuevo = $stockAnterior + $linea['cantidad'];

                    // Suma stock y actualiza el precio de compra al último
                    // costo unitario CON IGV (desembolso real por unidad).
                    $producto = $Productos->patchEntity($producto, [
                        'stock' => $stockNuevo,
                        'precio_compra' => $linea['precio_unitario_con_igv'],
                    ]);
                    if (!$Productos->save($producto)) {
                        return false;
                    }

                    $mov = $ProductoMovimientos->newEntity([
                        'producto_id' => $linea['producto_id'],
                        'usuario_id' => $ingreso->usuario_id,
                        'ingreso_mercaderia_id' => $ingreso->id,
                        'tipo' => 'ingreso',
                        'cantidad' => $linea['cantidad'],
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $stockNuevo,
                        'motivo' => 'Ingreso de mercadería ' . ($ingreso->serie ? $ingreso->serie . '-' : '') . ($ingreso->numero ?: '#' . $ingreso->id),
                    ]);
                    if (!$ProductoMovimientos->save($mov)) {
                        return false;
                    }
                }

                return true;
            });

            if ($ok) {
                $this->Flash->success('Ingreso de mercadería registrado. Stock actualizado.');
                return $this->redirect(['action' => 'view', $ingreso->id]);
            }

            $this->Flash->error('No se pudo registrar el ingreso.');
        }

        return $this->prepararFormulario($ingreso);
    }

    /**
     * Anula un ingreso: revierte el stock de cada línea (si hay suficiente)
     * y marca el ingreso y sus movimientos como anulados.
     */
    public function anular($id = null)
    {
        $this->request->allowMethod(['post']);

        $ingreso = $this->IngresosMercaderia->get($id, contain: ['IngresosMercaderiaDetalle']);

        if ($ingreso->estado === 'ANULADO') {
            $this->Flash->error('Este ingreso ya está anulado.');
            return $this->redirect(['action' => 'view', $id]);
        }

        $Productos = $this->IngresosMercaderia->IngresosMercaderiaDetalle->Productos;
        $ProductoMovimientos = $this->fetchTable('ProductoMovimientos');
        $conn = $this->IngresosMercaderia->getConnection();

        // No permitir anular si algún producto ya no tiene stock suficiente
        // para revertir (ya se vendió/consumió lo ingresado).
        foreach ($ingreso->ingresos_mercaderia_detalle as $linea) {
            $producto = $Productos->get($linea->producto_id);
            if ((float) $producto->stock < (float) $linea->cantidad) {
                $this->Flash->error(
                    'No se puede anular: el producto "' . $producto->nombre .
                    '" no tiene stock suficiente para revertir el ingreso (se habría vendido o consumido).'
                );
                return $this->redirect(['action' => 'view', $id]);
            }
        }

        $ok = $conn->transactional(function () use ($ingreso, $Productos, $ProductoMovimientos) {
            foreach ($ingreso->ingresos_mercaderia_detalle as $linea) {
                $producto = $Productos->get($linea->producto_id);
                $stockAnterior = (float) $producto->stock;
                $stockNuevo = $stockAnterior - (float) $linea->cantidad;

                $producto = $Productos->patchEntity($producto, ['stock' => $stockNuevo]);
                if (!$Productos->save($producto)) {
                    return false;
                }

                $mov = $ProductoMovimientos->newEntity([
                    'producto_id' => $linea->producto_id,
                    'usuario_id' => $this->Authentication->getIdentity()?->id,
                    'ingreso_mercaderia_id' => $ingreso->id,
                    'tipo' => 'egreso',
                    'cantidad' => $linea->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                    'motivo' => 'Anulación de ingreso de mercadería #' . $ingreso->id,
                ]);
                if (!$ProductoMovimientos->save($mov)) {
                    return false;
                }
            }

            $ingreso->estado = 'ANULADO';
            return (bool) $this->IngresosMercaderia->save($ingreso);
        });

        if ($ok) {
            $this->Flash->success('Ingreso anulado y stock revertido.');
        } else {
            $this->Flash->error('No se pudo anular el ingreso.');
        }

        return $this->redirect(['action' => 'view', $id]);
    }

    /**
     * Reporte de vencimientos: lista las líneas de ingreso que tienen fecha
     * de vencimiento, clasificadas por cercanía a esa fecha, con la cantidad
     * ingresada de ese lote y el stock TOTAL actual del producto como
     * referencia (no es stock por lote — el sistema no lo lleva).
     */
    public function vencimientos()
    {
        $hoy = new \DateTimeImmutable('today');
        $diasProximo = (int) $this->request->getQuery('dias', 60);
        if ($diasProximo < 1) {
            $diasProximo = 60;
        }
        $filtro = (string) $this->request->getQuery('filtro', 'todos');
        $searchTerm = trim((string) $this->request->getQuery('search', ''));

        $Detalle = $this->fetchTable('IngresosMercaderiaDetalle');

        $query = $Detalle->find()
            ->contain([
                'Productos' => ['CategoriasProductos'],
                'IngresosMercaderia' => ['Proveedores'],
            ])
            ->where([
                'IngresosMercaderiaDetalle.fecha_vencimiento IS NOT' => null,
            ])
            ->order(['IngresosMercaderiaDetalle.fecha_vencimiento' => 'ASC']);

        // Excluir líneas de ingresos anulados.
        $query->matching('IngresosMercaderia', function ($q) {
            return $q->where(['IngresosMercaderia.estado' => 'REGISTRADO']);
        });

        if ($searchTerm !== '') {
            $like = '%' . strtolower($searchTerm) . '%';
            $query->where(function ($exp) use ($like) {
                return $exp->or([
                    'LOWER(Productos.nombre) LIKE' => $like,
                    'LOWER(Productos.codigo) LIKE' => $like,
                    'LOWER(IngresosMercaderiaDetalle.lote) LIKE' => $like,
                ]);
            });
        }

        $lineas = $query->all();

        $limaProximo = $hoy->modify("+{$diasProximo} days");
        $filas = [];
        $resumen = ['vencido' => 0, 'proximo' => 0, 'vigente' => 0];

        foreach ($lineas as $l) {
            $venc = $l->fecha_vencimiento; // Cake\I18n\Date
            $vencInmutable = new \DateTimeImmutable($venc->format('Y-m-d'));
            $diasRestantes = (int) $hoy->diff($vencInmutable)->format('%r%a');

            if ($vencInmutable < $hoy) {
                $estado = 'vencido';
            } elseif ($vencInmutable <= $limaProximo) {
                $estado = 'proximo';
            } else {
                $estado = 'vigente';
            }

            if ($filtro !== 'todos' && $filtro !== $estado) {
                continue;
            }

            $resumen[$estado]++;

            $filas[] = [
                'producto' => $l->producto->nombre ?? ('Producto #' . $l->producto_id),
                'codigo' => $l->producto->codigo ?? '',
                'categoria' => $l->producto->categorias_producto->nombre ?? '—',
                'lote' => $l->lote ?: '—',
                'fecha_vencimiento' => $vencInmutable,
                'dias_restantes' => $diasRestantes,
                'estado' => $estado,
                'cantidad_ingresada' => (float) $l->cantidad,
                'stock_actual' => (float) ($l->producto->stock ?? 0),
                'ingreso_id' => $l->ingreso_mercaderia_id,
                'proveedor' => $l->ingresos_mercaderium->proveedore->nombre ?? '—',
                'fecha_ingreso' => $l->ingresos_mercaderium->fecha_ingreso ?? null,
            ];
        }

        if ($this->request->getQuery('export') === 'excel') {
            return $this->exportarVencimientosExcel($filas, $diasProximo, $filtro);
        }

        $this->set(compact('filas', 'resumen', 'diasProximo', 'filtro', 'searchTerm'));
    }

    private function exportarVencimientosExcel(array $filas, int $diasProximo, string $filtro)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Vencimientos');

        $headers = ['Producto', 'Código', 'Categoría', 'Lote', 'Vencimiento', 'Días', 'Estado', 'Cant. Ingresada', 'Stock Actual', 'Proveedor', 'Ingreso #'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF0DCAF0');

        $estadoLabel = ['vencido' => 'VENCIDO', 'proximo' => 'POR VENCER', 'vigente' => 'VIGENTE'];
        $row = 2;
        foreach ($filas as $f) {
            $sheet->setCellValue("A{$row}", $f['producto']);
            $sheet->setCellValue("B{$row}", $f['codigo'] ?: '-');
            $sheet->setCellValue("C{$row}", $f['categoria']);
            $sheet->setCellValue("D{$row}", $f['lote']);
            $sheet->setCellValue("E{$row}", $f['fecha_vencimiento']->format('d/m/Y'));
            $sheet->setCellValue("F{$row}", $f['dias_restantes']);
            $sheet->setCellValue("G{$row}", $estadoLabel[$f['estado']] ?? $f['estado']);
            $sheet->setCellValue("H{$row}", $f['cantidad_ingresada']);
            $sheet->setCellValue("I{$row}", $f['stock_actual']);
            $sheet->setCellValue("J{$row}", $f['proveedor']);
            $sheet->setCellValue("K{$row}", $f['ingreso_id']);
            $row++;
        }

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Vencimientos_' . ($filtro !== 'todos' ? $filtro . '_' : '') . date('Y-m-d_His') . '.xlsx';
        $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($tmpFile);

        return $this->response->withFile($tmpFile, [
            'download' => true,
            'name' => $filename,
            'delete' => true,
        ]);
    }

    /**
     * Búsqueda AJAX de productos activos para el formulario de ingreso.
     */
    public function buscarProducto()
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;

        $q = trim((string) $this->request->getQuery('q', ''));

        $query = $this->fetchTable('Productos')->find()
            ->where(['Productos.estado' => 1])
            ->order(['Productos.nombre' => 'ASC'])
            ->limit(15);

        if ($q !== '') {
            $like = '%' . strtolower($q) . '%';
            $query->where(function ($exp) use ($like) {
                return $exp->or([
                    'LOWER(Productos.nombre) LIKE' => $like,
                    'LOWER(Productos.codigo) LIKE' => $like,
                ]);
            });
        }

        $data = $query->all()->map(function ($p) {
            return [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'codigo' => $p->codigo ?: '',
                'unidad' => $p->unidad ?: 'NIU',
                'precio_compra' => (float) $p->precio_compra,
                'stock' => (float) $p->stock,
            ];
        })->toList();

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function prepararFormulario($ingreso)
    {
        $proveedores = $this->IngresosMercaderia->Proveedores
            ->find('list', keyField: 'id', valueField: function ($p) {
                return ($p->ruc ? $p->ruc . ' - ' : '') . $p->nombre;
            })
            ->where(['activo' => 1])
            ->order(['nombre' => 'ASC'])
            ->toArray();

        $this->set(compact('ingreso', 'proveedores'));
        $this->render('add');
    }
}
