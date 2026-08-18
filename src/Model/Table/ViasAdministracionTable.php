<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ViasAdministracion Model
 *
 * @property \App\Model\Table\RecetasMedicamentosTable&\Cake\ORM\Association\HasMany $RecetasMedicamentos
 *
 * @method \App\Model\Entity\ViaAdministracion newEmptyEntity()
 * @method \App\Model\Entity\ViaAdministracion newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\ViaAdministracion> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ViaAdministracion get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\ViaAdministracion findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\ViaAdministracion patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\ViaAdministracion> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ViaAdministracion|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\ViaAdministracion saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\ViaAdministracion>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ViaAdministracion>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ViaAdministracion>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ViaAdministracion> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ViaAdministracion>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ViaAdministracion>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ViaAdministracion>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ViaAdministracion> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ViasAdministracionTable extends Table
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

        $this->setTable('vias_administracion');
        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('RecetasMedicamentos', [
            'foreignKey' => 'via_administracion_id',
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
