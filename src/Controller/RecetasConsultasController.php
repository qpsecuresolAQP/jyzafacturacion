<?php

declare(strict_types=1);

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;

/**
 * RecetasConsultas Controller
 *
 * @property \App\Model\Table\RecetasConsultasTable $RecetasConsultas
 */
class RecetasConsultasController extends AppController
{
    public function exportRecetaPdf($id = null)
    {
        $logoUrl = Router::url('/img/logoClinica.png', true);
        // Obtener los datos completos de la receta consulta
        $recetaConsulta = $this->RecetasConsultas->get($id, 
            contain : [
                'Consultas' => [
                    'Doctores',
                    'HistoriasClinicas' => ['Pacientes']
                ],
                'Recetas'
            ]
        );

        // Configurar vista sin layout
        $this->viewBuilder()->enableAutoLayout(false);
        $this->set(compact('recetaConsulta', 'logoUrl'));

        // Renderizar la vista específica para PDF
        $html = $this->render('receta_pdf');

        // Configurar DomPDF
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);

        // Configurar tamaño y orientación
        $dompdf->setPaper('A4', 'portrait');

        // Renderizar PDF
        $dompdf->render();

        // Generar nombre del archivo
        $pacienteNombre = $recetaConsulta->consulta->historia_clinica->paciente->nombre ?? 'Receta';
        $filename = "Receta_Medica_{$pacienteNombre}_{$id}.pdf";
        $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $filename); // Limpiar caracteres especiales

        // Descargar el PDF
        $dompdf->stream($filename, ['Attachment' => 0]);
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->RecetasConsultas->find()
            ->contain(['Consultas', 'Recetas']);
        $recetasConsultas = $this->paginate($query);

        $this->set(compact('recetasConsultas'));
    }

    /**
     * View method
     *
     * @param string|null $id Recetas Consulta id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $recetasConsulta = $this->RecetasConsultas->get($id, contain: ['Consultas', 'Recetas']);
        $this->set(compact('recetasConsulta'));
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
        $recetasConsulta = $this->RecetasConsultas->newEmptyEntity();
        if ($this->request->is('post')) {
            $recetasConsulta = $this->RecetasConsultas->patchEntity($recetasConsulta, $this->request->getData());
            if ($this->RecetasConsultas->save($recetasConsulta)) {
                $this->Flash->success(__('The recetas consulta has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The recetas consulta could not be saved. Please, try again.'));
        }
        $consultas = $this->RecetasConsultas->Consultas->find('list', limit: 200)->all();
        $recetas = $this->RecetasConsultas->Recetas->find('list', limit: 200)->all();
        $this->set(compact('recetasConsulta', 'consultas', 'recetas'));
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
     * @param string|null $id Recetas Consulta id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $recetasConsulta = $this->RecetasConsultas->get($id, contain: ['Consultas' => ['HistoriasClinicas' => ['Pacientes']]]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $recetasConsulta = $this->RecetasConsultas->patchEntity($recetasConsulta, $this->request->getData());
            if ($this->RecetasConsultas->save($recetasConsulta)) {
                $this->set('cerrarVentana', true);
            } else {
                $this->Flash->error(__('La receta no pudo ser guardada. Intente de nuevo.'));
            }
            $this->Flash->error(__('The recetas consulta could not be saved. Please, try again.'));
        }
        $consultas = $this->RecetasConsultas->Consultas->find('list', limit: 200)->all();
        $recetas = $this->RecetasConsultas->Recetas->find('list', limit: 200)->all();
        $this->set(compact('recetasConsulta', 'consultas', 'recetas'));
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
     * @param string|null $id Recetas Consulta id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $recetasConsulta = $this->RecetasConsultas->get($id);
        if ($this->RecetasConsultas->delete($recetasConsulta)) {
            $this->Flash->success(__('The recetas consulta has been deleted.'));
        } else {
            $this->Flash->error(__('The recetas consulta could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
