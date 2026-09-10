<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ProveedoresTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('proveedores');
        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Productos', [
            'foreignKey' => 'proveedor_id',
        ]);

        $this->hasMany('IngresosMercaderia', [
            'foreignKey' => 'proveedor_id',
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
            ->scalar('ruc')
            ->maxLength('ruc', 11)
            ->add('ruc', 'validFormat', [
                'rule' => ['custom', '/^\d{11}$/'],
                'message' => 'El RUC debe tener 11 dígitos.',
                'on' => function ($context) {
                    return !empty($context['data']['ruc']);
                },
            ])
            ->allowEmptyString('ruc');

        $validator
            ->scalar('direccion')
            ->maxLength('direccion', 255)
            ->allowEmptyString('direccion');

        $validator
            ->scalar('whatsapp')
            ->maxLength('whatsapp', 20)
            ->allowEmptyString('whatsapp');

        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->boolean('activo')
            ->notEmptyString('activo');

        return $validator;
    }
}
