<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= h($this->fetch('title') ?: 'Recomendación de Micropigmentación') ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; margin: 30px; }
        h1 { font-size: 22px; margin-bottom: 20px; }
        strong { color: #333; }
        .footer { margin-top: 40px; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <?= $this->fetch('content') ?>
    <div class="footer">
        <hr>
        <p>Exportado el <?= date('d/m/Y H:i') ?></p>
    </div>
</body>
</html>
