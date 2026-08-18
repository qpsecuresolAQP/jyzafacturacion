<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\EventInterface;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;

/**
 * RolesPermisos Model
 *
 * @property \App\Model\Table\RolesTable&\Cake\ORM\Association\BelongsTo $Rols
 * @property \App\Model\Table\PermisosTable&\Cake\ORM\Association\BelongsTo $Permisos
 *
 * @method \App\Model\Entity\RolesPermiso newEmptyEntity()
 * @method \App\Model\Entity\RolesPermiso newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\RolesPermiso> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RolesPermiso get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\RolesPermiso findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\RolesPermiso patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\RolesPermiso> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\RolesPermiso|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\RolesPermiso saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\RolesPermiso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\RolesPermiso>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\RolesPermiso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\RolesPermiso> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\RolesPermiso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\RolesPermiso>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\RolesPermiso>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\RolesPermiso> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RolesPermisosTable extends Table
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

        $this->setTable('roles_permisos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Rols', [
            'foreignKey' => 'rol_id',
            'className' => 'Roles',
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
            ->integer('rol_id')
            ->notEmptyString('rol_id');

        $validator
            ->integer('permiso_id')
            ->notEmptyString('permiso_id');

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
        $rules->add($rules->isUnique(['rol_id', 'permiso_id']), ['errorField' => 'rol_id']);
        $rules->add($rules->existsIn(['rol_id'], 'Rols'), ['errorField' => 'rol_id']);
        $rules->add($rules->existsIn(['permiso_id'], 'Permisos'), ['errorField' => 'permiso_id']);

        return $rules;
    }

    /**
     * afterSave - Sincroniza cambios en permisos del rol a todos los usuarios con ese rol
     * Cuando se agrega un permiso a un rol, se agrega automáticamente a todos los usuarios de ese rol
     */
    public function afterSave(EventInterface $event, EntityInterface $entity): void
    {
        // Solo procesar si es rol Doctor (rol_id = 2)
        if ($entity->rol_id === 2) {
            $usuariosPermisosTable = TableRegistry::getTableLocator()->get('UsuariosPermisos');
            $usuariosTable = TableRegistry::getTableLocator()->get('Users');

            // Obtener todos los doctores (usuarios con rol_id = 2)
            $doctores = $usuariosTable->find()
                ->select(['Users.id'])
                ->where(['Users.rol_id' => 2])
                ->toArray();

            // Para cada doctor, crear/actualizar su registro en usuarios_permisos
            foreach ($doctores as $doctor) {
                $usuarioPermiso = $usuariosPermisosTable->find()
                    ->where([
                        'usuario_id' => $doctor->id,
                        'permiso_id' => $entity->permiso_id,
                    ])
                    ->first();

                if (!$usuarioPermiso) {
                    // Si no existe, crearlo
                    $usuarioPermiso = $usuariosPermisosTable->newEntity([
                        'usuario_id' => $doctor->id,
                        'permiso_id' => $entity->permiso_id,
                        'allow' => true,
                    ]);
                    $usuariosPermisosTable->save($usuarioPermiso);
                } else {
                    // Si existe pero está denegado, actualizarlo a permitido
                    if (!$usuarioPermiso->allow) {
                        $usuarioPermiso->allow = true;
                        $usuariosPermisosTable->save($usuarioPermiso);
                    }
                }
            }
        }
    }

    /**
     * afterDelete - Sincroniza eliminación de permisos del rol a todos los usuarios con ese rol
     * Cuando se elimina un permiso de un rol, se elimina automáticamente de todos los usuarios de ese rol
     */
    public function afterDelete(EventInterface $event, EntityInterface $entity): void
    {
        // Solo procesar si es rol Doctor (rol_id = 2)
        if ($entity->rol_id === 2) {
            $usuariosPermisosTable = TableRegistry::getTableLocator()->get('UsuariosPermisos');
            $usuariosTable = TableRegistry::getTableLocator()->get('Users');

            // Obtener todos los doctores (usuarios con rol_id = 2)
            $doctores = $usuariosTable->find()
                ->select(['Users.id'])
                ->where(['Users.rol_id' => 2])
                ->toArray();

            // Eliminar el permiso de todos los doctores que lo tenían por el rol
            // SOLO si no hay un override específico del usuario
            foreach ($doctores as $doctor) {
                $usuariosPermisosTable->deleteAll([
                    'usuario_id' => $doctor->id,
                    'permiso_id' => $entity->permiso_id,
                    'allow' => true, // Solo eliminar si estaba permitido (es decir, por el rol)
                ]);
            }
        }
    }
}
