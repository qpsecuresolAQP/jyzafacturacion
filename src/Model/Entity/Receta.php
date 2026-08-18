<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Receta Entity
 *
 * @property int $id
 * @property string|null $nombre
 * @property string|null $descripcion
 * @property string|null $notas
 * @property string|null $indicaciones_consolidadas
 * @property string|null $tipo_usuario
 * @property string|null $tipo_atencion
 * @property string|null $especialidad_medica
 * @property string|null $h_cl
 * @property string|null $sis
 * @property string|null $particular
 * @property float|null $peso
 * @property \Cake\I18n\Date|null $valido_hasta
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Consulta[] $consultas
 */
class Receta extends Entity
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
        'descripcion' => true,
        'notas' => true,
        'indicaciones_consolidadas' => true,
        'tipo_usuario' => true,
        'tipo_atencion' => true,
        'especialidad_medica' => true,
        'h_cl' => true,
        'sis' => true,
        'particular' => true,
        'peso' => true,
        'valido_hasta' => true,
        'created' => true,
        'modified' => true,
        'consultas' => true,
    ];
}
