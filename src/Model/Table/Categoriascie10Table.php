<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Categoriascie10 Model
 *
 * @method \App\Model\Entity\Categoriascie10 newEmptyEntity()
 * @method \App\Model\Entity\Categoriascie10 newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Categoriascie10> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Categoriascie10 get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Categoriascie10 findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Categoriascie10 patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Categoriascie10> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Categoriascie10|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Categoriascie10 saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Categoriascie10>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Categoriascie10>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Categoriascie10>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Categoriascie10> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Categoriascie10>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Categoriascie10>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Categoriascie10>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Categoriascie10> deleteManyOrFail(iterable $entities, array $options = [])
 */
class Categoriascie10Table extends Table
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

        $this->setTable('categoriascie10');
        $this->setDisplayField('clave');
        $this->setPrimaryKey('id');
        
        $this->hasMany('ConsultasCie', [
            'foreignKey' => 'cie_id',
        ]);
        $this->hasMany('GinecologicaCie', [
            'foreignKey' => 'cie_id',
        ]);
        $this->hasMany('CardiovascularCie', [
            'foreignKey' => 'categoria_id',
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
            ->scalar('clave')
            ->maxLength('clave', 10)
            ->requirePresence('clave', 'create')
            ->notEmptyString('clave');

        $validator
            ->scalar('descripcion')
            ->maxLength('descripcion', 256)
            ->requirePresence('descripcion', 'create')
            ->notEmptyString('descripcion');

        $validator
            ->integer('idSubGrupo')
            ->requirePresence('idSubGrupo', 'create')
            ->notEmptyString('idSubGrupo');

        return $validator;
    }
}
