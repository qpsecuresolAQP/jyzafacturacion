<?php

require 'vendor/autoload.php';

use Greenter\XMLSecLibs\Certificate\X509Certificate;
use Greenter\XMLSecLibs\Certificate\X509ContentType;

$pfx = file_get_contents(__DIR__ . '/webroot/certificados/certificadojyza.pfx');

// contraseña del certificado
$password = 'Sunat123';

$certificate = new X509Certificate($pfx, $password);

$pem = $certificate->export(X509ContentType::PEM);

file_put_contents(
    __DIR__ . '/webroot/certificados/certificadojyza.pem',
    $pem
);

echo "CERTIFICADO PEM GENERADO";