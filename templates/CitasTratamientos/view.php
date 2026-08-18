<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CitasTratamiento $citasTratamiento
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Citas Tratamiento'), ['action' => 'edit', $citasTratamiento->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Citas Tratamiento'), ['action' => 'delete', $citasTratamiento->id], ['confirm' => __('Are you sure you want to delete # {0}?', $citasTratamiento->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Citas Tratamientos'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Citas Tratamiento'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="citasTratamientos view content">
            <h3><?= h($citasTratamiento->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Tratamiento') ?></th>
                    <td><?= $citasTratamiento->hasValue('tratamiento') ? $this->Html->link($citasTratamiento->tratamiento->nombre, ['controller' => 'Tratamientos', 'action' => 'view', $citasTratamiento->tratamiento->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Cita') ?></th>
                    <td><?= $citasTratamiento->hasValue('cita') ? $this->Html->link($citasTratamiento->cita->id, ['controller' => 'Citas', 'action' => 'view', $citasTratamiento->cita->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($citasTratamiento->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($citasTratamiento->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($citasTratamiento->modified) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>