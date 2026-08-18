<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * VistaReportePacientes Model
 *
 * @property \App\Model\Table\PacientesTable&\Cake\ORM\Association\BelongsTo $Pacientes
 * @property \App\Model\Table\DepartamentosTable&\Cake\ORM\Association\BelongsTo $Departamentos
 *
 * @method \App\Model\Entity\VistaReportePaciente newEmptyEntity()
 * @method \App\Model\Entity\VistaReportePaciente newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\VistaReportePaciente> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\VistaReportePaciente get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\VistaReportePaciente findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\VistaReportePaciente patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\VistaReportePaciente> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\VistaReportePaciente|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\VistaReportePaciente saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\VistaReportePaciente>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaReportePaciente>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VistaReportePaciente>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaReportePaciente> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VistaReportePaciente>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaReportePaciente>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VistaReportePaciente>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaReportePaciente> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class VistaReportePacientesTable extends Table
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

        $this->setTable('vista_reporte_pacientes');
        $this->setDisplayField('nombre_paciente');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Pacientes', [
            'foreignKey' => 'paciente_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Departamentos', [
            'foreignKey' => 'departamento_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'usuario_id',
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
            ->integer('paciente_id')
            ->notEmptyString('paciente_id');

        $validator
            ->scalar('nombre_paciente')
            ->maxLength('nombre_paciente', 255)
            ->requirePresence('nombre_paciente', 'create')
            ->notEmptyString('nombre_paciente');

        $validator
            ->scalar('apellido_paciente')
            ->maxLength('apellido_paciente', 255)
            ->requirePresence('apellido_paciente', 'create')
            ->notEmptyString('apellido_paciente');

        $validator
            ->integer('usuario_id')
            ->notEmptyString('usuario_id');

        $validator
            ->scalar('nombre_usuario')
            ->maxLength('nombre_usuario', 100)
            ->requirePresence('nombre_usuario', 'create')
            ->notEmptyString('nombre_usuario');

        $validator
            ->integer('departamento_id')
            ->notEmptyString('departamento_id');

        $validator
            ->scalar('nombre_departamento')
            ->maxLength('nombre_departamento', 50)
            ->requirePresence('nombre_departamento', 'create')
            ->notEmptyString('nombre_departamento');

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
        $rules->add($rules->existsIn(['paciente_id'], 'Pacientes'), ['errorField' => 'paciente_id']);
        $rules->add($rules->existsIn(['departamento_id'], 'Departamentos'), ['errorField' => 'departamento_id']);

        return $rules;
    }
}
