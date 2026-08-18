<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Cita Entity
 *
 * @property int $id
 * @property int $doctor_id
 * @property int $paciente_id
 * @property \Cake\I18n\DateTime $fecha_hora
 * @property string|null $motivo
 * @property string|null $estado
 * @property int|null $campana_id
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Doctore $doctore
 * @property \App\Model\Entity\Paciente $paciente
 */
class Cita extends Entity
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
        'doctor_id' => true,
        'paciente_id' => true,
        'fecha_hora' => true,
        'motivo' => true,
        'estado' => true,
        'duracion_minutos' => true,
        'created' => true,
        'modified' => true,
        'doctore' => true,
        'campana_id' => true,
        'paciente' => true,
        'user_id' => true,
        'tratamiento_id' => true,
        'tratamiento' => true,
        'tipo' => true,
        'hora_llegada' => true,
        'hora_inicio_consulta' => true,
        'hora_fin_consulta' => true,
    ];
}
