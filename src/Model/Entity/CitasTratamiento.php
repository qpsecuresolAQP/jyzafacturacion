<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CitasTratamiento Entity
 *
 * @property int $id
 * @property int|null $tratamiento_id
 * @property int|null $cita_id
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Tratamiento $tratamiento
 * @property \App\Model\Entity\Cita $cita
 */
class CitasTratamiento extends Entity
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
        'tratamiento_id' => true,
        'cita_id' => true,
        'created' => true,
        'modified' => true,
        'tratamiento' => true,
        'cita' => true,
    ];
}
