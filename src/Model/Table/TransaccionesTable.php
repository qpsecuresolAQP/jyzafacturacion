<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Transacciones Model
 *
 * @property \App\Model\Table\ProductosTable&\Cake\ORM\Association\BelongsTo $Productos
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Transaccione newEmptyEntity()
 * @method \App\Model\Entity\Transaccione newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Transaccione> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Transaccione get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Transaccione findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Transaccione patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Transaccione> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Transaccione|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Transaccione saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Transaccione>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Transaccione>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Transaccione>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Transaccione> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Transaccione>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Transaccione>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Transaccione>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Transaccione> deleteManyOrFail(iterable $entities, array $options = [])
 */
class TransaccionesTable extends Table
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

        $this->setTable('transacciones');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Productos', [
            'foreignKey' => 'producto_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
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
            ->integer('cantidad')
            ->requirePresence('cantidad', 'create')
            ->notEmptyString('cantidad');

        $validator
            ->dateTime('fecha_transaccion')
            ->notEmptyDateTime('fecha_transaccion');

        $validator
            ->scalar('notas')
            ->allowEmptyString('notas');

        $validator
            ->integer('producto_id')
            ->notEmptyString('producto_id');

        $validator
            ->integer('referencia_id')
            ->allowEmptyString('referencia_id');

        $validator
            ->scalar('tipo_transaccion')
            ->requirePresence('tipo_transaccion', 'create')
            ->notEmptyString('tipo_transaccion');

        $validator
            ->integer('user_id')
            ->notEmptyString('user_id');

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
        $rules->add($rules->existsIn(['producto_id'], 'Productos'), ['errorField' => 'producto_id']);
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
