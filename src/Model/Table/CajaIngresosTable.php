<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class CajaIngresosTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('caja_ingresos');

        $this->setPrimaryKey('id');

        $this->belongsTo('Cajas', [
            'foreignKey' => 'caja_id',
        ]);
    }
}