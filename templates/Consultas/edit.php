<div class="container mt-4">
    <h2 class="mb-3 text-info">Editar Consulta</h2>

    <?= $this->Form->create($consulta, ['class' => 'needs-validation']) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $this->Form->control('paciente_id', [
                'label' => 'Paciente',
                'options' => $pacientes,
                'empty' => 'Seleccione un paciente',
                'class' => 'form-control',
                'default' => $consulta->historias_clinica->paciente_id,
                'disabled' => true
            ]) ?>
        </div>
        <div class="col-md-6">
            <!-- Campo de usuario oculto -->
            <?= $this->Form->hidden('user_id', ['value' => $usuario->id]) ?>
            
            <!-- Campo para seleccionar el doctor -->
<?php
if ($usuario->rol == 3 && !empty($consulta->doctor_id)) {
    echo $this->Form->control('doctor_id', [
        'type' => 'hidden',
        'value' => $consulta->doctor_id
    ]);
} else {
    echo $this->Form->control('doctor_id', [
        'label' => 'Doctor',
        'options' => $doctores,
        'class' => 'form-control',
        'empty' => 'Seleccione un doctor'
    ]);
}
?>
        </div>
    </div>

    <!-- SECCIÓN DE RECETAS -->
    <hr class="my-4">
    <h5 class="mb-4 text-info"><i class="fas fa-prescription-bottle"></i> Recetas</h5>

    <!-- Almacenar datos de receta existente en elemento oculto para cargar automáticamente -->
    <?php if (!empty($consulta->recetas) && count($consulta->recetas) > 0): ?>
        <?php 
            $primeraReceta = $consulta->recetas[0];
            $datosReceta = [
                'id' => $primeraReceta->id,
                'nombre' => $primeraReceta->nombre,
                'descripcion' => $primeraReceta->descripcion,
                'notas' => $primeraReceta->notas,
                'tipo_usuario' => $primeraReceta->tipo_usuario,
                'tipo_atencion' => $primeraReceta->tipo_atencion,
                'especialidad_medica' => $primeraReceta->especialidad_medica,
                'h_cl' => $primeraReceta->h_cl,
                'sis' => $primeraReceta->sis,
                'particular' => $primeraReceta->particular,
                'peso' => $primeraReceta->peso,
                'valido_hasta' => $primeraReceta->valido_hasta ? $primeraReceta->valido_hasta->format('Y-m-d') : null,
                'recetas_medicamentos' => array_map(function($med) {
                    return [
                        'id' => $med->medicamento ? $med->medicamento->id : null,
                        'codigo' => $med->medicamento ? $med->medicamento->codigo : ($med->codigo_medicamento ?? ''),
                        'nombre' => $med->medicamento ? $med->medicamento->nombre : ($med->nombre_medicamento ?? ''),
                        'concentracion' => $med->medicamento ? $med->medicamento->concentracion : ($med->concentracion ?? ''),
                        'cantidad' => $med->cantidad,
                        'forma_id' => $med->forma_farmaceutica_id,
                        'forma_nombre' => $med->forma_farmaceutica->nombre ?? '',
                        'via_id' => $med->via_administracion_id,
                        'via_nombre' => $med->via_administracion->nombre ?? '',
                        'duracion_dias' => $med->duracion_dias,
                        'dosis' => $med->dosis ?? '',
                        'observaciones' => $med->observaciones
                    ];
                }, $primeraReceta->recetas_medicamentos ?? [])
            ];
            $datosBase64 = base64_encode(json_encode($datosReceta));
        ?>
        <div id="receta-data-container" style="display: none;" data-receta-id="<?= $primeraReceta->id ?>" data-receta-data="<?= $datosBase64 ?>"></div>
    <?php endif; ?>

    <h5 class="mb-4 text-info"><i class="fas fa-edit"></i> Editar Receta</h5>
    
    <div class="card border-info mb-4">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="fas fa-pills"></i> Datos de la Receta</h6>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="receta-nombre" class="form-label fw-bold">Nombre de Receta</label>
                    <input type="text" id="receta-nombre" class="form-control" placeholder="Ej: Antibióticos">
                </div>
                <div class="col-md-8">
                    <label for="receta-descripcion" class="form-label fw-bold">Descripción</label>
                    <input type="text" id="receta-descripcion" class="form-control" placeholder="Ej: Tratamiento para infección bacteriana">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="receta-notas" class="form-label fw-bold">Notas Especiales</label>
                    <textarea id="receta-notas" class="form-control" rows="2" placeholder="Ej: Tomar todos los medicamentos juntos al desayuno..."></textarea>
                </div>
            </div>

            <hr class="my-4">

            <!-- NUEVOS CAMPOS DE RECETA (COMENTADO - DE MOMENTO NO NECESARIO) -->
            <!-- <h6 class="mb-3 fw-bold"><i class="fas fa-file-medical"></i> Información Adicional de la Receta</h6>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="receta-tipo-usuario" class="form-label fw-bold">Tipo de Usuario</label>
                    <select id="receta-tipo-usuario" class="form-control">
                        <option value="">Seleccionar...</option>
                        <option value="DEMANDA">DEMANDA</option>
                        <option value="SIS">SIS</option>
                        <option value="INTERVENCIÓN SANITARIA">INTERVENCIÓN SANITARIA</option>
                        <option value="SOAT">SOAT</option>
                        <option value="OTROS">OTROS</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="receta-tipo-atencion" class="form-label fw-bold">Tipo de Atención</label>
                    <select id="receta-tipo-atencion" class="form-control">
                        <option value="">Seleccionar...</option>
                        <option value="CONSULTA EXTERNA">CONSULTA EXTERNA</option>
                        <option value="EMERGENCIA">EMERGENCIA</option>
                        <option value="HOSPITALIZACIÓN">HOSPITALIZACIÓN</option>
                        <option value="PARTICULAR">PARTICULAR</option>
                        <option value="CAMA">CAMA</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="receta-especialidad-medica" class="form-label fw-bold">Especialidad Médica</label>
                    <select id="receta-especialidad-medica" class="form-control">
                        <option value="">Seleccionar...</option>
                        <option value="CIRUGÍA">CIRUGÍA</option>
                        <option value="GINECO-OBSTETRICIA">GINECO-OBSTETRICIA</option>
                        <option value="MEDICINA">MEDICINA</option>
                        <option value="ONCOLOGÍA">ONCOLOGÍA</option>
                        <option value="PEDIATRÍA">PEDIATRÍA</option>
                        <option value="NEONATO">NEONATO</option>
                        <option value="DENTAL">DENTAL</option>
                    </select>
                </div>
            </div> -->

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="receta-h-cl" class="form-label fw-bold">H. Cl.</label>
                    <input type="text" id="receta-h-cl" class="form-control" placeholder="Referencia H. Cl.">
                </div>
                <div class="col-md-3 d-none">
                    <label for="receta-sis" class="form-label fw-bold">SIS</label>
                    <input type="text" id="receta-sis" class="form-control" placeholder="Número SIS">
                </div>
                <div class="col-md-3 d-none">
                    <label for="receta-particular" class="form-label fw-bold">Particular</label>
                    <input type="text" id="receta-particular" class="form-control" placeholder="Referencia Particular">
                </div>
                <div class="col-md-3">
                    <label for="receta-peso" class="form-label fw-bold">Peso (kg)</label>
                    <input type="number" id="receta-peso" class="form-control" step="0.01" placeholder="Ej: 65.50">
                </div>
                <div class="col-md-6">
                    <label for="receta-valido-hasta" class="form-label fw-bold">Válido Hasta</label>
                    <input type="date" id="receta-valido-hasta" class="form-control">
                </div>
            </div>

            
            <hr class="my-4">

            <!-- Entrada manual de medicamentos -->
            <h6 class="mb-3 fw-bold"><i class="fas fa-pills"></i> Agregar Medicamentos</h6>
            
            <!-- Búsqueda de medicamentos en BD -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="position-relative">
                        <label for="med-buscar" class="form-label fw-bold">
                            <i class="fas fa-search"></i> Buscar Medicamento en BD (opcional)
                        </label>
                        <input 
                            type="text" 
                            id="med-buscar" 
                            class="form-control" 
                            placeholder="Busca por código o nombre (ej: IBU, Ibuprofeno)..." 
                            autocomplete="off"
                        >
                        <div id="med-suggestions" class="list-group position-absolute w-100" style="display: none; z-index: 1050; max-height: 300px; overflow-y: auto;"></div>
                    </div>
                    <small class="text-muted d-block mt-2">
                        💡 <strong>Tip:</strong> Al seleccionar un medicamento de la base de datos, se completarán automáticamente los campos. También puedes ingresar datos manualmente en los campos de abajo.
                    </small>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-3">
                    <label for="med-codigo" class="form-label fw-bold">Código</label>
                    <input type="text" id="med-codigo" class="form-control" placeholder="Ej: IBU" maxlength="50">
                </div>
                <div class="col-md-9">
                    <label for="med-nombre" class="form-label fw-bold">Nombre del Medicamento <span class="text-danger">*</span></label>
                    <input type="text" id="med-nombre" class="form-control" placeholder="Ej: Ibuprofeno" maxlength="255">
                </div>
            </div>

            <!-- Detalles del medicamento -->
            <div id="med-details" class="card bg-light border-info mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-clipboard-list"></i> Detalles del Medicamento</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label fw-bold">Concentración</label>
                            <input type="text" id="med-concentracion" class="form-control" placeholder="Ej: 500mg" maxlength="100">
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label fw-bold">Cantidad <span class="text-danger">*</span></label>
                            <input type="number" id="med-cantidad" class="form-control" min="1" placeholder="Número de unidades">
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label fw-bold">Forma Farmacéutica <span class="text-danger">*</span></label>
                            <select id="med-forma" class="form-control">
                                <option value="">Seleccionar...</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label fw-bold">Vía Administración <span class="text-danger">*</span></label>
                            <select id="med-via" class="form-control">
                                <option value="">Seleccionar...</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label fw-bold">Duración (días)</label>
                            <input type="number" id="med-duracion" class="form-control" min="1" placeholder="Ej: 7">
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label fw-bold">Dosis</label>
                            <input type="text" id="med-dosis" class="form-control" maxlength="50" placeholder="Ej: 500mg cada 8h">
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <label class="form-label fw-bold">Frecuencia</label>
                            <textarea id="med-indicaciones" class="form-control" rows="2" placeholder="Ej: Tomar con agua, después de las comidas, no mezclar con lácteos..."></textarea>
                        </div>
                    </div>

                    <div class="row mt-3 pt-3 border-top">
                        <div class="col-12 d-flex flex-wrap botones-medicamento justify-content-end align-items-center">
                            <button type="button" class="btn btn-outline-secondary btn-accion-medicamento" onclick="limpiarFormularioMedicamento();">
                                <i class="fas fa-times-circle"></i> 
                                <span>Limpiar</span>
                            </button>
                            <button type="button" class="btn btn-success btn-accion-medicamento btn-primary-accion" onclick="agregarMedicaReceta()">
                                <i class="fas fa-check-circle"></i> 
                                <span>Agregar a Receta</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de medicamentos agregados/existentes -->
            <div id="medicamentos-tabla-container" style="display: none;" class="mb-4">
                <h6 class="mb-3 fw-bold"><i class="fas fa-pills"></i> Medicamentos de la Receta</h6>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width: 200px;">Medicamento</th>
                                <th style="min-width: 80px;">Conc.</th>
                                <th style="min-width: 70px;">Cant.</th>
                                <th style="min-width: 100px;">Forma</th>
                                <th style="min-width: 100px;">Vía</th>
                                <th style="min-width: 60px;">Días</th>
                                <th style="min-width: 100px;">Dosis</th>
                                <th style="min-width: 200px;">Indicaciones</th>
                                <th style="min-width: 60px;" class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="medicamentos-tbody">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Campos ocultos para envío -->
            <input type="hidden" name="receta_id_editando" id="receta-id-editando" value="">
            <input type="hidden" name="medicamentos_receta" id="medicamentos-json" value="[]">
            <input type="hidden" name="receta_datos" id="receta-datos-json" value="{}">
        </div>
    </div>

    <!-- SECCIÓN DE DIAGNÓSTICO Y CIE-10 -->
    <hr class="my-4">
    <h5 class="mb-4 text-info"><i class="fas fa-stethoscope"></i> Diagnóstico</h5>

    <div class="row">
        <div class="col-md-12">
            <?= $this->Form->control('diagnostico', ['label' => 'Diagnóstico', 'class' => 'form-control mb-2', 'rows' => 3]) ?>
        </div>

        <div class="col-md-12">
            <div class="mb-3 position-relative">
                <label for="cie-search">CIE-10</label>
                <input type="text" id="cie-search" class="form-control" placeholder="Escribe para buscar..." autocomplete="off">
                <div id="cie-suggestions" class="list-group position-absolute w-100" style="display: none; z-index: 1000;"></div>
            </div>

    <div id="selected-cies">
        <?php if (!empty($consulta->consultas_cie)): ?>
            <?php foreach ($consulta->consultas_cie as $cie): ?>
                <?php
                    $descripcion = '';
                    $clave = '';
                    $id = null;

                    // 1. Si es un diagnóstico CIE
                    if (!empty($cie->diagnosticoscie10)) {
                        $descripcion = $cie->diagnosticoscie10->descripcion;
                        $clave = $cie->diagnosticoscie10->clave;
                        $id = $cie->cie_id;
                    }

                    // 2. Si es una categoría
                    if (empty($descripcion) && !empty($cie->categoriascie10)) {
                        $descripcion = $cie->categoriascie10->descripcion;
                        $clave = $cie->categoriascie10->clave;
                        $id = $cie->categoria_id;
                    }

                    if ($id !== null):
                        $json = json_encode(['id' => $id, 'clave' => $clave]);
                ?>
                    <span class="badge bg-info text-white me-2 p-2 d-inline-block mb-2" data-cie-json="<?= htmlspecialchars($json) ?>">
                        <?= h($clave . ' - ' . $descripcion) ?>
                        <button type="button" class="btn-close btn-close-white ms-1" onclick="eliminarCie(this)" style="font-size: 0.75rem;"></button>
                    </span>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
        </div>
    </div>

    <div class="col-12 text-center mt-3 mb-2">
        <?= $this->Form->button(__('Actualizar Consulta'), ['class' => 'btn btn-primary', 'onclick' => 'guardarRecetaEditada()']) ?>
        <?= $this->Html->link(__('Cancelar'), ['controller' => 'Pacientes', 'action' => 'view', $consulta->historias_clinica->paciente_id], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>

<script>
    // Variables globales para el formulario de edición
    let formasFarmaceuticas = [];
    let viasAdministracion = [];
    let medicamentosAgregados = [];
    let medicamentoEditandoIdx = null;
    let medicamentosBaseDatos = [];

    function cargarFormasYVias() {
        return Promise.all([
            // Formas Farmacéuticas
            fetch('<?= $this->Url->build(['controller' => 'FormasFarmaceuticas', 'action' => 'getAll']) ?>')
                .then(r => {
                    if (!r.ok) throw new Error('Error cargando formas: ' + r.status);
                    return r.json();
                })
                .then(data => {
                    formasFarmaceuticas = Array.isArray(data) ? data : [];
                    const select = document.getElementById('med-forma');
                    if (select) {
                        select.innerHTML = '<option value="">Seleccionar...</option>';
                        formasFarmaceuticas.forEach(f => {
                            select.innerHTML += `<option value="${f.id}">${f.nombre}</option>`;
                        });
                    }
                    console.log('Formas cargadas:', formasFarmaceuticas);
                })
                .catch(e => console.error('Error en formas:', e)),
            
            // Vías de administración
            fetch('<?= $this->Url->build(['controller' => 'ViasAdministracion', 'action' => 'getAll']) ?>')
                .then(r => {
                    if (!r.ok) throw new Error('Error cargando vías: ' + r.status);
                    return r.json();
                })
                .then(data => {
                    viasAdministracion = Array.isArray(data) ? data : [];
                    const select = document.getElementById('med-via');
                    if (select) {
                        select.innerHTML = '<option value="">Seleccionar...</option>';
                        viasAdministracion.forEach(v => {
                            select.innerHTML += `<option value="${v.id}">${v.nombre}</option>`;
                        });
                    }
                    console.log('Vías cargadas:', viasAdministracion);
                })
                .catch(e => console.error('Error en vías:', e))
        ]);
    }

    function editarRecetaExistente(recetaId, datosBase64) {
        try {
            // Decodificar los datos de la receta
            const receta = JSON.parse(atob(datosBase64));
            
            console.log('Editando receta existente:', receta);
            
            // Guardar el ID de la receta que estamos editando
            document.getElementById('receta-id-editando').value = recetaId;
            
            // Pre-llenar el formulario con los datos de la receta existente
            document.getElementById('receta-nombre').value = receta.nombre || '';
            document.getElementById('receta-descripcion').value = receta.descripcion || '';
            document.getElementById('receta-notas').value = receta.notas || '';
            
            // Precarga de campos adicionales
            // COMENTADO - Campos no necesarios de momento
            // document.getElementById('receta-tipo-usuario').value = receta.tipo_usuario || '';
            // document.getElementById('receta-tipo-atencion').value = receta.tipo_atencion || '';
            // document.getElementById('receta-especialidad-medica').value = receta.especialidad_medica || '';
            document.getElementById('receta-h-cl').value = receta.h_cl || '';
            document.getElementById('receta-sis').value = receta.sis || '';
            document.getElementById('receta-particular').value = receta.particular || '';
            document.getElementById('receta-peso').value = receta.peso || '';
            document.getElementById('receta-valido-hasta').value = receta.valido_hasta || '';
            
            // Mostrar medicamentos en tabla de display (solo lectura)
            mostrarMedicamentosReceta(receta.recetas_medicamentos || []);
            
            // Scroll al formulario
            document.querySelector('.card.border-info').scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Dar foco al primer campo
            document.getElementById('receta-nombre').focus();
        } catch(error) {
            console.error('Error al decodificar datos de receta:', error);
            alert('Hubo un error al cargar la receta');
        }
    }

    function mostrarMedicamentosReceta(medicamentos) {
        const container = document.getElementById('medicamentos-tabla-container');
        const tbody = document.getElementById('medicamentos-tbody');

        if (!medicamentos || medicamentos.length === 0) {
            medicamentosAgregados = [];
            container.style.display = 'none';
            return;
        }

        // Cargar los medicamentos en el array para poder editarlos
        medicamentosAgregados = medicamentos.map(med => ({
            id: med.id || null,
            nombre: med.nombre || med.nombre_medicamento || '',
            nombre_medicamento: med.nombre_medicamento || med.nombre || '',
            codigo: med.codigo || med.codigo_medicamento || '',
            codigo_medicamento: med.codigo_medicamento || med.codigo || '',
            concentracion: med.concentracion || '',
            cantidad: med.cantidad,
            forma_id: med.forma_id,
            forma_nombre: med.forma_nombre || '',
            via_id: med.via_id,
            via_nombre: med.via_nombre || '',
            duracion_dias: med.duracion_dias || '',
            dosis: med.dosis || '',
            observaciones: med.observaciones || ''
        }));
        
        actualizarTablaReceta();
    }

    // Entrada manual - no se requiere inicialización de buscador
    // Los campos se rellenan directamente por el usuario

    function agregarMedicaReceta() {
        const nombre = document.getElementById('med-nombre').value.trim();
        const codigo = document.getElementById('med-codigo').value.trim();
        const concentracion = document.getElementById('med-concentracion').value.trim();
        const cantidad = document.getElementById('med-cantidad').value;
        const formaId = document.getElementById('med-forma').value;
        const viaId = document.getElementById('med-via').value;
        const duracion = document.getElementById('med-duracion').value;
        const dosis = document.getElementById('med-dosis').value;
        const indicaciones = document.getElementById('med-indicaciones').value;

        if (!nombre) {
            alert('Ingresa el nombre del medicamento');
            return;
        }

        if (!cantidad || !formaId || !viaId) {
            alert('Completa los campos obligatorios (Cantidad, Forma, Vía)');
            return;
        }

        const formaNombre = formasFarmaceuticas.find(f => f.id == formaId)?.nombre || '';
        const viaNombre = viasAdministracion.find(v => v.id == viaId)?.nombre || '';

        const medicamento = {
            nombre: nombre,
            codigo: codigo || '',
            nombre_medicamento: nombre,
            codigo_medicamento: codigo || '',
            concentracion: concentracion || '',
            cantidad: cantidad,
            forma_id: formaId,
            forma_nombre: formaNombre,
            via_id: viaId,
            via_nombre: viaNombre,
            duracion_dias: duracion,
            dosis: dosis,
            observaciones: indicaciones
        };

        // Si estamos editando, actualizar; si no, agregar
        if (medicamentoEditandoIdx !== null) {
            medicamentosAgregados[medicamentoEditandoIdx] = medicamento;
            medicamentoEditandoIdx = null;
        } else {
            medicamentosAgregados.push(medicamento);
        }

        actualizarTablaReceta();
        limpiarFormularioMedicamento();
    }

    function actualizarTablaReceta() {
        const container = document.getElementById('medicamentos-tabla-container');
        const tbody = document.getElementById('medicamentos-tbody');

        if (medicamentosAgregados.length === 0) {
            container.style.display = 'none';
            return;
        }

        tbody.innerHTML = '';
        medicamentosAgregados.forEach((med, idx) => {
            const tr = document.createElement('tr');
            const indicacionesText = med.observaciones ? med.observaciones.substring(0, 50) + (med.observaciones.length > 50 ? '...' : '') : '—';
            
            // Si no hay nombre de forma, buscar en la lista o usar el ID
            let formaNombre = med.forma_nombre || '';
            if (!formaNombre && med.forma_id) {
                const formaEncontrada = formasFarmaceuticas.find(f => f.id == med.forma_id);
                formaNombre = formaEncontrada ? formaEncontrada.nombre : `(ID: ${med.forma_id})`;
            }
            
            // Si no hay nombre de vía, buscar en la lista o usar el ID
            let viaNombre = med.via_nombre || '';
            if (!viaNombre && med.via_id) {
                const viaEncontrada = viasAdministracion.find(v => v.id == med.via_id);
                viaNombre = viaEncontrada ? viaEncontrada.nombre : `(ID: ${med.via_id})`;
            }
            
            tr.innerHTML = `
                <td>
                    <strong class="text-primary">${med.codigo}</strong><br>
                    <span class="text-muted">${med.nombre}</span>
                </td>
                <td class="text-center"><small>${med.concentracion || '—'}</small></td>
                <td class="text-center"><strong>${med.cantidad}</strong></td>
                <td><small>${formaNombre || '—'}</small></td>
                <td><small>${viaNombre || '—'}</small></td>
                <td class="text-center"><small>${med.duracion_dias || '—'}</small></td>
                <td class="text-center"><small>${med.dosis || '—'}</small></td>
                <td><small class="text-muted" title="${med.observaciones || ''}">${indicacionesText}</small></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarMedicamento(${idx})" title="Editar medicamento">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarMedicamento(${idx})" title="Eliminar medicamento">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
        container.style.display = 'block';
    }

    function editarMedicamento(idx) {
        const med = medicamentosAgregados[idx];
        if (!med) return;
        
        // Cargar los datos en el formulario de entrada manual
        document.getElementById('med-codigo').value = med.codigo_medicamento || med.codigo || '';
        document.getElementById('med-nombre').value = med.nombre_medicamento || med.nombre || '';
        document.getElementById('med-concentracion').value = med.concentracion || '';
        document.getElementById('med-cantidad').value = med.cantidad || '';
        document.getElementById('med-forma').value = med.forma_id || '';
        document.getElementById('med-via').value = med.via_id || '';
        document.getElementById('med-duracion').value = med.duracion_dias || '';
        document.getElementById('med-dosis').value = med.dosis || '';
        document.getElementById('med-indicaciones').value = med.observaciones || '';
        
        // Marcar que estamos editando este medicamento
        medicamentoEditandoIdx = idx;
        
        // Cambiar el texto del botón
        const btn = document.querySelector('#med-details .btn-primary-accion');
        if (btn) {
            btn.innerHTML = '<i class="fas fa-edit"></i> <span>Actualizar Medicamento</span>';
        }
        
        // Mostrar el formulario y enfoque
        document.getElementById('med-nombre').focus();
    }

    function eliminarMedicamento(idx) {
        medicamentosAgregados.splice(idx, 1);
        actualizarTablaReceta();
    }

    function limpiarFormularioMedicamento() {
        document.getElementById('med-codigo').value = '';
        document.getElementById('med-nombre').value = '';
        document.getElementById('med-concentracion').value = '';
        document.getElementById('med-cantidad').value = '';
        document.getElementById('med-forma').value = '';
        document.getElementById('med-via').value = '';
        document.getElementById('med-duracion').value = '';
        document.getElementById('med-dosis').value = '';
        document.getElementById('med-indicaciones').value = '';
        medicamentoEditandoIdx = null;
        document.getElementById('med-nombre').focus();
        
        // Restaurar el texto del botón
        const btn = document.querySelector('#med-details .btn-primary-accion');
        if (btn) {
            btn.innerHTML = '<i class="fas fa-check-circle"></i> <span>Agregar a Receta</span>';
        }
    }

    // Función para buscar medicamentos en la BD
    function inicializarBuscadorMedicamentos() {
        console.log('Iniciando inicializarBuscadorMedicamentos()');
        
        const buscarInput = document.getElementById('med-buscar');
        const sugerenciasDiv = document.getElementById('med-suggestions');
        
        if (!buscarInput || !sugerenciasDiv) {
            console.warn('Elementos de búsqueda de medicamentos no encontrados');
            return;
        }
        
        buscarInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            if (query.length < 2) {
                sugerenciasDiv.style.display = 'none';
                return;
            }
            
            // Buscar medicamentos por código o nombre
            fetch('<?= $this->Url->build(['controller' => 'Medicamentos', 'action' => 'buscar']) ?>?q=' + encodeURIComponent(query))
                .then(r => {
                    if (!r.ok) throw new Error('Error en búsqueda: ' + r.status);
                    return r.json();
                })
                .then(data => {
                    console.log('Medicamentos encontrados:', data);
                    medicamentosBaseDatos = Array.isArray(data) ? data : [];
                    
                    sugerenciasDiv.innerHTML = '';
                    
                    if (medicamentosBaseDatos.length === 0) {
                        sugerenciasDiv.style.display = 'none';
                        return;
                    }
                    
                    medicamentosBaseDatos.forEach(med => {
                        const div = document.createElement('div');
                        div.className = 'list-group-item list-group-item-action med-suggestion-item';
                        div.innerHTML = `
                            <strong>${med.codigo || '(sin código)'}</strong> - ${med.nombre}<br>
                            <small class="text-muted">Concentración: ${med.concentracion || 'N/A'}</small>
                        `;
                        div.style.cursor = 'pointer';
                        
                        div.addEventListener('click', function() {
                            seleccionarMedicamento(med);
                        });
                        
                        sugerenciasDiv.appendChild(div);
                    });
                    
                    sugerenciasDiv.style.display = 'block';
                })
                .catch(e => console.error('Error en búsqueda de medicamentos:', e));
        });
        
        // Cerrar sugerencias al hacer click fuera
        document.addEventListener('click', function(e) {
            if (e.target.id !== 'med-buscar') {
                sugerenciasDiv.style.display = 'none';
            }
        });
    }

    // Función para seleccionar un medicamento y autocompletar
    function seleccionarMedicamento(medicamento) {
        console.log('Medicamento seleccionado:', medicamento);
        
        // Llenar campos automáticamente
        document.getElementById('med-codigo').value = medicamento.codigo || '';
        document.getElementById('med-nombre').value = medicamento.nombre || '';
        document.getElementById('med-concentracion').value = medicamento.concentracion || '';
        
        // Limpiar búsqueda
        document.getElementById('med-buscar').value = '';
        document.getElementById('med-suggestions').style.display = 'none';
        
        // Enfocar en cantidad para que continúe ingresando datos
        document.getElementById('med-cantidad').focus();
    }

    function eliminarCie(button) {
        event.preventDefault();
        button.closest('.badge').remove();
    }

    function sincronizarCies() {
        // Obtener todos los badges visibles en selected-cies
        const selectedCiesContainer = document.getElementById('selected-cies');
        const badges = selectedCiesContainer.querySelectorAll('.badge[data-cie-json]');
        
        // Eliminar todos los inputs ocultos existentes
        const inputsAntiguos = selectedCiesContainer.querySelectorAll('input[name="cie_data[]"]');
        inputsAntiguos.forEach(input => input.remove());
        
        // Crear nuevos inputs basándose en los badges visibles
        badges.forEach(badge => {
            const jsonData = badge.getAttribute('data-cie-json');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'cie_data[]';
            input.value = jsonData;
            selectedCiesContainer.appendChild(input);
        });
    }

    function guardarRecetaEditada() {
        // Sincronizar CIEs antes de guardar
        sincronizarCies();
        
        const recetaId = document.getElementById('receta-id-editando').value;
        
        // Si no hay receta siendo editada, solo enviar el formulario
        if (!recetaId) {
            document.querySelector('form').submit();
            return;
        }
        
        // Recopilar datos de la receta editada
        const nombre = document.getElementById('receta-nombre').value.trim();
        const descripcion = document.getElementById('receta-descripcion').value.trim();
        const notas = document.getElementById('receta-notas').value.trim();
        // COMENTADO - Campos no necesarios de momento
        // const tipoUsuario = document.getElementById('receta-tipo-usuario').value;
        // const tipoAtencion = document.getElementById('receta-tipo-atencion').value;
        // const especialidadMedica = document.getElementById('receta-especialidad-medica').value;
        const hCl = document.getElementById('receta-h-cl').value;
        const sis = document.getElementById('receta-sis').value;
        const particular = document.getElementById('receta-particular').value;
        const peso = document.getElementById('receta-peso').value;
        const validoHasta = document.getElementById('receta-valido-hasta').value;
        
        console.log('Guardando receta editada:', {
            recetaId: recetaId,
            nombre: nombre,
            descripcion: descripcion,
            notas: notas
        });
        
        // Crear los datos de la receta a enviar (sin medicamentos porque estamos en edición)
        const recetaDatos = {
            id: recetaId,
            nombre: nombre,
            descripcion: descripcion,
            notas: notas,
            // COMENTADO - Campos no necesarios de momento
            // tipo_usuario: tipoUsuario,
            // tipo_atencion: tipoAtencion,
            // especialidad_medica: especialidadMedica,
            h_cl: hCl,
            sis: sis,
            particular: particular,
            peso: peso,
            valido_hasta: validoHasta
        };
        
        // Guardar ID y datos en campos ocultos
        document.getElementById('receta-id-editando').value = recetaId;
        document.getElementById('receta-datos-json').value = JSON.stringify(recetaDatos);
        document.getElementById('medicamentos-json').value = JSON.stringify(medicamentosAgregados);
        
        // Enviar el formulario
        document.querySelector('form').submit();
    }

    function initializeCieSearch(context) {
        const key = 'cieLogicInitialized_' + Math.random();
        if (context._cieLogicKey) return;
        context._cieLogicKey = key;

        const searchInput = context.querySelector("#cie-search");
        const suggestionsList = context.querySelector("#cie-suggestions");
        const selectedCies = context.querySelector("#selected-cies");

        if (!searchInput || !suggestionsList || !selectedCies) {
            delete context._cieLogicKey;
            return;
        }

        searchInput.addEventListener("input", function () {
            const query = searchInput.value.trim().toLowerCase();

            if (query.length < 2) {
                suggestionsList.style.display = "none";
                return;
            }

            fetch("<?= $this->Url->build(['controller' => 'Consultas', 'action' => 'buscarCie']) ?>?q=" + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    suggestionsList.innerHTML = "";

                    if (data.length === 0) {
                        suggestionsList.style.display = "none";
                        return;
                    }

                    const limitedData = data.slice(0, 10);

                    limitedData.forEach(item => {
                        const div = document.createElement("div");
                        div.classList.add("list-group-item", "list-group-item-action", "cie-item");
                        div.textContent = `${item.clave} - ${item.descripcion}`;
                        div.dataset.id = item.id;

                        div.addEventListener("click", function () {
                            addSelectedCie(item);
                            suggestionsList.style.display = "none";
                        });

                        suggestionsList.appendChild(div);
                    });

                    suggestionsList.style.display = "block";
                })
                .catch(error => console.error("Error en la búsqueda:", error));
        });

        function addSelectedCie(item) {
            const existingSpans = selectedCies.querySelectorAll('.badge[data-cie-json]');
            for (let span of existingSpans) {
                try {
                    const jsonValue = JSON.parse(span.getAttribute('data-cie-json'));
                    if (jsonValue.id === item.id) {
                        alert(`El CIE ${item.clave} ya fue agregado.`);
                        return;
                    }
                } catch (e) {
                    console.error('Error parsing CIE data:', e);
                }
            }

            const jsonData = JSON.stringify({ id: item.id, clave: item.clave });
            
            const span = document.createElement("span");
            span.setAttribute("data-cie-json", jsonData);
            span.textContent = `${item.clave} - ${item.descripcion} `;
            span.classList.add("badge", "bg-info", "text-white", "me-2", "p-2", "d-inline-block", "mb-2");

            const removeBtn = document.createElement("button");
            removeBtn.type = "button";
            removeBtn.className = "btn-close btn-close-white ms-1";
            removeBtn.style.fontSize = "0.75rem";
            removeBtn.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                span.remove();
            });

            span.appendChild(removeBtn);
            selectedCies.appendChild(span);

            searchInput.value = "";
        }
    }

    // Al cargar la página
    document.addEventListener("DOMContentLoaded", function () {
        initializeCieSearch(document);
        iniciaProceso();
    });

    function iniciaProceso() {
        // Primero cargar formas y vías, luego cargar medicamentos
        cargarFormasYVias().then(() => {
            // Inicializar buscador de medicamentos
            inicializarBuscadorMedicamentos();
            
            // Cargar los medicamentos después de que las formas y vías estén disponibles
            const recetaContainer = document.getElementById('receta-data-container');
            if (recetaContainer) {
                const recetaId = recetaContainer.getAttribute('data-receta-id');
                const recetaDataBase64 = recetaContainer.getAttribute('data-receta-data');
                
                if (recetaId && recetaDataBase64) {
                    editarRecetaExistente(recetaId, recetaDataBase64);
                }
            }
        });
    }

    // Cuando se abre un modal
    $(document).on('shown.bs.modal', function (e) {
        const modalContent = e.target.querySelector('.modal-content');
        if (modalContent) {
            initializeCieSearch(modalContent);
        }
    });

    // Si usas AJAX
    $(document).on('ajaxComplete', function () {
        initializeCieSearch(document);
    });
</script>

<style>

    #cie-suggestions .cie-item {
        /* color: white; */
        color: #212529;
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    #cie-suggestions .cie-item:hover {
        /* background-color: #6c757d; */
        background-color: #e2e6ea;
    }

    #med-suggestions {
        color: #212529;
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    #med-suggestions .list-group-item {
        color: #212529;
        background-color: #f8f9fa;
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    #med-suggestions .list-group-item:hover {
        background-color: #e2e6ea;
    }

    /* Estilos para búsqueda de medicamentos */
    #med-suggestions .med-suggestion-item {
        color: #212529;
        background-color: #f8f9fa;
        padding: 10px 12px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid #dee2e6;
    }

    #med-suggestions .med-suggestion-item:hover {
        background-color: #e7f3ff;
        border-color: #0dcaf0;
        box-shadow: 0 2px 4px rgba(13, 202, 240, 0.2);
    }

    #med-suggestions .med-suggestion-item strong {
        color: #0dcaf0;
        font-size: 0.95rem;
    }

    #med-details {
        background-color: transparent;
        border: none;
        border-top: 1px solid #e9ecef;
        padding: 1.5rem 0 0.5rem 0;
        margin: 1.5rem 0 0 0;
    }

    #med-details .card-body {
        padding: 1rem 0;
    }

    .botones-medicamento {
        gap: 1.2rem;
    }

    .btn-accion-medicamento {
        padding: 9px 22px;
        font-size: 0.9rem;
        min-width: 160px;
        white-space: nowrap;
    }

    @media (max-width: 768px) {
        .botones-medicamento {
            gap: 1rem;
        }

        .btn-accion-medicamento {
            min-width: 140px;
        }
    }

    .card-header {
        border-radius: 0.25rem 0.25rem 0 0;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-compact tbody td {
        padding: 0.4rem 0.5rem;
        font-size: 0.85rem;
        vertical-align: middle;
    }

    .table-compact thead th {
        padding: 0.5rem;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .form-control-sm, .form-select-sm {
        height: calc(1.5em + 0.5rem + 2px) !important;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
    }
</style>