<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ExamenesFisico Entity
 *
 * @property int $id
 * @property int|null $edad
 * @property string|null $peso
 * @property string|null $altura
 * @property string|null $temperatura
 * @property string|null $imc
 * @property string|null $fr
 * @property string|null $eva
 * @property string|null $presion
 * @property string|null $frecuencia_cardiaca
 * @property string|null $saturacion
 * @property string|null $glicemina
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\HistoriasClinica[] $historias_clinicas
 */
class ExamenesFisico extends Entity
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
        'edad' => true,
        'peso' => true,
        'altura' => true,
        'temperatura' => true,
        'imc' => true,
        'fr' => true,
        'eva' => true,
        'presion' => true,
        'frecuencia_cardiaca' => true,
        'saturacion' => true,
        'glicemina' => true,
        'created' => true,
        'modified' => true,
        'historias_clinicas' => true,
    ];
}
