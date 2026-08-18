<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Procedimiento Entity
 *
 * @property int $id
 * @property string|null $procedimiento
 * @property int|null $doctor_id
 * @property int|null $historia_id
 * @property int|null $user_id
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Doctore $doctor
 * @property \App\Model\Entity\HistoriasClinica $historias_clinica
 */
class Procedimiento extends Entity
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
        'procedimiento' => true,
        'doctor_id' => true,
        'historia_id' => true,
        'user_id' => true,
        'created' => true,
        'modified' => true,
        'doctor' => true,
        'historias_clinica' => true,
    ];
}
