<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class IngresosMercaderiaTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('ingresos_mercaderia');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Proveedores', [
            'foreignKey' => 'proveedor_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'usuario_id',
        ]);

        $this->hasMany('IngresosMercaderiaDetalle', [
            'foreignKey' => 'ingreso_mercaderia_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);

        $this->hasMany('ProductoMovimientos', [
            'foreignKey' => 'ingreso_mercaderia_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('proveedor_id')
            ->requirePresence('proveedor_id', 'create')
            ->notEmptyString('proveedor_id', 'Selecciona un proveedor.');

        $validator
            ->scalar('almacen')
            ->maxLength('almacen', 100)
            ->notEmptyString('almacen');

        $validator
            ->inList('tipo_doc', ['FACTURA', 'BOLETA', 'GUIA', 'OTRO']);

        $validator
            ->scalar('serie')
            ->maxLength('serie', 20)
            ->allowEmptyString('serie');

        $validator
            ->scalar('numero')
            ->maxLength('numero', 30)
            ->allowEmptyString('numero');

        $validator
            ->inList('moneda', ['SOLES', 'DOLARES']);

        $validator
            ->dateTime('fecha_ingreso')
            ->requirePresence('fecha_ingreso', 'create')
            ->notEmptyDateTime('fecha_ingreso', 'Indica la fecha de ingreso.');

        $validator
            ->date('fecha_factura')
            ->allowEmptyDate('fecha_factura');

        $validator
            ->scalar('observacion')
            ->allowEmptyString('observacion');

        return $validator;
    }
}
