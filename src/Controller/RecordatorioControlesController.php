<?php

declare(strict_types=1);

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * RecordatorioControles Controller
 *
 * @property \App\Model\Table\RecordatorioControlesTable $RecordatorioControles
 */
class RecordatorioControlesController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $currentAction = $this->request->getParam('action');
        $this->verificarPermisoORedireccionar('RecordatorioControles', $currentAction);
    }

    public function index()
    {
        $table = $this->fetchTable('RecordatorioControles');
        $query = $table->find()
            ->contain(['Recordatorios.Pacientes'])
            ->orderBy(['RecordatorioControles.fecha_control' => 'DESC']);
        $recordatorioControles = $this->paginate($query);

        $this->set(compact('recordatorioControles'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        }
    }

    public function view($id = null)
    {
        $table = $this->fetchTable('RecordatorioControles');
        $recordatorioControl = $table->get($id, contain: [
            'Recordatorios.Pacientes'
        ]);

        $this->set(compact('recordatorioControl'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        }
    }

    public function reminder($id = null)
    {
        $table = $this->fetchTable('RecordatorioControles');
        $recordatorioControl = $table->get($id, contain: [
            'Recordatorios' => function ($q) {
                return $q->contain('Pacientes');
            }
        ]);

        $this->set(compact('recordatorioControl'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        }
    }

    public function add()
    {
        $table = $this->fetchTable('RecordatorioControles');
        $recordatorioControl = $table->newEmptyEntity();
        $recordatorioId = $this->request->getQuery('recordatorio_id');

        if ($recordatorioControl->isNew() && !$this->request->is('post')) {
            $recordatorioControl->fecha_control = date('Y-m-d');
            $recordatorioControl->proximo_control = date('Y-m-d', strtotime('+30 days'));
            $recordatorioControl->estado = 'P';
        }

        if ($this->request->is('post')) {
            $recordatorioControl = $table->patchEntity($recordatorioControl, $this->request->getData());

            if (empty($recordatorioControl->fecha_control)) {
                $recordatorioControl->fecha_control = date('Y-m-d');
            }

            if (empty($recordatorioControl->proximo_control)) {
                $recordatorioControl->proximo_control = date('Y-m-d', strtotime('+30 days'));
            }

            if (empty($recordatorioControl->estado)) {
                $recordatorioControl->estado = 'P';
            }

            if ($table->save($recordatorioControl)) {
                $this->Flash->success(__('El control fue creado exitosamente.'));

                $recordatorio = $this->fetchTable('Recordatorios')->get($recordatorioControl->recordatorio_id);

                return $this->redirect([
                    'controller' => 'Pacientes',
                    'action' => 'view',
                    $recordatorio->paciente_id,
                    '#' => 'recordatorios'
                ]);
            }

            $this->Flash->error(__('El control no pudo ser creado. Intente de nuevo.'));
        } elseif (!empty($recordatorioId)) {
            $recordatorioControl->recordatorio_id = $recordatorioId;
            $recordatorioControl->fecha_control = date('Y-m-d');
            $recordatorioControl->proximo_control = date('Y-m-d', strtotime('+30 days'));
            $recordatorioControl->estado = 'P';
        }

        $recordatoriosTable = $this->fetchTable('Recordatorios');
        $recordatorios = [];
        foreach (
            $recordatoriosTable->find()
                ->contain('Pacientes')
                ->orderBy(['Recordatorios.fecha_inicio' => 'DESC'])
                ->limit(200)
                ->all() as $item
        ) {
            $nombrePaciente = $item->paciente ? trim($item->paciente->nombre . ' ' . $item->paciente->apellido) : 'Sin paciente';
            $recordatorios[$item->id] = $item->titulo . ' - ' . $nombrePaciente;
        }

        $this->set(compact('recordatorioControl', 'recordatorios', 'recordatorioId'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        }
    }

    public function edit($id = null)
    {
        $table = $this->fetchTable('RecordatorioControles');
        $recordatorioControl = $table->get($id, contain: []);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $recordatorioControl = $table->patchEntity($recordatorioControl, $this->request->getData());

            if ($table->save($recordatorioControl)) {
                $this->Flash->success(__('El control fue actualizado exitosamente.'));

                $recordatorio = $this->fetchTable('Recordatorios')->get($recordatorioControl->recordatorio_id);

                return $this->redirect([
                    'controller' => 'Pacientes',
                    'action' => 'view',
                    $recordatorio->paciente_id,
                    '#' => 'recordatorios'
                ]);
            }

            $this->Flash->error(__('El control no pudo ser actualizado. Intente de nuevo.'));
        }

        $recordatoriosTable = $this->fetchTable('Recordatorios');
        $recordatorios = [];
        foreach (
            $recordatoriosTable->find()
                ->contain('Pacientes')
                ->orderBy(['Recordatorios.fecha_inicio' => 'DESC'])
                ->limit(200)
                ->all() as $item
        ) {
            $nombrePaciente = $item->paciente ? trim($item->paciente->nombre . ' ' . $item->paciente->apellido) : 'Sin paciente';
            $recordatorios[$item->id] = $item->titulo . ' - ' . $nombrePaciente;
        }

        $this->set(compact('recordatorioControl', 'recordatorios'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        }
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $table = $this->fetchTable('RecordatorioControles');
        $recordatorioControl = $table->get($id);
        $recordatorioId = $recordatorioControl->recordatorio_id;

        $recordatorioControl->estado_control = 'I';

        if ($table->save($recordatorioControl)) {
            $this->Flash->success(__('El control fue desactivado correctamente.'));
        } else {
            $this->Flash->error(__('El control no pudo ser desactivado.'));
        }

        $recordatorio = $this->fetchTable('Recordatorios')->get($recordatorioId);

        return $this->redirect([
            'controller' => 'Pacientes',
            'action' => 'view',
            $recordatorio->paciente_id,
            '#' => 'recordatorios'
        ]);
    }

    public function toggleEstado($id = null)
    {
        $this->request->allowMethod(['post']);

        $table = $this->fetchTable('RecordatorioControles');
        $recordatorioControl = $table->get($id);
        $recordatorioControl->estado = $recordatorioControl->estado === 'C' ? 'P' : 'C';

        if ($table->save($recordatorioControl)) {
            $this->Flash->success(__('El estado del control fue actualizado.'));
        } else {
            $this->Flash->error(__('No fue posible actualizar el estado del control.'));
        }

        $recordatorio = $this->fetchTable('Recordatorios')->get($recordatorioControl->recordatorio_id);

        return $this->redirect([
            'controller' => 'Pacientes',
            'action' => 'view',
            $recordatorio->paciente_id,
            '#' => 'recordatorios'
        ]);
    }

    public function getByRecordatorio($recordatorioId = null)
    {
        $this->request->allowMethod(['get']);

        $controles = [];
        if (!empty($recordatorioId)) {
            $table = $this->fetchTable('RecordatorioControles');
            $controles = $table->find()
                ->where(['recordatorio_id' => $recordatorioId])
                ->select(['id', 'fecha_control', 'proximo_control', 'detalles', 'productos_utilizados', 'observaciones', 'recordatorio_enviado', 'estado', 'estado_control'])
                ->orderBy(['fecha_control' => 'DESC'])
                ->toArray();
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode($controles));
    }

    public function reportes()
    {
        $table = $this->fetchTable('RecordatorioControles');

        $fechaInicio = $this->request->getQuery('fecha_inicio');
        $fechaFin = $this->request->getQuery('fecha_fin');
        $estado = $this->request->getQuery('estado');

        $query = $table->find()
            ->contain(['Recordatorios.Pacientes'])
            ->orderBy(['RecordatorioControles.fecha_control' => 'DESC']);

        if (!empty($fechaInicio)) {
            $query->where(['DATE(RecordatorioControles.fecha_control) >=' => $fechaInicio]);
        }

        if (!empty($fechaFin)) {
            $query->where(['DATE(RecordatorioControles.fecha_control) <=' => $fechaFin]);
        }

        if (!empty($estado) && in_array($estado, ['P', 'C'], true)) {
            $query->where(['RecordatorioControles.estado' => $estado]);
        }

        $recordatorioControles = $query->all();

        $resumen = [
            'P' => ['total' => 0, 'label' => 'Pendientes'],
            'C' => ['total' => 0, 'label' => 'Citados'],
        ];

        foreach ($recordatorioControles as $control) {
            if (isset($resumen[$control->estado])) {
                $resumen[$control->estado]['total']++;
            }
        }

        $totalControles = count($recordatorioControles);

        $this->set(compact('recordatorioControles', 'resumen', 'totalControles', 'fechaInicio', 'fechaFin', 'estado'));

        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        }
    }

    public function exportarPdf()
    {
        $table = $this->fetchTable('RecordatorioControles');

        $fechaInicio = $this->request->getQuery('fecha_inicio');
        $fechaFin = $this->request->getQuery('fecha_fin');
        $estado = $this->request->getQuery('estado');

        $query = $table->find()
            ->contain(['Recordatorios.Pacientes'])
            ->orderBy(['RecordatorioControles.fecha_control' => 'DESC']);

        if (!empty($fechaInicio)) {
            $query->where(['DATE(RecordatorioControles.fecha_control) >=' => $fechaInicio]);
        }

        if (!empty($fechaFin)) {
            $query->where(['DATE(RecordatorioControles.fecha_control) <=' => $fechaFin]);
        }

        if (!empty($estado) && in_array($estado, ['P', 'C'], true)) {
            $query->where(['RecordatorioControles.estado' => $estado]);
        }

        $recordatorioControles = $query->all();

        $resumen = [
            'P' => ['total' => 0, 'label' => 'Pendientes'],
            'C' => ['total' => 0, 'label' => 'Citados'],
        ];

        foreach ($recordatorioControles as $control) {
            if (isset($resumen[$control->estado])) {
                $resumen[$control->estado]['total']++;
            }
        }

        $this->viewBuilder()->disableAutoLayout();
        $this->set(compact('recordatorioControles', 'resumen', 'fechaInicio', 'fechaFin', 'estado'));

        $html = $this->render('reportes_pdf')->getBody()->__toString();

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'reporte_recordatorios_' . date('Ymd_His') . '.pdf';

        return $this->response
            ->withType('application/pdf')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->withStringBody($dompdf->output());
    }
}
