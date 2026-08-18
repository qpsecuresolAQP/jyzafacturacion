<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RecetasConsulta Entity
 *
 * @property int $id
 * @property int $consulta_id
 * @property int $receta_id
 * @property string|null $observaciones
 * @property string|null $descripcion
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Consulta $consulta
 * @property \App\Model\Entity\Doctore $doctore
 * @property \App\Model\Entity\Receta $receta
 */
class RecetasConsulta extends Entity
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
        'consulta_id' => true,
        'receta_id' => true,
        'observaciones' => true,
        'descripcion' => true,
        'created' => true,
        'modified' => true,
        'consulta' => true,
        'doctore' => true,
        'receta' => true,
    ];
}
