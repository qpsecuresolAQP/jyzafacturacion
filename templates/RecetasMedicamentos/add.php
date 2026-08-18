<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RecetaMedicamento $recetaMedicamento
 * @var \Cake\Collection\CollectionInterface $medicamentos
 * @var \Cake\Collection\CollectionInterface $formasFarmaceuticas
 * @var \Cake\Collection\CollectionInterface $viasAdministracion
 */
?>
<div class="container mt-4 mb-4">
    <h2 class="text-info mb-4">
        <i class="fas fa-prescription-bottle me-2"></i>
        <?= $recetaMedicamento->isNew() ? 'Agregar Medicamento a Receta' : 'Editar Medicamento en Receta' ?>
    </h2>

    <div class="card card-body bg-light">
        <?= $this->Form->create($recetaMedicamento, ['class' => 'row g-3']) ?>

        <!-- Receta (Solo lectura si es edición) -->
        <div class="col-md-6">
            <label class="form-label">Receta:</label>
            <?php if ($recetaMedicamento->isNew()): ?>
                <?= $this->Form->control('receta_id', [
                    'options' => $recetas,
                    'class' => 'form-control',
                    'label' => false,
                    'empty' => 'Seleccionar receta',
                    'required' => true,
                    'value' => $recetaIdPreseleccionada ?? null
                ]) ?>
            <?php else: ?>
                <input type="hidden" name="receta_id" value="<?= $recetaMedicamento->receta_id ?>">
                <p class="form-control-plaintext fw-bold"><?= $recetaMedicamento->receta->nombre ?? 'Receta #' . $recetaMedicamento->receta_id ?></p>
            <?php endif; ?>
        </div>

        <!-- Medicamento: Búsqueda dinámica -->
        <div class="col-md-6">
            <label class="form-label">Medicamento (Búsqueda por código o nombre):</label>
            <div class="input-group">
                <input 
                    type="text" 
                    id="medicamento-search" 
                    class="form-control" 
                    placeholder="Buscar por código (0000000065) o nombre (CEFALEXINA)"
                    autocomplete="off"
                    <?php if (!$recetaMedicamento->isNew()): ?>
                        value="<?= $recetaMedicamento->medicamento->codigo . ' - ' . $recetaMedicamento->medicamento->nombre ?>"
                    <?php endif; ?>
                >
                <button class="btn btn-outline-secondary" type="button" id="medicamento-clear">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="medicamento-results" class="list-group mt-2" style="display:none; max-height: 300px; overflow-y: auto;"></div>
            
            <?= $this->Form->control('medicamento_id', [
                'type' => 'hidden',
                'id' => 'medicamento_id_hidden'
            ]) ?>
            
            <div id="medicamento-selected" class="alert alert-info mt-2" style="display:none;">
                <strong id="medicamento-selected-name"></strong>
                <small id="medicamento-selected-code" class="d-block text-muted"></small>
            </div>
        </div>

        <!-- Concentración -->
        <div class="col-md-6">
            <label class="form-label">Concentración:</label>
            <?= $this->Form->control('concentracion', [
                'type' => 'text',
                'class' => 'form-control',
                'label' => false,
                'placeholder' => 'Ej: 500MG, 10MG'
            ]) ?>
        </div>

        <!-- Cantidad -->
        <div class="col-md-6">
            <label class="form-label">Cantidad:</label>
            <?= $this->Form->control('cantidad', [
                'type' => 'number',
                'class' => 'form-control',
                'label' => false,
                'placeholder' => 'Ej: 1, 20, 100',
                'required' => true,
                'min' => 1
            ]) ?>
        </div>

        <!-- Forma Farmacéutica -->
        <div class="col-md-6">
            <label class="form-label">Forma Farmacéutica:</label>
            <?= $this->Form->control('forma_farmaceutica_id', [
                'options' => $formasFarmaceuticas,
                'class' => 'form-control',
                'label' => false,
                'empty' => 'Seleccionar forma...'
            ]) ?>
        </div>

        <!-- Vía de Administración -->
        <div class="col-md-6">
            <label class="form-label">Vía de Administración:</label>
            <?= $this->Form->control('via_administracion_id', [
                'options' => $viasAdministracion,
                'class' => 'form-control',
                'label' => false,
                'empty' => 'Seleccionar vía...'
            ]) ?>
        </div>

        <!-- Duración en Días -->
        <div class="col-md-6">
            <label class="form-label">Duración (días):</label>
            <?= $this->Form->control('duracion_dias', [
                'type' => 'number',
                'class' => 'form-control',
                'label' => false,
                'placeholder' => 'Ej: 7, 10, 30',
                'min' => 1
            ]) ?>
        </div>

        <!-- Observaciones -->
        <div class="col-12">
            <label class="form-label">Observaciones / Notas Adicionales:</label>
            <?= $this->Form->control('observaciones', [
                'type' => 'textarea',
                'class' => 'form-control',
                'label' => false,
                'rows' => 3,
                'placeholder' => 'Ej: Tomar con alimentos, Evitar durante el embarazo, etc.'
            ]) ?>
        </div>

        <!-- Botones -->
        <div class="col-12 text-center mt-4">
            <?= $this->Form->button(
                '<i class="fas fa-save me-2"></i>' . ($recetaMedicamento->isNew() ? 'Agregar Medicamento' : 'Actualizar'),
                ['class' => 'btn btn-success', 'escape' => false]
            ) ?>
            
            <?php if (!$recetaMedicamento->isNew()): ?>
                <?= $this->Form->postLink(
                    '<i class="fas fa-trash me-2"></i>Eliminar',
                    ['action' => 'delete', $recetaMedicamento->id],
                    [
                        'class' => 'btn btn-danger',
                        'confirm' => '¿Estás seguro de que deseas eliminar este medicamento?',
                        'escape' => false
                    ]
                ) ?>
            <?php endif; ?>
            
            <?= $this->Html->link(
                '<i class="fas fa-times me-2"></i>Cancelar',
                ['controller' => 'Recetas', 'action' => 'view', $recetaMedicamento->receta_id ?? 'null'],
                ['class' => 'btn btn-secondary', 'escape' => false]
            ) ?>
        </div>

        <?= $this->Form->end() ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('medicamento-search');
    const resultsDiv = document.getElementById('medicamento-results');
    const selectedDiv = document.getElementById('medicamento-selected');
    const selectedName = document.getElementById('medicamento-selected-name');
    const selectedCode = document.getElementById('medicamento-selected-code');
    const hiddenInput = document.getElementById('medicamento_id_hidden');
    const clearBtn = document.getElementById('medicamento-clear');
    let searchTimeout;

    // Búsqueda AJAX
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Cancelar búsqueda anterior si existe
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        
        if (query.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }

        // Esperar un poco antes de buscar (debounce)
        searchTimeout = setTimeout(() => {
            const searchUrl = '<?= $this->Url->build(['action' => 'buscarMedicamentos']) ?>' + '?q=' + encodeURIComponent(query);
            
            console.log('Buscando: ' + query);
            console.log('URL: ' + searchUrl);
            
            fetch(searchUrl)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Error en la búsqueda: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Datos recibidos:', data);
                    resultsDiv.innerHTML = '';
                    
                    if (!data.medicamentos || data.medicamentos.length === 0) {
                        resultsDiv.innerHTML = '<div class="list-group-item text-muted">No hay medicamentos encontrados</div>';
                    } else {
                        data.medicamentos.forEach(med => {
                            const item = document.createElement('button');
                            item.type = 'button';
                            item.className = 'list-group-item list-group-item-action';
                            item.innerHTML = `
                                <div><strong>${med.codigo}</strong> - ${med.nombre}</div>
                                <small class="text-muted">${med.concentracion || 'Sin concentración especificada'}</small>
                            `;
                            item.addEventListener('click', function(e) {
                                e.preventDefault();
                                selectMedicamento(med);
                            });
                            resultsDiv.appendChild(item);
                        });
                    }
                    
                    resultsDiv.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error en búsqueda AJAX:', error);
                    resultsDiv.innerHTML = '<div class="list-group-item text-danger">Error al buscar medicamentos</div>';
                    resultsDiv.style.display = 'block';
                });
        }, 300); // Esperar 300ms después de escribir
    });

    function selectMedicamento(med) {
        console.log('Medicamento seleccionado:', med);
        searchInput.value = med.codigo + ' - ' + med.nombre;
        hiddenInput.value = med.id;
        selectedName.textContent = med.nombre;
        selectedCode.textContent = 'Código: ' + med.codigo + ' | Concentración: ' + (med.concentracion || 'N/A');
        selectedDiv.style.display = 'block';
        resultsDiv.style.display = 'none';
    }

    // Limpiar búsqueda
    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        hiddenInput.value = '';
        selectedDiv.style.display = 'none';
        resultsDiv.style.display = 'none';
        searchInput.focus();
    });

    // Cerrar resultados al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#medicamento-search') && !e.target.closest('#medicamento-results')) {
            resultsDiv.style.display = 'none';
        }
    });
});
</script>

<style>
#medicamento-results .list-group-item {
    cursor: pointer;
    border: 1px solid #dee2e6;
}

#medicamento-results .list-group-item:hover {
    background-color: #f0f0f0;
}
</style>
