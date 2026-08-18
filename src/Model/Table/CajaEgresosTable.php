<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class CajaEgresosTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('caja_egresos');

        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Cajas', [
            'foreignKey' => 'caja_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(
        Validator $validator
    ): Validator {

        $validator
            ->numeric('monto')
            ->greaterThan('monto', 0);

        $validator
            ->scalar('descripcion')
            ->notEmptyString('descripcion');

        return $validator;
    }
}