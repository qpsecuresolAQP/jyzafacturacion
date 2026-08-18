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
    });

// Inicializa eventos relacionados al paciente
document
    .getElementById("paciente-id")
    .addEventListener("change", updatePacienteData);

// Agrega un nuevo tratamiento
function validateAndAddTratamiento() {
    const rows = document.querySelectorAll("#tabla-tratamientos tbody tr");
    for (let row of rows) {
        const searchInput = row.querySelector(".tratamiento-search");
        const tratamientoIdInput = row.querySelector(".tratamiento-id");

        // Verifica si el campo de búsqueda está vacío o si no se ha seleccionado un tratamiento
        if (!searchInput.value || !tratamientoIdInput.value) {
            alert(
                "Debe seleccionar un tratamiento antes de agregar otro nuevo."
            );
            return; // Detiene la ejecución si no se cumple la validación
        }
    }
    addTratamiento(); // Solo se llama a addTratamiento si todas las validaciones son correctas
}

function addTratamiento() {
    const table = document.querySelector("#tabla-tratamientos tbody");
    const index = table.children.length;

    const row = document.createElement("tr");
    row.innerHTML = `
        <input type="hidden" name="tratamientos[${index}][id]" value="" />
        <td><input type="number" name="tratamientos[${index}][cantidad]" class="form-control cantidad" min="1" value="1"></td>
        <td>
            <input type="text" name="tratamientos[${index}][tratamiento_name]" class="form-control tratamiento-search" placeholder="Escriba para buscar tratamiento" autocomplete="off">
            <input type="hidden" name="tratamientos[${index}][tratamiento_id]" class="tratamiento-id" value="">
            <ul class="tratamiento-suggestions" style="list-style-type: none; padding-left: 0; margin-top: 5px; max-height: 150px; overflow-y: auto;">
            <style>
                .tratamiento-item {
                    padding: 10px;
                    cursor: pointer;
                    transition: background-color 0.3s;
                }
                .tratamiento-item:hover {
                    background-color: #111;
                }
            </style>
            ${tratamientosData
                .map(
                    (t) => `
                <li class="tratamiento-item" 
                    data-id="${t.id}" 
                    data-name="${t.nombre}" 
                    data-precio="${t.costo}">
                    ${t.nombre}
                </li>`
                )
                .join("")}
        </ul>



        </td>
        <td><input type="number" name="tratamientos[${index}][precio_unitario]" class="form-control precio-unitario" step="0.01" min="0" value="0"></td>
        <td><input type="text" name="tratamientos[${index}][total]" class="form-control subtotal" readonly value="0"></td>
        <td><textarea name="tratamientos[${index}][observaciones]" class="form-control" placeholder="Observaciones..." rows="1" maxlength="500"></textarea></td>
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

        // Establecer el nombre en el input de búsqueda
        searchInput.value = tratamientoName;

        // Establecer el ID en el input hidden
        tratamientoIdInput.value = tratamientoId;

        // Establecer el precio en el input de precio unitario
        precioUnitarioInput.value = tratamientoPrecio;

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
    const paciente = pacientesData.find((p) => p.id == pacienteId) || {};

    document.getElementById("paciente-direccion").value =
        paciente.direccion || "";
    document.getElementById("paciente-dni").value = paciente.dni || "";
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
    const precioUnitario =
        parseFloat(row.querySelector(".precio-unitario").value) || 0;
    const subtotalInput = row.querySelector(".subtotal");

    const subtotal = cantidad * precioUnitario;
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