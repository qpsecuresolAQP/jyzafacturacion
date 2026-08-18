<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RecordatorioControl Entity
 *
 * @property int $id
 * @property int $recordatorio_id
 * @property \Cake\I18n\Date $fecha_control
 * @property \Cake\I18n\Date|null $proximo_control
 * @property string|null $detalles
 * @property string|null $productos_utilizados
 * @property string|null $observaciones
 * @property bool $recordatorio_enviado
 * @property string|null $estado
 * @property string|null $estado_control
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Recordatorio $recordatorio
 */
class RecordatorioControl extends Entity
{
    protected array $_accessible = [
        'recordatorio_id' => true,
        'fecha_control' => true,
        'proximo_control' => true,
        'detalles' => true,
        'productos_utilizados' => true,
        'observaciones' => true,
        'recordatorio_enviado' => true,
        'estado' => true,
        'estado_control' => true,
        'created' => true,
        'modified' => true,
        'recordatorio' => true,
    ];
}