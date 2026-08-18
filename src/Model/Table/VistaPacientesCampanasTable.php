<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * VistaPacientesCampanas Model
 *
 * @property \App\Model\Table\PacientesTable&\Cake\ORM\Association\BelongsTo $Pacientes
 *
 * @method \App\Model\Entity\VistaPacientesCampana newEmptyEntity()
 * @method \App\Model\Entity\VistaPacientesCampana newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\VistaPacientesCampana> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\VistaPacientesCampana get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\VistaPacientesCampana findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\VistaPacientesCampana patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\VistaPacientesCampana> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\VistaPacientesCampana|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\VistaPacientesCampana saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\VistaPacientesCampana>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaPacientesCampana>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VistaPacientesCampana>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaPacientesCampana> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VistaPacientesCampana>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaPacientesCampana>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VistaPacientesCampana>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaPacientesCampana> deleteManyOrFail(iterable $entities, array $options = [])
 */
class VistaPacientesCampanasTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('vista_pacientes_campanas');
        $this->setDisplayField('nombre');

        $this->belongsTo('Pacientes', [
            'foreignKey' => 'paciente_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('paciente_id')
            ->notEmptyString('paciente_id');

        $validator
            ->scalar('nombre')
            ->maxLength('nombre', 255)
            ->requirePresence('nombre', 'create')
            ->notEmptyString('nombre');

        $validator
            ->scalar('apellido')
            ->maxLength('apellido', 255)
            ->requirePresence('apellido', 'create')
            ->notEmptyString('apellido');

        $validator
            ->scalar('dni')
            ->maxLength('dni', 20)
            ->allowEmptyString('dni');

        $validator
            ->integer('campana_id')
            ->notEmptyString('campana_id');

        $validator
            ->scalar('nombre_campana')
            ->maxLength('nombre_campana', 255)
            ->allowEmptyString('nombre_campana');

        $validator
            ->dateTime('fecha_consulta')
            ->notEmptyDateTime('fecha_consulta');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['paciente_id'], 'Pacientes'), ['errorField' => 'paciente_id']);

        return $rules;
    }
}
