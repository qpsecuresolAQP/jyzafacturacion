<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Citas Model
 *
 * @property \App\Model\Table\DoctoresTable&\Cake\ORM\Association\BelongsTo $Doctores
 * @property \App\Model\Table\PacientesTable&\Cake\ORM\Association\BelongsTo $Pacientes
 *
 * @method \App\Model\Entity\Cita newEmptyEntity()
 * @method \App\Model\Entity\Cita newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Cita> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Cita get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Cita findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Cita patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Cita> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Cita|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Cita saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Cita>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Cita>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Cita>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Cita> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Cita>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Cita>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Cita>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Cita> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CitasTable extends Table
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

        $this->setTable('citas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Doctores', [
            'foreignKey' => 'doctor_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Pacientes', [
            'foreignKey' => 'paciente_id',
            'joinType' => 'INNER',
        ]);
        

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Tratamientos', [
            'foreignKey' => 'tratamiento_id',
            'joinType' => 'INNER',  // Cambia a 'LEFT' si es opcional
        ]);
        $this->hasMany('CitasTratamientos', [
            'foreignKey' => 'cita_id',
            'dependent' => true,
        ]);
        $this->belongsTo('Campanas', [
            'foreignKey' => 'campana_id',
            'className' => 'Campañas',
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
            ->integer('doctor_id')
            ->notEmptyString('doctor_id');

        $validator
            ->integer('paciente_id')
            ->notEmptyString('paciente_id', 'Por favor, Seleccione un paciente.');


        $validator
            ->dateTime('fecha_hora')
            ->requirePresence('fecha_hora', 'create')
            ->notEmptyDateTime('fecha_hora');

        $validator
            ->scalar('motivo')
            ->maxLength('motivo', 255)
            ->allowEmptyString('motivo');

        $validator
            ->scalar('estado')
            ->maxLength('estado', 100)
            ->allowEmptyString('estado');

        $validator
            ->integer('duracion_minutos')
            ->requirePresence('duracion_minutos', 'create')
            ->notEmptyString('duracion_minutos', 'La duración es obligatoria')
            ->add('duracion_minutos', 'validDuration', [
                'rule' => function ($value, $context) {
                    return in_array($value, [15, 30, 45, 60, 75, 90, 105, 120, 135, 150, 165, 180]); // Solo permite estos valores
                },
                'message' => 'La duración debe ser 15, 30, 45, 60, 75, 90, 105, 120, 135, 150, 165 o 180 minutos'
            ]);
        $validator
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id', 'El usuario es obligatorio.');
        $validator
            ->time('hora_llegada')
            ->allowEmptyTime('hora_llegada'); // Permite que pueda ser NULL

        $validator
            ->time('hora_inicio_consulta') // ✅ Agregar validación
            ->allowEmptyTime('hora_inicio_consulta');

        $validator
            ->time('hora_fin_consulta') // ✅ Agregar validación
            ->allowEmptyTime('hora_fin_consulta');

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
        $rules->add($rules->existsIn(['doctor_id'], 'Doctores'), ['errorField' => 'doctor_id']);
        $rules->add($rules->existsIn(['paciente_id'], 'Pacientes'), ['errorField' => 'paciente_id']);

        return $rules;
    }
}
