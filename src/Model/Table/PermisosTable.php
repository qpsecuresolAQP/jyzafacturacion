<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Permisos Model
 *
 * @property \App\Model\Table\UsuariosPermisosTable&\Cake\ORM\Association\HasMany $UsuariosPermisos
 * @property \App\Model\Table\RolesTable&\Cake\ORM\Association\BelongsToMany $Roles
 *
 * @method \App\Model\Entity\Permiso newEmptyEntity()
 * @method \App\Model\Entity\Permiso newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Permiso> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Permiso get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Permiso findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Permiso patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Permiso> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Permiso|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Permiso saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Permiso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Permiso>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Permiso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Permiso> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Permiso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Permiso>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Permiso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Permiso> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PermisosTable extends Table
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

        $this->setTable('permisos');
        $this->setDisplayField('controller');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('UsuariosPermisos', [
            'foreignKey' => 'permiso_id',
        ]);
        $this->belongsToMany('Roles', [
            'foreignKey' => 'permiso_id',
            'targetForeignKey' => 'role_id',
            'joinTable' => 'roles_permisos',
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
            ->scalar('controller')
            ->maxLength('controller', 60)
            ->requirePresence('controller', 'create')
            ->notEmptyString('controller');

        $validator
            ->scalar('action')
            ->maxLength('action', 60)
            ->requirePresence('action', 'create')
            ->notEmptyString('action');

        $validator
            ->scalar('descripcion')
            ->maxLength('descripcion', 150)
            ->allowEmptyString('descripcion');

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
        $rules->add($rules->isUnique(['controller', 'action']), ['errorField' => 'controller']);

        return $rules;
    }
}
