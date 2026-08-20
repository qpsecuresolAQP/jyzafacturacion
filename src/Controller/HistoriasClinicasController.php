<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * HistoriasClinicas Controller
 *
 * @property \App\Model\Table\HistoriasClinicasTable $HistoriasClinicas
 */
class HistoriasClinicasController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $currentAction = $this->request->getParam('action');

        // DEBUG: mostrar qué acción llega aquí
        error_log('HistoriasClinicas beforeFilter action=' . $currentAction);

        $this->verificarPermisoORedireccionar('HistoriasClinicas', $currentAction);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->HistoriasClinicas->find()
            ->contain(['Pacientes']);
        $historiasClinicas = $this->paginate($query);

        $this->set(compact('historiasClinicas'));
    }

    /**
     * View method
     *
     * @param string|null $id Historias Clinica id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $historiasClinica = $this->HistoriasClinicas->get($id, contain: ['Pacientes']);
        $this->set(compact('historiasClinica'));
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
    public function add($paciente_id = null)
    {
        $historiasClinica = $this->HistoriasClinicas->newEmptyEntity();

        if ($this->request->is('post')) {
            $historiasClinica = $this->HistoriasClinicas->patchEntity($historiasClinica, $this->request->getData());

            if ($this->HistoriasClinicas->save($historiasClinica)) {
                $this->Flash->success(__('La historia clínica ha sido guardada correctamente.'));

                return $this->redirect(['controller' => 'Pacientes', 'action' => 'view', $historiasClinica->paciente_id]);
            }
            $this->Flash->error(__('No se pudo guardar la historia clínica. Inténtelo de nuevo.'));
        }

        // Si se proporciona un ID de paciente, lo asignamos automáticamente
        $paciente = null;
        $pacientes = [];

        if ($paciente_id) {
            $paciente = $this->HistoriasClinicas->Pacientes->get($paciente_id);
            $historiasClinica->paciente_id = $paciente_id; // Asignar automáticamente
        } else {
            $pacientes = $this->HistoriasClinicas->Pacientes->find('list', limit: 200)->all();
        }

        $users = $this->HistoriasClinicas->Users->find('list', limit: 200)->all();
        $this->set(compact('historiasClinica', 'pacientes', 'paciente', 'users'));

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
     * @param string|null $id Historias Clinica id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($paciente_id = null)
    {
        // Buscar la historia clínica asociada al paciente
        $historiasClinica = $this->HistoriasClinicas->find()
            ->contain(['Users', 'Pacientes'])
            ->where(['paciente_id' => $paciente_id])
            ->first();

        if (!$historiasClinica) {
            $this->Flash->error(__('No se encontró la historia clínica para este paciente.'));
            return $this->redirect(['controller' => 'Pacientes', 'action' => 'view', $paciente_id]);
        }

        // Si se envió el formulario
        if ($this->request->is(['patch', 'post', 'put'])) {
            $historiasClinica = $this->HistoriasClinicas->patchEntity($historiasClinica, $this->request->getData());
            if ($this->HistoriasClinicas->save($historiasClinica)) {
                $this->Flash->success(__('La historia clínica ha sido actualizada.'));
                return $this->redirect(['controller' => 'Pacientes', 'action' => 'view', $paciente_id, '#' => 'antecedentes']);
            }
            $this->Flash->error(__('La historia clínica no pudo ser actualizada. Intenta nuevamente.'));
        }

        // Datos adicionales para el formulario
        $pacientes = $this->HistoriasClinicas->Pacientes->find('list', limit: 200)->all();
        $users = $this->HistoriasClinicas->Users->find('list', limit: 200)->all();

        $this->set(compact(
            'historiasClinica',
            'pacientes',
            'users'
        ));

        // Layout según si es solicitud AJAX o no
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
     * @param string|null $id Historias Clinica id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $historiasClinica = $this->HistoriasClinicas->get($id);
        if ($this->HistoriasClinicas->delete($historiasClinica)) {
            $this->Flash->success(__('The historias clinica has been deleted.'));
        } else {
            $this->Flash->error(__('The historias clinica could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Cumpleanos Recordatorio method
     *
     * @param string|null $id Historias Clinica id.
     * @return void Renders view
     */
    public function cumpleanosRecordatorio($id = null)
    {
        // Debug: output para ver si llega aquí
        error_log("cumpleanosRecordatorio called with id: " . $id);
        
        if (!$id) {
            return $this->redirect(['action' => 'index']);
        }
        
        try {
            $historia = $this->HistoriasClinicas->get($id, [
                'contain' => ['Pacientes']
            ]);
            
            error_log("Historia loaded: " . print_r($historia->toArray(), true));
            $this->set(compact('historia'));
        } catch (\Exception $e) {
            error_log("Error loading historia: " . $e->getMessage());
            $this->Flash->error('Error al cargar el recordatorio');
            return $this->redirect(['action' => 'index']);
        }
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

}