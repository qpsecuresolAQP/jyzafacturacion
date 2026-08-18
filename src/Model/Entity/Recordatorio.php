<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Recordatorio Entity
 *
 * @property int $id
 * @property int $paciente_id
 * @property string $titulo
 * @property \Cake\I18n\Date $fecha_inicio
 * @property string|null $duracion_estimada
 * @property string|null $observacion
 * @property string|null $estado_control
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Paciente $paciente
 * @property \App\Model\Entity\RecordatorioControl[] $recordatorio_controles
 */
class Recordatorio extends Entity
{
    protected array $_accessible = [
        'paciente_id' => true,
        'titulo' => true,
        'fecha_inicio' => true,
        'duracion_estimada' => true,
        'observacion' => true,
        'estado_control' => true,
        'created' => true,
        'modified' => true,
        'paciente' => true,
        'recordatorio_controles' => true,
    ];
}