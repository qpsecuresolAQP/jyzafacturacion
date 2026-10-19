<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\I18n\Date;
use Cake\ORM\Entity;

class ProductoLote extends Entity
{
    protected array $_accessible = [
        'producto_id' => true,
        'lote' => true,
        'fecha_vencimiento' => true,
        'cantidad_inicial' => true,
        'stock' => true,
        'origen' => true,
        'ingreso_mercaderia_detalle_id' => true,
        'created' => true,
        'modified' => true,
    ];

    /**
     * Días que faltan para vencer (negativo si ya venció); null si no tiene fecha.
     */
    protected function _getDiasParaVencer(): ?int
    {
        if ($this->fecha_vencimiento === null) {
            return null;
        }

        return (int) Date::today()->diffInDays($this->fecha_vencimiento, false);
    }

    protected function _getVencido(): bool
    {
        return $this->fecha_vencimiento !== null && $this->fecha_vencimiento < Date::today();
    }

    /**
     * Texto corto para listas: "L-123 · vence 10/03/2027 · stock 20".
     */
    protected function _getDescripcion(): string
    {
        $partes = [$this->lote ? 'Lote ' . $this->lote : 'Sin lote'];
        $partes[] = $this->fecha_vencimiento ? 'vence ' . $this->fecha_vencimiento->format('d/m/Y') : 'sin vencimiento';
        $partes[] = 'stock ' . rtrim(rtrim(number_format((float) $this->stock, 2, '.', ''), '0'), '.');

        return implode(' · ', $partes) . ($this->vencido ? ' (VENCIDO)' : '');
    }
}
