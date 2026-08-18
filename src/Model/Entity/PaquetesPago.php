<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PaquetesPago Entity
 *
 * @property int $id
 * @property int $historia_id
 * @property string $nombre
 * @property string|null $descripcion
 * @property int $num_sesiones
 * @property string $precio_total
 * @property string $tipo_pago
 * @property \Cake\I18n\Date $fecha_recordatorio
 * @property string $estado
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 * @property int $user_id
 * @property int $estado_paquete
 *
 * @property \App\Model\Entity\HistoriasClinica $historias_clinica
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\PaquetesPagosCuota[] $paquetes_pagos_cuotas
 */
class PaquetesPago extends Entity
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
        'historia_id' => true,
        'nombre' => true,
        'descripcion' => true,
        'num_sesiones' => true,
        'precio_total' => true,
        'tipo_pago' => true,
        'fecha_recordatorio' => true,
        'estado' => true,
        'created' => true,
        'modified' => true,
        'user_id' => true,
        'historias_clinica' => true,
        'user' => true,
        'paquetes_pagos_cuotas' => true,
        'estado_paquete' => true,
    ];
}
