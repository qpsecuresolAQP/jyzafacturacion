<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\IngresosMercaderium $ingreso
 * @var array $proveedores
 */
$hoy = date('Y-m-d\TH:i');
?>

<?php $this->assign('title', 'Registrar Ingreso de Mercadería'); ?>

<style>
    #form-ingreso select.form-control { width: 100%; }
    #tabla-items thead th { font-size: .78rem; text-transform: uppercase; letter-spacing: .02em; white-space: nowrap; }
    #tabla-items td { vertical-align: middle; }
</style>

<div class="container-fluid mt-4 mb-4">

    <?php if (!$this->Permisos->tiene('IngresosMercaderia', 'add')): ?>
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-lock"></i> No tienes permisos para registrar ingresos de mercadería.
        </div>
    <?php else: ?>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h3 class="text-info mb-0"><i class="fas fa-truck-loading"></i> Registrar Ingreso de Mercadería</h3>
        <?= $this->Html->link('<i class="fas fa-list"></i> Ver Lista', ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-success']) ?>
    </div>

    <?= $this->Form->create($ingreso, ['id' => 'form-ingreso']) ?>

    <!-- ══════════ CABECERA ══════════ -->
    <div class="card mb-3">
        <div class="card-header"><strong><i class="fas fa-file-invoice-dollar"></i> Documento de compra</strong></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6 mb-2">
                    <label class="form-label fw-semibold">Proveedor</label>
                    <?= $this->Form->control('proveedor_id', [
                        'label' => false,
                        'options' => $proveedores,
                        'empty' => '-- Seleccione proveedor --',
                        'class' => 'form-control',
                        'required' => true,
                        'value' => $ingreso->proveedor_id,
                        'templates' => ['inputContainer' => '{{content}}'],
                    ]) ?>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label fw-semibold">Tipo de Documento</label>
                    <select name="tipo_doc" class="form-control">
                        <?php foreach (['FACTURA' => 'Factura', 'BOLETA' => 'Boleta', 'GUIA' => 'Guía', 'OTRO' => 'Otro'] as $v => $l): ?>
                            <option value="<?= $v ?>" <?= ($ingreso->tipo_doc ?? 'FACTURA') === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label fw-semibold">Moneda</label>
                    <select name="moneda" class="form-control">
                        <option value="SOLES" <?= ($ingreso->moneda ?? 'SOLES') === 'SOLES' ? 'selected' : '' ?>>Soles</option>
                        <option value="DOLARES" <?= ($ingreso->moneda ?? '') === 'DOLARES' ? 'selected' : '' ?>>Dólares</option>
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <label class="form-label fw-semibold">Serie</label>
                    <input type="text" name="serie" class="form-control" maxlength="20" value="<?= h($ingreso->serie) ?>" placeholder="F001">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label fw-semibold">Número</label>
                    <input type="text" name="numero" class="form-control" maxlength="30" value="<?= h($ingreso->numero) ?>" placeholder="0001234">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label fw-semibold">Almacén</label>
                    <input type="text" name="almacen" class="form-control" maxlength="100" value="<?= h($ingreso->almacen ?: 'Almacen Principal') ?>">
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label fw-semibold">Fecha de Ingreso</label>
                    <input type="datetime-local" name="fecha_ingreso" class="form-control" required
                           value="<?= $ingreso->fecha_ingreso ? $ingreso->fecha_ingreso->format('Y-m-d\TH:i') : $hoy ?>">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="form-label fw-semibold">Fecha de Factura <span class="text-muted fw-normal">(opcional)</span></label>
                    <input type="date" name="fecha_factura" class="form-control"
                           value="<?= $ingreso->fecha_factura ? $ingreso->fecha_factura->format('Y-m-d') : '' ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════ ITEMS ══════════ -->
    <div class="card mb-3">
        <div class="card-header"><strong><i class="fas fa-boxes"></i> Productos ingresados</strong></div>
        <div class="card-body">
            <div class="row g-2 align-items-end mb-3">
                <div class="col-md-4 position-relative">
                    <label class="form-label fw-semibold">Buscar producto</label>
                    <input type="text" id="buscar-producto" class="form-control" placeholder="Nombre o código..." autocomplete="off">
                    <input type="hidden" id="sel-producto-id">
                    <div id="resultados-producto" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1000; max-height: 260px; overflow-y: auto;"></div>
                    <small id="sel-producto-info" class="text-muted"></small>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Cantidad</label>
                    <input type="number" id="in-cantidad" class="form-control" step="0.01" min="0.01">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Precio Unit. c/IGV</label>
                    <input type="number" id="in-precio" class="form-control" step="0.0001" min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Lote <span class="text-muted fw-normal">(opc.)</span></label>
                    <input type="text" id="in-lote" class="form-control" maxlength="50">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Vencimiento <span class="text-muted fw-normal">(opc.)</span></label>
                    <input type="date" id="in-vencimiento" class="form-control">
                </div>
                <div class="col-12">
                    <button type="button" id="btn-add-item" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Agregar producto</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle" id="tabla-items">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 24%;">Producto</th>
                            <th style="width: 9%;">Cantidad</th>
                            <th style="width: 12%;">P.Unit. c/IGV</th>
                            <th style="width: 13%;">Subtotal (neto)</th>
                            <th style="width: 12%;">IGV (18%)</th>
                            <th style="width: 12%;">Total</th>
                            <th style="width: 12%;">Lote / Venc.</th>
                            <th style="width: 6%;"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        <tr id="fila-vacia"><td colspan="8" class="text-center text-muted py-3">Sin productos agregados.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ══════════ PIE ══════════ -->
    <div class="card mb-3">
        <div class="card-header"><strong><i class="fas fa-calculator"></i> Resumen</strong></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-7">
                    <label class="form-label fw-semibold">Observación <span class="text-muted fw-normal">(opcional)</span></label>
                    <textarea name="observacion" class="form-control" rows="3" placeholder="Alguna observación del ingreso..."><?= h($ingreso->observacion) ?></textarea>
                </div>
                <div class="col-md-5">
                    <table class="table table-sm mb-3">
                        <tr><td class="text-end fw-semibold">Subtotal</td><td class="text-end" id="tot-subtotal">0.00</td></tr>
                        <tr><td class="text-end fw-semibold">IGV</td><td class="text-end" id="tot-igv">0.00</td></tr>
                        <tr class="table-light"><td class="text-end fw-bold">Total</td><td class="text-end fw-bold" id="tot-total">0.00</td></tr>
                    </table>
                </div>
                <div class="col-12 text-center">
                    <button type="submit" id="btn-guardar" class="btn btn-info px-4"><i class="fas fa-save"></i> Guardar Ingreso</button>
                    <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary ms-2 px-4']) ?>
                </div>
            </div>
        </div>
    </div>

    <?= $this->Form->end() ?>

    <?php endif; ?>
</div>

<script>
(function () {
    const btnAddEl = document.getElementById('btn-add-item');
    if (!btnAddEl) { return; } // sin permiso, no hay formulario

    const IGV_PCT = 0.18;
    const urlBuscar = '<?= $this->Url->build(['action' => 'buscarProducto']) ?>';

    const inputBuscar = document.getElementById('buscar-producto');
    const resultados = document.getElementById('resultados-producto');
    const selId = document.getElementById('sel-producto-id');
    const selInfo = document.getElementById('sel-producto-info');
    const inCantidad = document.getElementById('in-cantidad');
    const inPrecio = document.getElementById('in-precio');
    const inLote = document.getElementById('in-lote');
    const inVenc = document.getElementById('in-vencimiento');
    const btnAdd = document.getElementById('btn-add-item');
    const itemsBody = document.getElementById('items-body');
    const filaVacia = document.getElementById('fila-vacia');
    const form = document.getElementById('form-ingreso');
    const btnGuardar = document.getElementById('btn-guardar');

    let selProducto = null;
    let itemIndex = 0;
    let timer = null;

    function money(n) {
        return (Math.round(n * 100) / 100).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    inputBuscar.addEventListener('input', function () {
        selProducto = null;
        selId.value = '';
        selInfo.textContent = '';
        clearTimeout(timer);
        const q = this.value.trim();
        timer = setTimeout(() => buscar(q), 250);
    });

    async function buscar(q) {
        try {
            const res = await fetch(urlBuscar + '?q=' + encodeURIComponent(q));
            const data = await res.json();
            resultados.innerHTML = '';
            if (!data.length) { resultados.innerHTML = '<div class="list-group-item text-muted small">Sin resultados</div>'; return; }
            data.forEach(p => {
                const a = document.createElement('button');
                a.type = 'button';
                a.className = 'list-group-item list-group-item-action';
                a.textContent = (p.codigo ? '[' + p.codigo + '] ' : '') + p.nombre + '  ·  stock: ' + p.stock;
                a.addEventListener('click', () => elegir(p));
                resultados.appendChild(a);
            });
        } catch (e) {
            resultados.innerHTML = '<div class="list-group-item text-danger small">Error al buscar</div>';
        }
    }

    function elegir(p) {
        selProducto = p;
        selId.value = p.id;
        inputBuscar.value = p.nombre;
        resultados.innerHTML = '';
        selInfo.textContent = 'Unidad: ' + p.unidad + ' · Stock actual: ' + p.stock;
        // precio_compra ya viene CON IGV, y el campo pide el precio con IGV.
        if (!inPrecio.value && p.precio_compra > 0) inPrecio.value = p.precio_compra;
        inCantidad.focus();
    }

    document.addEventListener('click', function (e) {
        if (!resultados.contains(e.target) && e.target !== inputBuscar) resultados.innerHTML = '';
    });

    btnAdd.addEventListener('click', function () {
        if (!selProducto) { alert('Selecciona un producto de la lista.'); return; }
        const cant = parseFloat(inCantidad.value || 0);
        const precioConIgv = parseFloat(inPrecio.value || 0);
        if (cant <= 0) { alert('Ingresa una cantidad mayor a 0.'); return; }
        if (precioConIgv < 0) { alert('El precio no puede ser negativo.'); return; }

        // El precio ingresado es CON IGV; se calcula el neto hacia atrás.
        const tot = Math.round(cant * precioConIgv * 100) / 100;
        const sub = Math.round(cant * (precioConIgv / (1 + IGV_PCT)) * 100) / 100;
        const igv = Math.round((tot - sub) * 100) / 100;
        const i = itemIndex++;

        const tr = document.createElement('tr');
        tr.dataset.sub = sub;
        tr.dataset.igv = igv;
        tr.dataset.tot = tot;
        tr.innerHTML = `
            <td>${escapeHtml(selProducto.nombre)}
                <input type="hidden" name="items[${i}][producto_id]" value="${selProducto.id}">
            </td>
            <td>${money(cant)}<input type="hidden" name="items[${i}][cantidad]" value="${cant}"></td>
            <td>${money(precioConIgv)}<input type="hidden" name="items[${i}][precio_unitario]" value="${precioConIgv}"></td>
            <td class="text-end">${money(sub)}</td>
            <td class="text-end">${money(igv)}</td>
            <td class="text-end">${money(tot)}</td>
            <td class="small">
                ${escapeHtml(inLote.value || '—')}<br>${inVenc.value || ''}
                <input type="hidden" name="items[${i}][lote]" value="${escapeHtml(inLote.value)}">
                <input type="hidden" name="items[${i}][fecha_vencimiento]" value="${inVenc.value}">
            </td>
            <td><button type="button" class="btn btn-danger btn-sm btn-quitar"><i class="fas fa-trash"></i></button></td>
        `;
        tr.querySelector('.btn-quitar').addEventListener('click', () => { tr.remove(); recalc(); });
        if (filaVacia) filaVacia.style.display = 'none';
        itemsBody.appendChild(tr);

        // Limpiar entrada
        selProducto = null; selId.value = '';
        inputBuscar.value = ''; inCantidad.value = ''; inPrecio.value = '';
        inLote.value = ''; inVenc.value = ''; selInfo.textContent = '';
        inputBuscar.focus();
        recalc();
    });

    function recalc() {
        let sub = 0, igv = 0, tot = 0;
        itemsBody.querySelectorAll('tr[data-sub]').forEach(tr => {
            sub += parseFloat(tr.dataset.sub);
            igv += parseFloat(tr.dataset.igv);
            tot += parseFloat(tr.dataset.tot);
        });
        document.getElementById('tot-subtotal').textContent = money(sub);
        document.getElementById('tot-igv').textContent = money(igv);
        document.getElementById('tot-total').textContent = money(tot);
        if (!itemsBody.querySelector('tr[data-sub]') && filaVacia) filaVacia.style.display = '';
    }

    function escapeHtml(s) {
        const d = document.createElement('div');
        d.textContent = s ?? '';
        return d.innerHTML;
    }

    form.addEventListener('submit', function (e) {
        if (!itemsBody.querySelector('tr[data-sub]')) {
            e.preventDefault();
            alert('Agrega al menos un producto al ingreso.');
            return;
        }
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    });

    window.addEventListener('pageshow', function () {
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = '<i class="fas fa-save"></i> Guardar Ingreso';
    });
})();
</script>
