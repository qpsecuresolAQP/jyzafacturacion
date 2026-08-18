<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DocumentosProcedimiento Entity
 *
 * @property int $id
 * @property int|null $procedimiento_id
 * @property int|null $documento_id
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Procedimiento $procedimiento
 * @property \App\Model\Entity\Documento $documento
 */
class DocumentosProcedimiento extends Entity
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
        'procedimiento_id' => true,
        'documento_id' => true,
        'created' => true,
        'modified' => true,
        'procedimiento' => true,
        'documento' => true,
    ];
}
