<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Proveedor extends Entity
{
    protected array $_accessible = [
        'nombre' => true,
        'ruc' => true,
        'direccion' => true,
        'whatsapp' => true,
        'email' => true,
        'activo' => true,
        'created' => true,
        'modified' => true,
    ];
}
