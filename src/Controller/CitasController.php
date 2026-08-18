<?php

declare(strict_types=1);

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Citas Controller
 *
 * @property \App\Model\Table\CitasTable $Citas
 */
class CitasController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $currentAction = $this->request->getParam('action');
        
        $this->verificarPermisoORedireccionar('Citas', $currentAction);
    }

    public function exportarReporteExcel()
{
    // Obtener y validar fechas
    $fechaInicio  = $this->request->getQuery('fecha_inicio');
    $fechaFin     = $this->request->getQuery('fecha_fin');
    $doctorId     = $this->request->getQuery('doctor_id');
    $userId       = $this->request->getQuery('user_id');
    $estadoFiltro = $this->request->getQuery('estado');

    if ($fechaInicio && $fechaFin) {
        try {
            $tz = new \DateTimeZone('America/Lima');
            $fechaInicio = new \DateTime($fechaInicio, $tz);
            $fechaFin    = new \DateTime($fechaFin, $tz);
            $fechaFin->modify('+1 day -1 second');
        } catch (\Exception $e) {
            $this->Flash->error('Formato de fechas inválido.');
            return $this->redirect(['action' => 'reportecitas']);
        }
    }

    // Consulta con relaciones
    $query = $this->Citas->find()
        ->contain(['Pacientes', 'Doctores', 'Users'])
        ->where([
            'fecha_hora >=' => $fechaInicio->format('Y-m-d H:i:s'),
            'fecha_hora <=' => $fechaFin->format('Y-m-d H:i:s'),
        ]);

    if ($doctorId) {
        $query->where(['Citas.doctor_id' => $doctorId]);
    }
    if ($userId)       { $query->where(['user_id'   => $userId]); }
    if ($estadoFiltro) { $query->where(['estado'    => $estadoFiltro]); }

    $citas = $query->order(['fecha_hora' => 'ASC'])->toArray();

    // Crear hoja Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = ['Estado','Paciente1','Doctor','Fecha y hora','Usuario'];
    $sheet->fromArray($headers, null, 'A1');

    // Inicializar contador de estados
    $totalPorEstado = [];

    $row = 2;
    foreach ($citas as $cita) {
        $estado   = $cita->estado ?? 'Desconocido';
        // Contar estados
        if (!isset($totalPorEstado[$estado])) {
            $totalPorEstado[$estado] = 0;
        }
        $totalPorEstado[$estado]++;

        $paciente1 = !empty($cita->paciente1) ? trim($cita->paciente1->nombre . ' ' . $cita->paciente1->apellido) : 'Paciente1 no encontrado';

        $doctor = 'Doctor no asignado';
        if (!empty($cita->doctor)) {
            $doctor = trim($cita->doctor->nombre . ' ' . $cita->doctor->apellido);
        } else if (!empty($cita->doctor_id)) {
            $doctorData = $this->Citas->Doctores->find()
                ->select(['nombre', 'apellido'])
                ->where(['id' => $cita->doctor_id])
                ->first();
            if ($doctorData) {
                $doctor = trim($doctorData->nombre . ' ' . $doctorData->apellido);
            }
        }

        $usuario  = !empty($cita->user) ? $cita->user->username : 'Usuario no encontrado';
        $fecha    = ($cita->fecha_hora instanceof \DateTime) ? $cita->fecha_hora->format('Y-m-d H:i:s') : $cita->fecha_hora;

        $sheet->setCellValue("A{$row}", $estado);
        $sheet->setCellValue("B{$row}", $paciente1);
        $sheet->setCellValue("C{$row}", $doctor);
        $sheet->setCellValue("D{$row}", $fecha);
        $sheet->setCellValue("E{$row}", $usuario);
        $row++;
    }

    // Dejar una fila vacía
    $row++;

    // Escribir título de totales
    $sheet->setCellValue("A{$row}", "Totales por Estado:");
    $row++;

    // Escribir totales por estado
    foreach ($totalPorEstado as $estado => $cantidad) {
        $sheet->setCellValue("A{$row}", $estado);
        $sheet->setCellValue("B{$row}", $cantidad);
        $row++;
    }

    // Ajustar ancho de columnas
    foreach (range('A','E') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $writer = new Xlsx($spreadsheet);
    $filename = 'reporte_citas_' . date('Ymd_His') . '.xlsx';

    $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx');
    $writer->save($tmpFile);

    return $this->response->withFile($tmpFile, [
        'download' => true,
        'name' => $filename,
        'delete' => true,
    ]);
}
public function exportarReportePdf()
{
    // Obtener los parámetros del rango de fechas, doctor, usuario y estado desde la URL
    $fechaInicio = $this->request->getQuery('fecha_inicio');
    $fechaFin = $this->request->getQuery('fecha_fin');
    $doctorId = $this->request->getQuery('doctor_id');
    $userId = $this->request->getQuery('user_id'); // Nuevo parámetro
    $estadoFiltro = $this->request->getQuery('estado'); // Nuevo parámetro para estado
    $attachment = $this->request->getQuery('download') === 'true';

    $username = null;
    if ($userId) {
        // Obtener el username del usuario correspondiente al user_id
        $user = $this->Citas->Users->find()
            ->select(['username'])
            ->where(['id' => $userId])
            ->first();
        $username = $user ? $user->username : 'Usuario no encontrado';
    }
    if ($fechaInicio && $fechaFin) {
        try {
            $fechaInicio = new \DateTime($fechaInicio, new \DateTimeZone('America/Lima'));
            $fechaFin = new \DateTime($fechaFin, new \DateTimeZone('America/Lima'));
            $fechaFin->modify('+1 day -1 second'); // Incluir todo el día
        } catch (\Exception $e) {
            $this->Flash->error('Formato de fechas inválido.');
            return $this->redirect(['action' => 'reportecitas']);
        }
    }

    $citasAgrupadas = [];
    $totalCitas = 0;

    if ($fechaInicio && $fechaFin) {
        $query = $this->Citas->find()
            ->contain([
                'Pacientes',
                'Users',
                'CitasTratamientos' => [
                    'Tratamientos'
                ]
            ])
            ->where([
                'fecha_hora >=' => $fechaInicio->format('Y-m-d H:i:s'),
                'fecha_hora <=' => $fechaFin->format('Y-m-d H:i:s'),
            ]);

        if ($doctorId) {
            $query->where(['doctor_id' => $doctorId]);
        }

        if ($userId) {
            $query->where(['user_id' => $userId]);
        }

        if ($estadoFiltro) {
            $query->where(['estado' => $estadoFiltro]); // Filtrar por estado
        }

        $citas = $query->order(['fecha_hora' => 'ASC'])->toArray();

        foreach ($citas as $cita) {
            $doctor = $this->Citas->Doctores->find()
                ->select(['nombre', 'apellido'])
                ->where(['id' => $cita->doctor_id])
                ->first();
            $doctorNombreCompleto = $doctor
                ? $doctor->nombre . ' ' . $doctor->apellido
                : 'Doctor no asignado';

            // Obtener los tratamientos asociados a esta cita
            $tratamientos = [];
            if (!empty($cita->citas_tratamientos)) {
                foreach ($cita->citas_tratamientos as $citaTratamiento) {
                    if (!empty($citaTratamiento->tratamiento)) {
                        $tratamientos[] = $citaTratamiento->tratamiento->nombre;
                    }
                }
            }

            // Obtener nombre y teléfono del paciente
            $pacienteNombre = '';
            $telefonoCelular = '';
            
            // Verificar si el paciente vino en la relación cargada
            if (!empty($cita->pacientes)) {
                $pacienteNombre = ($cita->pacientes->nombre ?? '') . ' ' . ($cita->pacientes->apellido ?? '');
                $telefonoCelular = $cita->pacientes->telefono_celular ?? '';
            } else if ($cita->paciente_id) {
                // Si no vino en la relación, consultar directamente
                $paciente = $this->Citas->Pacientes->find()
                    ->select(['nombre', 'apellido', 'telefono_celular'])
                    ->where(['id' => $cita->paciente_id])
                    ->first();
                if ($paciente) {
                    $pacienteNombre = ($paciente->nombre ?? '') . ' ' . ($paciente->apellido ?? '');
                    $telefonoCelular = $paciente->telefono_celular ?? '';
                }
            }

            // Obtener el username del usuario asociado a la cita
            $userNombre = '';
            if (!empty($cita->user)) {
                $userNombre = $cita->user->username ?? '';
            }

            $estado = $cita->estado ?? 'Desconocido';
            if (!isset($citasAgrupadas[$estado])) {
                $citasAgrupadas[$estado] = [
                    'total' => 0,
                    'citas' => []
                ];
            }
            $citasAgrupadas[$estado]['total']++;
            
            $citasAgrupadas[$estado]['citas'][] = [
                'paciente' => trim($pacienteNombre),
                'doctor' => $doctorNombreCompleto,
                'fecha_hora' => $cita->fecha_hora->format('Y-m-d H:i:s'),
                'usuario' => $userNombre,
                'telefono_celular' => $telefonoCelular,
                'duracion_minutos' => $cita->duracion_minutos ?? 0,
                'tratamientos' => $tratamientos,
            ];
        }

        $totalCitas = count($citas);
    }

    $this->viewBuilder()->disableAutoLayout();
    
    $this->set(compact('fechaInicio', 'fechaFin', 'doctorId', 'userId', 'estadoFiltro', 'totalCitas', 'citasAgrupadas', 'username'));
    $html = $this->render('reportecitas_pdf')->getBody()->__toString();

    // PDF con Dompdf
    $options = new Options();
    $options->set('defaultFont', 'DejaVu Sans');
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    if ($attachment) {
        $dompdf->stream("reporte_citas.pdf", ["Attachment" => true]);
    } else {
        $this->response = $this->response->withType('application/pdf');
        $this->response = $this->response->withStringBody($dompdf->output());
        return $this->response;
    }
}


    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */

    public function index()
    {
        // Obtener la identidad del usuario autenticado
        $usuario = $this->Authentication->getIdentity();

        if (!$usuario) {
            // Redirigir al login si no hay usuario autenticado
            return $this->redirect(['controller' => 'Usuarios', 'action' => 'login']);
        }

        // Obtener el doctor de la URL si existe
        $doctorId = $this->request->getQuery('doctor_id');
        $fecha = $this->request->getQuery('fecha') ?? date('Y-m-d');

        // Si el usuario es doctor (rol_id = 2) y no hay un doctor en la URL, asignar automáticamente su doctor_id
        if (!$doctorId && $usuario->rol_id == 2 && !empty($usuario->doctor_id)) {
            $doctorId = $usuario->doctor_id;
        }

        // Consulta base de las citas
        $query = $this->Citas->find()
            ->contain(['Doctores', 'Pacientes']);


        // Filtrar por doctor si hay un doctor seleccionado (excepto para admins)
        if ($doctorId) {
            $query->where(['Citas.doctor_id' => $doctorId]);
        }

        // Filtrar por fecha seleccionada
        if ($fecha) {
            $query->where(['DATE(Citas.fecha_hora)' => $fecha]);
        }

        // Obtener resultados
        $citas = $query->all();

        // Obtener listas para selectores
        $doctores = $this->Citas->Doctores->find('list', [
            'keyField' => 'id',
            'valueField' => function ($doctor) {
                return $doctor->nombre . ' ' . $doctor->apellido;
            }
        ])
        ->contain(['Users'])
        ->matching('Users', function ($q) {
            return $q->where(['Users.estado_user !=' => 'I']);
        })
        ->toArray();

        // Obtener roles permitidos basado en los permisos del sistema
        // Solo los roles que tienen permiso para editar citas (Citas.edit)
        $rolesPermitidos = $this->fetchTable('RolesPermisos')->find()
            ->distinct(['RolesPermisos.rol_id'])
            ->select(['RolesPermisos.rol_id'])
            ->innerJoinWith('Permisos')
            ->where([
                'Permisos.controller' => 'Citas', 
                'Permisos.action' => 'edit'
            ])
            ->toArray();
        
        // Extraer solo los IDs de rol
        $rolesPermitidosIds = array_map(function($item) {
            return $item->rol_id;
        }, $rolesPermitidos);

        // Obtener el rol_id del usuario actual
        $usuarioRolId = $usuario->rol_id ?? null;

        // Pasar datos a la vista
        $this->set(compact('citas', 'doctores', 'doctorId', 'fecha', 'usuario', 'rolesPermitidosIds', 'usuarioRolId'));
    }


    /**
     * View method
     *
     * @param string|null $id Cita id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $cita = $this->Citas->get(
            $id,
            contain: ['Doctores', 'Campanas', 'Pacientes']
        );

        // Asegurar que Pacientes se hidrate correctamente
        if (!$cita->Pacientes && $cita->paciente_id) {
            $paciente = $this->Citas->Pacientes->get($cita->paciente_id);
            $cita->Pacientes = $paciente;
        }

        $this->set(compact('cita'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */

    public function add()
    {
        $cita = $this->Citas->newEmptyEntity();
        $doctorSeleccionado = null; // Inicializar variable

        // Prellenar datos si se reciben como parámetros en la URL
        if ($this->request->is('get')) {
            if ($this->request->getQuery('fecha_hora')) {
                $cita->fecha_hora = $this->request->getQuery('fecha_hora');
            }
            if ($this->request->getQuery('doctor_id')) {
                $cita->doctor_id = $this->request->getQuery('doctor_id');
                $doctorSeleccionado = $this->request->getQuery('doctor_id'); // Guardar el doctor seleccionado
            }
        }

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Si viene información de un nuevo paciente1
            if (!empty($data['nuevo_paciente']['nombre']) && !empty($data['nuevo_paciente']['apellido'])) {
                $paciente1Data = $data['nuevo_paciente'];
                $paciente1 = $this->Citas->Pacientes->newEntity($paciente1Data);

                if ($this->Citas->Pacientes->save($paciente1)) {
                    $data['paciente_id'] = $paciente1->id; // Asignar el nuevo paciente1 a la cita
                } else {
                    $this->Flash->error(__('No se pudo crear el nuevo paciente1. Por favor, intente de nuevo.'));
                }
            }

            // Asignar el usuario logueado a la cita
            $data['user_id'] = $this->request->getAttribute('identity')->id;

            $cita = $this->Citas->patchEntity($cita, $data, [
                'associated' => ['CitasTratamientos', 'Campanas']
            ]);

            // Intentar guardar la cita
            if ($this->Citas->save($cita)) {
                if (!empty($data['citas_tratamientos']) && is_array($data['citas_tratamientos'])) {
                    $citasTratamientosTable = $this->fetchTable('CitasTratamientos');

                    foreach ($data['citas_tratamientos'] as $t) {
                        $nuevaAsociacion = $citasTratamientosTable->newEmptyEntity();
                        $nuevaAsociacion = $citasTratamientosTable->patchEntity($nuevaAsociacion, [
                            'cita_id' => $cita->id,
                            'tratamiento_id' => $t['tratamiento_id'] ?? null,
                            'cantidad' => $t['cantidad'] ?? 1,
                            'total' => $t['total'] ?? 0
                        ]);
                        $citasTratamientosTable->save($nuevaAsociacion);
                    }
                }
                $this->Flash->success(__('La cita se guardó correctamente.'));
                return $this->redirect([
                    'action' => 'index',
                    '?' => [
                        'doctor_id' => $cita->doctor_id,
                        'fecha' => $cita->fecha_hora->format('Y-m-d'),
                    ]
                ]);
            }

            $this->Flash->error(__('No se pudo guardar la cita. Por favor, inténtelo de nuevo.'));
        }

        $doctores = $this->Citas->Doctores->find('list')->all();
        $Pacientes = $this->Citas->Pacientes->find('list')->all();
        $campanas = $this->Citas->Campanas->find('list')->all();
        // ✅ Obtener lista de tratamientos
        $tratamientos = $this->Citas->Tratamientos->find('list', keyField: 'id', valueField: 'nombre')->all();
        // Pasar la variable doctorSeleccionado a la vista
        $this->set(compact('cita', 'doctores', 'Pacientes', 'doctorSeleccionado', 'tratamientos','campanas'));

        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Cita id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        // Verifica que el ID de la cita sea válido
        if (!$id) {
            $this->log('El ID no fue recibido en el controlador.', 'error');
            if ($this->request->is('ajax')) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => __('ID de cita no válido.')]));
            }
            $this->Flash->error(__('ID de cita no válido.'));
            return $this->redirect(['action' => 'index']);
        }

        // Intenta obtener la cita
        try {
            $cita = $this->Citas->get($id, 
                contain : ['Campanas']
            );
        } catch (\Exception $e) {
            $this->log('Error al obtener la cita: ' . $e->getMessage(), 'error');
            if ($this->request->is('ajax')) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => __('La cita no existe.')]));
            }
            $this->Flash->error(__('La cita no existe.'));
            return $this->redirect(['action' => 'index']);
        }

        // Procesa el formulario cuando es enviado (PATCH, POST, PUT)
        if ($this->request->is(['patch', 'post', 'put'])) {
            // Obtener los datos del formulario correctamente usando getData()
            $requestData = $this->request->getData();

            // **Combinamos la fecha y la hora solo si se han proporcionado ambos campos**
            if (isset($requestData['fecha']) && isset($requestData['hora'])) {
                $fecha = $requestData['fecha'];
                $hora = $requestData['hora'];

                // Crear una fecha y hora combinada en formato "Y-m-d H:i:s"
                $fechaHora = $fecha . ' ' . $hora;
                $requestData['fecha_hora'] = $fechaHora; // Asignamos el valor combinado a `fecha_hora`
            }

            // Aplicar los cambios en la entidad Cita
            $cita = $this->Citas->patchEntity($cita, $requestData);

            // Si se guardó correctamente
            if ($this->Citas->save($cita)) {
                if (!empty($requestData['citas_tratamientos'])) {
                    $citasTratamientosTable = $this->fetchTable('CitasTratamientos');

                    // Eliminar los tratamientos anteriores asociados a esta cita
                    $citasTratamientosTable->deleteAll(['cita_id' => $cita->id]);

                    // Insertar los nuevos tratamientos
                    foreach ($requestData['citas_tratamientos'] as $t) {
                        $nuevaAsociacion = $citasTratamientosTable->newEmptyEntity();
                        $nuevaAsociacion = $citasTratamientosTable->patchEntity($nuevaAsociacion, [
                            'cita_id' => $cita->id,
                            'tratamiento_id' => $t['tratamiento_id'] ?? null,
                            'cantidad' => $t['cantidad'] ?? 1,
                            'total' => $t['total'] ?? 0
                        ]);
                        $citasTratamientosTable->save($nuevaAsociacion);
                    }
                }

                // Si es una solicitud AJAX
                if ($this->request->is('ajax')) {
                    return $this->response->withType('application/json')
                        ->withStringBody(json_encode([
                            'success' => true,
                            'message' => __('Cita actualizada correctamente.'),
                            'doctor_id' => $cita->doctor_id,
                            'fecha_hora' => $cita->fecha_hora->format('Y-m-d')
                        ]));
                }

                // Si no es AJAX, muestra mensaje y redirige
                $this->Flash->success(__('Cita actualizada correctamente.'));

                // Redirigir al índice manteniendo el odontólogo y la fecha seleccionados
                return $this->redirect([
                    'action' => 'index',
                    '?' => [
                        'doctor_id' => $cita->doctor_id,
                        'fecha' => $cita->fecha_hora->format('Y-m-d'),
                    ]
                ]);
            }

            // Si no se pudo guardar, mostrar error
            if ($this->request->is('ajax')) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => __('No se pudo actualizar la cita.')]));
            }
            $this->Flash->error(__('No se pudo actualizar la cita. Por favor, inténtelo de nuevo.'));
        }

        // Obtener la lista de doctores y Pacientes para mostrarlos en el formulario
        $doctores = $this->Citas->Doctores->find('list')->toArray();
        $Pacientes = $this->Citas->Pacientes->find(
            'list',
            keyField: 'id',
            valueField: function ($row) {
                return $row->nombre . ' ' . $row->apellido;
            }
        )->toArray();

        // Cargar los tratamientos disponibles
        $tratamientos = $this->Citas->Tratamientos->find('list', 
            keyField : 'id',
            valueField : 'nombre'
        )->toArray();        
        $campanas = $this->Citas->Campanas->find('list')->toArray();
        // Obtener tratamientos ya asignados a la cita
        $citasTratamientosTable = $this->fetchTable('CitasTratamientos');
        $tratamientosAsociados = $citasTratamientosTable->find()
            ->where(['cita_id' => $id])
            ->contain(['Tratamientos'])
            ->all()
            ->toArray();

        // Asignar la cita, doctores y Pacientes a la vista
        $this->set(compact('cita', 'doctores', 'Pacientes', 'tratamientos', 'tratamientosAsociados','campanas'));

        // Si es una solicitud AJAX, usar un layout diferente
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }


    /**
     * Delete method
     *
     * @param string|null $id Cita id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cita = $this->Citas->get($id);

        if ($this->Citas->delete($cita)) {
            $this->Flash->success(__('The cita has been deleted.'));
        } else {
            $this->Flash->error(__('The cita could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

   public function fetchCitas()
    {
        $this->request->allowMethod(['get']);

        try {
            // Obtener los parámetros de la consulta
            $fecha = $this->request->getQuery('fecha');
            $doctorId = $this->request->getQuery('doctor_id');

            // Validar que se hayan enviado los parámetros
            if (!$fecha || !$doctorId) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['error' => 'Faltan parámetros necesarios']));
            }

            // Calcular el rango de fechas para la semana seleccionada
            $fechaInicio = new \DateTime($fecha, new \DateTimeZone('America/Lima'));
            $fechaFin = clone $fechaInicio;
            $fechaFin->modify('+6 days');

            // Buscar el nombre completo del doctor por el doctor_id
            $doctor = $this->Citas->Doctores->find()
                ->select(['nombre', 'apellido'])
                ->where(['id' => $doctorId])
                ->first();

        // Asignar nombre del doctor
        $doctorNombreCompleto = $doctor
            ? $doctor->nombre . ' ' . $doctor->apellido
            : 'Doctor no asignado';

        // Buscar las citas en el rango de fechas y para el doctor seleccionado
        $citas = $this->Citas->find()
            ->where([
                'fecha_hora >=' => $fechaInicio->format('Y-m-d 00:00:00'),
                'fecha_hora <=' => $fechaFin->format('Y-m-d 23:59:59'),
                'doctor_id' => $doctorId
            ])
            ->order(['fecha_hora' => 'ASC'])
            ->toArray();
        
        // Obtener IDs de pacientes
        $pacienteIds = array_column($citas, 'paciente_id');
        
        // Traer datos de pacientes
        $pacientes = [];
        if (!empty($pacienteIds)) {
            $pacientesData = $this->Citas->Pacientes->find()
                ->select(['id', 'nombre', 'apellido', 'telefono_celular'])
                ->where(['id IN' => $pacienteIds])
                ->toArray();
            
            foreach ($pacientesData as $p) {
                $pacientes[$p->id] = $p;
            }
        }
        
        //Diferenciar citas con historia: Obtener todos los paciente_ids de las citas
        $historiasClinicas = [];
        if (!empty($pacienteIds)) {
            $historias = $this->Citas->Pacientes->HistoriasClinicas->find()
                ->select(['paciente_id', 'dni'])
                ->where(['paciente_id IN' => $pacienteIds])
                ->toArray();
            
            // Crear un array asociativo para acceso rápido
            foreach ($historias as $historia) {
                $historiasClinicas[$historia->paciente_id] = $historia;
            }
        }

        // Formatear las citas para devolverlas en JSON
        $citasFormateadas = array_map(function ($cita) use ($doctorNombreCompleto, $historiasClinicas, $pacientes) {
        $citaHoraLima = $cita->fecha_hora->setTimezone(new \DateTimeZone('America/Lima'));

        // Obtener datos del paciente
        $pacienteName = 'Paciente desconocido';
        $telefonoCelular = '';
        if (isset($pacientes[$cita->paciente_id])) {
            $paciente = $pacientes[$cita->paciente_id];
            $pacienteName = trim($paciente->nombre . ' ' . $paciente->apellido);
            $telefonoCelular = $paciente->telefono_celular ?? '';
        }

        // Verificar si este paciente tiene historia clínica con DNI
        $tieneHistoriaClinicaDni = false;
        if (isset($historiasClinicas[$cita->paciente_id])) {
            $historia = $historiasClinicas[$cita->paciente_id];
            $tieneHistoriaClinicaDni = !empty($historia->dni);
        }

        return [
            'id' => $cita->id,
            'doctor_id' => $cita->doctor_id,
            'fecha_hora' => $citaHoraLima->format('Y-m-d H:i:s'),
            'duracion_minutos' => $cita->duracion_minutos,
            'paciente_id' => $cita->paciente_id,
            'paciente' => $pacienteName,
            'telefono_celular' => $telefonoCelular,
            'estado' => $cita->estado,
            'hora_llegada' => $cita->hora_llegada,
            'doctor' => $doctorNombreCompleto,
            'tiene_historia_clinica_dni' => $tieneHistoriaClinicaDni,
            'motivo' => $cita->motivo ?? '',
        ];
    }, $citas);

            return $this->response->withType('application/json')
                ->withStringBody(json_encode($citasFormateadas));
        } catch (\Exception $e) {
            // Retornar un JSON de error en caso de excepción
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['error' => 'Error al obtener citas: ' . $e->getMessage()]));
        }
    }

    public function changeStatus()
    {
        $this->request->allowMethod(['post']);

        $id = $this->request->getData('id');
        $estado = $this->request->getData('estado');

        if (!$id || !$estado) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Parámetros inválidos.']));
        }

        try {
            $cita = $this->Citas->get($id);
            $cita->estado = $estado;

            if ($this->Citas->save($cita)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => true, 'message' => 'Estado actualizado correctamente.']));
            } else {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'No se pudo actualizar el estado.']));
            }
        } catch (\Exception $e) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Error al procesar la solicitud.']));
        }
    }

    public function reportecitas()
    {
        // Obtener los parámetros del rango de fechas y doctor desde el formulario
        $fechaInicio = $this->request->getQuery('fecha_inicio');
        $fechaFin = $this->request->getQuery('fecha_fin');
        $doctorId = $this->request->getQuery('doctor_id');
        $userId = $this->request->getQuery('user_id'); 

        if ($fechaInicio && $fechaFin) {
            try {
                $fechaInicio = new \DateTime($fechaInicio, new \DateTimeZone('America/Lima'));
                $fechaFin = new \DateTime($fechaFin, new \DateTimeZone('America/Lima'));
                $fechaFin->modify('+1 day -1 second'); // Ajustar para incluir todo el día final
            } catch (\Exception $e) {
                $this->Flash->error('Formato de fechas inválido.');
                $fechaInicio = null;
                $fechaFin = null;
            }
        }

        $citasAgrupadas = [];
        $totalCitas = 0;

        if ($fechaInicio && $fechaFin) {
            $query = $this->Citas->find()
                ->contain([
                    'Pacientes',
                    'Users',
                    'CitasTratamientos' => [
                        'Tratamientos'
                    ]
                ])
                ->where([
                    'fecha_hora >=' => $fechaInicio->format('Y-m-d H:i:s'),
                    'fecha_hora <=' => $fechaFin->format('Y-m-d H:i:s')
                ]);

            // Filtrar por doctor solo si se selecciona uno
            if ($doctorId) {
                $query->where(['doctor_id' => $doctorId]);
            }

            // Filtrar por user_id si se selecciona uno
            if ($userId) {
                $query->where(['user_id' => $userId]);
            }
            $citas = $query->order(['fecha_hora' => 'ASC'])->toArray();

            foreach ($citas as $cita) {
                // Consultar el nombre completo del doctor
                $doctor = $this->Citas->Doctores->find()
                    ->select(['nombre', 'apellido'])
                    ->where(['id' => $cita->doctor_id])
                    ->first();
                $doctorNombreCompleto = $doctor
                    ? $doctor->nombre . ' ' . $doctor->apellido
                    : 'Doctor no asignado';

                // Obtener los tratamientos asociados a esta cita
                $tratamientos = [];
                if (!empty($cita->citas_tratamientos)) {
                    foreach ($cita->citas_tratamientos as $citaTratamiento) {
                        if (!empty($citaTratamiento->tratamiento)) {
                            $tratamientos[] = $citaTratamiento->tratamiento->nombre;
                        }
                    }
                }

                // Obtener nombre del paciente
                $nombrePaciente = '';
                if (!empty($cita->pacientes)) {
                    $nombrePaciente = ($cita->pacientes->nombre ?? '') . ' ' . ($cita->pacientes->apellido ?? '');
                }

                // Obtener teléfono del paciente
                $telefonoPaciente = '';
                if (!empty($cita->pacientes)) {
                    $telefonoPaciente = $cita->pacientes->telefono_celular ?? '';
                }

                // Obtener username del usuario
                $usuarioNombre = '';
                if (!empty($cita->user)) {
                    $usuarioNombre = $cita->user->username ?? '';
                }

                // Agrupar los datos por estado
                $estado = $cita->estado ?? 'Desconocido';
                if (!isset($citasAgrupadas[$estado])) {
                    $citasAgrupadas[$estado] = [
                        'total' => 0,
                        'citas' => []
                    ];
                }
                $citasAgrupadas[$estado]['total']++;
                $citasAgrupadas[$estado]['citas'][] = [
                    'id' => $cita->id,
                    'paciente' => trim($nombrePaciente),
                    'telefono_celular' => $telefonoPaciente,
                    'doctor' => $doctorNombreCompleto,
                    'fecha_hora' => $cita->fecha_hora->format('Y-m-d H:i:s'),
                    'duracion_minutos' => $cita->duracion_minutos ?? 0,
                    'tratamientos' => $tratamientos,
                    'usuario' => $usuarioNombre
                ];
            }

            $totalCitas = count($citas);
        }

        // Obtener la lista de doctores para el filtro
        $doctores = $this->Citas->Doctores->find(
            'list',
            keyField: 'id',
            valueField: function ($doctor) {
                return $doctor->nombre . ' ' . $doctor->apellido;
            }
        )->toArray();
        // Obtener la lista de usuarios para el filtro (asegúrate de tener esta consulta)
            $usuarios = $this->Citas->Users->find('list', 
                keyField : 'id',
                valueField : 'username' // O el campo que prefieras
            )->toArray();
            $estados = [
                'pendiente' => 'Pendiente',
                'confirmado' => 'Confirmado',
                'en_consultorio' => 'En Consultorio',
                'finalizado' => 'Finalizado',
                'cancelado' => 'Cancelado'
            ];
        $this->set(compact('fechaInicio', 'fechaFin', 'doctorId', 'totalCitas', 'citasAgrupadas', 'doctores', 'usuarios','estados'));
    }


    // public function verificarDisponibilidad()
    // {
    //     $this->request->allowMethod(['get']);

    //     // Obtener los parámetros de la URL
    //     $fechaHora = $this->request->getQuery('fecha_hora');
    //     $doctorId = $this->request->getQuery('doctor_id');
    //     $citaId = $this->request->getQuery('cita_id'); // El ID de la cita que estamos editando

    //     // Convertir la fecha a objeto DateTime
    //     $inicioNuevaCita = new \DateTime($fechaHora);

    //     // Duración en minutos (por defecto 15 minutos si no se envía)
    //     $duracionMinutos = (int) $this->request->getQuery('duracion_minutos', 15);
    //     $finNuevaCita = (clone $inicioNuevaCita)->modify("+{$duracionMinutos} minutes");

    //     // Consulta para encontrar citas que se solapen con la nueva
    //     $citaExistente = $this->Citas->find()
    //         ->where([
    //             'doctor_id' => $doctorId,
    //             'OR' => [
    //                 [
    //                     'fecha_hora <' => $finNuevaCita->format('Y-m-d H:i:s'),
    //                     $this->Citas->query()->newExpr("DATE_ADD(fecha_hora, INTERVAL duracion_minutos MINUTE) > '" . $inicioNuevaCita->format('Y-m-d H:i:s') . "'")
    //                 ]
    //             ]
    //         ]);

    //     // Si estamos editando una cita, excluir la cita actual (si se proporcionó un citaId)
    //     if ($citaId !== null) {
    //         $citaExistente->andWhere(['Citas.id !=' => $citaId]);
    //     }

    //     // Obtener el primer resultado
    //     $citaExistente = $citaExistente->first();

    //     // Si se encontró una cita existente que se solapa, la hora está ocupada
    //     $disponible = $citaExistente ? false : true;

    //     // Devolver respuesta en formato JSON
    //     return $this->response->withType('application/json')
    //         ->withStringBody(json_encode(['disponible' => $disponible]));
    // }

    public function verificarDisponibilidad()
    {
        $this->request->allowMethod(['get']);

        // Obtener los parámetros de la URL
        $fechaHora = $this->request->getQuery('fecha_hora');
        $doctorId = $this->request->getQuery('doctor_id');
        $citaId = $this->request->getQuery('cita_id'); // El ID de la cita que estamos editando

        // Convertir la fecha a objeto DateTime
        $inicioNuevaCita = new \DateTime($fechaHora);

        // Duración en minutos (por defecto 15 minutos si no se envía)
        $duracionMinutos = (int) $this->request->getQuery('duracion_minutos', 15);
        $finNuevaCita = (clone $inicioNuevaCita)->modify("+{$duracionMinutos} minutes");

        // Consulta para encontrar citas que se solapen con la nueva
        $citaExistente = $this->Citas->find()
            ->where([
                'doctor_id' => $doctorId,
                'estado !=' => 'cancelado', // ignoramos las citas canceladas en la verificacion
                'OR' => [
                    [
                        'fecha_hora <' => $finNuevaCita->format('Y-m-d H:i:s'),
                        $this->Citas->query()->newExpr("DATE_ADD(fecha_hora, INTERVAL duracion_minutos MINUTE) > '" . $inicioNuevaCita->format('Y-m-d H:i:s') . "'")
                    ]
                ]
            ]);

        // Si estamos editando una cita, excluir la cita actual (si se proporcionó un citaId)
        if ($citaId !== null) {
            $citaExistente->andWhere(['Citas.id !=' => $citaId]);
        }

        // Obtener el primer resultado
        $citaExistente = $citaExistente->first();

        // Si se encontró una cita existente que se solapa, la hora está ocupada
        $disponible = $citaExistente ? false : true;
        // 🔒 Verificar bloqueos horarios si la cita está disponible
        if ($disponible) {
            $HorariosBloqueos = $this->fetchTable('HorariosBloqueos');
            $fecha = $inicioNuevaCita->format('Y-m-d');
            $horaInicio = $inicioNuevaCita->format('H:i:s');
            $horaFin = $finNuevaCita->format('H:i:s');

            $disponible = !$HorariosBloqueos->verificarBloqueo($doctorId, $fecha, $horaInicio, $horaFin);
        }

        // Devolver respuesta en formato JSON
        return $this->response->withType('application/json')
            ->withStringBody(json_encode(['disponible' => $disponible]));
    }
    public function citaDiaria()
    {
        // Obtener la lista de doctores
        $doctores = $this->Citas->Doctores->find(
            'list',
            keyField: 'id',
            valueField: function ($row) {
                return $row->nombre . ' ' . $row->apellido;
            }
        )->toArray();

        // Obtener doctor seleccionado (si lo hay)
        $doctorId = $this->request->getQuery('doctor_id');
        
        $usuario = $this->Authentication->getIdentity();

        // Si el usuario es doctor (rol_id = 2) y no hay un doctor en la URL, asignar automáticamente su doctor_id
        if (!$doctorId && $usuario->rol_id == 2 && !empty($usuario->doctor_id)) {
            $doctorId = $usuario->doctor_id;
        }

        // Obtener la lista de tratamientos
        $tratamientos = $this->Citas->CitasTratamientos->Tratamientos->find('list', keyField: 'id', valueField: 'nombre')->toArray();

        // Validar que el parámetro 'fecha' esté presente
        $fecha = $this->request->getQuery('fecha', date('Y-m-d')); // Si no hay fecha, usar hoy

        // Calcular el rango de fechas para hoy
        $fechaInicio = new \DateTime($fecha . ' 00:00:00', new \DateTimeZone('America/Lima'));
        $fechaFin = clone $fechaInicio;
        $fechaFin->modify('+1 day')->modify('-1 second'); // Último segundo del día

        // Construir la consulta
        $query = $this->Citas->find()
            ->contain(['Pacientes', 'Doctores', 'CitasTratamientos.Tratamientos']) // Asegúrate de cargar la relación con Tratamientos
            ->where([
                'fecha_hora >=' => $fechaInicio->format('Y-m-d H:i:s'),
                'fecha_hora <=' => $fechaFin->format('Y-m-d H:i:s')
            ]);
        if (!empty($doctorId)) {
            $query->where(['Citas.doctor_id' => $doctorId]);
        }
        // Obtener las citas
        $citas = $query->order(['fecha_hora' => 'ASC'])->toArray();

        // Obtener el rol_id del usuario actual
        $usuarioRolId = $usuario->rol_id ?? null;

        // Pasar las variables a la vista
        $this->set(compact('citas', 'doctores', 'doctorId', 'tratamientos','usuario', 'usuarioRolId'));
    }


    public function registrarInicio($id)
    {
        $cita = $this->Citas->get($id);
        $cita->hora_inicio_consulta = date('H:i:s');

        if ($this->Citas->save($cita)) {
            $this->Flash->success('Inicio de consulta registrado.');
        } else {
            $this->Flash->error('No se pudo registrar.');
        }

        // Redirigir al índice manteniendo el odontólogo y la fecha seleccionados
        return $this->redirect([
            'action' => 'citaDiaria',
            '?' => [
                'doctor_id' => $cita->doctor_id,
            ]
        ]);
    }
    public function finalizarConsulta($id)
    {
        $cita = $this->Citas->get($id);
        $cita->hora_fin_consulta = date('H:i:s');
        $cita->estado = 'finalizado';

        if ($this->Citas->save($cita)) {
            $this->Flash->success('Consulta finalizada.');
        } else {
            $this->Flash->error('No se pudo finalizar.');
        }

        return $this->redirect([
            'action' => 'citaDiaria',
            '?' => [
                'doctor_id' => $cita->doctor_id,
            ]
        ]);
    }

    public function marcarHoraLlegada()
    {
        $this->request->allowMethod(['post']);
        
        try {
            $citaId = $this->request->getData('cita_id');
            $horaLlegada = $this->request->getData('hora_llegada');

            if (!$citaId || !$horaLlegada) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'Parámetros faltantes']));
            }

            $cita = $this->Citas->get($citaId);
            $cita->hora_llegada = $horaLlegada;

            if ($this->Citas->save($cita)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => true, 'message' => 'Hora de llegada registrada correctamente']));
            } else {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'No se pudo registrar la hora de llegada']));
            }
        } catch (\Exception $e) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]));
        }
    }

    // src/Controller/CitasController.php
    // src/Controller/CitasController.php


    public function actualizarHora()
    {
        $this->request->allowMethod(['post']);

        // Obtener los datos del formulario
        $citaId = $this->request->getData('cita_id');
        $nuevaFecha = $this->request->getData('nueva_fecha'); // Fecha proporcionada
        $nuevaHora = $this->request->getData('nueva_hora'); // Hora proporcionada

        // Combinar la nueva fecha con la nueva hora y agregar segundos: 'YYYY-MM-DD HH:MM:SS'
        $fechaHoraStr = $nuevaFecha . ' ' . $nuevaHora . ':00'; // Agregar los segundos (00)

        // Intentar crear un objeto DateTime para validar la fecha y hora
        try {
            $fechaHora = new \DateTime($fechaHoraStr);  // Crear un objeto DateTime con la fecha y hora proporcionada

            // Obtener la cita
            $cita = $this->Citas->get($citaId);
            $cita->fecha_hora = $fechaHora->format('Y-m-d H:i:s'); // Formatear correctamente la fecha y hora

            // Guardar la cita con la nueva fecha y hora
            if ($this->Citas->save($cita)) {
                $this->Flash->success('La hora de la cita ha sido actualizada correctamente.');
            } else {
                $this->Flash->error('No se pudo actualizar la hora de la cita.');
            }
        } catch (\Exception $e) {
            // Si no se pudo crear el objeto DateTime
            $this->Flash->error('Fecha y hora inválidas.');
        }

        // Redirigir a la vista correspondiente
        return $this->redirect(['action' => 'index']);
    }
    
     public function restablecerCita()
    {
        $this->request->allowMethod(['post']);

        $citaId = $this->request->getData('cita_id');

        if (!$citaId) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'ID de cita no proporcionado.']));
        }

        try {
            $cita = $this->Citas->get($citaId);
            $cita->estado = 'pendiente';
            $cita->hora_llegada = null;

            if ($this->Citas->save($cita)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => true, 'message' => 'Cita restablecida correctamente.']));
            } else {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'No se pudo guardar la cita.',
                        'errors' => $cita->getErrors()
                    ]));
            }
        } catch (\Exception $e) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Error del servidor.',
                    'exception' => $e->getMessage()
                ]));
        }
    }
    
     public function actualizarTablaCitas() 
    {
        // Verificar que sea una petición AJAX
        if (!$this->request->is('ajax')) {
            throw new NotFoundException('Acceso no permitido');
        }
        
        // $this->viewBuilder()->setLayout(null);
        $this->viewBuilder()->setLayout('ajax');
    
        
        // Obtener usuario actual desde la sesión
        $session = $this->request->getSession();
        $usuario = $session->read('Auth.User') ?: $session->read('Auth');
        
        // Si no hay usuario en sesión, usar valores por defecto
        if (!$usuario) {
            $usuario = ['rol' => 1, 'id' => null]; // Ajusta según tu estructura
        }
        
        $doctorId = $this->request->getQuery('doctor_id');
        
        // Reutilizar la misma lógica que en tu método index o cita_diaria
        $query = $this->Citas->find()
            ->contain(['Pacientes', 'CitasTratamientos.Tratamientos', 'Doctores'])
            ->where(['DATE(Citas.fecha_hora)' => date('Y-m-d')])
            ->order(['Citas.fecha_hora' => 'ASC']);
        
        // Filtrar por doctor si está seleccionado
        if (!empty($doctorId)) {
            $query->where(['Citas.doctor_id' => $doctorId]);
        } elseif (isset($usuario['rol']) && $usuario['rol'] == 3) { // Si es doctor, solo sus citas
            $query->where(['Citas.doctor_id' => $usuario['id']]);
        }
        
        $citas = $query->toArray();
        
        // Preparar doctores para el filtro (si es necesario)
        if (!isset($usuario['rol']) || $usuario['rol'] != 3) {
            $doctores = $this->Citas->Doctores->find('list', keyField: 'id', valueField: function ($doctor) {
                return $doctor->nombre . ' ' . $doctor->apellido;
            })->toArray();
        } else {
            $doctores = [];
        }
        
        $this->set(compact('citas', 'usuario', 'doctores'));

        // Renderizar solo el element de la tabla
        $this->render('/element/tabla_citas');
        if ($this->request->is('ajax')) {
                $this->viewBuilder()->setLayout('ajax');
            } else {
                $this->viewBuilder()->setLayout('default');
            }
    }

    /**
     * Calendario visual de citas por especialista (columnas = doctores, filas = horas)
     */
    public function calendarioEspecialistas()
    {
        $usuario = $this->Authentication->getIdentity();

        if (!$usuario) {
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        $fecha = $this->request->getQuery('fecha') ?? date('Y-m-d');

        // Roles que tienen permiso para editar citas, usado en la vista para habilitar/deshabilitar acciones
        $rolesPermitidos = $this->fetchTable('RolesPermisos')->find()
            ->distinct(['RolesPermisos.rol_id'])
            ->select(['RolesPermisos.rol_id'])
            ->innerJoinWith('Permisos')
            ->where([
                'Permisos.controller' => 'Citas',
                'Permisos.action' => 'edit',
            ])
            ->toArray();

        $rolesPermitidosIds = array_map(function ($item) {
            return $item->rol_id;
        }, $rolesPermitidos);

        $usuarioRolId = $usuario->rol_id ?? null;

        $this->set(compact('fecha', 'usuario', 'rolesPermitidosIds', 'usuarioRolId'));
    }

    /**
     * Especialistas y sus citas para una fecha dada (usado por AJAX en calendarioEspecialistas)
     */
    public function fetchEspecialistas()
    {
        $this->request->allowMethod(['get']);

        try {
            $fecha = $this->request->getQuery('fecha');

            if (!$fecha) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['error' => 'Falta la fecha']));
            }

            $fechaObj = new \DateTime($fecha, new \DateTimeZone('America/Lima'));

            $especialistas = $this->Citas->Doctores->find()
                ->select(['id', 'nombre', 'apellido'])
                ->contain(['Users'])
                ->matching('Users', function ($q) {
                    return $q->where(['Users.estado_user !=' => 'I']);
                })
                ->order(['nombre' => 'ASC'])
                ->toArray();

            $especialistasFormateados = array_map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'nombre' => trim($doctor->nombre . ' ' . $doctor->apellido),
                ];
            }, $especialistas);

            $citas = $this->Citas->find()
                ->contain(['CitasTratamientos.Tratamientos'])
                ->where([
                    'DATE(Citas.fecha_hora)' => $fechaObj->format('Y-m-d'),
                ])
                ->order(['fecha_hora' => 'ASC'])
                ->toArray();

            $pacienteIds = array_column($citas, 'paciente_id');
            $doctorIds = array_column($citas, 'doctor_id');

            $pacientes = [];
            if (!empty($pacienteIds)) {
                $pacientesData = $this->Citas->Pacientes->find()
                    ->select(['id', 'nombre', 'apellido', 'telefono_celular'])
                    ->where(['id IN' => array_unique($pacienteIds)])
                    ->toArray();

                foreach ($pacientesData as $p) {
                    $pacientes[$p->id] = $p;
                }
            }

            $doctores = [];
            if (!empty($doctorIds)) {
                $doctoresData = $this->Citas->Doctores->find()
                    ->select(['id', 'nombre', 'apellido'])
                    ->where(['id IN' => array_unique($doctorIds)])
                    ->toArray();

                foreach ($doctoresData as $d) {
                    $doctores[$d->id] = $d;
                }
            }

            $historiasClinicas = [];
            if (!empty($pacienteIds)) {
                $historias = $this->Citas->Pacientes->HistoriasClinicas->find()
                    ->select(['paciente_id', 'dni'])
                    ->where(['paciente_id IN' => array_unique($pacienteIds)])
                    ->toArray();

                foreach ($historias as $historia) {
                    $historiasClinicas[$historia->paciente_id] = $historia;
                }
            }

            $citasFormateadas = array_map(function ($cita) use ($pacientes, $doctores, $historiasClinicas) {
                $citaHoraLima = $cita->fecha_hora->setTimezone(new \DateTimeZone('America/Lima'));

                $pacienteName = 'Paciente desconocido';
                $telefonoCelular = '';
                if (isset($pacientes[$cita->paciente_id])) {
                    $paciente = $pacientes[$cita->paciente_id];
                    $pacienteName = trim($paciente->nombre . ' ' . $paciente->apellido);
                    $telefonoCelular = $paciente->telefono_celular ?? '';
                }

                $doctorName = 'Doctor no asignado';
                if (isset($doctores[$cita->doctor_id])) {
                    $doctor = $doctores[$cita->doctor_id];
                    $doctorName = trim($doctor->nombre . ' ' . $doctor->apellido);
                }

                $tieneHistoriaClinicaDni = false;
                if (isset($historiasClinicas[$cita->paciente_id])) {
                    $historia = $historiasClinicas[$cita->paciente_id];
                    $tieneHistoriaClinicaDni = !empty($historia->dni);
                }

                $tratamientos = [];
                if (!empty($cita->citas_tratamientos)) {
                    foreach ($cita->citas_tratamientos as $ct) {
                        if (!empty($ct->tratamiento)) {
                            $tratamientos[] = [
                                'id' => $ct->tratamiento->id,
                                'nombre' => $ct->tratamiento->nombre ?? '',
                            ];
                        }
                    }
                }

                return [
                    'id' => $cita->id,
                    'doctor_id' => $cita->doctor_id,
                    'fecha_hora' => $citaHoraLima->format('Y-m-d H:i:s'),
                    'duracion_minutos' => $cita->duracion_minutos,
                    'paciente_id' => $cita->paciente_id,
                    'paciente' => $pacienteName,
                    'telefono_celular' => $telefonoCelular,
                    'estado' => $cita->estado,
                    'hora_llegada' => $cita->hora_llegada,
                    'doctor' => $doctorName,
                    'tiene_historia_clinica_dni' => $tieneHistoriaClinicaDni,
                    'motivo' => $cita->motivo ?? '',
                    'tratamientos' => $tratamientos,
                ];
            }, $citas);

            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'especialistas' => $especialistasFormateados,
                    'citas' => $citasFormateadas,
                ]));
        } catch (\Exception $e) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['error' => 'Error al obtener datos: ' . $e->getMessage()]));
        }
    }

    /**
     * Mover una cita a otro horario y/o doctor (drag & drop en calendarioEspecialistas)
     */
    public function moverCita()
    {
        $this->request->allowMethod(['post']);

        try {
            $citaId = $this->request->getData('cita_id');
            $fecha = $this->request->getData('fecha');
            $hora = $this->request->getData('hora');
            $doctorId = $this->request->getData('doctor_id');

            if (!$citaId || !$fecha || !$hora || !$doctorId) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'error' => 'Faltan datos requeridos']));
            }

            $cita = $this->Citas->findById($citaId)->first();

            if (!$cita) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'error' => 'Cita no encontrada']));
            }

            $fechaHoraString = $fecha . ' ' . $hora;
            $fechaHoraNueva = new \DateTime($fechaHoraString, new \DateTimeZone('America/Lima'));

            $duracionMinutos = (int)$cita->duracion_minutos;
            $finNuevaCita = (clone $fechaHoraNueva)->modify("+{$duracionMinutos} minutes");

            // Validación 1: que no haya otra cita solapada
            $citaExistente = $this->Citas->find()
                ->where([
                    'doctor_id' => $doctorId,
                    'estado !=' => 'cancelado',
                    'Citas.id !=' => $citaId,
                    'OR' => [
                        [
                            'fecha_hora <' => $finNuevaCita->format('Y-m-d H:i:s'),
                            $this->Citas->query()->newExpr("DATE_ADD(fecha_hora, INTERVAL duracion_minutos MINUTE) > '" . $fechaHoraNueva->format('Y-m-d H:i:s') . "'"),
                        ],
                    ],
                ])
                ->first();

            if ($citaExistente) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'error' => 'El horario ya está ocupado. Hay otra cita programada en ese rango de hora.',
                    ]));
            }

            // Validación 2: horario de atención del doctor
            $HorariosDoctores = $this->fetchTable('HorariosDoctores');
            $diaSemana = (int)$fechaHoraNueva->format('w');
            $horaInicioCita = $fechaHoraNueva->format('H:i:s');

            $horarioDoctor = $HorariosDoctores->find()
                ->where([
                    'doctor_id' => $doctorId,
                    'dia_semana' => $diaSemana,
                    'hora_inicio <=' => $horaInicioCita,
                    'hora_fin >=' => $horaInicioCita,
                ])
                ->first();

            if (!$horarioDoctor) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'error' => 'El especialista no tiene horario de atención asignado en ese rango de hora.',
                    ]));
            }

            // Validación 3: bloqueos horarios
            $HorariosBloqueos = $this->fetchTable('HorariosBloqueos');
            $fechaFormat = $fechaHoraNueva->format('Y-m-d');
            $horaInicio = $fechaHoraNueva->format('H:i:s');
            $horaFin = $finNuevaCita->format('H:i:s');

            if ($HorariosBloqueos->verificarBloqueo($doctorId, $fechaFormat, $horaInicio, $horaFin)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'error' => 'No se puede agendar en ese horario. El especialista tiene bloqueado ese rango de horas.',
                    ]));
            }

            $cita->fecha_hora = $fechaHoraNueva;
            $cita->doctor_id = (int)$doctorId;

            if ($this->Citas->save($cita)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => true, 'message' => 'Cita movida correctamente']));
            } else {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'error' => 'No se pudo guardar la cita']));
            }
        } catch (\Exception $e) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]));
        }
    }
}

