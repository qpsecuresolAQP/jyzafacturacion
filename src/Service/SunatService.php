<?php
declare(strict_types=1);

namespace App\Service;

use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;

class SunatService
{
    private See $see;

    public function __construct(object $company)
    {
        $this->see = new See();

        $certPath = ROOT . DS . 'webroot' . DS . (string)$company->certificado_sunat;

        if (empty($company->certificado_sunat) || !file_exists($certPath)) {
            throw new \RuntimeException('No se encontró el certificado SUNAT en: ' . $certPath);
        }

        $certContent = file_get_contents($certPath);
        if ($certContent === false) {
            throw new \RuntimeException('No se pudo leer el certificado SUNAT.');
        }

        $this->see->setCertificate($certContent);

        if (($company->ambiente ?? 'beta') === 'beta') {
            $this->see->setService(SunatEndpoints::FE_BETA);
        } else {
            $this->see->setService(SunatEndpoints::FE_PRODUCCION);
        }

        if (empty($company->ruc) || empty($company->sol_user) || empty($company->sol_pass)) {
            throw new \RuntimeException('Faltan credenciales SOL en la empresa.');
        }

        $this->see->setClaveSOL(
            (string)$company->ruc,
            (string)$company->sol_user,
            (string)$company->sol_pass
        );
    }

    public function getSee(): See
    {
        return $this->see;
    }
}