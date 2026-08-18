<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Consultas Model
 *
 * @property \App\Model\Table\DocumentosTable&\Cake\ORM\Association\BelongsTo $Documentos
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\DoctoresTable&\Cake\ORM\Association\BelongsTo $Doctores
 * @property \App\Model\Table\RecetasTable&\Cake\ORM\Association\BelongsToMany $Recetas
 *
 * @method \App\Model\Entity\Consulta newEmptyEntity()
 * @method \App\Model\Entity\Consulta newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Consulta> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Consulta get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Consulta findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Consulta patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Consulta> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Consulta|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Consulta saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Consulta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Consulta>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Consulta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Consulta> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Consulta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Consulta>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Consulta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Consulta> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ConsultasTable extends Table
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

        $this->setTable('consultas');
        $this->setDisplayField('motivo');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('DocumentosConsultas', [
            'foreignKey' => 'consulta_id',
            'dependent' => true,
            'cascadeCallbacks' => true
        ]);
        
        $this->belongsTo('HistoriasClinicas', [
            'foreignKey' => 'historia_id',
            'className' => 'HistoriasClinicas',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Doctores', [
            'foreignKey' => 'doctor_id',
            'joinType' => 'LEFT',
        ]);
        $this->hasMany('RecetasConsultas', [
            'foreignKey' => 'consulta_id',
        ]);
        $this->belongsToMany('Recetas', [
            'through' => 'RecetasConsultas',
            'foreignKey' => 'consulta_id',
            'targetForeignKey' => 'receta_id',
        ]);
        $this->belongsToMany('Diagnosticoscie10', [
            'joinTable' => 'consultas_cie',
            'foreignKey' => 'consulta_id',
            'targetForeignKey' => 'cie_id',
        ]);
        $this->belongsToMany('Categoriascie10', [
            'joinTable' => 'consultas_cie',
            'foreignKey' => 'consulta_id',
            'targetForeignKey' => 'categoria_id',
        ]);
        $this->hasMany('ConsultasCie', [
            'foreignKey' => 'consulta_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
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
            ->scalar('motivo')
            ->allowEmptyString('motivo');

        $validator
            ->scalar('prescripcion')
            ->allowEmptyString('prescripcion');

        $validator
            ->scalar('diagnostico')
            ->allowEmptyString('diagnostico');

        $validator
            ->scalar('orden_medico')
            ->allowEmptyString('orden_medico');

        $validator
            ->integer('documento_id')
            ->allowEmptyString('documento_id');

        $validator
            ->integer('historia_id')
            ->allowEmptyString('historia_id');

        $validator
            ->integer('doctor_id')
            ->allowEmptyString('doctor_id');

        $validator
            ->integer('user_id')
            ->notEmptyString('user_id');

        $validator
            ->scalar('examen_fisico')
            ->allowEmptyString('examen_fisico');

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
        $rules->add($rules->existsIn(['doctor_id'], 'Doctores'), ['errorField' => 'doctor_id']);
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);
        return $rules;
    }
}
