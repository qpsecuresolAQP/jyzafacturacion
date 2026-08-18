<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class InvoiceItemsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('invoice_items');
        $this->setDisplayField('descripcion');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Invoices', [
            'foreignKey' => 'invoice_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Productos', [
            'foreignKey' => 'producto_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Tratamientos', [
            'foreignKey' => 'tratamiento_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Examenes', [
            'foreignKey' => 'examen_id',
            'joinType' => 'LEFT',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('invoice_id')
            ->requirePresence('invoice_id', 'create')
            ->notEmptyString('invoice_id');

        $validator
            ->integer('tratamiento_id')
            ->allowEmptyString('tratamiento_id');

        $validator
            ->integer('examen_id')
            ->allowEmptyString('examen_id');

        $validator
            ->scalar('descripcion')
            ->maxLength('descripcion', 255)
            ->requirePresence('descripcion', 'create')
            ->notEmptyString('descripcion');

        $validator
            ->decimal('cantidad')
            ->requirePresence('cantidad', 'create')
            ->notEmptyString('cantidad');

        $validator
            ->decimal('valor_unitario')
            ->requirePresence('valor_unitario', 'create')
            ->notEmptyString('valor_unitario');

        $validator
            ->decimal('precio_unitario')
            ->requirePresence('precio_unitario', 'create')
            ->notEmptyString('precio_unitario');

        $validator
            ->decimal('base_igv')
            ->requirePresence('base_igv', 'create')
            ->notEmptyString('base_igv');

        $validator
            ->scalar('tipo_item')
            ->requirePresence('tipo_item', 'create')
            ->notEmptyString('tipo_item');
            
        $validator
            ->decimal('igv')
            ->requirePresence('igv', 'create')
            ->notEmptyString('igv');

        $validator
            ->decimal('total')
            ->requirePresence('total', 'create')
            ->notEmptyString('total');

        $validator
            ->scalar('codigo_producto')
            ->maxLength('codigo_producto', 50)
            ->allowEmptyString('codigo_producto');

        $validator
            ->scalar('unidad')
            ->maxLength('unidad', 10)
            ->allowEmptyString('unidad');

        return $validator;
    }

   public function buildRules(RulesChecker $rules): RulesChecker
{
    $rules->add($rules->existsIn(['invoice_id'], 'Invoices'), [
        'errorField' => 'invoice_id'
    ]);

    $rules->add(function ($entity) {
        if (empty($entity->tratamiento_id)) {
            return true;
        }
        return $this->Tratamientos->exists(['id' => $entity->tratamiento_id]);
    }, 'existsTratamiento', [
        'errorField' => 'tratamiento_id',
        'message' => 'El tratamiento no existe.',
    ]);

    $rules->add(function ($entity) {
        if ($entity->tipo_item === 'producto' && empty($entity->producto_id)) {
            return false;
        }
        return true;
    }, 'productoRequired', [
        'errorField' => 'producto_id',
        'message' => 'Debe seleccionar un producto.',
    ]);

    $rules->add(function ($entity) {
        if (empty($entity->producto_id)) {
            return true;
        }
        return $this->Productos->exists(['id' => $entity->producto_id]);
    }, 'existsProducto', [
        'errorField' => 'producto_id',
        'message' => 'El producto no existe.',
    ]);

    $rules->add(function ($entity) {
        if ($entity->tipo_item === 'examen' && empty($entity->examen_id)) {
            return false;
        }
        return true;
    }, 'examenRequired', [
        'errorField' => 'examen_id',
        'message' => 'Debe seleccionar un examen.',
    ]);

    $rules->add(function ($entity) {
        if (empty($entity->examen_id)) {
            return true;
        }
        return $this->Examenes->exists(['id' => $entity->examen_id]);
    }, 'existsExamen', [
        'errorField' => 'examen_id',
        'message' => 'El examen no existe.',
    ]);

    return $rules;
}
}