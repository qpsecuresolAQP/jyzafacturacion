<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RecetasMedicamentos Model
 *
 * @property \App\Model\Table\RecetasTable&\Cake\ORM\Association\BelongsTo $Recetas
 * @property \App\Model\Table\MedicamentosTable&\Cake\ORM\Association\BelongsTo $Medicamentos
 * @property \App\Model\Table\FormasFarmaceuticasTable&\Cake\ORM\Association\BelongsTo $FormasFarmaceuticas
 * @property \App\Model\Table\ViasAdministracionTable&\Cake\ORM\Association\BelongsTo $ViasAdministracion
 *
 * @method \App\Model\Entity\RecetaMedicamento newEmptyEntity()
 * @method \App\Model\Entity\RecetaMedicamento newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\RecetaMedicamento> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RecetaMedicamento get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|string $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\RecetaMedicamento findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\RecetaMedicamento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\RecetaMedicamento> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\RecetaMedicamento|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\RecetaMedicamento saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\RecetaMedicamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\RecetaMedicamento>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\RecetaMedicamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\RecetaMedicamento> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\RecetaMedicamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\RecetaMedicamento>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\RecetaMedicamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\RecetaMedicamento> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RecetasMedicamentosTable extends Table
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

        $this->setTable('recetas_medicamentos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Recetas', [
            'foreignKey' => 'receta_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Medicamentos', [
            'foreignKey' => 'medicamento_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('FormasFarmaceuticas', [
            'foreignKey' => 'forma_farmaceutica_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('ViasAdministracion', [
            'foreignKey' => 'via_administracion_id',
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
            ->integer('receta_id')
            ->requirePresence('receta_id', 'create')
            ->notEmptyString('receta_id');

        $validator
            ->integer('medicamento_id')
            ->allowEmptyString('medicamento_id');

        $validator
            ->scalar('nombre_medicamento')
            ->maxLength('nombre_medicamento', 255)
            ->allowEmptyString('nombre_medicamento');

        $validator
            ->scalar('codigo_medicamento')
            ->maxLength('codigo_medicamento', 50)
            ->allowEmptyString('codigo_medicamento');

        $validator
            ->scalar('concentracion')
            ->maxLength('concentracion', 100)
            ->allowEmptyString('concentracion');

        $validator
            ->integer('cantidad')
            ->greaterThan('cantidad', 0)
            ->allowEmptyString('cantidad');

        $validator
            ->integer('forma_farmaceutica_id')
            ->allowEmptyString('forma_farmaceutica_id');

        $validator
            ->integer('via_administracion_id')
            ->allowEmptyString('via_administracion_id');


        $validator
            ->integer('duracion_dias')
            ->allowEmptyString('duracion_dias');

        $validator
            ->scalar('dosis')
            ->maxLength('dosis', 50)
            ->allowEmptyString('dosis');

        $validator
            ->scalar('observaciones')
            ->allowEmptyString('observaciones');

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
        $rules->add($rules->existsIn(['receta_id'], 'Recetas'), ['errorField' => 'receta_id']);
        // medicamento_id es opcional para permitir entrada manual
        $rules->add($rules->existsIn(['medicamento_id'], 'Medicamentos'), ['errorField' => 'medicamento_id', 'allowNullValues' => true]);
        $rules->add($rules->existsIn(['forma_farmaceutica_id'], 'FormasFarmaceuticas'), ['errorField' => 'forma_farmaceutica_id', 'allowNullValues' => true]);
        $rules->add($rules->existsIn(['via_administracion_id'], 'ViasAdministracion'), ['errorField' => 'via_administracion_id', 'allowNullValues' => true]);

        return $rules;
    }
}
