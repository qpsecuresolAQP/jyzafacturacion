<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * Tratamientos Controller
 *
 * @property \App\Model\Table\TratamientosTable $Tratamientos
 */
class TratamientosController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $currentAction = $this->request->getParam('action');
        
        $this->verificarPermisoORedireccionar('Tratamientos', $currentAction);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // Permiso verificado en beforeFilter
        $estadoFiltro = $this->request->getQuery('estado', 'activos');
        $busqueda = trim((string)$this->request->getQuery('q', ''));

        $query = $this->Tratamientos->find();

        if ($estadoFiltro === 'activos') {
            $query->where(['estado' => 1]);
        } elseif ($estadoFiltro === 'inactivos') {
            $query->where(['estado' => 0]);
        }
        // 'todos' no aplica filtro

        if ($busqueda !== '') {
            $query->where(['nombre LIKE' => '%' . $busqueda . '%']);
        }

        $tratamientos = $this->paginate($query);

        $this->set(compact('tratamientos', 'estadoFiltro', 'busqueda'));
    }

    /**
     * View method
     *
     * @param string|null $id Tratamiento id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {        
        $tratamiento = $this->Tratamientos->get($id, contain: []);
        $this->set(compact('tratamiento'));
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
        $tratamiento = $this->Tratamientos->newEmptyEntity();
        if ($this->request->is('post')) {
            $tratamiento = $this->Tratamientos->patchEntity($tratamiento, $this->request->getData());
            if ($this->Tratamientos->save($tratamiento)) {
                $this->Flash->success(__('The tratamiento has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The tratamiento could not be saved. Please, try again.'));
        }
        $this->set(compact('tratamiento'));
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
     * @param string|null $id Tratamiento id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {        
        $tratamiento = $this->Tratamientos->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $tratamiento = $this->Tratamientos->patchEntity($tratamiento, $this->request->getData());
            if ($this->Tratamientos->save($tratamiento)) {
                $this->Flash->success(__('The tratamiento has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The tratamiento could not be saved. Please, try again.'));
        }
        $this->set(compact('tratamiento'));
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
     * @param string|null $id Tratamiento id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $tratamiento = $this->Tratamientos->get($id);

        // Eliminación lógica: solo se desactiva, no se borra el registro
        // (queda referenciado por facturas/presupuestos ya emitidos).
        $tratamiento->estado = 0;

        if ($this->Tratamientos->save($tratamiento)) {
            $this->Flash->success(__('Tratamiento desactivado correctamente.'));
        } else {
            $this->Flash->error(__('No se pudo desactivar el tratamiento.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Reactiva un tratamiento previamente desactivado.
     *
     * @param string|null $id Tratamiento id.
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function reactivar($id = null)
    {
        $this->request->allowMethod(['post']);
        $tratamiento = $this->Tratamientos->get($id);

        $tratamiento->estado = 1;

        if ($this->Tratamientos->save($tratamiento)) {
            $this->Flash->success(__('Tratamiento reactivado correctamente.'));
        } else {
            $this->Flash->error(__('No se pudo reactivar el tratamiento.'));
        }

        return $this->redirect(['action' => 'index', '?' => ['estado' => 'inactivos']]);
    }

    public function buscarTratamientos()
    {
        $q = $this->request->getQuery('q');

        $data = $this->Tratamientos->find()
            ->where(['nombre LIKE' => "%$q%", 'estado' => 1])
            ->limit(10)
            ->all();

        $result = [];

        foreach ($data as $t) {
            $result[] = [
                'id' => $t->id,
                'nombre' => $t->nombre,
                'precio' => (float)$t->costo
            ];
        }

        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', ['result']);
    }
}

