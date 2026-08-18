<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * Documentos Controller
 *
 * @property \App\Model\Table\DocumentosTable $Documentos
 */
class DocumentosController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $currentAction = $this->request->getParam('action');
        
        $this->verificarPermisoORedireccionar('Documentos', $currentAction);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Documentos->find()
            ->contain(['HistoriasClinicas']);
        $documentos = $this->paginate($query);

        $this->set(compact('documentos'));
    }

    /**
     * View method
     *
     * @param string|null $id Documento id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $documento = $this->Documentos->get($id, contain: [
            'HistoriasClinicas' => ['Pacientes'], // Asegurar que se traiga el paciente dentro de Historia
        ]);
        $this->set(compact('documento'));
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
        $documento = $this->Documentos->newEmptyEntity();
        
        $historia = null;

        // Si se pasa un historia_id, asignarlo a la consulta
        if ($historia_id) {
            $historia = $this->Documentos->HistoriasClinicas->get($historia_id, contain : ['Pacientes']);
            $documento->historia_id = $historia_id;
            $documento->paciente_id = $historia->paciente_id;
        }

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Manejo del archivo subido
            $archivo = $data['ruta_archivo'];
            if (!empty($archivo->getClientFilename())) {
                // Crear directorio si no existe
                $tipo = $data['tipo']; // 'radiografia' o 'foto_avance'
                $dir = WWW_ROOT . "uploads/HistoriaClinica/{$historia_id}/{$tipo}s/";

                // Crear carpeta si no existe
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                // Generar un nombre único para el archivo
                $filename = time() . '_' . $archivo->getClientFilename();
                $filepath = $dir . $filename;

                // Guardar el archivo en el servidor
                $archivo->moveTo($filepath);

                // Guardar la ruta del archivo en los datos
                $data['ruta_archivo'] = "uploads/HistoriaClinica/{$historia_id}/{$tipo}s/{$filename}";
            }

            // Crear la entidad con los datos del formulario
            $documento = $this->Documentos->patchEntity($documento, $data);

            // Guardar en la base de datos
            if ($this->Documentos->save($documento)) {
                $this->Flash->success(__('El archivo ha sido subido y guardado correctamente.'));

                // Redirigir al view del paciente
                if ($historia_id) {
                    return $this->redirect(['controller' => 'Pacientes', 'action' => 'view', $historia->paciente_id, '#' => 'ficha']);
                }

                // Si no hay historia_id, redirigir al índice
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('El archivo no pudo ser subido. Por favor, inténtelo nuevamente.'));
        }

        // Cargar la lista de pacientes solo si no hay historia_id
        $pacientes = $historia_id ? null : $this->Documentos->HistoriasClinicas->Pacientes->find('list')->all();
        $historias = $this->Documentos->HistoriasClinicas->find('list',
            keyField : 'id',
            valueField : function ($historia) {
                return $historia->paciente->nombre . ' ' . $historia->paciente->apellido;
            }
        )->contain(['Pacientes'])->toArray();

        $this->set(compact('documento', 'pacientes','historias','historia'));
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
     * @param string|null $id Documento id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $documento = $this->Documentos->get($id, contain: []);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Manejo del archivo subido
            $archivo = $data['ruta_archivo'];
            if (!empty($archivo->getClientFilename())) {
                // Ruta del archivo existente
                $oldFilePath = WWW_ROOT . $documento->ruta_archivo;

                // Crear directorio para el nuevo archivo
                $historiaID = $data['historia_id'];
                $tipo = $data['tipo'];
                $dir = WWW_ROOT . "uploads/HistoriaClinica/{$historiaID}/{$tipo}s/";

                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                // Generar nombre para el nuevo archivo
                $filename = time() . '_' . $archivo->getClientFilename();
                $filepath = $dir . $filename;

                // Guardar el nuevo archivo en el servidor
                $archivo->moveTo($filepath);

                // Guardar la nueva ruta en los datos
                $data['ruta_archivo'] = "uploads/HistoriaClinica/{$historiaID}/{$tipo}s/{$filename}";

                // Eliminar el archivo antiguo si existe
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            } else {
                // Mantener la ruta del archivo existente si no se sube uno nuevo
                unset($data['ruta_archivo']);
            }

            // Crear la entidad con los datos actualizados
            $documento = $this->Documentos->patchEntity($documento, $data);

            // Guardar en la base de datos
            if ($this->Documentos->save($documento)) {
                $this->Flash->success(__('El archivo ha sido actualizado correctamente.'));

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('El archivo no pudo ser actualizado. Por favor, inténtelo nuevamente.'));
        }

        $pacientes = $this->Documentos->HistoriasClinicas->Pacientes->find('list')->all();

        $this->set(compact('documento', 'pacientes'));
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
     * @param string|null $id Documento id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $documento = $this->Documentos->get($id);
        if ($this->Documentos->delete($documento)) {
            $this->Flash->success(__('The documento has been deleted.'));
        } else {
            $this->Flash->error(__('The documento could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

