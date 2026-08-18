<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class InvoiceDistribucionesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('invoice_distribuciones');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Invoices', [
            'foreignKey' => 'invoice_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Laboratorios', [
            'foreignKey' => 'laboratorio_id',
            'joinType' => 'LEFT',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('invoice_id')
            ->requirePresence('invoice_id', 'create')
            ->notEmptyString('invoice_id');

        $validator
            ->scalar('tipo')
            ->requirePresence('tipo', 'create')
            ->notEmptyString('tipo')
            ->add('tipo', 'inList', [
                'rule' => ['inList', ['LABORATORIO', 'MATERIALES']],
                'message' => 'Tipo de distribución inválido.',
            ]);

        $validator
            ->integer('laboratorio_id')
            ->allowEmptyString('laboratorio_id');

        $validator
            ->decimal('monto')
            ->requirePresence('monto', 'create')
            ->notEmptyString('monto')
            ->greaterThan('monto', 0, 'El monto debe ser mayor a 0.');

        $validator
            ->scalar('descripcion')
            ->allowEmptyString('descripcion');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['invoice_id'], 'Invoices'), ['errorField' => 'invoice_id']);
        $rules->add($rules->existsIn(['laboratorio_id'], 'Laboratorios'), ['errorField' => 'laboratorio_id']);

        return $rules;
    }
}
