<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class PagosDoctoresHistorial extends Entity
{
    protected array $_accessible = [
        'doctor_id' => true,
        'user_id' => true,
        'fecha_desde' => true,
        'fecha_hasta' => true,
        'monto_total' => true,
        'total_comprobantes' => true,
        'observaciones' => true,
        'created' => true,
        'modified' => true,
        'doctore' => true,
        'user' => true,
        'pagos_doctores_historial_invoices' => true,
    ];
}
