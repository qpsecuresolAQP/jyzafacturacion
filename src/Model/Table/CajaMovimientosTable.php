<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CajaMovimientosTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('caja_movimientos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Cajas', [
            'foreignKey' => 'caja_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Invoices', [
            'foreignKey' => 'invoice_id',
            'joinType' => 'LEFT',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('caja_id')
            ->requirePresence('caja_id', 'create')
            ->notEmptyString('caja_id');

        $validator
            ->integer('invoice_id')
            ->requirePresence('invoice_id', 'create')
            ->notEmptyString('invoice_id');

        $validator
            ->scalar('metodo_pago')
            ->maxLength('metodo_pago', 20)
            ->requirePresence('metodo_pago', 'create')
            ->notEmptyString('metodo_pago')
            ->add('metodo_pago', 'inList', [
                'rule' => ['inList', ['EFECTIVO',
                    'YAPE',
                    'TARJETA',
                    'TRANSFERENCIA',
                    'PLIN',
                    'OTROS']],
                'message' => 'Método de pago inválido.',
            ]);

        $validator
            ->decimal('monto_total')
            ->requirePresence('monto_total', 'create')
            ->notEmptyString('monto_total')
            ->greaterThan('monto_total', 0, 'El monto total debe ser mayor a 0.');

        $validator
            ->decimal('monto_recibido')
            ->requirePresence('monto_recibido', 'create')
            ->notEmptyString('monto_recibido')
            ->greaterThanOrEqual('monto_recibido', 0, 'El monto recibido no puede ser negativo.');

        $validator
            ->decimal('vuelto')
            ->requirePresence('vuelto', 'create')
            ->notEmptyString('vuelto')
            ->greaterThanOrEqual('vuelto', 0, 'El vuelto no puede ser negativo.');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['caja_id'], 'Cajas'), ['errorField' => 'caja_id']);
        $rules->add($rules->existsIn(['invoice_id'], 'Invoices'), ['errorField' => 'invoice_id']);

        // Removida restricción de unicidad para permitir múltiples pagos por invoice
        // (sistema híbrido de múltiples métodos de pago)

        return $rules;
    }
}