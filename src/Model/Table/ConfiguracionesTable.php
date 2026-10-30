<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * Ajustes generales del sistema en formato clave/valor.
 */
class ConfiguracionesTable extends Table
{
    public const CAJA_PASSWORD_HASH = 'caja_password_hash';

    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('configuraciones');
        $this->setPrimaryKey('clave');
        $this->setDisplayField('clave');

        $this->addBehavior('Timestamp');
    }

    public function obtener(string $clave): ?string
    {
        $fila = $this->find()->where(['clave' => $clave])->first();

        return $fila ? $fila->get('valor') : null;
    }

    public function guardar(string $clave, ?string $valor): bool
    {
        $fila = $this->find()->where(['clave' => $clave])->first()
            ?? $this->newEntity(['clave' => $clave], ['accessibleFields' => ['clave' => true]]);
        $fila->set('valor', $valor);

        return (bool)$this->save($fila);
    }
}
