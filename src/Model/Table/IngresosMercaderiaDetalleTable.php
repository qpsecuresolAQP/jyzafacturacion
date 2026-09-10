<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class IngresosMercaderiaDetalleTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('ingresos_mercaderia_detalle');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('IngresosMercaderia', [
            'foreignKey' => 'ingreso_mercaderia_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Productos', [
            'foreignKey' => 'producto_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('producto_id')
            ->requirePresence('producto_id', 'create')
            ->notEmptyString('producto_id');

        $validator
            ->decimal('cantidad')
            ->greaterThan('cantidad', 0, 'La cantidad debe ser mayor a 0.')
            ->requirePresence('cantidad', 'create')
            ->notEmptyString('cantidad');

        $validator
            ->decimal('precio_unitario')
            ->greaterThanOrEqual('precio_unitario', 0, 'El precio no puede ser negativo.')
            ->notEmptyString('precio_unitario');

        $validator
            ->scalar('lote')
            ->maxLength('lote', 50)
            ->allowEmptyString('lote');

        $validator
            ->date('fecha_vencimiento')
            ->allowEmptyDate('fecha_vencimiento');

        return $validator;
    }
}
