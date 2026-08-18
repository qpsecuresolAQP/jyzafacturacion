<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PaquetesPagosCuota Entity
 *
 * @property int $id
 * @property int $paquete_pago_id
 * @property string $titulo_referencial
 * @property string $monto
 * @property \Cake\I18n\Date $fecha_recordatorio
 * @property string $estado
 * @property \Cake\I18n\DateTime|null $fecha_pago
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 * @property int $estado_cuota
 *
 * @property \App\Model\Entity\PaquetesPago $paquetes_pago
 */
class PaquetesPagosCuota extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'paquete_pago_id' => true,
        'titulo_referencial' => true,
        'monto' => true,
        'fecha_recordatorio' => true,
        'estado' => true,
        'fecha_pago' => true,
        'created' => true,
        'modified' => true,
        'paquetes_pago' => true,
        'estado_cuota' => true,
    ];
}
