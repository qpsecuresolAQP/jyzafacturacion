<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * HorariosDoctore Entity
 *
 * @property int $id
 * @property int $doctor_id
 * @property int $dia_semana
 * @property \Cake\I18n\Time $hora_inicio
 * @property \Cake\I18n\Time $hora_fin
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Doctore $doctor
 */
class HorariosDoctore extends Entity
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
        'dia_semana' => true,
        'hora_inicio' => true,
        'hora_fin' => true,
        'created' => true,
        'modified' => true,
        'doctor' => true,
    ];
}
