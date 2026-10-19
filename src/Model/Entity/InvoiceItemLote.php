<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class InvoiceItemLote extends Entity
{
    protected array $_accessible = [
        'invoice_item_id' => true,
        'producto_lote_id' => true,
        'cantidad' => true,
        'created' => true,
    ];
}
