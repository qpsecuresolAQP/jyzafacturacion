<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ExamenesFisico $examenesFisico
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Examenes Fisico'), ['action' => 'edit', $examenesFisico->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Examenes Fisico'), ['action' => 'delete', $examenesFisico->id], ['confirm' => __('Are you sure you want to delete # {0}?', $examenesFisico->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Examenes Fisicos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Examenes Fisico'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="examenesFisicos view content">
            <h3><?= h($examenesFisico->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($examenesFisico->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Edad') ?></th>
                    <td><?= $examenesFisico->edad === null ? '' : $this->Number->format($examenesFisico->edad) ?></td>
                </tr>
                <tr>
                    <th><?= __('Peso') ?></th>
                    <td><?= $examenesFisico->peso === null ? '' : $this->Number->format($examenesFisico->peso) ?></td>
                </tr>
                <tr>
                    <th><?= __('Altura') ?></th>
                    <td><?= $examenesFisico->altura === null ? '' : $this->Number->format($examenesFisico->altura) ?></td>
                </tr>
                <tr>
                    <th><?= __('Temperatura') ?></th>
                    <td><?= $examenesFisico->temperatura === null ? '' : $this->Number->format($examenesFisico->temperatura) ?></td>
                </tr>
                <tr>
                    <th><?= __('Eva') ?></th>
                    <td><?= $examenesFisico->eva === null ? '' : $this->Number->format($examenesFisico->eva) ?></td>
                </tr>
                <tr>
                    <th><?= __('Presion') ?></th>
                    <td><?= $examenesFisico->presion === null ? '' : $this->Number->format($examenesFisico->presion) ?></td>
                </tr>
                <tr>
                    <th><?= __('Frecuencia Cardiaca') ?></th>
                    <td><?= $examenesFisico->frecuencia_cardiaca === null ? '' : $this->Number->format($examenesFisico->frecuencia_cardiaca) ?></td>
                </tr>
                <tr>
                    <th><?= __('Saturacion') ?></th>
                    <td><?= $examenesFisico->saturacion === null ? '' : $this->Number->format($examenesFisico->saturacion) ?></td>
                </tr>
                <tr>
                    <th><?= __('Glicemina') ?></th>
                    <td><?= $examenesFisico->glicemina === null ? '' : $this->Number->format($examenesFisico->glicemina) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>