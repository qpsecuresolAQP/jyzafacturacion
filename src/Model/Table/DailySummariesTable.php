<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class DailySummariesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('daily_summaries');
        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('Invoices', [
            'foreignKey' => 'daily_summary_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('company_id')
            ->requirePresence('company_id', 'create')
            ->notEmptyString('company_id');

        $validator
            ->date('fecha')
            ->requirePresence('fecha', 'create')
            ->notEmptyDate('fecha');

        $validator
            ->integer('correlativo')
            ->requirePresence('correlativo', 'create')
            ->notEmptyString('correlativo');

        $validator
            ->scalar('nombre')
            ->maxLength('nombre', 100)
            ->requirePresence('nombre', 'create')
            ->notEmptyString('nombre');

        $validator
            ->scalar('ticket')
            ->maxLength('ticket', 100)
            ->allowEmptyString('ticket');

        $validator
            ->scalar('estado')
            ->maxLength('estado', 30)
            ->allowEmptyString('estado');

        $validator
            ->scalar('codigo_sunat')
            ->maxLength('codigo_sunat', 20)
            ->allowEmptyString('codigo_sunat');

        $validator->allowEmptyString('descripcion_sunat');

        $validator
            ->scalar('xml_path')
            ->maxLength('xml_path', 255)
            ->allowEmptyString('xml_path');

        $validator
            ->scalar('cdr_path')
            ->maxLength('cdr_path', 255)
            ->allowEmptyString('cdr_path');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['company_id'], 'Companies'), ['errorField' => 'company_id']);
        return $rules;
    }
}