<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\HistoriaClinica $historia
 */
?>

<?php $this->assign('title', 'Recordatorio - Cumpleaños'); ?>

<style>
    body {
        background: linear-gradient(135deg, #fff8e7 0%, #fff0d9 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px 0;
        font-family: 'Segoe UI', system-ui, sans-serif;
    }

    .bday-outer {
        display: flex;
        justify-content: center;
        padding: 2rem 1rem;
        width: 100%;
    }

    .bday-card {
        position: relative;
        max-width: 460px;
        width: 100%;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    }

    .bg-image {
        position: absolute;
        inset: 0;
        background: url('<?= $this->Url->image("imagencumpleaños.jpg") ?>') no-repeat center center;
        background-size: cover;
        z-index: 0;
    }

    .bg-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.94) 0%, rgba(255,250,240,0.92) 100%);
        z-index: 1;
    }

    .stripe-top, .stripe-bot {
        position: absolute;
        left: 0; right: 0;
        height: 6px;
        background: linear-gradient(90deg, #0F6E56, #1D9E75, #5DCAA5, #1D9E75, #0F6E56);
        z-index: 3;
    }

    .stripe-top { top: 0; }
    .stripe-bot { bottom: 0; }

    .bday-inner {
        position: relative;
        z-index: 2;
        padding: 2.25rem 2rem 2rem;
        text-align: center;
    }

    .logo-wrap {
        margin-bottom: 1.25rem;
    }

    .logo-top {
        max-width: 130px;
        filter: drop-shadow(0 1px 3px rgba(0,0,0,0.08));
    }

    .title-script {
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 26px;
        color: #1D9E75;
        font-style: italic;
        margin-bottom: 0;
        line-height: 1.2;
    }

    .title-bold {
        font-size: 34px;
        font-weight: 700;
        color: #0F6E56;
        letter-spacing: 1px;
        margin: 0 0 0.5rem;
        line-height: 1.1;
    }

    .diamond-wrap {
        display: flex;
        justify-content: center;
        margin: 0.5rem 0 1rem;
    }

    .diamond {
        width: 8px;
        height: 8px;
        background: #1D9E75;
        transform: rotate(45deg);
        border-radius: 1px;
    }

    .greeting {
        font-size: 15px;
        color: #3a3a3a;
        margin-bottom: 0.6rem;
    }

    .greeting-name {
        font-weight: 700;
        color: #0F6E56;
    }

    .sub-msg {
        font-size: 14px;
        color: #5a6a60;
        line-height: 1.7;
        margin-bottom: 1.5rem;
    }

    .sub-msg span {
        color: #1D9E75;
        font-weight: 600;
    }

    .gift-box {
        display: flex;
        align-items: center;
        gap: 14px;
        border: 1px solid #d0ccc0;
        border-radius: 14px;
        padding: 1rem 1.25rem;
        background: rgba(255, 255, 255, 0.85);
        margin-bottom: 1.5rem;
        text-align: left;
    }

    .gift-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #E1F5EE;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 22px;
        color: #1D9E75;
    }

    .gift-title {
        font-size: 15px;
        font-weight: 700;
        color: #1a2e26;
        line-height: 1.4;
    }

    .gift-title span {
        color: #1D9E75;
        font-style: italic;
        font-weight: 600;
    }

    .gift-sub {
        font-size: 12px;
        color: #7a8a80;
        margin-top: 2px;
    }

    .features {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 1.5rem;
    }

    .feat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }

    .feat-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: 1px solid #d0ccc0;
        background: rgba(255, 255, 255, 0.85);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #1D9E75;
    }

    .feat-label {
        font-size: 10px;
        font-weight: 700;
        color: #5a6a60;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        text-align: center;
        line-height: 1.3;
    }

    .hr-deco {
        border: none;
        border-top: 1px solid rgba(29, 158, 117, 0.18);
        margin: 0 0 1rem;
    }

    .footer-msg {
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 14px;
        font-style: italic;
        color: #5a8a75;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    @media (max-width: 500px) {
        .bday-inner { padding: 1.75rem 1.25rem 1.5rem; }
        .title-bold { font-size: 28px; }
        .features { grid-template-columns: repeat(3, 1fr); gap: 6px; }
        .feat-label { font-size: 9px; }
    }
</style>

<?php
    $nombrePaciente = '';
    if (isset($historia->paciente) && !empty($historia->paciente)) {
        $paciente = $historia->paciente;
        $nombrePaciente = trim(($paciente->nombre ?? '') . ' ' . ($paciente->apellido ?? ''));
    }
?>

<div class="bday-outer">
    <div class="bday-card">
        <div class="bg-image"></div>
        <div class="bg-overlay"></div>
        <div class="stripe-top"></div>
        <div class="stripe-bot"></div>

        <div class="bday-inner">

            <!-- Logo -->
            <div class="logo-wrap">
                <?= $this->Html->image('logoJyza.webp', ['alt' => 'Consultorio Ginecológico JYZA', 'class' => 'logo-top']) ?>
            </div>

            <!-- Título -->
            <div class="title-script">
                Feliz <i class="fas fa-heart" style="font-size:18px; vertical-align:-2px; color:#1D9E75;"></i>
            </div>
            <div class="title-bold">CUMPLEAÑOS!</div>

            <!-- Diamante decorativo -->
            <div class="diamond-wrap"><div class="diamond"></div></div>

            <!-- Saludo -->
            <div class="greeting">
                Hola,
                <?php if (!empty($nombrePaciente)): ?>
                    <span class="greeting-name"><?= h($nombrePaciente) ?></span>
                <?php endif; ?>
                <i class="fas fa-heart" style="font-size:12px; color:#1D9E75;"></i>
            </div>

            <!-- Mensaje -->
            <div class="sub-msg">
                Hoy celebramos a una persona muy especial.<br>
                Te deseamos un día lleno de alegría, amor<br>
                y mucha <span>felicidad</span>.
            </div>

            <!-- Detalle especial -->
            <div class="gift-box">
                <div class="gift-icon">
                    <i class="fas fa-gift"></i>
                </div>
                <div>
                    <div class="gift-title">
                        Tenemos un detalle especial <span>para ti</span>
                        <i class="fas fa-heart" style="font-size:12px; vertical-align:-1px; color:#1D9E75;"></i>
                    </div>
                    <div class="gift-sub">Válido durante todo tu mes de cumpleaños.</div>
                </div>
            </div>

            <!-- Características -->
            <div class="features">
                <div class="feat-item">
                    <div class="feat-icon"><i class="fas fa-smile"></i></div>
                    <div class="feat-label">Cuidamos tu salud</div>
                </div>
                <div class="feat-item">
                    <div class="feat-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="feat-label">Tratamiento personalizado</div>
                </div>
                <div class="feat-item">
                    <div class="feat-icon"><i class="fas fa-heart"></i></div>
                    <div class="feat-label">Resultados que se notan</div>
                </div>
            </div>

            <hr class="hr-deco">

            <!-- Footer -->
            <div class="footer-msg">
                <i class="fas fa-heart" style="font-size:12px;"></i>
                Te esperamos para seguir cuidando tu salud
            </div>

        </div>
    </div>
</div>