<?php

declare(strict_types=1);

namespace App\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;
/**
 * Recetas Controller
 *
 * @property \App\Model\Table\RecetasTable $Recetas
 */
class RecetasController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $currentAction = $this->request->getParam('action');
        
        $this->verificarPermisoORedireccionar('Recetas', $currentAction);
    }

    public function exportRecetaPdf($id = null)
    {
        $logoUrl = Router::url('/img/logoClinica.png', true);
        // Obtener los datos completos de la receta consulta
        $receta = $this->Recetas->get($id);

        // Configurar vista sin layout
        $this->viewBuilder()->enableAutoLayout(false);
        $this->set(compact('receta', 'logoUrl'));

        // Renderizar la vista específica para PDF
        $html = $this->render('receta_pdf');

        // Configurar DomPDF
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);

        // Configurar tamaño y orientación
        $dompdf->setPaper('A5', 'landscape');

        // Renderizar PDF
        $dompdf->render();

        // Generar nombre del archivo
        $filename = "Receta_Medica_{$id}.pdf";
        $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $filename); // Limpiar caracteres especiales

        // Descargar el PDF
        $dompdf->stream($filename, ['Attachment' => 1]);
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Recetas->find();
        $recetas = $this->paginate($query);
        $query->order(['Recetas.id' => 'DESC']);

        $searchTerm = $this->request->getQuery('search');

        if (!empty($searchTerm)) {
            $query->where([
                'OR' => [
                    'LOWER(Recetas.nombre) LIKE' => '%' . strtolower($searchTerm) . '%',
                ]
            ]);
        }

        $recetas = $this->paginate($query);

        $this->set(compact('recetas', 'searchTerm'));
    }

    /**
     * View method
     *
     * @param string|null $id Receta id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $receta = $this->Recetas->get($id, contain: ['Consultas']);
        $this->set(compact('receta'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
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
        $receta = $this->Recetas->newEmptyEntity();
        if ($this->request->is('post')) {
            $receta = $this->Recetas->patchEntity($receta, $this->request->getData());
            if ($this->Recetas->save($receta)) {
                $this->Flash->success(__('The receta has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The receta could not be saved. Please, try again.'));
        }
        $consultas = $this->Recetas->Consultas->find('list', limit: 200)->all();
        $this->set(compact('receta', 'consultas'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Receta id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $receta = $this->Recetas->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $receta = $this->Recetas->patchEntity($receta, $this->request->getData());
            if ($this->Recetas->save($receta)) {
                $this->Flash->success(__('The receta has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The receta could not be saved. Please, try again.'));
        }
        $consultas = $this->Recetas->Consultas->find('list', limit: 200)->all();
        $this->set(compact('receta', 'consultas'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Add From Consulta With Medicamentos
     * Crea una receta desde una consulta con todos los medicamentos de una vez
     */
    public function addFromConsultaWithMeds($consultaId = null)
    {
        if (!$consultaId) {
            $this->Flash->error('Consulta no especificada.');
            return $this->redirect(['action' => 'index']);
        }

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            // Crear la receta
            $receta = $this->Recetas->newEntity([
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'] ?? null,
                'notas' => $data['notas'] ?? null
            ]);

            if ($this->Recetas->save($receta)) {
                // Asociar receta con consulta
                $recetasConsultasTable = TableRegistry::getTableLocator()->get('RecetasConsultas');
                $asociacion = $recetasConsultasTable->newEntity([
                    'receta_id' => $receta->id,
                    'consulta_id' => $consultaId
                ]);
                $recetasConsultasTable->save($asociacion);

                // Guardar medicamentos
                $medicamentos = json_decode($data['medicamentos'], true);
                if (!empty($medicamentos)) {
                    $recetasMedicamentosTable = TableRegistry::getTableLocator()->get('RecetasMedicamentos');
                    
                    foreach ($medicamentos as $med) {
                        $recetaMedicamento = $recetasMedicamentosTable->newEntity([
                            'receta_id' => $receta->id,
                            'medicamento_id' => $med['medicamento_id'],
                            'concentracion' => $med['concentracion'],
                            'cantidad' => $med['cantidad'],
                            'forma_farmaceutica_id' => $med['forma_farmaceutica_id'],
                            'via_administracion_id' => $med['via_administracion_id'],
                            'duracion_dias' => $med['duracion_dias'],
                            'dosis' => $med['dosis'] ?? '',
                            'observaciones' => $med['observaciones']
                        ]);
                        $recetasMedicamentosTable->save($recetaMedicamento);
                    }
                    
                    // Actualizar indicaciones consolidadas
                    $this->_actualizarIndicacionesReceta($receta->id);
                }

                $this->Flash->success('¡Receta creada exitosamente con ' . count($medicamentos) . ' medicamento(s)!');
                return $this->redirect(['action' => 'view', $receta->id]);
            }

            $this->Flash->error('No se pudo guardar la receta. Intenta de nuevo.');
        }

        // Obtener consulta
        $consultasTable = TableRegistry::getTableLocator()->get('Consultas');
        $consulta = $consultasTable->get($consultaId, contain: ['Doctores', 'HistoriasClinicas']);

        $this->set(compact('consulta', 'consultaId'));
        
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Actualizar indicaciones consolidadas de la receta
     *
     * @param int $recetaId ID de la receta
     * @return void
     */
    private function _actualizarIndicacionesReceta($recetaId)
    {
        try {
            $recetas = TableRegistry::getTableLocator()->get('Recetas');
            $medicamentos = $recetas->RecetasMedicamentos->find()
                ->where(['receta_id' => $recetaId])
                ->contain(['Medicamentos', 'FormasFarmaceuticas', 'ViasAdministracion'])
                ->all();

            $indicaciones = [];
            foreach ($medicamentos as $med) {
                $linea = strtoupper($med->medicamento->nombre);
                
                if ($med->concentracion) {
                    $linea .= ' ' . $med->concentracion;
                }
                
                if ($med->forma_farmaceutica) {
                    $linea .= ' - ' . $med->forma_farmaceutica->nombre;
                }
                
                if ($med->cantidad) {
                    $linea .= ' (' . $med->cantidad . ' unidades)';
                }
                
                if ($med->via_administracion) {
                    $linea .= ' - VÍA: ' . strtoupper($med->via_administracion->nombre);
                }
                
                if ($med->duracion_dias) {
                    $linea .= ' - DURACIÓN: ' . $med->duracion_dias . ' días';
                }
                
                if ($med->observaciones) {
                    $linea .= ' - NOTA: ' . $med->observaciones;
                }
                
                $indicaciones[] = $linea;
            }

            $receta = $recetas->get($recetaId);
            $receta->indicaciones_consolidadas = implode("\n", $indicaciones);
            $recetas->save($receta);
        } catch (\Exception $e) {
            // Log silencioso de errores
        }
    }

    /**
     * Add From Consulta method
     * Crea una receta desde una consulta específica
     */
    public function addFromConsulta($consultaId = null)
    {
        if (!$consultaId) {
            $this->Flash->error('Consulta no especificada.');
            return $this->redirect(['action' => 'index']);
        }

        // Obtener consulta
        $consultasTable = TableRegistry::getTableLocator()->get('Consultas');
        $consulta = $consultasTable->get($consultaId, contain: ['Doctores', 'HistoriasClinicas']);

        $receta = $this->Recetas->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['consulta_id'] = $consultaId;

            $receta = $this->Recetas->patchEntity($receta, $data);

            if ($this->Recetas->save($receta)) {
                // Asociar receta con consulta
                $recetasConsultasTable = TableRegistry::getTableLocator()->get('RecetasConsultas');
                
                $asociacion = $recetasConsultasTable->newEntity([
                    'receta_id' => $receta->id,
                    'consulta_id' => $consultaId
                ]);
                $recetasConsultasTable->save($asociacion);

                $this->Flash->success('Receta creada correctamente. Ahora puedes agregar medicamentos.');

                // Redirigir para agregar medicamentos a la receta
                return $this->redirect(['controller' => 'RecetasMedicamentos', 'action' => 'add', '?' => ['receta_id' => $receta->id]]);
            }

            $this->Flash->error('No se pudo guardar la receta. Intenta de nuevo.');
        }

        $this->set(compact('receta', 'consulta', 'consultaId'));
        
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Get by Consulta method
     * Obtiene las recetas asociadas a una consulta específica
     */
    public function getByConsulta()
    {
        $this->request->allowMethod(['get']);
        
        $consultaId = $this->request->getQuery('consulta_id');
        
        if (!$consultaId) {
            $this->response = $this->response->withStatus(400);
            return $this->response->withType('application/json')->withStringBody(json_encode(['error' => 'Consulta ID requerido']));
        }

        // Obtener recetas através de la tabla intermedia RecetasConsultas
        $recetasConsultasTable = TableRegistry::getTableLocator()->get('RecetasConsultas');
        $recetasConsultas = $recetasConsultasTable->find()
            ->where(['consulta_id' => $consultaId])
            ->toArray();

        $recetas = [];
        foreach ($recetasConsultas as $recetaConsulta) {
            $receta = $this->Recetas->get($recetaConsulta->receta_id, contain: [
                'RecetasMedicamentos' => [
                    'Medicamentos',
                    'FormasFarmaceuticas',
                    'ViasAdministracion'
                ]
            ]);
            
            // Mapear datos para incluir los nombres de las relaciones
            $recetaData = [
                'id' => $receta->id,
                'nombre' => $receta->nombre,
                'descripcion' => $receta->descripcion,
                'notas' => $receta->notas,
                'tipo_usuario' => $receta->tipo_usuario,
                'tipo_atencion' => $receta->tipo_atencion,
                'especialidad_medica' => $receta->especialidad_medica,
                'h_cl' => $receta->h_cl,
                'sis' => $receta->sis,
                'particular' => $receta->particular,
                'peso' => $receta->peso,
                'recetas_medicamentos' => []
            ];
            
            if (!empty($receta->recetas_medicamentos)) {
                foreach ($receta->recetas_medicamentos as $med) {
                    $formaNombre = '';
                    if (!empty($med->forma_farmaceutica_id) && !empty($med->forma_farmaceutica)) {
                        $formaNombre = $med->forma_farmaceutica->nombre ?? '';
                    }
                    
                    $viaNombre = '';
                    if (!empty($med->via_administracion_id) && !empty($med->via_administracion)) {
                        $viaNombre = $med->via_administracion->nombre ?? '';
                    }
                    
                    $medicamentoData = [
                        'id' => $med->id,
                        'medicamento' => [
                            'id' => $med->medicamento->id,
                            'codigo' => $med->medicamento->codigo,
                            'nombre' => $med->medicamento->nombre,
                            'concentracion' => $med->medicamento->concentracion
                        ],
                        'cantidad' => $med->cantidad,
                        'forma_farmaceutica_id' => $med->forma_farmaceutica_id,
                        'forma_farmaceutica_nombre' => $formaNombre,
                        'via_administracion_id' => $med->via_administracion_id,
                        'via_administracion_nombre' => $viaNombre,
                        'duracion_dias' => $med->duracion_dias,
                        'observaciones' => $med->observaciones
                    ];
                    
                    $recetaData['recetas_medicamentos'][] = $medicamentoData;
                }
            }
            
            $recetas[] = $recetaData;
        }

        $this->response = $this->response->withType('application/json');
        return $this->response->withStringBody(json_encode($recetas));
    }

    /**
     * Export Receta PDF with Consulta information
     * 
     * @param string|null $id Receta id.
     * @return void
     */
    public function exportPdf($id = null)
    {
        $logoUrl = Router::url('/img/logoClinica.png', true);
        
        $receta = $this->Recetas->get($id, [
            'contain' => [
                'RecetasMedicamentos' => [
                    'Medicamentos',
                    'FormasFarmaceuticas',
                    'ViasAdministracion'
                ],
                'RecetasConsultas' => [
                    'Consultas' => [
                        'HistoriasClinicas' => [
                            'Pacientes'
                        ],
                        'Doctores'
                    ]
                ]
            ]
        ]);

        // Obtener la consulta asociada
        $consulta = null;
        $historiasClinicas = null;
        
        if (!empty($receta->recetas_consultas) && count($receta->recetas_consultas) > 0) {
            $consulta = $receta->recetas_consultas[0]->consulta;
            
            // Cargar datos adicionales de la consulta si no están disponibles
            if (!empty($consulta->id)) {
                $consultasTable = TableRegistry::getTableLocator()->get('Consultas');
                $consulta = $consultasTable->get($consulta->id, [
                    'contain' => [
                        'Doctores',
                        'ConsultasCie' => [
                            'Diagnosticoscie10',
                            'Categoriascie10'
                        ]
                    ]
                ]);
                
                // Cargar la historia clínica explícitamente si tenemos el historia_id
                if (!empty($consulta->historia_id)) {
                    $historiasClinicasTable = TableRegistry::getTableLocator()->get('HistoriasClinicas');
                    $historiasClinicas = $historiasClinicasTable->get($consulta->historia_id, [
                        'contain' => [
                            'Pacientes'
                        ]
                    ]);
                }
            }
        }

        // Configurar vista sin layout
        $this->viewBuilder()->enableAutoLayout(false);
        $this->set(compact('receta', 'consulta', 'historiasClinicas', 'logoUrl'));

        // Renderizar la vista específica para PDF
        $html = $this->render('receta_pdf_full');

        // Configurar DomPDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);

        // Configurar tamaño y orientación
        $dompdf->setPaper('A5', 'landscape');

        // Renderizar PDF
        $dompdf->render();

        // Generar nombre del archivo
        $filename = "Receta_{$receta->nombre}_{$id}.pdf";
        $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $filename); // Limpiar caracteres especiales

        // Descargar el PDF
        $dompdf->stream($filename, ['Attachment' => 1]);
    }

    /**
     * Delete method
     *
     * @param string|null $id Receta id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $receta = $this->Recetas->get($id);
        if ($this->Recetas->delete($receta)) {
            $this->Flash->success(__('The receta has been deleted.'));
        } else {
            $this->Flash->error(__('The receta could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

