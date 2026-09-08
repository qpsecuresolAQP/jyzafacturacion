<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">

    <div class="row justify-content-center">
        <div class="col-lg-11">
            <!-- HEADER -->
            <div class="card shadow border-0 mb-5">
                <div class="card-header bg-info">
                    <h4 class="text-white mb-0 fw-bold">
                        <i class="fas fa-receipt me-2"></i>Nuevo Comprobante / Recibo Interno
                    </h4>
                </div>

                <div class="card-body">
                    <?= $this->Form->create(null, [
                        'autocomplete' => 'off'
                    ]) ?>
                    <!-- INFORMACIÓN DEL USUARIO -->
                    <div class="alert alert-info border-2 border-info mb-4">
                        <i class="fas fa-user-check me-2"></i>
                        <strong>Usuario Autenticado:</strong> 
                        <span id="current-user" class="badge bg-info"><?= h($userName ?? 'No identificado') ?></span>
                        <input type="hidden" name="user_id" value="<?= (int)($userId ?? 0) ?>">
                        <input type="hidden" id="user_id_field" value="<?= (int)($userId ?? 0) ?>">
                        <?php if (!empty($presupuestoId)): ?>
                            <input type="hidden" name="presupuesto_id" value="<?= (int) $presupuestoId ?>">
                        <?php endif; ?>
                    </div>

                    <!-- SELECCIÓN DE CAJA -->
                    <?php if ($cajaSeleccionada): ?>
                        <!-- CAJA PRE-SELECCIONADA -->
                        <div class="alert alert-success border-2 border-success mb-4">
                            <i class="fas fa-cash-register me-2"></i>
                            <strong>Caja Seleccionada:</strong> 
                            <span class="badge bg-success"><?= h($cajaSeleccionada->nombre) ?> (<?= h($cajaSeleccionada->codigo) ?>)</span>
                            <input type="hidden" name="caja_id" value="<?= (int)$cajaSeleccionada->id ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Caja</label>
                            <input type="text" class="form-control form-control-solid bg-light" value="<?= h($cajaSeleccionada->nombre) ?> (<?= h($cajaSeleccionada->codigo) ?>)" disabled>
                        </div>
                    <?php else: ?>
                        <!-- SELECTOR DE CAJAS -->
                        <div class="alert alert-warning border-2 border-warning mb-4">
                            <i class="fas fa-cash-register me-2"></i>
                            <strong>Selecciona una Caja:</strong>
                            <div class="mt-2">
                                <select name="caja_id" id="caja_id" class="form-select form-select-solid" required>
                                    <option value="">-- Selecciona una caja abierta --</option>
                                    <?php foreach ($cajasAbiertas as $caja): ?>
                                        <option value="<?= (int)$caja->id ?>">
                                            <?= h($caja->nombre) ?> (<?= h($caja->codigo) ?>) - S/ <?= number_format($caja->monto_inicial, 2) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>

                        <!-- SECCIÓN 1: DATOS GENERALES -->
                        <div class="card card-flush mb-4 border-0 border-top border-5 border-info">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-bold text-info">
                                    <i class="fas fa-info-circle me-2"></i>Datos del Comprobante
                                </h6>
                            </div>
                            <div class="card-body pt-4 pb-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Empresa</label>
                                        <select name="company_id" id="company_id" class="form-select form-select-solid" required>
                                            <option value="">Seleccione una empresa</option>
                                            <?php foreach ($companies as $id => $label): ?>
                                                <option value="<?= $id ?>"><?= h($label) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Tipo Documento</label>
                                        <select name="tipo_doc" id="tipo_doc" class="form-select form-select-solid" required>
                                            <option value="RI">Recibo Interno</option>
                                            <option value="03" selected>Boleta</option>
                                            <option value="01">Factura</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mt-3">
                                        <label class="form-label fw-semibold">Doctor Responsable</label>
                                        <select name="doctor_id" id="doctor_id" class="form-select form-select-solid">
                                            <option value="">Seleccione un doctor</option>
                                            <?php foreach ($doctores as $id => $label): ?>
                                                <option value="<?= $id ?>"><?= h($label) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 2: CLIENTE -->
                        <div class="card card-flush mb-4 border-0 border-top border-5 border-info">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-bold text-info">
                                    <i class="fas fa-user-circle me-2"></i>Datos del Cliente
                                </h6>
                            </div>
                            <div class="card-body pt-4 pb-4">
                                <!-- Búsqueda de Paciente -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Buscar Paciente</label>
                                    <input type="text" id="buscarPaciente" placeholder="Ingrese nombre del paciente..." class="form-control form-control-solid" autocomplete="off">
                                    <input type="hidden" name="paciente_id" id="paciente_id">
                                    <div id="resultadosPacientes" class="list-group list-group-flush mt-2" style="max-height: 250px; overflow-y: auto;"></div>
                                </div>

                                <!-- Campos Dinámicos Boleta/Factura -->
                                <div id="boleta-fields" class="row g-3" style="display: none;">
                                <!-- NUEVO: selector tipo doc -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Tipo Documento</label>
                                    <select name="boleta_tipo_doc" id="boleta_tipo_doc" class="form-select form-select-solid">
                                        <option value="1">DNI</option>
                                        <option value="4">Carnet Extranjería</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" id="boleta_doc_label">DNI</label>
                                    <div class="input-group">
                                        <input type="text" name="boleta_dni" id="boleta_dni" 
                                            class="form-control form-control-solid" 
                                            maxlength="9" placeholder="12345678">
                                        <button type="button" class="btn btn-outline-primary" id="btn-buscar-dni">
                                            <i class="fas fa-search"></i> Buscar
                                        </button>
                                    </div>
                                    <div id="dni-help" class="form-text mt-1"></div>
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label fw-semibold">Nombre</label>
                                    <input type="text" name="boleta_nombre" id="boleta_nombre" 
                                        class="form-control form-control-solid">
                                </div>
                            </div>
                                <div id="factura-fields" class="row g-3" style="display:none;">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">RUC</label>
                                        <div class="input-group">
                                            <input type="text" name="factura_ruc" id="factura_ruc" class="form-control form-control-solid" maxlength="11" placeholder="12345678901">
                                            <button type="button" class="btn btn-outline-primary" id="btn-buscar-ruc">
                                                <i class="fas fa-search"></i> Buscar
                                            </button>
                                        </div>
                                        <div id="ruc-help" class="form-text mt-1"></div>
                                    </div>

                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold">Razón Social</label>
                                        <input type="text" name="factura_razon_social" id="factura_razon_social" class="form-control form-control-solid">
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold">Dirección</label>
                                        <input type="text" name="factura_direccion" id="factura_direccion" class="form-control form-control-solid">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email</label>
                                        <input type="email" name="factura_email" id="factura_email" class="form-control form-control-solid">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 3: ITEMS -->
                        <div class="card card-flush mb-4 border-0 border-top border-5 border-info">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-bold text-info">
                                    <i class="fas fa-shopping-cart me-2"></i>Items / Servicios
                                </h6>
                            </div>
                            <div class="card-body pt-4 pb-4">
                                <div id="items-container">
                                    <p class="text-muted mb-0" id="no-items-placeholder">No hay items agregados. Usa los botones de abajo para agregar un servicio, producto o examen.</p>
                                </div>
                                <div class="d-flex gap-2 flex-wrap mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-info" id="btn-add-item">
                                        <i class="fas fa-plus me-1"></i>Agregar Item
                                    </button>
                                </div>
                            </div>
                        </div>

                       <!-- SECCIÓN 4: FORMA DE PAGO -->
                        <div class="card card-flush mb-4 border-0 border-top border-5 border-info">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-bold text-info">
                                    <i class="fas fa-hand-holding-usd me-2"></i>Forma de Pago
                                </h6>
                            </div>
                            <div class="card-body pt-4 pb-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Forma de Pago</label>
                                        <select name="forma_pago" id="forma_pago" class="form-select form-select-solid">
                                            <option value="CONTADO">Contado</option>
                                            <option value="CREDITO">Crédito</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 5: PAGO HIBRIDO (solo CONTADO) -->
                        <div id="contado-section">
                            <div class="card card-flush mb-4 border-0 border-top border-5 border-info">
                                <div class="card-header bg-white border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-bold text-info">
                                            <i class="fas fa-wallet me-2"></i>Métodos de Pago (Híbrido)
                                        </h6>
                                        <button type="button" class="btn btn-sm btn-info" id="btn-add-payment-method">
                                            <i class="fas fa-plus me-1"></i>Agregar Método
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body pt-4 pb-4">
                                    <!-- Métodos de Pago -->
                                    <div id="payment-methods-container">
                                        <!-- Se agregarán dinámicamente -->
                                    </div>
                                    <!-- Input oculto para guardar métodos -->
                                    <input type="hidden" name="payment_methods_json" id="payment_methods_json" value="[]">
                                    <!-- Resumen de Pago -->
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="alert alert-info border-3 border-info mb-0" id="resumen-pago" style="background-color: #e7f3ff;">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="fw-bold fs-6">Total a pagar:</span><br>
                                                        <span class="fw-bold fs-6">Total recibido:</span>
                                                    </div>
                                                    <div class="text-end">
                                                        <h5 class="mb-0 text-success fw-bold" id="total-invoice">S/ 0.00</h5>
                                                        <h5 class="mb-0 fw-bold" id="total-received" style="color: #0dcaf0;">S/ 0.00</h5>
                                                    </div>
                                                </div>
                                                <div id="payment-alert" class="mt-2" style="display: none;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 5b: CUOTAS DE CRÉDITO (solo CREDITO) -->
                        <div id="credito-section" style="display:none;">
                            <div class="card card-flush mb-4 border-0 border-top border-5 border-primary">
                                <div class="card-header bg-white border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-bold text-primary">
                                            <i class="fas fa-calendar-alt me-2"></i>Cuotas de Crédito
                                        </h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <label class="form-label fw-semibold mb-0">N° Cuotas:</label>
                                            <input type="number" id="numero_cuotas" min="1" max="36" value="2"
                                                class="form-control form-control-solid" style="width: 90px;" onwheel="return false;">
                                            <button type="button" class="btn btn-sm btn-primary" id="btn-generar-cuotas">
                                                <i class="fas fa-magic me-1"></i>Generar Cuotas
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-4 pb-4">
                                    <p class="text-muted small mb-3">
                                        El sistema distribuye el total en partes iguales; puedes ajustar montos y fechas manualmente.
                                        La suma de todas las cuotas debe ser exactamente igual al total del comprobante.
                                    </p>
                                    <div id="cuotas-container">
                                        <!-- Se agregarán dinámicamente -->
                                    </div>
                                    <input type="hidden" name="cuotas_json" id="cuotas_json" value="[]">
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="alert alert-primary border-3 border-primary mb-0" id="resumen-cuotas">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold fs-6">Total a pagar:</span>
                                                    <h5 class="mb-0 text-success fw-bold" id="total-invoice-credito">S/ 0.00</h5>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <span class="fw-bold fs-6">Total en cuotas:</span>
                                                    <h5 class="mb-0 fw-bold" id="total-cuotas" style="color: #0d6efd;">S/ 0.00</h5>
                                                </div>
                                                <div id="cuotas-alert" class="mt-2" style="display: none;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- BOTONES DE ACCIÓN -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="d-flex gap-3 justify-content-center pb-3">
                                    <button type="submit" class="btn btn-info px-3" id="submitButton">
                                        <i class="fas fa-save me-2"></i>Guardar Comprobante
                                    </button>

                                    <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary px-3">
                                        <i class="fas fa-times-circle me-2"></i>Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card-flush {
    border-radius: 0.375rem;
}

.form-select-solid,
.form-control-solid {
    border: 1px solid #dee2e6;
    padding: 0.625rem 0.875rem;
    border-radius: 0.375rem;
    background-color: #fff;
}

.form-select-solid:focus,
.form-control-solid:focus {
    border-color: #0dcaf0;
    box-shadow: 0 0 0 0.2rem rgba(13, 202, 240, 0.25);
}

.list-group-item {
    border: none;
    border-bottom: 1px solid #e9ecef;
    padding: 0.75rem 1rem;
}

.list-group-item:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.item-row {
    border-radius: 0.375rem;
    border-left: 4px solid #0dcaf0;
    transition: all 0.3s ease;
    background-color: #fff;
}

.item-row:hover {
    box-shadow: 0 2px 8px rgba(13, 202, 240, 0.15);
}

.btn {
    font-weight: 600;
    border-radius: 0.375rem;
}

.btn-info {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
}

.btn-info:hover {
    background-color: #0ba5d4;
    border-color: #0ba5d4;
}

.card {
    border: none;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.card-header {
    background-color: #fff;
    border-bottom: 1px solid #e9ecef;
}

.border-top.border-5 {
    border-width: 4px !important;
}

.input-group-text {
    border: 1px solid #dee2e6;
    background-color: #f8f9fa;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
// ============================================================
// NUEVO — FORMA DE PAGO (CONTADO / CREDITO)
// ============================================================
const formaPagoSelect = document.getElementById('forma_pago');
const contadoSection = document.getElementById('contado-section');
const creditoSection = document.getElementById('credito-section');
const numeroCuotasInput = document.getElementById('numero_cuotas');
const btnGenerarCuotas = document.getElementById('btn-generar-cuotas');
const cuotasContainer = document.getElementById('cuotas-container');
const cuotasJsonInput = document.getElementById('cuotas_json');
const totalInvoiceCredito = document.getElementById('total-invoice-credito');
const totalCuotasEl = document.getElementById('total-cuotas');
const cuotasAlert = document.getElementById('cuotas-alert');

let cuotas = [];
let cuotaCounter = 0;

function toggleFormaPago() {
    const esCredito = formaPagoSelect.value === 'CREDITO';
    contadoSection.style.display = esCredito ? 'none' : 'block';
    creditoSection.style.display = esCredito ? 'block' : 'none';

    if (esCredito && cuotas.length === 0) {
        generarCuotas();
    }
    actualizarPago();
}

function formatearFechaLocal(fecha) {
    const y = fecha.getFullYear();
    const m = String(fecha.getMonth() + 1).padStart(2, '0');
    const d = String(fecha.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
}
function fechaMinimaCuota() {
    const fecha = new Date();
    fecha.setDate(fecha.getDate() + 1);
    return formatearFechaLocal(fecha);
}

function fechaVencimientoPorDefecto(numeroCuota) {
    const fecha = new Date();
    // numeroCuota - 1: la cuota 1 es HOY, la 2 es hoy+30, etc.
    fecha.setDate(fecha.getDate() + 1 + (30 * (numeroCuota - 1)));
    return formatearFechaLocal(fecha);
}
function generarCuotas() {
    const n = Math.max(1, parseInt(numeroCuotasInput.value || 1));
    const total = Math.round(calcularTotalFormulario() * 100) / 100;

    cuotas = [];
    cuotaCounter = 0;

    const montoBase = Math.floor((total / n) * 100) / 100;
    let acumulado = 0;

    for (let i = 1; i <= n; i++) {
        // La última cuota ajusta el residual de redondeo, para que la suma
        // siempre cuadre exactamente con el total del comprobante.
        const monto = (i === n)
            ? Math.round((total - acumulado) * 100) / 100
            : montoBase;

        acumulado = Math.round((acumulado + monto) * 100) / 100;

        cuotas.push({
            id: cuotaCounter++,
            numero: i,
            monto: monto,
            fecha_vencimiento: fechaVencimientoPorDefecto(i)
        });
    }

    renderCuotas();
    actualizarCuotas();
}

function removeCuota(id) {
    cuotas = cuotas.filter(c => c.id !== id);
    // Renumerar para mantener consistencia visual
    cuotas.forEach((c, idx) => c.numero = idx + 1);
    renderCuotas();
    actualizarCuotas();
}

function updateCuota(id, field, value) {
    const cuota = cuotas.find(c => c.id === id);
    if (cuota) {
        cuota[field] = field === 'monto' ? parseFloat(value || 0) : value;
        actualizarCuotas();
    }
}

function esHoy(fechaStr) {
    return fechaStr === formatearFechaLocal(new Date());
}

function renderCuotas() {
    cuotasContainer.innerHTML = '';
    if (cuotas.length === 0) {
        cuotasContainer.innerHTML = '<p class="text-muted">No hay cuotas generadas...</p>';
        return;
    }
    cuotas.forEach(c => {
        const requierePago = esHoy(c.fecha_vencimiento);
        const metodosOptions = ['EFECTIVO','YAPE','TARJETA','TRANSFERENCIA','PLIN','OTROS']
            .map(m => `<option value="${m}" ${c.metodo_pago === m ? 'selected' : ''}>${m}</option>`)
            .join('');

        const div = document.createElement('div');
        div.className = 'row g-3 mb-3 p-3 border rounded bg-light align-items-end';
        div.innerHTML = `
            <div class="col-md-2">
                <label class="form-label fw-semibold">Cuota N°</label>
                <input type="text" class="form-control form-control-solid" value="${c.numero}" disabled>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Monto</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">S/</span>
                    <input type="number" step="0.01" min="0.01" class="form-control form-control-solid monto-cuota-input"
                        id="monto-cuota-${c.id}" value="${c.monto.toFixed(2)}" inputmode="decimal" onwheel="return false;">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Fecha de Vencimiento</label>
                <input type="date" class="form-control form-control-solid fecha-cuota-input"
                    id="fecha-cuota-${c.id}" value="${c.fecha_vencimiento}" min="${fechaMinimaCuota()}">
            </div>
            <div class="col-md-3 metodo-pago-wrap" style="${requierePago ? '' : 'display:none;'}">
                <label class="form-label fw-semibold">
                    Método de Pago ${requierePago ? '<span class="text-danger">*</span>' : ''}
                </label>
                <select class="form-select form-select-solid metodo-cuota-input" id="metodo-cuota-${c.id}">
                    <option value="">-- Seleccionar --</option>
                    ${metodosOptions}
                </select>
                <small class="text-muted">Esta cuota vence hoy, se cobra al instante.</small>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm w-100" id="btn-remove-cuota-${c.id}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        cuotasContainer.appendChild(div);

        document.getElementById(`monto-cuota-${c.id}`).addEventListener('input', e => updateCuota(c.id, 'monto', e.target.value));
        document.getElementById(`fecha-cuota-${c.id}`).addEventListener('change', e => {
            updateCuota(c.id, 'fecha_vencimiento', e.target.value);
            renderCuotas(); // re-renderizar para mostrar/ocultar el select de método si cambia la fecha
        });
        const metodoSelect = document.getElementById(`metodo-cuota-${c.id}`);
        if (metodoSelect) {
            metodoSelect.addEventListener('change', e => updateCuota(c.id, 'metodo_pago', e.target.value));
        }
        document.getElementById(`btn-remove-cuota-${c.id}`).addEventListener('click', () => removeCuota(c.id));
    });
}

function actualizarCuotas() {
    const total = Math.round(calcularTotalFormulario() * 100) / 100;
    const totalCuotas = Math.round(cuotas.reduce((sum, c) => sum + parseFloat(c.monto || 0), 0) * 100) / 100;

    totalInvoiceCredito.textContent = `S/ ${total.toFixed(2)}`;
    totalCuotasEl.textContent = `S/ ${totalCuotas.toFixed(2)}`;

    const diferencia = Math.round((total - totalCuotas) * 100) / 100;
    cuotasAlert.style.display = diferencia !== 0 ? 'block' : 'none';

    if (diferencia > 0) {
        cuotasAlert.innerHTML = `<strong class="text-warning">⚠️ Falta S/ ${Math.abs(diferencia).toFixed(2)} por asignar en cuotas</strong>`;
    } else if (diferencia < 0) {
        cuotasAlert.innerHTML = `<strong class="text-warning">⚠️ Las cuotas suman S/ ${Math.abs(diferencia).toFixed(2)} de más</strong>`;
    } else {
        cuotasAlert.innerHTML = '<strong class="text-success">✓ La suma de cuotas coincide con el total a pagar</strong>';
    }

    cuotasJsonInput.value = JSON.stringify(cuotas.map(c => ({
        monto: c.monto,
        fecha_vencimiento: c.fecha_vencimiento,
        metodo_pago: c.metodo_pago || null

    })));
}

formaPagoSelect.addEventListener('change', toggleFormaPago);
btnGenerarCuotas.addEventListener('click', generarCuotas);
    // VERIFICAR QUE EL USUARIO ESTÉ AUTENTICADO
    const userIdField = document.getElementById('user_id_field');
    const userId = userIdField ? parseInt(userIdField.value) : 0;
    const currentUserBadge = document.getElementById('current-user');
    
    if (userId === 0 || !userId) {
        currentUserBadge.classList.remove('bg-info');
        currentUserBadge.classList.add('bg-danger');
        currentUserBadge.textContent = 'SIN AUTENTICAR';
        alert('⚠️ Debes estar autenticado para crear comprobantes');
        return;
    }
    // �🔗 URLs correctas con path base
    const urlBuscarPacientes = '<?= $this->Url->build('/pacientes/buscar-paciente') ?>';
    const urlBuscarProductos = '<?= $this->Url->build('/productos/buscar') ?>';
    const urlConsultarDni = '<?= $this->Url->build('/invoices/consultar-dni-api') ?>';
    const urlConsultarRuc = '<?= $this->Url->build('/invoices/consultar-ruc-api') ?>';
    
    const tipoDoc = document.getElementById('tipo_doc');
    const boletaFields = document.getElementById('boleta-fields');
    const facturaFields = document.getElementById('factura-fields');

    const itemsContainer = document.getElementById('items-container');
    const btnAddItem = document.getElementById('btn-add-item');

    const btnAddPaymentMethod = document.getElementById('btn-add-payment-method');
    const paymentMethodsContainer = document.getElementById('payment-methods-container');
    const paymentMethodsJson = document.getElementById('payment_methods_json');
    const resumenPago = document.getElementById('resumen-pago');
    const totalInvoice = document.getElementById('total-invoice');
    const totalReceived = document.getElementById('total-received');
    const paymentAlert = document.getElementById('payment-alert');
    
    let paymentMethods = [];
    let paymentMethodCounter = 0;

    const tratamientos = <?= json_encode(array_map(function ($t) {
        return [
            'id' => $t->id,
            'nombre' => $t->nombre,
            'costo' => (float)$t->costo,
            'codigo_producto' => 'TRAT',
            'unidad' => 'NIU',
        ];
    }, $tratamientos ?? []), JSON_UNESCAPED_UNICODE) ?>;

    const productos = <?= json_encode(array_map(function ($p) {
        return [
            'id' => $p->id,
            'nombre' => $p->nombre,
            'precio' => (float)$p->precio,
            'codigo' => $p->codigo ?: 'PROD',
            'unidad' => $p->unidad ?: 'NIU',
            'stock' => (float)$p->stock,
        ];
    }, $productos ?? []), JSON_UNESCAPED_UNICODE) ?>;

    const examenes = <?= json_encode(array_map(function ($ex) {
        return [
            'id' => $ex->id,
            'nombre' => $ex->nombre,
            'categoria' => $ex->categorias_examene->nombre ?? '',
            'muestra' => $ex->muestra,
            'precio' => (float)$ex->precio,
            'codigo_producto' => 'EXAM',
            'unidad' => 'NIU',
        ];
    }, $examenes ?? []), JSON_UNESCAPED_UNICODE) ?>;

    let itemIndex = 0;

    function toggleTipoDocumento() {
        const val = tipoDoc.value;
        boletaFields.style.display  = (val === '03' || val === 'RI') ? 'flex' : 'none';
        facturaFields.style.display = val === '01' ? 'flex' : 'none';

         // RI también puede usar cliente (opcional)
        if (val === 'RI') {
            boletaFields.style.display = 'flex'; // o un bloque general
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.innerText = text ?? '';
        return div.innerHTML;
    }

    function buildTratamientoOptions() {
        let html = '<option value="">Seleccione</option>';
        tratamientos.forEach(t => {
            html += `<option value="${t.id}" data-nombre="${escapeHtml(t.nombre)}" data-precio="${t.costo}" data-codigo="${escapeHtml(t.codigo_producto)}" data-unidad="${escapeHtml(t.unidad)}">${escapeHtml(t.nombre)} - S/ ${Number(t.costo).toFixed(2)}</option>`;
        });
        return html;
    }

    function buildProductoOptions() {
        let html = '<option value="">Seleccione</option>';
        productos.forEach(p => {
            html += `<option value="${p.id}" data-nombre="${escapeHtml(p.nombre)}" data-precio="${p.precio}" data-codigo="${escapeHtml(p.codigo)}" data-unidad="${escapeHtml(p.unidad)}" data-stock="${p.stock}">${escapeHtml(p.nombre)} - S/ ${Number(p.precio).toFixed(2)} (Stock: ${Number(p.stock).toFixed(2)})</option>`;
        });
        return html;
    }

    function calcularTotalFormulario() {
    let total = 0;

    document.querySelectorAll('.item-row').forEach(row => {
        const cantidadInput  = row.querySelector('.cantidad-input');
        const precioInput    = row.querySelector('.precio-input');
        const descuentoInput = row.querySelector('.descuento-input');
        const descuentoTipo  = row.querySelector('.descuento-tipo-select');
        const helpEl         = row.querySelector('.descuento-help');

        const cantidad  = Number(cantidadInput?.value  || 0);
        const precio    = Number(precioInput?.value    || 0);
        const descuento = Number(descuentoInput?.value || 0);
        const tipo      = descuentoTipo?.value || 'porcentaje';

        // Items de presupuesto: el precio unitario ya representa el monto neto a
        // cobrar (el descuento fue pactado una sola vez en el presupuesto). Si aquí
        // se editan cantidad/precio para un pago parcial, no se debe restar el
        // descuento otra vez, o se aplicaría dos veces.
        const esDePresupuesto = row.dataset.desdePresupuesto === '1';

        let precioConDescuento = precio;
        let descuentoMonto     = 0;

        if (!esDePresupuesto && descuento > 0 && precio > 0) {
            if (tipo === 'porcentaje') {
                descuentoMonto     = precio * (descuento / 100);
                precioConDescuento = precio - descuentoMonto;
            } else {
                descuentoMonto     = descuento;
                precioConDescuento = precio - descuento;
            }
            precioConDescuento = Math.max(0, precioConDescuento);
        }

        const lineaTotal = cantidad * precioConDescuento;

        // ✅ Mostrar desglose visual claro
        if (helpEl) {
            if (esDePresupuesto && descuento > 0) {
                const tipoLabel = tipo === 'porcentaje' ? `${descuento}%` : `S/ ${descuento.toFixed(2)}`;
                helpEl.innerHTML = `
                    <span class="text-muted">Descuento pactado en presupuesto: −${tipoLabel} (ya aplicado)</span>
                    ${cantidad > 1 ? `<br><span class="text-primary">Línea: S/ ${lineaTotal.toFixed(2)}</span>` : ''}
                `;
            } else if (descuento > 0 && precio > 0) {
                const tipoLabel = tipo === 'porcentaje'
                    ? `${descuento}%`
                    : `S/ ${descuentoMonto.toFixed(2)}`;
                helpEl.innerHTML = `
                    <span class="text-danger text-decoration-line-through me-1">S/ ${precio.toFixed(2)}</span>
                    <span class="text-success fw-bold">→ S/ ${precioConDescuento.toFixed(2)}</span>
                    <span class="text-muted ms-1">(−${tipoLabel})</span>
                    ${cantidad > 1 ? `<br><span class="text-primary">Línea: S/ ${lineaTotal.toFixed(2)}</span>` : ''}
                `;
            } else {
                helpEl.innerHTML = '';
            }
        }

        total += lineaTotal;
    });

    return total;
}function actualizarPago() {
    const total = calcularTotalFormulario();
    let totalRecibido = 0;
    paymentMethods.forEach(pm => {
        totalRecibido += parseFloat(pm.monto || 0);
    });

    totalInvoice.textContent = `S/ ${total.toFixed(2)}`;
    totalReceived.textContent = `S/ ${totalRecibido.toFixed(2)}`;

    const diferencia = Math.round((total - totalRecibido) * 100) / 100;
    paymentAlert.style.display = diferencia !== 0 ? 'block' : 'none';

    if (diferencia > 0) {
        paymentAlert.innerHTML = `<strong class="text-warning">⚠️ Falta S/ ${Math.abs(diferencia).toFixed(2)} por cobrar</strong>`;
        paymentAlert.className = 'mt-2';
    } else if (diferencia < 0) {
        paymentAlert.innerHTML = `<strong class="text-warning">⚠️ Sobra S/ ${Math.abs(diferencia).toFixed(2)}</strong>`;
        paymentAlert.className = 'mt-2';
    } else {
        paymentAlert.innerHTML = '<strong class="text-success">✓ El monto recibido coincide con el total a pagar</strong>';
        paymentAlert.className = 'mt-2';
    }

    paymentMethodsJson.value = JSON.stringify(paymentMethods);

    // NUEVO: si estamos en modo crédito, mantener el resumen de cuotas actualizado
    if (formaPagoSelect && formaPagoSelect.value === 'CREDITO') {
        actualizarCuotas();
    }
}

    function getPaymentTotals() {
        const total = Math.round(calcularTotalFormulario() * 100) / 100;
        const totalRecibido = Math.round(paymentMethods.reduce((sum, pm) => sum + parseFloat(pm.monto || 0), 0) * 100) / 100;

        return { total, totalRecibido };
    }

    function addPaymentMethod() {
        const pm = {
            id: paymentMethodCounter++,
            metodo: '',
            monto: 0
        };

        paymentMethods.push(pm);
        renderPaymentMethods();
        actualizarPago();
    }

    function removePaymentMethod(id) {
        paymentMethods = paymentMethods.filter(pm => pm.id !== id);
        renderPaymentMethods();
        actualizarPago();
    }

    function updatePaymentMethod(id, field, value) {
        const pm = paymentMethods.find(p => p.id === id);
        if (pm) {
            pm[field] = field === 'monto' ? parseFloat(value || 0) : value;
            actualizarPago();
        }
    }

    function renderPaymentMethods() {
        paymentMethodsContainer.innerHTML = '';

        if (paymentMethods.length === 0) {
            paymentMethodsContainer.innerHTML = '<p class="text-muted">No hay métodos de pago agregados. Haz clic en "Agregar Método".</p>';
            return;
        }

        paymentMethods.forEach(pm => {
            const div = document.createElement('div');
            div.className = 'row g-3 mb-3 p-3 border rounded bg-light';
            
            const metodosOptions = [
                { value: 'EFECTIVO', label: '💵 Efectivo' },
                { value: 'YAPE', label: '📱 Yape' },
                { value: 'TARJETA', label: '💳 Tarjeta' },
                { value: 'TRANSFERENCIA', label: '🏦 Transferencia' },
                { value: 'PLIN', label: '🧾 Plin' },
                { value: 'OTROS', label: '📦 Otros' }
            ];
            
            const selectOptions = '<option value="">-- Seleccionar método --</option>' + 
                metodosOptions.map(m => `<option value="${m.value}" ${pm.metodo === m.value ? 'selected' : ''}>${m.label}</option>`).join('');
            
            div.innerHTML = `
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Método de Pago</label>
                    <select class="form-select form-select-solid" id="metodo-${pm.id}" autocomplete="off">
                        ${selectOptions}
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Monto</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">S/</span>
                        <input type="number" step="0.01" min="0" class="form-control form-control-solid monto-input" 
                            id="monto-${pm.id}" 
                            placeholder="0.00" 
                            value="${pm.monto.toFixed(2)}"
                            inputmode="decimal"
                            autocomplete="off" onwheel="return false;">
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger btn-sm w-100" id="btn-remove-${pm.id}">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
            `;

            paymentMethodsContainer.appendChild(div);

            // Event listeners
            const selectMethod = document.getElementById(`metodo-${pm.id}`);
            const inputMonto = document.getElementById(`monto-${pm.id}`);
            const btnRemove = document.getElementById(`btn-remove-${pm.id}`);

            selectMethod.value = pm.metodo;
            selectMethod.addEventListener('change', (e) => {
                updatePaymentMethod(pm.id, 'metodo', e.target.value);
            });
            inputMonto.addEventListener('input', (e) => {
                updatePaymentMethod(pm.id, 'monto', e.target.value);
            });
            inputMonto.addEventListener('change', (e) => {
                actualizarPago(); // Recalcular al salir del campo
            });
            btnRemove.addEventListener('click', () => removePaymentMethod(pm.id));
        });
    }

    function setTipoItemBadge(row, tipo) {
        const badge = tipoItemBadges[tipo] || tipoItemBadges.tratamiento;
        const badgeEl = row.querySelector('.tipo-item-badge');
        const badgeIcon = row.querySelector('.tipo-item-badge-icon');
        const badgeLabel = row.querySelector('.tipo-item-badge-label');
        row.querySelector('.tipo-item-select').value = tipo;
        if (badgeEl) {
            badgeEl.className = `badge bg-${badge.color} py-2 px-3 tipo-item-badge`;
        }
        if (badgeIcon) badgeIcon.className = `fas ${badge.icon} me-1 tipo-item-badge-icon`;
        if (badgeLabel) badgeLabel.textContent = badge.label;
    }

    function limpiarSeleccionItem(row) {
        row.querySelector('.tratamiento-id').value = '';
        row.querySelector('.producto-id').value = '';
        row.querySelector('.examen-id').value = '';
        row.querySelector('.descripcion-input').value = '';
        row.querySelector('.precio-input').value = '';
        row.querySelector('.codigo-input').value = '';
        row.querySelector('.unidad-input').value = 'NIU';
        const stockHelp = row.querySelector('.stock-help');
        if (stockHelp) stockHelp.innerHTML = '';
        delete row.dataset.stock;
        const cantidadInput = row.querySelector('.cantidad-input');
        if (cantidadInput) cantidadInput.removeAttribute('max');
    }

    const tipoItemBadges = {
        tratamiento: { label: 'Servicio', icon: 'fa-briefcase-medical', color: 'info' },
        producto:    { label: 'Producto', icon: 'fa-box',   color: 'primary' },
        examen:      { label: 'Examen',   icon: 'fa-flask', color: 'warning' },
    };

    function addItemRow(tipo, focusRow) {
        tipo = tipo === 'producto' || tipo === 'examen' ? tipo : 'tratamiento';
        focusRow = focusRow !== false;
        const badge = tipoItemBadges[tipo];
        const codigoPorTipo = { tratamiento: 'TRAT', producto: 'PROD', examen: 'EXAM' };
        const html = `
            <div class="card card-flush mb-3 item-row border-start border-4" data-index="${itemIndex}" style="border-start-color: #667eea !important;">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Tipo Item -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Tipo</label>
                            <div>
                                <span class="badge bg-${badge.color} py-2 px-3 tipo-item-badge"><i class="fas ${badge.icon} me-1 tipo-item-badge-icon"></i><span class="tipo-item-badge-label">${badge.label}</span></span>
                            </div>
                            <select name="items[${itemIndex}][tipo_item]" class="form-select form-select-solid tipo-item-select d-none">
                                <option value="tratamiento" ${tipo === 'tratamiento' ? 'selected' : ''}>Servicio</option>
                                <option value="producto" ${tipo === 'producto' ? 'selected' : ''}>Producto</option>
                                <option value="examen" ${tipo === 'examen' ? 'selected' : ''}>Examen</option>
                            </select>
                        </div>

                        <!-- Búsqueda unificada: tratamiento, producto o examen -->
                        <div class="col-md-6 item-search-wrap">
                            <label class="form-label fw-semibold">Buscar Servicio / Producto / Examen</label>
                            <input type="text" class="form-control form-control-solid search-item" placeholder="Escriba para buscar...">
                            <div class="list-group resultados-item mt-2" style="max-height: 250px; overflow-y: auto;"></div>
                            <input type="hidden" name="items[${itemIndex}][tratamiento_id]" class="tratamiento-id">
                            <input type="hidden" name="items[${itemIndex}][producto_id]" class="producto-id">
                            <input type="hidden" name="items[${itemIndex}][examen_id]" class="examen-id">
                            <div class="form-text stock-help mt-1"></div>
                        </div>

                        <!-- Descripción -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Descripción interna 
                                <small class="text-muted fw-normal">(nota para uso interno)</small>
                            </label>
                            <input type="text" name="items[${itemIndex}][descripcion]" class="form-control form-control-solid descripcion-input" placeholder="Describa el servicio o producto">
                        </div>

                        <!-- Cantidad -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Cantidad</label>
                            <input type="number" step="0.01" min="0.01" name="items[${itemIndex}][cantidad]" value="1" class="form-control form-control-solid cantidad-input" onwheel="return false;">
                        </div>

                        <!-- Precio Unitario -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Precio Unit.</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">S/</span>
                                <input type="number" step="0.01" min="0" 
                                    name="items[${itemIndex}][precio_unitario]" 
                                    class="form-control form-control-solid precio-input" 
                                    placeholder="0.00" 
                                    inputmode="decimal"
                                    onwheel="return false;">
                            </div>
                        </div>

                        <!-- Descuento -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Descuento</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0"
                                    name="items[${itemIndex}][descuento]"
                                    class="form-control form-control-solid descuento-input"
                                    placeholder="0"
                                    inputmode="decimal"
                                    onwheel="return false;">
                                <select class="form-select form-select-solid descuento-tipo-select" 
                                        name="items[${itemIndex}][descuento_tipo]" 
                                        style="max-width: 80px;">
                                    <option value="porcentaje">%</option>
                                    <option value="monto">S/</option>
                                </select>
                            </div>
                            <div class="form-text descuento-help text-muted mt-1"></div>
                        </div>

                        <!-- Código Producto -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Código</label>
                            <input type="text" name="items[${itemIndex}][codigo_producto]" value="${codigoPorTipo[tipo]}" class="form-control form-control-solid codigo-input" readonly>
                        </div>

                        <!-- Unidad -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Unidad</label>
                            <input type="text" name="items[${itemIndex}][unidad]" value="NIU" class="form-control form-control-solid unidad-input" readonly>
                        </div>

                        <!-- Botón Eliminar -->
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger btn-sm w-100 btn-remove-item">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const placeholder = document.getElementById('no-items-placeholder');
        if (placeholder) placeholder.remove();

        itemsContainer.insertAdjacentHTML('beforeend', html);
        const row = itemsContainer.lastElementChild;

        setTipoItemBadge(row, tipo);
        itemIndex++;

        // Llevar la vista al nuevo item y enfocar su buscador
        if (focusRow) {
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            const focusTarget = row.querySelector('.search-item');
            if (focusTarget) setTimeout(() => focusTarget.focus(), 300);
        }
        // attach stock-aware validation to cantidad input
        const cantidadInput = row.querySelector('.cantidad-input');
        const stockHelpEl = row.querySelector('.stock-help');
        if (cantidadInput) {
            cantidadInput.addEventListener('input', function (e) {
                const stock = parseFloat(row.dataset.stock || NaN);
                const val = parseFloat(e.target.value || 0);
                if (!isNaN(stock)) {
                    if (val > stock) {
                        e.target.classList.add('is-invalid');
                        if (stockHelpEl) stockHelpEl.innerHTML = `<span class="badge bg-danger">Cantidad (${val}) excede stock (${stock})</span>`;
                    } else {
                        e.target.classList.remove('is-invalid');
                        if (stockHelpEl) stockHelpEl.innerHTML = `<span class="text-${(stock <= 0 ? 'danger' : 'muted')}">Stock: ${stock}</span>`;
                    }
                }
                actualizarPago();
            });
        }

        actualizarPago();
    }

    itemsContainer.addEventListener('change', function (e) {
        const row = e.target.closest('.item-row');
        if (!row) return;

        if (e.target.classList.contains('descuento-tipo-select')) {
            actualizarPago();
            return;
        }
        
    });

    itemsContainer.addEventListener('input', async function (e) {

    // 🔎 BUSCADOR PRODUCTO
    if (e.target.classList.contains('search-item')) {

        const rawQuery = e.target.value.trim();
        const query = rawQuery.toLowerCase();
        const row = e.target.closest('.item-row');
        const resultsBox = row.querySelector('.resultados-item');
        const searchInput = e.target;

        if (query.length < 2) {
            resultsBox.innerHTML = '';
            return;
        }

        const tratamientosMatch = tratamientos
            .filter(t => t.nombre.toLowerCase().includes(query))
            .map(t => ({ tipo: 'tratamiento', id: t.id, nombre: t.nombre, precio: t.costo, extra: '' }));

        const examenesMatch = examenes
            .filter(ex => ex.nombre.toLowerCase().includes(query))
            .map(ex => ({ tipo: 'examen', id: ex.id, nombre: ex.nombre, precio: ex.precio, extra: ex.categoria || '' }));

        let productosMatch = [];
        try {
            const res = await fetch(urlBuscarProductos + '?q=' + encodeURIComponent(rawQuery));
            const data = await res.json();
            productosMatch = (data || []).map(p => ({ tipo: 'producto', id: p.id, nombre: p.nombre, precio: p.precio, extra: `Stock: ${p.stock}`, stock: p.stock }));
        } catch (err) {
            // si falla la búsqueda de productos, seguimos mostrando tratamientos/exámenes
        }

        // Si el usuario ya escribió algo distinto mientras esperábamos la respuesta, descartamos
        if (searchInput.value.trim() !== rawQuery) return;

        const resultados = [...tratamientosMatch, ...productosMatch, ...examenesMatch];

        resultsBox.innerHTML = '';

        if (resultados.length === 0) {
            resultsBox.innerHTML = '<div class="list-group-item text-muted">Sin resultados</div>';
            return;
        }

        resultados.forEach(r => {
            const badge = tipoItemBadges[r.tipo];
            const item = document.createElement('button');
            item.type = "button";
            item.className = "list-group-item list-group-item-action d-flex justify-content-between align-items-center";
            item.innerHTML = `
                <span><span class="badge bg-${badge.color} me-2"><i class="fas ${badge.icon}"></i> ${badge.label}</span>${escapeHtml(r.nombre)} ${r.extra ? `<small class="text-muted">(${escapeHtml(r.extra)})</small>` : ''}</span>
                <span class="fw-semibold">S/ ${Number(r.precio).toFixed(2)}</span>
            `;

            item.addEventListener('click', () => {
                limpiarSeleccionItem(row);
                setTipoItemBadge(row, r.tipo);

                searchInput.value = r.nombre;
                row.querySelector(`.${r.tipo}-id`).value = r.id;
                row.querySelector('.descripcion-input').value = r.nombre;
                row.querySelector('.precio-input').value = r.precio;
                row.querySelector('.codigo-input').value = { tratamiento: 'TRAT', producto: 'PROD', examen: 'EXAM' }[r.tipo];
                row.querySelector('.unidad-input').value = 'NIU';

                if (r.tipo === 'producto') {
                    const stockHelp = row.querySelector('.stock-help');
                    if (stockHelp) stockHelp.innerHTML = `<span class="text-${(r.stock <= 0 ? 'danger' : 'muted')}">Stock: ${r.stock}</span>`;
                    row.dataset.stock = Number(r.stock);
                    const cantidadInput = row.querySelector('.cantidad-input');
                    if (cantidadInput) {
                        // No fijar el atributo HTML "max" por debajo del "min" (0.01):
                        // con stock 0 el navegador bloquearía CUALQUIER cantidad, incluida 1,
                        // con el mensaje "el valor mínimo debe ser menor que el máximo".
                        // La validación de stock insuficiente ya se hace aparte (clase is-invalid + backend).
                        if (Number(r.stock) >= 0.01) {
                            cantidadInput.max = Number(r.stock);
                        } else {
                            cantidadInput.removeAttribute('max');
                        }
                        const val = parseFloat(cantidadInput.value || 0);
                        if (!isNaN(val) && val > Number(r.stock)) {
                            cantidadInput.classList.add('is-invalid');
                            if (stockHelp) stockHelp.innerHTML = `<span class="badge bg-danger">Cantidad (${val}) excede stock (${r.stock})</span>`;
                        } else {
                            cantidadInput.classList.remove('is-invalid');
                        }
                    }
                }

                resultsBox.innerHTML = '';
                actualizarPago();
            });

            resultsBox.appendChild(item);
        });

        return;
    }

    // 💰 CALCULO NORMAL
    if (
        e.target.classList.contains('cantidad-input') ||
        e.target.classList.contains('precio-input') ||
        e.target.classList.contains('descuento-input')

    ) {
        actualizarPago();
    }
});

    itemsContainer.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-remove-item')) {
            e.target.closest('.item-row').remove();
            if (itemsContainer.querySelectorAll('.item-row').length === 0) {
                itemsContainer.innerHTML = '<p class="text-muted mb-0" id="no-items-placeholder">No hay items agregados. Usa los botones de abajo para agregar un servicio, producto o examen.</p>';
            }
            actualizarPago();
        }
    });

    btnAddPaymentMethod.addEventListener('click', addPaymentMethod);
    btnAddItem.addEventListener('click', () => addItemRow('tratamiento'));
    tipoDoc.addEventListener('change', toggleTipoDocumento);

    // Validación antes de enviar: actualizar JSON de métodos y permitir POST normal

    document.querySelector('form').addEventListener('submit', function(e) {
        // Actualizar el JSON con los valores actuales del formulario
        paymentMethods.forEach(pm => {
            const selectEl = document.getElementById(`metodo-${pm.id}`);
            const inputEl = document.getElementById(`monto-${pm.id}`);

            if (selectEl) pm.metodo = selectEl.value;
            if (inputEl) pm.monto = parseFloat(inputEl.value || 0);
        });

        const jsonString = JSON.stringify(paymentMethods);
        paymentMethodsJson.value = jsonString;

        // Validar que haya al menos un item agregado
        if (itemsContainer.querySelectorAll('.item-row').length === 0) {
            alert('Debes agregar al menos un item (servicio, producto o examen).');
            e.preventDefault();
            return false;
        }

        // Validar cantidades vs stock antes de enviar
        let stockError = false;
        document.querySelectorAll('.item-row').forEach(row => {
            const stock = parseFloat(row.dataset.stock || NaN);
            const cantidadInput = row.querySelector('.cantidad-input');
            const stockHelp = row.querySelector('.stock-help');
            if (cantidadInput) {
                const val = parseFloat(cantidadInput.value || 0);
                if (!isNaN(stock) && val > stock) {
                    stockError = true;
                    cantidadInput.classList.add('is-invalid');
                    if (stockHelp) stockHelp.innerHTML = `<span class="badge bg-danger">Cantidad (${val}) excede stock (${stock})</span>`;
                }
            }
        
    });
    if (stockError) {
        alert('Hay cantidades que exceden el stock. Corrija antes de enviar.');
        e.preventDefault();
        return false;
    }

    const total = Math.round(calcularTotalFormulario() * 100) / 100;
    const esCredito = formaPagoSelect.value === 'CREDITO';

    if (esCredito) {
        // ============================================================
        // VALIDACIÓN — CRÉDITO
        // ============================================================
        if (cuotas.length === 0) {
            alert('Debes generar al menos una cuota para el pago al crédito.');
            e.preventDefault();
            return false;
        }

        for (const c of cuotas) {
            if (!c.monto || c.monto <= 0) {
                alert(`La cuota #${c.numero} debe tener un monto mayor a 0.`);
                e.preventDefault();
                return false;
            }
            if (!c.fecha_vencimiento) {
                alert(`La cuota #${c.numero} debe tener una fecha de vencimiento.`);
                e.preventDefault();
                return false;
            }
             if (esHoy(c.fecha_vencimiento) && !c.metodo_pago) {
                alert(`La cuota #${c.numero} vence hoy, debes indicar el método de pago.`);
                e.preventDefault();
                return false;
            }
        }

        const totalCuotas = Math.round(cuotas.reduce((sum, c) => sum + parseFloat(c.monto || 0), 0) * 100) / 100;
        if (totalCuotas !== total) {
            alert(`La suma de cuotas (S/ ${totalCuotas.toFixed(2)}) debe ser igual al total del comprobante (S/ ${total.toFixed(2)}).`);
            e.preventDefault();
            return false;
        }

        cuotasJsonInput.value = JSON.stringify(cuotas.map(c => ({
            monto: c.monto,
            fecha_vencimiento: c.fecha_vencimiento,
            metodo_pago: c.metodo_pago || null
        })));
        // No se valida payment_methods_json porque en crédito no aplica.
        paymentMethodsJson.value = '[]';

    } else {
        // ============================================================
        // VALIDACIÓN — CONTADO (igual que antes, sin cambios)
        // ============================================================
        paymentMethods.forEach(pm => {
            const selectEl = document.getElementById(`metodo-${pm.id}`);
            const inputEl = document.getElementById(`monto-${pm.id}`);
            if (selectEl) pm.metodo = selectEl.value;
            if (inputEl) pm.monto = parseFloat(inputEl.value || 0);
        });

        const metodosValidos = paymentMethods.filter(pm => pm.metodo && pm.monto > 0);
        paymentMethodsJson.value = JSON.stringify(metodosValidos);

        if (metodosValidos.length === 0) {
            alert('Debes agregar al menos un método de pago con monto válido.');
            e.preventDefault();
            return false;
        }

        const totalRecibido = Math.round(metodosValidos.reduce((sum, pm) => sum + parseFloat(pm.monto || 0), 0) * 100) / 100;
        if (total !== totalRecibido) {
            alert(`El monto recibido debe ser igual al total a pagar. Total: S/ ${total.toFixed(2)} | Recibido: S/ ${totalRecibido.toFixed(2)}`);
            e.preventDefault();
            return false;
        }
    }

});

    toggleTipoDocumento();
    // NO agregar automáticamente EFECTIVO - dejar que el usuario agregue métodos según necesite
    // addPaymentMethod();
    actualizarPago();
    toggleFormaPago();

    // Pre-seleccionar la primera empresa
    const companySelect = document.getElementById('company_id');
    if (companySelect && companySelect.options.length > 1) {
        companySelect.value = companySelect.options[1].value;
    }

    // Pre-seleccionar la primera caja abierta si no hay caja preseleccionada
    const cajaSelect = document.getElementById('caja_id');
    if (cajaSelect && cajaSelect.options.length > 1) {
        cajaSelect.value = cajaSelect.options[1].value;
    }

    // Restaurar datos previos en caso de error al guardar
    const OLD_FORM = <?= json_encode($oldFormData ?? [], JSON_UNESCAPED_UNICODE) ?>;
    if (OLD_FORM && Object.keys(OLD_FORM).length > 0) {
        try {
            // Restaurar items
            if (Array.isArray(OLD_FORM.items) && OLD_FORM.items.length > 0) {
                itemsContainer.innerHTML = '';
                itemIndex = 0;
                OLD_FORM.items.forEach(it => {
                    addItemRow(it.tipo_item, false);
                    const row = itemsContainer.lastElementChild;
                    if (!row) return;
                    const descripcion = row.querySelector('.descripcion-input');
                    const cantidad = row.querySelector('.cantidad-input');
                    const precio = row.querySelector('.precio-input');
                    const productoId = row.querySelector('.producto-id');
                    const tratamientoId = row.querySelector('.tratamiento-id');
                    const examenId = row.querySelector('.examen-id');
                    const descuentoInput = row.querySelector('.descuento-input');
                    const descuentoTipoSelect = row.querySelector('.descuento-tipo-select');

                    if (descripcion) descripcion.value = it.descripcion ?? '';
                    if (cantidad) cantidad.value = it.cantidad ?? 1;
                    if (precio) precio.value = it.precio_unitario ?? it.precio_unitario ?? 0;
                    if (descuentoInput) descuentoInput.value = it.descuento ?? 0;
                    if (descuentoTipoSelect) descuentoTipoSelect.value = it.descuento_tipo ?? 'porcentaje';
                    if (tratamientoId && it.tratamiento_id) {
                        tratamientoId.value = it.tratamiento_id;
                        // Mostrar el nombre del tratamiento en el buscador visible
                        const trat = tratamientos.find(t => String(t.id) === String(it.tratamiento_id));
                        const searchItemTrat = row.querySelector('.search-item');
                        if (searchItemTrat) searchItemTrat.value = trat ? trat.nombre : (it.descripcion ?? '');
                    }

                    // Item proveniente de un presupuesto: el descuento ya fue pactado ahí,
                    // no debe poder modificarse desde la factura (para evitar incongruencias).
                    // Cantidad, precio y tratamiento sí quedan editables (pagos parciales, ajustes, etc.)
                    // y la fila se puede quitar si ese item no se factura ahora.
                    if (it.desde_presupuesto) {
                        row.dataset.desdePresupuesto = '1';
                        if (descuentoInput) descuentoInput.setAttribute('readonly', 'readonly');
                        if (descuentoTipoSelect) {
                            descuentoTipoSelect.setAttribute('disabled', 'disabled');
                            // Los <select> disabled no se envían en el submit: reflejar el valor con un hidden.
                            const hiddenTipo = document.createElement('input');
                            hiddenTipo.type = 'hidden';
                            hiddenTipo.name = descuentoTipoSelect.getAttribute('name');
                            hiddenTipo.value = it.descuento_tipo ?? 'porcentaje';
                            descuentoTipoSelect.insertAdjacentElement('afterend', hiddenTipo);
                        }
                        // Marca para el backend: el precio unitario ya es neto (descuento
                        // pactado en el presupuesto), no debe restarse de nuevo al guardar.
                        const hiddenDesdePresupuesto = document.createElement('input');
                        hiddenDesdePresupuesto.type = 'hidden';
                        hiddenDesdePresupuesto.name = `items[${row.dataset.index}][desde_presupuesto]`;
                        hiddenDesdePresupuesto.value = '1';
                        row.querySelector('.card-body')?.appendChild(hiddenDesdePresupuesto);
                    }
                    if (productoId && it.producto_id) {
                        productoId.value = it.producto_id;
                        // Mostrar stock si conocemos el producto en la lista `productos`
                        const prod = productos.find(p => String(p.id) === String(it.producto_id));
                        const stockHelpEl = row.querySelector('.stock-help');
                        const searchItemProd = row.querySelector('.search-item');
                        if (prod) {
                            if (searchItemProd) searchItemProd.value = prod.nombre;
                            // Si viene de presupuesto, el precio ya fue calculado (neto, con
                            // descuento o saldo pendiente): no sobreescribir con el precio de catálogo.
                            if (!it.desde_presupuesto && precio) precio.value = prod.precio ?? precio.value;
                            if (stockHelpEl) stockHelpEl.innerHTML = `<span class="text-${(prod.stock <= 0 ? 'danger' : 'muted')}">Stock: ${prod.stock}</span>`;
                            // set row stock and cantidad max
                            row.dataset.stock = Number(prod.stock);
                            const cantidadInputRest = row.querySelector('.cantidad-input');
                            if (cantidadInputRest) {
                                // Igual que al seleccionar producto: no fijar "max" por debajo
                                // del "min" (0.01), o el navegador bloquea cualquier cantidad con stock 0.
                                if (Number(prod.stock) >= 0.01) {
                                    cantidadInputRest.max = Number(prod.stock);
                                } else {
                                    cantidadInputRest.removeAttribute('max');
                                }
                                const val = parseFloat(cantidadInputRest.value || 0);
                                if (!isNaN(val) && val > Number(prod.stock)) {
                                    cantidadInputRest.classList.add('is-invalid');
                                    if (stockHelpEl) stockHelpEl.innerHTML = `<span class="badge bg-danger">Cantidad (${val}) excede stock (${prod.stock})</span>`;
                                }
                            }
                        }
                    }
                    if (examenId && it.examen_id) {
                        examenId.value = it.examen_id;
                        // Mostrar el nombre del examen en el buscador visible
                        const ex = examenes.find(e => String(e.id) === String(it.examen_id));
                        const searchItemExam = row.querySelector('.search-item');
                        if (searchItemExam) searchItemExam.value = ex ? ex.nombre : (it.descripcion ?? '');
                    }
                });
                actualizarPago();
            }

            // Restaurar métodos de pago
            if (OLD_FORM.payment_methods_json) {
                try {
                    const pm = JSON.parse(OLD_FORM.payment_methods_json);
                    paymentMethods = [];
                    paymentMethodCounter = 0;
                    pm.forEach(p => {
                        paymentMethods.push({ id: paymentMethodCounter++, metodo: p.metodo || '', monto: parseFloat(p.monto || 0) });
                    });
                    renderPaymentMethods();
                    actualizarPago();
                } catch (e) {
                    // ignore
                }
            }

            // Restaurar campos simples
            ['company_id','tipo_doc','doctor_id','boleta_dni','boleta_nombre','factura_ruc','factura_razon_social','factura_direccion','factura_email','paciente_id','buscarPaciente','caja_id','boleta_tipo_doc'].forEach(k => {
                if (OLD_FORM[k] !== undefined && OLD_FORM[k] !== null) {
                    const el = document.querySelector(`[name="${k}"]`) || document.getElementById(k);
                    if (el) el.value = OLD_FORM[k];
                }
            });
            toggleTipoDocumento();
        } catch (e) {
            // no-op
        }
    }

    // 🔎 BUSCADOR PACIENTE
    const inputPaciente = document.getElementById('buscarPaciente');
    const resultadosPacientes = document.getElementById('resultadosPacientes');
    const pacienteIdInput = document.getElementById('paciente_id');

    // Si viene paciente preseleccionado, prellenar el formulario
    <?php if (!empty($pacientePrelleno)): ?>
    const pacienteName = '<?= h($pacientePrelleno->nombre . ' ' . $pacientePrelleno->apellido) ?>';
    const pacienteId = <?= (int) $pacientePrelleno->id ?>;
    const pacienteDni = '<?= h($pacientePrelleno->historias_clinicas->dni ?? '') ?>';

    pacienteIdInput.value = pacienteId;
    inputPaciente.value = pacienteName;
    resultadosPacientes.innerHTML = '';

    // Prellenar DNI en boleta con detección automática de tipo
    const boletaDniField = document.getElementById('boleta_dni');
    const boletaNombreField = document.getElementById('boleta_nombre');
    const boletaTipoDocPrellenado = document.getElementById('boleta_tipo_doc');
    const boletaDocLabelPrellenado = document.getElementById('boleta_doc_label');
    const boletaFieldsPrellenado = document.getElementById('boleta-fields');
    const tipoDocField = document.getElementById('tipo_doc');

    // Asegurar que el tipo de documento esté en RI o 03 para mostrar campos
    const currentTipoPrelleno = tipoDocField?.value || '';
    if (currentTipoPrelleno !== 'RI' && currentTipoPrelleno !== '03') {
        tipoDocField.value = '03'; // Establecer a Boleta por defecto
        toggleTipoDocumento(); // Actualizar visibilidad de campos
    } else {
        boletaFieldsPrellenado.style.display = 'flex'; // Mostrar campos si ya está seleccionado RI o 03
    }

    // Llenar los campos con los datos del paciente
    if (pacienteDni) {
        boletaDniField.value = pacienteDni;
        boletaNombreField.value = pacienteName;

        // Auto-detectar tipo de documento
        if (pacienteDni.length > 8) {
            boletaTipoDocPrellenado.value = '4'; // Carnet Extranjería
            boletaDocLabelPrellenado.textContent = 'Carnet Extranjería';
            boletaDniField.maxLength = 12;
        } else {
            boletaTipoDocPrellenado.value = '1'; // DNI
            boletaDocLabelPrellenado.textContent = 'DNI';
            boletaDniField.maxLength = 8;
        }
    } else {
        // Si no hay DNI, solo llenar el nombre
        boletaNombreField.value = pacienteName;
        boletaTipoDocPrellenado.value = '1'; // DNI por defecto
        boletaDocLabelPrellenado.textContent = 'DNI';
        boletaDniField.maxLength = 8;
    }
    <?php endif; ?>

    inputPaciente.addEventListener('keyup', function() {
        let q = this.value.trim();

        if (q.length < 2) {
            resultadosPacientes.innerHTML = '';
            return;
        }

        fetch(urlBuscarPacientes + '?q=' + encodeURIComponent(q))
        .then(res => res.json())
        .then(data => {
            let html = '';

            if (!data || data.length === 0) {
                resultadosPacientes.innerHTML = '<div class="list-group-item">Sin resultados</div>';
                return;
            }

            data.forEach(p => {
                html += `
                    <a href="#" class="list-group-item list-group-item-action"
                       data-id="${p.id}"
                       data-nombre="${escapeHtml(p.nombre)}"
                       data-dni="${escapeHtml(p.dni || '')}">
                        ${escapeHtml(p.nombre)}
                    </a>
                `;
            });

            resultadosPacientes.innerHTML = html;
        })
        .catch(() => {});
    });

    resultadosPacientes.addEventListener('click', function(e) {
        e.preventDefault();

        const item = e.target.closest('a');
        if (!item) return;

        const pacienteId = item.dataset.id;
        const pacienteNombre = item.dataset.nombre;
        const pacienteDni = item.dataset.dni || '';

        document.getElementById('paciente_id').value = pacienteId;
        document.getElementById('buscarPaciente').value = pacienteNombre;

        // Auto-completar DNI y nombre en boleta
        const boletaDniField = document.getElementById('boleta_dni');
        const boletaNombreField = document.getElementById('boleta_nombre');
        const tipoDocField = document.getElementById('tipo_doc');
        const boletaTipoDoc = document.getElementById('boleta_tipo_doc');
        const boletaFields = document.getElementById('boleta-fields');
        const boletaDocLabel = document.getElementById('boleta_doc_label');

       // ✅ Corregido: solo auto-completar campos si el tipo lo permite, sin forzar cambio
        const currentTipo = tipoDocField?.value || '';
        if (currentTipo === 'RI' || currentTipo === '03') {
            boletaFields.style.display = 'flex';
            // Llenar DNI y nombre solo si es Boleta/RI
            if (boletaDniField && pacienteDni) {
                boletaDniField.value = pacienteDni;
                if (pacienteDni.length > 8) {
                    boletaTipoDoc.value = '4';
                    boletaDocLabel.textContent = 'Carnet Extranjería';
                    boletaDniField.maxLength = 12;
                } else {
                    boletaTipoDoc.value = '1';
                    boletaDocLabel.textContent = 'DNI';
                    boletaDniField.maxLength = 8;
                }
            }
            if (boletaNombreField) boletaNombreField.value = pacienteNombre;
        }
// Si es Factura (01), no hacer nada con boleta-fields
// El paciente_id ya se guardó arriba, eso es suficiente

        // Llenar los campos con los datos del paciente
        if (boletaDniField && pacienteDni) {
            boletaDniField.value = pacienteDni;

            // Si el DNI tiene más de 8 caracteres, es un Carnet de Extranjería
            if (pacienteDni.length > 8) {
                boletaTipoDoc.value = '4'; // 4 = Carnet Extranjería
                boletaDocLabel.textContent = 'Carnet Extranjería';
                boletaDniField.maxLength = 12;
            } else {
                // Si tiene 8 o menos caracteres, es un DNI
                boletaTipoDoc.value = '1'; // 1 = DNI
                boletaDocLabel.textContent = 'DNI';
                boletaDniField.maxLength = 8;
            }
        } else {
            // Si no hay DNI, resetear a DNI por defecto
            boletaTipoDoc.value = '1';
            boletaDocLabel.textContent = 'DNI';
            boletaDniField.maxLength = 8;
        }

        if (boletaNombreField) {
            boletaNombreField.value = pacienteNombre;
        }

        resultadosPacientes.innerHTML = '';
    });

    // 🔎 DNI y RUC
    const btnBuscarDni = document.getElementById('btn-buscar-dni');
    const btnBuscarRuc = document.getElementById('btn-buscar-ruc');

    const boletaDni = document.getElementById('boleta_dni');
    const boletaNombre = document.getElementById('boleta_nombre');
    const dniHelp = document.getElementById('dni-help');
    
    // ara canerte extarnejria:
    const boletaTipoDoc  = document.getElementById('boleta_tipo_doc');
    const boletaDocLabel = document.getElementById('boleta_doc_label');

    boletaTipoDoc.addEventListener('change', function () {
        const esCE = this.value === '4';
        boletaDocLabel.textContent = esCE ? 'Carnet Extranjería' : 'DNI';
        boletaDni.maxLength        = esCE ? 12 : 8;
        boletaDni.placeholder      = esCE ? '123456789' : '12345678';
        dniHelp.innerHTML          = '';
        // NO borrar el valor al cambiar de tipo de documento
    });
    const facturaRuc = document.getElementById('factura_ruc');
    const facturaRazonSocial = document.getElementById('factura_razon_social');
    const facturaDireccion = document.getElementById('factura_direccion');
    const rucHelp = document.getElementById('ruc-help');

    // REEMPLAZAR ESTE BLOQUE COMPLETO:
    if (btnBuscarDni) {
        btnBuscarDni.addEventListener('click', async function (e) {
            e.preventDefault();

            const boletaTipoDoc = document.getElementById('boleta_tipo_doc');
            const tipo = boletaTipoDoc ? boletaTipoDoc.value : '1';
            const doc  = (boletaDni.value || '').trim();

            const validDni = tipo === '1' && /^\d{8}$/.test(doc);
            const validCE  = tipo === '4' && /^[A-Za-z0-9]{9,12}$/.test(doc);


            if (!validDni && !validCE) {
                dniHelp.innerHTML = tipo === '4'
                    ? '<span class="text-danger">Carnet Extranjería debe tener entre 9 y 12 caracteres alfanuméricos.</span>'
                    : '<span class="text-danger">DNI debe tener exactamente 8 dígitos.</span>';
                return;
            }

            if (tipo === '4') {
                // CE: solo confirmar formato, no hay API
                dniHelp.innerHTML = '<span class="text-success">Formato válido. Ingrese el nombre manualmente.</span>';
                return;
            }

            // Solo DNI llega aquí
            dniHelp.innerHTML = '<span class="text-muted">Consultando DNI...</span>';
            try {
                const res  = await fetch(urlConsultarDni + '?dni=' + encodeURIComponent(doc), {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (!data.ok) {
                    dniHelp.innerHTML = `<span class="text-danger">${data.error || data.message || 'No se pudo consultar el DNI.'}</span>`;
                    return;
                }
                boletaNombre.value = data.nombre || '';
                dniHelp.innerHTML  = '<span class="text-success">DNI encontrado correctamente.</span>';
            } catch {
                dniHelp.innerHTML = '<span class="text-danger">Error al consultar DNI.</span>';
            }
        });
    }

    if (btnBuscarRuc) {
        btnBuscarRuc.addEventListener('click', async function (e) {
            e.preventDefault();

            const ruc = (facturaRuc.value || '').trim();

            if (!/^\d{11}$/.test(ruc)) {
                rucHelp.innerHTML = '<span class="text-danger">Ingrese un RUC válido de 11 dígitos.</span>';
                return;
            }

            rucHelp.innerHTML = '<span class="text-muted">Consultando RUC...</span>';

            try {
                const res = await fetch(urlConsultarRuc + '?ruc=' + encodeURIComponent(ruc), {
                    headers: { 'Accept': 'application/json' }
                });

                const data = await res.json();

                if (!data.ok) {
                    rucHelp.innerHTML = `<span class="text-danger">${data.error || data.message || 'No se pudo consultar el RUC.'}</span>`;
                    return;
                }

                facturaRazonSocial.value = data.razon_social || '';
                facturaDireccion.value = data.direccion || '';

                let extra = 'RUC encontrado correctamente.';
                if (data.estado || data.condicion) {
                    extra += ` Estado: ${data.estado || '-'} | Condición: ${data.condicion || '-'}`;
                }

                rucHelp.innerHTML = `<span class="text-success">${extra}</span>`;
            } catch (error) {
                rucHelp.innerHTML = '<span class="text-danger">Error al consultar RUC.</span>';
            }
        });
    }
});
// Aplica a todos los inputs type="number" de la página
document.querySelectorAll('input[type="number"]').forEach(input => {
    input.addEventListener('wheel', e => e.preventDefault());
});
// 🔒 Proteger campos de precio contra cambios accidentales con scroll/wheel y flechas
document.addEventListener('DOMContentLoaded', function() {
    // Deshabilitar scroll/wheel en inputs tipo number
    document.addEventListener('wheel', function(e) {
        if (document.activeElement && document.activeElement.classList.contains('precio-input')) {
            e.preventDefault();
        }
    }, { passive: false });

    // Deshabilitar cambios con flechas UP/DOWN en campos numéricos sensibles
    document.addEventListener('keydown', function(e) {
        if (document.activeElement) {
            const cls = document.activeElement.classList;
           if (cls.contains('precio-input') || cls.contains('cantidad-input') || 
                cls.contains('monto-input')  || cls.contains('descuento-input')) {
                if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                    e.preventDefault();
                }
            }
        }
    });

    // Event delegation para nuevos inputs agregados dinámicamente
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('precio-input')) {
            // Formatear a máximo 2 decimales
            let value = e.target.value;
            if (value && !isNaN(value)) {
                // Limitar a 2 decimales automáticamente
                const parts = value.split('.');
                if (parts.length > 1 && parts[1].length > 2) {
                    e.target.value = parseFloat(value).toFixed(2);
                }
            }
        }
    });
});
</script>

<script>
    $(document).ready(function() {
            // Asegurar que si el botón quedó deshabilitado tras un POST con errores,
            // se reactive al cargar la página para permitir correcciones.
            (function ensureSubmitEnabledOnLoad() {
                const btn = $("#submitButton");
                if (btn.length && btn.prop('disabled')) {
                    const original = btn.data('original-text') || 'Guardar Comprobante';
                    btn.prop('disabled', false).text(original);
                }
            })();
        // Al enviar guardamos el texto original y deshabilitamos el botón mientras se procesa
        $(document).on("submit", "form", function(event) {
            const form = $(this)[0];
            const btnGuardar = $(this).find("#submitButton");

            if (!form.checkValidity()) {
                // Evita el envío si hay errores en el formulario
                event.preventDefault();
                // Asegurarnos que el botón esté habilitado para permitir correcciones
                btnGuardar.prop("disabled", false);
                return;
            }

            // Guardar texto original para restaurarlo luego
            if (!btnGuardar.data('original-text')) {
                btnGuardar.data('original-text', btnGuardar.text());
            }

            btnGuardar.prop("disabled", true).text("Guardando...");
        });

        // Re-habilitar el botón cuando el usuario modifica cualquier campo del formulario
        $(document).on("input change", "form :input", function() {
            const btnGuardar = $(this).closest("form").find("#submitButton");
            if (!btnGuardar.length) return;
            if (btnGuardar.prop('disabled')) {
                const original = btnGuardar.data('original-text') || 'Guardar Comprobante';
                btnGuardar.prop('disabled', false).text(original);
            }
        });

        // Detecta cambios en inputs file específicamente (mantener compatibilidad previa)
        $(document).on("change", "input[type='file']", function() {
            const btnGuardar = $(this).closest("form").find("#submitButton");
            if (this.files.length > 0) {
                const original = btnGuardar.data('original-text') || 'Guardar Comprobante';
                btnGuardar.prop("disabled", false).text(original);
            }
        });
    });
</script>