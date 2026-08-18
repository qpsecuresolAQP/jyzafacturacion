<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * FisicosHistorias Controller
 *
 * @property \App\Model\Table\FisicosHistoriasTable $FisicosHistorias
 */
class FisicosHistoriasController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // Obtener el ID de la historia clínica desde la URL (GET)
        $historiaClinicaId = $this->request->getQuery('historia_id');

        // Construir la consulta con el filtro de historia clínica
        $query = $this->FisicosHistorias->find()
            ->contain(['Examens', 'Historias'])
            ->order(['FisicosHistorias.created' => 'DESC']);

        // Aplicar el filtro si se proporciona historia_id
        if (!empty($historiaClinicaId)) {
            $query->where(['FisicosHistorias.historia_id' => $historiaClinicaId]);
        }

        $fisicosHistorias = $this->paginate($query);

        $this->set(compact('fisicosHistorias', 'historiaClinicaId'));

        // Si la solicitud es AJAX, cambiar el layout para cargar solo la tabla
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * View method
     *
     * @param string|null $id Fisicos Historia id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $fisicosHistoria = $this->FisicosHistorias->get($id, contain: ['Examens', 'Historias']);
        $this->set(compact('fisicosHistoria'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $fisicosHistoria = $this->FisicosHistorias->newEmptyEntity();
        if ($this->request->is('post')) {
            $fisicosHistoria = $this->FisicosHistorias->patchEntity($fisicosHistoria, $this->request->getData());
            if ($this->FisicosHistorias->save($fisicosHistoria)) {
                $this->Flash->success(__('The fisicos historia has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The fisicos historia could not be saved. Please, try again.'));
        }
        $examens = $this->FisicosHistorias->Examens->find('list', limit: 200)->all();
        $historias = $this->FisicosHistorias->Historias->find('list', limit: 200)->all();
        $this->set(compact('fisicosHistoria', 'examens', 'historias'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Fisicos Historia id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $fisicosHistoria = $this->FisicosHistorias->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $fisicosHistoria = $this->FisicosHistorias->patchEntity($fisicosHistoria, $this->request->getData());
            if ($this->FisicosHistorias->save($fisicosHistoria)) {
                $this->Flash->success(__('The fisicos historia has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The fisicos historia could not be saved. Please, try again.'));
        }
        $examens = $this->FisicosHistorias->Examens->find('list', limit: 200)->all();
        $historias = $this->FisicosHistorias->Historias->find('list', limit: 200)->all();
        $this->set(compact('fisicosHistoria', 'examens', 'historias'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Fisicos Historia id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $fisicosHistoria = $this->FisicosHistorias->get($id);
        if ($this->FisicosHistorias->delete($fisicosHistoria)) {
            $this->Flash->success(__('The fisicos historia has been deleted.'));
        } else {
            $this->Flash->error(__('The fisicos historia could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

