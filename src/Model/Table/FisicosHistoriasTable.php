<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FisicosHistorias Model
 *
 * @property \App\Model\Table\ExamenesFisicosTable&\Cake\ORM\Association\BelongsTo $Examens
 * @property \App\Model\Table\HistoriasClinicasTable&\Cake\ORM\Association\BelongsTo $Historias
 *
 * @method \App\Model\Entity\FisicosHistoria newEmptyEntity()
 * @method \App\Model\Entity\FisicosHistoria newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\FisicosHistoria> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FisicosHistoria get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\FisicosHistoria findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\FisicosHistoria patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\FisicosHistoria> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\FisicosHistoria|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\FisicosHistoria saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\FisicosHistoria>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FisicosHistoria>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FisicosHistoria>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FisicosHistoria> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FisicosHistoria>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FisicosHistoria>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FisicosHistoria>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FisicosHistoria> deleteManyOrFail(iterable $entities, array $options = [])
 */
class FisicosHistoriasTable extends Table
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

        $this->setTable('fisicos_historias');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Examens', [
            'foreignKey' => 'examen_id',
            'className' => 'ExamenesFisicos',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Historias', [
            'foreignKey' => 'historia_id',
            'className' => 'HistoriasClinicas',
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
            ->integer('examen_id')
            ->notEmptyString('examen_id');

        $validator
            ->integer('historia_id')
            ->notEmptyString('historia_id');

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
        $rules->add($rules->existsIn(['examen_id'], 'Examens'), ['errorField' => 'examen_id']);
        $rules->add($rules->existsIn(['historia_id'], 'Historias'), ['errorField' => 'historia_id']);

        return $rules;
    }
}
