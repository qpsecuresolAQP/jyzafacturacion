<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Departamentos Model
 *
 * @property \App\Model\Table\HistoriasClinicasTable&\Cake\ORM\Association\HasMany $HistoriasClinicas
 *
 * @method \App\Model\Entity\Departamento newEmptyEntity()
 * @method \App\Model\Entity\Departamento newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Departamento> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Departamento get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Departamento findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Departamento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Departamento> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Departamento|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Departamento saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Departamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Departamento>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Departamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Departamento> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Departamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Departamento>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Departamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Departamento> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DepartamentosTable extends Table
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

        $this->setTable('departamentos');
        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasOne('HistoriasClinicas', [
            'foreignKey' => 'departamento_id',
        ]);

        $this->hasMany('VistaReportePacientes', [
            'foreignKey' => 'departamento_id',
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
            ->scalar('nombre')
            ->maxLength('nombre', 50)
            ->requirePresence('nombre', 'create')
            ->notEmptyString('nombre');

        return $validator;
    }
}
