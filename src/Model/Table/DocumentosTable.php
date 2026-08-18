<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Documentos Model
 *
 * @property \App\Model\Table\HistoriasClinicasTable&\Cake\ORM\Association\BelongsTo $Historias
 * @property \App\Model\Table\ConsultasTable&\Cake\ORM\Association\BelongsTo $Consultas
 *
 * @method \App\Model\Entity\Documento newEmptyEntity()
 * @method \App\Model\Entity\Documento newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Documento> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Documento get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Documento findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Documento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Documento> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Documento|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Documento saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Documento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Documento>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Documento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Documento> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Documento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Documento>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Documento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Documento> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DocumentosTable extends Table
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

        $this->setTable('documentos');
        $this->setDisplayField('descripcion');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('HistoriasClinicas', [ 
            'foreignKey' => 'historia_id',
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
            ->integer('historia_id')
            ->notEmptyString('historia_id');

        $validator
            ->scalar('descripcion')
            ->maxLength('descripcion', 300)
            ->allowEmptyString('descripcion');

        $validator
            ->scalar('tipo')
            ->maxLength('tipo', 20)
            ->allowEmptyString('tipo');

        $validator
            ->scalar('ruta_archivo')
            ->maxLength('ruta_archivo', 255)
            ->allowEmptyString('ruta_archivo');

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
