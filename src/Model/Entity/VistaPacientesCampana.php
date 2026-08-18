<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * VistaPacientesCampana Entity
 *
 * @property int $paciente_id
 * @property string $nombre
 * @property string $apellido
 * @property string|null $dni
 * @property int $campana_id
 * @property string|null $nombre_campana
 * @property \Cake\I18n\DateTime $fecha_consulta
 *
 * @property \App\Model\Entity\Paciente $paciente
 */
class VistaPacientesCampana extends Entity
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
        'paciente_id' => true,
        'nombre' => true,
        'apellido' => true,
        'dni' => true,
        'campana_id' => true,
        'nombre_campana' => true,
        'fecha_consulta' => true,
        'paciente' => true,
    ];
}
