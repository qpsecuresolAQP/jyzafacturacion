<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Tratamientos Model
 *
 * @method \App\Model\Entity\Tratamiento newEmptyEntity()
 * @method \App\Model\Entity\Tratamiento newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Tratamiento> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Tratamiento get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Tratamiento findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Tratamiento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Tratamiento> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Tratamiento|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Tratamiento saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Tratamiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Tratamiento>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Tratamiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Tratamiento> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Tratamiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Tratamiento>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Tratamiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Tratamiento> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TratamientosTable extends Table
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

        $this->setTable('tratamientos');
        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('Presupuestos', [
            'foreignKey' => 'tratamiento_id',
            'targetForeignKey' => 'presupuesto_id',
            'joinTable' => 'presupuestos_tratamientos',
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

        $validator
            ->decimal('costo')
            ->requirePresence('costo', 'create')
            ->notEmptyString('costo');

        $validator
            ->decimal('monto_fijo_pago')
            ->allowEmptyString('monto_fijo_pago')
            ->greaterThanOrEqual('monto_fijo_pago', 0, 'El monto fijo de pago no puede ser negativo.');

        $validator
            ->boolean('estado')
            ->notEmptyString('estado');

        return $validator;
    }
}
