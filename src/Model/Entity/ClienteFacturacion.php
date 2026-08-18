<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class ClienteFacturacion extends Entity
{
    protected array $_accessible = [
        'company_id' => true,
        'tipo_doc' => true,
        'numero_doc' => true,
        'nombre_razon_social' => true,
        'direccion' => true,
        'email' => true,
        'telefono' => true,
        'created' => true,
        'modified' => true,
        'company' => true,
        'invoices' => true,
    ];
}