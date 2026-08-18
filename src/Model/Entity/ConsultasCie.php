<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ConsultasCie Entity
 *
 * @property int $id
 * @property int $consulta_id
 * @property int $cie_id
 *
 * @property \App\Model\Entity\Consulta $consulta
 * @property \App\Model\Entity\Diagnosticoscie10 $cy
 */
class ConsultasCie extends Entity
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
        'cie_id' => true,
        'consulta' => true,
        'categoria_id' => true,
        'cy' => true,
    ];
}
