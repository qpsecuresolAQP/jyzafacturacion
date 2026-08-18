<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class InvoiceCuotasTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('invoice_cuotas');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Invoices', [
            'foreignKey' => 'invoice_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('invoice_id')
            ->requirePresence('invoice_id', 'create')
            ->notEmptyString('invoice_id');

        $validator
            ->integer('numero_cuota')
            ->requirePresence('numero_cuota', 'create')
            ->notEmptyString('numero_cuota')
            ->greaterThan('numero_cuota', 0, 'El número de cuota debe ser mayor a 0.');

        $validator
            ->decimal('monto')
            ->requirePresence('monto', 'create')
            ->notEmptyString('monto')
            ->greaterThan('monto', 0, 'El monto de la cuota debe ser mayor a 0.');

        $validator
            ->date('fecha_vencimiento')
            ->requirePresence('fecha_vencimiento', 'create')
            ->notEmptyDate('fecha_vencimiento');

        $validator
            ->scalar('estado')
            ->maxLength('estado', 20)
            ->allowEmptyString('estado');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['invoice_id'], 'Invoices'), ['errorField' => 'invoice_id']);

        return $rules;
    }
}