<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class InvoiceItem extends Entity
{
    protected array $_accessible = [
        'invoice_id' => true,
        'tratamiento_id' => true,
        'descripcion' => true,
        'cantidad' => true,
        'valor_unitario' => true,
        'precio_unitario' => true,
        'tipo_item' => true,
        'producto_id' => true,
        'producto' => true,
        'examen_id' => true,
        'examene' => true,
        'base_igv' => true,
        'igv' => true,
        'total' => true,
        'codigo_producto' => true,
        'unidad' => true,
        'created' => true,
        'modified' => true,
        'invoice' => true,
        'tratamiento' => true,
        'descuento'      => true,
        'descuento_tipo' => true,
        'nombre_sunat' => true,

    ];
}