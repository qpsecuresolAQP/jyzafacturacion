<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * VistaConsultasProcedimientos Model
 *
 * @method \App\Model\Entity\VistaConsultasProcedimiento newEmptyEntity()
 * @method \App\Model\Entity\VistaConsultasProcedimiento newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\VistaConsultasProcedimiento> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\VistaConsultasProcedimiento get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\VistaConsultasProcedimiento findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\VistaConsultasProcedimiento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\VistaConsultasProcedimiento> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\VistaConsultasProcedimiento|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\VistaConsultasProcedimiento saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\VistaConsultasProcedimiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaConsultasProcedimiento>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VistaConsultasProcedimiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaConsultasProcedimiento> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VistaConsultasProcedimiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaConsultasProcedimiento>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\VistaConsultasProcedimiento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\VistaConsultasProcedimiento> deleteManyOrFail(iterable $entities, array $options = [])
 */
class VistaConsultasProcedimientosTable extends Table
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

        $this->setTable('vista_consultas_procedimientos');
        $this->setDisplayField('nombre');
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
            ->scalar('dni')
            ->maxLength('dni', 20)
            ->allowEmptyString('dni');

        $validator
            ->scalar('nombre')
            ->maxLength('nombre', 255)
            ->requirePresence('nombre', 'create')
            ->notEmptyString('nombre');

        $validator
            ->scalar('apellido')
            ->maxLength('apellido', 255)
            ->requirePresence('apellido', 'create')
            ->notEmptyString('apellido');

        $validator
            ->scalar('motivo')
            ->allowEmptyString('motivo');

        $validator
            ->allowEmptyString('procedimiento');

        return $validator;
    }
}
