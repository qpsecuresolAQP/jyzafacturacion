<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * HistoriasClinicas Model
 *
 * @property \App\Model\Table\PacientesTable&\Cake\ORM\Association\BelongsTo $Pacientes
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\PresupuestosTable&\Cake\ORM\Association\HasMany $Presupuestos
 *
 * @method \App\Model\Entity\HistoriasClinica newEmptyEntity()
 * @method \App\Model\Entity\HistoriasClinica newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\HistoriasClinica> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\HistoriasClinica get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\HistoriasClinica findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\HistoriasClinica patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\HistoriasClinica> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\HistoriasClinica|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\HistoriasClinica saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\HistoriasClinica>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\HistoriasClinica>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\HistoriasClinica>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\HistoriasClinica> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\HistoriasClinica>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\HistoriasClinica>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\HistoriasClinica>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\HistoriasClinica> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class HistoriasClinicasTable extends Table
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

        $this->setTable('historias_clinicas');
        $this->setDisplayField('antecedentes');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Pacientes', [
            'foreignKey' => 'paciente_id',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Presupuestos', [
            'foreignKey' => 'historia_id',
            'dependent' => true,
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
            ->integer('paciente_id')
            ->allowEmptyString('paciente_id');

        $validator
            ->scalar('dni')
            ->maxLength('dni', 20, 'El DNI no puede tener más de 20 caracteres.')
            ->allowEmptyString('dni')
            ->add('dni', 'unique', ['rule' => 'validateUnique', 'provider' => 'table', 'message' => 'Este DNI ya está registrado.']);

        $validator
            ->date('fecha_nacimiento')
            ->allowEmptyDate('fecha_nacimiento');

        $validator
            ->integer('edad')
            ->allowEmptyString('edad');

        $validator
            ->integer('departamento_id')
            ->allowEmptyString('departamento_id');

        $validator
            ->scalar('sexo')
            ->maxLength('sexo', 2)
            ->allowEmptyString('sexo');

        $validator
            ->scalar('tipo_orden')
            ->maxLength('tipo_orden', 255)
            ->allowEmptyString('tipo_orden');

        $validator
            ->integer('user_id')
            ->allowEmptyString('user_id');

        $validator
            ->scalar('como_entero')
            ->maxLength('como_entero', 255)
            ->allowEmptyString('como_entero');

        $validator
            ->scalar('obs_administrativas')
            ->maxLength('obs_administrativas', 255)
            ->allowEmptyString('obs_administrativas');

        $validator
            ->scalar('ocupacion')
            ->maxLength('ocupacion', 255)
            ->allowEmptyString('ocupacion');

        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->scalar('parentesco')
            ->maxLength('parentesco', 100)
            ->allowEmptyString('parentesco');

        $validator
            ->scalar('apoderado')
            ->maxLength('apoderado', 100)
            ->allowEmptyString('apoderado');

        $validator
            ->scalar('recomendado')
            ->maxLength('recomendado', 100)
            ->allowEmptyString('recomendado');

        $validator
            ->scalar('direccion')
            ->maxLength('direccion', 255)
            ->allowEmptyString('direccion');

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
        $rules->add($rules->isUnique(['dni'], ['allowMultipleNulls' => true]), ['errorField' => 'dni']);
        $rules->add($rules->existsIn(['paciente_id'], 'Pacientes'), ['errorField' => 'paciente_id']);
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
