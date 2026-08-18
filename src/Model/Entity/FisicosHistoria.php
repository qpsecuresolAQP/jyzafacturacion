<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FisicosHistoria Entity
 *
 * @property int $id
 * @property int $examen_id
 * @property int $historia_id
 *
 * @property \App\Model\Entity\ExamenesFisico $examen
 * @property \App\Model\Entity\HistoriasClinica $historia
 */
class FisicosHistoria extends Entity
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
        'examen_id' => true,
        'historia_id' => true,
        'examen' => true,
        'historia' => true,
    ];
}
