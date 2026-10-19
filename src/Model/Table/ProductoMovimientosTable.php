<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Historial de movimientos de stock de un producto. Todo cambio de stock
 * (compras, ventas, anulaciones, ajustes y movimientos manuales) deja un
 * movimiento con el motivo, el costo del momento y los lotes que tocó.
 */
class ProductoMovimientosTable extends Table
{
    /** Motivos que se pueden elegir en un ingreso manual. */
    public const MOTIVOS_INGRESO = [
        'DEVOLUCION_CLIENTE' => 'Devolución de un cliente',
        'STOCK_INICIAL' => 'Carga de stock inicial',
        'OTRO' => 'Otro (explique)',
    ];

    /** Motivos que se pueden elegir en un egreso manual. */
    public const MOTIVOS_EGRESO = [
        'VENCIDO' => 'Producto vencido',
        'MERMA' => 'Merma o deterioro',
        'ROTURA' => 'Rotura o daño',
        'USO_INTERNO' => 'Uso interno',
        'DEVOLUCION_PROVEEDOR' => 'Devolución a proveedor',
        'OTRO' => 'Otro (explique)',
    ];

    /** Motivos que genera el propio sistema. */
    public const MOTIVOS_SISTEMA = [
        'COMPRA' => 'Compra',
        'ANULACION_COMPRA' => 'Anulación de compra',
        'VENTA' => 'Venta',
        'ANULACION_VENTA' => 'Anulación de venta',
        'AJUSTE_INVENTARIO' => 'Ajuste por conteo físico',
    ];

    /**
     * Egresos que representan dinero perdido (se suman en el reporte de
     * pérdidas): mercadería vencida, mermas, roturas y faltantes de inventario.
     */
    public const PERDIDAS = ['VENCIDO', 'MERMA', 'ROTURA', 'AJUSTE_INVENTARIO'];

    public static function etiquetaMotivo(?string $codigo): string
    {
        $todos = self::MOTIVOS_INGRESO + self::MOTIVOS_EGRESO + self::MOTIVOS_SISTEMA;

        return $todos[(string) $codigo] ?? 'Sin clasificar';
    }

    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('producto_movimientos');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Productos', [
            'foreignKey' => 'producto_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'usuario_id',
        ]);

        $this->belongsTo('IngresosMercaderia', [
            'foreignKey' => 'ingreso_mercaderia_id',
        ]);

        $this->belongsTo('Invoices', [
            'foreignKey' => 'invoice_id',
        ]);

        $this->hasMany('ProductoMovimientoLotes', [
            'foreignKey' => 'producto_movimiento_id',
            'dependent' => true,
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->inList('tipo', ['ingreso', 'egreso'])
            ->requirePresence('tipo', 'create')
            ->notEmptyString('tipo');

        $validator
            ->decimal('cantidad')
            ->greaterThan('cantidad', 0, 'La cantidad debe ser mayor a 0.')
            ->requirePresence('cantidad', 'create')
            ->notEmptyString('cantidad');

        $validator
            ->scalar('motivo')
            ->maxLength('motivo', 255)
            ->allowEmptyString('motivo', null);

        $validator
            ->scalar('motivo_codigo')
            ->maxLength('motivo_codigo', 30)
            ->allowEmptyString('motivo_codigo');

        return $validator;
    }

    /**
     * Registra un movimiento de stock con los lotes que tocó.
     *
     * $datos: producto_id, tipo (ingreso|egreso), cantidad, motivo_codigo,
     * stock_anterior, stock_nuevo, y opcionalmente motivo (detalle libre),
     * usuario_id, ingreso_mercaderia_id, invoice_id y lotes [id_lote => cantidad].
     * El costo unitario se toma del producto en este momento, para poder
     * valorizar bajas y pérdidas aunque el costo cambie después.
     */
    public function registrar(array $datos): EntityInterface
    {
        $lotes = $datos['lotes'] ?? [];
        unset($datos['lotes']);

        $producto = $this->Productos->get($datos['producto_id']);
        $movimiento = $this->newEntity($datos + ['costo_unitario' => $producto->precio_compra]);
        $this->saveOrFail($movimiento);

        $Detalle = $this->ProductoMovimientoLotes->getTarget();
        foreach ($lotes as $loteId => $cantidad) {
            if ((float) $cantidad <= 0) {
                continue;
            }
            $Detalle->saveOrFail($Detalle->newEntity([
                'producto_movimiento_id' => $movimiento->id,
                'producto_lote_id' => (int) $loteId,
                'cantidad' => round((float) $cantidad, 2),
            ]));
        }

        return $movimiento;
    }
}
