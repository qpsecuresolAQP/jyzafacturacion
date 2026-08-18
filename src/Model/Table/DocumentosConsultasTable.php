<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DocumentosConsultas Model
 *
 * @property \App\Model\Table\ConsultasTable&\Cake\ORM\Association\BelongsTo $Consultas
 * @property \App\Model\Table\DocumentosTable&\Cake\ORM\Association\BelongsTo $Documentos
 *
 * @method \App\Model\Entity\DocumentosConsulta newEmptyEntity()
 * @method \App\Model\Entity\DocumentosConsulta newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\DocumentosConsulta> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DocumentosConsulta get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\DocumentosConsulta findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\DocumentosConsulta patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\DocumentosConsulta> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\DocumentosConsulta|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\DocumentosConsulta saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\DocumentosConsulta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\DocumentosConsulta>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\DocumentosConsulta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\DocumentosConsulta> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\DocumentosConsulta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\DocumentosConsulta>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\DocumentosConsulta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\DocumentosConsulta> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DocumentosConsultasTable extends Table
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

        $this->setTable('documentos_consultas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Consultas', [
            'foreignKey' => 'consulta_id',
        ]);
        $this->belongsTo('Documentos', [
            'foreignKey' => 'documento_id',
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
            ->integer('consulta_id')
            ->allowEmptyString('consulta_id');

        $validator
            ->integer('documento_id')
            ->allowEmptyString('documento_id');

        $validator
            ->scalar('ruta_archivo')
            ->maxLength('ruta_archivo', 500)
            ->allowEmptyString('ruta_archivo');

        $validator
            ->scalar('descripcion')
            ->maxLength('descripcion', 500)
            ->allowEmptyString('descripcion');

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
        $rules->add($rules->existsIn(['consulta_id'], 'Consultas'), ['errorField' => 'consulta_id']);
        $rules->add($rules->existsIn(['documento_id'], 'Documentos'), ['errorField' => 'documento_id']);

        return $rules;
    }
}
