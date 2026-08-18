<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Invoice extends Entity
{
    protected array $_accessible = [
        'company_id' => true,
        'paciente_id' => true,
        'historia_clinica_id' => true,
        'doctor_id' => true,
        'user_id' => true,
        'caja_id' => true,

        'tipo_doc' => true,
        'serie' => true,
        'correlativo' => true,
        'cliente_tipo_doc' => true,
        'cliente_numero' => true,
        'cliente_nombre' => true,
        'cliente_direccion' => true,
        'cliente_email' => true,
        'subtotal' => true,
        'igv' => true,
        'total' => true,
        'estado' => true,
        'codigo_sunat' => true,
        'descripcion_sunat' => true,
        'xml_path' => true,
        'cdr_path' => true,
        'hash_xml' => true,
        'daily_summary_id' => true,
        'enviado_sunat_at' => true,
        'created' => true,
        'modified' => true,
        'company' => true,
        'paciente' => true,
        'historia_clinica' => true,
        'invoice_items' => true,
        'daily_summary' => true,
        'cliente_facturacion_id' => true,
        'cliente_facturacion' => true,
        'pdf_path' => true,
        'caja_movimientos' => true,
        'user' => true,
        'caja' => true,
        'forma_pago' => true,
        'invoice_cuotas' => true,
    ];
}