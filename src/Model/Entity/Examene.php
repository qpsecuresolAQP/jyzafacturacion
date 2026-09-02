<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Examene extends Entity
{
    protected array $_accessible = [
        'categoria_examen_id' => true,
        'nombre' => true,
        'muestra' => true,
        'precio_convenio' => true,
        'laboratorio_id' => true,
        'comision_medico' => true,
        'gasto_materiales' => true,
        'precio' => true,
        'estado' => true,
        'created' => true,
        'modified' => true,
        'categorias_examene' => true,
        'laboratorio' => true,
    ];
}
