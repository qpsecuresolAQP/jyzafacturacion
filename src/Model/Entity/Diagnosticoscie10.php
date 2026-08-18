<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Diagnosticoscie10 Entity
 *
 * @property int $id
 * @property string $clave
 * @property string $descripcion
 * @property int $idCategoria
 */
class Diagnosticoscie10 extends Entity
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
        'clave' => true,
        'descripcion' => true,
        'idCategoria' => true,
    ];
}
