<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * VistaReportePaciente Entity
 *
 * @property int $paciente_id
 * @property string $nombre_paciente
 * @property string $apellido_paciente
 * @property \Cake\I18n\DateTime $modified
 * @property int $usuario_id
 * @property string $nombre_usuario
 * @property int $departamento_id
 * @property string $nombre_departamento
 *
 * @property \App\Model\Entity\Paciente $paciente
 * @property \App\Model\Entity\Departamento $departamento
 */
class VistaReportePaciente extends Entity
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
        'nombre_paciente' => true,
        'apellido_paciente' => true,
        'modified' => true,
        'usuario_id' => true,
        'nombre_usuario' => true,
        'departamento_id' => true,
        'nombre_departamento' => true,
        'paciente' => true,
        'departamento' => true,
    ];
}
