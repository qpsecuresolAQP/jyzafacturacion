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
 * @property \App\Model\Table\DepartamentosTable&\Cake\ORM\Association\BelongsTo $Departamentos
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\DocumentosTable&\Cake\ORM\Association\HasMany $Documentos
 * @property \App\Model\Table\ConsultasTable&\Cake\ORM\Association\HasMany $Consultas
 * @property \App\Model\Table\ProcedimientosTable&\Cake\ORM\Association\HasMany $Procedimientos
 * @property \App\Model\Table\PresupuestosTable&\Cake\ORM\Association\HasMany $Presupuestos
 * @property \App\Model\Table\OrdenesTable&\Cake\ORM\Association\HasMany $Ordenes
 * @property \App\Model\Table\FichasFacialesTable&\Cake\ORM\Association\HasMany $FichasFaciales
 * @property \App\Model\Table\FichasProfesionalesTable&\Cake\ORM\Association\HasMany $FichasProfesionales
 * @property \App\Model\Table\FichasCosmetrariasCoralesTable&\Cake\ORM\Association\HasMany $FichasCosmetrariasCorales
 * @property \App\Model\Table\FichasMicropigmentacionTable&\Cake\ORM\Association\HasMany $FichasMicropigmentacion
 * @property \App\Model\Table\FichasLiftingTable&\Cake\ORM\Association\HasMany $FichasLifting
 * @property \App\Model\Table\PaquetesPagosTable&\Cake\ORM\Association\HasMany $PaquetesPagos
 * @property \App\Model\Table\ExamenesFisicosTable&\Cake\ORM\Association\BelongsToMany $ExamenesFisicos
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
        $this->belongsTo('Departamentos', [
            'foreignKey' => 'departamento_id',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);
        // Relación correcta con Documentos (una historia tiene muchos documentos)
        $this->hasMany('Documentos', [
            'foreignKey' => 'historia_id',
            'dependent' => true,
        ]);

        // Relación con Consultas (una historia tiene muchas consultas)
        $this->hasMany('Consultas', [
            'foreignKey' => 'historia_id',
            'dependent' => true,
        ]);
        // Relación con Procedimientos (una historia tiene muchas procedimientos)
        $this->hasMany('Procedimientos', [
            'foreignKey' => 'historia_id',
            'dependent' => true,
        ]);
        $this->hasMany('Presupuestos', [
            'foreignKey' => 'historia_id',
            'dependent' => true,
        ]);
        $this->hasMany('Ordenes', [
            'foreignKey' => 'historia_id',
            'dependent' => true,
        ]);
        $this->hasMany('FichasFaciales', [
            'foreignKey' => 'historia_clinica_id',
            'dependent' => true,
        ]);
        $this->hasMany('FichasProfesionales', [
            'foreignKey' => 'historia_clinica_id',
            'dependent' => true,
        ]);
        $this->hasMany('FichasCosmetrariasCorales', [
            'foreignKey' => 'historia_clinica_id',
            'dependent' => true,
        ]);
        $this->hasMany('FichasMicropigmentacion', [
            'foreignKey' => 'historia_clinica_id',
            'dependent' => true,
        ]);
        $this->hasMany('FichasLifting', [
            'foreignKey' => 'historia_clinica_id',
            'dependent' => true,
        ]);
        $this->hasMany('PaquetesPagos', [
            'foreignKey' => 'historia_id',
            'dependent' => true,
        ]);
        $this->belongsToMany('ExamenesFisicos', [
            'through' => 'FisicosHistorias', // nombre de la tabla intermedia
            'foreignKey' => 'historia_id', // clave foránea en la tabla intermedia que apunta a HistoriasClinicas
            'targetForeignKey' => 'examen_id', // clave foránea en la tabla intermedia que apunta a ExamenesFisicos
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
            ->scalar('medicacion')
            ->allowEmptyString('medicacion');

        $validator
            ->scalar('alergias')
            ->allowEmptyString('alergias');

        $validator
            ->scalar('enfermedades')
            ->allowEmptyString('enfermedades');

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
        $rules->add($rules->existsIn(['departamento_id'], 'Departamentos'), ['errorField' => 'departamento_id']);
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
