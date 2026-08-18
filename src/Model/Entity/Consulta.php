<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Consulta Entity
 *
 * @property int $id
 * @property string|null $motivo
 * @property string|null $prescripcion
 * @property string|null $diagnostico
 * @property string|null $orden_medico
 * @property int|null $examen_fisico
 * @property int|null $documento_id
 * @property int|null $historia_id
 * @property int|null $consultas_cie
 * @property int|null $doctor_id
 * @property int $user_id
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\HistoriasClinica $historias_clinicas
 * @property \App\Model\Entity\Doctore $doctore
 * @property \App\Model\Entity\HistoriasConsulta[] $historias_consultas
 * @property \App\Model\Entity\Receta[] $recetas
 */
class Consulta extends Entity
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
        'motivo' => true,
        'prescripcion' => true,
        'orden_medico' => true,
        'examen_fisico' => true,
        'diagnostico' => true,
        'historia_id' => true,
        'consultas_cie' => true,
        'user_id' => true,
        'doctor_id' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'historias_clinicas' => true,
        'doctore' => true,
        'historias_consultas' => true,
        'recetas' => true,
    ];
}
