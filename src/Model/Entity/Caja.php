<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Caja extends Entity
{
    protected array $_accessible = [
        'nombre' => true,
        'codigo' => true,
        'user_id' => true,
        'fecha' => true,
        'monto_inicial' => true,
        'monto_cierre' => true,
        'estado' => true,
        'observacion' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'caja_movimientos' => true,
    ];
}