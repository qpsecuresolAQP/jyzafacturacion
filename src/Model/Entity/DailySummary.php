<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class DailySummary extends Entity
{
    protected array $_accessible = [
        'company_id' => true,
        'fecha' => true,
        'correlativo' => true,
        'nombre' => true,
        'ticket' => true,
        'estado' => true,
        'codigo_sunat' => true,
        'descripcion_sunat' => true,
        'xml_path' => true,
        'cdr_path' => true,
        'created' => true,
        'modified' => true,
        'company' => true,
        'invoices' => true,
    ];
}