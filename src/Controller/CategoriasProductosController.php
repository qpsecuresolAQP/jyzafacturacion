<?php
declare(strict_types=1);

namespace App\Controller;

class CategoriasProductosController extends AppController
{
    public function index()
    {
        $searchTerm = trim((string) $this->request->getQuery('search', ''));

        $query = $this->CategoriasProductos->find()->order(['CategoriasProductos.id' => 'DESC']);

        if ($searchTerm !== '') {
            $query->where([
                'LOWER(CategoriasProductos.nombre) LIKE' => '%' . strtolower($searchTerm) . '%',
            ]);
        }

        $categoriasProductos = $this->paginate($query);

        $this->set(compact('categoriasProductos', 'searchTerm'));
    }

    public function view($id = null)
    {
        $categoriaProducto = $this->CategoriasProductos->get($id, contain: [
            'Productos' => function ($q) {
                return $q->order(['Productos.nombre' => 'ASC']);
            },
        ]);

        $this->set(compact('categoriaProducto'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    public function add()
    {
        $categoriaProducto = $this->CategoriasProductos->newEmptyEntity();

        if ($this->request->is('post')) {
            $categoriaProducto = $this->CategoriasProductos->patchEntity(
                $categoriaProducto,
                $this->request->getData()
            );

            if ($this->CategoriasProductos->save($categoriaProducto)) {
                $this->Flash->success('La categoría fue guardada correctamente.');
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error('No se pudo guardar la categoría.');
        }

        $this->set(compact('categoriaProducto'));
         // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    public function edit($id = null)
    {
        $categoriaProducto = $this->CategoriasProductos->get($id);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $categoriaProducto = $this->CategoriasProductos->patchEntity(
                $categoriaProducto,
                $this->request->getData()
            );

            if ($this->CategoriasProductos->save($categoriaProducto)) {
                $this->Flash->success('La categoría fue actualizada correctamente.');
                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error('No se pudo actualizar la categoría.');
        }

        $this->set(compact('categoriaProducto'));
         // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $categoriaProducto = $this->CategoriasProductos->get($id);

        if ($this->CategoriasProductos->save(
            $this->CategoriasProductos->patchEntity($categoriaProducto, ['estado' => 0])
        )) {
            $this->Flash->success('La categoría fue desactivada correctamente.');
        } else {
            $this->Flash->error('No se pudo desactivar la categoría.');
        }

        return $this->redirect(['action' => 'index']);
    }
}