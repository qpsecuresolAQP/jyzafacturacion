<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class VistaReporteProductosTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('vista_reporte_productos');
        $this->setPrimaryKey('producto_id');
        $this->setEntityClass('App\Model\Entity\VistaReporteProducto');
    }
}
