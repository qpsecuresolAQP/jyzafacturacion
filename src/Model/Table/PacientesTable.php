<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Pacientes Model
 *
 * @property \App\Model\Table\HistoriasClinicasTable&\Cake\ORM\Association\HasMany $HistoriasClinicas
 *
 * @method \App\Model\Entity\Paciente newEmptyEntity()
 * @method \App\Model\Entity\Paciente newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Paciente> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Paciente get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Paciente findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Paciente patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Paciente> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Paciente|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Paciente saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Paciente>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Paciente>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Paciente>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Paciente> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Paciente>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Paciente>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Paciente>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Paciente> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PacientesTable extends Table
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

        $this->setTable('pacientes');
        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasOne('HistoriasClinicas', [
            'foreignKey' => 'paciente_id',
        ]);
        $this->hasMany('VistaReportePacientes', [
            'foreignKey' => 'paciente_id',
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
            ->scalar('telefono_celular')
            ->maxLength('telefono_celular', 22)
            ->allowEmptyString('telefono_celular');

        return $validator;
    }
}
