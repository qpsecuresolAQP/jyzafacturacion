<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CitasTratamientos Model
 *
 * @property \App\Model\Table\TratamientosTable&\Cake\ORM\Association\BelongsTo $Tratamientos
 * @property \App\Model\Table\CitasTable&\Cake\ORM\Association\BelongsTo $Citas
 *
 * @method \App\Model\Entity\CitasTratamiento newEmptyEntity()
 * @method \App\Model\Entity\CitasTratamiento newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\CitasTratamiento> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CitasTratamiento get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\CitasTratamiento findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\CitasTratamiento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\CitasTratamiento> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CitasTratamiento|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\CitasTratamiento saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\CitasTratamiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CitasTratamiento>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CitasTratamiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CitasTratamiento> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CitasTratamiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CitasTratamiento>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CitasTratamiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CitasTratamiento> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CitasTratamientosTable extends Table
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

        $this->setTable('citas_tratamientos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Tratamientos', [
            'foreignKey' => 'tratamiento_id',
        ]);
        $this->belongsTo('Citas', [
            'foreignKey' => 'cita_id',
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
            ->integer('tratamiento_id')
            ->allowEmptyString('tratamiento_id');

        $validator
            ->integer('cita_id')
            ->allowEmptyString('cita_id');

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
        $rules->add($rules->existsIn(['tratamiento_id'], 'Tratamientos'), ['errorField' => 'tratamiento_id']);
        $rules->add($rules->existsIn(['cita_id'], 'Citas'), ['errorField' => 'cita_id']);

        return $rules;
    }
}
