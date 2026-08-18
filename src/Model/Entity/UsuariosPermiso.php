<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UsuariosPermiso Entity
 *
 * @property int $id
 * @property int $usuario_id
 * @property int $permiso_id
 * @property bool $allow
 * @property \Cake\I18n\DateTime|null $created
 *
 * @property \App\Model\Entity\User $usuario
 * @property \App\Model\Entity\Permiso $permiso
 */
class UsuariosPermiso extends Entity
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
        'usuario_id' => true,
        'permiso_id' => true,
        'allow' => true,
        'created' => true,
        'usuario' => true,
        'permiso' => true,
    ];
}
