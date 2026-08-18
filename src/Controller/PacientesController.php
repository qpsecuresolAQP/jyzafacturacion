<?php

declare(strict_types=1);

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;

/**
 * Pacientes Controller
 *
 * @property \App\Model\Table\PacientesTable $Pacientes
 */
class PacientesController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $currentAction = $this->request->getParam('action');
         // 👇 PERMITIR BUSCADOR
    if (in_array($currentAction, ['buscarPacientesSelect', 'buscarPaciente', 'consultarDniApi'])) {
        return;
    }

        $this->verificarPermisoORedireccionar('Pacientes', $currentAction);
    }

    public function exportPacientePdf($id = null)
    {
        $paciente = $this->Pacientes->get($id, contain: [
            'Citas' => [
                'Campanas',
            ],
            'HistoriasClinicas' => [
                'Departamentos',
                'Consultas' => [
                    'Doctores'
                ],
            ],
        ]);

        // Renderizar la vista HTML como contenido para el PDF
        $this->viewBuilder()->enableAutoLayout(false);
        $this->set(compact('paciente'));
        $html = $this->render('historia_clinica');

        // Configurar DomPDF para el PDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);

        // Configurar tamaño de papel
        $dompdf->setPaper('A4', 'portrait');

        // Renderizar el PDF
        $dompdf->render();

        // Descargar el archivo PDF
        $dompdf->stream("Paciente_Detalle_{$id}.pdf", ['Attachment' => 1]);
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // consulta de pacientes activos A
        $query = $this->Pacientes->find()
            ->where(['Pacientes.estado' => 'A'])
            ->contain([
                'HistoriasClinicas' => [
                    'Departamentos'
                ]
            ]);
        $pacientes = $this->paginate($query);

        $query->order(['Pacientes.id' => 'DESC']);

        $searchTerm = $this->request->getQuery('search');
        $searchTermDni = $this->request->getQuery('searchDni');
        $conHistoria = $this->request->getQuery('con_historia');
        $searchTelefono = $this->request->getQuery('searchTelefono');

        if (!empty($searchTerm)) {
            $query->where([
                'OR' => [
                    'LOWER(Pacientes.nombre) LIKE' => '%' . strtolower($searchTerm) . '%',
                    'LOWER(Pacientes.apellido) LIKE' => '%' . strtolower($searchTerm) . '%',
                ]
            ]);
        }

        if (!empty($searchTermDni)) {
            $query->where([
                'OR' => [
                    'HistoriasClinicas.dni LIKE' => '%' . $searchTermDni . '%',
                ]
            ]);
        }

        if (!empty($searchTelefono)) {
            $query->where([
                'Pacientes.telefono_celular LIKE' => '%' . $searchTelefono . '%',
            ]);
        }

        if (!empty($conHistoria)) {
            $query->innerJoinWith('HistoriasClinicas');
        }

        $pacientes = $this->paginate($query);

        $this->set(compact('pacientes', 'searchTerm', 'searchTermDni', 'conHistoria', 'searchTelefono'));
    }

    /**
     * View method
     *
     * @param string|null $id Paciente id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $paciente = $this->Pacientes->get($id, contain: [
            'HistoriasClinicas' => [
                'Departamentos',
                'Users',
                'Consultas' => [
                    'Doctores',
                    'ConsultasCie' => [
                        'Diagnosticoscie10'
                    ],
                    'Recetas' => [
                        'RecetasMedicamentos' => [
                            'Medicamentos',
                            'FormasFarmaceuticas',
                            'ViasAdministracion'
                        ]
                    ]
                ],
                'Procedimientos' => [
                    'Doctores'
                ],
                'Presupuestos' => [
                    'sort' => ['Presupuestos.created' => 'DESC'],
                    'PresupuestosTratamientos' => [
                        'Tratamientos',
                        'sort' => ['PresupuestosTratamientos.created' => 'DESC']
                    ],
                    'PresupuestosInvoices' => [
                        'Invoices'
                    ]
                ],
                'Documentos',
                'PaquetesPagos' => [
                    'sort' => ['PaquetesPagos.created' => 'DESC'],
                    'conditions' => ['PaquetesPagos.estado_paquete' => 'A'],
                    'PaquetesPagosCuotas' => [
                        'sort' => ['PaquetesPagosCuotas.created' => 'DESC']
                    ]
                ],
            ],
            'Recordatorios' => [
                'conditions' => [
                    'Recordatorios.estado_control !=' => 'I'
                ],
                'sort' => [
                    'Recordatorios.fecha_inicio' => 'DESC'
                ],
                'RecordatorioControles' => [
                    'conditions' => [
                        'RecordatorioControles.estado_control !=' => 'I'
                    ],
                    'sort' => [
                        'RecordatorioControles.fecha_control' => 'DESC'
                    ]
                ]
            ],
            'Citas' => [
                'Doctores',
                'sort' => [
                    'Citas.fecha_hora' => 'DESC'
                ]
            ],
        ]);

        // Cargar comprobantes (invoices) del paciente
        $invoicesTable = $this->fetchTable('Invoices');
        $comprobantes = $invoicesTable
            ->find()
            ->where(['Invoices.paciente_id' => $paciente->id])
            ->orderDesc('Invoices.created')
            ->toArray();

        // Calcular monto facturado (pagado) vs pendiente por cada presupuesto,
        // excluyendo facturas anuladas
        $facturadoPorPresupuesto = [];
        if (!empty($paciente->historias_clinica->presupuestos)) {
            foreach ($paciente->historias_clinica->presupuestos as $presupuesto) {
                $facturadoPorPresupuesto[$presupuesto->id] = array_sum(array_map(
                    fn($pi) => ($pi->invoice->estado ?? '') !== 'ANULADO' ? (float) $pi->monto_facturado : 0,
                    $presupuesto->presupuestos_invoices ?? []
                ));
            }
        }

        $this->set(compact('paciente', 'comprobantes', 'facturadoPorPresupuesto'));
        // Si es una solicitud AJAX, usar un layout diferente
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Consulta DNI en API externa (RENIEC) para autocompletar el formulario de paciente
     *
     * @return \Cake\Http\Response
     */
    public function consultarDniApi()
    {
        $this->request->allowMethod(['get']);
        $this->autoRender = false;

        $dni = trim((string) $this->request->getQuery('dni', ''));

        if (!preg_match('/^\d{8}$/', $dni)) {
            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'ok' => false,
                    'message' => 'DNI inválido'
                ], JSON_UNESCAPED_UNICODE));
        }

        try {
            $apiKey = (string) \Cake\Core\Configure::read('PeruApi.key');
            $baseUrl = rtrim((string) \Cake\Core\Configure::read('PeruApi.baseUrl'), '/');

            if ($apiKey === '' || $baseUrl === '') {
                throw new \RuntimeException('No se configuró PeruApi en app_local.php');
            }

            $url = $baseUrl . '/dni/' . $dni . '?summary=0&plan=0';

            $ch = \curl_init($url);
            \curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'X-API-KEY: ' . $apiKey,
                    'Accept: application/json',
                ],
                CURLOPT_TIMEOUT => 15,
            ]);

            $res = \curl_exec($ch);

            if ($res === false) {
                $errorCurl = \curl_error($ch);
                \curl_close($ch);
                throw new \RuntimeException('Error CURL: ' . $errorCurl);
            }

            $httpCode = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
            \curl_close($ch);

            $json = json_decode($res, true);

            if ($httpCode !== 200 || !is_array($json)) {
                throw new \RuntimeException('Respuesta inválida: ' . $res);
            }

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'ok' => true,
                    'dni' => $json['dni'] ?? $dni,
                    'nombres' => $json['nombres'] ?? '',
                    'apellido_paterno' => $json['apellido_paterno'] ?? '',
                    'apellido_materno' => $json['apellido_materno'] ?? '',
                ], JSON_UNESCAPED_UNICODE));

        } catch (\Throwable $e) {
            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'ok' => false,
                    'message' => 'Error consultando DNI',
                    'error' => $e->getMessage(),
                ], JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $paciente = $this->Pacientes->newEmptyEntity();
        $historiaClinicaTable = $this->fetchTable('HistoriasClinicas');
        $historiaClinica = $historiaClinicaTable->newEmptyEntity();
        $usuario = $this->Authentication->getIdentity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['user_id'] = $usuario->id;

            $dni = $data['historiaClinica']['dni'] ?? null;

            // Validar duplicado solo si hay un DNI
            if (!empty($dni)) {
                $existeDni = $historiaClinicaTable->find()
                    ->where(['dni' => $dni])
                    ->first();

                if ($existeDni) {
                    $this->Flash->error(__('El DNI ya está registrado para otro paciente.'));
                    return $this->redirect(['action' => 'add']);
                }
            }

            // Continuar con el guardado
            $paciente = $this->Pacientes->patchEntity($paciente, $data);

            if ($this->Pacientes->save($paciente)) {
                $historiaClinicaData = array_filter($data['historiaClinica'] ?? []);

                if (!empty($historiaClinicaData)) {
                    $historiaClinica = $historiaClinicaTable->newEmptyEntity();
                    $historiaClinica = $historiaClinicaTable->patchEntity($historiaClinica, $historiaClinicaData);
                    $historiaClinica->paciente_id = $paciente->id;

                    if ($historiaClinicaTable->save($historiaClinica)) {
                        $this->Flash->success(__('El paciente y su historia clínica han sido creados.'));
                    } else {
                        $this->Flash->error(__('El paciente fue creado, pero hubo un problema al generar la historia clínica.'));
                    }
                } else {
                    $this->Flash->success(__('El paciente ha sido creado sin historia clínica.'));
                }

                return $this->redirect(['action' => 'view', $paciente->id]);
            }
            $this->Flash->error(__('El paciente no pudo ser guardado. Inténtelo de nuevo.'));
        }

        $departamentos = $this->Pacientes->HistoriasClinicas->Departamentos->find('list', limit: 200)->all();
        $campanas = $this->Pacientes->Citas->Campanas->find('list', limit: 200)->all();
        $users = $this->Pacientes->HistoriasClinicas->Users->find('list', limit: 200)->all();

        $this->set(compact('paciente', 'historiaClinica', 'departamentos', 'campanas', 'users', 'usuario'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Paciente id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $paciente = $this->Pacientes->get(
            $id,
            contain: ['HistoriasClinicas']
        );

        $historiaClinicaTable = $this->fetchTable('HistoriasClinicas');

        // Buscar historia clínica existente
        $historiaClinica = $historiaClinicaTable->find()
            ->where(['paciente_id' => $paciente->id])
            ->first(); // Puede ser null

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Validación de DNI solo si hay historia clínica y se envió
            $dni = $data['historia_clinica']['dni'] ?? null;

            if (!empty($dni) && $historiaClinica) {
                $existeDni = $historiaClinicaTable->find()
                    ->where([
                        'dni' => $dni,
                        'paciente_id !=' => $paciente->id
                    ])
                    ->first();

                if ($existeDni) {
                    $this->Flash->error(__('El DNI ya está registrado para otro paciente.'));
                    return $this->redirect(['action' => 'edit', $id]);
                }

                // Asegura que se guarde el DNI si es válido
                $data['historia_clinica']['dni'] = $dni;
            }

            // Actualizar datos del paciente
            $paciente = $this->Pacientes->patchEntity($paciente, $data);

            // Si hay historia existente y datos, actualizarla
            if (!empty($data['historia_clinica']) && $historiaClinica) {
                $historiaClinica = $historiaClinicaTable->patchEntity($historiaClinica, $data['historia_clinica']);
                $historiaClinica->paciente_id = $paciente->id;
            }

            // Guardar en transacción
            $this->Pacientes->getConnection()->transactional(function () use ($paciente, $historiaClinica, $historiaClinicaTable) {
                if (!$this->Pacientes->save($paciente)) {
                    throw new \Exception('Error al guardar el paciente.');
                }

                // Guardar historia solo si existe
                if ($historiaClinica && !$historiaClinica->isNew() && !$historiaClinicaTable->save($historiaClinica)) {
                    throw new \Exception('Error al guardar la historia clínica.');
                }
            });

            $this->Flash->success(__('El paciente ha sido actualizado.'));
            return $this->redirect(['action' => 'view', $paciente->id]);
        }

        // Solo cargar datos para combos si hay historia
        $departamentos = $campanas = $users = [];
        if ($historiaClinica) {
            $departamentos = $this->Pacientes->HistoriasClinicas->Departamentos->find('list', limit: 200)->all();
            $campanas = $this->Pacientes->Citas->Campanas->find('list', limit: 200)->all();
            $users = $this->Pacientes->HistoriasClinicas->Users->find('list', limit: 200)->all();
        }

        $this->set(compact('paciente', 'historiaClinica', 'departamentos', 'campanas', 'users'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Paciente id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null){
        //soft delete cambiar estado a I para no eliminar realmente el registro
        $this->request->allowMethod(['post', 'delete']);
        $paciente = $this->Pacientes->get($id);
        $paciente->estado = 'I';
        if ($this->Pacientes->save($paciente)) {
            $this->Flash->success(__('El paciente ha sido eliminado.'));
        } else {
            $this->Flash->error(__('El paciente no pudo ser eliminado. Inténtelo de nuevo.'));
        }
        return $this->redirect(['action' => 'index']);
    }
    // busqueda en otras tablas
    public function buscarPaciente()
    {
        $this->autoRender = false; // Evitar renderizado automático
        $this->response = $this->response->withType('json');

        $query = $this->request->getQuery('q'); // Obtener término de búsqueda

        if (!$query) {
            return $this->response->withStringBody(json_encode([])); // Si no hay consulta, retornar vacío
        }

        // Nueva forma de consulta en CakePHP 5+
        $resultados = $this->Pacientes->find()
            ->contain(['HistoriasClinicas' => function ($q) {
                return $q->select(['id', 'paciente_id', 'dni']);
            }])
            ->where([
                'estado' => 'A',
                'OR' => [
                    'Pacientes.nombre LIKE' => '%' . $query . '%',
                    'Pacientes.apellido LIKE' => '%' . $query . '%',
                ]
            ])
            ->select(['Pacientes.id', 'Pacientes.nombre', 'Pacientes.apellido'])
            ->limit(30)
            ->toArray();

        // Formatear resultados
        $pacientes = array_map(fn($paciente) => [
            'id' => $paciente->id,
            'nombre' => $paciente->nombre . ' ' . $paciente->apellido,
            'dni' => $paciente->historias_clinica->dni ?? '',
        ], $resultados);

        return $this->response->withType('application/json')
    ->withStringBody(json_encode($pacientes));
    }
public function buscarPacientesSelect()
{
    $this->request->allowMethod(['get']);

    $q = $this->request->getQuery('q');

    if (!$q) {
        return $this->response->withType('application/json')
            ->withStringBody(json_encode(['result' => []]));
    }

    $data = $this->Pacientes->find()
        ->where([
            'estado' => 'A',
            'OR' => [
                'nombre LIKE' => "%$q%",
                'apellido LIKE' => "%$q%",
            ]
        ])
        ->select(['id', 'nombre', 'apellido'])
        ->limit(30)
        ->all();

    $result = [];

    foreach ($data as $p) {
        $result[] = [
            'id' => $p->id,
            'text' => trim($p->nombre . ' ' . $p->apellido),
        ];
    }

    return $this->response->withType('application/json')
        ->withStringBody(json_encode(['result' => $result]));
}

public function historial($id = null)
    {
        $this->request->allowMethod(['get']);

        $this->loadModel('Invoices');

        $historial = $this->Invoices->find()
            ->select([
                'fecha' => 'Invoices.created',
                'documento' => 'Invoices.serie',
                'total' => 'Invoices.total'
            ])
            ->where(['Invoices.cliente_id' => $id]) // 👈 paciente = cliente
            ->orderDesc('Invoices.created')
            ->toArray();

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($historial));
    }

    public function historialfactura()
{
    // solo renderiza la vista
}

public function obtenerHistorial($id = null)
{
    $this->request->allowMethod(['get']);

    $this->loadModel('Invoices');

    $historial = $this->Invoices->find()
        ->select([
            'fecha' => 'Invoices.created',
            'documento' => 'Invoices.serie',
            'total' => 'Invoices.total'
        ])
        ->where(['Invoices.cliente_id' => $id])
        ->orderDesc('Invoices.created')
        ->toArray();

    return $this->response
        ->withType('application/json')
        ->withStringBody(json_encode($historial));
}

}
