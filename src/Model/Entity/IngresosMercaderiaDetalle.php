<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class IngresosMercaderiaDetalle extends Entity
{
    protected array $_accessible = [
        'ingreso_mercaderia_id' => true,
        'producto_id' => true,
        'cantidad' => true,
        'precio_unitario' => true,
        'subtotal' => true,
        'igv' => true,
        'total' => true,
        'lote' => true,
        'fecha_vencimiento' => true,
        'created' => true,
        'modified' => true,
        'ingresos_mercaderium' => true,
        'producto' => true,
    ];
}
