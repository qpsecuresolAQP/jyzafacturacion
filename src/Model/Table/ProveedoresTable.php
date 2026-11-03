<?php
declare(strict_types=1);

namespace App\Model\Table;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
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

    /**
     * Mantiene `ruc` y tipo/número de documento en sincronía: el formulario de Proveedores
     * solo envía `ruc`, y la búsqueda por documento de Ingreso de Mercadería envía tipo/número.
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        $ruc = trim((string)$entity->get('ruc'));
        $numero = trim((string)$entity->get('numero_documento'));

        if ($entity->isDirty('ruc') && $ruc !== '') {
            $entity->set('tipo_documento', '6');
            $entity->set('numero_documento', $ruc);
        } elseif ($entity->get('tipo_documento') === '6' && $numero !== '') {
            $entity->set('ruc', $numero);
        }
    }
}
