<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CajasTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('cajas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('CajaMovimientos', [
            'foreignKey' => 'caja_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('nombre')
            ->maxLength('nombre', 100)
            ->requirePresence('nombre', 'create')
            ->notEmptyString('nombre');

        $validator
            ->scalar('codigo')
            ->maxLength('codigo', 50)
            ->requirePresence('codigo', 'create')
            ->notEmptyString('codigo');

        $validator
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id');

        $validator
            ->date('fecha')
            ->requirePresence('fecha', 'create')
            ->notEmptyDate('fecha');

        $validator
            ->decimal('monto_inicial')
            ->requirePresence('monto_inicial', 'create')
            ->notEmptyString('monto_inicial')
            ->greaterThanOrEqual('monto_inicial', 0, 'El monto inicial no puede ser negativo.');

        $validator
            ->decimal('monto_cierre')
            ->allowEmptyString('monto_cierre');

        $validator
            ->scalar('estado')
            ->maxLength('estado', 20)
            ->requirePresence('estado', 'create')
            ->notEmptyString('estado')
            ->add('estado', 'inList', [
                'rule' => ['inList', ['ABIERTA', 'PAUSADA', 'CERRADA']],
                'message' => 'Estado inválido.',
            ]);

        $validator
            ->scalar('observacion')
            ->maxLength('observacion', 255)
            ->allowEmptyString('observacion');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'), [
            'errorField' => 'user_id',
            'message' => 'El usuario no existe.',
        ]);

        return $rules;
    }
}