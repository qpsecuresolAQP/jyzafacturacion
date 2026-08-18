<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class CategoriasProductosTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('categorias_productos');
        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Productos', [
            'foreignKey' => 'categoria_producto_id',
        ]);
    }
}