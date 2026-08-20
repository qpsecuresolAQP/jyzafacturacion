<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PagosDoctoresHistorialMovimientosTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('pagos_doctores_historial_movimientos');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('PagosDoctoresHistorial', [
            'foreignKey' => 'pago_historial_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('CajaMovimientos', [
            'foreignKey' => 'caja_movimiento_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Invoices', [
            'foreignKey' => 'invoice_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('pago_historial_id')
            ->requirePresence('pago_historial_id', 'create')
            ->notEmptyString('pago_historial_id');

        $validator
            ->integer('caja_movimiento_id')
            ->requirePresence('caja_movimiento_id', 'create')
            ->notEmptyString('caja_movimiento_id');

        $validator
            ->integer('invoice_id')
            ->requirePresence('invoice_id', 'create')
            ->notEmptyString('invoice_id');

        $validator
            ->scalar('metodo_pago')
            ->requirePresence('metodo_pago', 'create')
            ->notEmptyString('metodo_pago');

        $validator
            ->decimal('base_doctor')
            ->requirePresence('base_doctor', 'create')
            ->notEmptyString('base_doctor');

        $validator
            ->decimal('monto_pagado')
            ->requirePresence('monto_pagado', 'create')
            ->notEmptyString('monto_pagado');

        $validator
            ->scalar('conceptos')
            ->allowEmptyString('conceptos');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['pago_historial_id'], 'PagosDoctoresHistorial'), ['errorField' => 'pago_historial_id']);
        $rules->add($rules->existsIn(['caja_movimiento_id'], 'CajaMovimientos'), ['errorField' => 'caja_movimiento_id']);
        $rules->add($rules->existsIn(['invoice_id'], 'Invoices'), ['errorField' => 'invoice_id']);
        $rules->add(
            $rules->isUnique(['caja_movimiento_id']),
            ['errorField' => 'caja_movimiento_id', 'message' => 'Este método de pago de la factura ya fue pagado al doctor anteriormente.']
        );

        return $rules;
    }
}
