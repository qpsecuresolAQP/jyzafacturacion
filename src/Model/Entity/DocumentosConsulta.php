<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DocumentosConsulta Entity
 *
 * @property int $id
 * @property int|null $consulta_id
 * @property int|null $documento_id
 * @property string|null $ruta_archivo
 * @property string|null $descripcion
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Consulta $consulta
 * @property \App\Model\Entity\Documento $documento
 */
class DocumentosConsulta extends Entity
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
        'consulta_id' => true,
        'documento_id' => true,
        'ruta_archivo' => true,
        'descripcion' => true,
        'created' => true,
        'modified' => true,
        'consulta' => true,
        'documento' => true,
    ];
}
