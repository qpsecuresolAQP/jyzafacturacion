<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UsuariosPermisos Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Usuarios
 * @property \App\Model\Table\PermisosTable&\Cake\ORM\Association\BelongsTo $Permisos
 *
 * @method \App\Model\Entity\UsuariosPermiso newEmptyEntity()
 * @method \App\Model\Entity\UsuariosPermiso newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\UsuariosPermiso> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosPermiso get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\UsuariosPermiso findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\UsuariosPermiso patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\UsuariosPermiso> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosPermiso|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\UsuariosPermiso saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\UsuariosPermiso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UsuariosPermiso>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\UsuariosPermiso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UsuariosPermiso> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\UsuariosPermiso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UsuariosPermiso>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\UsuariosPermiso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\UsuariosPermiso> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsuariosPermisosTable extends Table
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

        $this->setTable('usuarios_permisos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuario_id',
            'className' => 'Users',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Permisos', [
            'foreignKey' => 'permiso_id',
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
            ->integer('usuario_id')
            ->notEmptyString('usuario_id');

        $validator
            ->integer('permiso_id')
            ->notEmptyString('permiso_id');

        $validator
            ->boolean('allow')
            ->notEmptyString('allow');

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
        $rules->add($rules->isUnique(['usuario_id', 'permiso_id']), ['errorField' => 'usuario_id']);
        $rules->add($rules->existsIn(['usuario_id'], 'Usuarios'), ['errorField' => 'usuario_id']);
        $rules->add($rules->existsIn(['permiso_id'], 'Permisos'), ['errorField' => 'permiso_id']);

        return $rules;
    }
}
