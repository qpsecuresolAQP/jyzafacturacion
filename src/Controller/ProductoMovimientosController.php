<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\ProductoLotesTable;
use App\Model\Table\ProductoMovimientosTable;

/**
 * Movimientos manuales de stock (ingreso, egreso y ajuste por conteo físico),
 * historial por producto con sus lotes, corrección de lotes y reporte de
 * pérdidas. Todo cambio de stock pasa por los lotes y queda en el historial.
 */
class ProductoMovimientosController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $currentAction = $this->request->getParam('action');

        $this->verificarPermisoORedireccionar('ProductoMovimientos', $currentAction);
    }

    public function add($productoId = null)
    {
        $producto = $this->ProductoMovimientos->Productos->get($productoId);
        $movimiento = $this->ProductoMovimientos->newEmptyEntity();
        $ProductoLotes = $this->fetchTable('ProductoLotes');

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $resultado = $this->procesarMovimiento($producto, $data);

            if ($resultado['ok']) {
                $this->Flash->success($resultado['mensaje']);
                if (!empty($resultado['aviso'])) {
                    $this->Flash->warning($resultado['aviso']);
                }

                return $this->redirect($this->origenRedirect($producto));
            }

            $this->Flash->error($resultado['mensaje']);
        }

        $lotes = $ProductoLotes->find()
            ->where(['producto_id' => $producto->id, 'stock >' => 0])
            ->orderBy(['fecha_vencimiento IS NULL' => 'ASC', 'fecha_vencimiento' => 'ASC', 'id' => 'ASC'])
            ->all();

        $this->set([
            'producto' => $producto,
            'movimiento' => $movimiento,
            'lotes' => $lotes,
            'motivosIngreso' => ProductoMovimientosTable::MOTIVOS_INGRESO,
            'motivosEgreso' => ProductoMovimientosTable::MOTIVOS_EGRESO,
            'diasAlerta' => ProductoLotesTable::DIAS_ALERTA_VENCIMIENTO,
        ]);
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    public function historial($productoId = null)
    {
        $producto = $this->ProductoMovimientos->Productos->get($productoId);

        $consulta = $this->ProductoMovimientos->find()
            ->where(['ProductoMovimientos.producto_id' => $productoId])
            ->contain([
                'Users',
                'Invoices',
                'IngresosMercaderia',
                'ProductoMovimientoLotes' => ['ProductoLotes'],
            ])
            ->orderBy(['ProductoMovimientos.created' => 'DESC', 'ProductoMovimientos.id' => 'DESC']);
        $totalMovimientos = (clone $consulta)->count();
        $movimientos = $consulta->limit(500)->all();

        $lotes = $this->fetchTable('ProductoLotes')->find()
            ->where(['producto_id' => $productoId, 'stock >' => 0])
            ->orderBy(['fecha_vencimiento IS NULL' => 'ASC', 'fecha_vencimiento' => 'ASC', 'id' => 'ASC'])
            ->all();

        $cambios = $this->fetchTable('ProductoLoteCambios')->find()
            ->contain(['Users', 'ProductoLotes'])
            ->where(['ProductoLotes.producto_id' => $productoId])
            ->orderBy(['ProductoLoteCambios.id' => 'DESC'])
            ->limit(50)
            ->all();

        $this->set(compact('producto', 'movimientos', 'totalMovimientos', 'lotes', 'cambios'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Corrige el número de lote y el vencimiento de un lote (ej. completar los
     * datos del stock anterior a los lotes). El cambio queda auditado.
     */
    public function editarLote($loteId = null)
    {
        $ProductoLotes = $this->fetchTable('ProductoLotes');
        $lote = $ProductoLotes->get($loteId, contain: ['Productos']);
        $producto = $lote->producto;

        if ($this->request->is(['post', 'put'])) {
            try {
                $cambios = $ProductoLotes->getConnection()->transactional(function () use ($ProductoLotes, $lote) {
                    return $ProductoLotes->corregirDatos(
                        (int) $lote->id,
                        (string) $this->request->getData('lote'),
                        $this->request->getData('fecha_vencimiento'),
                        $this->Authentication->getIdentity()?->getIdentifier()
                    );
                });

                $this->Flash->success($cambios ? 'Lote actualizado. El cambio quedó registrado.' : 'No hubo cambios en el lote.');

                return $this->redirect(['action' => 'historial', $producto->id]);
            } catch (\RuntimeException $e) {
                $this->Flash->error($e->getMessage());
            }
        }

        $this->set(compact('lote', 'producto'));
    }

    /**
     * Pérdidas y bajas del período: mercadería vencida, mermas, roturas y
     * faltantes de inventario, valorizadas al costo del momento.
     */
    public function perdidas()
    {
        $hoy = date('Y-m-d');
        $desde = $this->fechaFiltro('desde', date('Y-m-01'));
        $hasta = $this->fechaFiltro('hasta', $hoy);
        if ($desde > $hasta) {
            [$desde, $hasta] = [$hasta, $desde];
        }

        $movimientos = $this->ProductoMovimientos->find()
            ->contain(['Productos', 'Users', 'ProductoMovimientoLotes' => ['ProductoLotes']])
            ->where([
                'ProductoMovimientos.tipo' => 'egreso',
                'ProductoMovimientos.motivo_codigo IN' => array_merge(
                    array_keys(ProductoMovimientosTable::MOTIVOS_EGRESO),
                    ['AJUSTE_INVENTARIO']
                ),
                'DATE(ProductoMovimientos.created) >=' => $desde,
                'DATE(ProductoMovimientos.created) <=' => $hasta,
            ])
            ->orderBy(['ProductoMovimientos.created' => 'DESC', 'ProductoMovimientos.id' => 'DESC'])
            ->all();

        $resumen = [];
        $totalPerdida = 0.0;
        foreach ($movimientos as $m) {
            $codigo = (string) $m->motivo_codigo;
            $valor = round((float) $m->cantidad * (float) $m->costo_unitario, 2);
            $esPerdida = in_array($codigo, ProductoMovimientosTable::PERDIDAS, true);

            $resumen[$codigo] ??= [
                'etiqueta' => ProductoMovimientosTable::etiquetaMotivo($codigo),
                'perdida' => $esPerdida,
                'movimientos' => 0,
                'unidades' => 0.0,
                'valor' => 0.0,
            ];
            $resumen[$codigo]['movimientos']++;
            $resumen[$codigo]['unidades'] += (float) $m->cantidad;
            $resumen[$codigo]['valor'] = round($resumen[$codigo]['valor'] + $valor, 2);
            if ($esPerdida) {
                $totalPerdida = round($totalPerdida + $valor, 2);
            }
        }
        // Las pérdidas primero, de mayor a menor valor.
        uasort($resumen, fn($a, $b) => [$b['perdida'], $b['valor']] <=> [$a['perdida'], $a['valor']]);

        $this->set(compact('movimientos', 'resumen', 'totalPerdida', 'desde', 'hasta'));
    }

    /**
     * Valida y registra un movimiento manual. Devuelve ['ok', 'mensaje', 'aviso'].
     *
     * @return array{ok: bool, mensaje: string, aviso: ?string}
     */
    private function procesarMovimiento($producto, array $data): array
    {
        $tipo = (string) ($data['tipo'] ?? '');
        $observacion = trim((string) ($data['motivo'] ?? ''));
        $loteId = !empty($data['producto_lote_id']) ? (int) $data['producto_lote_id'] : null;
        $fallo = fn(string $mensaje) => ['ok' => false, 'mensaje' => $mensaje, 'aviso' => null];

        if (!in_array($tipo, ['ingreso', 'egreso', 'ajuste'], true)) {
            return $fallo('Elija el tipo de movimiento.');
        }

        $ProductoLotes = $this->fetchTable('ProductoLotes');
        $Productos = $this->ProductoMovimientos->Productos;
        $usuarioId = $this->Authentication->getIdentity()?->getIdentifier();
        $aviso = null;

        // ── Validaciones de entrada ──
        if ($tipo === 'ajuste') {
            if (!$loteId) {
                return $fallo('Elija el lote que va a ajustar.');
            }
            $contadoTexto = trim((string) ($data['cantidad_contada'] ?? ''));
            if ($contadoTexto === '' || !is_numeric($contadoTexto) || (float) $contadoTexto < 0) {
                return $fallo('Ingrese la cantidad contada (0 o más).');
            }
            $codigo = 'AJUSTE_INVENTARIO';
        } else {
            $cantidad = (float) ($data['cantidad'] ?? 0);
            if ($cantidad <= 0) {
                return $fallo('La cantidad debe ser mayor a 0.');
            }
            $codigo = (string) ($data['motivo_codigo'] ?? '');
            $permitidos = $tipo === 'ingreso'
                ? ProductoMovimientosTable::MOTIVOS_INGRESO
                : ProductoMovimientosTable::MOTIVOS_EGRESO;
            if (!isset($permitidos[$codigo])) {
                return $fallo('Elija el motivo del movimiento.');
            }
            if ($codigo === 'OTRO' && $observacion === '') {
                return $fallo('Con el motivo "Otro" explique qué pasó en la observación.');
            }
            if ($tipo === 'egreso' && $codigo === 'VENCIDO' && !$loteId) {
                return $fallo('Para dar de baja por vencimiento elija el lote vencido.');
            }
        }

        try {
            $conn = $this->ProductoMovimientos->getConnection();
            $mensaje = $conn->transactional(function () use ($producto, $tipo, $codigo, $data, $observacion, $loteId, $ProductoLotes, $Productos, $usuarioId, &$aviso) {
                $stockAnterior = (float) $Productos->get($producto->id)->stock;

                if ($tipo === 'ingreso') {
                    $cantidad = (float) $data['cantidad'];
                    $lote = $ProductoLotes->ingresar(
                        (int) $producto->id,
                        $cantidad,
                        $data['lote'] ?? null,
                        $data['fecha_vencimiento'] ?? null,
                        'MOVIMIENTO',
                        null,
                        true
                    );
                    $lotes = [(int) $lote->id => $cantidad];
                    $dias = $lote->dias_para_vencer;
                    if ($dias !== null && $dias <= ProductoLotesTable::DIAS_ALERTA_VENCIMIENTO) {
                        $aviso = sprintf('El lote %s vence en %d día(s).', $lote->lote ?: 'sin número', $dias);
                    }
                    $tipoMov = 'ingreso';
                    $cantidadMov = $cantidad;
                    $texto = 'Ingreso registrado.';
                } elseif ($tipo === 'egreso') {
                    $cantidad = (float) $data['cantidad'];
                    if ($loteId !== null) {
                        $lote = $ProductoLotes->get($loteId);
                        if ((int) $lote->producto_id !== (int) $producto->id) {
                            throw new \RuntimeException('El lote elegido no pertenece a este producto.');
                        }
                        if ($codigo === 'VENCIDO' && !$lote->vencido) {
                            throw new \RuntimeException('El lote elegido todavía no vence: no se puede dar de baja por vencimiento.');
                        }
                    }
                    // Sin lote elegido sale del que vence antes, pero nunca de uno vencido
                    // (las bajas de mercadería vencida deben elegir su lote).
                    $lotes = $ProductoLotes->descontar((int) $producto->id, $cantidad, $loteId !== null, $loteId);
                    $tipoMov = 'egreso';
                    $cantidadMov = $cantidad;
                    $texto = 'Egreso registrado.';
                } else {
                    // Se comprueba que el lote sea de este producto ANTES de tocar nada.
                    if ((int) $ProductoLotes->get($loteId)->producto_id !== (int) $producto->id) {
                        throw new \RuntimeException('El lote elegido no pertenece a este producto.');
                    }
                    $ajuste = $ProductoLotes->ajustar((int) $loteId, (float) $data['cantidad_contada']);
                    if ($ajuste['diferencia'] === 0.0) {
                        return 'El conteo coincide con el sistema: no hubo diferencia que ajustar.';
                    }
                    $tipoMov = $ajuste['diferencia'] > 0 ? 'ingreso' : 'egreso';
                    $cantidadMov = abs($ajuste['diferencia']);
                    $lotes = [(int) $ajuste['lote']->id => $cantidadMov];
                    if ($observacion === '') {
                        $observacion = sprintf('Conteo físico: sistema %s, contado %s', $this->num($ajuste['anterior']), $this->num($ajuste['nuevo']));
                    }
                    $texto = sprintf(
                        'Ajuste registrado: el lote pasó de %s a %s (%s%s).',
                        $this->num($ajuste['anterior']),
                        $this->num($ajuste['nuevo']),
                        $ajuste['diferencia'] > 0 ? '+' : '−',
                        $this->num($cantidadMov)
                    );
                }

                $this->ProductoMovimientos->registrar([
                    'producto_id' => $producto->id,
                    'usuario_id' => $usuarioId,
                    'tipo' => $tipoMov,
                    'motivo_codigo' => $codigo,
                    'cantidad' => $cantidadMov,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => (float) $Productos->get($producto->id)->stock,
                    'motivo' => $observacion !== '' ? mb_substr($observacion, 0, 255) : null,
                    'lotes' => $lotes,
                ]);

                return $texto;
            });
        } catch (\RuntimeException $e) {
            return $fallo($e->getMessage());
        }

        return ['ok' => true, 'mensaje' => $mensaje, 'aviso' => $aviso];
    }

    private function num(float $valor): string
    {
        return rtrim(rtrim(number_format($valor, 2, '.', ''), '0'), '.') ?: '0';
    }

    private function fechaFiltro(string $campo, string $porDefecto): string
    {
        $valor = (string) $this->request->getQuery($campo, $porDefecto);

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor) && checkdate((int) substr($valor, 5, 2), (int) substr($valor, 8, 2), (int) substr($valor, 0, 4))
            ? $valor
            : $porDefecto;
    }

    /**
     * Decide a dónde volver tras registrar un movimiento: si se llegó desde
     * el listado general de Productos, vuelve ahí; si se llegó desde la
     * vista de una categoría, vuelve a esa categoría (comportamiento previo).
     */
    private function origenRedirect($producto): array
    {
        $referer = $this->request->referer();
        if ($referer && str_contains($referer, '/productos') && !str_contains($referer, '/categorias-productos/')) {
            return ['controller' => 'Productos', 'action' => 'index'];
        }

        return [
            'controller' => 'CategoriasProductos',
            'action' => 'view',
            $producto->categoria_producto_id,
        ];
    }
}
