<?php
/**
 * @var \App\View\AppView $this
 * @var string $destino
 * @var string|null $error
 */
?>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold">
                    <i class="fas fa-lock"></i> Acceso a Caja
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Ingresa la clave de caja. Quedará habilitada hasta que cierres sesión.
                    </p>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger py-2"><?= h($error) ?></div>
                    <?php endif; ?>

                    <?= $this->Form->create(null, ['url' => ['action' => 'desbloquear', '?' => ['redirect' => $destino]]]) ?>
                        <div class="mb-3">
                            <label for="clave" class="form-label">Clave de caja</label>
                            <input type="password" name="clave" id="clave" class="form-control"
                                   required autofocus autocomplete="off">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-unlock"></i> Ingresar
                            </button>
                            <a href="<?= $this->Url->build(['controller' => 'Invoices', 'action' => 'index']) ?>"
                               class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
