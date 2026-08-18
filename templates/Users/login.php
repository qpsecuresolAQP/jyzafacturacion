<div class="login-form-container">
    <div class="form-header">
        <div class="user-icon">
            <i class="fas fa-users"></i>
        </div>
        <h2 class="form-title">Iniciar Sesión</h2>
    </div>

    <?= $this->Flash->render('auth') ?>
    <?= $this->Flash->render('error') ?>
    <?= $this->Flash->render() ?>
    <?= $this->Form->create(null, ['id' => 'loginForm']) ?>

    <div class="input-group-custom">
        <div class="input-icon">
            <i class="fas fa-user"></i>
        </div>
        <?= $this->Form->control('username', [
            'label' => false,
            'placeholder' => 'Usuario',
            'class' => 'form-control-custom',
            'required' => true
        ]) ?>
    </div>

    <div class="input-group-custom password-field">
        <div class="input-icon">
            <i class="fas fa-lock"></i>
        </div>
        <?= $this->Form->control('password', [
            'label' => false,
            'placeholder' => 'Contraseña',
            'class' => 'form-control-custom',
            'id' => 'passwordField',
            'required' => true,
        ]) ?>
        <button type="button" class="password-toggle" id="togglePassword">
            <i class="fas fa-eye" id="toggleIcon"></i>
        </button>
    </div>

    <?= $this->Form->button(__('Iniciar'), ['class' => 'btn-login']) ?>
    <?= $this->Form->end() ?>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const passwordField = document.getElementById("passwordField");
        const togglePassword = document.getElementById("togglePassword");
        const toggleIcon = document.getElementById("toggleIcon");
    
        togglePassword.addEventListener("click", function () {
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.remove("fa-eye");
                toggleIcon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.remove("fa-eye-slash");
                toggleIcon.classList.add("fa-eye");
            }
        });
    });
</script>