<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FormasFarmaceuticas Model
 *
 * @property \App\Model\Table\RecetasMedicamentosTable&\Cake\ORM\Association\HasMany $RecetasMedicamentos
 *
 * @method \App\Model\Entity\FormaFarmaceutica newEmptyEntity()
 * @method \App\Model\Entity\FormaFarmaceutica newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\FormaFarmaceutica> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FormaFarmaceutica get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\FormaFarmaceutica findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\FormaFarmaceutica patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\FormaFarmaceutica> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\FormaFarmaceutica|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\FormaFarmaceutica saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\FormaFarmaceutica>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FormaFarmaceutica>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FormaFarmaceutica>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FormaFarmaceutica> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FormaFarmaceutica>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FormaFarmaceutica>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FormaFarmaceutica>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FormaFarmaceutica> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FormasFarmaceuticasTable extends Table
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

        $this->setTable('formas_farmaceuticas');
        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('RecetasMedicamentos', [
            'foreignKey' => 'forma_farmaceutica_id',
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
            ->scalar('nombre')
            ->maxLength('nombre', 100)
            ->requirePresence('nombre', 'create')
            ->notEmptyString('nombre')
            ->add('nombre', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('descripcion')
            ->allowEmptyString('descripcion');

        $validator
            ->boolean('activa')
            ->allowEmptyString('activa');

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
        $rules->add($rules->isUnique(['nombre']), ['errorField' => 'nombre']);

        return $rules;
    }
}
