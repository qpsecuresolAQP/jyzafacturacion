<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Permiso Entity
 *
 * @property int $id
 * @property string $controller
 * @property string $action
 * @property string|null $descripcion
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\UsuariosPermiso[] $usuarios_permisos
 * @property \App\Model\Entity\Role[] $roles
 */
class Permiso extends Entity
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
        'controller' => true,
        'action' => true,
        'descripcion' => true,
        'created' => true,
        'modified' => true,
        'usuarios_permisos' => true,
        'roles' => true,
    ];
}
