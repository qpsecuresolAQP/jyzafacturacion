<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Producto $producto
 * @var \App\Model\Entity\ProductoMovimiento $movimiento
 * @var iterable<\App\Model\Entity\ProductoLote> $lotes
 * @var array $motivosIngreso
 * @var array $motivosEgreso
 * @var int $diasAlerta
 */
$viejo = fn(string $campo, string $porDefecto = '') => h((string) ($this->request->getData($campo) ?? $porDefecto));
$tipoActual = (string) ($this->request->getData('tipo') ?? $this->request->getQuery('tipo') ?? 'ingreso');
// Los botones del historial llegan con tipo, lote y motivo ya elegidos (?tipo=egreso&lote=5&motivo=VENCIDO).
$loteElegido = (string) ($this->request->getData('producto_lote_id') ?? $this->request->getQuery('lote') ?? '');
$motivoElegido = (string) ($this->request->getData('motivo_codigo') ?? $this->request->getQuery('motivo') ?? '');
$unidad = $producto->unidad_nombre ?? 'unidad';
$lotes = collection($lotes)->toList();
?>
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <?= $this->Form->create($movimiento, ['class' => 'row g-3', 'id' => 'form-movimiento']) ?>

            <div class="col-12 mb-3">
                <h3 class="text-info"><i class="fas fa-boxes"></i> Registrar Movimiento de Stock</h3>
                <p class="text-muted mb-0">
                    Producto: <strong><?= h($producto->nombre) ?></strong>
                    &middot; Stock actual: <strong><?= $this->Number->format($producto->stock) ?> <?= h($unidad) ?>(s)</strong>
                    <?php if ($producto->requiere_vencimiento): ?>
                        &middot; <span class="badge bg-info text-dark">Controla vencimiento</span>
                    <?php endif; ?>
                </p>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label fw-semibold">¿Qué desea registrar?</label>
                <div class="btn-group w-100" role="group" id="grupo-tipo">
                    <input type="radio" class="btn-check" name="tipo" id="tipo-ingreso" value="ingreso" <?= $tipoActual === 'ingreso' ? 'checked' : '' ?>>
                    <label class="btn btn-outline-success" for="tipo-ingreso"><i class="fas fa-plus-circle"></i> Ingreso</label>
                    <input type="radio" class="btn-check" name="tipo" id="tipo-egreso" value="egreso" <?= $tipoActual === 'egreso' ? 'checked' : '' ?>>
                    <label class="btn btn-outline-danger" for="tipo-egreso"><i class="fas fa-minus-circle"></i> Egreso / Baja</label>
                    <input type="radio" class="btn-check" name="tipo" id="tipo-ajuste" value="ajuste" <?= $tipoActual === 'ajuste' ? 'checked' : '' ?>>
                    <label class="btn btn-outline-primary" for="tipo-ajuste"><i class="fas fa-clipboard-check"></i> Ajuste por conteo</label>
                </div>
            </div>

            <!-- ═══ INGRESO ═══ -->
            <div class="row g-3 m-0 p-0 seccion" data-tipo="ingreso">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cantidad (<?= h($unidad) ?>s)</label>
                    <input type="number" name="cantidad" step="0.01" min="0.01" class="form-control" value="<?= $viejo('cantidad') ?>" onwheel="return false;">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Motivo</label>
                    <select name="motivo_codigo" class="form-select">
                        <option value="">Seleccione...</option>
                        <?php foreach ($motivosIngreso as $codigo => $etiqueta): ?>
                            <option value="<?= h($codigo) ?>" <?= $motivoElegido === $codigo ? 'selected' : '' ?>><?= h($etiqueta) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Las compras a proveedor se registran en "Ingreso de mercadería".</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Lote <?= $producto->requiere_vencimiento ? '<span class="text-danger">*</span>' : '<span class="text-muted fw-normal">(opcional)</span>' ?>
                    </label>
                    <input type="text" name="lote" maxlength="50" class="form-control" placeholder="Ej: L-2045" value="<?= $viejo('lote') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Fecha de vencimiento <?= $producto->requiere_vencimiento ? '<span class="text-danger">*</span>' : '<span class="text-muted fw-normal">(opcional)</span>' ?>
                    </label>
                    <input type="date" name="fecha_vencimiento" id="in-vencimiento" class="form-control" value="<?= $viejo('fecha_vencimiento') ?>">
                    <small id="aviso-vencimiento"></small>
                </div>
                <div class="col-12">
                    <small class="text-muted">
                        Si ya existe un lote con ese número y la misma fecha, la cantidad se suma a ese lote.
                        No se puede ingresar mercadería ya vencida.
                    </small>
                </div>
            </div>

            <!-- ═══ EGRESO ═══ -->
            <div class="row g-3 m-0 p-0 seccion" data-tipo="egreso">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cantidad (<?= h($unidad) ?>s)</label>
                    <input type="number" name="cantidad" step="0.01" min="0.01" class="form-control" value="<?= $viejo('cantidad') ?>" onwheel="return false;">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Motivo</label>
                    <select name="motivo_codigo" id="motivo-egreso" class="form-select">
                        <option value="">Seleccione...</option>
                        <?php foreach ($motivosEgreso as $codigo => $etiqueta): ?>
                            <option value="<?= h($codigo) ?>" <?= $motivoElegido === $codigo ? 'selected' : '' ?>><?= h($etiqueta) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Lote de donde sale <span id="lote-egreso-oblig" class="text-danger" style="display:none;">*</span></label>
                    <select name="producto_lote_id" id="lote-egreso" class="form-select">
                        <option value="" data-auto="1">Automático: primero el que vence antes (sin lotes vencidos)</option>
                        <?php foreach ($lotes as $lote): ?>
                            <option value="<?= $lote->id ?>" data-vencido="<?= $lote->vencido ? 1 : 0 ?>" <?= $loteElegido === (string) $lote->id ? 'selected' : '' ?>><?= h($lote->descripcion) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted" id="ayuda-lote-egreso">Para dar de baja mercadería vencida elija el motivo "Producto vencido" y su lote.</small>
                </div>
            </div>

            <!-- ═══ AJUSTE ═══ -->
            <div class="row g-3 m-0 p-0 seccion" data-tipo="ajuste">
                <div class="col-12">
                    <div class="alert alert-info small mb-0">
                        Use el ajuste cuando al <strong>contar físicamente</strong> un lote la cantidad real no coincide con el sistema.
                        Se corrige ese lote conservando su número y vencimiento, y la diferencia queda registrada.
                    </div>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Lote que contó</label>
                    <select name="producto_lote_id" id="lote-ajuste" class="form-select">
                        <option value="">Seleccione el lote...</option>
                        <?php foreach ($lotes as $lote): ?>
                            <option value="<?= $lote->id ?>" data-stock="<?= h((string) $lote->stock) ?>" <?= $loteElegido === (string) $lote->id ? 'selected' : '' ?>><?= h($lote->descripcion) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cantidad contada</label>
                    <input type="number" name="cantidad_contada" id="cantidad-contada" step="0.01" min="0" class="form-control" value="<?= $viejo('cantidad_contada') ?>" onwheel="return false;">
                </div>
                <div class="col-12"><div id="resultado-ajuste" class="fw-semibold"></div></div>
            </div>

            <div class="col-12 mb-3">
                <label class="form-label fw-semibold">
                    Observación <span id="obs-oblig" class="text-danger" style="display:none;">(obligatoria con el motivo "Otro")</span>
                    <span id="obs-opc" class="text-muted fw-normal">(opcional)</span>
                </label>
                <textarea name="motivo" id="observacion" class="form-control" rows="2" maxlength="255" placeholder="Detalle: qué pasó, quién autorizó, número de guía..."><?= $viejo('motivo') ?></textarea>
            </div>

            <div class="col-12 text-center">
                <?= $this->Form->button(__('Registrar Movimiento'), ['class' => 'btn btn-info', 'id' => 'btn-registrar']) ?>
                <?= $this->Html->link(
                    __('Cancelar'),
                    ['controller' => 'CategoriasProductos', 'action' => 'view', $producto->categoria_producto_id],
                    ['class' => 'btn btn-secondary ms-2']
                ) ?>
            </div>

            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<script>
(function () {
    const form = document.getElementById('form-movimiento');
    const radios = form.querySelectorAll('input[name="tipo"]');
    const secciones = form.querySelectorAll('.seccion');
    const motivoEgreso = document.getElementById('motivo-egreso');
    const loteEgreso = document.getElementById('lote-egreso');
    const loteEgresoOblig = document.getElementById('lote-egreso-oblig');
    const ayudaLoteEgreso = document.getElementById('ayuda-lote-egreso');
    const loteAjuste = document.getElementById('lote-ajuste');
    const contada = document.getElementById('cantidad-contada');
    const resultadoAjuste = document.getElementById('resultado-ajuste');
    const vencimiento = document.getElementById('in-vencimiento');
    const avisoVencimiento = document.getElementById('aviso-vencimiento');
    const observacion = document.getElementById('observacion');
    const obsOblig = document.getElementById('obs-oblig');
    const obsOpc = document.getElementById('obs-opc');
    const DIAS_ALERTA = <?= (int) $diasAlerta ?>;
    const REQUIERE_VENCIMIENTO = <?= json_encode((bool) $producto->requiere_vencimiento) ?>;

    function tipoActual() {
        return form.querySelector('input[name="tipo"]:checked').value;
    }

    // Solo los campos de la sección elegida se envían y se validan.
    function mostrarSeccion() {
        const tipo = tipoActual();
        secciones.forEach(function (sec) {
            const activa = sec.dataset.tipo === tipo;
            sec.style.display = activa ? '' : 'none';
            sec.querySelectorAll('input, select').forEach(function (el) { el.disabled = !activa; });
        });
        actualizarMotivoEgreso();
        actualizarObservacion();
        actualizarAjuste();
        actualizarVencimiento();
    }

    // Con "Producto vencido" solo se ofrecen los lotes vencidos y el lote es obligatorio.
    function actualizarMotivoEgreso() {
        const vencido = motivoEgreso.value === 'VENCIDO';
        loteEgresoOblig.style.display = vencido ? '' : 'none';
        loteEgreso.required = vencido && tipoActual() === 'egreso';
        Array.from(loteEgreso.options).forEach(function (op) {
            if (op.dataset.auto) { op.hidden = vencido; return; }
            op.hidden = vencido && op.dataset.vencido !== '1';
        });
        if (vencido && loteEgreso.selectedOptions[0] && loteEgreso.selectedOptions[0].hidden) loteEgreso.value = '';
        ayudaLoteEgreso.textContent = vencido
            ? 'Solo se muestran los lotes vencidos con stock.'
            : 'Para dar de baja mercadería vencida elija el motivo "Producto vencido" y su lote.';
    }

    function actualizarObservacion() {
        const tipo = tipoActual();
        const motivo = tipo === 'ingreso'
            ? form.querySelector('.seccion[data-tipo="ingreso"] select[name="motivo_codigo"]').value
            : (tipo === 'egreso' ? motivoEgreso.value : '');
        const obligatoria = motivo === 'OTRO';
        obsOblig.style.display = obligatoria ? '' : 'none';
        obsOpc.style.display = obligatoria ? 'none' : '';
        observacion.required = obligatoria;
    }

    function actualizarAjuste() {
        const opt = loteAjuste.selectedOptions[0];
        if (!opt || !opt.dataset.stock || contada.value === '') {
            resultadoAjuste.textContent = '';
            return;
        }
        const sistema = parseFloat(opt.dataset.stock);
        const dif = Math.round((parseFloat(contada.value) - sistema) * 100) / 100;
        if (dif === 0) {
            resultadoAjuste.className = 'fw-semibold text-success';
            resultadoAjuste.textContent = 'Coincide con el sistema (' + sistema + '): no hay nada que ajustar.';
        } else {
            resultadoAjuste.className = 'fw-semibold ' + (dif > 0 ? 'text-warning' : 'text-danger');
            resultadoAjuste.textContent = 'Sistema: ' + sistema + ' → Contado: ' + contada.value + ' (' + (dif > 0 ? 'sobran ' : 'faltan ') + Math.abs(dif) + ')';
        }
    }

    // Vencimiento del ingreso: error si ya venció, advertencia si vence pronto.
    function actualizarVencimiento() {
        if (tipoActual() !== 'ingreso') { avisoVencimiento.textContent = ''; return; }
        vencimiento.required = REQUIERE_VENCIMIENTO;
        form.querySelector('input[name="lote"]').required = REQUIERE_VENCIMIENTO;
        if (!vencimiento.value) { avisoVencimiento.textContent = ''; return; }
        const hoy = new Date(); hoy.setHours(0, 0, 0, 0);
        const partes = vencimiento.value.split('-');
        const fecha = new Date(partes[0], partes[1] - 1, partes[2]);
        const dias = Math.round((fecha - hoy) / 86400000);
        if (dias < 0) {
            avisoVencimiento.className = 'text-danger';
            avisoVencimiento.textContent = 'Ya venció: no se puede ingresar mercadería vencida.';
        } else if (dias <= DIAS_ALERTA) {
            avisoVencimiento.className = 'text-warning';
            avisoVencimiento.textContent = 'Atención: vence en ' + dias + ' día(s).';
        } else {
            avisoVencimiento.textContent = '';
        }
    }

    radios.forEach(function (r) { r.addEventListener('change', mostrarSeccion); });
    motivoEgreso.addEventListener('change', function () { actualizarMotivoEgreso(); actualizarObservacion(); });
    form.querySelector('.seccion[data-tipo="ingreso"] select[name="motivo_codigo"]').addEventListener('change', actualizarObservacion);
    loteAjuste.addEventListener('change', actualizarAjuste);
    contada.addEventListener('input', actualizarAjuste);
    vencimiento.addEventListener('input', actualizarVencimiento);

    form.addEventListener('submit', function (e) {
        if (!form.checkValidity()) { return; }
        if (tipoActual() === 'ingreso' && vencimiento.value) {
            const partes = vencimiento.value.split('-');
            const hoy = new Date(); hoy.setHours(0, 0, 0, 0);
            if (new Date(partes[0], partes[1] - 1, partes[2]) < hoy) {
                e.preventDefault();
                alert('La fecha de vencimiento ya pasó: no se puede ingresar mercadería vencida.');
                return;
            }
        }
        document.getElementById('btn-registrar').disabled = true;
    });

    mostrarSeccion();
})();
</script>
