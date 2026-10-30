<?php
/**
 * @var \App\View\AppView $this
 * @var bool $tieneClave
 * @var string|null $error
 */
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0">
            <i class="fas fa-key"></i> Clave de Caja
        </h3>
        <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="row">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <?php if ($tieneClave): ?>
                        <div class="alert alert-success py-2">
                            <i class="fas fa-lock"></i> La caja tiene clave. Los usuarios (excepto el administrador)
                            deben ingresarla una vez por sesión.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-secondary py-2">
                            <i class="fas fa-lock-open"></i> La caja no tiene clave: cualquier usuario puede entrar.
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger py-2"><?= h($error) ?></div>
                    <?php endif; ?>

                    <?= $this->Form->create(null) ?>
                        <div class="mb-3">
                            <label for="clave" class="form-label"><?= $tieneClave ? 'Nueva clave' : 'Clave' ?></label>
                            <input type="password" name="clave" id="clave" class="form-control"
                                   minlength="4" required autocomplete="new-password">
                        </div>
                        <div class="mb-3">
                            <label for="confirmacion" class="form-label">Confirmar clave</label>
                            <input type="password" name="confirmacion" id="confirmacion" class="form-control"
                                   minlength="4" required autocomplete="new-password">
                        </div>
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-save"></i> <?= $tieneClave ? 'Cambiar clave' : 'Guardar clave' ?>
                        </button>
                    <?= $this->Form->end() ?>

                    <?php if ($tieneClave): ?>
                        <hr>
                        <?= $this->Form->create(null) ?>
                            <input type="hidden" name="quitar" value="1">
                            <button type="submit" class="btn btn-outline-danger"
                                    onclick="return confirm('¿Quitar la clave de caja? Cualquier usuario podrá entrar sin clave.')">
                                <i class="fas fa-lock-open"></i> Quitar clave
                            </button>
                        <?= $this->Form->end() ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
