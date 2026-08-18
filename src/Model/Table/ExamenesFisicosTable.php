<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ExamenesFisicos Model
 *
 * @method \App\Model\Entity\ExamenesFisico newEmptyEntity()
 * @method \App\Model\Entity\ExamenesFisico newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\ExamenesFisico> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ExamenesFisico get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\ExamenesFisico findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\ExamenesFisico patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\ExamenesFisico> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ExamenesFisico|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\ExamenesFisico saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\ExamenesFisico>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ExamenesFisico>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ExamenesFisico>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ExamenesFisico> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ExamenesFisico>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ExamenesFisico>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ExamenesFisico>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ExamenesFisico> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ExamenesFisicosTable extends Table
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

        $this->setTable('examenes_fisicos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsToMany('HistoriasClinicas', [
            'joinTable' => 'FisicosHistorias',
            'foreignKey' => 'examen_id',
            'targetForeignKey' => 'historia_id',
        ]);

        $this->belongsToMany('ConsultasGinecologicas', [
            'joinTable' => 'ConsultasGinecologicas',
            'foreignKey' => 'examen_id',
            'targetForeignKey' => 'ginecologica_id',
        ]);

        $this->addBehavior('Timestamp');
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
            ->integer('edad')
            ->allowEmptyString('edad');

        $validator
            ->decimal('peso')
            ->allowEmptyString('peso');

        $validator
            ->decimal('altura')
            ->allowEmptyString('altura');

        $validator
            ->decimal('temperatura')
            ->allowEmptyString('temperatura');

        $validator
            ->decimal('imc')
            ->allowEmptyString('imc');

        $validator
            ->decimal('fr')
            ->allowEmptyString('fr');

        $validator
            ->decimal('eva')
            ->allowEmptyString('eva');

        $validator
            ->decimal('presion')
            ->allowEmptyString('presion');

        $validator
            ->decimal('frecuencia_cardiaca')
            ->allowEmptyString('frecuencia_cardiaca');

        $validator
            ->decimal('saturacion')
            ->allowEmptyString('saturacion');

        $validator
            ->decimal('glicemina')
            ->allowEmptyString('glicemina');

        return $validator;
    }
}
