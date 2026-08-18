<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PresupuestosTratamientos Model
 *
 * @property \App\Model\Table\PresupuestosTable&\Cake\ORM\Association\BelongsTo $Presupuestos
 * @property \App\Model\Table\TratamientosTable&\Cake\ORM\Association\BelongsTo $Tratamientos
 *
 * @method \App\Model\Entity\PresupuestosTratamiento newEmptyEntity()
 * @method \App\Model\Entity\PresupuestosTratamiento newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\PresupuestosTratamiento> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PresupuestosTratamiento get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\PresupuestosTratamiento findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\PresupuestosTratamiento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\PresupuestosTratamiento> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\PresupuestosTratamiento|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\PresupuestosTratamiento saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\PresupuestosTratamiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PresupuestosTratamiento>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PresupuestosTratamiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PresupuestosTratamiento> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PresupuestosTratamiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PresupuestosTratamiento>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PresupuestosTratamiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PresupuestosTratamiento> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PresupuestosTratamientosTable extends Table
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

        $this->setTable('presupuestos_tratamientos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Presupuestos', [
            'foreignKey' => 'presupuesto_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Tratamientos', [
            'foreignKey' => 'tratamiento_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Productos', [
            'foreignKey' => 'producto_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Examenes', [
            'foreignKey' => 'examen_id',
            'joinType' => 'LEFT',
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
            ->integer('presupuesto_id')
            ->notEmptyString('presupuesto_id');

        $validator
            ->integer('tratamiento_id')
            ->allowEmptyString('tratamiento_id');

        $validator
            ->scalar('tipo_item')
            ->requirePresence('tipo_item', 'create')
            ->notEmptyString('tipo_item')
            ->add('tipo_item', 'inList', [
                'rule' => ['inList', ['tratamiento', 'producto', 'examen']],
                'message' => 'Tipo de ítem inválido.',
            ]);

        $validator
            ->integer('producto_id')
            ->allowEmptyString('producto_id');

        $validator
            ->integer('examen_id')
            ->allowEmptyString('examen_id');

        $validator
            ->decimal('precio_unitario')
            ->requirePresence('precio_unitario', 'create')
            ->notEmptyString('precio_unitario');

        $validator
            ->integer('cantidad')
            ->requirePresence('cantidad', 'create')
            ->notEmptyString('cantidad');
        
        $validator
            ->integer('descuento')
            ->allowEmptyString('descuento');

        $validator
            ->scalar('descuento_tipo')
            ->allowEmptyString('descuento_tipo')
            ->add('descuento_tipo', 'inList', [
                'rule' => ['inList', ['porcentaje', 'monto']],
                'message' => 'Tipo de descuento inválido.',
            ]);

        $validator
            ->scalar('observaciones')
            ->allowEmptyString('observaciones');

        $validator
            ->decimal('total')
            ->requirePresence('total', 'create')
            ->notEmptyString('total');

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
        $rules->add($rules->existsIn(['presupuesto_id'], 'Presupuestos'), ['errorField' => 'presupuesto_id']);
        $rules->add($rules->existsIn(['tratamiento_id'], 'Tratamientos'), ['errorField' => 'tratamiento_id']);
        $rules->add($rules->existsIn(['producto_id'], 'Productos'), ['errorField' => 'producto_id']);
        $rules->add($rules->existsIn(['examen_id'], 'Examenes'), ['errorField' => 'examen_id']);

        return $rules;
    }
}
