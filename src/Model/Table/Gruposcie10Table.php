<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Gruposcie10 Model
 *
 * @method \App\Model\Entity\Gruposcie10 newEmptyEntity()
 * @method \App\Model\Entity\Gruposcie10 newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Gruposcie10> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Gruposcie10 get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Gruposcie10 findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Gruposcie10 patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Gruposcie10> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Gruposcie10|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Gruposcie10 saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Gruposcie10>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Gruposcie10>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Gruposcie10>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Gruposcie10> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Gruposcie10>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Gruposcie10>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Gruposcie10>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Gruposcie10> deleteManyOrFail(iterable $entities, array $options = [])
 */
class Gruposcie10Table extends Table
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

        $this->setTable('gruposcie10');
        $this->setDisplayField('clave');
        $this->setPrimaryKey('id');
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
            ->scalar('clave')
            ->maxLength('clave', 6)
            ->requirePresence('clave', 'create')
            ->notEmptyString('clave');

        $validator
            ->scalar('descripcion')
            ->maxLength('descripcion', 122)
            ->requirePresence('descripcion', 'create')
            ->notEmptyString('descripcion');

        return $validator;
    }
}
