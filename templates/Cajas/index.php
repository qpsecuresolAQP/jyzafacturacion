<div class="container-fluid py-4">
    <!-- HEADER CON BOTONES -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0">
            <i class="fas fa-cash-register"></i> Gestión de Cajas
        </h3>

        <div class="d-flex flex-wrap gap-2">
            <a href="<?= $this->Url->build(['action' => 'abrir']) ?>" class="btn btn-info">
                <i class="fas fa-plus-circle"></i> Abrir Caja
            </a>
        </div>
    </div>

    <!-- TABLA DE CAJAS -->
    <div class="table-responsive card">
        <table class="table table-striped mb-0">
            <thead class="bg-info text-white">
                <tr>
                    <th>ID</th>
                    <th>Caja</th>
                    <th>Código</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                    <th class="text-end">Monto Inicial</th>
                    <th class="text-end">Recaudo Total</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($cajas)): ?>
                    <?php foreach ($cajas as $caja): ?>
                        <tr>
                            <td>
                                <span class="badge bg-secondary">#<?= h($caja->id) ?></span>
                            </td>

                            <td class="fw-semibold"><?= h($caja->nombre) ?></td>
                            <td><?= h($caja->codigo) ?></td>
                            <td><?= h($caja->user->name ?? $caja->user->username ?? ('Usuario #' . $caja->user_id)) ?></td>
                            <td><?= h($caja->fecha?->format('Y-m-d')) ?></td>

                            <td class="text-end">
                                S/ <?= number_format((float)$caja->monto_inicial, 2) ?>
                            </td>

                            <td class="text-end">
                                <strong class="text-success">
                                    S/ <?= number_format((float)($caja->recaudo_total ?? 0), 2) ?>
                                </strong>
                            </td>

                            <td class="text-center">
                                <?php
                                $color = 'secondary';

                                if ($caja->estado === 'ABIERTA') {
                                    $color = 'success';
                                } elseif ($caja->estado === 'PAUSADA') {
                                    $color = 'warning';
                                } elseif ($caja->estado === 'CERRADA') {
                                    $color = 'dark';
                                }
                                ?>
                                <span class="badge bg-<?= $color ?>">
                                    <?= h($caja->estado) ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <a href="<?= $this->Url->build(['action' => 'view', $caja->id]) ?>"
                                   class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            No hay cajas registradas.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .container-fluid.py-4 .card {
        border: none;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .container-fluid.py-4 .card-header {
        background-color: #fff;
        border-bottom: 1px solid #e9ecef;
    }

    .container-fluid.py-4 .btn {
        font-weight: 600;
        border-radius: 0.375rem;
    }

    .container-fluid.py-4 .btn-info {
        background-color: #0dcaf0;
        border-color: #0dcaf0;
    }

    .container-fluid.py-4 .btn-info:hover {
        background-color: #0ba5d4;
        border-color: #0ba5d4;
    }

    .container-fluid.py-4 .badge {
        border-radius: 999px;
        padding: 0.45rem 0.75rem;
        font-weight: 700;
    }

    .container-fluid.py-4 .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .container-fluid.py-4 .table .btn {
        box-shadow: none;
    }
</style>