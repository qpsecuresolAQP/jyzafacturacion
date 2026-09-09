<?php
declare(strict_types=1);

namespace App\Controller;

class CategoriasProductosController extends AppController
{
    public function index()
    {
        $searchTerm = trim((string) $this->request->getQuery('search', ''));
        $estadoFiltro = $this->request->getQuery('estado', 'activos');

        $query = $this->CategoriasProductos->find()->order(['CategoriasProductos.id' => 'DESC']);

        if ($estadoFiltro === 'activos') {
            $query->where(['CategoriasProductos.estado' => 1]);
        } elseif ($estadoFiltro === 'inactivos') {
            $query->where(['CategoriasProductos.estado' => 0]);
        }
        // 'todos' no aplica filtro

        if ($searchTerm !== '') {
            $query->where([
                'LOWER(CategoriasProductos.nombre) LIKE' => '%' . strtolower($searchTerm) . '%',
            ]);
        }

        $categoriasProductos = $this->paginate($query);

        $this->set(compact('categoriasProductos', 'searchTerm', 'estadoFiltro'));
    }

    public function view($id = null)
    {
        $categoriaProducto = $this->CategoriasProductos->get($id, contain: [
            'Productos' => function ($q) {
                return $q->order(['Productos.nombre' => 'ASC']);
            },
        ]);

        // Separar activos e inactivos: la tabla principal solo muestra los
        // activos, los inactivos van en su propio apartado reactivable.
        $productosActivos = $categoriaProducto->productos
            ? array_values(array_filter($categoriaProducto->productos, fn($p) => (int)$p->estado === 1))
            : [];
        $productosInactivos = $categoriaProducto->productos
            ? array_values(array_filter($categoriaProducto->productos, fn($p) => (int)$p->estado === 0))
            : [];

        $this->set(compact('categoriaProducto', 'productosActivos', 'productosInactivos'));
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

        $conn = $this->CategoriasProductos->getConnection();
        $ok = $conn->transactional(function () use ($categoriaProducto) {
            if (!$this->CategoriasProductos->save(
                $this->CategoriasProductos->patchEntity($categoriaProducto, ['estado' => 0])
            )) {
                return false;
            }

            // Cascada: desactiva también los productos que sigan activos,
            // marcándolos para poder distinguirlos luego de los que ya
            // estaban inactivos por su cuenta (esos no se tocan).
            $this->CategoriasProductos->Productos->updateAll(
                ['estado' => 0, 'desactivado_por_categoria' => 1],
                ['categoria_producto_id' => $categoriaProducto->id, 'estado' => 1]
            );

            return true;
        });

        if ($ok) {
            $this->Flash->success('La categoría y sus productos fueron desactivados correctamente.');
        } else {
            $this->Flash->error('No se pudo desactivar la categoría.');
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Reactiva una categoría de producto previamente desactivada. Solo
     * reactiva los productos que la propia cascada de delete() había
     * desactivado (desactivado_por_categoria = 1); los que ya estaban
     * inactivos por su cuenta antes de eso quedan como estaban.
     */
    public function reactivar($id = null)
    {
        $this->request->allowMethod(['post']);

        $categoriaProducto = $this->CategoriasProductos->get($id);

        $conn = $this->CategoriasProductos->getConnection();
        $ok = $conn->transactional(function () use ($categoriaProducto) {
            if (!$this->CategoriasProductos->save(
                $this->CategoriasProductos->patchEntity($categoriaProducto, ['estado' => 1])
            )) {
                return false;
            }

            $this->CategoriasProductos->Productos->updateAll(
                ['estado' => 1, 'desactivado_por_categoria' => 0],
                ['categoria_producto_id' => $categoriaProducto->id, 'desactivado_por_categoria' => 1]
            );

            return true;
        });

        if ($ok) {
            $this->Flash->success('La categoría fue reactivada correctamente, junto con los productos que se desactivaron con ella.');
        } else {
            $this->Flash->error('No se pudo reactivar la categoría.');
        }

        return $this->redirect(['action' => 'index']);
    }
}