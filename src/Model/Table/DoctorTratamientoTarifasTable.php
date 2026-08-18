<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class DoctorTratamientoTarifasTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('doctor_tratamiento_tarifas');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Doctores', [
            'foreignKey' => 'doctor_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Tratamientos', [
            'foreignKey' => 'tratamiento_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('doctor_id')
            ->requirePresence('doctor_id', 'create')
            ->notEmptyString('doctor_id');

        $validator
            ->integer('tratamiento_id')
            ->requirePresence('tratamiento_id', 'create')
            ->notEmptyString('tratamiento_id');

        $validator
            ->decimal('monto_fijo')
            ->requirePresence('monto_fijo', 'create')
            ->notEmptyString('monto_fijo')
            ->greaterThanOrEqual('monto_fijo', 0, 'El monto fijo no puede ser negativo.');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['doctor_id'], 'Doctores'), ['errorField' => 'doctor_id']);
        $rules->add($rules->existsIn(['tratamiento_id'], 'Tratamientos'), ['errorField' => 'tratamiento_id']);
        $rules->add(
            $rules->isUnique(['doctor_id', 'tratamiento_id']),
            ['errorField' => 'tratamiento_id', 'message' => 'Ya existe una tarifa fija para este doctor y tratamiento.']
        );

        return $rules;
    }
}
