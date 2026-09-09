<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ProductosTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('productos');
        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('CategoriasProductos', [
            'foreignKey' => 'categoria_producto_id',
        ]);

        $this->belongsTo('Proveedores', [
            'foreignKey' => 'proveedor_id',
            'joinType' => 'LEFT',
            'propertyName' => 'proveedor',
        ]);

        $this->hasMany('InvoiceItems', [
            'foreignKey' => 'producto_id',
        ]);

        $this->hasMany('ProductoMovimientos', [
            'foreignKey' => 'producto_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('nombre')
            ->maxLength('nombre', 150)
            ->requirePresence('nombre', 'create')
            ->notEmptyString('nombre');

        $validator
            ->decimal('precio')
            ->requirePresence('precio', 'create')
            ->notEmptyString('precio');

        $validator
            ->decimal('precio_compra')
            ->requirePresence('precio_compra', 'create')
            ->notEmptyString('precio_compra');

        $validator
            ->decimal('stock')
            ->requirePresence('stock', 'create')
            ->notEmptyString('stock');

        $validator
            ->integer('proveedor_id')
            ->allowEmptyString('proveedor_id');

        $validator
            ->boolean('desactivado_por_categoria')
            ->allowEmptyString('desactivado_por_categoria');

        return $validator;
    }
}