<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * Lotes que tocó cada movimiento de stock y cuántas unidades en cada uno.
 */
class ProductoMovimientoLotesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('producto_movimiento_lotes');
        $this->setPrimaryKey('id');

        $this->belongsTo('ProductoMovimientos', [
            'foreignKey' => 'producto_movimiento_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('ProductoLotes', [
            'foreignKey' => 'producto_lote_id',
            'joinType' => 'INNER',
        ]);
    }
}
