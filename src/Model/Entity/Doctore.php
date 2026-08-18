<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Doctore Entity
 *
 * @property int $id
 * @property string $nombre
 * @property string $apellido
 * @property string $especialidad
 * @property string $telefono
 * @property string|null $email
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 */
class Doctore extends Entity
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
        'nombre' => true,
        'apellido' => true,
        'especialidad' => true,
        'telefono' => true,
        'email' => true,
        'porcentaje_pago' => true,
        'modo_pago' => true,

        'created' => true,
        'modified' => true,
        'doctor_tratamiento_tarifas' => true,
    ];
}
