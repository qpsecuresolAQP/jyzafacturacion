<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * HorariosBloqueos Model
 *
 * @property \App\Model\Table\DoctoresTable&\Cake\ORM\Association\BelongsTo $Doctores
 *
 * @method \App\Model\Entity\HorariosBloqueo newEmptyEntity()
 * @method \App\Model\Entity\HorariosBloqueo newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\HorariosBloqueo> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\HorariosBloqueo get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\HorariosBloqueo findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\HorariosBloqueo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\HorariosBloqueo> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\HorariosBloqueo|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\HorariosBloqueo saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\HorariosBloqueo>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\HorariosBloqueo>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\HorariosBloqueo>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\HorariosBloqueo> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\HorariosBloqueo>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\HorariosBloqueo>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\HorariosBloqueo>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\HorariosBloqueo> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class HorariosBloqueosTable extends Table
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

        $this->setTable('horarios_bloqueos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Doctores', [
            'foreignKey' => 'doctor_id',
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
            ->integer('doctor_id')
            ->notEmptyString('doctor_id', 'Debes seleccionar un doctor');

        $validator
            ->date('fecha')
            ->requirePresence('fecha', 'create')
            ->notEmptyDate('fecha', 'La fecha es obligatoria');

        $validator
            ->time('hora_inicio')
            ->requirePresence('hora_inicio', 'create')
            ->notEmptyTime('hora_inicio', 'La hora de inicio es obligatoria');

        $validator
            ->time('hora_fin')
            ->requirePresence('hora_fin', 'create')
            ->notEmptyTime('hora_fin', 'La hora de fin es obligatoria');

        $validator
            ->scalar('motivo')
            ->maxLength('motivo', 255, 'El motivo no puede exceder 255 caracteres')
            ->allowEmptyString('motivo');

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
        $rules->add($rules->isUnique(['doctor_id', 'fecha', 'hora_inicio']), ['errorField' => 'doctor_id']);
        $rules->add($rules->existsIn(['doctor_id'], 'Doctores'), ['errorField' => 'doctor_id']);

        return $rules;
    }

    /**
     * Verifica si hay bloqueos en una fecha y hora específica
     */
    public function verificarBloqueo($doctorId, $fecha, $horaInicio, $horaFin)
    {
        $bloqueo = $this->find()
            ->where([
                'doctor_id' => $doctorId,
                'fecha' => $fecha,
                'OR' => [
                    [
                        'hora_inicio <' => $horaFin,
                        'hora_fin >' => $horaInicio
                    ]
                ]
            ])
            ->first();

        return $bloqueo !== null;
    }
}
