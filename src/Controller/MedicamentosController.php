<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * Medicamentos Controller
 *
 * @property \App\Model\Table\MedicamentosTable $Medicamentos
 */
class MedicamentosController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $currentAction = $this->request->getParam('action');
        
        $this->verificarPermisoORedireccionar('Medicamentos', $currentAction);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Medicamentos->find();
        $medicamentos = $this->paginate($query);

        $this->set(compact('medicamentos'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * View method
     *
     * @param string|null $id Medicamento id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $medicamento = $this->Medicamentos->get($id, contain: []);
        $this->set(compact('medicamento'));
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
        $medicamento = $this->Medicamentos->newEmptyEntity();
        
        if ($this->request->is('post')) {
            $medicamento = $this->Medicamentos->patchEntity($medicamento, $this->request->getData());
            
            if ($this->Medicamentos->save($medicamento)) {
                $this->Flash->success(__('El medicamento ha sido creado exitosamente.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('El medicamento no pudo ser creado. Intente de nuevo.'));
        }
        
        $this->set(compact('medicamento'));
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
     * @param string|null $id Medicamento id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $medicamento = $this->Medicamentos->get($id, contain: []);
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $medicamento = $this->Medicamentos->patchEntity($medicamento, $this->request->getData());
            
            if ($this->Medicamentos->save($medicamento)) {
                $this->Flash->success(__('El medicamento ha sido actualizado exitosamente.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('El medicamento no pudo ser actualizado. Intente de nuevo.'));
        }
        
        $this->set(compact('medicamento'));
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
     * @param string|null $id Medicamento id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $medicamento = $this->Medicamentos->get($id);
        
        if ($this->Medicamentos->delete($medicamento)) {
            $this->Flash->success(__('El medicamento ha sido eliminado.'));
        } else {
            $this->Flash->error(__('El medicamento no pudo ser eliminado. Intente de nuevo.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Buscar medicamentos por código o nombre (AJAX)
     */
    public function buscar()
    {
        $this->request->allowMethod(['get']);
        $q = $this->request->getQuery('q');
        
        $medicamentos = [];
        if (!empty($q)) {
            $medicamentos = $this->Medicamentos->find()
                ->where([
                    'OR' => [
                        'LOWER(codigo) LIKE' => '%' . strtolower($q) . '%',
                        'LOWER(nombre) LIKE' => '%' . strtolower($q) . '%'
                    ]
                ])
                ->select(['id', 'codigo', 'nombre', 'concentracion'])
                ->limit(10)
                ->toArray();
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode($medicamentos));
    }
}

