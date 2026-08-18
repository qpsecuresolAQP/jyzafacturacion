<?php

declare(strict_types=1);

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;

/**
 * Consultas Controller
 *
 * @property \App\Model\Table\ConsultasTable $Consultas
 */
class ConsultasController extends AppController
{
    /**
     * Verificación centralizada de permisos
     * Se ejecuta antes de cualquier acción
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        $currentAction = $this->request->getParam('action');
        
        $this->verificarPermisoORedireccionar('Consultas', $currentAction);
    }

    public function exportConsultasPdf($id = null)
    {
        $logoUrl = Router::url('/img/logoClinica.png', true);
        // Obtener los datos completos de la receta consulta
        $consulta = $this->Consultas->get($id, contain: [
            'HistoriasClinicas' => ['Pacientes'],
            'ConsultasCie' => ['Diagnosticoscie10'],
            'Doctores'
        ]);

        // Renderizar la vista HTML como contenido para el PDF
        $this->viewBuilder()->enableAutoLayout(false);
        $this->set(compact('consulta', 'logoUrl'));
        $html = $this->render('consulta_proce');

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
        $dompdf->stream("Orden_Medica_{$id}.pdf", ['Attachment' => 0]);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Consultas->find();
        $query->order(['Consultas.id' => 'DESC']);
        $consultas = $this->paginate($query);

        $this->set(compact('consultas'));
    }

    /**
     * View method
     *
     * @param string|null $id Consulta id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
     public function view($id = null)
    {
        $consulta = $this->Consultas->get($id, contain: [
            'DocumentosConsultas.Documentos',
            'Users',
            'HistoriasClinicas.Pacientes',
            'Doctores',
            'ConsultasCie' => ['Diagnosticoscie10', 'Categoriascie10'],
            'Recetas' => ['RecetasMedicamentos' => ['Medicamentos', 'FormasFarmaceuticas', 'ViasAdministracion']]
        ]);
        $this->set(compact('consulta'));
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
    public function add($historia_id = null)
    {
        $consulta = $this->Consultas->newEmptyEntity();
        $usuario = $this->Authentication->getIdentity();
        $historia = null;

        if ($historia_id) {
            $historia = $this->Consultas->HistoriasClinicas->get($historia_id, contain: ['Pacientes']);
            $consulta->historia_id = $historia_id;
            $consulta->paciente_id = $historia->paciente_id;
        }

        if ($usuario->rol_id == 2) {
            $consulta->doctor_id = $usuario->doctor_id;
        }

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['user_id'] = $usuario->id;

            if ($usuario->rol_id == 2) {
                $data['doctor_id'] = $usuario->doctor_id;
            }

            if (!$historia) {
                $this->Flash->error(__('No se encontró la historia clínica.'));
                return $this->redirect($this->referer());
            }

            $data['historia_id'] = $historia->id;
            $data['paciente_id'] = $historia->paciente_id;

            // Procesar CIE
            if (!empty($data['cie_data'])) {
                $cieRelaciones = [];

                foreach ($data['cie_data'] as $json) {
                    $item = json_decode($json, true);

                    if (!empty($item['clave']) && strlen($item['clave']) > 3) {
                        // Es un CIE (clave larga)
                        $cieRelaciones[] = ['cie_id' => $item['id']];
                    } else {
                        // Es una categoría (clave corta)
                        $cieRelaciones[] = ['categoria_id' => $item['id']];
                    }
                }

                $data['consultas_cie'] = $cieRelaciones;
            }
            $consulta = $this->Consultas->patchEntity($consulta, $data, [
                'associated' => ['ConsultasCie'] // Solo asociamos CIE por ahora
            ]);
            if ($this->Consultas->save($consulta)) {

                // 📂 Manejo de documentos subidos — comprimir solo imágenes
                // Manejo de documentos subidos en add — compresión + corrección EXIF
                if (!empty($this->request->getUploadedFiles()['documentos'])) {
                    $documentos = $this->request->getUploadedFiles()['documentos'];

                    $documentosTable = $this->fetchTable('Documentos');
                    $documentosConsultasTable = $this->fetchTable('DocumentosConsultas');

                    foreach ($documentos as $archivo) {
                        if (empty($archivo->getClientFilename())) {
                            continue;
                        }

                        $dir = WWW_ROOT . "uploads/HistoriaClinica/{$consulta->historia_id}/Consultas/";
                        if (!is_dir($dir)) {
                            mkdir($dir, 0755, true);
                        }

                        $originalFilename = $archivo->getClientFilename();
                        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalFilename);
                        $filepath = $dir . $safeName;

                        if ($archivo->getError() !== UPLOAD_ERR_OK) {
                            $this->Flash->error(__('Error al subir el archivo: {0}', $archivo->getError()));
                            continue;
                        }

                        // Guardar stream a temp
                        $ext = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
                        $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('upload_') . '.' . $ext;
                        file_put_contents($tmp, $archivo->getStream()->getContents());

                        // Detectar si es imagen y si GD está disponible
                        $imgInfo = @getimagesize($tmp);
                        $isImage = ($imgInfo !== false && extension_loaded('gd'));

                        if ($isImage) {
                            // Parámetros
                            $maxWidth = 1600; // ancho máximo
                            $quality = 75;    // calidad JPEG/WebP 0-100

                            // Crear resource origen
                            $type = $imgInfo[2];
                            $src = false;
                            switch ($type) {
                                case IMAGETYPE_JPEG:
                                    $src = @imagecreatefromjpeg($tmp);
                                    break;
                                case IMAGETYPE_PNG:
                                    $src = @imagecreatefrompng($tmp);
                                    break;
                                case IMAGETYPE_WEBP:
                                    if (function_exists('imagecreatefromwebp')) {
                                        $src = @imagecreatefromwebp($tmp);
                                    } else {
                                        $src = @imagecreatefromstring(file_get_contents($tmp));
                                    }
                                    break;
                                default:
                                    $src = @imagecreatefromstring(file_get_contents($tmp));
                            }

                            if ($src !== false) {
                                // --- CORRECCIÓN DE ORIENTACIÓN EXIF para JPEG ---
                                if ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
                                    $exif = @exif_read_data($tmp);
                                    if (!empty($exif['Orientation'])) {
                                        $orientation = (int)$exif['Orientation'];
                                        if ($orientation === 3) {
                                            $rot = imagerotate($src, 180, 0);
                                            if ($rot !== false) {
                                                imagedestroy($src);
                                                $src = $rot;
                                            }
                                        } elseif ($orientation === 6) {
                                            $rot = imagerotate($src, -90, 0);
                                            if ($rot !== false) {
                                                imagedestroy($src);
                                                $src = $rot;
                                            }
                                        } elseif ($orientation === 8) {
                                            $rot = imagerotate($src, 90, 0);
                                            if ($rot !== false) {
                                                imagedestroy($src);
                                                $src = $rot;
                                            }
                                        }
                                        // notar: orientaciones con mirror no están manejadas aquí
                                    }
                                }

                                // Obtener dimensiones desde el resource (por si rotó)
                                $width = imagesx($src);
                                $height = imagesy($src);

                                // calcular nuevo tamaño manteniendo aspecto
                                if ($width > $maxWidth) {
                                    $ratio = $height / $width;
                                    $newWidth = $maxWidth;
                                    $newHeight = (int) round($maxWidth * $ratio);
                                } else {
                                    $newWidth = $width;
                                    $newHeight = $height;
                                }

                                $dst = imagecreatetruecolor($newWidth, $newHeight);

                                // preservar transparencia PNG/WEBP
                                if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP) {
                                    imagealphablending($dst, false);
                                    imagesavealpha($dst, true);
                                }

                                imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                                $saved = false;
                                if ($type == IMAGETYPE_JPEG) {
                                    $saved = imagejpeg($dst, $filepath, $quality);
                                } elseif ($type == IMAGETYPE_PNG) {
                                    $pngLevel = (int) round((100 - $quality) / 11.1111);
                                    $saved = imagepng($dst, $filepath, $pngLevel);
                                } elseif ($type == IMAGETYPE_WEBP && function_exists('imagewebp')) {
                                    $saved = imagewebp($dst, $filepath, $quality);
                                } else {
                                    $saved = imagejpeg($dst, $filepath, $quality);
                                }

                                imagedestroy($src);
                                imagedestroy($dst);

                                // fallback mover original si algo falla
                                if (!$saved) {
                                    if (!rename($tmp, $filepath)) copy($tmp, $filepath);
                                }
                            } else {
                                // no se pudo crear resource -> guardar original
                                if (!rename($tmp, $filepath)) copy($tmp, $filepath);
                            }
                        } else {
                            // No es imagen o GD no disponible -> guardar tal cual (PDF u otros)
                            if (!rename($tmp, $filepath)) copy($tmp, $filepath);
                        }

                        // limpiar temp si quedó
                        if (file_exists($tmp)) @unlink($tmp);

                        // Registrar en BD si archivo existe
                        if (file_exists($filepath)) {
                            $documento = $documentosTable->newEntity([
                                'historia_id' => $consulta->historia_id,
                                'ruta_archivo' => "uploads/HistoriaClinica/{$consulta->historia_id}/Consultas/{$safeName}",
                                'tipo' => 'consulta'
                            ]);

                            if ($documentosTable->save($documento)) {
                                $documentoConsulta = $documentosConsultasTable->newEntity([
                                    'consulta_id' => $consulta->id,
                                    'documento_id' => $documento->id
                                ]);
                                $documentosConsultasTable->save($documentoConsulta);
                            } else {
                                @unlink($filepath);
                                $this->Flash->error(__('No se pudo registrar el documento en la base de datos.'));
                            }
                        } else {
                            $this->Flash->error(__('Hubo un problema al guardar el archivo: {0}', $originalFilename));
                        }
                    }
                }

                // Procesar recetas si fueron añadidas desde el formulario
                if (!empty($data['medicamentos_receta']) || !empty($data['receta_datos'])) {
                    $medicamentosJson = $data['medicamentos_receta'] ?? '[]';
                    $recetaDatosJson = $data['receta_datos'] ?? '{}';

                    $medicamentos = json_decode($medicamentosJson, true);
                    $recetaDatos = json_decode($recetaDatosJson, true);

                    // Solo crear receta si hay medicamentos o nombre
                    if (!empty($medicamentos) || !empty($recetaDatos['nombre'])) {
                        $recetasTable = TableRegistry::getTableLocator()->get('Recetas');
                        $recetasConsultasTable = TableRegistry::getTableLocator()->get('RecetasConsultas');
                        $recetasMedicamentosTable = TableRegistry::getTableLocator()->get('RecetasMedicamentos');

                        // Crear la receta
                        $receta = $recetasTable->newEntity([
                            'nombre' => $recetaDatos['nombre'] ?? 'Receta sin nombre',
                            'descripcion' => $recetaDatos['descripcion'] ?? '',
                            'notas' => $recetaDatos['notas'] ?? '',
                            'tipo_usuario' => $recetaDatos['tipo_usuario'] ?? '',
                            'tipo_atencion' => $recetaDatos['tipo_atencion'] ?? '',
                            'especialidad_medica' => $recetaDatos['especialidad_medica'] ?? '',
                            'h_cl' => $recetaDatos['h_cl'] ?? '',
                            'sis' => $recetaDatos['sis'] ?? '',
                            'particular' => $recetaDatos['particular'] ?? '',
                            'peso' => $recetaDatos['peso'] ?? null,
                            'valido_hasta' => $recetaDatos['valido_hasta'] ?? null,
                            'indicaciones_consolidadas' => $recetaDatos['indicaciones_consolidadas'] ?? ''
                        ]);

                        if ($recetasTable->save($receta)) {
                            // Asociar receta con consulta
                            $recetaConsulta = $recetasConsultasTable->newEntity([
                                'receta_id' => $receta->id,
                                'consulta_id' => $consulta->id
                            ]);
                            $recetasConsultasTable->save($recetaConsulta);

                            // Agregar medicamentos a la receta
                            if (!empty($medicamentos) && is_array($medicamentos)) {
                                foreach ($medicamentos as $med) {
                                    $recetaMedicamento = $recetasMedicamentosTable->newEntity([
                                        'receta_id' => $receta->id,
                                        'medicamento_id' => $med['id'] ?? null,
                                        'nombre_medicamento' => $med['nombre_medicamento'] ?? $med['nombre'] ?? '',
                                        'codigo_medicamento' => $med['codigo_medicamento'] ?? $med['codigo'] ?? '',
                                        'concentracion' => $med['concentracion'] ?? '',
                                        'cantidad' => $med['cantidad'] ?? 0,
                                        'forma_farmaceutica_id' => $med['forma_id'] ?? null,
                                        'via_administracion_id' => $med['via_id'] ?? null,
                                        'duracion_dias' => $med['duracion_dias'] ?? 0,
                                        'dosis' => $med['dosis'] ?? '',
                                        'observaciones' => $med['observaciones'] ?? ''
                                    ]);
                                    $recetasMedicamentosTable->save($recetaMedicamento);
                                }
                            }

                            // Actualizar indicaciones consolidadas
                            $this->_actualizarIndicacionesReceta($receta->id);
                        }
                    }
                }

                $this->Flash->success(__('La consulta ha sido registrada con éxito.'));
                return $this->redirect(['controller' => 'Pacientes', 'action' => 'view', $historia->paciente_id, '#' => 'consultas']);
            }

            $this->Flash->error(__('No se pudo guardar la consulta. Intente de nuevo.'));
        }

        // Si viene con historia_id cargamos solo ese paciente (el select queda disabled)
        // Si no viene, la vista usa búsqueda AJAX igual que Recordatorios
        $pacienteSeleccionado = ($historia && $historia->paciente) ? $historia->paciente : null;

        $doctores = $this->Consultas->Doctores->find(
            'list',
            keyField: 'id',
            valueField: 'nombre'
        )->toArray();

        $this->set(compact('consulta', 'historia_id', 'usuario', 'pacienteSeleccionado', 'doctores', 'historia'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Consulta id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $consulta = $this->Consultas->get(
            $id,
            contain: ['ConsultasCie' => ['Diagnosticoscie10', 'Categoriascie10'], 'HistoriasClinicas' => ['Pacientes'], 'Recetas' => ['RecetasMedicamentos' => ['Medicamentos', 'FormasFarmaceuticas', 'ViasAdministracion']]]
        );

        $usuario = $this->Authentication->getIdentity(); // Usuario autenticado

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Solo actualizar CIEs si viene información de CIEs en el formulario
            if (!empty($data['cie_data']) && is_array($data['cie_data'])) {
                // Eliminar todos los CIE actuales antes de agregar los nuevos
                $consultasCieTable = $this->fetchTable('ConsultasCie');
                if (is_iterable($consulta->consultas_cie)) {
                    foreach ($consulta->consultas_cie as $cie) {
                        $consultasCieTable->delete($cie);
                    }
                }
            }

            // Procesar recetas y medicamentos
            if (!empty($data['medicamentos_receta']) || !empty($data['receta_datos'])) {
                $medicamentosData = [];
                $recetaDatos = [];
                $recetaIdEditando = $data['receta_id_editando'] ?? null;

                if (!empty($data['medicamentos_receta'])) {
                    $medicamentosData = json_decode($data['medicamentos_receta'], true) ?? [];
                }

                if (!empty($data['receta_datos'])) {
                    $recetaDatos = json_decode($data['receta_datos'], true) ?? [];
                }

                // Si estamos editando una receta existente, actualizarla (no eliminarla)
                if (!empty($recetaIdEditando)) {
                    $recetasTable = $this->fetchTable('Recetas');
                    $recetaAEditar = $recetasTable->get($recetaIdEditando);

                    // Actualizar datos de la receta (nombre, descripción, notas y nuevos campos)
                    $recetaAEditar = $recetasTable->patchEntity($recetaAEditar, [
                        'nombre' => $recetaDatos['nombre'] ?? '',
                        'descripcion' => $recetaDatos['descripcion'] ?? '',
                        'notas' => $recetaDatos['notas'] ?? '',
                        'tipo_usuario' => $recetaDatos['tipo_usuario'] ?? '',
                        'tipo_atencion' => $recetaDatos['tipo_atencion'] ?? '',
                        'especialidad_medica' => $recetaDatos['especialidad_medica'] ?? '',
                        'h_cl' => $recetaDatos['h_cl'] ?? '',
                        'sis' => $recetaDatos['sis'] ?? '',
                        'particular' => $recetaDatos['particular'] ?? '',
                        'peso' => $recetaDatos['peso'] ?? null,
                        'valido_hasta' => $recetaDatos['valido_hasta'] ?? null,
                        'indicaciones_consolidadas' => $recetaDatos['indicaciones_consolidadas'] ?? '',
                    ]);
                    $recetasTable->save($recetaAEditar);

                    // Eliminar medicamentos viejos de la receta
                    $recetasMedicamentosTable = $this->fetchTable('RecetasMedicamentos');
                    $medicamentosExistentes = $recetasMedicamentosTable->find()
                        ->where(['receta_id' => $recetaIdEditando])
                        ->toArray();

                    foreach ($medicamentosExistentes as $med) {
                        $recetasMedicamentosTable->delete($med);
                    }

                    // Agregar medicamentos nuevos a la receta existente
                    foreach ($medicamentosData as $med) {
                        $recetaMedicamento = $recetasMedicamentosTable->newEntity([
                            'receta_id' => $recetaIdEditando,
                            'medicamento_id' => $med['id'] ?? null,
                            'nombre_medicamento' => $med['nombre_medicamento'] ?? $med['nombre'] ?? '',
                            'codigo_medicamento' => $med['codigo_medicamento'] ?? $med['codigo'] ?? '',
                            'concentracion' => $med['concentracion'] ?? '',
                            'cantidad' => $med['cantidad'],
                            'forma_farmaceutica_id' => $med['forma_id'],
                            'via_administracion_id' => $med['via_id'],
                            'duracion_dias' => $med['duracion_dias'] ?? $med['duracion'] ?? 0,
                            'dosis' => $med['dosis'] ?? '',
                            'observaciones' => $med['observaciones'] ?? '',
                        ]);
                        $recetasMedicamentosTable->save($recetaMedicamento);
                    }
                } else {
                    // Crear nueva receta solo si hay medicamentos O si el nombre de la receta tiene contenido
                    $tieneNombre = !empty($recetaDatos['nombre'] ?? '');
                    if (!empty($medicamentosData) || $tieneNombre) {
                        $recetasTable = $this->fetchTable('Recetas');
                        $recetasConsultasTable = $this->fetchTable('RecetasConsultas');

                        $nuevaReceta = $recetasTable->newEntity([
                            'nombre' => $recetaDatos['nombre'] ?? '',
                            'descripcion' => $recetaDatos['descripcion'] ?? '',
                            'notas' => $recetaDatos['notas'] ?? '',
                            'tipo_usuario' => $recetaDatos['tipo_usuario'] ?? '',
                            'tipo_atencion' => $recetaDatos['tipo_atencion'] ?? '',
                            'especialidad_medica' => $recetaDatos['especialidad_medica'] ?? '',
                            'h_cl' => $recetaDatos['h_cl'] ?? '',
                            'sis' => $recetaDatos['sis'] ?? '',
                            'particular' => $recetaDatos['particular'] ?? '',
                            'peso' => $recetaDatos['peso'] ?? null,
                            'indicaciones_consolidadas' => $recetaDatos['indicaciones_consolidadas'] ?? '',
                            'fecha_creacion' => new \DateTime(),
                        ]);

                        if ($recetasTable->save($nuevaReceta)) {
                            // Crear asociación en RecetasConsultas
                            $recetaConsulta = $recetasConsultasTable->newEntity([
                                'receta_id' => $nuevaReceta->id,
                                'consulta_id' => $consulta->id
                            ]);
                            $recetasConsultasTable->save($recetaConsulta);

                            // Agregar medicamentos a la receta
                            $recetasMedicamentosTable = $this->fetchTable('RecetasMedicamentos');
                            foreach ($medicamentosData as $med) {
                                $recetaMedicamento = $recetasMedicamentosTable->newEntity([
                                    'receta_id' => $nuevaReceta->id,
                                    'medicamento_id' => $med['id'],
                                    'cantidad' => $med['cantidad'],
                                    'forma_farmaceutica_id' => $med['forma_id'],
                                    'via_administracion_id' => $med['via_id'],
                                    'duracion_dias' => $med['duracion_dias'] ?? $med['duracion'] ?? 0,
                                    'observaciones' => $med['observaciones'] ?? '',
                                ]);
                                $recetasMedicamentosTable->save($recetaMedicamento);
                            }
                        }
                    }
                }
            }

            // Preparar relaciones segun lo que viene del formulario
            $cieRelaciones = [];

            if (!empty($data['cie_data'])) {
                foreach ($data['cie_data'] as $json) {
                    $item = json_decode($json, true);
                    $clave = $item['clave'] ?? '';
                    $id = $item['id'];

                    if (strlen($clave) > 3) {
                        // Es un cie_id
                        $cieRelaciones[] = ['cie_id' => $id];
                    } else {
                        // Es una categoria_id
                        $cieRelaciones[] = ['categoria_id' => $id];
                    }
                }
                $data['consultas_cie'] = $cieRelaciones;
            } else {
                // Si NO vienen CIEs en el formulario, no modificar los existentes
                unset($data['consultas_cie']);
            }

            // Crear nueva entidad con los datos
            $consulta = $this->Consultas->patchEntity($consulta, $data, [
                'associated' => ['ConsultasCie']
            ]);

            if ($this->Consultas->save($consulta)) {
                $this->Flash->success(__('La consulta ha sido actualizada con exito.'));
                return $this->redirect(['controller' => 'Pacientes', 'action' => 'view', $consulta->historias_clinica->paciente_id, '#' => 'consultas']);
            }

            $this->Flash->error(__('No se pudo actualizar la consulta. Intente de nuevo.'));
        }

        // Obtener listas de datos relacionados
        $pacientes = $this->Consultas->HistoriasClinicas->Pacientes->find(
            'list',
            keyField: 'id',
            valueField: function ($row) {
                return $row->nombre . ' ' . $row->apellido;
            }
        )->toArray();
        $doctores = $this->Consultas->Doctores->find('list', keyField: 'id', valueField: 'nombre')->toArray();

        $this->set(compact('consulta', 'pacientes', 'doctores', 'usuario'));
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        } else {
            $this->viewBuilder()->setLayout('default');
        }
    }


    /**
     * Delete method
     *
     * @param string|null $id Consulta id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $consulta = $this->Consultas->get($id);
        if ($this->Consultas->delete($consulta)) {
            $this->Flash->success(__('The consulta has been deleted.'));
        } else {
            $this->Flash->error(__('The consulta could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function buscarCie()
    {
        $this->request->allowMethod(['get']);

        $query = $this->request->getQuery('q');
        $resultados = [];

        if (!empty($query)) {
            $cieDiagnosticos = $this->Consultas->Diagnosticoscie10
                ->find()
                ->select(['id', 'descripcion', 'clave'])
                ->where([
                    'OR' => [
                        'LOWER(Diagnosticoscie10.descripcion) LIKE' => '%' . $query . '%',
                        'LOWER(Diagnosticoscie10.clave) LIKE' => '%' . $query . '%'
                    ]
                ])
                ->limit(10)
                ->toArray();

            $cieCategorias = $this->Consultas->Categoriascie10
                ->find()
                ->select(['id', 'descripcion', 'clave'])
                ->where([
                    'OR' => [
                        'LOWER(Categoriascie10.descripcion) LIKE' => '%' . $query . '%',
                        'LOWER(Categoriascie10.clave) LIKE' => '%' . $query . '%'
                    ]
                ])
                ->limit(10)
                ->toArray();

            foreach ($cieCategorias as $cat) {
                $resultados[] = [
                    'id' => $cat->id,
                    'clave' => $cat->clave,
                    'descripcion' => $cat->descripcion,
                    'tipo' => 'categoria'
                ];
            }

            foreach ($cieDiagnosticos as $cie) {
                $resultados[] = [
                    'id' => $cie->id,
                    'clave' => $cie->clave,
                    'descripcion' => $cie->descripcion,
                    'tipo' => 'cie'
                ];
            }
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode($resultados));
    }

    /**
     * Actualizar indicaciones consolidadas de una receta
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
                // Obtener nombre del medicamento (desde BD o entrada manual)
                $nombreMedicamento = $med->medicamento ? $med->medicamento->nombre : $med->nombre_medicamento;
                $linea = strtoupper($nombreMedicamento ?? 'Sin nombre');

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

