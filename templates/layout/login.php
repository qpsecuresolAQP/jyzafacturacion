<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('Iniciar Sesión') ?></title>
    <link rel="icon" type="image/png" href="<?= $this->Url->image('jyzaicon.png') ?>">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            height: 100vh;
            overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('<?= $this->Url->image('drCloseFondo.png') ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.2);
            z-index: -1;
        }

        @media (max-width: 992px) {
            body {
                height: auto;
                overflow: auto;
            }
        }
        
        .login-container {
            display: flex;
            width: 100%;
            height: 100vh;
        }
        
        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
                height: auto;
            }
        }
        
        /* Panel Izquierdo - Logo centrado */
        .login-left {
            width: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 3rem;
            position: relative;
        }
        
        .logo-section {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }
        
        .logo-section img {
            max-width: 400px;
            width: 100%;
            height: auto;
            
        }
        .logo-img {
            opacity: 0.2; /* 80% de opacidad */
        }

        
        .tagline {
            position: relative;
            z-index: 1;
            color: #bfdcdf;
            font-size: 2.4rem;
            font-weight: 400;
            letter-spacing: 3px;
        }
        
        /* Panel Derecho - Formulario */
        .login-right {
            width: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem;
        }
        
        .login-form-container {
            width: 100%;
            max-width: 450px;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .user-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 10;
        }
        
        .user-icon i {
            font-size: 6rem;
            color: rgba(255, 255, 255, 0.25);
        }
        
        @media (min-width: 992px) {
            .user-icon i {
                font-size: 10rem;
            }
        }
        
        .form-title {
            color: rgba(255, 255, 255, 0.9);
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 0;
        }
        
        /* Inputs con iconos */
        .input-group-custom {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .input-icon {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 60px;
            background-color: #0c4a6e;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            border-radius: 4px 0 0 4px;
            z-index: 5;
        }
        
        .form-control-custom {
            width: 100%;
            padding: 1rem 1rem 1rem 75px;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            background-color: white;
            transition: all 0.3s ease;
        }
        
        .form-control-custom:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }
        
        .form-control-custom::placeholder {
            color: #cbd5e1;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }
        
        /* Checkbox y enlace */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .form-check-custom {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-check-custom input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .form-check-custom label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            margin: 0;
            cursor: pointer;
        }
        
        .forgot-password {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .forgot-password:hover {
            color: white;
        }
        
        /* Botón */
        .btn-login {
            width: 33%;
            margin: 0 auto;
            display: block;
            padding: 1rem;
            background-color: #16a34a;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background-color: #15803d;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.4);
        }
        
        /* Toggle Password Button */
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            font-size: 1.2rem;
            z-index: 10;
        }

        .password-toggle:hover {
            color: #0c4a6e;
        }

        .input-group-custom.password-field {
            position: relative;
        }
        
        /* Responsivo */
        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
                height: 100vh;
            }
            .login-left,
            .login-right {
                width: 100%;
                height: auto;
                min-height: auto;
                padding: 2rem 2rem;
            }
            
            .tagline {
                display: none;
            }

            .logo-section img {
                max-width: 250px;
            }
        }
        
        @media (max-width: 576px) {
            .login-left,
            .login-right {
                padding: 1.5rem;
            }
            
            .login-right {
                justify-content: flex-start;
            }
            
            .form-header {
                margin-bottom: 1.5rem;
            }
            
            .form-title {
                font-size: 1.3rem;
                margin-bottom: 1rem;
            }

            .tagline {
                font-size: 1.2rem;
            }
            
            .user-icon i {
                font-size: 4rem;
            }
            
            .input-group-custom {
                margin-bottom: 1rem;
            }
            
            .form-control-custom {
                padding: 0.7rem 0.7rem 0.7rem 60px;
                font-size: 0.85rem;
            }
            
            .input-icon {
                width: 50px;
                font-size: 1rem;
            }
            
            .btn-login {
                width: 50%;
                padding: 0.7rem;
                font-size: 0.9rem;
            }
            
            .form-control-custom::placeholder {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Panel Izquierdo con Logo centrado -->
        <div class="login-left">
            <div class="logo-section">
                <img src="<?= $this->Url->image('jyzaicon.png') ?>" alt="Consultorio Ginecológico JYZA">
            </div>
            <!-- <div class="tagline">
                QP Secure Solutions
            </div> -->
        </div>

        <!-- Panel Derecho con Formulario -->
        <div class="login-right">
            <?= $this->fetch('content') ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>