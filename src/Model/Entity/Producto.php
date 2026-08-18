<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Producto extends Entity
{
    protected array $_accessible = [
        'categoria_producto_id' => true,
        'proveedor_id' => true,
        'nombre' => true,
        'descripcion' => true,
        'codigo' => true,
        'codigo_sunat' => true,
        'unidad' => true,
        'precio' => true,
        'precio_compra' => true,
        'stock' => true,
        'stock_minimo' => true,
        'estado' => true,
        'created' => true,
        'modified' => true,
        'categoria_producto' => true,
        'proveedor' => true,
    ];
}