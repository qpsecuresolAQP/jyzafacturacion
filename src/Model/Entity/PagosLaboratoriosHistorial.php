<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class PagosLaboratoriosHistorial extends Entity
{
    protected array $_accessible = [
        'laboratorio_id' => true,
        'user_id' => true,
        'fecha_desde' => true,
        'fecha_hasta' => true,
        'monto_total' => true,
        'total_comprobantes' => true,
        'observaciones' => true,
        'created' => true,
        'modified' => true,
        'laboratorio' => true,
        'user' => true,
        'pagos_laboratorios_historial_distribuciones' => true,
    ];
}
