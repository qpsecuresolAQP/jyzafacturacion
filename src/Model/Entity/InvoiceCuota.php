<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class InvoiceCuota extends Entity
{
    protected array $_accessible = [
        'invoice_id' => true,
        'numero_cuota' => true,
        'monto' => true,
        'fecha_vencimiento' => true,
        'estado' => true,
        'metodo_pago' => true,   
        'caja_id' => true,       
        'pagado_en' => true,     
        'created' => true,
        'modified' => true,
        'invoice' => true,
    ];

    /**
     * Devuelve fecha_vencimiento siempre normalizada a formato ISO (Y-m-d),
     * sin importar si en BD/objeto viene como DateTimeInterface o como
     * string en formato local (dd/mm/yy o dd/mm/yyyy).
     *
     * Uso: $cuota->fecha_vencimiento_iso
     */
    protected function _getFechaVencimientoIso(): ?string
    {
        $valor = $this->_fields['fecha_vencimiento'] ?? null;

        if ($valor === null || $valor === '') {
            return null;
        }

        if ($valor instanceof \DateTimeInterface) {
            return $valor->format('Y-m-d');
        }

        $str = trim((string) $valor);

        // Ya viene en ISO: 2026-09-24 (con o sin hora pegada)
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $str)) {
            return substr($str, 0, 10);
        }

        // Formato local: 24/09/2026 o 24/09/26
        if (preg_match('#^(\d{2})/(\d{2})/(\d{2,4})$#', $str, $m)) {
            $anio = strlen($m[3]) === 2 ? ('20' . $m[3]) : $m[3];
            return sprintf('%04d-%02d-%02d', (int) $anio, (int) $m[2], (int) $m[1]);
        }

        return null;
    }
}