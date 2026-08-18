<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Proveedor $proveedor
 */
?>
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="mb-4 mt-3">
                <h3 class="text-info"><i class="fas fa-truck"></i> Detalle del Proveedor</h3>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Nombre:</strong></div>
                <div class="col-md-9"><?= h($proveedor->nombre) ?></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>WhatsApp:</strong></div>
                <div class="col-md-9">
                    <?php if (!empty($proveedor->whatsapp)): ?>
                        <?= $this->Html->link(
                            '<i class="fab fa-whatsapp"></i> ' . h($proveedor->whatsapp),
                            'https://wa.me/' . preg_replace('/\D/', '', $proveedor->whatsapp),
                            ['escape' => false, 'target' => '_blank', 'class' => 'btn btn-success btn-sm']
                        ) ?>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Correo:</strong></div>
                <div class="col-md-9">
                    <?php if (!empty($proveedor->email)): ?>
                        <?= $this->Html->link(
                            '<i class="fas fa-envelope"></i> ' . h($proveedor->email),
                            'mailto:' . $proveedor->email,
                            ['escape' => false, 'class' => 'btn btn-outline-primary btn-sm']
                        ) ?>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Estado:</strong></div>
                <div class="col-md-9">
                    <span class="badge bg-<?= $proveedor->activo ? 'success' : 'secondary' ?>">
                        <?= $proveedor->activo ? 'Activo' : 'Inactivo' ?>
                    </span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Creado:</strong></div>
                <div class="col-md-9"><?= h($proveedor->created) ?></div>
            </div>

            <div class="text-center mt-4">
                <?= $this->Html->link(__('Volver a la Lista'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                <?= $this->Html->link(__('Editar'), ['action' => 'edit', $proveedor->id], ['class' => 'btn btn-primary ms-2']) ?>
            </div>
        </div>
    </div>
</div>
