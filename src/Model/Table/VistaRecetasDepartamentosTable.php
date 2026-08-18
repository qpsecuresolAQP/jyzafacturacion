<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * VistaRecetasDepartamentos Model
 *
 * @property \App\Model\Table\DepartamentosTable&\Cake\ORM\Association\BelongsTo $Departamentos
 * @property \App\Model\Table\PacientesTable&\Cake\ORM\Association\BelongsTo $Pacientes
 * @property \App\Model\Table\RecetasTable&\Cake\ORM\Association\BelongsTo $Recetas
 *
 * @method \App\Model\Entity\VistaRecetasDepartamento newEmptyEntity()
 * @method \App\Model\Entity\VistaRecetasDepartamento newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\VistaRecetasDepartamento> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\VistaRecetasDepartamento get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\VistaRecetasDepartamento findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\VistaRecetasDepartamento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\VistaRecetasDepartamento> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\VistaRecetasDepartamento|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\VistaRecetasDepartamento saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\VistaRecetasDepartamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaRecetasDepartamento>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VistaRecetasDepartamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaRecetasDepartamento> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VistaRecetasDepartamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaRecetasDepartamento>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VistaRecetasDepartamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaRecetasDepartamento> deleteManyOrFail(iterable $entities, array $options = [])
 */
class VistaRecetasDepartamentosTable extends Table
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

        $this->setTable('vista_recetas_departamentos');
        $this->setDisplayField('nombre_departamento');

        $this->belongsTo('Departamentos', [
            'foreignKey' => 'departamento_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Pacientes', [
            'foreignKey' => 'paciente_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Recetas', [
            'foreignKey' => 'receta_id',
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
            ->integer('departamento_id')
            ->notEmptyString('departamento_id');

        $validator
            ->scalar('nombre_departamento')
            ->maxLength('nombre_departamento', 50)
            ->requirePresence('nombre_departamento', 'create')
            ->notEmptyString('nombre_departamento');

        $validator
            ->integer('paciente_id')
            ->notEmptyString('paciente_id');

        $validator
            ->scalar('nombre_paciente')
            ->maxLength('nombre_paciente', 511)
            ->notEmptyString('nombre_paciente');

        $validator
            ->integer('receta_id')
            ->notEmptyString('receta_id');

        $validator
            ->scalar('nombre_receta')
            ->maxLength('nombre_receta', 300)
            ->allowEmptyString('nombre_receta');

        $validator
            ->dateTime('fecha_consulta')
            ->requirePresence('fecha_consulta', 'create')
            ->notEmptyDateTime('fecha_consulta');

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
        $rules->add($rules->existsIn(['departamento_id'], 'Departamentos'), ['errorField' => 'departamento_id']);
        $rules->add($rules->existsIn(['paciente_id'], 'Pacientes'), ['errorField' => 'paciente_id']);
        $rules->add($rules->existsIn(['receta_id'], 'Recetas'), ['errorField' => 'receta_id']);

        return $rules;
    }
}
