<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * Procedimientos Controller
 *
 * @property \App\Model\Table\ProcedimientosTable $Procedimientos
 */
class ProcedimientosController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $currentAction = $this->request->getParam('action');
        
        $this->verificarPermisoORedireccionar('Procedimientos', $currentAction);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Procedimientos->find();
        $procedimientos = $this->paginate($query);

        $this->set(compact('procedimientos'));
    }

    /**
     * View method
     *
     * @param string|null $id Procedimiento id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
        public function view($id = null)
    {
        $procedimiento = $this->Procedimientos->get($id, contain: ['DocumentosProcedimientos.Documentos', 'HistoriasClinicas' => ['Pacientes'], 'Doctores']);
        $this->set(compact('procedimiento'));
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
    public function add($historia_id = null)
    {
        $procedimiento = $this->Procedimientos->newEmptyEntity();
        $usuario = $this->Authentication->getIdentity();

        if ($historia_id) {
            $historia = $this->Procedimientos->HistoriasClinicas->get($historia_id, contain: ['Pacientes']);
            $procedimiento->historia_id = $historia_id;
            $procedimiento->paciente_id = $historia->paciente_id;
        }

        if ($usuario->rol_id == 2) {
            $procedimiento->doctor_id = $usuario->doctor_id;
        }

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['user_id'] = $usuario->id;

            if ($usuario->rol_id == 2) {
                $data['doctor_id'] = $usuario->doctor_id;
            }

            if ($historia_id) {
                $data['historia_id'] = $historia_id;
                $data['paciente_id'] = $historia->paciente_id;
            } else {
                if (empty($data['paciente_id'])) {
                    $this->Flash->error(__('Debe seleccionar un paciente.'));
                    return $this->redirect($this->referer());
                }

                $historia = $this->Procedimientos->HistoriasClinicas->find()
                    ->where(['paciente_id' => $data['paciente_id']])
                    ->first();

                if (!$historia) {
                    $this->Flash->error(__('No se encontró una historia clínica para el paciente seleccionado.'));
                    return $this->redirect($this->referer());
                }

                $data['historia_id'] = $historia->id;
            }

            $procedimiento = $this->Procedimientos->patchEntity($procedimiento, $data);
            if ($this->Procedimientos->save($procedimiento)) {
                // ✅ Bloque para guardar documentos
                if (!empty($this->request->getUploadedFiles()['documentos'])) {
                    $documentos = $this->request->getUploadedFiles()['documentos'];

                    $documentosTable = $this->fetchTable('Documentos');
                    $documentosProcedimientosTable = $this->fetchTable('DocumentosProcedimientos');

                    foreach ($documentos as $archivo) {
                        if (!empty($archivo->getClientFilename())) {
                            $dir = WWW_ROOT . "uploads/HistoriaClinica/{$procedimiento->historia_id}/Procedimientos/";
                            if (!is_dir($dir)) {
                                mkdir($dir, 0755, true);
                            }

                            $filename = time() . '_' . $archivo->getClientFilename();
                            $filepath = $dir . $filename;

                            if ($archivo->getError() === UPLOAD_ERR_OK) {
                                $archivo->moveTo($filepath);

                                if (file_exists($filepath)) {
                                    $documento = $documentosTable->newEntity([
                                        'historia_id' => $procedimiento->historia_id,
                                        'ruta_archivo' => "uploads/HistoriaClinica/{$procedimiento->historia_id}/Procedimientos/{$filename}",
                                        'tipo' => 'procedimiento'
                                    ]);

                                    if ($documentosTable->save($documento)) {
                                        $documentoProcedimiento = $documentosProcedimientosTable->newEntity([
                                            'procedimiento_id' => $procedimiento->id,
                                            'documento_id' => $documento->id
                                        ]);
                                        $documentosProcedimientosTable->save($documentoProcedimiento);
                                    }
                                } else {
                                    $this->Flash->error(__('Hubo un problema al guardar el archivo en el servidor.'));
                                }
                            } else {
                                $this->Flash->error(__('Error al subir el archivo: ' . $archivo->getError()));
                            }
                        }
                    }
                }

                $this->Flash->success(__('El procedimiento fue guardado correctamente.'));
                return $this->redirect(['controller' => 'Pacientes', 'action' => 'view', $data['paciente_id'], '#' => 'consultas']);
            }

            $this->Flash->error(__('No se pudo guardar el procedimiento. Intente nuevamente.'));
        }

        $users = $this->Procedimientos->Users->find('list')->all();

        $historias = $this->Procedimientos->HistoriasClinicas->find(
            'list',
            keyField: 'id',
            valueField: function ($row) {
                return $row->paciente->nombre . ' ' . $row->paciente->apellido;
            },
            contain: ['Pacientes']
        )->toArray();

        $doctores = $this->Procedimientos->Doctores->find(
            'list',
            keyField: 'id',
            valueField: function ($row) {
                return $row->nombre . ' ' . $row->apellido;
            }
        )->toArray();

        $this->set(compact('procedimiento', 'users', 'historias', 'doctores', 'historia'));

        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Procedimiento id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $procedimiento = $this->Procedimientos->get($id, contain: ['HistoriasClinicas' => ['Pacientes'], 'Doctores', 'Users', 'DocumentosProcedimientos.Documentos']);
        $user = $this->Authentication->getIdentity();
        if ($user->rol_id == 2) {
            $procedimiento->doctor_id = $user->doctor_id;
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $data['user_id'] = $user->id;
            $procedimiento = $this->Procedimientos->patchEntity($procedimiento, $data);
            if ($this->Procedimientos->save($procedimiento)) {
                // ✅ Manejo de documentos subidos
                if (!empty($this->request->getUploadedFiles()['documentos'])) {
                    $documentos = $this->request->getUploadedFiles()['documentos'];
                    $documentosTable = $this->fetchTable('Documentos');
                    $documentosProcedimientosTable = $this->fetchTable('DocumentosProcedimientos');

                    foreach ($documentos as $archivo) {
                        if (!empty($archivo->getClientFilename())) {
                            $dir = WWW_ROOT . "uploads/HistoriaClinica/{$procedimiento->historia_id}/Procedimientos/";
                            if (!is_dir($dir)) {
                                mkdir($dir, 0755, true);
                            }

                            $filename = time() . '_' . $archivo->getClientFilename();
                            $filepath = $dir . $filename;
                            $archivo->moveTo($filepath);

                            if (file_exists($filepath)) {
                                $documento = $documentosTable->newEntity([
                                    'historia_id' => $procedimiento->historia_id,
                                    'ruta_archivo' => "uploads/HistoriaClinica/{$procedimiento->historia_id}/Procedimientos/{$filename}",
                                    'tipo' => 'procedimiento'
                                ]);

                                if ($documentosTable->save($documento)) {
                                    $docProcedimiento = $documentosProcedimientosTable->newEntity([
                                        'procedimiento_id' => $procedimiento->id,
                                        'documento_id' => $documento->id
                                    ]);
                                    $documentosProcedimientosTable->save($docProcedimiento);
                                }
                            }
                        }
                    }
                }
                $this->Flash->success(__('The procedimiento has been saved.'));

                return $this->redirect(['controller' => 'Pacientes', 'action' => 'view', $procedimiento->historias_clinica->paciente_id, '#' => 'consultas']);
            }
            $this->Flash->error(__('The procedimiento could not be saved. Please, try again.'));
        }
        $users = $this->Procedimientos->Users->find('list')->all();
        $doctores = $this->Procedimientos->Doctores->find(
            'list',
            keyField: 'id',
            valueField: function ($row) {
                return $row->nombre . ' ' . $row->apellido;
            }
        )->toArray();
        $historias = $this->Procedimientos->HistoriasClinicas->find(
            'list',
            keyField: 'id',
            valueField: function ($row) {
                return $row->paciente->nombre . ' ' . $row->paciente->apellido;
            },
            contain: ['Pacientes']
        )->toArray();
        $this->set(compact('procedimiento', 'users', 'doctores', 'historias'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Procedimiento id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $procedimiento = $this->Procedimientos->get($id);
        if ($this->Procedimientos->delete($procedimiento)) {
            $this->Flash->success(__('The procedimiento has been deleted.'));
        } else {
            $this->Flash->error(__('The procedimiento could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

