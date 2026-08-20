<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class InvoicesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('invoices');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Pacientes', [
            'foreignKey' => 'paciente_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('HistoriasClinicas', [
            'foreignKey' => 'historia_clinica_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Doctores', [
            'foreignKey' => 'doctor_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Cajas', [
            'foreignKey' => 'caja_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('DailySummaries', [
            'foreignKey' => 'daily_summary_id',
            'joinType' => 'LEFT',
        ]);

        $this->hasMany('InvoiceItems', [
            'foreignKey' => 'invoice_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
        $this->hasMany('CajaMovimientos', [
            'foreignKey' => 'invoice_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
        $this->hasMany('PresupuestosInvoices', [
            'foreignKey' => 'invoice_id',
        ]);
        $this->hasMany('InvoiceDistribuciones', [
            'foreignKey' => 'invoice_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);

        // Agregar  hasMany a la nueva tabal creada para las cuotas de la factura
        $this->hasMany('InvoiceCuotas', [
            'foreignKey' => 'invoice_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);

    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('company_id')
            ->requirePresence('company_id', 'create')
            ->notEmptyString('company_id');

        $validator
            ->integer('paciente_id')
            ->allowEmptyString('paciente_id');

        $validator
            ->integer('historia_clinica_id')
            ->allowEmptyString('historia_clinica_id');

        $validator
            ->scalar('tipo_doc')
            ->maxLength('tipo_doc', 2)
            ->requirePresence('tipo_doc', 'create')
            ->notEmptyString('tipo_doc');

        $validator
            ->scalar('serie')
            ->maxLength('serie', 10)
            ->allowEmptyString('serie', null, function ($context) {
                return ($context['data']['estado'] ?? null) === 'RECIBO_INTERNO';
            });

        $validator
            ->integer('correlativo')
            ->allowEmptyString('correlativo', null, function ($context) {
                return ($context['data']['estado'] ?? null) === 'RECIBO_INTERNO';
            });

        $validator
            ->scalar('cliente_tipo_doc')
            ->maxLength('cliente_tipo_doc', 2)
            ->allowEmptyString('cliente_tipo_doc');

        $validator
            ->scalar('cliente_numero')
            ->maxLength('cliente_numero', 20)
            ->allowEmptyString('cliente_numero');

        $validator
            ->scalar('cliente_nombre')
            ->maxLength('cliente_nombre', 255)
            ->allowEmptyString('cliente_nombre');

        $validator
            ->scalar('cliente_direccion')
            ->maxLength('cliente_direccion', 255)
            ->allowEmptyString('cliente_direccion');

        $validator
            ->email('cliente_email')
            ->allowEmptyString('cliente_email');

        $validator
            ->decimal('subtotal')
            ->allowEmptyString('subtotal');

        $validator
            ->decimal('igv')
            ->allowEmptyString('igv');

        $validator
            ->decimal('total')
            ->allowEmptyString('total');

        $validator
            ->scalar('estado')
            ->maxLength('estado', 30)
            ->allowEmptyString('estado');

        $validator
            ->scalar('codigo_sunat')
            ->maxLength('codigo_sunat', 20)
            ->allowEmptyString('codigo_sunat');

        $validator
            ->allowEmptyString('descripcion_sunat');

        $validator
            ->scalar('xml_path')
            ->maxLength('xml_path', 255)
            ->allowEmptyString('xml_path');

        $validator
            ->scalar('cdr_path')
            ->maxLength('cdr_path', 255)
            ->allowEmptyString('cdr_path');

        $validator
            ->scalar('hash_xml')
            ->maxLength('hash_xml', 255)
            ->allowEmptyString('hash_xml');

        $validator
            ->integer('daily_summary_id')
            ->allowEmptyString('daily_summary_id');

        $validator
            ->dateTime('enviado_sunat_at')
            ->allowEmptyDateTime('enviado_sunat_at');

        $validator
            ->integer('cliente_facturacion_id')
            ->allowEmptyString('cliente_facturacion_id');

        $validator
            ->scalar('pdf_path')
            ->maxLength('pdf_path', 255)
            ->allowEmptyString('pdf_path');


        $validator
            ->integer('doctor_id')
            ->allowEmptyString('doctor_id');

        $validator
            ->integer('user_id')
            ->allowEmptyString('user_id');

        // Agregar en validationDefault(), junto a la validación de 'estado':
        $validator
            ->scalar('forma_pago')
            ->maxLength('forma_pago', 10)
            ->allowEmptyString('forma_pago') // ya tiene DEFAULT 'CONTADO' en BD
            ->add('forma_pago', 'inList', [
                'rule' => ['inList', ['CONTADO', 'CREDITO']],
                'message' => 'Forma de pago inválida.',
            ]);            
        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['company_id'], 'Companies'), ['errorField' => 'company_id']);
        $rules->add(function ($entity, $options) {
    if (empty($entity->serie) || empty($entity->correlativo)) {
        return true;
    }

    $conditions = [
        'company_id' => $entity->company_id,
        'serie' => $entity->serie,
        'correlativo' => $entity->correlativo,
    ];

    if (!empty($entity->id)) {
        $conditions['id !='] = $entity->id;
    }

    return !$this->exists($conditions);
}, 'uniqueSerieCorrelativo', [
    'errorField' => 'correlativo',
    'message' => 'Ya existe un comprobante con la misma serie y correlativo para esta empresa.',
]);
        $rules->add(function ($entity) {
            if (empty($entity->paciente_id)) {
                return true;
            }
            return $this->Pacientes->exists(['id' => $entity->paciente_id]);
        }, 'existsPaciente', [
            'errorField' => 'paciente_id',
            'message' => 'El paciente no existe.',
        ]);

        $rules->add(function ($entity) {
            if (empty($entity->historia_clinica_id)) {
                return true;
            }
            return $this->HistoriasClinicas->exists(['id' => $entity->historia_clinica_id]);
        }, 'existsHistoriaClinica', [
            'errorField' => 'historia_clinica_id',
            'message' => 'La historia clínica no existe.',
        ]);

        $rules->add(function ($entity) {
            if (empty($entity->daily_summary_id)) {
                return true;
            }
            return $this->DailySummaries->exists(['id' => $entity->daily_summary_id]);
        }, 'existsDailySummary', [
            'errorField' => 'daily_summary_id',
            'message' => 'El resumen diario no existe.',
        ]);

        $rules->add(function ($entity) {
            if (empty($entity->doctor_id)) {
                return true;
            }
            return $this->Doctores->exists(['id' => $entity->doctor_id]);
        }, 'existsDoctore', [
            'errorField' => 'doctor_id',
            'message' => 'El doctor no existe.',
        ]);

        return $rules;
    }
}