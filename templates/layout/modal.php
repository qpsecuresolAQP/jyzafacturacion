<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->fetch('title') ?></title>
    
    <!-- Incluye solo los CSS esenciales para el modal -->
    <?= $this->Html->css('sb-admin-2.min.css') ?>
    <?= $this->fetch('css') ?>
</head>
<body>
    <div class="container-fluid">
        <?= $this->fetch('content') ?> <!-- Solo el contenido específico de la vista -->
    </div>

    <!-- Solo los scripts necesarios -->
    <?= $this->Html->script('vendor/jquery/jquery.min.js') ?>
    <?= $this->Html->script('vendor/bootstrap/js/bootstrap.bundle.min.js') ?>
    <?= $this->fetch('script') ?>
</body>
</html>