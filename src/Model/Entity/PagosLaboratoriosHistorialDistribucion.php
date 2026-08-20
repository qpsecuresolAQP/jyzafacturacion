<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class PagosLaboratoriosHistorialDistribucion extends Entity
{
    protected array $_accessible = [
        'pago_historial_id' => true,
        'invoice_distribucion_id' => true,
        'invoice_id' => true,
        'monto_pagado' => true,
        'conceptos' => true,
        'created' => true,
        'modified' => true,
        'pagos_laboratorios_historial' => true,
        'invoice_distribucion' => true,
        'invoice' => true,
    ];
}
