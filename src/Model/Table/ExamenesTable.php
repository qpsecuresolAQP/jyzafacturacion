<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ExamenesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('examenes');
        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('CategoriasExamenes', [
            'foreignKey' => 'categoria_examen_id',
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
            ->integer('categoria_examen_id')
            ->requirePresence('categoria_examen_id', 'create')
            ->notEmptyString('categoria_examen_id');

        $validator
            ->scalar('nombre')
            ->maxLength('nombre', 200)
            ->requirePresence('nombre', 'create')
            ->notEmptyString('nombre');

        $validator
            ->scalar('muestra')
            ->maxLength('muestra', 150)
            ->allowEmptyString('muestra');

        $validator
            ->decimal('precio_convenio')
            ->requirePresence('precio_convenio', 'create')
            ->notEmptyString('precio_convenio')
            ->greaterThanOrEqual('precio_convenio', 0, 'El precio de convenio no puede ser negativo.');

        $validator
            ->decimal('precio')
            ->requirePresence('precio', 'create')
            ->notEmptyString('precio')
            ->greaterThanOrEqual('precio', 0, 'El precio al paciente no puede ser negativo.');

        $validator
            ->decimal('comision_medico')
            ->allowEmptyString('comision_medico')
            ->greaterThanOrEqual('comision_medico', 0, 'La comisión del médico no puede ser negativa.');

        $validator
            ->integer('laboratorio_id')
            ->allowEmptyString('laboratorio_id');

        $validator
            ->boolean('estado')
            ->notEmptyString('estado');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['categoria_examen_id'], 'CategoriasExamenes'), ['errorField' => 'categoria_examen_id']);
        $rules->add($rules->existsIn(['laboratorio_id'], 'Laboratorios'), ['errorField' => 'laboratorio_id']);

        return $rules;
    }
}
