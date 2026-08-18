<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * VistaReporteConsultasDoctores Model
 *
 * @method \App\Model\Entity\VistaReporteConsultasDoctore newEmptyEntity()
 * @method \App\Model\Entity\VistaReporteConsultasDoctore newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\VistaReporteConsultasDoctore> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\VistaReporteConsultasDoctore get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\VistaReporteConsultasDoctore findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\VistaReporteConsultasDoctore patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\VistaReporteConsultasDoctore> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\VistaReporteConsultasDoctore|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\VistaReporteConsultasDoctore saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\VistaReporteConsultasDoctore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaReporteConsultasDoctore>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VistaReporteConsultasDoctore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaReporteConsultasDoctore> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VistaReporteConsultasDoctore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaReporteConsultasDoctore>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VistaReporteConsultasDoctore>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaReporteConsultasDoctore> deleteManyOrFail(iterable $entities, array $options = [])
 */
class VistaReporteConsultasDoctoresTable extends Table
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

        $this->setTable('vista_reporte_consultas_doctores');
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
            ->integer('doctor_id')
            ->allowEmptyString('doctor_id');

        $validator
            ->scalar('doctor_nombre')
            ->maxLength('doctor_nombre', 255)
            ->allowEmptyString('doctor_nombre');

        $validator
            ->dateTime('fecha_consulta')
            ->allowEmptyDateTime('fecha_consulta');

        $validator
            ->notEmptyString('total_consultas');

        return $validator;
    }
}
