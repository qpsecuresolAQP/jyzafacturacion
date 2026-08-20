<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PagosLaboratoriosHistorialDistribucionesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('pagos_laboratorios_historial_distribuciones');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('PagosLaboratoriosHistorial', [
            'foreignKey' => 'pago_historial_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('InvoiceDistribuciones', [
            'foreignKey' => 'invoice_distribucion_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Invoices', [
            'foreignKey' => 'invoice_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('pago_historial_id')
            ->requirePresence('pago_historial_id', 'create')
            ->notEmptyString('pago_historial_id');

        $validator
            ->integer('invoice_distribucion_id')
            ->requirePresence('invoice_distribucion_id', 'create')
            ->notEmptyString('invoice_distribucion_id');

        $validator
            ->integer('invoice_id')
            ->requirePresence('invoice_id', 'create')
            ->notEmptyString('invoice_id');

        $validator
            ->decimal('monto_pagado')
            ->requirePresence('monto_pagado', 'create')
            ->notEmptyString('monto_pagado');

        $validator
            ->scalar('conceptos')
            ->allowEmptyString('conceptos');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['pago_historial_id'], 'PagosLaboratoriosHistorial'), ['errorField' => 'pago_historial_id']);
        $rules->add($rules->existsIn(['invoice_distribucion_id'], 'InvoiceDistribuciones'), ['errorField' => 'invoice_distribucion_id']);
        $rules->add($rules->existsIn(['invoice_id'], 'Invoices'), ['errorField' => 'invoice_id']);
        $rules->add(
            $rules->isUnique(['invoice_distribucion_id']),
            ['errorField' => 'invoice_distribucion_id', 'message' => 'Esta distribución ya fue pagada al laboratorio anteriormente.']
        );

        return $rules;
    }
}
