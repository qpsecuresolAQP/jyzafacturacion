<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class CategoriasExamene extends Entity
{
    protected array $_accessible = [
        'nombre' => true,
        'estado' => true,
        'created' => true,
        'modified' => true,
        'examenes' => true,
    ];
}
