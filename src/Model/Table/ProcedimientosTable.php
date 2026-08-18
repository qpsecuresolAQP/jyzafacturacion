<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Procedimientos Model
 *
 * @property \App\Model\Table\DoctoresTable&\Cake\ORM\Association\BelongsTo $Doctors
 *
 * @method \App\Model\Entity\Procedimiento newEmptyEntity()
 * @method \App\Model\Entity\Procedimiento newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Procedimiento> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Procedimiento get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Procedimiento findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Procedimiento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Procedimiento> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Procedimiento|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Procedimiento saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Procedimiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Procedimiento>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Procedimiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Procedimiento> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Procedimiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Procedimiento>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Procedimiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Procedimiento> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ProcedimientosTable extends Table
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

        $this->setTable('procedimientos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('DocumentosProcedimientos', [
            'foreignKey' => 'procedimiento_id',
            'dependent' => true,
            'cascadeCallbacks' => true
        ]);

        $this->belongsTo('Doctores', [
            'foreignKey' => 'doctor_id',
            'className' => 'Doctores',
        ]);
        $this->belongsTo('HistoriasClinicas', [
            'foreignKey' => 'historia_id',
            'className' => 'HistoriasClinicas',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
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
            ->scalar('procedimiento')
            ->allowEmptyString('procedimiento');

        $validator
            ->integer('doctor_id')
            ->allowEmptyString('doctor_id');

        $validator
            ->integer('historia_id')
            ->allowEmptyString('historia_id');

        $validator
            ->integer('user_id')
            ->allowEmptyString('user_id');
            
        $validator
            ->integer('documento_id')
            ->allowEmptyString('documento_id');

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
        $rules->add($rules->existsIn(['historia_id'], 'HistoriasClinicas'), ['errorField' => 'historia_id']);
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
