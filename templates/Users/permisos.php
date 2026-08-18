<?php
/**
 * Vista para gestionar permisos de un usuario
 * Muestra un formulario con todos los permisos disponibles agrupados por controlador
 */
?>

<style>
/* ── Layout ── */
.gp-wrap { padding: 1.5rem 0; }

/* ── Header card ── */
.gp-header-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 14px;
}
.gp-avatar {
    width: 48px; height: 48px;
    border-radius: 50%;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; font-weight: 600; color: #1d4ed8;
    flex-shrink: 0;
}
.gp-header-card h2 { margin: 0; font-size: 16px; font-weight: 600; color: #111827; }
.gp-header-card p  { margin: 2px 0 0; font-size: 13px; color: #6b7280; }
.gp-header-meta {
    margin-left: auto;
    display: flex; gap: 8px; flex-wrap: wrap; align-items: center;
}
.gp-chip {
    font-size: 12px; font-weight: 500;
    padding: 4px 12px; border-radius: 99px;
    border: 1px solid #e5e7eb;
    background: #f9fafb; color: #374151;
    display: inline-flex; align-items: center; gap: 5px;
}
.gp-chip svg { width: 13px; height: 13px; opacity: .6; }

/* ── Legend ── */
.gp-legend {
    background: #f8faff;
    border: 1px solid #dbeafe;
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 1.25rem;
    display: flex; gap: 10px; flex-wrap: wrap; align-items: center;
}
.gp-legend-title { font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .05em; margin-right: 4px; }
.badge-rol      { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
.badge-add      { background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; }
.badge-deny     { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
.badge-none     { background:#f3f4f6; color:#6b7280; border:1px solid #e5e7eb; }
.gp-badge {
    font-size: 11px; font-weight: 600;
    padding: 3px 9px; border-radius: 99px;
    display: inline-flex; align-items: center; gap: 4px;
}

/* ── Accordion ── */
.gp-accordion { display: flex; flex-direction: column; gap: 8px; margin-bottom: 1.5rem; }
.gp-section {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
    transition: box-shadow .15s;
}
.gp-section:focus-within { box-shadow: 0 0 0 3px #bfdbfe; }

.gp-section-header {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 18px;
    cursor: pointer;
    user-select: none;
    background: #fafafa;
    border-bottom: 1px solid transparent;
    transition: background .12s;
}
.gp-section-header:hover { background: #f3f4f6; }
.gp-section-header.open { background: #f0f7ff; border-bottom-color: #dbeafe; }

.gp-section-icon {
    width: 32px; height: 32px; border-radius: 8px;
    background: #eff6ff; border: 1px solid #bfdbfe;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.gp-section-icon svg { width: 16px; height: 16px; color: #1d4ed8; }
.gp-section-title { font-size: 14px; font-weight: 600; color: #111827; }
.gp-section-count {
    margin-left: auto;
    font-size: 11px; font-weight: 600;
    padding: 3px 9px; border-radius: 99px;
    background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;
}
.gp-chevron {
    width: 16px; height: 16px; color: #9ca3af;
    transition: transform .2s;
    flex-shrink: 0;
}
.gp-chevron.open { transform: rotate(90deg); }

.gp-section-body { display: none; }
.gp-section-body.open { display: block; }

/* ── Table ── */
.gp-table-wrap { overflow-x: auto; }
.gp-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.gp-table th {
    padding: 8px 16px;
    font-size: 11px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .05em;
    color: #9ca3af; text-align: left;
    border-bottom: 1px solid #f3f4f6;
    background: #fafafa;
}
.gp-table th:first-child { width: 50px; }
.gp-table th:nth-child(2) { width: 140px; }
.gp-table td {
    padding: 10px 16px;
    border-bottom: 1px solid #f9fafb;
    vertical-align: middle;
    color: #6b7280;
}
.gp-table tr:last-child td { border-bottom: none; }
.gp-table tr:hover td { background: #f9fafb; }
.gp-table code {
    font-size: 12px; font-family: 'SFMono-Regular', Consolas, monospace;
    background: #f3f4f6; color: #374151;
    padding: 2px 7px; border-radius: 5px; border: 1px solid #e5e7eb;
}

/* ── Checkbox ── */
.gp-check-wrap { display: flex; align-items: center; justify-content: center; }
.gp-check {
    width: 17px; height: 17px; cursor: pointer;
    accent-color: #1d4ed8;
    border-radius: 4px;
}

/* ── Buttons ── */
.gp-actions {
    display: flex; gap: 8px; align-items: center;
    padding-top: 4px;
}
.gp-btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 20px; font-size: 14px; font-weight: 500;
    border-radius: 8px; border: 1px solid; cursor: pointer;
    text-decoration: none; transition: background .12s, transform .1s;
    font-family: inherit;
}
.gp-btn:active { transform: scale(.98); }
.gp-btn-primary { background: #1d4ed8; color: #fff; border-color: #1d4ed8; }
.gp-btn-primary:hover { background: #1e40af; border-color: #1e40af; color: #fff; text-decoration: none; }
.gp-btn-secondary { background: #fff; color: #374151; border-color: #d1d5db; }
.gp-btn-secondary:hover { background: #f3f4f6; color: #111827; text-decoration: none; }
.gp-btn svg { width: 16px; height: 16px; }
</style>

<div class="gp-wrap">

    <!-- Header -->
    <div class="gp-header-card">
        <div class="gp-avatar">
            <?= strtoupper(substr(h($user->username), 0, 2)) ?>
        </div>
        <div>
            <h2>Permisos de <?= h($user->username) ?></h2>
            <p>Gestiona los accesos individuales para este usuario</p>
        </div>
        <div class="gp-header-meta">
            <span class="gp-chip">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                ID <?= $user->id ?>
            </span>
            <span class="gp-chip">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                Rol ID <?= $user->rol_id ?>
            </span>
        </div>
    </div>

    <!-- Legend -->
    <div class="gp-legend">
        <span class="gp-legend-title">Leyenda</span>
        <span class="gp-badge badge-rol">✓ Del Rol</span>
        <span style="font-size:12px;color:#6b7280;">Heredado del rol</span>
        <span class="gp-badge badge-add">✓ Adicional</span>
        <span style="font-size:12px;color:#6b7280;">Otorgado extra</span>
        <span class="gp-badge badge-deny">✗ Denegado</span>
        <span style="font-size:12px;color:#6b7280;">Negado explícitamente</span>
        <span class="gp-badge badge-none">Sin permiso</span>
    </div>

    <?= $this->Form->create(null, ['method' => 'POST', 'id' => 'form-permisos']) ?>

    <!-- Accordion por controlador -->
    <div class="gp-accordion" id="accordionPermisos">
        <?php foreach ($permisosPorControlador as $controller => $permisos): ?>
            <div class="gp-section">
                <div class="gp-section-header" onclick="toggleSection('<?= h($controller) ?>', this)">
                    <div class="gp-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 0 1-.657.643 48.39 48.39 0 0 1-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 0 1-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 0 0-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 0 1-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 0 0 .657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 0 1-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.401.604-.401.959v0c0 .333.277.599.61.58a48.1 48.1 0 0 0 5.427-.63 48.05 48.05 0 0 0 .582-4.717.532.532 0 0 0-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.959.401v0a.656.656 0 0 0 .658-.663 48.422 48.422 0 0 0-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 0 1-.61-.58v0Z"/></svg>
                    </div>
                    <span class="gp-section-title"><?= h($controller) ?></span>
                    <span class="gp-section-count"><?= count($permisos) ?> permisos</span>
                    <svg class="gp-chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </div>

                <div class="gp-section-body" id="body-<?= h($controller) ?>">
                    <div class="gp-table-wrap">
                        <table class="gp-table">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="gp-check-wrap" title="Seleccionar todos">
                                            <input type="checkbox"
                                                   class="gp-check select-all-controller"
                                                   data-controller="<?= h($controller) ?>">
                                        </div>
                                    </th>
                                    <th>Acción</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($permisos as $permiso): ?>
                                    <?php
                                        $isChecked = isset($permisosActualesMap[$permiso->id]) && $permisosActualesMap[$permiso->id];
                                        $estado    = $permisosActualesEstado[$permiso->id] ?? null;
                                        $enRol     = in_array($permiso->id, $permisosDelRol);
                                    ?>
                                    <tr class="permission-row-<?= h($controller) ?>">
                                        <td>
                                            <div class="gp-check-wrap">
                                                <input type="checkbox"
                                                       class="gp-check permission-check"
                                                       id="permiso_<?= $permiso->id ?>"
                                                       name="permiso_<?= $permiso->id ?>"
                                                       value="1"
                                                       data-controller="<?= h($controller) ?>"
                                                       <?= $isChecked ? 'checked' : '' ?>>
                                            </div>
                                        </td>
                                        <td>
                                            <code><?= h($permiso->action) ?></code>
                                        </td>
                                        <td>
                                            <span style="color:#111827;"><?= h($permiso->descripcion ?? 'Sin descripción') ?></span>
                                            <div style="margin-top:4px;">
                                                <?php if ($estado === 'rol'): ?>
                                                    <span class="gp-badge badge-rol">✓ Del Rol</span>
                                                <?php elseif ($estado === 'adicional'): ?>
                                                    <span class="gp-badge badge-add">✓ Adicional</span>
                                                <?php elseif ($estado === 'denegado'): ?>
                                                    <span class="gp-badge badge-deny">✗ Denegado</span>
                                                <?php else: ?>
                                                    <span class="gp-badge badge-none">Sin permiso</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Actions -->
    <div class="gp-actions">
        <button type="submit" class="gp-btn gp-btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3"/></svg>
            Guardar Permisos
        </button>
        <?= $this->Html->link(
            '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg> Volver',
            ['action' => 'index'],
            ['class' => 'gp-btn gp-btn-secondary', 'escape' => false]
        ) ?>
    </div>

    <?= $this->Form->end() ?>
</div>

<script>
function toggleSection(controller, header) {
    const body    = document.getElementById('body-' + controller);
    const chevron = header.querySelector('.gp-chevron');
    const isOpen  = body.classList.contains('open');
    body.classList.toggle('open', !isOpen);
    chevron.classList.toggle('open', !isOpen);
    header.classList.toggle('open', !isOpen);
}

document.addEventListener('DOMContentLoaded', function () {
    // Abrir primer acordeón por defecto
    const first = document.querySelector('.gp-section-header');
    if (first) first.click();

    // Seleccionar todos en un controlador
    document.querySelectorAll('.select-all-controller').forEach(cb => {
        cb.addEventListener('change', function () {
            document.querySelectorAll(
                `.permission-check[data-controller="${this.dataset.controller}"]`
            ).forEach(p => p.checked = this.checked);
        });
    });

    // Indeterminate en "seleccionar todo" al cambiar individuales
    document.querySelectorAll('.permission-check').forEach(cb => {
        cb.addEventListener('change', function () {
            const ctrl    = this.dataset.controller;
            const all     = document.querySelectorAll(`.permission-check[data-controller="${ctrl}"]`);
            const checked = Array.from(all).filter(c => c.checked).length;
            const master  = document.querySelector(`.select-all-controller[data-controller="${ctrl}"]`);
            master.checked       = checked === all.length;
            master.indeterminate = checked > 0 && checked < all.length;
        });
    });
});
</script>