<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * VistaReporteConsultasDoctore Entity
 *
 * @property int|null $doctor_id
 * @property string|null $doctor_nombre
 * @property \Cake\I18n\DateTime|null $fecha_consulta
 * @property int $total_consultas
 */
class VistaReporteConsultasDoctore extends Entity
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
        'doctor_nombre' => true,
        'fecha_consulta' => true,
        'total_consultas' => true,
    ];
}
