<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class PagosDoctoresHistorialMovimiento extends Entity
{
    protected array $_accessible = [
        'pago_historial_id' => true,
        'caja_movimiento_id' => true,
        'invoice_id' => true,
        'metodo_pago' => true,
        'base_doctor' => true,
        'monto_pagado' => true,
        'conceptos' => true,
        'created' => true,
        'modified' => true,
        'pagos_doctores_historial' => true,
        'caja_movimiento' => true,
        'invoice' => true,
    ];
}
