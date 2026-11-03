<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class IngresosMercaderiaDetalle extends Entity
{
    protected array $_accessible = [
        'ingreso_mercaderia_id' => true,
        'tipo_item' => true,
        'producto_id' => true,
        'tratamiento_id' => true,
        'examen_id' => true,
        'cantidad' => true,
        'precio_unitario' => true,
        'subtotal' => true,
        'igv' => true,
        'total' => true,
        'lote' => true,
        'fecha_vencimiento' => true,
        'producto_lote_id' => true,
        'created' => true,
        'modified' => true,
        'ingresos_mercaderium' => true,
        'producto' => true,
        'tratamiento' => true,
        'examene' => true,
    ];

    /** Nombre del producto, procedimiento o examen de la línea. */
    protected function _getNombreItem(): string
    {
        return match ($this->tipo_item ?? 'producto') {
            'tratamiento' => $this->tratamiento->nombre ?? ('Procedimiento #' . $this->tratamiento_id),
            'examen' => $this->examene->nombre ?? ('Examen #' . $this->examen_id),
            default => $this->producto->nombre ?? ('Producto #' . $this->producto_id),
        };
    }
}
