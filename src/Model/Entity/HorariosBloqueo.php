<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * HorariosBloqueo Entity
 *
 * @property int $id
 * @property int $doctor_id
 * @property \Cake\I18n\Date $fecha
 * @property \Cake\I18n\Time $hora_inicio
 * @property \Cake\I18n\Time $hora_fin
 * @property string|null $motivo
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\HorariosDoctore $horarios_doctore
 */
class HorariosBloqueo extends Entity
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
        'doctor_id' => true,
        'fecha' => true,
        'hora_inicio' => true,
        'hora_fin' => true,
        'motivo' => true,
        'created' => true,
        'modified' => true,
        'horarios_doctore' => true,
    ];
}
