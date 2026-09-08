<style> 
    .error-js{
        display:none;
    }
</style>
<?= $this->Form->create($presupuesto, ['class' => 'row g-3']) ?>

<div class="container mt-4 mb-4">

    <div class="col-md-12 mb-3">
    <div class="form-check form-switch">
<input
    class="form-check-input"
    type="checkbox"
    id="esProforma"
    <?= empty($presupuesto->historia_id) ? 'checked' : '' ?>
>        <label class="form-check-label" for="esProforma">
            Proforma
        </label>
    </div>
</div>
<div class="container mt-4 mb-4">
    <!-- Selección de Paciente -->
    <div class="col-12 mb-4">
        <h3 class="text-info"><i class="fas fa-user"></i> Agregar Presupuesto</h3>
    </div>
<div id="bloquePaciente">
    <!-- Buscador de Paciente -->
    <div class="col-md-12 mx-auto mb-3">
        <label for="searchPaciente" class="form-label">Buscar Paciente</label>
        <div class="input-group">
            <?= $this->Form->text('search_paciente', [
                'label' => false,
                'class' => 'form-control',
                'id' => 'searchPaciente',
                'placeholder' => 'Ingrese el nombre o apellido del paciente',
                'value' => $presupuesto->historias_clinica ? $presupuesto->historias_clinica->paciente->nombre . ' ' . $presupuesto->historias_clinica->paciente->apellido : '',
            ]) ?>
            <button type="button" id="searchButton" class="btn btn-primary">
                <i class="fas fa-search"></i> Buscar
            </button>
        </div>
        <div id="pacienteResults" class="list-group mt-2"></div>
        <?= $this->Form->hidden('historia_id', ['id' => 'historiaId', 'value' => $presupuesto->historia_id]) ?>
        <?= $this->Form->hidden('paciente_id_search', ['id' => 'pacienteId']) ?>
    </div>
</div>
    
<!-- Mostrar Dirección y DNI del paciente seleccionado -->
<div class="col-md-3 mb-3 error-js">
    <label class="form-label">Dirección</label>
    <input type="text" id="paciente-direccion" class="form-control" readonly />
</div>
<div class="col-md-3 mb-3 error-js">
    <label class="form-label">DNI</label>
    <input type="text" id="paciente-dni" class="form-control" readonly/>
</div>

<div id="bloqueProforma" class="container" style="display:none;">

    <div class="col-md-10 mb-3">
        <?= $this->Form->control('nombre_apellido', [
            'label' => 'Nombre y Apellido / Referencia (Puede estar vacio)',
            'class' => 'form-control',
            'type' => 'text',
        ]) ?>
    </div>

    <div class="col-md-10 mb-3">
        <?= $this->Form->control('telefono', [
            'label' => 'Teléfono (Puede estar vacio)',
            'class' => 'form-control',
        ]) ?>
    </div>

</div>

<!-- fecha modified -->
<div class="col-md-3 mb-3">
    <?= $this->Form->control('modified', [
        'label' => 'Fecha de Presupuesto',
        'class' => 'form-control',
        'type' => 'date',
        'value' => $presupuesto->modified ? $presupuesto->modified->format('Y-m-d') : date('Y-m-d'),
    ]) ?>
</div>

<!-- notas -->
<div class="col-md-10 mb-3">
<?= $this->Form->control('notas', [
    'label' => 'Observaciones',
    'type' => 'text',
    'class' => 'form-control',
    'value' => $presupuesto->notas ? $presupuesto->notas : '',
]) ?>
</div>

    <!-- Tabla de Tratamientos -->
    <div class="col-12 mb-4 mt-3">
        <h3 class="text-info"><i class="fas fa-briefcase-medical"></i> Servicios, Productos y Exámenes</h3>
    </div>

    <div class="col-12">
        <div class="table-responsive">
            <table class="table table-striped" id="tabla-tratamientos">
                <thead class="bg-info text-white">
                    <tr>
                        <th><?= __('Cantidad') ?></th>
                        <th><?= __('Ítem') ?></th>
                        <th><?= __('Precio Unitario') ?></th>
                        <th style="min-width: 90px;"><?= __('Descuento') ?></th>
                        <th><?= __('Subtotal') ?></th>
                        <th><?= __('Observaciones') ?></th>
                        <th><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($presupuesto->presupuestos_tratamientos as $index => $tratamiento):
                        $tipoItem = $tratamiento->tipo_item ?? 'tratamiento';
                        if ($tipoItem === 'producto') {
                            $itemNombre = $tratamiento->producto->nombre ?? '';
                            $itemIdField = 'producto_id';
                            $itemIdValue = $tratamiento->producto_id;
                            $itemPlaceholder = 'Escriba para buscar producto';
                            $itemOptions = $productosData;
                            $itemPrecioKey = 'precio';
                        } elseif ($tipoItem === 'examen') {
                            $itemNombre = $tratamiento->examene->nombre ?? '';
                            $itemIdField = 'examen_id';
                            $itemIdValue = $tratamiento->examen_id;
                            $itemPlaceholder = 'Escriba para buscar examen';
                            $itemOptions = $examenesData;
                            $itemPrecioKey = 'precio';
                        } else {
                            $itemNombre = $tratamiento->tratamiento->nombre ?? '';
                            $itemIdField = 'tratamiento_id';
                            $itemIdValue = $tratamiento->tratamiento_id;
                            $itemPlaceholder = 'Escriba para buscar tratamiento';
                            $itemOptions = $tratamientosData;
                            $itemPrecioKey = 'costo';
                        }
                    ?>
                    <tr>
                        <!-- Campo ID oculto -->
                        <?= $this->Form->control("tratamientos.$index.id", [
                            'type' => 'hidden',
                            'value' => $tratamiento->id,
                        ]) ?>
                        <?= $this->Form->control("tratamientos.$index.tipo_item", [
                            'type' => 'hidden',
                            'value' => $tipoItem,
                        ]) ?>

                        <!-- Campo de cantidad -->
                        <td>
                            <?= $this->Form->control("tratamientos.$index.cantidad", [
                                'type' => 'number',
                                'value' => $tratamiento->cantidad,
                                'min' => 1,
                                'class' => 'form-control cantidad',
                                'label' => false,
                            ]) ?>
                        </td>

                        <!-- Campo de búsqueda para el ítem (tratamiento/producto/examen) -->
                        <td>
                            <input type="text" name="tratamientos[<?= $index ?>][item_name]"
                                   class="form-control tratamiento-search"
                                   placeholder="<?= h($itemPlaceholder) ?>"
                                   value="<?= h($itemNombre) ?>"
                                   autocomplete="off">

                            <input type="hidden" name="tratamientos[<?= $index ?>][<?= $itemIdField ?>]"
                                   class="tratamiento-id"
                                   value="<?= h($itemIdValue) ?>">

                            <ul class="tratamiento-suggestions"
                                style="list-style-type: none; padding-left: 0; margin-top: 5px; max-height: 150px; overflow-y: auto;">
                                <?php foreach ($itemOptions as $t): ?>
                                    <li class="tratamiento-item" data-id="<?= $t['id'] ?>"
                                        data-name="<?= h($t['nombre']) ?>"
                                        data-precio="<?= $t[$itemPrecioKey] ?>">
                                        <?= h($t['nombre']) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>

                        <!-- Campo de precio unitario -->
                        <td>
                            <?= $this->Form->control("tratamientos.$index.precio_unitario", [
                                'type' => 'number',
                                'value' => $tratamiento->precio_unitario,
                                'class' => 'form-control precio-unitario',
                                'step' => '0.01',
                                'min' => 0,
                                'label' => false,
                            ]) ?>
                        </td>

                        <!-- Campo de descuento -->
                        <td>
                            <?= $this->Form->control("tratamientos.$index.descuento", [
                                'type' => 'number',
                                'value' => $tratamiento->descuento,
                                'class' => 'form-control descuento mb-1',
                                'step' => '0.01',
                                'min' => 0,
                                'label' => false,
                            ]) ?>
                            <?= $this->Form->select("tratamientos.$index.descuento_tipo", [
                                'porcentaje' => '%',
                                'monto' => 'S/',
                            ], [
                                'value' => $tratamiento->descuento_tipo ?? 'porcentaje',
                                'class' => 'form-select descuento-tipo',
                            ]) ?>
                        </td>

                        <!-- Campo de subtotal -->
                        <td>
                            <?= $this->Form->control("tratamientos.$index.total", [
                                'type' => 'text',
                                'value' => $tratamiento->total,
                                'class' => 'form-control subtotal',
                                'readonly' => true,
                                'label' => false,
                            ]) ?>
                        </td>

                        <!-- Campo de observaciones -->
                        <td>
                            <?= $this->Form->control("tratamientos.$index.observaciones", [
                                'type' => 'text',
                                'value' => isset($tratamiento->observaciones) ? $tratamiento->observaciones : '',
                                'class' => 'form-control observaciones',
                                'placeholder' => 'Observaciones',
                                'label' => false,
                            ]) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>                
            </table>
        </div>
        <button id="add-tratamiento-btn" type="button" class="btn btn-info mt-2" onclick="validateAndAddTratamiento('tratamiento')">
            Agregar Tratamiento
        </button>
        <button id="add-producto-btn" type="button" class="btn btn-info mt-2" onclick="validateAndAddTratamiento('producto')">
            Agregar Producto
        </button>
        <button id="add-examen-btn" type="button" class="btn btn-info mt-2" onclick="validateAndAddTratamiento('examen')">
            Agregar Examen
        </button>
    </div>

    <!-- Desglose de Totales -->
    <div class="col-12 mt-4 mb-4 mt-3">
        <h3 class="text-info"><i class="fas fa-calculator"></i> Total</h3>
    </div>

    <div class="col-md-4 mb-3">
        <?= $this->Form->control('total_visible', [
            'label' => 'Total',
            'class' => 'form-control',
            'id' => 'total-visible',
            'readonly' => true,
        ]) ?>
    </div>
    <div class="col-md-4 mb-3">
        <?= $this->Form->control('subtotal', [
            'type' => 'hidden',
            'class' => 'form-control',
            'id' => 'subtotal',
            'readonly' => true,
        ]) ?>
    </div>
    <div class="col-md-4 mb-3">
        <?= $this->Form->control('igv', [
            'type' => 'hidden',
            'class' => 'form-control',
            'id' => 'igv',
            'readonly' => true,
        ]) ?>
    </div>
    <div class="col-md-4 mb-3">
        <?= $this->Form->control('total', [
            'type' => 'hidden',
            'class' => 'form-control',
            'id' => 'total',
            'readonly' => true,
        ]) ?>
    </div>

    <!-- Botones -->
    <div class="col-12 text-center">
        
        <?= $this->Form->button(__('Guardar Presupuesto'), ['class' => 'btn btn-info']) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary me-2']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>

<!-- Datos en formato JSON para JS -->
<script>
    window.pacientesData = <?= json_encode($pacientesData) ?>;
    window.pacientesData2 = <?= json_encode($pacientesData2) ?>;
    window.tratamientosData = <?= json_encode($tratamientosData) ?>;
    window.productosData = <?= json_encode($productosData) ?>;
    window.examenesData = <?= json_encode($examenesData) ?>;
</script>
<script>
$(document).on('change', '#esProforma', function () {

    if ($(this).is(':checked')) {

        $('#bloquePaciente').hide();
        $('#bloqueProforma').show();

        $('#historiaId').val('');
        $('#pacienteId').val('');
        $('#searchPaciente').val('');

    } else {

        $('#bloquePaciente').show();
        $('#bloqueProforma').hide();

    }

    validateForm(); // <- importante
});

// Estado inicial cuando se carga el formulario
$(document).on('shown.bs.modal', function () {

    const chk = $('#esProforma');

    if (!chk.length) {
        return;
    }

    if (chk.is(':checked')) {

        $('#bloquePaciente').hide();
        $('#bloqueProforma').show();

        $('.error-js').hide();

    } else {

        $('#bloquePaciente').show();
        $('#bloqueProforma').hide();

    }

});
$(document).ready(function () {

    if ($('#esProforma').is(':checked')) {

        $('#bloquePaciente').hide();
        $('#bloqueProforma').show();

    } else {

        $('#bloquePaciente').show();
        $('#bloqueProforma').hide();

    }

    validateForm();

});
</script>
<script>
    // ===== BÚSQUEDA Y SELECCIÓN DE PACIENTES =====
    document.getElementById("searchButton").addEventListener("click", function() {
        const searchText = document.getElementById("searchPaciente").value.toLowerCase();
        const resultados = document.getElementById("pacienteResults");
        resultados.innerHTML = "";

        const filtrados = pacientesData.filter(p =>
            p.nombre.toLowerCase().includes(searchText) ||
            p.apellido.toLowerCase().includes(searchText)
        );

        if (filtrados.length === 0) {
            resultados.innerHTML = '<div class="alert alert-warning">No se encontraron pacientes</div>';
            return;
        }

        filtrados.forEach(paciente => {
            const div = document.createElement("div");
            div.className = "list-group-item cursor-pointer";
            div.textContent = `${paciente.nombre} ${paciente.apellido}`;
            div.style.cursor = "pointer";
            div.addEventListener("click", function() {
                document.getElementById("searchPaciente").value = `${paciente.nombre} ${paciente.apellido}`;
                document.getElementById("pacienteId").value = paciente.id;
                
                // Encontrar la historia_id correspondiente a este paciente_id
                const pacienteData = pacientesData2.find((p) => parseInt(p.paciente_id) === parseInt(paciente.id));
                if (pacienteData) {
                    document.getElementById("historiaId").value = pacienteData.historia_id;
                }
                
                resultados.innerHTML = "";
                updatePacienteData({ target: { value: paciente.id } });
            });
            resultados.appendChild(div);
        });
    });

    // ===== CONFIGURACIÓN DE BÚSQUEDA DE TRATAMIENTOS =====
    document.querySelectorAll(".tratamiento-search").forEach(setupTratamientoSearch);

    function setupTratamientoSearch(searchInput) {
        const row = searchInput.closest("tr");
        const suggestionsList = row.querySelector(".tratamiento-suggestions");
        const tratamientoIdInput = row.querySelector(".tratamiento-id");
        const precioUnitarioInput = row.querySelector(".precio-unitario");

        // Verificar si ya hay un tratamiento seleccionado
        if (tratamientoIdInput.value) {
            suggestionsList.style.display = "none"; // Ocultar sugerencias si ya hay selección
            updateSubtotal(row); // Recalcular subtotal al cargar la página
        }

        // Filtrar sugerencias mientras se escribe
        searchInput.addEventListener("input", () => {
            const searchText = searchInput.value.toLowerCase();
            const items = suggestionsList.querySelectorAll(".tratamiento-item");

            let hasMatch = false;

            items.forEach((item) => {
                const name = item.getAttribute("data-name").toLowerCase();
                const match = name.includes(searchText);
                item.style.display = match ? "block" : "none";
                if (match) hasMatch = true;
            });

            // Mostrar sugerencias solo si hay coincidencias y si el usuario está escribiendo
            suggestionsList.style.display = hasMatch ? "block" : "none";
        });

        // Mostrar sugerencias solo si el usuario borra el tratamiento y empieza a escribir otro
        searchInput.addEventListener("focus", () => {
            if (!tratamientoIdInput.value) {
                suggestionsList.style.display = "block";
            }
        });

        // Seleccionar un tratamiento de la lista
        suggestionsList.addEventListener("click", (event) => {
            const selectedItem = event.target.closest(".tratamiento-item");
            if (!selectedItem) return;

            searchInput.value = selectedItem.getAttribute("data-name");
            tratamientoIdInput.value = selectedItem.getAttribute("data-id");
            precioUnitarioInput.value = selectedItem.getAttribute("data-precio");

            updateSubtotal(row);
            suggestionsList.style.display = "none";
        });

        // Ocultar sugerencias al hacer clic fuera
        document.addEventListener("click", (event) => {
            if (!row.contains(event.target)) {
                suggestionsList.style.display = "none";
            }
        });

        // Si el usuario borra el campo, volver a mostrar la lista
        searchInput.addEventListener("input", () => {
            if (searchInput.value.trim() === "") {
                tratamientoIdInput.value = ""; // Borrar el ID del tratamiento
                suggestionsList.style.display = "block";
            }
        });
    }

    // Recalcula los costos al cargar la página
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll(".tratamiento-search").forEach((searchInput) => {
            const row = searchInput.closest("tr");
            updateSubtotal(row);
        });
    });

    document
        .querySelector("#tabla-tratamientos tbody")
        .addEventListener("input", (event) => {
            const row = event.target.closest("tr");
            if (!row) return;

            if (event.target.classList.contains("cantidad")) {
                const cantidadInput = event.target;
                let cantidad = parseFloat(cantidadInput.value);
                if (isNaN(cantidad) || cantidad < 1) {
                    cantidadInput.value = 1; // Asignar 1 si la cantidad es inválida o negativa
                }
                updateSubtotal(row);
            }

            if (event.target.classList.contains("descuento")) {
                const descuentoInput = event.target;
                let descuento = parseFloat(descuentoInput.value);
                if (isNaN(descuento) || descuento < 0) {
                    descuentoInput.value = 0; // Asignar 0 si el descuento es inválido o negativo
                }
                updateSubtotal(row);
            }

            if (event.target.classList.contains("precio-unitario")) {
                const precioInput = event.target;
                let precioUnitario = parseFloat(precioInput.value);
                if (isNaN(precioUnitario) || precioUnitario < 0) {
                    precioInput.value = 0; // Asignar 0 si el precio es inválido o negativo
                }
                updateSubtotal(row);
            }
        });

    document
        .querySelector("#tabla-tratamientos tbody")
        .addEventListener("change", (event) => {
            const row = event.target.closest("tr");
            if (!row) return;

            if (event.target.classList.contains("tratamiento-select")) {
                updateTratamiento(row);
            }

            if (event.target.classList.contains("descuento-tipo")) {
                updateSubtotal(row);
            }
        });

    // Inicializa eventos relacionados al paciente
    document
        .getElementById("paciente-id")
        ?.addEventListener("change", updatePacienteData);

    // Configuración por tipo de ítem: fuente de datos, campo id, placeholder
    window.itemTypeConfig = {
        tratamiento: {
            data: tratamientosData,
            idField: 'tratamiento_id',
            placeholder: 'Escriba para buscar tratamiento',
            getPrecio: (t) => t.costo,
        },
        producto: {
            data: productosData,
            idField: 'producto_id',
            placeholder: 'Escriba para buscar producto',
            getPrecio: (p) => p.precio,
        },
        examen: {
            data: examenesData,
            idField: 'examen_id',
            placeholder: 'Escriba para buscar examen',
            getPrecio: (e) => e.precio,
        },
    };

    // Agrega un nuevo tratamiento/producto/examen
    function validateAndAddTratamiento(tipoItem) {
        const rows = document.querySelectorAll("#tabla-tratamientos tbody tr");
        for (let row of rows) {
            const searchInput = row.querySelector(".tratamiento-search");
            const tratamientoIdInput = row.querySelector(".tratamiento-id");

            // Verifica si el campo de búsqueda está vacío o si no se ha seleccionado un ítem
            if (!searchInput.value || !tratamientoIdInput.value) {
                alert(
                    "Debe seleccionar un ítem antes de agregar otro nuevo."
                );
                return; // Detiene la ejecución si no se cumple la validación
            }
        }
        addTratamiento(tipoItem); // Solo se llama a addTratamiento si todas las validaciones son correctas
    }

    function addTratamiento(tipoItem) {
        tipoItem = tipoItem || 'tratamiento';
        const config = itemTypeConfig[tipoItem];
        const table = document.querySelector("#tabla-tratamientos tbody");
        const index = table.children.length;

        const row = document.createElement("tr");
        row.dataset.tipoItem = tipoItem;
        row.innerHTML = `
            <input type="hidden" name="tratamientos[${index}][id]" value="" />
            <input type="hidden" name="tratamientos[${index}][tipo_item]" value="${tipoItem}" />
            <td><input type="number" name="tratamientos[${index}][cantidad]" class="form-control cantidad" min="1" value="1"></td>
            <td>
                <input type="text" name="tratamientos[${index}][item_name]" class="form-control tratamiento-search" placeholder="${config.placeholder}" autocomplete="off">
                <input type="hidden" name="tratamientos[${index}][${config.idField}]" class="tratamiento-id" value="">
                <ul class="tratamiento-suggestions" style="list-style-type: none; padding-left: 0; margin-top: 5px; max-height: 150px; overflow-y: auto;">
                <style>
                    .tratamiento-item {
                        padding: 10px;
                        cursor: pointer;
                        transition: background-color 0.3s;
                    }
                    .tratamiento-item:hover {
                        background-color: #e4dede;
                    }
                </style>
                ${config.data
                    .map(
                        (t) => `
                    <li class="tratamiento-item"
                        data-id="${t.id}"
                        data-name="${t.nombre}"
                        data-precio="${config.getPrecio(t)}">
                        ${t.nombre}
                    </li>`
                    )
                    .join("")}
            </ul>
            </td>
            <td><input type="number" name="tratamientos[${index}][precio_unitario]" class="form-control precio-unitario" step="0.01" min="0" value="0"></td>
            <td>
                <input type="number" name="tratamientos[${index}][descuento]" class="form-control descuento mb-1" step="0.01" min="0" value="0">
                <select class="form-select descuento-tipo" name="tratamientos[${index}][descuento_tipo]">
                    <option value="porcentaje">%</option>
                    <option value="monto">S/</option>
                </select>
            </td>
            <td><input type="text" name="tratamientos[${index}][total]" class="form-control subtotal" readonly value="0"></td>
            <td><input type="text" name="tratamientos[${index}][observaciones]" class="form-control observaciones" placeholder="Observaciones"></td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-remove-row">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        table.appendChild(row);
        updateSubtotal(row);

        const searchInput = row.querySelector(".tratamiento-search");
        const suggestionsList = row.querySelector(".tratamiento-suggestions");
        const tratamientoIdInput = row.querySelector(".tratamiento-id");
        const precioUnitarioInput = row.querySelector(".precio-unitario");
        const descuentoInput = row.querySelector(".descuento");

        // Filtrar las sugerencias a medida que se escribe
        searchInput.addEventListener("input", () => {
            const searchText = searchInput.value.toLowerCase();
            const items = suggestionsList.querySelectorAll(".tratamiento-item");

            items.forEach((item) => {
                const name = item.getAttribute("data-name").toLowerCase();
                if (name.includes(searchText)) {
                    item.style.display = "block"; // Mostrar sugerencia si coincide
                } else {
                    item.style.display = "none"; // Ocultar sugerencia si no coincide
                }
            });
        });

        // Seleccionar un tratamiento al hacer clic
        suggestionsList.addEventListener("click", (event) => {
            const selectedItem = event.target.closest(".tratamiento-item");
            if (!selectedItem) return;

            const tratamientoId = selectedItem.getAttribute("data-id");
            const tratamientoName = selectedItem.getAttribute("data-name");
            const tratamientoPrecio = selectedItem.getAttribute("data-precio");
            const tratamientoDescuento = selectedItem.getAttribute("data-descuento");

            // Establecer el nombre en el input de búsqueda
            searchInput.value = tratamientoName;

            // Establecer el ID en el input hidden
            tratamientoIdInput.value = tratamientoId;

            // Establecer el precio en el input de precio unitario
            precioUnitarioInput.value = tratamientoPrecio;

            // Establecer el descuento en el input de descuento
            descuentoInput.value = tratamientoDescuento;

            // Recalcular el subtotal con el precio actualizado
            updateSubtotal(row);

            // Ocultar las sugerencias después de seleccionar un tratamiento
            suggestionsList.style.display = "none";
        });

        // Ocultar las sugerencias al hacer clic fuera del campo de búsqueda
        document.addEventListener("click", (event) => {
            if (!row.contains(event.target)) {
                suggestionsList.style.display = "none";
            }
        });

        // Mostrar las sugerencias al hacer clic en el input
        searchInput.addEventListener("focus", () => {
            suggestionsList.style.display = "block";
        });

        // Elimina una fila
        document
            .querySelector("#tabla-tratamientos tbody")
            .addEventListener("click", (event) => {
                if (event.target.closest(".btn-remove-row")) {
                    event.target.closest("tr").remove();
                    calculateTotal();
                }
            });
    }

    // Actualiza los datos del paciente seleccionado
    function updatePacienteData(event) {
        const pacienteId = event.target.value;
        const pacienteData2 = pacientesData2.find((p) => p.paciente_id == pacienteId) || {};

        document.getElementById("paciente-direccion").value =
            pacienteData2.direccion || "";
        document.getElementById("paciente-dni").value = pacienteData2.dni || "";
    }

    // Actualiza los valores de tratamiento (precio unitario y subtotal)
    function updateTratamiento(row) {
        const select = row.querySelector(".tratamiento-select");
        const tratamientoId = select.value;
        const precioUnitarioInput = row.querySelector(".precio-unitario");

        const tratamiento =
            tratamientosData.find((t) => t.id == tratamientoId) || {};
        precioUnitarioInput.value = tratamiento.costo || "0";
        updateSubtotal(row);
    }

    // Calcula el subtotal de una fila
    function updateSubtotal(row) {
        const cantidad = parseFloat(row.querySelector(".cantidad").value) || 0;
        const precioUnitario = parseFloat(row.querySelector(".precio-unitario").value) || 0;
        const subtotalInput = row.querySelector(".subtotal");
        const descuento = parseFloat(row.querySelector(".descuento").value) || 0;
        const descuentoTipoSelect = row.querySelector(".descuento-tipo");
        const descuentoTipo = descuentoTipoSelect ? descuentoTipoSelect.value : "porcentaje";

        let precioConDescuento = precioUnitario;

        // Aplicar descuento (igual que en Invoices/add): por unidad, en % o en S/
        if (descuento > 0 && precioUnitario > 0) {
            if (descuentoTipo === "porcentaje") {
                precioConDescuento = precioUnitario - (precioUnitario * descuento) / 100;
            } else {
                precioConDescuento = precioUnitario - descuento;
            }
            precioConDescuento = Math.max(0, precioConDescuento);
        }

        const subtotal = cantidad * precioConDescuento;
        subtotalInput.value = subtotal.toFixed(2);
        calculateTotal();
    }

    // Calcula los totales de la tabla
    function calculateTotal() {
        const subtotales = Array.from(document.querySelectorAll(".subtotal")).map(
            (input) => parseFloat(input.value) || 0
        );

        const total = subtotales.reduce((sum, value) => sum + value, 0);

        document.getElementById("subtotal").value = total.toFixed(2);
        document.getElementById("total").value = total.toFixed(2);
        document.getElementById("total-visible").value = total.toFixed(2);
    }

    // Inicializa los tratamientos existentes al cargar la página
    function initializeTratamientos() {
        const rows = document.querySelectorAll("#tabla-tratamientos tbody tr");
        rows.forEach((row) => updateSubtotal(row));
        calculateTotal();
    }

    initializeTratamientos();
</script>

