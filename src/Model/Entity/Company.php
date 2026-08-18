<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Company extends Entity
{
    protected array $_accessible = [
        'ruc' => true,
        'razon_social' => true,
        'nombre_comercial' => true,
        'direccion' => true,
        'ubigeo' => true,
        'departamento' => true,
        'provincia' => true,
        'distrito' => true,
        'urbanizacion' => true,
        'cod_local' => true,
        'sol_user' => true,
        'sol_pass' => true,
        'certificado_sunat' => true,
        'ambiente' => true,
        'created' => true,
        'modified' => true,
        'invoices' => true,
        'daily_summaries' => true,
    ];
}