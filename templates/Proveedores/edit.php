<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Proveedor $proveedor
 */
?>
<div class="container mt-4 mb-4">
    <?= $this->Form->create($proveedor, ['class' => 'row g-3']) ?>

    <div class="col-12 mb-4">
        <h3 class="text-info"><i class="fas fa-truck"></i> Editar Proveedor</h3>
    </div>

    <div class="col-md-5 mb-3">
        <label class="form-label" for="ruc">RUC</label>
        <div class="input-group">
            <?= $this->Form->control('ruc', [
                'label' => false,
                'class' => 'form-control',
                'placeholder' => 'Ej: 20123456789',
                'maxlength' => 11,
                'templates' => ['inputContainer' => '{{content}}'],
            ]) ?>
            <div class="input-group-append">
                <button type="button" id="btn-buscar-ruc" class="btn btn-outline-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </div>
        <small id="ruc-help" class="form-text"></small>
    </div>

    <div class="col-md-7 mb-3">
        <?= $this->Form->control('nombre', [
            'label' => 'Nombre / Razón Social',
            'class' => 'form-control',
            'placeholder' => 'Ej: Distribuidora Dental SAC',
        ]) ?>
    </div>

    <div class="col-md-12 mb-3">
        <?= $this->Form->control('direccion', [
            'label' => 'Dirección',
            'class' => 'form-control',
            'placeholder' => 'Dirección fiscal del proveedor',
        ]) ?>
    </div>

    <div class="col-md-6 mb-3">
        <?= $this->Form->control('whatsapp', [
            'label' => 'WhatsApp',
            'class' => 'form-control',
            'placeholder' => 'Ej: 51987654321',
            'type'  => 'tel',
        ]) ?>
        <small class="text-muted">Incluye código de país, sin espacios ni símbolos.</small>
    </div>

    <div class="col-md-6 mb-3">
        <?= $this->Form->control('email', [
            'label' => 'Correo',
            'class' => 'form-control',
            'placeholder' => 'Ej: ventas@proveedor.com',
            'type'  => 'email',
        ]) ?>
    </div>

    <div class="col-md-12 mb-3">
        <?= $this->Form->control('activo', [
            'label' => 'Activo',
            'type'  => 'checkbox',
        ]) ?>
    </div>

    <div class="col-12 text-center">
        <?= $this->Form->button(__('Guardar Cambios'), ['class' => 'btn btn-info', 'id' => 'submitButton']) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index'], ['class' => 'btn btn-secondary ms-2']) ?>
    </div>

    <?= $this->Form->end() ?>
</div>

<script>
    (function () {
        const form = document.querySelector("form");
        const btnGuardar = document.getElementById("submitButton");

        if (form && btnGuardar) {
            form.addEventListener("submit", function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    return;
                }
                btnGuardar.disabled = true;
                btnGuardar.innerText = "Guardando...";
            });
        }

        // ── Buscar datos del proveedor por RUC (SUNAT vía PeruApi) ──
        const urlRuc = '<?= $this->Url->build('/invoices/consultar-ruc-api') ?>';
        const btnRuc = document.getElementById('btn-buscar-ruc');
        const inRuc = document.getElementById('ruc');
        const help = document.getElementById('ruc-help');
        const inNombre = document.getElementById('nombre');
        const inDireccion = document.getElementById('direccion');

        async function buscarRuc() {
            const ruc = (inRuc.value || '').trim();
            if (!/^\d{11}$/.test(ruc)) {
                help.className = 'form-text text-danger';
                help.textContent = 'El RUC debe tener 11 dígitos.';
                return;
            }
            help.className = 'form-text text-muted';
            help.textContent = 'Consultando...';
            btnRuc.disabled = true;
            try {
                const res = await fetch(urlRuc + '?ruc=' + encodeURIComponent(ruc));
                const data = await res.json();
                if (data.ok) {
                    if (data.razon_social) inNombre.value = data.razon_social;
                    if (data.direccion) inDireccion.value = data.direccion;
                    help.className = 'form-text text-success';
                    help.textContent = 'Datos cargados' + (data.estado ? ' · ' + data.estado : '') + (data.condicion ? ' · ' + data.condicion : '');
                } else {
                    help.className = 'form-text text-danger';
                    help.textContent = data.message || 'No se encontró el RUC.';
                }
            } catch (e) {
                help.className = 'form-text text-danger';
                help.textContent = 'Error al consultar el RUC.';
            } finally {
                btnRuc.disabled = false;
            }
        }

        if (btnRuc) {
            btnRuc.addEventListener('click', buscarRuc);
            inRuc.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') { e.preventDefault(); buscarRuc(); }
            });
        }
    })();
</script>
