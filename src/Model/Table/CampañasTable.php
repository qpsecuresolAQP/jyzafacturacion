<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Campañas Model
 *
 * @method \App\Model\Entity\Campaña newEmptyEntity()
 * @method \App\Model\Entity\Campaña newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Campaña> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Campaña get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Campaña findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Campaña patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Campaña> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Campaña|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Campaña saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Campaña>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Campaña>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Campaña>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Campaña> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Campaña>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Campaña>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Campaña>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Campaña> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CampañasTable extends Table
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

        $this->setTable('campañas');
        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasOne('HistoriasClinicas', [
            'foreignKey' => 'departamento_id',
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
            ->maxLength('nombre', 255)
            ->allowEmptyString('nombre');

        $validator
            ->scalar('descripcion')
            ->maxLength('descripcion', 300)
            ->allowEmptyString('descripcion');

        return $validator;
    }
}
