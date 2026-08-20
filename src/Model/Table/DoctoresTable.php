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
use ArrayObject;

/**
 * Doctores Model
 *
 * @method \App\Model\Entity\Doctore newEmptyEntity()
 * @method \App\Model\Entity\Doctore newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Doctore> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Doctore get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Doctore findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Doctore patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Doctore> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Doctore|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Doctore saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Doctore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Doctore>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Doctore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Doctore> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Doctore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Doctore>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Doctore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Doctore> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DoctoresTable extends Table
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

        $this->setTable('doctores');
        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->belongsTo('Users', [
            'foreignKey' => 'id',
            'bindingKey' => 'doctor_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('DoctorTratamientoTarifas', [
            'foreignKey' => 'doctor_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
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
            ->requirePresence('nombre', 'create')
            ->notEmptyString('nombre');

        $validator
            ->scalar('apellido')
            ->maxLength('apellido', 255)
            ->requirePresence('apellido', 'create')
            ->notEmptyString('apellido');

        $validator
            ->scalar('especialidad')
            ->maxLength('especialidad', 200)
            ->requirePresence('especialidad', 'create')
            ->notEmptyString('especialidad');

        $validator
            ->scalar('telefono')
            ->maxLength('telefono', 15)
            ->requirePresence('telefono', 'create')
            ->notEmptyString('telefono');
        
        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->decimal('porcentaje_pago')
            ->requirePresence('porcentaje_pago', 'create')
            ->notEmptyString('porcentaje_pago')
            ->greaterThanOrEqual('porcentaje_pago', 0, 'El porcentaje no puede ser menor que 0.')
            ->lessThanOrEqual('porcentaje_pago', 100, 'El porcentaje no puede ser mayor que 100.');

        $validator
            ->scalar('modo_pago')
            ->requirePresence('modo_pago', 'create')
            ->notEmptyString('modo_pago')
            ->add('modo_pago', 'inList', [
                'rule' => ['inList', ['PORCENTAJE', 'FIJO']],
                'message' => 'Modo de pago inválido.',
            ]);

        return $validator;
    }
    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($entity->isNew()) {
            $usuariosTable = TableRegistry::getTableLocator()->get('Users');
            $usuariosPermisosTable = TableRegistry::getTableLocator()->get('UsuariosPermisos');
            $rolesPermisosTable = TableRegistry::getTableLocator()->get('RolesPermisos');

            // Extraer inicial del nombre
            $nombreParts = explode(' ', trim($entity->nombre));
            $inicialNombre = strtolower(substr($nombreParts[0], 0, 1)); // Primera letra del primer nombre

            // Extraer primer apellido completo
            $apellidoParts = explode(' ', trim($entity->apellido));
            $primerApellido = strtolower($apellidoParts[0]); // Primer apellido completo

            // Generar username
            $username = $inicialNombre . $primerApellido;

            $usuario = $usuariosTable->newEntity([
                'username' => $username,
                'password' => 'Doc_' . $entity->telefono,
                'rol_id' => 2, // Rol de doctor
                'doctor_id' => $entity->id, // Relación con el doctor recién creado
            ]);

            if ($usuariosTable->save($usuario)) {
                // Obtener todos los permisos del rol Doctor (rol_id = 2)
                $permisosDelRol = $rolesPermisosTable->find()
                    ->select(['permiso_id'])
                    ->where(['rol_id' => 2])
                    ->toArray();

                // Crear registros en usuarios_permisos para este usuario
                // Esto permite que el usuario herede todos los permisos del rol
                // y luego se puedan denegar permisos específicos si es necesario
                foreach ($permisosDelRol as $rolePermiso) {
                    $usuarioPermiso = $usuariosPermisosTable->newEntity([
                        'usuario_id' => $usuario->id,
                        'permiso_id' => $rolePermiso->permiso_id,
                        'allow' => true, // El usuario tiene este permiso por el rol
                    ]);
                    $usuariosPermisosTable->save($usuarioPermiso);
                }
            }
        }
    }
}
