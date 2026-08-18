<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FisicosHistoria $fisicosHistoria
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Fisicos Historia'), ['action' => 'edit', $fisicosHistoria->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Fisicos Historia'), ['action' => 'delete', $fisicosHistoria->id], ['confirm' => __('Are you sure you want to delete # {0}?', $fisicosHistoria->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Fisicos Historias'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Fisicos Historia'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="fisicosHistorias view content">
            <h3><?= h($fisicosHistoria->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Examen') ?></th>
                    <td><?= $fisicosHistoria->hasValue('examen') ? $this->Html->link($fisicosHistoria->examen->id, ['controller' => 'ExamenesFisicos', 'action' => 'view', $fisicosHistoria->examen->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Historia') ?></th>
                    <td><?= $fisicosHistoria->hasValue('historia') ? $this->Html->link($fisicosHistoria->historia->id, ['controller' => 'HistoriasClinicas', 'action' => 'view', $fisicosHistoria->historia->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($fisicosHistoria->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>