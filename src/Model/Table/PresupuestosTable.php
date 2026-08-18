<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Presupuestos Model
 *
 * @property \App\Model\Table\HistoriasClinicasTable&\Cake\ORM\Association\BelongsTo $HistoriasClinicas
 *
 * @method \App\Model\Entity\Presupuesto newEmptyEntity()
 * @method \App\Model\Entity\Presupuesto newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Presupuesto> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Presupuesto get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Presupuesto findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Presupuesto patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Presupuesto> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Presupuesto|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Presupuesto saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Presupuesto>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Presupuesto>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Presupuesto>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Presupuesto> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Presupuesto>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Presupuesto>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Presupuesto>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Presupuesto> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PresupuestosTable extends Table
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

        $this->setTable('presupuestos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('HistoriasClinicas', [
            'foreignKey' => 'historia_id',
        ]);
        $this->hasMany('PresupuestosTratamientos', [
            'foreignKey' => 'presupuesto_id',
        ]);
        $this->hasMany('PresupuestosInvoices', [
            'foreignKey' => 'presupuesto_id',
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
            ->allowEmptyString('historia_id', 'create');

        $validator
            ->scalar('nombre_apellido')
            ->allowEmptyString('nombre_apellido');

        $validator
            ->scalar('telefono')
            ->allowEmptyString('telefono');

        $validator
            ->decimal('total')
            ->allowEmptyString('total');

        $validator
            ->scalar('notas')
            ->allowEmptyString('notas');

        $validator
            ->decimal('tipo_de_cambio')
            ->allowEmptyString('tipo_de_cambio');

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

        return $rules;
    }
}
