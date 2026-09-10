<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class IngresosMercaderium extends Entity
{
    protected array $_accessible = [
        'proveedor_id' => true,
        'usuario_id' => true,
        'almacen' => true,
        'tipo_doc' => true,
        'serie' => true,
        'numero' => true,
        'moneda' => true,
        'fecha_ingreso' => true,
        'fecha_factura' => true,
        'subtotal' => true,
        'igv' => true,
        'total' => true,
        'observacion' => true,
        'estado' => true,
        'created' => true,
        'modified' => true,
        'proveedor' => true,
        'user' => true,
        'ingresos_mercaderia_detalle' => true,
    ];
}
