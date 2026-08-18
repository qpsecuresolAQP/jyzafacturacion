<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class DoctorTratamientoTarifa extends Entity
{
    protected array $_accessible = [
        'doctor_id' => true,
        'tratamiento_id' => true,
        'monto_fijo' => true,
        'created' => true,
        'modified' => true,
        'doctore' => true,
        'tratamiento' => true,
    ];
}
