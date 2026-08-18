<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\ORM\TableRegistry;

/**
 * RecetasMedicamentos Controller
 *
 * @property \App\Model\Table\RecetasMedicamentosTable $RecetasMedicamentos
 */
class RecetasMedicamentosController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->RecetasMedicamentos->find()
            ->contain(['Recetas', 'Medicamentos', 'FormasFarmaceuticas', 'ViasAdministracion']);
        $recetasMedicamentos = $this->paginate($query);

        $this->set(compact('recetasMedicamentos'));
    }

    /**
     * View method
     *
     * @param string|null $id Recetas Medicamentos id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $recetaMedicamento = $this->RecetasMedicamentos->get($id, contain: ['Recetas', 'Medicamentos', 'FormasFarmaceuticas', 'ViasAdministracion']);
        $this->set(compact('recetaMedicamento'));
    }

    /**
     * Add method
     * Puede recibir receta_id como parámetro query para pre-seleccionar la receta
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $recetaMedicamento = $this->RecetasMedicamentos->newEmptyEntity();
        $recetaIdPreseleccionada = $this->request->getQuery('receta_id');
        
        if ($this->request->is('post')) {
            $recetaMedicamento = $this->RecetasMedicamentos->patchEntity($recetaMedicamento, $this->request->getData());
            
            if ($this->RecetasMedicamentos->save($recetaMedicamento)) {
                // Actualizar indicaciones consolidadas de la receta
                $this->_actualizarIndicacionesReceta($recetaMedicamento->receta_id);
                
                $this->Flash->success(__('El medicamento ha sido agregado a la receta.'));
                return $this->redirect(['controller' => 'Recetas', 'action' => 'view', $recetaMedicamento->receta_id]);
            }
            $this->Flash->error(__('El medicamento no pudo ser agregado. Intente de nuevo.'));
        }
        
        $recetas = $this->RecetasMedicamentos->Recetas->find('list', limit: 200)->all();
        $medicamentos = $this->RecetasMedicamentos->Medicamentos->find('list', limit: 200)->all();
        $formasFarmaceuticas = $this->RecetasMedicamentos->FormasFarmaceuticas->find('list', limit: 200)->all();
        $viasAdministracion = $this->RecetasMedicamentos->ViasAdministracion->find('list', limit: 200)->all();
        
        $this->set(compact('recetaMedicamento', 'recetas', 'medicamentos', 'formasFarmaceuticas', 'viasAdministracion', 'recetaIdPreseleccionada'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Recetas Medicamentos id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $recetaMedicamento = $this->RecetasMedicamentos->get($id, contain: []);
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $recetaMedicamento = $this->RecetasMedicamentos->patchEntity($recetaMedicamento, $this->request->getData());
            
            if ($this->RecetasMedicamentos->save($recetaMedicamento)) {
                // Actualizar indicaciones consolidadas de la receta
                $this->_actualizarIndicacionesReceta($recetaMedicamento->receta_id);
                
                $this->Flash->success(__('El medicamento ha sido actualizado.'));
                return $this->redirect(['controller' => 'Recetas', 'action' => 'view', $recetaMedicamento->receta_id]);
            }
            $this->Flash->error(__('El medicamento no pudo ser actualizado. Intente de nuevo.'));
        }
        
        $recetas = $this->RecetasMedicamentos->Recetas->find('list', limit: 200)->all();
        $medicamentos = $this->RecetasMedicamentos->Medicamentos->find('list', limit: 200)->all();
        $formasFarmaceuticas = $this->RecetasMedicamentos->FormasFarmaceuticas->find('list', limit: 200)->all();
        $viasAdministracion = $this->RecetasMedicamentos->ViasAdministracion->find('list', limit: 200)->all();
        
        $this->set(compact('recetaMedicamento', 'recetas', 'medicamentos', 'formasFarmaceuticas', 'viasAdministracion'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Recetas Medicamentos id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $recetaMedicamento = $this->RecetasMedicamentos->get($id);
        $recetaId = $recetaMedicamento->receta_id;
        
        if ($this->RecetasMedicamentos->delete($recetaMedicamento)) {
            // Actualizar indicaciones consolidadas de la receta
            $this->_actualizarIndicacionesReceta($recetaId);
            
            $this->Flash->success(__('El medicamento ha sido eliminado.'));
        } else {
            $this->Flash->error(__('El medicamento no pudo ser eliminado. Intente de nuevo.'));
        }

        return $this->redirect(['controller' => 'Recetas', 'action' => 'view', $recetaId]);
    }

    /**
     * Buscar medicamentos por código o nombre (AJAX)
     *
     * @return \Cake\Http\Response|void
     */
    public function buscarMedicamentos()
    {
        $this->request->allowMethod(['get', 'post']);
        $busqueda = $this->request->getQuery('q', '');
        
        $medicamentos = [];
        if (!empty($busqueda)) {
            $medicamentos = $this->RecetasMedicamentos->Medicamentos->buscar($busqueda)->toArray();
        }
        
        // Mapear medicamentos para retornar solo los campos necesarios
        $medicamentosArray = array_map(function($med) {
            return [
                'id' => $med->id,
                'codigo' => $med->codigo,
                'nombre' => $med->nombre,
                'concentracion' => $med->concentracion ?? ''
            ];
        }, $medicamentos);
        
        // Responder directamente con JSON sin renderizar vista
        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode(['medicamentos' => $medicamentosArray]));
    }

    /**
     * Actualizar indicaciones consolidadas de la receta
     *
     * @param int $recetaId ID de la receta
     * @return void
     */
    private function _actualizarIndicacionesReceta($recetaId)
    {
        try {
            $recetas = TableRegistry::getTableLocator()->get('Recetas');
            $medicamentos = $recetas->RecetasMedicamentos->find()
                ->where(['receta_id' => $recetaId])
                ->contain(['Medicamentos', 'FormasFarmaceuticas', 'ViasAdministracion'])
                ->all();

            $indicaciones = [];
            foreach ($medicamentos as $med) {
                $linea = strtoupper($med->medicamento->nombre);
                
                if ($med->concentracion) {
                    $linea .= ' ' . $med->concentracion;
                }
                
                if ($med->forma_farmaceutica) {
                    $linea .= ' - ' . $med->forma_farmaceutica->nombre;
                }
                
                if ($med->cantidad) {
                    $linea .= ' (' . $med->cantidad . ' unidades)';
                }
                
                if ($med->via_administracion) {
                    $linea .= ' - VÍA: ' . strtoupper($med->via_administracion->nombre);
                }
                
                if ($med->duracion_dias) {
                    $linea .= ' - DURACIÓN: ' . $med->duracion_dias . ' días';
                }
                
                if ($med->observaciones) {
                    $linea .= ' - NOTA: ' . $med->observaciones;
                }
                
                $indicaciones[] = $linea;
            }

            $receta = $recetas->get($recetaId);
            $receta->indicaciones_consolidadas = implode("\n", $indicaciones);
            $recetas->save($receta);
        } catch (\Exception $e) {
            // Log silencioso de errores
        }
    }
}
