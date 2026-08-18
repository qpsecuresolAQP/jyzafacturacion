<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * HorariosDoctores Controller
 *
 * @property \App\Model\Table\HorariosDoctoresTable $HorariosDoctores
 */
class HorariosDoctoresController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $currentAction = $this->request->getParam('action');
        
        $this->verificarPermisoORedireccionar('HorariosDoctores', $currentAction);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->HorariosDoctores->find()
            ->contain(['Doctores']);
        $horariosDoctores = $this->paginate($query);

        $this->set(compact('horariosDoctores'));
    }

    /**
     * View method
     *
     * @param string|null $id Horarios Doctore id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Obtener el doctor con su relación de horarios
        $doctor = $this->HorariosDoctores->Doctores->get($id, contain: ['HorariosDoctores']);

        // Obtener todos los horarios del doctor (si ya los tenemos)
        $horariosDoctores = $this->HorariosDoctores->find()
            ->where(['doctor_id' => $id]) // Filtramos por el ID del doctor
            ->all();

        // Pasamos el doctor y sus horarios a la vista
        $this->set(compact('doctor', 'horariosDoctores'));

        // Si es una petición AJAX, usamos un layout específico
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    // editar horarios
    // HorariosDoctoresController.php
    // public function editAll($doctorId)
    // {
    //     // Obtener los horarios del doctor específico
    //     $horariosDoctores = $this->HorariosDoctores->find('all', [
    //         'conditions' => ['doctor_id' => $doctorId]
    //     ])->toArray();

    //     if ($this->request->is(['patch', 'post', 'put'])) {
    //         // Recibir los datos enviados desde el formulario
    //         $horariosData = $this->request->getData('horarios');

    //         // Actualizar cada horario con los nuevos datos
    //         foreach ($horariosDoctores as $index => $horario) {
    //             // Asegurarse de que el índice exista en los datos del formulario
    //             if (isset($horariosData[$index])) {
    //                 $horario = $this->HorariosDoctores->patchEntity($horario, $horariosData[$index]);

    //                 if ($this->HorariosDoctores->save($horario)) {
    //                     // Si se guarda correctamente, se pueden agregar mensajes de éxito
    //                 } else {
    //                     // Si no se guarda correctamente, puedes agregar mensajes de error
    //                     $this->Flash->error(__('No se pudieron guardar algunos horarios.'));
    //                 }
    //             }
    //         }

    //         $this->Flash->success(__('Los horarios han sido actualizados.'));
    //         return $this->redirect(['action' => 'index']);
    //     }

    //     $this->set(compact('horariosDoctores', 'doctorId'));
    //       if ($this->request->is('ajax')) {
    //         $this->viewBuilder()->setLayout('ajax');
    //     } else {
    //         $this->viewBuilder()->setLayout('default');
    //     }
    // }
    public function editAll($doctorId)
    {
        // Obtener al doctor con sus horarios (si quieres usar contain)
        $doctor = $this->HorariosDoctores->Doctores->get($doctorId);

        // Obtener los horarios del doctor específico
        $horariosDoctores = $this->HorariosDoctores->find('all', 
            conditions : ['doctor_id' => $doctorId]
        )->toArray();

        if ($this->request->is(['patch', 'post', 'put'])) {
            $horariosData = $this->request->getData('horarios');

            foreach ($horariosDoctores as $index => $horario) {
                if (isset($horariosData[$index])) {
                    $horario = $this->HorariosDoctores->patchEntity($horario, $horariosData[$index]);

                    if (!$this->HorariosDoctores->save($horario)) {
                        $this->Flash->error(__('No se pudieron guardar algunos horarios.'));
                    }
                }
            }

            $this->Flash->success(__('Los horarios han sido actualizados.'));
            return $this->redirect(['action' => 'index']);
        }

        // Pasar el doctor también a la vista
        $this->set(compact('horariosDoctores', 'doctorId', 'doctor'));

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
        // Cargamos la lista de doctores para el formulario
        $doctores = $this->HorariosDoctores->Doctores->find('list', limit: 200)->all();

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $doctorId = $data['doctor_id'];
            $horarios = $data['horarios'] ?? [];

            $guardados = 0;

            foreach ($horarios as $horarioData) {
                $horarioEntity = $this->HorariosDoctores->newEmptyEntity();

                // Añadir el ID del doctor al bloque de datos del horario
                $horarioData['doctor_id'] = $doctorId;

                $this->HorariosDoctores->patchEntity($horarioEntity, $horarioData);

                if ($this->HorariosDoctores->save($horarioEntity)) {
                    $guardados++;
                }
            }

            if ($guardados > 0) {
                $this->Flash->success("Se guardaron {$guardados} horarios correctamente.");
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('No se pudo guardar ningún horario. Revisa los datos.');
            }
        }

        // Creamos una entidad vacía solo para el helper del form
        $horariosDoctore = $this->HorariosDoctores->newEmptyEntity();
        $this->set(compact('horariosDoctore', 'doctores'));

        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Horarios Doctore id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $horariosDoctore = $this->HorariosDoctores->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $horariosDoctore = $this->HorariosDoctores->patchEntity($horariosDoctore, $this->request->getData());
            if ($this->HorariosDoctores->save($horariosDoctore)) {
                $this->Flash->success(__('The horarios doctore has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The horarios doctore could not be saved. Please, try again.'));
        }
        $doctores = $this->HorariosDoctores->Doctores->find('list', limit: 200)->all();
        $this->set(compact('horariosDoctore', 'doctores'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Horarios Doctore id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $horariosDoctore = $this->HorariosDoctores->get($id);
        if ($this->HorariosDoctores->delete($horariosDoctore)) {
            $this->Flash->success(__('The horarios doctore has been deleted.'));
        } else {
            $this->Flash->error(__('The horarios doctore could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    public function obtenerHorarios()
    {
        $this->autoRender = false;
        $doctor_id = $this->request->getQuery('doctor_id');

        if (!$doctor_id) {
            echo json_encode(['success' => false, 'message' => 'Doctor ID no recibido']);
            return;
        }

        $horarios = $this->HorariosDoctores->find()
            ->where(['doctor_id' => $doctor_id])
            ->toArray();

        if (empty($horarios)) {
            echo json_encode(['success' => false, 'message' => 'No hay horarios en la BD']);
        } else {
            echo json_encode(['success' => true, 'horarios' => $horarios]);
        }
    }
}

