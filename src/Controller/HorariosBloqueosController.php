<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * HorariosBloqueos Controller
 *
 * @property \App\Model\Table\HorariosBloqueosTable $HorariosBloqueos
 */
class HorariosBloqueosController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $currentAction = $this->request->getParam('action');
        
        $this->verificarPermisoORedireccionar('HorariosBloqueos', $currentAction);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // Obtener todos los bloqueos sin paginar, ordenados por fecha
        $query = $this->HorariosBloqueos->find()
            ->contain(['Doctores'])
            ->orderBy(['HorariosBloqueos.fecha' => 'DESC']);
        $horariosBloqueos = $query->toArray();

        // Obtener horarios de doctores
        $horariosDoctores = $this->fetchTable('HorariosDoctores')
            ->find()
            ->contain(['Doctores'])
            ->toArray();

        // Obtener lista de doctores para el selector
        $doctoresTable = $this->fetchTable('Doctores');
        $doctoresData = $doctoresTable->find()
            ->select(['id', 'nombre', 'apellido'])
            ->toArray();
        
        // Mantener array con strings para FormHelper select
        $doctoresSelect = [];
        $doctores = [];
        foreach ($doctoresData as $doctor) {
            $doctoresSelect[$doctor['id']] = $doctor['nombre'] . ' ' . $doctor['apellido'];
            $doctores[$doctor['id']] = $doctor; // Mantener objetos para el template
        }

        $this->set(compact('horariosBloqueos', 'horariosDoctores', 'doctores'));
    }
    /**
     * View method
     *
     * @param string|null $id Horarios Bloqueo id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $horariosBloqueo = $this->HorariosBloqueos->get($id, contain: ['Doctores']);
        $this->set(compact('horariosBloqueo'));
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
        $horariosBloqueo = $this->HorariosBloqueos->newEmptyEntity();
        if ($this->request->is('post')) {
            // Validar que la fecha no sea anterior a hoy
            $fechaBloqueo = new \DateTime($this->request->getData('fecha'));
            $fechaBloqueo->setTime(0, 0, 0);  // Establecer hora a medianoche
            $hoy = new \DateTime();
            $hoy->setTime(0, 0, 0);

            if ($fechaBloqueo < $hoy) {
                $this->Flash->error(__('No puedes bloquear horarios con fecha anterior a hoy.'));
                return $this->redirect(['action' => 'index']);
            }

            $horariosBloqueo = $this->HorariosBloqueos->patchEntity($horariosBloqueo, $this->request->getData());
            if ($this->HorariosBloqueos->save($horariosBloqueo)) {
                $this->Flash->success(__('El bloqueo horario ha sido guardado correctamente.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo guardar el bloqueo horario. Intenta de nuevo.'));
        }
        // Obtener doctores con nombre completo
        $doctorTable = $this->fetchTable('Doctores');
        $doctoresData = $doctorTable->find()
            ->select(['id', 'nombre', 'apellido'])
            ->toArray();
        
        $doctores = [];
        foreach ($doctoresData as $doctor) {
            $doctores[$doctor['id']] = $doctor['nombre'] . ' ' . $doctor['apellido'];
        }
        
        $this->set(compact('horariosBloqueo', 'doctores'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Horarios Bloqueo id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $horariosBloqueo = $this->HorariosBloqueos->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $horariosBloqueo = $this->HorariosBloqueos->patchEntity($horariosBloqueo, $this->request->getData());
            if ($this->HorariosBloqueos->save($horariosBloqueo)) {
                $this->Flash->success(__('El bloqueo horario ha sido actualizado correctamente.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo actualizar el bloqueo horario. Intenta de nuevo.'));
        }
        // Obtener doctores con nombre completo
        $doctorTable = $this->fetchTable('Doctores');
        $doctoresData = $doctorTable->find()
            ->select(['id', 'nombre', 'apellido'])
            ->toArray();
        
        $doctores = [];
        foreach ($doctoresData as $doctor) {
            $doctores[$doctor['id']] = $doctor['nombre'] . ' ' . $doctor['apellido'];
        }
        
        $this->set(compact('horariosBloqueo', 'doctores'));
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
     * @param string|null $id Horarios Bloqueo id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $horariosBloqueo = $this->HorariosBloqueos->get($id);
        if ($this->HorariosBloqueos->delete($horariosBloqueo)) {
            $this->Flash->success(__('El bloqueo horario ha sido eliminado correctamente.'));
        } else {
            $this->Flash->error(__('No se pudo eliminar el bloqueo horario. Intenta de nuevo.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Obtener Bloqueos Por Fecha - Devuelve todos los bloqueos de un doctor para una fecha específica
     *
     * @return void
     */
    public function obtenerBloqueosPorFecha()
    {
        $this->request->allowMethod(['get']);
        
        $fecha = $this->request->getQuery('fecha');
        $doctorId = $this->request->getQuery('doctor_id');

        if (!$fecha || !$doctorId) {
            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode(['bloqueos' => []]));
        }

        // Buscar todos los bloqueos para esta fecha y doctor
        $bloqueos = $this->HorariosBloqueos->find()
            ->where([
                'doctor_id' => $doctorId,
                'fecha' => $fecha
            ])
            ->all();

        $resultado = [];
        foreach ($bloqueos as $bloqueo) {
            $resultado[] = [
                'hora_inicio' => $bloqueo->hora_inicio,
                'hora_fin' => $bloqueo->hora_fin
            ];
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode(['bloqueos' => $resultado]));
    }

    /**
     * Verificar Bloqueo method - Verifica si existe un bloqueo para fecha/hora/doctor específicos
     *
     * @return void
     */
    public function verificarBloqueo()
    {
        $this->request->allowMethod(['get']);
        
        $fecha = $this->request->getQuery('fecha');
        $hora = $this->request->getQuery('hora');
        $doctorId = $this->request->getQuery('doctor_id');

        if (!$fecha || !$hora || !$doctorId) {
            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode(['bloqueado' => false]));
        }

        // Buscar bloqueos que contengan esta hora
        $bloqueos = $this->HorariosBloqueos->find()
            ->where([
                'doctor_id' => $doctorId,
                'fecha' => $fecha,
                'hora_inicio <=' => $hora,
                'hora_fin >=' => $hora
            ])
            ->first();

        $bloqueado = !is_null($bloqueos);

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode(['bloqueado' => $bloqueado]));
    }
}

