<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Consulta $consulta
 */
$this->setLayout('default');
?>

<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-prescription-bottle"></i> 
                    Nueva Receta con Medicamentos
                </h3>
            </div>

            <div class="card-body">
                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle"></i> Información de la Consulta:</strong><br>
                    <strong>Paciente:</strong> 
                    <?= !empty($consulta->historias_clinica) && !empty($consulta->historias_clinica->paciente)
                        ? h($consulta->historias_clinica->paciente->nombre . ' ' . $consulta->historias_clinica->paciente->apellido)
                        : 'Sin información'
                    ?><br>
                    <strong>Doctor:</strong> 
                    <?= !empty($consulta->doctore)
                        ? h($consulta->doctore->nombre . ' ' . $consulta->doctore->apellido)
                        : 'Sin doctor'
                    ?><br>
                    <strong>Motivo:</strong> 
                    <?= h($consulta->motivo) ?><br>
                    <strong>Fecha:</strong> 
                    <?= $consulta->created->format('d/m/Y H:i') ?>
                </div>

                <form id="receta-form" class="form-horizontal">
                    <!-- SECCIÓN 1: DATOS DE LA RECETA -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="mb-3"><i class="fas fa-file-medical"></i> Datos de la Receta</h5>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="nombre" class="col-sm-3 col-form-label">Nombre/Título</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                placeholder="Ej: Receta Ginecología - Enero 2026" required>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Nombre descriptivo para identificar la receta
                            </small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="descripcion" class="col-sm-3 col-form-label">Descripción</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="descripcion" name="descripcion" 
                                rows="3" placeholder="Detalles adicionales de la receta (opcional)"></textarea>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Notas sobre el diagnóstico o procedimiento
                            </small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="notas" class="col-sm-3 col-form-label">Notas Especiales</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="notas" name="notas" 
                                rows="2" placeholder="Ej: Tomar con alimentos, no mezclar medicamentos"></textarea>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Indicaciones generales para el paciente
                            </small>
                        </div>
                    </div>

                    <hr/>

                    <!-- SECCIÓN 2: AGREGAR MEDICAMENTOS -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="mb-3"><i class="fas fa-pills"></i> Medicamentos de la Receta</h5>
                        </div>
                    </div>

                    <!-- Búsqueda de Medicamentos -->
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Buscar Medicamento</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="text" class="form-control" id="medicamento-busqueda" 
                                    placeholder="Código o nombre (CEFALEXINA, 0000000065)" autocomplete="off">
                                <button type="button" class="btn btn-outline-secondary" id="btn-limpiar-busqueda">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div id="resultados-medicamentos" class="list-group mt-2" style="display:none; max-height: 300px; overflow-y: auto;"></div>
                        </div>
                    </div>

                    <!-- Medicamento seleccionado - Detalles -->
                    <div id="detalles-medicamento" style="display:none;" class="p-3 bg-light border rounded mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Concentración</label>
                                <input type="text" class="form-control" id="concentracion" placeholder="Ej: 500MG">
                            </div>
                            <div class="col-md-4">
                                <label>Cantidad</label>
                                <input type="number" class="form-control" id="cantidad" placeholder="Ej: 20" min="1" required>
                            </div>
                            <div class="col-md-4">
                                <label>Forma Farmacéutica</label>
                                <select class="form-control" id="forma-farmaceutica">
                                    <option value="">Seleccionar...</option>
                                    <option value="1">CÁPSULA</option>
                                    <option value="2">TABLETA</option>
                                    <option value="3">JARABE</option>
                                    <option value="4">INYECCIÓN</option>
                                    <option value="5">CREMA</option>
                                    <option value="6">GEL</option>
                                    <option value="7">GOTAS</option>
                                    <option value="8">ÓVULO</option>
                                    <option value="9">POLVO</option>
                                    <option value="10">SUSPENSIÓN</option>
                                    <option value="11">SOLUCIÓN</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label>Vía de Administración</label>
                                <select class="form-control" id="via-administracion">
                                    <option value="">Seleccionar...</option>
                                    <option value="1">ORAL</option>
                                    <option value="2">INTRAVENOSA (IV)</option>
                                    <option value="3">INTRAMUSCULAR (IM)</option>
                                    <option value="4">RECTAL</option>
                                    <option value="5">VAGINAL</option>
                                    <option value="6">TÓPICA</option>
                                    <option value="7">OFTÁLMICA</option>
                                    <option value="8">ÓTICA</option>
                                    <option value="9">NASAL</option>
                                    <option value="10">INHALADA</option>
                                    <option value="11">TRANSDÉRMICA</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Duración (días)</label>
                                <input type="number" class="form-control" id="duracion-dias" placeholder="Ej: 7" min="1">
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-success w-100" id="btn-agregar-medicamento">
                                    <i class="fas fa-plus"></i> Agregar a Receta
                                </button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label>Observaciones</label>
                                <textarea class="form-control" id="observaciones" rows="2" placeholder="Tomar con alimentos, etc."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de medicamentos agregados -->
                    <div id="tabla-medicamentos-container" style="display:none;">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Medicamento</th>
                                    <th>Conc.</th>
                                    <th>Cant.</th>
                                    <th>Forma</th>
                                    <th>Vía</th>
                                    <th>Días</th>
                                    <th>Obs.</th>
                                    <th style="width: 60px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-medicamentos">
                            </tbody>
                        </table>
                    </div>

                    <div id="sin-medicamentos" class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Busca y agrega medicamentos a la receta
                    </div>

                </form>
            </div>

            <div class="card-footer">
                <div class="row">
                    <div class="col-sm-9 offset-sm-3">
                        <button type="button" class="btn btn-primary" id="btn-guardar-receta">
                            <i class="fas fa-save"></i> Guardar Receta con Medicamentos
                        </button>
                        <?= $this->Html->link(
                            '<i class="fas fa-arrow-left"></i> Cancelar',
                            ['controller' => 'Consultas', 'action' => 'view', $consultaId],
                            ['class' => 'btn btn-secondary', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let medicamentosAgregados = [];
    let medicamentoSeleccionado = null;
    
    const inputBusqueda = document.getElementById('medicamento-busqueda');
    const resultados = document.getElementById('resultados-medicamentos');
    const detalles = document.getElementById('detalles-medicamento');
    const btnAgregar = document.getElementById('btn-agregar-medicamento');
    const btnGuardar = document.getElementById('btn-guardar-receta');
    const btnLimpiar = document.getElementById('btn-limpiar-busqueda');
    const tabla = document.getElementById('tabla-medicamentos');
    const tablaContainer = document.getElementById('tabla-medicamentos-container');
    const sinMedicamentos = document.getElementById('sin-medicamentos');

    // Búsqueda AJAX
    inputBusqueda.addEventListener('input', function() {
        const query = this.value.trim();
        
        if (query.length < 2) {
            resultados.style.display = 'none';
            return;
        }

        fetch('<?= $this->Url->build(['controller' => 'RecetasMedicamentos', 'action' => 'buscarMedicamentos']) ?>' + '?q=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                resultados.innerHTML = '';
                
                if (!data.medicamentos || data.medicamentos.length === 0) {
                    resultados.innerHTML = '<div class="list-group-item text-muted">No hay medicamentos encontrados</div>';
                } else {
                    data.medicamentos.forEach(med => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action';
                        item.innerHTML = `
                            <div><strong>${med.codigo}</strong> - ${med.nombre}</div>
                            <small class="text-muted">${med.concentracion || 'Sin concentración'}</small>
                        `;
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            seleccionarMedicamento(med);
                        });
                        resultados.appendChild(item);
                    });
                }
                
                resultados.style.display = 'block';
            })
            .catch(error => console.error('Error:', error));
    });

    function seleccionarMedicamento(med) {
        medicamentoSeleccionado = med;
        inputBusqueda.value = med.codigo + ' - ' + med.nombre;
        document.getElementById('concentracion').value = med.concentracion || '';
        resultados.style.display = 'none';
        detalles.style.display = 'block';
        document.getElementById('cantidad').focus();
    }

    // Agregar medicamento a la lista temporal
    btnAgregar.addEventListener('click', function() {
        if (!medicamentoSeleccionado) {
            alert('Selecciona un medicamento primero');
            return;
        }

        const cantidad = document.getElementById('cantidad').value;
        const forma = document.getElementById('forma-farmaceutica').value;
        const via = document.getElementById('via-administracion').value;

        if (!cantidad || !forma || !via) {
            alert('Completa todos los campos requeridos');
            return;
        }

        medicamentosAgregados.push({
            medicamento_id: medicamentoSeleccionado.id,
            nombre: medicamentoSeleccionado.nombre,
            codigo: medicamentoSeleccionado.codigo,
            concentracion: document.getElementById('concentracion').value,
            cantidad: cantidad,
            forma_farmaceutica_id: forma,
            via_administracion_id: via,
            duracion_dias: document.getElementById('duracion-dias').value,
            observaciones: document.getElementById('observaciones').value
        });

        actualizarTabla();
        limpiarFormularioMedicamento();
    });

    function actualizarTabla() {
        tabla.innerHTML = '';
        
        if (medicamentosAgregados.length > 0) {
            tablaContainer.style.display = 'block';
            sinMedicamentos.style.display = 'none';
            
            const formas = {'1': 'CÁPSULA', '2': 'TABLETA', '3': 'JARABE', '4': 'INYECCIÓN', '5': 'CREMA', '6': 'GEL', '7': 'GOTAS', '8': 'ÓVULO', '9': 'POLVO', '10': 'SUSPENSIÓN', '11': 'SOLUCIÓN'};
            const vias = {'1': 'ORAL', '2': 'IV', '3': 'IM', '4': 'RECTAL', '5': 'VAGINAL', '6': 'TÓPICA', '7': 'OFTÁLMICA', '8': 'ÓTICA', '9': 'NASAL', '10': 'INHALADA', '11': 'TRANSDÉRMICA'};
            
            medicamentosAgregados.forEach((med, index) => {
                const row = tabla.insertRow();
                row.innerHTML = `
                    <td><strong>${med.nombre}</strong> (${med.codigo})</td>
                    <td>${med.concentracion}</td>
                    <td>${med.cantidad}</td>
                    <td>${formas[med.forma_farmaceutica_id] || 'N/A'}</td>
                    <td>${vias[med.via_administracion_id] || 'N/A'}</td>
                    <td>${med.duracion_dias || '-'}</td>
                    <td><small>${med.observaciones || '-'}</small></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger" onclick="eliminarMedicamento(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
            });
        } else {
            tablaContainer.style.display = 'none';
            sinMedicamentos.style.display = 'block';
        }
    }

    window.eliminarMedicamento = function(index) {
        medicamentosAgregados.splice(index, 1);
        actualizarTabla();
    };

    function limpiarFormularioMedicamento() {
        inputBusqueda.value = '';
        document.getElementById('concentracion').value = '';
        document.getElementById('cantidad').value = '';
        document.getElementById('forma-farmaceutica').value = '';
        document.getElementById('via-administracion').value = '';
        document.getElementById('duracion-dias').value = '';
        document.getElementById('observaciones').value = '';
        medicamentoSeleccionado = null;
        detalles.style.display = 'none';
        resultados.style.display = 'none';
        inputBusqueda.focus();
    }

    btnLimpiar.addEventListener('click', function() {
        limpiarFormularioMedicamento();
    });

    // Guardar receta con todos los medicamentos
    btnGuardar.addEventListener('click', function() {
        const nombre = document.getElementById('nombre').value;
        
        if (!nombre) {
            alert('Completa el nombre de la receta');
            return;
        }

        if (medicamentosAgregados.length === 0) {
            alert('Agrega al menos un medicamento');
            return;
        }

        // Crear formulario oculto para enviar datos
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= $this->Url->build(['controller' => 'Recetas', 'action' => 'addFromConsultaWithMeds', $consultaId]) ?>';
        
        form.innerHTML = `
            <input type="hidden" name="nombre" value="${nombre}">
            <input type="hidden" name="descripcion" value="${document.getElementById('descripcion').value}">
            <input type="hidden" name="notas" value="${document.getElementById('notas').value}">
            <input type="hidden" name="medicamentos" value='${JSON.stringify(medicamentosAgregados)}'>
            <input type="hidden" name="_csrfToken" value="<?= $this->request->getAttribute('csrfToken') ?>">
        `;
        
        document.body.appendChild(form);
        form.submit();
    });

    // Cerrar resultados al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#medicamento-busqueda') && !e.target.closest('#resultados-medicamentos')) {
            resultados.style.display = 'none';
        }
    });
});
</script>

<style>
#resultados-medicamentos .list-group-item {
    cursor: pointer;
}

#resultados-medicamentos .list-group-item:hover {
    background-color: #f0f0f0;
}

#tabla-medicamentos-container {
    margin-top: 20px;
}

.table td {
    vertical-align: middle;
    font-size: 0.9rem;
}
</style>
