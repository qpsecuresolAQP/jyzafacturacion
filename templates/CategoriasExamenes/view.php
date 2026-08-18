<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CategoriasExamene $categoriasExamene
 */
?>
<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="mb-4 mt-3">
                <h3 class="text-info"><i class="fas fa-flask"></i> Detalle de Categoría de Examen</h3>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Nombre:</strong></div>
                <div class="col-md-9"><?= h($categoriasExamene->nombre) ?></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3"><strong>Estado:</strong></div>
                <div class="col-md-9">
                    <span class="badge bg-<?= $categoriasExamene->estado ? 'success' : 'secondary' ?>">
                        <?= $categoriasExamene->estado ? 'Activo' : 'Inactivo' ?>
                    </span>
                </div>
            </div>

            <?php if (!empty($categoriasExamene->examenes)): ?>
                <div class="mt-4">
                    <h5 class="text-info">Exámenes en esta categoría</h5>
                    <ul>
                        <?php foreach ($categoriasExamene->examenes as $examen): ?>
                            <li><?= h($examen->nombre) ?> — S/ <?= number_format((float) $examen->precio_convenio, 2) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <?= $this->Html->link(__('Volver a la Lista'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                <?= $this->Html->link(__('Editar'), ['action' => 'edit', $categoriasExamene->id], ['class' => 'btn btn-primary ms-2']) ?>
            </div>
        </div>
    </div>
</div>
