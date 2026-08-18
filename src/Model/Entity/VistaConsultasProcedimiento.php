<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * VistaConsultasProcedimiento Entity
 *
 * @property string|null $dni
 * @property string $nombre
 * @property string $apellido
 * @property string|null $motivo
 * @property string|resource|null $procedimiento
 */
class VistaConsultasProcedimiento extends Entity
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
        'dni' => true,
        'nombre' => true,
        'apellido' => true,
        'motivo' => true,
        'procedimiento' => true,
    ];
}
