<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ClientesFacturacionTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('clientes_facturacion');
        $this->setDisplayField('nombre_razon_social');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('Invoices', [
            'foreignKey' => 'cliente_facturacion_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('company_id')
            ->requirePresence('company_id', 'create')
            ->notEmptyString('company_id');

        $validator
            ->scalar('tipo_doc')
            ->maxLength('tipo_doc', 2)
            ->requirePresence('tipo_doc', 'create')
            ->notEmptyString('tipo_doc')
            ->add('tipo_doc', 'inList', [
                'rule' => ['inList', ['1', '6']],
                'message' => 'El tipo de documento debe ser 1 (DNI) o 6 (RUC).',
            ]);

        $validator
            ->scalar('numero_doc')
            ->maxLength('numero_doc', 20)
            ->requirePresence('numero_doc', 'create')
            ->notEmptyString('numero_doc');

        $validator
            ->scalar('nombre_razon_social')
            ->maxLength('nombre_razon_social', 255)
            ->requirePresence('nombre_razon_social', 'create')
            ->notEmptyString('nombre_razon_social');

        $validator
            ->scalar('direccion')
            ->maxLength('direccion', 255)
            ->allowEmptyString('direccion');

        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->scalar('telefono')
            ->maxLength('telefono', 30)
            ->allowEmptyString('telefono');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['company_id'], 'Companies'), ['errorField' => 'company_id']);

        $rules->add($rules->isUnique(
            ['company_id', 'tipo_doc', 'numero_doc'],
            ['errorField' => 'numero_doc']
        ));

        return $rules;
    }
}