<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Documento Entity
 *
 * @property int $id
 * @property int $historia_id
 * @property string|null $descripcion
 * @property string|null $tipo
 * @property string|null $ruta_archivo
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\HistoriasClinica $historia
 * @property \App\Model\Entity\Consulta[] $consultas
 */
class Documento extends Entity
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
        'historia_id' => true,
        'descripcion' => true,
        'tipo' => true,
        'ruta_archivo' => true,
        'created' => true,
        'modified' => true,
        'historia' => true,
        'consultas' => true,
    ];
}
