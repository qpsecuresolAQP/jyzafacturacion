<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * Lote(s) de los que salió cada producto vendido.
 */
class InvoiceItemLotesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('invoice_item_lotes');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => ['Model.beforeSave' => ['created' => 'new']],
        ]);

        $this->belongsTo('InvoiceItems', [
            'foreignKey' => 'invoice_item_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('ProductoLotes', [
            'foreignKey' => 'producto_lote_id',
            'joinType' => 'INNER',
        ]);
    }
}
