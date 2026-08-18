<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RecordatorioControles Model
 *
 * @property \App\Model\Table\RecordatoriosTable&\Cake\ORM\Association\BelongsTo $Recordatorios
 */
class RecordatorioControlesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('recordatorio_control');
        $this->setDisplayField('fecha_control');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Recordatorios', [
            'foreignKey' => 'recordatorio_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('recordatorio_id')
            ->requirePresence('recordatorio_id', 'create')
            ->notEmptyString('recordatorio_id', 'El recordatorio es requerido');

        $validator
            ->date('fecha_control')
            ->requirePresence('fecha_control', 'create')
            ->notEmptyDate('fecha_control', 'La fecha del control es requerida');

        $validator
            ->date('proximo_control')
            ->allowEmptyDate('proximo_control');

        $validator
            ->scalar('detalles')
            ->allowEmptyString('detalles');

        $validator
            ->scalar('productos_utilizados')
            ->allowEmptyString('productos_utilizados');

        $validator
            ->scalar('observaciones')
            ->allowEmptyString('observaciones');

        $validator
            ->boolean('recordatorio_enviado')
            ->allowEmptyString('recordatorio_enviado');

        $validator
            ->scalar('estado')
            ->maxLength('estado', 1)
            ->allowEmptyString('estado');

        $validator
            ->scalar('estado_control')
            ->maxLength('estado_control', 1)
            ->allowEmptyString('estado_control');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['recordatorio_id'], 'Recordatorios'), ['errorField' => 'recordatorio_id']);

        return $rules;
    }
}