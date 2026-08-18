<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PagosDoctoresHistorialTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('pagos_doctores_historial');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Doctores', [
            'foreignKey' => 'doctor_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'LEFT',
        ]);

        $this->hasMany('PagosDoctoresHistorialMovimientos', [
            'foreignKey' => 'pago_historial_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('doctor_id')
            ->requirePresence('doctor_id', 'create')
            ->notEmptyString('doctor_id');

        $validator
            ->date('fecha_desde')
            ->requirePresence('fecha_desde', 'create')
            ->notEmptyDate('fecha_desde');

        $validator
            ->date('fecha_hasta')
            ->requirePresence('fecha_hasta', 'create')
            ->notEmptyDate('fecha_hasta');

        $validator
            ->decimal('monto_total')
            ->requirePresence('monto_total', 'create')
            ->notEmptyString('monto_total')
            ->greaterThan('monto_total', 0, 'El monto a pagar debe ser mayor a 0.');

        $validator
            ->integer('total_comprobantes')
            ->requirePresence('total_comprobantes', 'create')
            ->notEmptyString('total_comprobantes');

        $validator
            ->scalar('observaciones')
            ->allowEmptyString('observaciones');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['doctor_id'], 'Doctores'), ['errorField' => 'doctor_id']);
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
