<?php
declare(strict_types=1);

namespace App\Controller;

class ProductosController extends AppController
{
    public function index()
    {
        $stockFiltro = (string) $this->request->getQuery('stock', 'todos');
        $searchTerm = trim((string) $this->request->getQuery('search', ''));
        $estadoFiltro = (string) $this->request->getQuery('estado', 'activos');

        $query = $this->Productos->find()
            ->contain(['CategoriasProductos', 'Proveedores'])
            ->order(['Productos.id' => 'DESC']);

        if ($estadoFiltro === 'activos') {
            $query->where(['Productos.estado' => 1]);
        } elseif ($estadoFiltro === 'inactivos') {
            $query->where(['Productos.estado' => 0]);
        }
        // 'todos' no aplica filtro

        if ($stockFiltro === 'agotado') {
            $query->where(['Productos.stock <=' => 0]);
        } elseif ($stockFiltro === 'bajo') {
            $query->where($this->stockBajoConditions('Productos'));
        } elseif ($stockFiltro === 'normal') {
            // Normal: hay stock y, o no tiene mínimo configurado, o el stock
            // actual todavía está por encima de ese mínimo.
            $query->where(['Productos.stock >' => 0])
                ->andWhere(function ($exp) {
                    return $exp->or([
                        'Productos.stock_minimo' => 0,
                        'Productos.stock >' => new \Cake\Database\Expression\IdentifierExpression('Productos.stock_minimo'),
                    ]);
                });
        }

        if ($searchTerm !== '') {
            $query->andWhere(function ($exp) use ($searchTerm) {
                return $exp->or([
                    'LOWER(Productos.nombre) LIKE' => '%' . strtolower($searchTerm) . '%',
                    'LOWER(Productos.codigo) LIKE' => '%' . strtolower($searchTerm) . '%',
                ]);
            });
        }

        $productos = $this->paginate($query);

        $stockCounts = [
            'agotado' => $this->Productos->find()->where(['stock <=' => 0])->count(),
            'bajo' => $this->Productos->find()->where($this->stockBajoConditions('Productos'))->count(),
        ];
        $stockCounts['normal'] = $this->Productos->find()->count() - $stockCounts['agotado'] - $stockCounts['bajo'];

        $this->set(compact('productos', 'stockFiltro', 'stockCounts', 'searchTerm', 'estadoFiltro'));
    }

    /**
     * Condiciones SQL para "stock bajo": tiene stock, tiene un mínimo
     * configurado (> 0) y el stock actual ya llegó a ese mínimo o menos.
     */
    private function stockBajoConditions(string $alias): array
    {
        return [
            $alias . '.stock >' => 0,
            $alias . '.stock_minimo >' => 0,
            $alias . '.stock <=' => new \Cake\Database\Expression\IdentifierExpression($alias . '.stock_minimo'),
        ];
    }

    public function add()
    {
        $producto = $this->Productos->newEmptyEntity();

        $categoriaProductoId = $this->request->getQuery('categoria_producto_id');

        if ($this->request->is('post')) {
            $producto = $this->Productos->patchEntity($producto, $this->request->getData());

            if ($this->Productos->save($producto)) {
                $this->Flash->success('El producto fue guardado correctamente.');

                if (!empty($producto->categoria_producto_id)) {
                    return $this->redirect([
                        'controller' => 'CategoriasProductos',
                        'action' => 'view',
                        $producto->categoria_producto_id,
                    ]);
                }

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error('No se pudo guardar el producto.');
        } elseif ($categoriaProductoId) {
            $producto->categoria_producto_id = (int)$categoriaProductoId;
        }

        $categorias = $this->Productos->CategoriasProductos
            ->find('list', keyField: 'id', valueField: 'nombre')
            ->where(['estado' => 1])
            ->order(['nombre' => 'ASC'])
            ->toArray();

        $proveedores = $this->Productos->Proveedores
            ->find('list', keyField: 'id', valueField: 'nombre')
            ->where(['activo' => 1])
            ->order(['nombre' => 'ASC'])
            ->toArray();

        $this->set(compact('producto', 'categorias', 'proveedores'));
         // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    public function edit($id = null)
    {
        $producto = $this->Productos->get($id);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $producto = $this->Productos->patchEntity($producto, $this->request->getData());

            if ($this->Productos->save($producto)) {
                $this->Flash->success('El producto fue actualizado correctamente.');

                if (!empty($producto->categoria_producto_id)) {
                    return $this->redirect([
                        'controller' => 'CategoriasProductos',
                        'action' => 'view',
                        $producto->categoria_producto_id,
                    ]);
                }

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error('No se pudo actualizar el producto.');
        }

        $categorias = $this->Productos->CategoriasProductos
            ->find('list', keyField: 'id', valueField: 'nombre')
            ->where(['estado' => 1])
            ->order(['nombre' => 'ASC'])
            ->toArray();

        $proveedores = $this->Productos->Proveedores
            ->find('list', keyField: 'id', valueField: 'nombre')
            ->where(['activo' => 1])
            ->order(['nombre' => 'ASC'])
            ->toArray();

        $this->set(compact('producto', 'categorias', 'proveedores'));
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

        $producto = $this->Productos->get($id);
        $categoriaProductoId = $producto->categoria_producto_id;

        // Desactivación manual y directa: si el producto había sido
        // reactivado o estaba marcado por una cascada previa, esa marca ya
        // no aplica — este apagado es decisión propia del usuario, no debe
        // revivir automáticamente si luego se reactiva la categoría.
        if ($this->Productos->save(
            $this->Productos->patchEntity($producto, ['estado' => 0, 'desactivado_por_categoria' => 0])
        )) {
            $this->Flash->success('El producto fue desactivado correctamente.');
        } else {
            $this->Flash->error('No se pudo desactivar el producto.');
        }

        // Si se llegó desde la vista de una categoría, volver ahí en vez del
        // índice general, para que el usuario vea el resultado en el mismo lugar.
        $referer = $this->request->referer();
        if ($referer && str_contains($referer, '/categorias-productos/view/')) {
            return $this->redirect([
                'controller' => 'CategoriasProductos',
                'action' => 'view',
                $categoriaProductoId,
            ]);
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Reactiva un producto previamente desactivado.
     */
    public function reactivar($id = null)
    {
        $this->request->allowMethod(['post']);

        $producto = $this->Productos->get($id);
        $categoriaProductoId = $producto->categoria_producto_id;

        if ($this->Productos->save(
            $this->Productos->patchEntity($producto, ['estado' => 1, 'desactivado_por_categoria' => 0])
        )) {
            $this->Flash->success('El producto fue reactivado correctamente.');
        } else {
            $this->Flash->error('No se pudo reactivar el producto.');
        }

        $referer = $this->request->referer();
        if ($referer && str_contains($referer, '/categorias-productos/view/')) {
            return $this->redirect([
                'controller' => 'CategoriasProductos',
                'action' => 'view',
                $categoriaProductoId,
            ]);
        }

        return $this->redirect(['action' => 'index']);
    }

    public function buscar()
{
    $this->request->allowMethod(['get']);

    $q = $this->request->getQuery('q');

    if (!$q) {
        return $this->response->withType('application/json')
            ->withStringBody(json_encode([]));
    }

    $productos = $this->Productos->find()
        ->where([
            'nombre LIKE' => '%' . $q . '%',
            'estado' => 1,
        ])
        ->limit(10)
        ->all();

    $data = [];

    foreach ($productos as $p) {
        $data[] = [
            'id' => $p->id,
            'nombre' => $p->nombre,
            'precio' => (float)$p->precio,
            'stock' => (float)$p->stock
        ];
    }

    return $this->response->withType('application/json')
        ->withStringBody(json_encode($data));
}
}