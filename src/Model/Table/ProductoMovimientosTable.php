<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ProductoMovimientosTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('producto_movimientos');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Productos', [
            'foreignKey' => 'producto_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'usuario_id',
        ]);

        $this->belongsTo('IngresosMercaderia', [
            'foreignKey' => 'ingreso_mercaderia_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->inList('tipo', ['ingreso', 'egreso'])
            ->requirePresence('tipo', 'create')
            ->notEmptyString('tipo');

        $validator
            ->decimal('cantidad')
            ->greaterThan('cantidad', 0, 'La cantidad debe ser mayor a 0.')
            ->requirePresence('cantidad', 'create')
            ->notEmptyString('cantidad');

        $validator
            ->scalar('motivo')
            ->maxLength('motivo', 255)
            ->allowEmptyString('motivo', null);

        return $validator;
    }
}
