<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * HistoriasClinica Entity
 *
 * @property int $id
 * @property int|null $paciente_id
 * @property string|null $dni
 * @property \Cake\I18n\Date|null $fecha_nacimiento
 * @property int|null $edad
 * @property int|null $departamento_id
 * @property string|null $sexo
 * @property string|null $tipo_orden
 * @property int|null $user_id
 * @property string|null $como_entero
 * @property string|null $obs_administrativas
 * @property string|null $ocupacion
 * @property string $email
 * @property string $parentesco
 * @property string $apoderado
 * @property string $recomendado
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Paciente $paciente
 * @property \App\Model\Entity\Departamento $departamento
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Documento[] $documentos
 * @property \App\Model\Entity\Consulta[] $consultas
 * @property \App\Model\Entity\Procedimiento[] $procedimientos
 * @property \App\Model\Entity\ExamenesFisico[] $examenes_fisicos
 * @property \App\Model\Entity\EvaluacionesCardiovasculare[] $evaluaciones_cardiovasculares
 * @property \App\Model\Entity\RiesgosQuirurgicosCardiovasculare[] $riesgos_quirurgicos_cardiovasculares
 */
class HistoriasClinica extends Entity
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
        'dni' => true,
        'fecha_nacimiento' => true,
        'edad' => true,
        'departamento_id' => true,
        'sexo' => true,
        'tipo_orden' => true,
        'user_id' => true,
        'como_entero' => true,
        'obs_administrativas' => true,
        'ocupacion' => true,
        'email' => true,
        'parentesco' => true,
        'apoderado' => true,
        'recomendado' => true,
        'created' => true,
        'modified' => true,
        'paciente' => true,
        'departamento' => true,
        'user' => true,
        'documentos' => true,
        'consultas' => true,
        'procedimientos' => true,
        'examenes_fisicos' => true,
        'evaluaciones_cardiovasculares' => true,
        'riesgos_quirurgicos_cardiovasculares' => true,
        'direccion' => true,
    ];
}
