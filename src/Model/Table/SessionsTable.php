<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * Sessions Table class
 * 
 * Tabla para almacenar sesiones de usuarios en la base de datos
 * Configurada en config/app.php para persistencia de sesiones
 * 
 * @property \App\Model\Table\UsersTable $Users
 */
class SessionsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('sessions');
        $this->setPrimaryKey('id');
    }
}
