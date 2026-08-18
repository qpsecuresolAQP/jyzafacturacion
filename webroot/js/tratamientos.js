// Inicializar la variable tratamientoIndex según el número de tratamientos existentes
let tratamientoIndex = document.querySelectorAll('.tratamiento').length;

function addTratamiento() {
    const container = document.getElementById('tratamientos-container');
    const newDiv = document.createElement('div');
    newDiv.className = 'tratamiento';

    const controlHTML = `
        <h3>Tratamiento #${tratamientoIndex + 1}</h3>
        <!-- Campo oculto para el ID del registro de tratamiento -->
        <input type="hidden" name="registros_tratamientos[${tratamientoIndex}][id]" value="" />

        <!-- Menú desplegable para seleccionar tratamiento -->
        <label for="tratamiento-select-${tratamientoIndex}">Nombre del Tratamiento</label>
        <select name="registros_tratamientos[${tratamientoIndex}][tratamiento_id]" 
                id="tratamiento-select-${tratamientoIndex}" 
                class="tratamiento-select" 
                data-index="${tratamientoIndex}" 
                required>
            <option value="">Seleccionar Tratamiento</option>
            ${tratamientosData.map(t => `<option value="${t.id}">${t.nombre}</option>`).join('')}
        </select>

        <!-- Campos para mostrar detalles del tratamiento -->
        <input type="hidden" name="registros_tratamientos[${tratamientoIndex}][nombre]" 
               id="nombre-${tratamientoIndex}" 
               class="tratamiento-nombre" 
               readonly  />

        <label for="descripcion-${tratamientoIndex}">Descripción</label>
        <input type="text" name="registros_tratamientos[${tratamientoIndex}][descripcion]" 
               id="descripcion-${tratamientoIndex}" 
               class="tratamiento-descripcion" 
               readonly />

        <label for="costo-${tratamientoIndex}">Costo</label>
        <input type="number" name="registros_tratamientos[${tratamientoIndex}][costo]" 
               id="costo-${tratamientoIndex}" 
               class="tratamiento-costo" 
               readonly />
    `;

    newDiv.innerHTML = controlHTML;
    container.appendChild(newDiv);

    // Agregar evento de cambio dinámico al nuevo select
    const select = newDiv.querySelector('.tratamiento-select');
    select.addEventListener('change', function () {
        const index = this.getAttribute('data-index'); // Índice del tratamiento
        const tratamientoId = this.value; // ID del tratamiento seleccionado

        // Buscar los datos del tratamiento seleccionado
        const tratamiento = tratamientosData.find(t => t.id == tratamientoId);

        if (tratamiento) {
            // Actualizar los campos correspondientes
            document.getElementById(`nombre-${index}`).value = tratamiento.nombre;
            document.getElementById(`descripcion-${index}`).value = tratamiento.descripcion;
            document.getElementById(`costo-${index}`).value = tratamiento.costo;
        } else {
            // Si no se selecciona un tratamiento válido, limpiar los campos
            document.getElementById(`nombre-${index}`).value = '';
            document.getElementById(`descripcion-${index}`).value = '';
            document.getElementById(`costo-${index}`).value = '';
        }
    });

    tratamientoIndex++;
}
function removeRowTratamiento() {
    const container = document.getElementById('tratamientos-container');
    if (tratamientoIndex >= length) {
        container.removeChild(container.lastChild);
        tratamientoIndex--;
    }
}