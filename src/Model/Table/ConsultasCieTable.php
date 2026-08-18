<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ConsultasCie Model
 *
 * @property \App\Model\Table\ConsultasTable&\Cake\ORM\Association\BelongsTo $Consultas
 * @property \App\Model\Table\Diagnosticoscie10Table&\Cake\ORM\Association\BelongsTo $Cies
 *
 * @method \App\Model\Entity\ConsultasCie newEmptyEntity()
 * @method \App\Model\Entity\ConsultasCie newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\ConsultasCie> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ConsultasCie get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\ConsultasCie findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\ConsultasCie patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\ConsultasCie> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ConsultasCie|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\ConsultasCie saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\ConsultasCie>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ConsultasCie>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ConsultasCie>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ConsultasCie> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ConsultasCie>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ConsultasCie>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ConsultasCie>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ConsultasCie> deleteManyOrFail(iterable $entities, array $options = [])
 */
class ConsultasCieTable extends Table
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

        $this->setTable('consultas_cie');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Consultas', [
            'foreignKey' => 'consulta_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Diagnosticoscie10', [
            'foreignKey' => 'cie_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Categoriascie10', [
            'foreignKey' => 'categoria_id',
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
            ->integer('consulta_id')
            ->notEmptyString('consulta_id');

        $validator
            ->integer('cie_id')
            ->allowEmptyString('cie_id');
        
        $validator
            ->integer('categoria_id')
            ->allowEmptyString('categoria_id');

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
        $rules->add($rules->existsIn(['cie_id'], 'Diagnosticoscie10'), ['errorField' => 'cie_id']);
        $rules->add($rules->existsIn(['categoria_id'], 'Categoriascie10'), ['errorField' => 'categoria_id']);

        return $rules;
    }
}
