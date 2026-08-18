<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Medicamentos Model
 *
 * @property \App\Model\Table\RecetasMedicamentosTable&\Cake\ORM\Association\HasMany $RecetasMedicamentos
 *
 * @method \App\Model\Entity\Medicamento newEmptyEntity()
 * @method \App\Model\Entity\Medicamento newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Medicamento> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Medicamento get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Medicamento findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Medicamento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Medicamento> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Medicamento|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Medicamento saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Medicamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Medicamento>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Medicamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Medicamento> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Medicamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Medicamento>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Medicamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Medicamento> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MedicamentosTable extends Table
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

        $this->setTable('medicamentos');
        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('RecetasMedicamentos', [
            'foreignKey' => 'medicamento_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
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
            ->scalar('codigo')
            ->maxLength('codigo', 50)
            ->requirePresence('codigo', 'create')
            ->notEmptyString('codigo')
            ->add('codigo', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('nombre')
            ->maxLength('nombre', 255)
            ->requirePresence('nombre', 'create')
            ->notEmptyString('nombre');

        $validator
            ->scalar('descripcion')
            ->allowEmptyString('descripcion');

        $validator
            ->scalar('concentracion')
            ->maxLength('concentracion', 100)
            ->allowEmptyString('concentracion');

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
        $rules->add($rules->isUnique(['codigo']), ['errorField' => 'codigo']);

        return $rules;
    }

    /**
     * Buscar medicamentos por código o nombre
     *
     * @param string $busqueda Término de búsqueda
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function buscar(string $busqueda): SelectQuery
    {
        return $this->find()
            ->where([
                'OR' => [
                    'codigo LIKE' => '%' . $busqueda . '%',
                    'nombre LIKE' => '%' . $busqueda . '%',
                ]
            ])
            ->limit(10);
    }
}
