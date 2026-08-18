<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RecordatorioControl $recordatorioControl
 */
?>

<?php $this->assign('title', 'Recordatorio - Control'); ?>

<?php
$paciente = $recordatorioControl->recordatorio->paciente ?? null;
$nombrePaciente = $paciente ? trim($paciente->nombre . ' ' . $paciente->apellido) : 'Paciente no especificado';
$telefonoPaciente = !empty($paciente->telefono_celular) ? preg_replace('/[^0-9]/', '', (string)$paciente->telefono_celular) : '';
$mensajeWhatsApp = rawurlencode('Hola ' . $nombrePaciente . ', te recordamos tu control. ¿Te agendamos para confirmarlo?');
$urlWhatsApp = !empty($telefonoPaciente) ? 'https://wa.me/51' . $telefonoPaciente . '?text=' . $mensajeWhatsApp : '';
?>

<div class="container py-4">
    <div class="card shadow-sm border-0 mx-auto" style="max-width: 720px; border-top: 5px solid #138f6d; border-bottom: 5px solid #138f6d; background: linear-gradient(180deg, #fffdf9 0%, #f6fff9 100%);">
        <div class="card-body p-4 p-md-5 text-center">
            <div class="mb-4">
                <div style="font-weight: 800; letter-spacing: 0.08rem; font-size: 1.35rem; color: #0f6f5f;">VDENT</div>
            </div>

            <div class="mb-3" style="letter-spacing: 0.45rem; font-size: 0.78rem; color: #4b6f61;">RECORDATORIO</div>

            <p class="lead mb-4" style="font-size: 1.08rem; line-height: 1.7; color: #3f4c45;">
                <strong style="color: #0f6f5f;"><?= h($nombrePaciente) ?></strong>, ¡Tu control ha sido registrado exitosamente!
                Aquí están los detalles de tu próximo control.
            </p>

            <div class="mx-auto mb-4" style="max-width: 480px;">
                <div class="d-flex align-items-center p-3 rounded-lg" style="background: #e8f7ef; border: 1px solid #cfe9db; box-shadow: 0 4px 12px rgba(12, 102, 77, 0.08);">
                    <div class="d-flex align-items-center justify-content-center mr-3" style="width: 44px; height: 44px; border-radius: 999px; background: #d9f1e5; color: #118b62;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="text-left">
                        <div style="font-size: 0.72rem; letter-spacing: 0.08rem; color: #6a8a7a;">PRÓXIMO CONTROL SUGERIDO</div>
                        <div style="font-size: 1rem; font-weight: 700; color: #163c31;">
                            <?= h($recordatorioControl->proximo_control) ?: 'No especificado' ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4" style="width: 12px; height: 12px; background: #1aa57c; transform: rotate(45deg); margin: 0 auto;"></div>

            <div class="card border-0 mx-auto" style="max-width: 480px; background: #ffffff; border-radius: 18px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center text-left">
                        <div class="d-flex align-items-center justify-content-center mr-3" style="width: 42px; height: 42px; border-radius: 999px; background: #eaf7f3; color: #1aa57c;">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div>
                            <div style="font-weight: 700; color: #3d554c;">¿Te agendamos?</div>
                            <div style="font-size: 0.92rem; color: #7a8b84;">Responde por WhatsApp o agenda la cita manualmente.</div>
                        </div>
                    </div>

                    <div class="d-flex flex-column align-items-end" style="gap: 0.5rem;">
                        <?php if (!empty($urlWhatsApp)): ?>
                            <a href="<?= h($urlWhatsApp) ?>" target="_blank" class="btn btn-success btn-sm" style="border-radius: 999px; min-width: 145px;">
                                <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                            </a>
                        <?php endif; ?>
                        <?= $this->Form->postLink('Confirmar como citado', ['controller' => 'RecordatorioControles', 'action' => 'toggleEstado', $recordatorioControl->id], ['class' => 'btn btn-outline-info btn-sm', 'style' => 'border-radius: 999px; min-width: 145px;', 'confirm' => '¿Confirmas que el paciente respondió por WhatsApp?']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>