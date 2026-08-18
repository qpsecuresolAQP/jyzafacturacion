<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * VistaRecetasDepartamento Entity
 *
 * @property int $departamento_id
 * @property string $nombre_departamento
 * @property int $paciente_id
 * @property string $nombre_paciente
 * @property int $receta_id
 * @property string|null $nombre_receta
 * @property \Cake\I18n\DateTime $fecha_consulta
 *
 * @property \App\Model\Entity\Departamento $departamento
 * @property \App\Model\Entity\Paciente $paciente
 * @property \App\Model\Entity\Receta $receta
 */
class VistaRecetasDepartamento extends Entity
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
        'departamento_id' => true,
        'nombre_departamento' => true,
        'paciente_id' => true,
        'nombre_paciente' => true,
        'receta_id' => true,
        'nombre_receta' => true,
        'fecha_consulta' => true,
        'departamento' => true,
        'paciente' => true,
        'receta' => true,
    ];
}
