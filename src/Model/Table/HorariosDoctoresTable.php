<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * HorariosDoctores Model
 *
 * @property \App\Model\Table\DoctoresTable&\Cake\ORM\Association\BelongsTo $Doctors
 *
 * @method \App\Model\Entity\HorariosDoctore newEmptyEntity()
 * @method \App\Model\Entity\HorariosDoctore newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\HorariosDoctore> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\HorariosDoctore get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\HorariosDoctore findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\HorariosDoctore patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\HorariosDoctore> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\HorariosDoctore|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\HorariosDoctore saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\HorariosDoctore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\HorariosDoctore>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\HorariosDoctore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\HorariosDoctore> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\HorariosDoctore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\HorariosDoctore>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\HorariosDoctore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\HorariosDoctore> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class HorariosDoctoresTable extends Table
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

        $this->setTable('horarios_doctores');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        // $this->belongsTo('Doctors', [
        //     'foreignKey' => 'doctor_id',
        //     'className' => 'Doctores',
        //     'joinType' => 'INNER',
        // ]);
        // Relación con Doctores
        $this->belongsTo('Doctores', [
            'foreignKey' => 'doctor_id',
            'joinType' => 'INNER', // Se asegura de que siempre haya un doctor asociado
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
            ->integer('doctor_id')
            ->notEmptyString('doctor_id');

        $validator
            ->requirePresence('dia_semana', 'create')
            ->notEmptyString('dia_semana');

        $validator
            ->time('hora_inicio')
            ->requirePresence('hora_inicio', 'create')
            ->notEmptyTime('hora_inicio');

        $validator
            ->time('hora_fin')
            ->requirePresence('hora_fin', 'create')
            ->notEmptyTime('hora_fin');

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
        $rules->add($rules->existsIn(['doctor_id'], 'Doctores'), ['errorField' => 'doctor_id']);

        return $rules;
    }
}
