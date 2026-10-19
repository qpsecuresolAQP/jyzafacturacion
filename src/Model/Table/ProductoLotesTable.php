<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\ProductoLote;
use Cake\I18n\Date;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Stock por lote con fecha de vencimiento.
 *
 * Todo cambio de stock pasa por aquí: ingresar() crea un lote, descontar()
 * saca de los lotes en orden FEFO (primero el que vence antes) y devolver()
 * repone en los lotes de donde salió. productos.stock se recalcula siempre
 * como la suma del stock de sus lotes.
 */
class ProductoLotesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('producto_lotes');
        $this->setDisplayField('lote');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Productos', [
            'foreignKey' => 'producto_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('IngresosMercaderiaDetalle', [
            'foreignKey' => 'ingreso_mercaderia_detalle_id',
            'joinType' => 'LEFT',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('producto_id')
            ->requirePresence('producto_id', 'create')
            ->notEmptyString('producto_id');

        $validator
            ->scalar('lote')
            ->maxLength('lote', 50)
            ->allowEmptyString('lote');

        $validator
            ->date('fecha_vencimiento')
            ->allowEmptyDate('fecha_vencimiento');

        $validator
            ->decimal('stock')
            ->greaterThanOrEqual('stock', 0, 'El stock de un lote no puede ser negativo.');

        return $validator;
    }

    /**
     * Días desde los que se considera "por vencer" al ingresar mercadería
     * (solo genera una advertencia, no bloquea).
     */
    public const DIAS_ALERTA_VENCIMIENTO = 90;

    /**
     * Ingresa stock a un lote y actualiza el total del producto.
     *
     * - Si ya existe un lote del producto con ese número y el mismo
     *   vencimiento, la cantidad se SUMA a ese lote (no se duplica la fila).
     * - Si existe con otro vencimiento se rechaza: un número de lote tiene una
     *   sola fecha, lo más probable es un error de digitación.
     * - Con $exigirDatos (ingresos de compra y movimientos manuales) se exige
     *   número de lote y vencimiento a los productos que lo requieren y se
     *   rechaza mercadería ya vencida.
     *
     * @throws \RuntimeException con un mensaje listo para mostrar al usuario
     */
    public function ingresar(
        int $productoId,
        float $cantidad,
        ?string $lote,
        $fechaVencimiento,
        string $origen,
        ?int $ingresoDetalleId = null,
        bool $exigirDatos = false
    ): ProductoLote {
        if ($cantidad <= 0) {
            throw new \RuntimeException('La cantidad a ingresar debe ser mayor a 0.');
        }

        $lote = $this->normalizarNumero($lote);
        $fecha = $this->parsearFecha($fechaVencimiento);
        $producto = $this->Productos->get($productoId);

        if ($exigirDatos) {
            if (!empty($producto->requiere_vencimiento) && ($lote === null || $fecha === null)) {
                throw new \RuntimeException(sprintf(
                    '"%s" exige número de lote y fecha de vencimiento.',
                    $producto->nombre
                ));
            }
            if ($fecha !== null && $fecha < Date::today()) {
                throw new \RuntimeException(sprintf(
                    'El lote%s de "%s" ya venció el %s: no se puede ingresar mercadería vencida.',
                    $lote !== null ? ' ' . $lote : '',
                    $producto->nombre,
                    $fecha->format('d/m/Y')
                ));
            }
        }

        if ($lote !== null) {
            $existente = $this->find()
                ->where(['ProductoLotes.producto_id' => $productoId, 'ProductoLotes.lote' => $lote])
                ->orderBy(['ProductoLotes.id' => 'ASC'])
                ->epilog('FOR UPDATE')
                ->first();

            if ($existente) {
                $fechaExistente = $existente->fecha_vencimiento ? $existente->fecha_vencimiento->format('Y-m-d') : null;
                $fechaNueva = $fecha ? $fecha->format('Y-m-d') : null;
                if ($fechaExistente !== $fechaNueva) {
                    throw new \RuntimeException(sprintf(
                        'El lote %s de "%s" ya está registrado %s, pero usted indicó %s. '
                        . 'Verifique la fecha o corrija el lote existente (Movimientos de stock > Editar lote).',
                        $lote,
                        $producto->nombre,
                        $fechaExistente ? 'con vencimiento ' . $existente->fecha_vencimiento->format('d/m/Y') : 'sin vencimiento',
                        $fecha ? 'el ' . $fecha->format('d/m/Y') : 'sin vencimiento'
                    ));
                }

                $existente->stock = round((float) $existente->stock + $cantidad, 2);
                $existente->cantidad_inicial = round((float) $existente->cantidad_inicial + $cantidad, 2);
                $this->saveOrFail($existente);
                $this->sincronizarStockProducto($productoId);

                return $existente;
            }
        }

        $entidad = $this->newEntity([
            'producto_id' => $productoId,
            'lote' => $lote,
            'fecha_vencimiento' => $fecha,
            'cantidad_inicial' => round($cantidad, 2),
            'stock' => round($cantidad, 2),
            'origen' => $origen,
            'ingreso_mercaderia_detalle_id' => $ingresoDetalleId,
        ]);
        $this->saveOrFail($entidad);
        $this->sincronizarStockProducto($productoId);

        return $entidad;
    }

    /**
     * Ajuste por conteo físico: deja el lote con la cantidad contada y
     * conserva su número y vencimiento (a diferencia de un ingreso, que crearía
     * un lote nuevo sin datos).
     *
     * @return array{lote: ProductoLote, anterior: float, nuevo: float, diferencia: float}
     */
    public function ajustar(int $loteId, float $contado): array
    {
        if ($contado < 0) {
            throw new \RuntimeException('La cantidad contada no puede ser negativa.');
        }

        $lote = $this->find()->where(['ProductoLotes.id' => $loteId])->epilog('FOR UPDATE')->firstOrFail();
        $anterior = round((float) $lote->stock, 2);
        $contado = round($contado, 2);
        $diferencia = round($contado - $anterior, 2);

        if ($diferencia !== 0.0) {
            $lote->stock = $contado;
            $this->saveOrFail($lote);
            $this->sincronizarStockProducto((int) $lote->producto_id);
        }

        return ['lote' => $lote, 'anterior' => $anterior, 'nuevo' => $contado, 'diferencia' => $diferencia];
    }

    /**
     * Corrige el número de lote y/o el vencimiento (ej. completar los datos de
     * stock anterior a los lotes o un error de digitación). Cada cambio queda
     * registrado con el usuario y los valores anterior y nuevo.
     *
     * @return array<int, array{campo: string, antes: ?string, despues: ?string}> cambios aplicados
     */
    public function corregirDatos(int $loteId, ?string $numero, $fechaVencimiento, ?int $usuarioId): array
    {
        $lote = $this->find()->where(['ProductoLotes.id' => $loteId])->epilog('FOR UPDATE')->firstOrFail();
        $producto = $this->Productos->get($lote->producto_id);

        $numero = $this->normalizarNumero($numero);
        $fecha = $this->parsearFecha($fechaVencimiento);

        if (!empty($producto->requiere_vencimiento) && ($numero === null || $fecha === null)) {
            throw new \RuntimeException(sprintf('"%s" exige número de lote y fecha de vencimiento.', $producto->nombre));
        }
        if ($numero !== null && $this->exists([
            'producto_id' => $lote->producto_id,
            'lote' => $numero,
            'id !=' => $lote->id,
        ])) {
            throw new \RuntimeException(sprintf('Ya existe otro lote "%s" de este producto.', $numero));
        }

        $antes = [
            'lote' => $lote->lote,
            'fecha_vencimiento' => $lote->fecha_vencimiento ? $lote->fecha_vencimiento->format('Y-m-d') : null,
        ];
        $despues = [
            'lote' => $numero,
            'fecha_vencimiento' => $fecha ? $fecha->format('Y-m-d') : null,
        ];

        $cambios = [];
        foreach ($despues as $campo => $valor) {
            if ($antes[$campo] !== $valor) {
                $cambios[] = ['campo' => $campo, 'antes' => $antes[$campo], 'despues' => $valor];
            }
        }
        if (empty($cambios)) {
            return [];
        }

        $lote->lote = $numero;
        $lote->fecha_vencimiento = $fecha;
        $this->saveOrFail($lote);

        $Cambios = \Cake\ORM\TableRegistry::getTableLocator()->get('ProductoLoteCambios');
        foreach ($cambios as $c) {
            $Cambios->saveOrFail($Cambios->newEntity([
                'producto_lote_id' => $lote->id,
                'usuario_id' => $usuarioId,
                'campo' => $c['campo'],
                'valor_anterior' => $c['antes'],
                'valor_nuevo' => $c['despues'],
            ]));
        }

        return $cambios;
    }

    private function normalizarNumero(?string $lote): ?string
    {
        $lote = trim((string) $lote);

        return $lote === '' ? null : mb_strtoupper($lote);
    }

    private function parsearFecha($valor): ?Date
    {
        if ($valor === null || $valor === '' || $valor === false) {
            return null;
        }
        if ($valor instanceof \DateTimeInterface) {
            return new Date($valor->format('Y-m-d'));
        }
        $texto = trim((string) $valor);
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $texto, $m) || !checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
            throw new \RuntimeException('La fecha de vencimiento no es válida.');
        }

        return new Date($m[1] . '-' . $m[2] . '-' . $m[3]);
    }

    /**
     * Saca $cantidad del producto recorriendo sus lotes en orden FEFO: primero
     * los que tienen vencimiento (el más próximo primero) y al final los que
     * no tienen fecha. Para ventas no se usan lotes vencidos.
     *
     * Con $loteId se saca solo de ese lote (ej. baja de un lote vencido).
     *
     * @return array<int, float> cantidad descontada por id de lote
     * @throws \RuntimeException si no alcanza el stock
     */
    public function descontar(int $productoId, float $cantidad, bool $incluirVencidos = false, ?int $loteId = null): array
    {
        $cantidad = round($cantidad, 2);
        if ($cantidad <= 0) {
            return [];
        }

        $query = $this->find()
            ->where(['ProductoLotes.producto_id' => $productoId, 'ProductoLotes.stock >' => 0])
            ->orderBy([
                'ProductoLotes.fecha_vencimiento IS NULL' => 'ASC',
                'ProductoLotes.fecha_vencimiento' => 'ASC',
                'ProductoLotes.id' => 'ASC',
            ])
            ->epilog('FOR UPDATE');

        if ($loteId !== null) {
            $query->where(['ProductoLotes.id' => $loteId]);
        } elseif (!$incluirVencidos) {
            $query->where(['OR' => [
                'ProductoLotes.fecha_vencimiento IS' => null,
                'ProductoLotes.fecha_vencimiento >=' => Date::today(),
            ]]);
        }

        $lotes = $query->all()->toList();
        $disponible = round(array_sum(array_map(fn($l) => (float) $l->stock, $lotes)), 2);

        if ($disponible < $cantidad) {
            $producto = $this->Productos->get($productoId);
            if ($loteId !== null) {
                $mensaje = sprintf('El lote seleccionado de "%s" solo tiene %s.', $producto->nombre, $this->numero($disponible));
            } elseif (!$incluirVencidos && (float) $producto->stock > $disponible) {
                $mensaje = sprintf(
                    'Stock vigente insuficiente para "%s": hay %s vigente(s) y %s vencido(s), que no se pueden vender.',
                    $producto->nombre,
                    $this->numero($disponible),
                    $this->numero((float) $producto->stock - $disponible)
                );
            } else {
                $mensaje = sprintf('Stock insuficiente para "%s": hay %s.', $producto->nombre, $this->numero($disponible));
            }
            throw new \RuntimeException($mensaje);
        }

        $asignado = [];
        $pendiente = $cantidad;
        foreach ($lotes as $lote) {
            if ($pendiente <= 0) {
                break;
            }
            $saca = min($pendiente, (float) $lote->stock);
            $lote->stock = round((float) $lote->stock - $saca, 2);
            $this->saveOrFail($lote);
            $asignado[(int) $lote->id] = round($saca, 2);
            $pendiente = round($pendiente - $saca, 2);
        }

        $this->sincronizarStockProducto($productoId);

        return $asignado;
    }

    /**
     * Repone stock en los lotes indicados (ej. al anular una venta).
     *
     * @param array<int, float> $porLote cantidad por id de lote
     */
    public function devolver(array $porLote): void
    {
        $productos = [];
        foreach ($porLote as $loteId => $cantidad) {
            if ((float) $cantidad <= 0) {
                continue;
            }
            $lote = $this->get($loteId);
            $lote->stock = round((float) $lote->stock + (float) $cantidad, 2);
            $this->saveOrFail($lote);
            $productos[(int) $lote->producto_id] = true;
        }

        foreach (array_keys($productos) as $productoId) {
            $this->sincronizarStockProducto($productoId);
        }
    }

    /**
     * productos.stock = suma del stock de sus lotes.
     */
    public function sincronizarStockProducto(int $productoId): float
    {
        $total = (float) ($this->find()
            ->select(['total' => $this->find()->func()->sum('stock')])
            ->where(['producto_id' => $productoId])
            ->disableHydration()
            ->first()['total'] ?? 0);

        $this->Productos->updateAll(['stock' => round($total, 2)], ['id' => $productoId]);

        return round($total, 2);
    }

    /**
     * Stock que se puede vender (lotes no vencidos) por producto.
     *
     * @param array<int> $productoIds
     * @return array<int, float>
     */
    public function stockVigentePorProducto(array $productoIds): array
    {
        if (empty($productoIds)) {
            return [];
        }

        $filas = $this->find()
            ->select(['producto_id', 'total' => $this->find()->func()->sum('stock')])
            ->where([
                'producto_id IN' => $productoIds,
                'stock >' => 0,
                'OR' => ['fecha_vencimiento IS' => null, 'fecha_vencimiento >=' => Date::today()],
            ])
            ->group(['producto_id'])
            ->disableHydration()
            ->all();

        $resultado = array_fill_keys($productoIds, 0.0);
        foreach ($filas as $f) {
            $resultado[(int) $f['producto_id']] = round((float) $f['total'], 2);
        }

        return $resultado;
    }

    private function numero(float $valor): string
    {
        return rtrim(rtrim(number_format($valor, 2, '.', ''), '0'), '.');
    }
}
