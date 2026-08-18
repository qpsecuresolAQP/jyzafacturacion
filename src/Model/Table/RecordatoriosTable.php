<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Recordatorios Model
 *
 * @property \App\Model\Table\PacientesTable&\Cake\ORM\Association\BelongsTo $Pacientes
 * @property \App\Model\Table\RecordatorioControlesTable&\Cake\ORM\Association\HasMany $RecordatorioControles
 */
class RecordatoriosTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('recordatorio');
        $this->setDisplayField('titulo');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Pacientes', [
            'foreignKey' => 'paciente_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('RecordatorioControles', [
            'foreignKey' => 'recordatorio_id',
            'className' => 'RecordatorioControles',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('paciente_id')
            ->requirePresence('paciente_id', 'create')
            ->notEmptyString('paciente_id', 'El paciente es requerido');

        $validator
            ->scalar('titulo')
            ->maxLength('titulo', 255)
            ->requirePresence('titulo', 'create')
            ->notEmptyString('titulo', 'El título del control es requerido');

        $validator
            ->date('fecha_inicio')
            ->requirePresence('fecha_inicio', 'create')
            ->notEmptyDate('fecha_inicio', 'La fecha de inicio es requerida');

        $validator
            ->scalar('duracion_estimada')
            ->maxLength('duracion_estimada', 100)
            ->allowEmptyString('duracion_estimada');

        $validator
            ->scalar('observacion')
            ->allowEmptyString('observacion');

        $validator
            ->scalar('estado_control')
            ->maxLength('estado_control', 1)
            ->allowEmptyString('estado_control');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['paciente_id'], 'Pacientes'), ['errorField' => 'paciente_id']);

        return $rules;
    }
}