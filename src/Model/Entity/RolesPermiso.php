<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RolesPermiso Entity
 *
 * @property int $id
 * @property int $rol_id
 * @property int $permiso_id
 * @property \Cake\I18n\DateTime|null $created
 *
 * @property \App\Model\Entity\Role $rol
 * @property \App\Model\Entity\Permiso $permiso
 */
class RolesPermiso extends Entity
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
        'rol_id' => true,
        'permiso_id' => true,
        'created' => true,
        'rol' => true,
        'permiso' => true,
    ];
}
