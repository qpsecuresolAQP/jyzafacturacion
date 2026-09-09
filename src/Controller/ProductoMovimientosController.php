<?php
declare(strict_types=1);

namespace App\Controller;

class ProductoMovimientosController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $currentAction = $this->request->getParam('action');

        $this->verificarPermisoORedireccionar('ProductoMovimientos', $currentAction);
    }

    public function add($productoId = null)
    {
        $producto = $this->ProductoMovimientos->Productos->get($productoId);
        $movimiento = $this->ProductoMovimientos->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $tipo = $data['tipo'] ?? null;
            $cantidad = (float)($data['cantidad'] ?? 0);

            $movimiento = $this->ProductoMovimientos->patchEntity($movimiento, [
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'motivo' => $data['motivo'] ?? null,
            ]);

            if ($movimiento->getErrors()) {
                $this->Flash->error('Revisa los datos del movimiento.');
            } elseif ($tipo === 'egreso' && $cantidad > (float)$producto->stock) {
                $this->Flash->error('No hay suficiente stock para este egreso.');
            } else {
                $stockAnterior = (float)$producto->stock;
                $stockNuevo = $tipo === 'ingreso'
                    ? $stockAnterior + $cantidad
                    : $stockAnterior - $cantidad;

                $movimiento->producto_id = $producto->id;
                $movimiento->usuario_id = $this->Authentication->getIdentity()?->id;
                $movimiento->stock_anterior = $stockAnterior;
                $movimiento->stock_nuevo = $stockNuevo;

                $conn = $this->ProductoMovimientos->getConnection();
                $ok = $conn->transactional(function () use ($producto, $movimiento, $stockNuevo) {
                    $productoActualizado = $this->ProductoMovimientos->Productos->patchEntity($producto, ['stock' => $stockNuevo]);
                    if (!$this->ProductoMovimientos->Productos->save($productoActualizado)) {
                        return false;
                    }

                    return (bool)$this->ProductoMovimientos->save($movimiento);
                });

                if ($ok) {
                    $this->Flash->success('El movimiento de stock fue registrado correctamente.');

                    return $this->redirect($this->origenRedirect($producto));
                }

                $this->Flash->error('No se pudo registrar el movimiento.');
            }
        }

        $this->set(compact('producto', 'movimiento'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    public function historial($productoId = null)
    {
        $producto = $this->ProductoMovimientos->Productos->get($productoId);

        $movimientos = $this->ProductoMovimientos->find()
            ->where(['producto_id' => $productoId])
            ->contain(['Users'])
            ->order(['ProductoMovimientos.created' => 'DESC'])
            ->all();

        $this->set(compact('producto', 'movimientos'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Decide a dónde volver tras registrar un movimiento: si se llegó desde
     * el listado general de Productos, vuelve ahí; si se llegó desde la
     * vista de una categoría, vuelve a esa categoría (comportamiento previo).
     */
    private function origenRedirect($producto): array
    {
        $referer = $this->request->referer();
        if ($referer && str_contains($referer, '/productos') && !str_contains($referer, '/categorias-productos/')) {
            return ['controller' => 'Productos', 'action' => 'index'];
        }

        return [
            'controller' => 'CategoriasProductos',
            'action' => 'view',
            $producto->categoria_producto_id,
        ];
    }
}
