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
            'IngresosMercaderiaDetalle' => ['Productos', 'Tratamientos', 'Examenes'],
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
            $items = (array) ($data['items'] ?? []);
            // Si algo falla, las líneas que el usuario ya cargó se devuelven al formulario.
            $itemsForm = $this->lineasParaFormulario($items);

            // IGV configurable; por defecto 18%.
            $igvPct = 0.18;

            $subtotal = 0.0;
            $igvTotal = 0.0;
            $lineasValidas = [];

            foreach ($items as $row) {
                // Producto (suma stock por lote) o procedimiento/examen (solo se registra la compra).
                $tipoItem = (string) ($row['tipo_item'] ?? 'producto');
                if (!in_array($tipoItem, ['producto', 'tratamiento', 'examen'], true)) {
                    continue;
                }
                $itemId = (int) ($row['item_id'] ?? 0);
                $esProducto = $tipoItem === 'producto';
                // Productos y exámenes (reactivos/insumos) llevan lote y vencimiento.
                $llevaLote = $tipoItem !== 'tratamiento';
                $cantidad = (float) ($row['cantidad'] ?? 0);
                // El precio que ingresa el usuario es el UNITARIO CON IGV
                // (tal como figura en la factura del proveedor).
                $precioConIgv = (float) ($row['precio_unitario'] ?? 0);

                if ($itemId <= 0 || $cantidad <= 0) {
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
                    'tipo_item' => $tipoItem,
                    'producto_id' => $esProducto ? $itemId : null,
                    'tratamiento_id' => $tipoItem === 'tratamiento' ? $itemId : null,
                    'examen_id' => $tipoItem === 'examen' ? $itemId : null,
                    'cantidad' => $cantidad,
                    // En el detalle se guarda el valor neto unitario.
                    'precio_unitario' => $precioNeto,
                    // Y el costo unitario CON IGV, que es lo que se guarda en
                    // productos.precio_compra (desembolso real por unidad).
                    'precio_unitario_con_igv' => round($precioConIgv, 4),
                    'subtotal' => $lineaBase,
                    'igv' => $lineaIgv,
                    'total' => $lineaTotal,
                    // Lote y vencimiento aplican a productos y exámenes, no a procedimientos.
                    'lote' => $llevaLote ? (trim((string) ($row['lote'] ?? '')) ?: null) : null,
                    'fecha_vencimiento' => $llevaLote && !empty($row['fecha_vencimiento']) ? $row['fecha_vencimiento'] : null,
                ];
            }

            if (empty($lineasValidas)) {
                $this->Flash->error('Agrega al menos un producto, procedimiento o examen al ingreso.');
                return $this->prepararFormulario($ingreso, $itemsForm);
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
                return $this->prepararFormulario($ingreso, $itemsForm);
            }

            $Productos = $this->IngresosMercaderia->IngresosMercaderiaDetalle->Productos;
            $ProductoMovimientos = $this->fetchTable('ProductoMovimientos');
            $ProductoLotes = $this->fetchTable('ProductoLotes');
            $conn = $this->IngresosMercaderia->getConnection();

            $avisosVencimiento = [];
            $mensajeError = null;

            try {
                $ok = $conn->transactional(function () use ($ingreso, $lineasValidas, $Productos, $ProductoMovimientos, $ProductoLotes, &$avisosVencimiento) {
                    if (!$this->IngresosMercaderia->save($ingreso)) {
                        return false;
                    }

                    foreach ($lineasValidas as $linea) {
                        // Procedimientos y exámenes no tienen stock: solo se guarda la línea.
                        if ($linea['tipo_item'] !== 'producto') {
                            $tabla = $linea['tipo_item'] === 'tratamiento' ? 'Tratamientos' : 'Examenes';
                            $refId = $linea['tipo_item'] === 'tratamiento' ? $linea['tratamiento_id'] : $linea['examen_id'];
                            if (!$this->fetchTable($tabla)->exists(['id' => $refId])) {
                                throw new \RuntimeException('Uno de los procedimientos o exámenes del ingreso ya no existe.');
                            }

                            $detalle = $this->IngresosMercaderia->IngresosMercaderiaDetalle->newEntity([
                                'ingreso_mercaderia_id' => $ingreso->id,
                                'tipo_item' => $linea['tipo_item'],
                                'tratamiento_id' => $linea['tratamiento_id'],
                                'examen_id' => $linea['examen_id'],
                                'cantidad' => $linea['cantidad'],
                                'precio_unitario' => $linea['precio_unitario'],
                                'subtotal' => $linea['subtotal'],
                                'igv' => $linea['igv'],
                                'total' => $linea['total'],
                                'lote' => $linea['lote'],
                                'fecha_vencimiento' => $linea['fecha_vencimiento'],
                            ]);
                            $this->IngresosMercaderia->IngresosMercaderiaDetalle->saveOrFail($detalle);
                            continue;
                        }

                        $producto = $Productos->get($linea['producto_id']);

                        $detalle = $this->IngresosMercaderia->IngresosMercaderiaDetalle->newEntity([
                            'ingreso_mercaderia_id' => $ingreso->id,
                            'tipo_item' => 'producto',
                            'producto_id' => $linea['producto_id'],
                            'cantidad' => $linea['cantidad'],
                            'precio_unitario' => $linea['precio_unitario'],
                            'subtotal' => $linea['subtotal'],
                            'igv' => $linea['igv'],
                            'total' => $linea['total'],
                            'lote' => $linea['lote'],
                            'fecha_vencimiento' => $linea['fecha_vencimiento'],
                        ]);
                        $this->IngresosMercaderia->IngresosMercaderiaDetalle->saveOrFail($detalle);

                        $stockAnterior = (float) $producto->stock;

                        // Cada línea entra por su lote (con número y vencimiento). Un lote
                        // ya registrado con la misma fecha se suma; con otra fecha se rechaza.
                        $lote = $ProductoLotes->ingresar(
                            (int) $linea['producto_id'],
                            (float) $linea['cantidad'],
                            $linea['lote'],
                            $linea['fecha_vencimiento'],
                            'INGRESO',
                            (int) $detalle->id,
                            true
                        );
                        $detalle->producto_lote_id = $lote->id;
                        $this->IngresosMercaderia->IngresosMercaderiaDetalle->saveOrFail($detalle);
                        $stockNuevo = (float) $Productos->get($linea['producto_id'])->stock;

                        $dias = $lote->dias_para_vencer;
                        if ($dias !== null && $dias <= \App\Model\Table\ProductoLotesTable::DIAS_ALERTA_VENCIMIENTO) {
                            $avisosVencimiento[] = sprintf('%s (lote %s) vence en %d día(s)', $producto->nombre, $lote->lote ?: 's/n', $dias);
                        }

                        // El costo del producto pasa a ser el de esta compra (CON IGV).
                        // Una bonificación (precio 0) suma stock pero no toca el costo.
                        if ($linea['precio_unitario_con_igv'] > 0) {
                            $producto = $Productos->patchEntity($Productos->get($linea['producto_id']), [
                                'precio_compra' => $linea['precio_unitario_con_igv'],
                            ]);
                            $Productos->saveOrFail($producto);
                        }

                        $ProductoMovimientos->registrar([
                            'producto_id' => $linea['producto_id'],
                            'usuario_id' => $ingreso->usuario_id,
                            'ingreso_mercaderia_id' => $ingreso->id,
                            'tipo' => 'ingreso',
                            'motivo_codigo' => 'COMPRA',
                            'cantidad' => $linea['cantidad'],
                            'stock_anterior' => $stockAnterior,
                            'stock_nuevo' => $stockNuevo,
                            'motivo' => 'Ingreso de mercadería ' . ($ingreso->serie ? $ingreso->serie . '-' : '') . ($ingreso->numero ?: '#' . $ingreso->id),
                            'lotes' => [(int) $lote->id => (float) $linea['cantidad']],
                        ]);
                    }

                    return true;
                });
            } catch (\RuntimeException $e) {
                $ok = false;
                $mensajeError = $e->getMessage();
            }

            if ($ok) {
                $this->Flash->success('Ingreso de mercadería registrado. Stock actualizado.');
                if (!empty($avisosVencimiento)) {
                    $this->Flash->warning(
                        'Atención, mercadería próxima a vencer (menos de '
                        . \App\Model\Table\ProductoLotesTable::DIAS_ALERTA_VENCIMIENTO . ' días): '
                        . implode('; ', $avisosVencimiento) . '.'
                    );
                }
                return $this->redirect(['action' => 'view', $ingreso->id]);
            }

            $this->Flash->error($mensajeError ?? 'No se pudo registrar el ingreso.');

            return $this->prepararFormulario($ingreso, $itemsForm);
        }

        return $this->prepararFormulario($ingreso);
    }

    /**
     * Convierte las líneas enviadas por el formulario al formato que el
     * formulario necesita para volver a dibujarlas si el ingreso es rechazado.
     */
    private function lineasParaFormulario(array $items): array
    {
        $tablas = ['producto' => 'Productos', 'tratamiento' => 'Tratamientos', 'examen' => 'Examenes'];
        $nombres = [];
        foreach ($tablas as $tipo => $tabla) {
            $ids = [];
            foreach ($items as $r) {
                if (($r['tipo_item'] ?? 'producto') === $tipo && (int) ($r['item_id'] ?? 0) > 0) {
                    $ids[] = (int) $r['item_id'];
                }
            }
            $nombres[$tipo] = $ids
                ? $this->fetchTable($tabla)->find('list', keyField: 'id', valueField: 'nombre')
                    ->where(['id IN' => array_unique($ids)])->toArray()
                : [];
        }

        $lineas = [];
        foreach ($items as $row) {
            $tipo = (string) ($row['tipo_item'] ?? 'producto');
            $itemId = (int) ($row['item_id'] ?? 0);
            $nombre = $nombres[$tipo][$itemId] ?? null;
            if ($nombre === null || (float) ($row['cantidad'] ?? 0) <= 0) {
                continue;
            }
            $lineas[] = [
                'tipo_item' => $tipo,
                'item_id' => $itemId,
                'nombre' => $nombre,
                'cantidad' => (float) $row['cantidad'],
                'precio' => (float) ($row['precio_unitario'] ?? 0),
                'lote' => trim((string) ($row['lote'] ?? '')),
                'fecha_vencimiento' => (string) ($row['fecha_vencimiento'] ?? ''),
            ];
        }

        return $lineas;
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
        $ProductoLotes = $this->fetchTable('ProductoLotes');
        $conn = $this->IngresosMercaderia->getConnection();

        // Solo se puede anular si cada lote de este ingreso sigue completo: si
        // ya se vendió o se dio de baja algo de él, la mercadería ya no está.
        $lotesPorLinea = [];
        foreach ($ingreso->ingresos_mercaderia_detalle as $linea) {
            if (($linea->tipo_item ?? 'producto') !== 'producto') {
                continue; // procedimientos y exámenes no tienen stock
            }
            // El lote al que entró la línea (puede compartirse con otras entregas del mismo lote).
            $lote = $linea->producto_lote_id
                ? $ProductoLotes->find()->where(['id' => $linea->producto_lote_id])->first()
                : $ProductoLotes->find()->where(['ingreso_mercaderia_detalle_id' => $linea->id])->first();
            $producto = $Productos->get($linea->producto_id);
            $disponible = $lote ? (float) $lote->stock : (float) $producto->stock;
            if ($disponible < (float) $linea->cantidad) {
                $this->Flash->error(
                    'No se puede anular: del producto "' . $producto->nombre . '"' .
                    ($linea->lote ? ' (lote ' . $linea->lote . ')' : '') .
                    ' ya se vendió o se dio de baja parte de lo ingresado.'
                );
                return $this->redirect(['action' => 'view', $id]);
            }
            $lotesPorLinea[$linea->id] = $lote;
        }

        try {
            $ok = $conn->transactional(function () use ($ingreso, $Productos, $ProductoMovimientos, $ProductoLotes, $lotesPorLinea) {
                foreach ($ingreso->ingresos_mercaderia_detalle as $linea) {
                    if (($linea->tipo_item ?? 'producto') !== 'producto') {
                        continue;
                    }
                    $stockAnterior = (float) $Productos->get($linea->producto_id)->stock;

                    $lote = $lotesPorLinea[$linea->id];
                    $usados = $lote
                        ? $ProductoLotes->descontar((int) $linea->producto_id, (float) $linea->cantidad, true, (int) $lote->id)
                        : $ProductoLotes->descontar((int) $linea->producto_id, (float) $linea->cantidad, true);
                    $stockNuevo = (float) $Productos->get($linea->producto_id)->stock;

                    $ProductoMovimientos->registrar([
                        'producto_id' => $linea->producto_id,
                        'usuario_id' => $this->Authentication->getIdentity()?->id,
                        'ingreso_mercaderia_id' => $ingreso->id,
                        'tipo' => 'egreso',
                        'motivo_codigo' => 'ANULACION_COMPRA',
                        'cantidad' => $linea->cantidad,
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $stockNuevo,
                        'motivo' => 'Anulación de ingreso de mercadería #' . $ingreso->id,
                        'lotes' => $usados,
                    ]);
                }

                $ingreso->estado = 'ANULADO';
                return (bool) $this->IngresosMercaderia->save($ingreso);
            });
        } catch (\RuntimeException $e) {
            $this->Flash->error($e->getMessage());
            return $this->redirect(['action' => 'view', $id]);
        }

        if ($ok) {
            $this->Flash->success('Ingreso anulado y stock revertido.');
        } else {
            $this->Flash->error('No se pudo anular el ingreso.');
        }

        return $this->redirect(['action' => 'view', $id]);
    }

    /**
     * Reporte de vencimientos: lotes con stock y fecha de vencimiento,
     * clasificados por cercanía a esa fecha, con el stock real de cada lote
     * y su valor a costo (stock x precio de compra) para ver cuánto dinero
     * hay en mercadería vencida o por vencer.
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

        $query = $this->fetchTable('ProductoLotes')->find()
            ->contain([
                'Productos' => ['CategoriasProductos'],
                // LEFT en toda la cadena: los lotes de movimientos manuales o
                // devoluciones no vienen de un ingreso de mercadería.
                'IngresosMercaderiaDetalle' => [
                    'joinType' => 'LEFT',
                    'IngresosMercaderia' => [
                        'joinType' => 'LEFT',
                        'Proveedores' => ['joinType' => 'LEFT'],
                    ],
                ],
            ])
            ->where([
                'ProductoLotes.fecha_vencimiento IS NOT' => null,
                'ProductoLotes.stock >' => 0,
            ])
            ->orderBy(['ProductoLotes.fecha_vencimiento' => 'ASC']);

        if ($searchTerm !== '') {
            $like = '%' . strtolower($searchTerm) . '%';
            $query->where(function ($exp) use ($like) {
                return $exp->or([
                    'LOWER(Productos.nombre) LIKE' => $like,
                    'LOWER(Productos.codigo) LIKE' => $like,
                    'LOWER(ProductoLotes.lote) LIKE' => $like,
                ]);
            });
        }

        $lotes = $query->all();

        $limaProximo = $hoy->modify("+{$diasProximo} days");
        $filas = [];
        $resumen = ['vencido' => 0, 'proximo' => 0, 'vigente' => 0];
        $valores = ['vencido' => 0.0, 'proximo' => 0.0, 'vigente' => 0.0];

        foreach ($lotes as $l) {
            $vencInmutable = new \DateTimeImmutable($l->fecha_vencimiento->format('Y-m-d'));
            $diasRestantes = (int) $hoy->diff($vencInmutable)->format('%r%a');

            if ($vencInmutable < $hoy) {
                $estado = 'vencido';
            } elseif ($vencInmutable <= $limaProximo) {
                $estado = 'proximo';
            } else {
                $estado = 'vigente';
            }

            $valor = round((float) $l->stock * (float) ($l->producto->precio_compra ?? 0), 2);
            $resumen[$estado]++;
            $valores[$estado] += $valor;

            if ($filtro !== 'todos' && $filtro !== $estado) {
                continue;
            }

            $ingreso = $l->ingresos_mercaderia_detalle->ingresos_mercaderium ?? null;
            $filas[] = [
                'lote_id' => $l->id,
                'producto_id' => $l->producto_id,
                'producto' => $l->producto->nombre ?? ('Producto #' . $l->producto_id),
                'codigo' => $l->producto->codigo ?? '',
                'categoria' => $l->producto->categorias_producto->nombre ?? '—',
                'lote' => $l->lote ?: '—',
                'fecha_vencimiento' => $vencInmutable,
                'dias_restantes' => $diasRestantes,
                'estado' => $estado,
                'cantidad_ingresada' => (float) $l->cantidad_inicial,
                'stock_lote' => (float) $l->stock,
                'valor' => $valor,
                'ingreso_id' => $ingreso->id ?? null,
                'proveedor' => $ingreso->proveedore->nombre ?? '—',
            ];
        }
        $valores = array_map(fn($v) => round($v, 2), $valores);

        if ($this->request->getQuery('export') === 'excel') {
            return $this->exportarVencimientosExcel($filas, $diasProximo, $filtro);
        }

        $this->set(compact('filas', 'resumen', 'valores', 'diasProximo', 'filtro', 'searchTerm'));
    }

    /**
     * Reporte de compras: ingresos de mercadería por rango de fechas y proveedor,
     * con el resumen por proveedor y el detalle de cada ingreso.
     */
    public function reporte()
    {
        $desde = (string) $this->request->getQuery('desde', date('Y-m-01'));
        $hasta = (string) $this->request->getQuery('hasta', date('Y-m-d'));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $desde)) {
            $desde = date('Y-m-01');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $hasta)) {
            $hasta = date('Y-m-d');
        }
        if ($desde > $hasta) {
            [$desde, $hasta] = [$hasta, $desde];
        }
        $proveedorId = (int) $this->request->getQuery('proveedor_id', 0);
        $estado = (string) $this->request->getQuery('estado', 'REGISTRADO');
        if (!in_array($estado, ['REGISTRADO', 'ANULADO', 'TODOS'], true)) {
            $estado = 'REGISTRADO';
        }

        $query = $this->IngresosMercaderia->find()
            ->contain(['Proveedores', 'IngresosMercaderiaDetalle' => ['Productos', 'Tratamientos', 'Examenes']])
            ->where([
                'IngresosMercaderia.fecha_ingreso >=' => $desde . ' 00:00:00',
                'IngresosMercaderia.fecha_ingreso <=' => $hasta . ' 23:59:59',
            ])
            ->orderBy(['IngresosMercaderia.fecha_ingreso' => 'ASC', 'IngresosMercaderia.id' => 'ASC']);
        if ($proveedorId > 0) {
            $query->where(['IngresosMercaderia.proveedor_id' => $proveedorId]);
        }
        if ($estado !== 'TODOS') {
            $query->where(['IngresosMercaderia.estado' => $estado]);
        }

        $ingresos = [];
        $porProveedor = [];
        // Totales por moneda: los ingresos en dólares no se suman con los de soles.
        $totales = [];

        foreach ($query->all() as $ing) {
            $moneda = $ing->moneda ?: 'SOLES';
            $prov = $ing->proveedore;
            $numeroDoc = $prov ? ($prov->numero_documento ?: $prov->ruc) : null;
            $anulado = $ing->estado === 'ANULADO';

            $ingresos[] = [
                'id' => $ing->id,
                'fecha' => $ing->fecha_ingreso,
                'documento' => $ing->tipo_doc . ' ' . trim(($ing->serie ? $ing->serie . '-' : '') . ($ing->numero ?? '')),
                'proveedor' => $prov->nombre ?? '—',
                'proveedor_doc' => $numeroDoc,
                'items' => count($ing->ingresos_mercaderia_detalle ?? []),
                'detalle' => array_map(fn($d) => [
                    'tipo' => $d->tipo_item ?? 'producto',
                    'nombre' => $d->nombre_item,
                    'cantidad' => (float) $d->cantidad,
                    // En el detalle se guarda el neto; se muestra también el unitario con IGV.
                    'precio_con_igv' => (float) $d->cantidad > 0 ? round((float) $d->total / (float) $d->cantidad, 4) : 0.0,
                    'subtotal' => (float) $d->subtotal,
                    'igv' => (float) $d->igv,
                    'total' => (float) $d->total,
                    'lote' => $d->lote,
                    'vencimiento' => $d->fecha_vencimiento,
                ], $ing->ingresos_mercaderia_detalle ?? []),
                'moneda' => $moneda,
                'subtotal' => (float) $ing->subtotal,
                'igv' => (float) $ing->igv,
                'total' => (float) $ing->total,
                'estado' => $ing->estado,
            ];

            // Los anulados se listan pero no suman a los totales.
            if ($anulado) {
                continue;
            }

            $clave = (int) $ing->proveedor_id . '|' . $moneda;
            $porProveedor[$clave] ??= [
                'proveedor_id' => (int) $ing->proveedor_id,
                'proveedor' => $prov->nombre ?? '—',
                'proveedor_doc' => $numeroDoc,
                'moneda' => $moneda,
                'cantidad' => 0,
                'subtotal' => 0.0,
                'igv' => 0.0,
                'total' => 0.0,
                'ultima' => null,
            ];
            $porProveedor[$clave]['cantidad']++;
            $porProveedor[$clave]['subtotal'] += (float) $ing->subtotal;
            $porProveedor[$clave]['igv'] += (float) $ing->igv;
            $porProveedor[$clave]['total'] += (float) $ing->total;
            $porProveedor[$clave]['ultima'] = $ing->fecha_ingreso;

            $totales[$moneda] ??= ['cantidad' => 0, 'subtotal' => 0.0, 'igv' => 0.0, 'total' => 0.0];
            $totales[$moneda]['cantidad']++;
            $totales[$moneda]['subtotal'] += (float) $ing->subtotal;
            $totales[$moneda]['igv'] += (float) $ing->igv;
            $totales[$moneda]['total'] += (float) $ing->total;
        }

        // Proveedores de mayor a menor compra; el porcentaje es sobre el total de su moneda.
        $porProveedor = array_values($porProveedor);
        usort($porProveedor, fn($a, $b) => [$a['moneda'], $b['total']] <=> [$b['moneda'], $a['total']]);
        foreach ($porProveedor as &$p) {
            $totalMoneda = $totales[$p['moneda']]['total'] ?? 0;
            $p['porcentaje'] = $totalMoneda > 0 ? round($p['total'] / $totalMoneda * 100, 1) : 0;
        }
        unset($p);

        if ($this->request->getQuery('export') === 'excel') {
            return $this->exportarReporteExcel($ingresos, $porProveedor, $desde, $hasta);
        }

        $proveedores = $this->IngresosMercaderia->Proveedores
            ->find('list', keyField: 'id', valueField: 'nombre')
            ->orderBy(['nombre' => 'ASC'])
            ->toArray();

        $this->set(compact('ingresos', 'porProveedor', 'totales', 'desde', 'hasta', 'proveedorId', 'estado', 'proveedores'));
    }

    private function exportarReporteExcel(array $ingresos, array $porProveedor, string $desde, string $hasta)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $fmtFecha = fn(string $f) => (new \DateTimeImmutable($f))->format('d/m/Y');
        $periodo = 'Del ' . $fmtFecha($desde) . ' al ' . $fmtFecha($hasta);

        $encabezado = function ($sheet, array $headers, string $ultimaCol) use ($periodo) {
            $sheet->setCellValue('A1', $periodo);
            $sheet->getStyle('A1')->getFont()->setBold(true);
            $sheet->fromArray($headers, null, 'A3');
            $sheet->getStyle("A3:{$ultimaCol}3")->getFont()->setBold(true);
            $sheet->getStyle("A3:{$ultimaCol}3")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF0DCAF0');
        };

        // Hoja 1: resumen por proveedor
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Por proveedor');
        $encabezado($sheet, ['Proveedor', 'Documento', 'Moneda', 'N° Ingresos', 'Subtotal', 'IGV', 'Total', '% del total', 'Último ingreso'], 'I');
        $row = 4;
        foreach ($porProveedor as $p) {
            $sheet->fromArray([
                $p['proveedor'], $p['proveedor_doc'] ?: '-', $p['moneda'], $p['cantidad'],
                round($p['subtotal'], 2), round($p['igv'], 2), round($p['total'], 2), $p['porcentaje'],
                $p['ultima'] ? $p['ultima']->format('d/m/Y') : '-',
            ], null, "A{$row}");
            $row++;
        }
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Hoja 2: detalle de ingresos
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Ingresos');
        $encabezado($sheet, ['Ingreso #', 'Fecha', 'Documento', 'Proveedor', 'Doc. Proveedor', 'Ítems', 'Moneda', 'Subtotal', 'IGV', 'Total', 'Estado'], 'K');
        $row = 4;
        foreach ($ingresos as $i) {
            $sheet->fromArray([
                $i['id'], $i['fecha'] ? $i['fecha']->format('d/m/Y H:i') : '-', $i['documento'], $i['proveedor'],
                $i['proveedor_doc'] ?: '-', $i['items'], $i['moneda'],
                $i['subtotal'], $i['igv'], $i['total'], $i['estado'],
            ], null, "A{$row}");
            $row++;
        }
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Hoja 3: ítems de cada ingreso
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Detalle');
        $encabezado($sheet, ['Ingreso #', 'Fecha', 'Documento', 'Proveedor', 'Tipo', 'Ítem', 'Cantidad', 'P. Unit. c/IGV', 'Subtotal', 'IGV', 'Total', 'Lote', 'Vencimiento', 'Moneda', 'Estado'], 'O');
        $tipos = ['producto' => 'Producto', 'tratamiento' => 'Procedimiento', 'examen' => 'Examen'];
        $row = 4;
        foreach ($ingresos as $i) {
            foreach ($i['detalle'] as $d) {
                $sheet->fromArray([
                    $i['id'], $i['fecha'] ? $i['fecha']->format('d/m/Y') : '-', $i['documento'], $i['proveedor'],
                    $tipos[$d['tipo']] ?? $d['tipo'], $d['nombre'], $d['cantidad'], $d['precio_con_igv'],
                    $d['subtotal'], $d['igv'], $d['total'], $d['lote'] ?: '-',
                    $d['vencimiento'] ? $d['vencimiento']->format('d/m/Y') : '-', $i['moneda'], $i['estado'],
                ], null, "A{$row}");
                $row++;
            }
        }
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($tmpFile);

        return $this->response->withFile($tmpFile, [
            'download' => true,
            'name' => 'Compras_' . $desde . '_a_' . $hasta . '.xlsx',
            'delete' => true,
        ])->withType('xlsx');
    }

    private function exportarVencimientosExcel(array $filas, int $diasProximo, string $filtro)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Vencimientos');

        $headers = ['Producto', 'Código', 'Categoría', 'Lote', 'Vencimiento', 'Días', 'Estado', 'Cant. Ingresada', 'Stock del Lote', 'Valor a Costo (S/)', 'Proveedor', 'Ingreso #'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);
        $sheet->getStyle('A1:L1')->getFill()
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
            $sheet->setCellValue("I{$row}", $f['stock_lote']);
            $sheet->setCellValue("J{$row}", $f['valor']);
            $sheet->setCellValue("K{$row}", $f['proveedor']);
            $sheet->setCellValue("L{$row}", $f['ingreso_id'] ?? '-');
            $row++;
        }

        foreach (range('A', 'L') as $col) {
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
        ])->withType('xlsx');
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
                'tipo' => 'producto',
                'id' => $p->id,
                'nombre' => $p->nombre,
                'codigo' => $p->codigo ?: '',
                'unidad' => $p->unidad ?: 'NIU',
                'precio_compra' => (float) $p->precio_compra,
                'stock' => (float) $p->stock,
                'requiere_vencimiento' => (bool) $p->requiere_vencimiento,
            ];
        })->toList();

        // Procedimientos y exámenes también se pueden comprar, pero no llevan stock.
        $like = '%' . strtolower($q) . '%';
        $tratamientos = $this->fetchTable('Tratamientos')->find()
            ->where(['Tratamientos.estado' => 1])
            ->orderBy(['Tratamientos.nombre' => 'ASC'])
            ->limit(10);
        $examenes = $this->fetchTable('Examenes')->find()
            ->where(['Examenes.estado' => 1])
            ->orderBy(['Examenes.nombre' => 'ASC'])
            ->limit(10);
        if ($q !== '') {
            $tratamientos->where(['LOWER(Tratamientos.nombre) LIKE' => $like]);
            $examenes->where(['LOWER(Examenes.nombre) LIKE' => $like]);
        }

        foreach ($tratamientos->all() as $t) {
            $data[] = [
                'tipo' => 'tratamiento',
                'id' => $t->id,
                'nombre' => $t->nombre,
                'codigo' => '',
                'unidad' => 'ZZ',
                'precio_compra' => 0.0,
                'stock' => null,
                'requiere_vencimiento' => false,
            ];
        }
        foreach ($examenes->all() as $e) {
            $data[] = [
                'tipo' => 'examen',
                'id' => $e->id,
                'nombre' => $e->nombre,
                'codigo' => '',
                'unidad' => 'ZZ',
                // Lo que se paga al laboratorio por el examen
                'precio_compra' => (float) $e->precio_convenio,
                'stock' => null,
                'requiere_vencimiento' => false,
            ];
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Búsqueda AJAX de proveedor por tipo y número de documento. Si no está registrado,
     * consulta SUNAT (RUC) o RENIEC (DNI) y lo crea; para otros documentos, o si la
     * consulta falla, lo crea con el nombre ingresado a mano.
     */
    public function buscarProveedor()
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;

        $tipo = (string) $this->request->getQuery('tipo', '');
        $numero = strtoupper(trim((string) $this->request->getQuery('numero', '')));
        $nombreManual = trim((string) $this->request->getQuery('nombre', ''));

        $formatos = [
            '6' => ['/^\d{11}$/', 'El RUC debe tener 11 dígitos.'],
            '1' => ['/^\d{8}$/', 'El DNI debe tener 8 dígitos.'],
            '4' => ['/^[A-Z0-9]{4,15}$/', 'Número de documento inválido.'],
            '7' => ['/^[A-Z0-9]{4,15}$/', 'Número de documento inválido.'],
            'A' => ['/^[A-Z0-9]{4,15}$/', 'Número de documento inválido.'],
        ];
        if (!isset($formatos[$tipo])) {
            return $this->respuestaJson(['ok' => false, 'message' => 'Tipo de documento inválido.']);
        }
        if (!preg_match($formatos[$tipo][0], $numero)) {
            return $this->respuestaJson(['ok' => false, 'message' => $formatos[$tipo][1]]);
        }

        $Proveedores = $this->fetchTable('Proveedores');

        $condiciones = ['tipo_documento' => $tipo, 'numero_documento' => $numero];
        if ($tipo === '6') {
            $condiciones = ['OR' => [$condiciones, ['ruc' => $numero]]];
        }
        $proveedor = $Proveedores->find()->where($condiciones)->first();

        if ($proveedor) {
            return $this->respuestaJson(['ok' => true, 'nuevo' => false] + $this->datosProveedor($proveedor));
        }

        $datos = ['tipo_documento' => $tipo, 'numero_documento' => $numero, 'activo' => true];
        $avisoApi = null;

        if ($nombreManual !== '') {
            $datos['nombre'] = $nombreManual;
        } elseif (in_array($tipo, ['6', '1'], true)) {
            try {
                $api = new \App\Service\PeruApiService();
                if ($tipo === '6') {
                    $ruc = $api->consultarRuc($numero);
                    $datos['nombre'] = $ruc['razon_social'];
                    $datos['direccion'] = mb_substr($ruc['direccion'], 0, 255) ?: null;
                } else {
                    $datos['nombre'] = $api->consultarDni($numero)['nombre'];
                }
            } catch (\Throwable $e) {
                $avisoApi = 'No se pudo consultar ' . ($tipo === '6' ? 'SUNAT' : 'RENIEC') . '.';
            }
        }

        if (trim((string) ($datos['nombre'] ?? '')) === '') {
            return $this->respuestaJson([
                'ok' => false,
                'requiere_nombre' => true,
                'message' => trim(($avisoApi ?? 'Proveedor no registrado.') . ' Ingrese el nombre para registrarlo.'),
            ]);
        }

        $datos['nombre'] = mb_substr($datos['nombre'], 0, 150);
        $proveedor = $Proveedores->newEntity($datos);
        if (!$Proveedores->save($proveedor)) {
            return $this->respuestaJson([
                'ok' => false,
                'message' => 'No se pudo registrar el proveedor: ' . json_encode($proveedor->getErrors(), JSON_UNESCAPED_UNICODE),
            ]);
        }

        return $this->respuestaJson(['ok' => true, 'nuevo' => true] + $this->datosProveedor($proveedor));
    }

    private function datosProveedor($proveedor): array
    {
        $numero = $proveedor->numero_documento ?: $proveedor->ruc;

        return [
            'id' => (int) $proveedor->id,
            'nombre' => (string) $proveedor->nombre,
            'etiqueta' => ($numero ? $numero . ' - ' : '') . $proveedor->nombre,
            'activo' => (bool) $proveedor->activo,
        ];
    }

    private function respuestaJson(array $data)
    {
        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function prepararFormulario($ingreso, array $itemsPrevios = [])
    {
        $proveedores = $this->IngresosMercaderia->Proveedores
            ->find('list', keyField: 'id', valueField: function ($p) {
                $numero = $p->numero_documento ?: $p->ruc;

                return ($numero ? $numero . ' - ' : '') . $p->nombre;
            })
            ->where(['activo' => 1])
            ->order(['nombre' => 'ASC'])
            ->toArray();

        $this->set(compact('ingreso', 'proveedores', 'itemsPrevios'));
        $this->render('add');
    }
}
