<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * Doctores Controller
 *
 * @property \App\Model\Table\DoctoresTable $Doctores
 */
class DoctoresController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $currentAction = $this->request->getParam('action');
        
        $this->verificarPermisoORedireccionar('Doctores', $currentAction);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Doctores->find();
        $doctores = $this->paginate($query);

        $this->set(compact('doctores'));
    }

    /**
     * View method
     *
     * @param string|null $id Doctore id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $doctore = $this->Doctores->get($id, contain: ['DoctorTratamientoTarifas' => ['Tratamientos']]);

        $tarifasFijas = [];
        if ($doctore->modo_pago === 'FIJO') {
            foreach ($doctore->doctor_tratamiento_tarifas as $tarifa) {
                $tarifasFijas[] = [
                    'tratamiento' => $tarifa->tratamiento->nombre ?? '-',
                    'monto_fijo' => (float) $tarifa->monto_fijo,
                ];
            }
        }

        $this->set(compact('doctore', 'tarifasFijas'));
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
        $doctore = $this->Doctores->newEmptyEntity();
        if ($this->request->is('post')) {
            $doctore = $this->Doctores->patchEntity($doctore, $this->request->getData());

            // En modo FIJO el pago se calcula por tarifa de tratamiento, no por
            // porcentaje: se limpia para evitar que quede un valor obsoleto que
            // pueda usarse por error si el doctor vuelve a modo PORCENTAJE.
            if ($doctore->modo_pago === 'FIJO') {
                $doctore->porcentaje_pago = 0;
            }

            if ($this->Doctores->save($doctore)) {
                $this->Flash->success(__('The doctore has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The doctore could not be saved. Please, try again.'));
        }
        $this->set(compact('doctore'));
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
     * @param string|null $id Doctore id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $doctore = $this->Doctores->get($id, contain: ['DoctorTratamientoTarifas']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $tarifasData = $data['tarifas'] ?? [];
            unset($data['tarifas']);

            $doctore = $this->Doctores->patchEntity($doctore, $data);

            // En modo FIJO el pago se calcula por tarifa de tratamiento, no por
            // porcentaje: se limpia para evitar que quede un valor obsoleto que
            // pueda usarse por error si el doctor vuelve a modo PORCENTAJE.
            if ($doctore->modo_pago === 'FIJO') {
                $doctore->porcentaje_pago = 0;
            }

            if ($this->Doctores->save($doctore)) {
                if ($doctore->modo_pago === 'FIJO') {
                    $DoctorTratamientoTarifas = $this->fetchTable('DoctorTratamientoTarifas');

                    foreach ($tarifasData as $tratamientoId => $monto) {
                        $tratamientoId = (int) $tratamientoId;
                        $montoTexto = trim((string) $monto);

                        $tarifa = $DoctorTratamientoTarifas->find()
                            ->where(['doctor_id' => $doctore->id, 'tratamiento_id' => $tratamientoId])
                            ->first();

                        if ($montoTexto === '') {
                            // Campo vacío: usar el default del tratamiento, quitar
                            // el override si existía.
                            if ($tarifa) {
                                $DoctorTratamientoTarifas->delete($tarifa);
                            }
                            continue;
                        }

                        $monto = (float) $montoTexto;

                        if (!$tarifa) {
                            $tarifa = $DoctorTratamientoTarifas->newEntity([
                                'doctor_id' => $doctore->id,
                                'tratamiento_id' => $tratamientoId,
                            ]);
                        }

                        $tarifa->monto_fijo = $monto;
                        $DoctorTratamientoTarifas->save($tarifa);
                    }
                }

                $this->Flash->success(__('The doctore has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The doctore could not be saved. Please, try again.'));
        }

        $tratamientos = $this->fetchTable('Tratamientos')->find()
            ->select(['id', 'nombre', 'costo', 'monto_fijo_pago'])
            ->order(['nombre' => 'ASC'])
            ->all()
            ->toList();

        $tarifasActuales = [];
        foreach ($doctore->doctor_tratamiento_tarifas as $tarifa) {
            $tarifasActuales[$tarifa->tratamiento_id] = (float) $tarifa->monto_fijo;
        }

        $this->set(compact('doctore', 'tratamientos', 'tarifasActuales'));
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
     * @param string|null $id Doctore id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $doctore = $this->Doctores->get($id);
        if ($this->Doctores->delete($doctore)) {
            $this->Flash->success(__('The doctore has been deleted.'));
        } else {
            $this->Flash->error(__('The doctore could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

