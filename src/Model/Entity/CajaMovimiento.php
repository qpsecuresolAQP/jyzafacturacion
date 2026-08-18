<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class CajaMovimiento extends Entity
{
    protected array $_accessible = [
        'caja_id' => true,
        'invoice_id' => true,
        'metodo_pago' => true,
        'monto_total' => true,
        'monto_recibido' => true,
        'vuelto' => true,
        'created' => true,
        'modified' => true,
        'caja' => true,
        'invoice' => true,
    ];
}