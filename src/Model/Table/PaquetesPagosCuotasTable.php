<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PaquetesPagosCuotas Model
 *
 * @property \App\Model\Table\PaquetesPagosTable&\Cake\ORM\Association\BelongsTo $PaquetesPagos
 *
 * @method \App\Model\Entity\PaquetesPagosCuota newEmptyEntity()
 * @method \App\Model\Entity\PaquetesPagosCuota newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\PaquetesPagosCuota> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PaquetesPagosCuota get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\PaquetesPagosCuota findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\PaquetesPagosCuota patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\PaquetesPagosCuota> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\PaquetesPagosCuota|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\PaquetesPagosCuota saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\PaquetesPagosCuota>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PaquetesPagosCuota>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PaquetesPagosCuota>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PaquetesPagosCuota> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PaquetesPagosCuota>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PaquetesPagosCuota>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PaquetesPagosCuota>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PaquetesPagosCuota> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PaquetesPagosCuotasTable extends Table
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

        $this->setTable('paquetes_pagos_cuotas');
        $this->setDisplayField('titulo_referencial');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('PaquetesPagos', [
            'foreignKey' => 'paquete_pago_id',
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
            ->integer('paquete_pago_id')
            ->notEmptyString('paquete_pago_id');

        $validator
            ->scalar('titulo_referencial')
            ->maxLength('titulo_referencial', 255)
            ->allowEmptyString('titulo_referencial');

        $validator
            ->decimal('monto')
            ->requirePresence('monto', 'create')
            ->notEmptyString('monto');

        $validator
            ->date('fecha_recordatorio')
            ->requirePresence('fecha_recordatorio', 'create')
            ->notEmptyDate('fecha_recordatorio');

        $validator
            ->inList('estado', ['pendiente', 'pagado'])
            ->requirePresence('estado', 'create')
            ->notEmptyString('estado');

        $validator
            ->dateTime('fecha_pago')
            ->allowEmptyDateTime('fecha_pago');

        $validator
            ->scalar('estado_cuota')
            ->maxLength('estado_cuota', 1)
            ->allowEmptyString('estado_cuota');

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
        $rules->add($rules->existsIn(['paquete_pago_id'], 'PaquetesPagos'), ['errorField' => 'paquete_pago_id']);

        return $rules;
    }
}
