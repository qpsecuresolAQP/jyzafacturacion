<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * Recordatorios Controller
 *
 * @property \App\Model\Table\RecordatoriosTable $Recordatorios
 */
class RecordatoriosController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $currentAction = $this->request->getParam('action');
        $this->verificarPermisoORedireccionar('Recordatorios', $currentAction);
    }

    public function index()
    {
        $table = $this->fetchTable('Recordatorios');
        $query = $table->find()
            ->contain(['Pacientes'])
            ->orderBy(['Recordatorios.fecha_inicio' => 'DESC']);
        $recordatorios = $this->paginate($query);

        $this->set(compact('recordatorios'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        }
    }

    public function view($id = null)
    {
        $table = $this->fetchTable('Recordatorios');
        $recordatorio = $table->get($id, contain: [
            'Pacientes',
            'RecordatorioControles' => [
                'conditions' => ['RecordatorioControles.estado_control !=' => 'I'],
                'sort' => ['RecordatorioControles.fecha_control' => 'DESC']
            ],
        ]);

        $this->set(compact('recordatorio'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        }
    }

    public function add()
    {
        $table = $this->fetchTable('Recordatorios');
        $recordatorio = $table->newEmptyEntity();
        $pacienteId = $this->request->getQuery('paciente_id');

        if ($this->request->is('post')) {
            $recordatorio = $table->patchEntity($recordatorio, $this->request->getData());
            if (empty($recordatorio->estado_control)) {
                $recordatorio->estado_control = 'A';
            }

            if ($table->save($recordatorio)) {
                $this->Flash->success(__('El recordatorio fue creado exitosamente.'));

                return $this->redirect([
                    'controller' => 'Pacientes',
                    'action' => 'view',
                    $recordatorio->paciente_id,
                    '#' => 'recordatorios'
                ]);
            }

            $this->Flash->error(__('El recordatorio no pudo ser creado. Intente de nuevo.'));
        } elseif (!empty($pacienteId)) {
            $recordatorio->paciente_id = $pacienteId;
        }

        $pacienteSeleccionado = null;
        if (!empty($pacienteId)) {
            $pacientesTable = $this->fetchTable('Pacientes');
            $pacienteSeleccionado = $pacientesTable->find()->where(['id' => $pacienteId])->first();
        }

        $this->set(compact('recordatorio', 'pacienteSeleccionado'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        }
    }

    public function edit($id = null)
    {
        $table = $this->fetchTable('Recordatorios');
        $recordatorio = $table->get($id);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $recordatorio = $table->patchEntity($recordatorio, $this->request->getData());

            if ($table->save($recordatorio)) {
                $this->Flash->success(__('El recordatorio fue actualizado exitosamente.'));

                return $this->redirect([
                    'controller' => 'Pacientes',
                    'action' => 'view',
                    $recordatorio->paciente_id,
                    '#' => 'recordatorios'
                ]);
            }

            $this->Flash->error(__('El recordatorio no pudo ser actualizado. Intente de nuevo.'));
        }

        $pacientesTable = $this->fetchTable('Pacientes');
        $pacienteSeleccionado = $pacientesTable->find()->where(['id' => $recordatorio->paciente_id])->first();

        $this->set(compact('recordatorio', 'pacienteSeleccionado'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        }
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $table = $this->fetchTable('Recordatorios');
        $controlesTable = $this->fetchTable('RecordatorioControles');

        $recordatorio = $table->get($id);
        $recordatorio->estado_control = 'I';

        if ($table->save($recordatorio)) {
            $controlesTable->updateAll(
                ['estado_control' => 'I'],
                ['recordatorio_id' => $id]
            );

            $this->Flash->success(__('El recordatorio fue desactivado correctamente.'));
        } else {
            $this->Flash->error(__('El recordatorio no pudo ser desactivado.'));
        }

        return $this->redirect([
            'controller' => 'Pacientes',
            'action' => 'view',
            $recordatorio->paciente_id,
            '#' => 'recordatorios'
        ]);
    }

    public function getByPaciente($pacienteId = null)
    {
        $this->request->allowMethod(['get']);

        $recordatorios = [];
        if (!empty($pacienteId)) {
            $table = $this->fetchTable('Recordatorios');
            $recordatorios = $table->find()
                ->where(['paciente_id' => $pacienteId])
                ->select(['id', 'titulo', 'fecha_inicio', 'duracion_estimada', 'observacion', 'estado_control'])
                ->orderBy(['fecha_inicio' => 'DESC'])
                ->toArray();
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode($recordatorios));
    }
}