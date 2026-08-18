<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DocumentosProcedimientos Model
 *
 * @property \App\Model\Table\ProcedimientosTable&\Cake\ORM\Association\BelongsTo $Procedimientos
 * @property \App\Model\Table\DocumentosTable&\Cake\ORM\Association\BelongsTo $Documentos
 *
 * @method \App\Model\Entity\DocumentosProcedimiento newEmptyEntity()
 * @method \App\Model\Entity\DocumentosProcedimiento newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\DocumentosProcedimiento> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DocumentosProcedimiento get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\DocumentosProcedimiento findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\DocumentosProcedimiento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\DocumentosProcedimiento> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\DocumentosProcedimiento|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\DocumentosProcedimiento saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\DocumentosProcedimiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\DocumentosProcedimiento>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\DocumentosProcedimiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\DocumentosProcedimiento> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\DocumentosProcedimiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\DocumentosProcedimiento>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\DocumentosProcedimiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\DocumentosProcedimiento> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DocumentosProcedimientosTable extends Table
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

        $this->setTable('documentos_procedimientos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Procedimientos', [
            'foreignKey' => 'procedimiento_id',
        ]);
        $this->belongsTo('Documentos', [
            'foreignKey' => 'documento_id',
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
            ->integer('procedimiento_id')
            ->allowEmptyString('procedimiento_id');

        $validator
            ->integer('documento_id')
            ->allowEmptyString('documento_id');

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
        $rules->add($rules->existsIn(['procedimiento_id'], 'Procedimientos'), ['errorField' => 'procedimiento_id']);
        $rules->add($rules->existsIn(['documento_id'], 'Documentos'), ['errorField' => 'documento_id']);

        return $rules;
    }
}
