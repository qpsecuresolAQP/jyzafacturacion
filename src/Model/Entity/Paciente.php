<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Paciente Entity
 *
 * @property int $id
 * @property string $nombre
 * @property string $apellido
 * @property string|null $telefono_celular
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Departamento $departamento
 * @property \App\Model\Entity\Campaña $campana
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Documento[] $documentos
 * @property \App\Model\Entity\HistoriasClinica[] $historias_clinicas
 * @property \App\Model\Entity\Recordatorio[] $recordatorios
 */
class Paciente extends Entity
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
        'apellido' => true,
        'telefono_celular' => true,
        'created' => true,
        'modified' => true,
        'departamento' => true,
        'campana' => true,
        'user' => true,
        'documentos' => true,
        'historias_clinicas' => true,
        'recordatorios' => true,
    ];
}
