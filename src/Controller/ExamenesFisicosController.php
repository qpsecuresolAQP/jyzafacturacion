<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\I18n\Date;

/**
 * ExamenesFisicos Controller
 *
 * @property \App\Model\Table\ExamenesFisicosTable $ExamenesFisicos
 */
class ExamenesFisicosController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $currentAction = $this->request->getParam('action');
        
        $this->verificarPermisoORedireccionar('ExamenesFisicos', $currentAction);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->ExamenesFisicos->find();
        $examenesFisicos = $this->paginate($query);

        $this->set(compact('examenesFisicos'));
    }

    /**
     * View method
     *
     * @param string|null $id Examenes Fisico id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $examenesFisico = $this->ExamenesFisicos->get($id, contain: []);
        $this->set(compact('examenesFisico'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add($historiaId = null)
    {
        // Crear una nueva entidad para el examen físico
        $examenFisico = $this->ExamenesFisicos->newEmptyEntity();

        // Cargar la historia clínica y el paciente asociado
        $historia = $this->ExamenesFisicos->HistoriasClinicas->get($historiaId, contain :['Pacientes']);

        // Calcular la edad del paciente
        $fechaNacimiento = $historia->fecha_nacimiento; // Asegúrate de que 'fecha_nacimiento' sea un campo de la tabla Pacientes
        $edad = $this->calculateAge($fechaNacimiento); // Función para calcular la edad

        // Si el formulario es enviado
        if ($this->request->is('post')) {
            $examenFisico = $this->ExamenesFisicos->patchEntity($examenFisico, $this->request->getData());

            if ($this->ExamenesFisicos->save($examenFisico)) {
                // Guardar la relación en la tabla intermedia
                $fisicosHistoriasTable = $this->fetchTable('FisicosHistorias');
                $fisicoHistoria = $fisicosHistoriasTable->newEntity([
                    'historia_id' => $historiaId,
                    'examen_id' => $examenFisico->id
                ]);

                if ($fisicosHistoriasTable->save($fisicoHistoria)) {
                    $this->Flash->success(__('El examen físico ha sido guardado.'));
                    return $this->redirect(['controller' => 'Pacientes', 'action' => 'view', $historia->paciente_id, '#' => 'antecedentes']);
                } else {
                    $this->Flash->error(__('No se pudo guardar la relación con la historia clínica.'));
                }
            } else {
                $this->Flash->error(__('No se pudo guardar el examen físico.'));
            }
        }

        // Pasar la edad calculada a la vista
        $this->set(compact('examenFisico', 'historiaId', 'edad', 'historia'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    // Función para calcular la edad a partir de la fecha de nacimiento
    private function calculateAge($fechaNacimiento)
    {
        // Verificar si la fecha de nacimiento no está presente
        if (empty($fechaNacimiento)) {
            return null; // No hay fecha de nacimiento, retornamos null (o un valor adecuado)
        }

        // Si es un objeto Cake\I18n\Date, lo convertimos a string
        if ($fechaNacimiento instanceof Date) {
            $fechaNacimiento = $fechaNacimiento->format('Y-m-d'); // Convertimos a formato de fecha para DateTime
        }

        // Intentar crear el objeto DateTime
        try {
            $fechaNacimiento = new \DateTime($fechaNacimiento);
        } catch (\Exception $e) {
            // Si hay un error al crear el DateTime, manejarlo
            return null; // Retorna null si no se puede crear la fecha
        }

        $hoy = new \DateTime();
        $edad = $hoy->diff($fechaNacimiento);
        return $edad->y; // Retorna la edad en años
    }

    /**
     * Edit method
     *
     * @param string|null $id Examenes Fisico id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $examenesFisico = $this->ExamenesFisicos->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $examenesFisico = $this->ExamenesFisicos->patchEntity($examenesFisico, $this->request->getData());
            if ($this->ExamenesFisicos->save($examenesFisico)) {
                $this->Flash->success(__('The examenes fisico has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The examenes fisico could not be saved. Please, try again.'));
        }
        $this->set(compact('examenesFisico'));
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
     * @param string|null $id Examenes Fisico id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $examenesFisico = $this->ExamenesFisicos->get($id);
        if ($this->ExamenesFisicos->delete($examenesFisico)) {
            $this->Flash->success(__('The examenes fisico has been deleted.'));
        } else {
            $this->Flash->error(__('The examenes fisico could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

