<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class InvoiceDistribucion extends Entity
{
    protected array $_accessible = [
        'invoice_id' => true,
        'tipo' => true,
        'laboratorio_id' => true,
        'monto' => true,
        'descripcion' => true,
        'created' => true,
        'modified' => true,
        'invoice' => true,
        'laboratorio' => true,
    ];
}
