<?php

require 'vendor/autoload.php';

use Greenter\XMLSecLibs\Certificate\X509Certificate;
use Greenter\XMLSecLibs\Certificate\X509ContentType;

$pfx = file_get_contents(__DIR__ . '/webroot/certificados/certificadoyuwer.pfx');

// contraseña del certificado
$password = 'QPdan98user';

$certificate = new X509Certificate($pfx, $password);

$pem = $certificate->export(X509ContentType::PEM);

file_put_contents(
    __DIR__ . '/webroot/certificados/certificadoyuwer.pem',
    $pem
);

echo "CERTIFICADO PEM GENERADO";