<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Doctore $doctore
 * @var array $tarifasFijas
 */
?>

<?php $this->assign('title', 'Detalle de Especialista'); ?>

<div class="doctores view content">

    <div class="container mt-4 mb-4">
        <div class="row justify-content-center">
            <div class="col-md-8 offset-md-2 row g-3">

                <!-- Título -->
                <div class="col-12 mb-4">
                    <h3 class="text-info">
                        <i class="fas fa-user-md"></i> <?= h($doctore->nombre . ' ' . $doctore->apellido) ?>
                    </h3>
                </div>

                <!-- Nombre -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Nombre</label>
                    <div class="form-control bg-light"><?= h($doctore->nombre) ?></div>
                </div>

                <!-- Apellido -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Apellido</label>
                    <div class="form-control bg-light"><?= h($doctore->apellido) ?></div>
                </div>

                <!-- Especialidad -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Especialidad</label>
                    <div class="form-control bg-light"><?= h($doctore->especialidad) ?></div>
                </div>

                <!-- Teléfono -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Teléfono</label>
                    <div class="form-control bg-light"><?= h($doctore->telefono) ?: '<em class="text-muted">No especificado</em>' ?></div>
                </div>

                <!-- Correo Electrónico -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Correo Electrónico</label>
                    <div class="form-control bg-light"><?= h($doctore->email) ?: '<em class="text-muted">No especificado</em>' ?></div>
                </div>

                <!-- Modo de Pago -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Modo de Pago</label>
                    <div class="form-control bg-light">
                        <?php if ($doctore->modo_pago === 'FIJO'): ?>
                            <span class="badge bg-secondary">Monto fijo por tratamiento</span>
                        <?php else: ?>
                            <span class="badge bg-info text-dark">Porcentaje del cobro</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($doctore->modo_pago === 'FIJO'): ?>
                    <!-- Tarifas fijas por tratamiento -->
                    <div class="col-md-12 mb-3">
                        <label class="fw-bold">Tarifas Fijas por Tratamiento</label>
                        <?php if (!empty($tarifasFijas)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Tratamiento</th>
                                            <th class="text-end">Tarifa Especial</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tarifasFijas as $tarifa): ?>
                                            <tr>
                                                <td><?= h($tarifa['tratamiento']) ?></td>
                                                <td class="text-end">S/ <?= number_format($tarifa['monto_fijo'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="form-control bg-light">
                                <em class="text-muted">Sin tarifas especiales — usa el default de cada tratamiento.</em>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <!-- Porcentaje de Pago -->
                    <div class="col-md-12 mb-3">
                        <label class="fw-bold">Porcentaje de Pago (%)</label>
                        <div class="form-control bg-light"><?= h($doctore->porcentaje_pago) ?>%</div>
                    </div>
                <?php endif; ?>

                <!-- Creado -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Creado</label>
                    <div class="form-control bg-light"><?= h($doctore->created) ?></div>
                </div>

                <!-- Última Modificación -->
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Última Modificación</label>
                    <div class="form-control bg-light"><?= h($doctore->modified) ?></div>
                </div>

            </div>
        </div>
    </div>

</div>