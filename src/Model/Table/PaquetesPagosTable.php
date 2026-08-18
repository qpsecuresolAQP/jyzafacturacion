<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PaquetesPagos Model
 *
 * @property \App\Model\Table\HistoriasClinicasTable&\Cake\ORM\Association\BelongsTo $HistoriasClinicas
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\PaquetesPagosCuotasTable&\Cake\ORM\Association\HasMany $PaquetesPagosCuotas
 *
 * @method \App\Model\Entity\PaquetesPago newEmptyEntity()
 * @method \App\Model\Entity\PaquetesPago newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\PaquetesPago> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PaquetesPago get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\PaquetesPago findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\PaquetesPago patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\PaquetesPago> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\PaquetesPago|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\PaquetesPago saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\PaquetesPago>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PaquetesPago>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PaquetesPago>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PaquetesPago> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PaquetesPago>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PaquetesPago>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PaquetesPago>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PaquetesPago> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PaquetesPagosTable extends Table
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

        $this->setTable('paquetes_pagos');
        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('HistoriasClinicas', [
            'foreignKey' => 'historia_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('PaquetesPagosCuotas', [
            'foreignKey' => 'paquete_pago_id',
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
            ->integer('historia_id')
            ->notEmptyString('historia_id');

        $validator
            ->scalar('nombre')
            ->maxLength('nombre', 255)
            ->requirePresence('nombre', 'create')
            ->notEmptyString('nombre');

        $validator
            ->scalar('descripcion')
            ->allowEmptyString('descripcion');

        $validator
            ->integer('num_sesiones')
            ->requirePresence('num_sesiones', 'create')
            ->notEmptyString('num_sesiones');

        $validator
            ->decimal('precio_total')
            ->requirePresence('precio_total', 'create')
            ->notEmptyString('precio_total');

        $validator
            ->inList('tipo_pago', ['contado', 'partes'])
            ->requirePresence('tipo_pago', 'create')
            ->notEmptyString('tipo_pago');

        $validator
            ->date('fecha_recordatorio')
            ->requirePresence('fecha_recordatorio', 'create')
            ->notEmptyDate('fecha_recordatorio');

        $validator
            ->inList('estado', ['pendiente', 'cancelado'])
            ->requirePresence('estado', 'create')
            ->notEmptyString('estado');

        $validator
            ->integer('user_id')
            ->notEmptyString('user_id');

        $validator
            ->scalar('estado_paquete')
            ->maxLength('estado_paquete', 1)
            ->allowEmptyString('estado_paquete');

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
        $rules->add($rules->existsIn(['historia_id'], 'HistoriasClinicas'), ['errorField' => 'historia_id']);
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
