<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RecetasConsultas Model
 *
 * @property \App\Model\Table\ConsultasTable&\Cake\ORM\Association\BelongsTo $Consultas
 * @property \App\Model\Table\RecetasTable&\Cake\ORM\Association\BelongsTo $Recetas
 *
 * @method \App\Model\Entity\RecetasConsulta newEmptyEntity()
 * @method \App\Model\Entity\RecetasConsulta newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\RecetasConsulta> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RecetasConsulta get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\RecetasConsulta findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\RecetasConsulta patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\RecetasConsulta> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\RecetasConsulta|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\RecetasConsulta saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\RecetasConsulta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\RecetasConsulta>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\RecetasConsulta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\RecetasConsulta> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\RecetasConsulta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\RecetasConsulta>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\RecetasConsulta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\RecetasConsulta> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RecetasConsultasTable extends Table
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

        $this->setTable('recetas_consultas');
        $this->setDisplayField('observaciones');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Consultas', [
            'foreignKey' => 'consulta_id',
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
            ->integer('consulta_id')
            ->notEmptyString('consulta_id');

        $validator
            ->integer('receta_id')
            ->notEmptyString('receta_id');

        $validator
            ->scalar('observaciones')
            ->maxLength('observaciones', 255)
            ->allowEmptyString('observaciones');
        
        $validator
            ->scalar('descripcion')
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
        $rules->add($rules->existsIn(['receta_id'], 'Recetas'), ['errorField' => 'receta_id']);

        return $rules;
    }
}
