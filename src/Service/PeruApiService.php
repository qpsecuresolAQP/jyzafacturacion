<?php
declare(strict_types=1);

namespace App\Service;

use Cake\Core\Configure;
use RuntimeException;

/**
 * Consulta de RUC (SUNAT) y DNI (RENIEC) a través de PeruApi (config PeruApi.key / PeruApi.baseUrl).
 */
class PeruApiService
{
    /**
     * @return array{ruc: string, razon_social: string, direccion: string, estado: string, condicion: string}
     */
    public function consultarRuc(string $ruc): array
    {
        if (!preg_match('/^\d{11}$/', $ruc)) {
            throw new RuntimeException('RUC inválido');
        }

        $json = $this->consultar('/ruc/' . $ruc);

        return [
            'ruc' => (string)($json['ruc'] ?? $ruc),
            'razon_social' => trim((string)($json['razon_social'] ?? '')),
            'direccion' => trim((string)($json['direccion'] ?? '')),
            'estado' => (string)($json['estado'] ?? ''),
            'condicion' => (string)($json['condicion'] ?? ''),
        ];
    }

    /**
     * @return array{dni: string, nombre: string}
     */
    public function consultarDni(string $dni): array
    {
        if (!preg_match('/^\d{8}$/', $dni)) {
            throw new RuntimeException('DNI inválido');
        }

        $json = $this->consultar('/dni/' . $dni);

        $nombre = trim((string)($json['cliente'] ?? ''));
        if ($nombre === '') {
            $nombre = trim(
                (string)($json['nombres'] ?? '') . ' ' .
                (string)($json['apellido_paterno'] ?? '') . ' ' .
                (string)($json['apellido_materno'] ?? '')
            );
        }

        return ['dni' => (string)($json['dni'] ?? $dni), 'nombre' => $nombre];
    }

    private function consultar(string $ruta): array
    {
        $apiKey = (string)Configure::read('PeruApi.key');
        $baseUrl = rtrim((string)Configure::read('PeruApi.baseUrl'), '/');

        if ($apiKey === '' || $baseUrl === '') {
            throw new RuntimeException('No se configuró PeruApi en app_local.php');
        }

        $ch = curl_init($baseUrl . $ruta . '?summary=0&plan=0');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'X-API-KEY: ' . $apiKey,
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT => 15,
        ]);

        $res = curl_exec($ch);
        if ($res === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('Error CURL: ' . $error);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $json = json_decode($res, true);
        if ($httpCode !== 200 || !is_array($json)) {
            throw new RuntimeException('Respuesta inválida de PeruApi (HTTP ' . $httpCode . ')');
        }

        return $json;
    }
}
