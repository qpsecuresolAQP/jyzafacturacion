<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CompaniesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('companies');
        $this->setDisplayField('razon_social');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Invoices', [
            'foreignKey' => 'company_id',
        ]);

        $this->hasMany('DailySummaries', [
            'foreignKey' => 'company_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('ruc')
            ->maxLength('ruc', 11)
            ->requirePresence('ruc', 'create')
            ->notEmptyString('ruc')
            ->lengthBetween('ruc', [11, 11]);

        $validator
            ->scalar('razon_social')
            ->maxLength('razon_social', 255)
            ->requirePresence('razon_social', 'create')
            ->notEmptyString('razon_social');

        $validator
            ->scalar('nombre_comercial')
            ->maxLength('nombre_comercial', 255)
            ->allowEmptyString('nombre_comercial');

        $validator
            ->scalar('direccion')
            ->maxLength('direccion', 255)
            ->requirePresence('direccion', 'create')
            ->notEmptyString('direccion');

        $validator
            ->scalar('ubigeo')
            ->maxLength('ubigeo', 6)
            ->requirePresence('ubigeo', 'create')
            ->notEmptyString('ubigeo');

        $validator
            ->scalar('departamento')
            ->maxLength('departamento', 100)
            ->requirePresence('departamento', 'create')
            ->notEmptyString('departamento');

        $validator
            ->scalar('provincia')
            ->maxLength('provincia', 100)
            ->requirePresence('provincia', 'create')
            ->notEmptyString('provincia');

        $validator
            ->scalar('distrito')
            ->maxLength('distrito', 100)
            ->requirePresence('distrito', 'create')
            ->notEmptyString('distrito');

        $validator
            ->scalar('urbanizacion')
            ->maxLength('urbanizacion', 100)
            ->allowEmptyString('urbanizacion');

        $validator
            ->scalar('cod_local')
            ->maxLength('cod_local', 4)
            ->requirePresence('cod_local', 'create')
            ->notEmptyString('cod_local');

        $validator
            ->scalar('sol_user')
            ->maxLength('sol_user', 100)
            ->requirePresence('sol_user', 'create')
            ->notEmptyString('sol_user');

        $validator
            ->scalar('sol_pass')
            ->maxLength('sol_pass', 100)
            ->requirePresence('sol_pass', 'create')
            ->notEmptyString('sol_pass');

        $validator
            ->scalar('certificado_sunat')
            ->maxLength('certificado_sunat', 255)
            ->requirePresence('certificado_sunat', 'create')
            ->notEmptyString('certificado_sunat');

        $validator
            ->scalar('ambiente')
            ->maxLength('ambiente', 20)
            ->requirePresence('ambiente', 'create')
            ->notEmptyString('ambiente');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['ruc']), ['errorField' => 'ruc']);
        return $rules;
    }
}