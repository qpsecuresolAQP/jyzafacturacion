<?php
declare(strict_types=1);

namespace App\Controller;

class CompaniesController extends AppController
{
    public function index()
    {
        $companies = $this->paginate($this->Companies->find()->order(['Companies.id' => 'DESC']));
        $this->set(compact('companies'));
    }

    public function view($id = null)
    {
        $company = $this->Companies->get($id);
        $this->set(compact('company'));
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Todas las acciones requieren autenticación
    }

    public function add()
{
    $this->autoRender = false;

    $company = $this->Companies->newEmptyEntity();

    if (!$this->request->is('post')) {
        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode([
                'ok' => false,
                'message' => 'La petición no es POST',
                'method' => $this->request->getMethod(),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    $data = $this->request->getData();

    $company = $this->Companies->patchEntity($company, $data);

    if ($this->Companies->save($company)) {
        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode([
                'ok' => true,
                'message' => 'Empresa guardada correctamente',
                'id' => $company->id,
                'data' => $company,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    return $this->response
        ->withType('application/json')
        ->withStringBody(json_encode([
            'ok' => false,
            'message' => 'No se pudo guardar la empresa',
            'errors' => $company->getErrors(),
            'data' => $data,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

    public function edit($id = null)
    {
        $company = $this->Companies->get($id);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $company = $this->Companies->patchEntity($company, $this->request->getData());

            if ($this->Companies->save($company)) {
                $this->Flash->success('Empresa emisora actualizada correctamente.');
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error('No se pudo actualizar la empresa emisora.');
        }

        $this->set(compact('company'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $company = $this->Companies->get($id);

        if ($this->Companies->delete($company)) {
            $this->Flash->success('Empresa eliminada.');
        } else {
            $this->Flash->error('No se pudo eliminar la empresa.');
        }

        return $this->redirect(['action' => 'index']);
    }
}