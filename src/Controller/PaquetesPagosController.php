<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * PaquetesPagos Controller
 *
 * @property \App\Model\Table\PaquetesPagosTable $PaquetesPagos
 */
class PaquetesPagosController extends AppController
{
    /**
     * Verificación centralizada de permisos
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        $currentAction = $this->request->getParam('action');

        // Permitir búsqueda de paquetes
        if (in_array($currentAction, ['buscarPaquetes', 'marcarPagado'])) {
            return;
        }

        $this->verificarPermisoORedireccionar('PaquetesPagos', $currentAction);
    }

    /**
     * Index method - Listar paquetes de pago de un paciente/historia
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $historiaId = $this->request->getQuery('historia_id');

        $query = $this->PaquetesPagos->find()
            ->contain([
                'HistoriasClinicas' => ['Pacientes'],
                'Users',
                'PaquetesPagosCuotas' => function ($q) {
                    return $q->where([
                        'PaquetesPagosCuotas.estado_cuota' => 'A'
                    ]);
                }
            ])
            ->where([
                'PaquetesPagos.estado_paquete' => 'A'
            ]);

        if (!empty($historiaId)) {
            $query->where([
                'PaquetesPagos.historia_id' => $historiaId
            ]);
        }

        $paquetesPagos = $this->paginate($query);

        $this->set(compact('paquetesPagos'));
    }

    /**
     * View method - Ver detalles de un paquete de pago
     *
     * @param string|null $id Paquete Id.
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function view($id = null)
    {
        $paquetePago = $this->PaquetesPagos->get($id, contain: [
            'HistoriasClinicas' => ['Pacientes'],
            'Users',
            'PaquetesPagosCuotas'
        ]);

        $this->set(compact('paquetePago'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Add method - Crear nuevo paquete de pago
     * Maneja tanto contado como pago en partes con cuotas incluidas
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $paquetePago = $this->PaquetesPagos->newEmptyEntity();
        $historiaId = $this->request->getQuery('historia_id');

        // Si viene historia_id, asignarlo a la entidad para que se preseleccione en el form
        if (!empty($historiaId)) {
            $paquetePago->historia_id = $historiaId;
        }

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Si viene historia_id en query, agregarlo al POST
            if (!empty($historiaId)) {
                $data['historia_id'] = $historiaId;
            }

            // Agregar el usuario actual
            $data['user_id'] = $this->request->getAttribute('identity')->getIdentifier();

            // Asegurar estado por defecto
            if (empty($data['estado'])) {
                $data['estado'] = 'pendiente';
            }

            // Normalizar fecha_recordatorio: si es 'partes', usar la primera cuota; si 'contado', asegurar formato YYYY-MM-DD
            if (!empty($data['tipo_pago']) && $data['tipo_pago'] === 'partes') {
                if (!empty($data['cuotas']) && is_array($data['cuotas'])) {
                    // buscar la primera cuota con fecha
                    foreach ($data['cuotas'] as $c) {
                        if (!empty($c['fecha_recordatorio'])) {
                            $data['fecha_recordatorio'] = substr($c['fecha_recordatorio'], 0, 10); // YYYY-MM-DD
                            break;
                        }
                    }
                }
            } else {
                // Para contado, asegurar formato YYYY-MM-DD (sin hora)
                if (!empty($data['fecha_recordatorio'])) {
                    try {
                        $dt = new \DateTime($data['fecha_recordatorio']);
                        $data['fecha_recordatorio'] = $dt->format('Y-m-d');
                    } catch (\Exception $e) {
                        // dejar como viene
                    }
                }
            }

            $paquetePago = $this->PaquetesPagos->patchEntity($paquetePago, $data);
            // Establecer como activo si es nuevo
            if ($paquetePago->isNew()) {
                $paquetePago->estado_paquete = 'A';
            }

            if ($this->PaquetesPagos->save($paquetePago)) {
                // Si es pago en partes, guardar cuotas
                if ($paquetePago->tipo_pago === 'partes' && !empty($data['cuotas'])) {
                    $PaquetesPagosCuotasTable = TableRegistry::getTableLocator()->get('PaquetesPagosCuotas');
                    $errorCuotas = false;
                    $totalPagado = 0;

                    try {
                        foreach ($data['cuotas'] as $cuota) {
                            if (!empty($cuota['monto']) && !empty($cuota['fecha_recordatorio'])) {
                                // Normalizar monto a float primero
                                $monto = floatval(str_replace(',', '.', $cuota['monto']));

                                $entity = $PaquetesPagosCuotasTable->newEntity([
                                    'paquete_pago_id' => $paquetePago->id,
                                    'titulo_referencial' => $cuota['titulo_referencial'] ?? '',
                                    'monto' => $monto,
                                    'fecha_recordatorio' => $cuota['fecha_recordatorio'],
                                    'estado' => 'pendiente',
                                    'estado_cuota' => 'A'  // Marcar como activo
                                ]);

                                if (!$PaquetesPagosCuotasTable->save($entity)) {
                                    $errors = $entity->getErrors();
                                    throw new \Exception('Error al guardar cuota: ' . json_encode($errors));
                                }

                                $totalPagado += $monto;
                            }
                        }

                        $this->Flash->success(__('El paquete de pago y sus cuotas han sido guardados.'));
                    } catch (\Exception $e) {
                        // En caso de error, eliminar las cuotas que pudieron haberse creado y redirigir a editar
                        try {
                            $PaquetesPagosCuotasTable->deleteAll(['paquete_pago_id' => $paquetePago->id]);
                        } catch (\Exception $inner) {
                        }

                        $this->Flash->error(__('Error al guardar las cuotas: ' . $e->getMessage()));
                        return $this->redirect(['action' => 'edit', $paquetePago->id]);
                    }
                } else {
                    $this->Flash->success(__('El paquete de pago ha sido guardado.'));
                }

                // Obtener paciente para redirigir a su view
                $pacienteId = null;
                try {
                    $historia = $this->PaquetesPagos->HistoriasClinicas->get($paquetePago->historia_id, contain: ['Pacientes']);
                    $pacienteId = $historia->paciente->id ?? $historia->paciente_id;
                } catch (\Exception $e) {
                    // ignorar
                }

                $redirectUrl = Router::url(['controller' => 'Pacientes', 'action' => 'view', $pacienteId]) . '#recordatorios-pago';

                if ($this->request->is('ajax')) {
                    $this->set(compact('redirectUrl'));
                    $this->viewBuilder()->setOption('serialize', ['redirectUrl']);
                    return;
                }

                return $this->redirect(['controller' => 'Pacientes', 'action' => 'view', $pacienteId, '#' => 'recordatorios-pago']);
            }

            // Mostrar errores de validación si existen
            $errors = $paquetePago->getErrors();
            if (!empty($errors)) {
                $messages = [];
                array_walk_recursive($errors, function ($v) use (&$messages) {
                    $messages[] = $v;
                });
                $this->Flash->error(__('No se pudo guardar el paquete de pago: ') . implode(' | ', $messages));
            } else {
                $this->Flash->error(__('No se pudo guardar el paquete de pago. Por favor intenta de nuevo.'));
            }
        }

        $historiasClinicas = $this->PaquetesPagos->HistoriasClinicas->find('all', contain: ['Pacientes'])->toArray();
        $this->set(compact('paquetePago', 'historiaId', 'historiasClinicas'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Edit method - Editar paquete de pago existente
     *
     * @param string|null $id Paquete Id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     */
    public function edit($id = null)
    {
        $paquetePago = $this->PaquetesPagos->get($id, contain: [
            'PaquetesPagosCuotas' => [
                'conditions' => ['PaquetesPagosCuotas.estado_cuota' => 'A']  // Solo cuotas activas
            ]
        ]);

        // Verificar que el paquete esté activo
        if ($paquetePago->estado_paquete == 'I') {
            throw new \Cake\Http\Exception\NotFoundException('Paquete no encontrado');
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Asegurar estado por defecto
            if (empty($data['estado'])) {
                $data['estado'] = $paquetePago->estado ?? 'pendiente';
            }

            // Normalizar fecha_recordatorio (date format: YYYY-MM-DD)
            if (!empty($data['tipo_pago']) && $data['tipo_pago'] === 'partes') {
                if (!empty($data['cuotas']) && is_array($data['cuotas'])) {
                    foreach ($data['cuotas'] as $c) {
                        if (!empty($c['fecha_recordatorio'])) {
                            $data['fecha_recordatorio'] = substr($c['fecha_recordatorio'], 0, 10);
                            break;
                        }
                    }
                }
            } else {
                if (!empty($data['fecha_recordatorio'])) {
                    try {
                        $dt = new \DateTime($data['fecha_recordatorio']);
                        $data['fecha_recordatorio'] = $dt->format('Y-m-d');
                    } catch (\Exception $e) {
                        // dejar como viene
                    }
                }
            }

            $paquetePago = $this->PaquetesPagos->patchEntity($paquetePago, $data);

            if ($this->PaquetesPagos->save($paquetePago)) {
                $this->Flash->success(__('El paquete de pago ha sido actualizado.'));

                // Procesar cuotas si el tipo de pago es 'partes'
                $data = $this->request->getData();
                $PaquetesPagosCuotasTable = TableRegistry::getTableLocator()->get('PaquetesPagosCuotas');
                if (!empty($data['tipo_pago']) && $data['tipo_pago'] === 'partes') {
                    // Reemplazar las cuotas existentes por las nuevas enviadas
                    try {
                        // eliminar existentes
                        $PaquetesPagosCuotasTable->deleteAll(['paquete_pago_id' => $paquetePago->id]);

                        $totalPagado = 0;
                        $createdAny = false;
                        if (!empty($data['cuotas']) && is_array($data['cuotas'])) {
                            foreach ($data['cuotas'] as $cuota) {
                                if (!empty($cuota['monto']) && !empty($cuota['fecha_recordatorio'])) {
                                    $monto = floatval(str_replace(',', '.', $cuota['monto']));
                                    $entity = $PaquetesPagosCuotasTable->newEntity([
                                        'paquete_pago_id' => $paquetePago->id,
                                        'titulo_referencial' => $cuota['titulo_referencial'] ?? '',
                                        'monto' => $monto,
                                        'fecha_recordatorio' => $cuota['fecha_recordatorio'],
                                        'estado' => $cuota['estado'] ?? 'pendiente',
                                    ]);

                                    if (!$PaquetesPagosCuotasTable->save($entity)) {
                                        $errors = $entity->getErrors();
                                        throw new \Exception('Error al guardar cuota: ' . json_encode($errors));
                                    }

                                    $totalPagado += $monto;
                                    $createdAny = true;
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        try {
                            $PaquetesPagosCuotasTable->deleteAll(['paquete_pago_id' => $paquetePago->id]);
                        } catch (\Exception $inner) {
                        }
                        $this->Flash->error(__('Error al procesar cuotas: ') . $e->getMessage());
                        return $this->redirect(['action' => 'edit', $paquetePago->id]);
                    }
                } else {
                    // Si cambiaron a contado, eliminar cuotas previas
                    try {
                        $PaquetesPagosCuotasTable->deleteAll(['paquete_pago_id' => $paquetePago->id]);
                    } catch (\Exception $e) {
                        // ignore
                    }
                }

                // Obtener paciente para redirigir a su view
                $pacienteId = null;
                try {
                    $historia = $this->PaquetesPagos->HistoriasClinicas->get($paquetePago->historia_id, contain: ['Pacientes']);
                    $pacienteId = $historia->paciente->id ?? $historia->paciente_id;
                } catch (\Exception $e) {
                    // ignorar
                }

                $redirectUrl = Router::url(['controller' => 'Pacientes', 'action' => 'view', $pacienteId]) . '#recordatorios-pago';

                if ($this->request->is('ajax')) {
                    $this->set(compact('redirectUrl'));
                    $this->viewBuilder()->setOption('serialize', ['redirectUrl']);
                    return;
                }

                return $this->redirect(['controller' => 'Pacientes', 'action' => 'view', $pacienteId, '#' => 'recordatorios-pago']);
            }

            // Mostrar errores de validación si existen
            $errors = $paquetePago->getErrors();
            if (!empty($errors)) {
                $messages = [];
                array_walk_recursive($errors, function ($v) use (&$messages) {
                    $messages[] = $v;
                });
                $this->Flash->error(__('No se pudo actualizar el paquete de pago: ') . implode(' | ', $messages));
            } else {
                $this->Flash->error(__('No se pudo actualizar el paquete de pago. Por favor intenta de nuevo.'));
            }
        }

        $historiasClinicas = $this->PaquetesPagos->HistoriasClinicas->find('all', contain: ['Pacientes'])->toArray();
        $this->set(compact('paquetePago', 'historiasClinicas'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Delete method - Eliminar paquete de pago
     *
     * @param string|null $id Paquete Id.
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $paquetePago = $this->PaquetesPagos->get($id);

        // Desactivar paquete
        $paquetePago->estado_paquete = 'I';

        if ($this->PaquetesPagos->save($paquetePago)) {

            // Desactivar cuotas asociadas
            $cuotasTable = $this->fetchTable('PaquetesPagosCuotas');

            $cuotasTable->updateAll(
                ['estado_cuota' => 'I'],
                ['paquete_pago_id' => $id]
            );

            $this->Flash->success(__('El paquete de pago ha sido eliminado.'));
        } else {
            $this->Flash->error(__('No se pudo eliminar el paquete de pago.'));
        }

        // Redirigir al paciente
        $pacienteId = null;

        try {
            $historia = $this->PaquetesPagos
                ->HistoriasClinicas
                ->get($paquetePago->historia_id);

            $pacienteId = $historia->paciente_id;
        } catch (\Exception $e) {
        }

        return $this->redirect([
            'controller' => 'Pacientes',
            'action' => 'view',
            $pacienteId,
            '#' => 'recordatorios-pago'
        ]);
    }

    /**
     * Registrar Pago method - Marcar una cuota como pagada
     *
     * @param string|null $cuotaId Cuota Id.
     * @return \Cake\Http\Response|null Redirects to referrer.
     */
    public function registrarPago($cuotaId = null)
    {
        $cuotasTable = $this->fetchTable('PaquetesPagosCuotas');

        $cuota = $cuotasTable->get($cuotaId);

        $cuota->estado = 'pagado';
        $cuota->fecha_pago = new \DateTime();

        if ($cuotasTable->save($cuota)) {

            $paquete = $this->fetchTable('PaquetesPagos')->get($cuota->paquete_pago_id);

            $historia = $this->fetchTable('HistoriasClinicas')->get($paquete->historia_id);

            $this->Flash->success('Pago registrado correctamente.');

            return $this->redirect([
                'controller' => 'Pacientes',
                'action' => 'view',
                $historia->paciente_id,
                '#' => 'recordatorios-pago'
            ]);
        }

        $this->Flash->error('No se pudo registrar el pago.');

        return $this->redirect($this->referer());
    }

    /**
     * Buscar Paquetes method - Búsqueda AJAX para seleccionar paquetes
     *
     * @return void
     */
    public function buscarPaquetes()
    {
        $this->request->allowMethod(['get']);

        $term = $this->request->getQuery('term', '');
        $historiaId = $this->request->getQuery('historia_id');

        $query = $this->PaquetesPagos->find()
            ->select(['id', 'nombre', 'num_sesiones', 'precio_total'])
            ->where([
                'PaquetesPagos.nombre LIKE' => '%' . $term . '%',
                'PaquetesPagos.estado_paquete' => 'A',  // Solo activos
            ]);
    }

    /**
     * Recordatorios de Pago method - Obtener recordatorios para Home/Dashboard
     * Retorna paquetes contado vencidos/proximos y cuotas pendientes
     *
     * @return void
     */
    public function recordatoriosPago()
    {
        $this->request->allowMethod(['get']);

        $hoy = new \DateTime();
        $proximosDias = (clone $hoy)->modify('+7 days');

        // Recordatorios de pago al contado (pendientes y proximos a vencer)
        $paquetesContado = $this->PaquetesPagos->find()
            ->contain([
                'HistoriasClinicas' => ['Pacientes'],
                'Users'
            ])
            ->where([
                'PaquetesPagos.tipo_pago' => 'contado',
                'PaquetesPagos.estado IN' => ['pendiente', 'pagado_parcial'],
                'PaquetesPagos.estado_paquete' => 'A',
                'PaquetesPagos.fecha_recordatorio <=' => $proximosDias->format('Y-m-d H:i:s')
            ])
            ->order(['PaquetesPagos.fecha_recordatorio' => 'ASC'])
            ->toArray();

        // Recordatorios de pago en partes (cuotas pendientes proximas)
        $PaquetesPagosCuotasTable = TableRegistry::getTableLocator()->get('PaquetesPagosCuotas');
        $cuotasPendientes = $PaquetesPagosCuotasTable->find()
            ->contain([
                'PaquetesPagos' => [
                    'HistoriasClinicas' => ['Pacientes'],
                    'Users'
                ]
            ])
            ->where([
                'PaquetesPagosCuotas.estado' => 'pendiente',
                'PaquetesPagosCuotas.estado_cuota' => 'A',
                'PaquetesPagosCuotas.fecha_recordatorio <=' => $proximosDias->format('Y-m-d H:i:s')
            ])
            ->order(['PaquetesPagosCuotas.fecha_recordatorio' => 'ASC'])
            ->toArray();

        $recordatorios = [
            'contado' => $paquetesContado,
            'partes' => $cuotasPendientes
        ];

        $this->set(compact('recordatorios'));
        $this->viewBuilder()->setOption('serialize', ['recordatorios']);
    }

    public function marcarPagado($id = null)
    {
        $this->request->allowMethod(['post']);

        $paquete = $this->PaquetesPagos->get($id);

        $paquete->estado = 'cancelado';

        if ($this->PaquetesPagos->save($paquete)) {

            // Marcar todas las cuotas como pagadas
            $cuotasTable = $this->fetchTable('PaquetesPagosCuotas');

            $cuotasTable->updateAll(
                [
                    'estado' => 'pagado',
                    'fecha_pago' => new \DateTime()
                ],
                [
                    'paquete_pago_id' => $id,
                    'estado_cuota' => 'A'
                ]
            );

            $this->Flash->success('Paquete marcado como pagado.');
        } else {
            $this->Flash->error('No se pudo actualizar el estado.');
        }

        $historia = $this->PaquetesPagos->HistoriasClinicas->get(
            $paquete->historia_id
        );

        return $this->redirect([
            'controller' => 'Pacientes',
            'action' => 'view',
            $historia->paciente_id,
            '#' => 'recordatorios-pago'
        ]);
    }
}
