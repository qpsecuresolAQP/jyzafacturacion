<?php

declare(strict_types=1);

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Cake\Routing\Router;

/**
 * Presupuestos Controller
 *
 * @property \App\Model\Table\PresupuestosTable $Presupuestos
 */
class PresupuestosController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $currentAction = $this->request->getParam('action');

        $this->verificarPermisoORedireccionar('Presupuestos', $currentAction);
    }

    public function exportPresupuestoPdf($id = null)
    {
        $logoUrl = Router::url('/img/logoClinica.png', true);
        // Obtener el paciente por ID y cargar las relaciones necesarias
        $presupuesto = $this->Presupuestos->get($id, contain: [
            'HistoriasClinicas.Pacientes',
            'PresupuestosTratamientos.Tratamientos',
            'PresupuestosTratamientos.Productos',
            'PresupuestosTratamientos.Examenes',
        ]);

        // Renderizar la vista HTML como contenido para el PDF
        $this->viewBuilder()->enableAutoLayout(false);
        $this->set(compact('presupuesto', 'logoUrl'));
        $html = $this->render('presupuesto_detalle');

        // Configurar DomPDF para el PDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);

        // Configurar tamaño de papel
        $dompdf->setPaper('A4', 'portrait');

        // Renderizar el PDF
        $dompdf->render();

        // Descargar el archivo PDF
        $dompdf->stream("presupuesto_detalle_{$id}.pdf", ['Attachment' => 1]);
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // Obtener el término de búsqueda de la solicitud (GET)
        $searchTerm = $this->request->getQuery('search', '');

        // Construir la consulta inicial
        $query = $this->Presupuestos->find()
            ->contain(['HistoriasClinicas' => ['Pacientes']])
            ->order(['Presupuestos.id' => 'DESC']);

        // Agregar condiciones de búsqueda si hay un término
        if (!empty($searchTerm)) {

            $query
                ->leftJoinWith('HistoriasClinicas.Pacientes')
                ->where([
                    'OR' => [
                        'LOWER(Pacientes.nombre) LIKE' => '%' . strtolower($searchTerm) . '%',
                        'LOWER(Pacientes.apellido) LIKE' => '%' . strtolower($searchTerm) . '%',
                        'LOWER(Presupuestos.nombre_apellido) LIKE' => '%' . strtolower($searchTerm) . '%',
                    ]
                ]);
        }

        // Paginar los resultados
        $presupuestos = $this->paginate($query);

        // Pasar datos a la vista
        $this->set(compact('presupuestos', 'searchTerm'));
    }

    /**
     * View method
     *
     * @param string|null $id Presupuesto id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $presupuesto = $this->Presupuestos->get($id, contain: [
            'HistoriasClinicas' => ['Pacientes'],
            'PresupuestosTratamientos.Tratamientos',
            'PresupuestosTratamientos.Productos',
            'PresupuestosTratamientos.Examenes',
            'PresupuestosInvoices.Invoices',
        ]);

        // Facturas vigentes (no anuladas) generadas desde este presupuesto
        $invoiceIds = array_map(
            fn($pi) => $pi->invoice_id,
            array_filter(
                $presupuesto->presupuestos_invoices,
                fn($pi) => ($pi->invoice->estado ?? '') !== 'ANULADO'
            )
        );

        // Monto ya facturado por ítem (tratamiento/producto/examen), sumando los invoice_items de esas facturas.
        // Se agrupa por una clave compuesta tipo_item + id, para no mezclar montos entre un
        // tratamiento, un producto y un examen que compartan el mismo id numérico.
        $montoFacturadoPorItem = [];
        if (!empty($invoiceIds)) {
            $items = $this->Presupuestos->PresupuestosInvoices->Invoices->InvoiceItems->find()
                ->where(['invoice_id IN' => $invoiceIds])
                ->all();

            foreach ($items as $item) {
                $claveItem = ($item->tipo_item ?? 'tratamiento') . '_' . (
                    $item->tratamiento_id ?? $item->producto_id ?? $item->examen_id
                );
                $montoFacturadoPorItem[$claveItem] =
                    ($montoFacturadoPorItem[$claveItem] ?? 0) + (float) $item->total;
            }
        }

        $this->set(compact('presupuesto', 'montoFacturadoPorItem'));
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
        $presupuesto = $this->Presupuestos->newEmptyEntity();
        $pacienteSeleccionado = null; // Variable para el paciente seleccionado

        // Obtener historia_id del query string (?historia_id=123)
        $historia_id = $this->request->getQuery('historia_id');

        // Prellenar datos si el historia_id fue pasado como parámetro en la URL
        if ($historia_id) {
            $presupuesto->historia_id = $historia_id;

            try {
                $historiaClinica = $this->Presupuestos
                    ->HistoriasClinicas
                    ->get($historia_id, [
                        'contain' => ['Pacientes']
                    ]);

                $pacienteSeleccionado = $historiaClinica->paciente;
            } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                $this->Flash->error(__('Historia clínica no encontrada'));
            }
        }

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Combina la fecha ingresada por el usuario con la hora actual
            $fechaUsuario = new \DateTime($data['modified']);
            $horaActual = new \DateTime();
            $fechaUsuario->setTime((int)$horaActual->format('H'), (int)$horaActual->format('i'), (int)$horaActual->format('s'));

            // Asigna la fecha combinada al campo modified
            $data['modified'] = $fechaUsuario->format('Y-m-d H:i:s');
            $tratamientosData = $data['tratamientos'] ?? [];
            $totalConIgv = $data['total'] ?? 0;

            // Remover datos de tratamientos antes de guardar el presupuesto
            unset($data['tratamientos']);
            $presupuesto = $this->Presupuestos->patchEntity($presupuesto, $data);

            $presupuesto->total = $totalConIgv;

            if ($this->Presupuestos->save($presupuesto)) {
                $presupuestoId = $presupuesto->id;

                // Procesar los tratamientos/productos/examenes y guardarlos en PresupuestosTratamientos
                foreach ($tratamientosData as $tratamiento) {
                    $tipoItem = $tratamiento['tipo_item'] ?? 'tratamiento';
                    $presupuestoTratamiento = $this->Presupuestos->PresupuestosTratamientos->newEmptyEntity();
                    $presupuestoTratamiento = $this->Presupuestos->PresupuestosTratamientos->patchEntity($presupuestoTratamiento, [
                        'presupuesto_id' => $presupuestoId,
                        'tipo_item' => $tipoItem,
                        'tratamiento_id' => $tratamiento['tratamiento_id'] ?? null,
                        'producto_id' => $tratamiento['producto_id'] ?? null,
                        'examen_id' => $tratamiento['examen_id'] ?? null,
                        'precio_unitario' => $tratamiento['precio_unitario'],
                        'cantidad' => $tratamiento['cantidad'],
                        'pz_dental' => $tratamiento['pz_dental'] ?? null,
                        'descuento' => $tratamiento['descuento'] ?? null,
                        'descuento_tipo' => $tratamiento['descuento_tipo'] ?? 'porcentaje',
                        'total' => $tratamiento['total'],
                        'observaciones' => $tratamiento['observaciones'] ?? null,
                    ]);

                    if (!$this->Presupuestos->PresupuestosTratamientos->save($presupuestoTratamiento)) {
                        $this->Flash->error(__('No se pudo guardar el ítem: {0}', $tratamiento['tratamiento_id'] ?? $tratamiento['producto_id'] ?? $tratamiento['examen_id'] ?? ''));
                    }
                }

                $this->Flash->success(__('El presupuesto ha sido guardado.'));

                // Obtener el historia_id y paciente_id del presupuesto guardado
                $historiaId = $presupuesto->historia_id;
                if ($historiaId) {
                    $historiaClinica = $this->Presupuestos->HistoriasClinicas->get($historiaId);
                    $pacienteId = $historiaClinica->paciente_id;
                    return $this->redirect(['controller' => 'Pacientes', 'action' => 'view', $pacienteId, '#' => 'presupuestos']);
                }

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('El presupuesto no pudo ser guardado. Por favor, inténtelo de nuevo.'));
        }

        $pacientesData = $this->Presupuestos->HistoriasClinicas->Pacientes->find('all')
            ->select(['id', 'nombre', 'apellido'])
            ->toArray();

        // Para pacientesData2, necesitamos el paciente_id de historias_clinicas y sus datos (dni, direccion, historia_id)
        $historias = $this->Presupuestos->HistoriasClinicas->find('all')
            ->select(['id', 'paciente_id', 'dni', 'direccion'])
            ->toArray();

        // Mapear los datos para que JavaScript pueda encontrar por paciente_id y obtener historia_id
        $pacientesData2 = [];
        foreach ($historias as $historia) {
            $pacientesData2[] = [
                'paciente_id' => $historia->paciente_id,
                'historia_id' => $historia->id,
                'dni' => $historia->dni,
                'direccion' => $historia->direccion
            ];
        }

        $tratamientosData = $this->Presupuestos->PresupuestosTratamientos->Tratamientos->find('all')
            ->select(['id', 'nombre', 'costo'])
            ->where(['estado' => 1])
            ->toArray();

        $productosData = $this->Presupuestos->PresupuestosTratamientos->Productos->find('all')
            ->select(['id', 'nombre', 'precio'])
            ->where(['estado' => 1])
            ->toArray();

        $examenesData = $this->Presupuestos->PresupuestosTratamientos->Examenes->find('all')
            ->select(['id', 'nombre', 'precio'])
            ->where(['estado' => 1])
            ->toArray();

        // Pasar el historia_id si viene desde la URL
        $historiaIdPreseleccionada = $historia_id ?? null;

        $this->set(compact('presupuesto', 'pacientesData', 'tratamientosData', 'productosData', 'examenesData', 'pacienteSeleccionado', 'pacientesData2', 'historiaIdPreseleccionada'));
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
     * @param string|null $id Presupuesto id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id)
    {
        $presupuesto = $this->Presupuestos->get($id, contain: [
            'PresupuestosTratamientos' => [
                'Tratamientos',
                'Productos',
                'Examenes',
            ],
            'HistoriasClinicas' => [
                'Pacientes',
            ],
        ]);


        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();
            // Combina la fecha ingresada por el usuario con la hora actual
            $fechaUsuario = new \DateTime($data['modified']);
            $horaActual = new \DateTime();
            $fechaUsuario->setTime((int)$horaActual->format('H'), (int)$horaActual->format('i'), (int)$horaActual->format('s'));

            // Asigna la fecha combinada al campo modified
            $data['modified'] = $fechaUsuario->format('Y-m-d H:i:s');
            $tratamientosData = $data['tratamientos'] ?? [];
            $totalConIgv = $data['total'] ?? 0;

            unset($data['tratamientos']);
            $presupuesto = $this->Presupuestos->patchEntity($presupuesto, $data);
            $presupuesto->total = $totalConIgv;

            if ($this->Presupuestos->save($presupuesto)) {
                foreach ($tratamientosData as $tratamiento) {
                    $tipoItem = $tratamiento['tipo_item'] ?? 'tratamiento';
                    // Si el ID está vacío, asumimos que es un nuevo tratamiento
                    if (empty($tratamiento['id'])) {
                        $presupuestoTratamiento = $this->Presupuestos->PresupuestosTratamientos->newEntity([
                            'presupuesto_id' => $presupuesto->id,
                            'tipo_item' => $tipoItem,
                            'tratamiento_id' => $tratamiento['tratamiento_id'] ?? null,
                            'producto_id' => $tratamiento['producto_id'] ?? null,
                            'examen_id' => $tratamiento['examen_id'] ?? null,
                            'precio_unitario' => $tratamiento['precio_unitario'],
                            'pz_dental' => $tratamiento['pz_dental'] ?? null,
                            'descuento' => $tratamiento['descuento'] ?? null,
                            'descuento_tipo' => $tratamiento['descuento_tipo'] ?? 'porcentaje',
                            'cantidad' => $tratamiento['cantidad'],
                            'total' => $tratamiento['total'],
                            'observaciones' => $tratamiento['observaciones'] ?? null,
                        ]);

                        if (!$this->Presupuestos->PresupuestosTratamientos->save($presupuestoTratamiento)) {
                            $this->Flash->error(__('No se pudo guardar el nuevo ítem: {0}. Errores: {1}', $tratamiento['tratamiento_id'] ?? $tratamiento['producto_id'] ?? $tratamiento['examen_id'] ?? '', json_encode($presupuestoTratamiento->getErrors())));
                        }
                    } else {
                        // Si el ID existe, editamos el tratamiento existente
                        if ($this->Presupuestos->PresupuestosTratamientos->exists(['id' => $tratamiento['id']])) {
                            $presupuestoTratamiento = $this->Presupuestos->PresupuestosTratamientos->get($tratamiento['id']);
                            $presupuestoTratamiento = $this->Presupuestos->PresupuestosTratamientos->patchEntity($presupuestoTratamiento, [
                                'tipo_item' => $tipoItem,
                                'tratamiento_id' => $tratamiento['tratamiento_id'] ?? null,
                                'producto_id' => $tratamiento['producto_id'] ?? null,
                                'examen_id' => $tratamiento['examen_id'] ?? null,
                                'precio_unitario' => $tratamiento['precio_unitario'],
                                'pz_dental' => $tratamiento['pz_dental'] ?? null,
                                'descuento' => $tratamiento['descuento'] ?? null,
                                'descuento_tipo' => $tratamiento['descuento_tipo'] ?? 'porcentaje',
                                'cantidad' => $tratamiento['cantidad'],
                                'total' => $tratamiento['total'],
                                'observaciones' => $tratamiento['observaciones'] ?? null,
                            ]);

                            if (!$this->Presupuestos->PresupuestosTratamientos->save($presupuestoTratamiento)) {
                                $this->Flash->error(__('No se pudo guardar el ítem existente: {0}. Errores: {1}', $tratamiento['tratamiento_id'] ?? $tratamiento['producto_id'] ?? $tratamiento['examen_id'] ?? '', json_encode($presupuestoTratamiento->getErrors())));
                            }
                        } else {
                            $this->Flash->error(__('No se encontró el ítem asociado con el ID: {0}', $tratamiento['id']));
                            continue; // Salta este tratamiento
                        }
                    }
                }
                $this->Flash->success(__('El presupuesto ha sido guardado.'));

                if (!empty($presupuesto->historia_id)) {

                    $historiaClinica = $this->Presupuestos->HistoriasClinicas->get(
                        $presupuesto->historia_id
                    );

                    return $this->redirect([
                        'controller' => 'Pacientes',
                        'action' => 'view',
                        $historiaClinica->paciente_id,
                        '#' => 'presupuestos'
                    ]);
                }

                // Si es proforma
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('El presupuesto no pudo ser guardado. Por favor, inténtelo de nuevo.'));
        }

        $pacientesData = $this->Presupuestos->HistoriasClinicas->Pacientes->find('all')
            ->select(['id', 'nombre', 'apellido'])
            ->toArray();

        // Para pacientesData2, necesitamos el paciente_id de historias_clinicas y sus datos (dni, direccion, historia_id)
        $historias = $this->Presupuestos->HistoriasClinicas->find('all')
            ->select(['id', 'paciente_id', 'dni', 'direccion'])
            ->toArray();

        // Mapear los datos para que JavaScript pueda encontrar por paciente_id y obtener historia_id
        $pacientesData2 = [];
        foreach ($historias as $historia) {
            $pacientesData2[] = [
                'paciente_id' => $historia->paciente_id,
                'historia_id' => $historia->id,
                'dni' => $historia->dni,
                'direccion' => $historia->direccion
            ];
        }

        $tratamientosData = $this->Presupuestos->PresupuestosTratamientos->Tratamientos->find('all')
            ->select(['id', 'nombre', 'costo'])
            ->where(['estado' => 1])
            ->toArray();

        $productosData = $this->Presupuestos->PresupuestosTratamientos->Productos->find('all')
            ->select(['id', 'nombre', 'precio'])
            ->where(['estado' => 1])
            ->toArray();

        $examenesData = $this->Presupuestos->PresupuestosTratamientos->Examenes->find('all')
            ->select(['id', 'nombre', 'precio'])
            ->where(['estado' => 1])
            ->toArray();

        $this->set(compact('presupuesto', 'pacientesData', 'tratamientosData', 'productosData', 'examenesData', 'pacientesData2'));
        // Usar un layout diferenciado para solicitudes normales o AJAX
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Delete method
            ->toArray();

        $this->set(compact('presupuesto', 'pacientesData', 'tratamientosData', 'pacientesData2'));
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
     * @param string|null $id Presupuesto id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $presupuesto = $this->Presupuestos->get($id);
        if ($this->Presupuestos->delete($presupuesto)) {
            $this->Flash->success(__('The presupuesto has been deleted.'));
        } else {
            $this->Flash->error(__('The presupuesto could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
