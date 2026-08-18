<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Recetas Model
 *
 * @property \App\Model\Table\ConsultasTable&\Cake\ORM\Association\BelongsToMany $Consultas
 * @property \App\Model\Table\MedicamentosTable&\Cake\ORM\Association\BelongsToMany $Medicamentos
 * @property \App\Model\Table\RecetasMedicamentosTable&\Cake\ORM\Association\HasMany $RecetasMedicamentos
 *
 * @method \App\Model\Entity\Receta newEmptyEntity()
 * @method \App\Model\Entity\Receta newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Receta> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Receta get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Receta findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Receta patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Receta> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Receta|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Receta saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Receta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Receta>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Receta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Receta> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Receta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Receta>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Receta>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Receta> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RecetasTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('recetas');
        $this->setDisplayField('nombre');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('Consultas', [
            'through' => 'RecetasConsultas',
            'foreignKey' => 'receta_id',
            'targetForeignKey' => 'consulta_id'
        ]);
        $this->belongsToMany('Medicamentos', [
            'through' => 'RecetasMedicamentos',
            'foreignKey' => 'receta_id',
            'targetForeignKey' => 'medicamento_id',
        ]);
        $this->hasMany('RecetasConsultas', [
            'foreignKey' => 'receta_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
        $this->hasMany('RecetasMedicamentos', [
            'foreignKey' => 'receta_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('nombre')
            ->maxLength('nombre', 300)
            ->allowEmptyString('nombre');

        $validator
            ->scalar('descripcion')
            ->allowEmptyString('descripcion');

        $validator
            ->scalar('notas')
            ->allowEmptyString('notas');

        $validator
            ->scalar('indicaciones_consolidadas')
            ->allowEmptyString('indicaciones_consolidadas');

        $validator
            ->scalar('tipo_usuario')
            ->maxLength('tipo_usuario', 50)
            ->allowEmptyString('tipo_usuario');

        $validator
            ->scalar('tipo_atencion')
            ->maxLength('tipo_atencion', 50)
            ->allowEmptyString('tipo_atencion');

        $validator
            ->scalar('especialidad_medica')
            ->maxLength('especialidad_medica', 50)
            ->allowEmptyString('especialidad_medica');

        $validator
            ->scalar('h_cl')
            ->maxLength('h_cl', 100)
            ->allowEmptyString('h_cl');

        $validator
            ->scalar('sis')
            ->maxLength('sis', 100)
            ->allowEmptyString('sis');

        $validator
            ->scalar('particular')
            ->maxLength('particular', 100)
            ->allowEmptyString('particular');

        $validator
            ->decimal('peso')
            ->allowEmptyString('peso');

        return $validator;
    }
}
