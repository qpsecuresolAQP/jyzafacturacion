<?php
declare(strict_types=1);

namespace App\Service;

use Cake\ORM\Locator\LocatorAwareTrait;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company as GreCompany;
use Greenter\Model\Summary\Summary;
use Greenter\Model\Summary\SummaryDetail;

/**
 * Envío del Resumen Diario de boletas a SUNAT.
 *
 * Lo usan el botón manual (InvoicesController::enviarResumenDiario) y el envío
 * automático nocturno (bin/cake resumen_diario), para que ambos sigan las
 * mismas reglas.
 */
class ResumenDiarioService
{
    use LocatorAwareTrait;

    /** Máximo de boletas por resumen. */
    public const MAX_BOLETAS = 100;

    /**
     * Siguiente correlativo para un resumen (normal o de anulación) que se ENVÍA
     * en $fechaEnvio. SUNAT nombra el archivo con la fecha de envío
     * (RUC-RC-YYYYMMDD-N), así que el número no puede repetirse entre todos los
     * resúmenes enviados ese día, sean de la fecha de emisión que sean.
     */
    public function siguienteCorrelativo(int $companyId, string $fechaEnvio): int
    {
        $last = $this->fetchTable('DailySummaries')->find()
            ->select(['correlativo'])
            ->where([
                'company_id' => $companyId,
                'created >=' => $fechaEnvio . ' 00:00:00',
                'created <=' => $fechaEnvio . ' 23:59:59',
            ])
            ->orderBy(['correlativo' => 'DESC'])
            ->first();

        return $last ? ((int)$last->correlativo + 1) : 1;
    }

    /**
     * Fechas de emisión (Y-m-d, de la más antigua a la más reciente) que tienen
     * boletas pendientes de resumen.
     *
     * @return array<string>
     */
    public function fechasPendientes(int $companyId): array
    {
        $Invoices = $this->fetchTable('Invoices');
        $fila = $Invoices->find();
        $fechas = $this->boletasPendientes($companyId)
            ->select(['dia' => $fila->func()->date(['Invoices.created' => 'identifier'])])
            ->distinct(['dia'])
            ->orderBy(['dia' => 'ASC'], true)
            ->disableHydration()
            ->all()
            ->extract('dia')
            ->toList();

        return array_map(fn($d) => substr((string)$d, 0, 10), $fechas);
    }

    /**
     * Boletas que aún deben ir en un resumen: PENDIENTE_RESUMEN y sin resumen
     * ACEPTADO asociado (permite reintentar si un envío falló).
     */
    private function boletasPendientes(int $companyId): \Cake\ORM\Query\SelectQuery
    {
        return $this->fetchTable('Invoices')->find()
            ->where([
                'Invoices.company_id' => $companyId,
                'Invoices.tipo_doc' => '03',
                'Invoices.estado' => 'PENDIENTE_RESUMEN',
            ])
            ->where(function ($exp, $q) {
                return $exp->or([
                    $exp->isNull('Invoices.daily_summary_id'),
                    'Invoices.daily_summary_id IN' => $q->newExpr(
                        "(SELECT id FROM daily_summaries WHERE estado NOT IN ('ACEPTADO'))"
                    ),
                ]);
            });
    }

    /**
     * Envía un resumen con las boletas pendientes de la fecha de emisión $fecha
     * (hasta MAX_BOLETAS) y consulta su ticket.
     *
     * @param int $intentosConsulta Veces que se consulta el ticket si SUNAT aún no responde.
     * @param int $esperaSegundos Espera entre consultas (la primera siempre espera 3 s).
     * @return array{estado: string, mensaje: string, summary_id: int|null, cantidad: int}
     *   estado: ACEPTADO | ENVIADO | RECHAZADO | SIN_BOLETAS | BLOQUEADO | REINTENTAR
     */
    public function enviar(int $companyId, \DateTimeImmutable $fecha, int $intentosConsulta = 1, int $esperaSegundos = 20): array
    {
        $resultado = fn(string $estado, string $mensaje, ?int $summaryId = null, int $cantidad = 0) => [
            'estado' => $estado,
            'mensaje' => $mensaje,
            'summary_id' => $summaryId,
            'cantidad' => $cantidad,
        ];

        $Invoices = $this->fetchTable('Invoices');
        $DailySummaries = $this->fetchTable('DailySummaries');
        $company = $this->fetchTable('Companies')->get($companyId);

        $start = $fecha->setTime(0, 0, 0);
        $end = $fecha->setTime(23, 59, 59);

        // Bloquear si hay boletas de una fecha ANTERIOR aún pendientes
        $pendienteAnterior = $this->boletasPendientes($companyId)
            ->where(['Invoices.created <' => $start->format('Y-m-d H:i:s')])
            ->select(['Invoices.created'])
            ->orderBy(['Invoices.created' => 'ASC'])
            ->first();

        if ($pendienteAnterior) {
            return $resultado(
                'BLOQUEADO',
                'Debes enviar primero el resumen del ' . $pendienteAnterior->created->format('d/m/Y') . ' antes de enviar este.'
            );
        }

        $boletas = $this->boletasPendientes($companyId)
            ->where([
                'Invoices.created >=' => $start->format('Y-m-d H:i:s'),
                'Invoices.created <=' => $end->format('Y-m-d H:i:s'),
            ])
            ->orderBy(['Invoices.correlativo' => 'ASC'])
            ->limit(self::MAX_BOLETAS)
            ->all()
            ->toList();

        if (empty($boletas)) {
            return $resultado('SIN_BOLETAS', 'No hay boletas pendientes de resumen para esa fecha.');
        }

        $fechaEnvio = new \DateTime('now', new \DateTimeZone('America/Lima'));
        $corr = $this->siguienteCorrelativo($companyId, $fechaEnvio->format('Y-m-d'));
        $nombre = $company->ruc . '-RC-' . $fechaEnvio->format('Ymd') . '-' . $corr;

        $see = (new SunatService($company))->getSee();

        $empresa = (new GreCompany())
            ->setRuc((string)$company->ruc)
            ->setRazonSocial((string)$company->razon_social)
            ->setNombreComercial((string)($company->nombre_comercial ?: $company->razon_social))
            ->setAddress(
                (new Address())
                    ->setUbigueo((string)$company->ubigeo)
                    ->setDepartamento((string)$company->departamento)
                    ->setProvincia((string)$company->provincia)
                    ->setDistrito((string)$company->distrito)
                    ->setUrbanizacion((string)($company->urbanizacion ?: '-'))
                    ->setDireccion((string)$company->direccion)
                    ->setCodLocal((string)($company->cod_local ?: '0000'))
            );

        $details = [];
        $InvoiceItems = $this->fetchTable('InvoiceItems');

        foreach ($boletas as $b) {
            $montoGravado = 0.0;
            $montoExonerado = 0.0;
            $igvBoleta = 0.0;

            foreach ($InvoiceItems->find()->where(['invoice_id' => $b->id])->all() as $item) {
                // Ítems anteriores: gravados por defecto
                $tipoAfectacion = (string)($item->tip_afe_igv ?? '10');

                if (!in_array($tipoAfectacion, ['10', '20'], true)) {
                    throw new \RuntimeException('Tipo de afectación IGV inválido en el ítem ' . $item->id);
                }

                $base = round((float)$item->base_igv, 2);
                $igvItem = round((float)$item->igv, 2);

                if ($tipoAfectacion === '20') {
                    if (abs($igvItem) > 0.001) {
                        throw new \RuntimeException('El ítem exonerado tiene IGV distinto de cero: ' . $item->id);
                    }
                    $montoExonerado += $base;
                } else {
                    $montoGravado += $base;
                }

                $igvBoleta += $igvItem;
            }

            $montoGravado = round($montoGravado, 2);
            $montoExonerado = round($montoExonerado, 2);
            $igvBoleta = round($igvBoleta, 2);

            // Validar que el desglose coincida con la boleta guardada
            if (abs(round($montoGravado + $montoExonerado + $igvBoleta, 2) - (float)$b->total) > 0.01) {
                throw new \RuntimeException('Los importes tributarios no coinciden en la boleta ' . $b->serie . '-' . $b->correlativo);
            }

            $details[] = (new SummaryDetail())
                ->setTipoDoc('03')
                ->setSerieNro($b->serie . '-' . str_pad((string)$b->correlativo, 8, '0', STR_PAD_LEFT))
                ->setEstado('1')
                ->setClienteTipo((string)($b->cliente_tipo_doc !== null && $b->cliente_tipo_doc !== '' ? $b->cliente_tipo_doc : '1'))
                ->setClienteNro((string)($b->cliente_numero ?: '00000000'))
                ->setTotal((float)$b->total)
                ->setMtoOperGravadas($montoGravado)
                ->setMtoOperExoneradas($montoExonerado)
                ->setMtoIGV($igvBoleta)
                ->setPorcentajeIgv(18.00);
        }

        $summary = (new Summary())
            ->setFecGeneracion(new \DateTime($fecha->format('Y-m-d')))
            ->setFecResumen($fechaEnvio)
            ->setCorrelativo((string)$corr)
            ->setCompany($empresa)
            ->setDetails($details);

        // Primero se envía a SUNAT: si falla aquí, no se toca la BD
        $res = $see->send($summary);

        // Guardar XML siempre
        $xmlDir = WWW_ROOT . 'xml' . DS;
        if (!is_dir($xmlDir)) {
            mkdir($xmlDir, 0777, true);
        }
        $xmlPath = 'xml/' . $summary->getName() . '.xml';
        file_put_contents(WWW_ROOT . $xmlPath, $see->getFactory()->getLastXml());

        if (!$res->isSuccess()) {
            $err = $res->getError();
            $code = (string)$err->getCode();
            $message = (string)$err->getMessage();

            // Código 0135: SUNAT ocupada, no guardar nada, boletas siguen libres
            if ($code === '0135') {
                return $resultado('REINTENTAR', 'SUNAT no pudo encolar el resumen ahora. Reintenta en unos minutos. Código: ' . $code);
            }

            // Otro error: registro de auditoría pero SIN tocar las boletas
            $DailySummaries->save($DailySummaries->newEntity([
                'company_id' => $companyId,
                'fecha' => $fecha->format('Y-m-d'),
                'correlativo' => $corr,
                'nombre' => $nombre,
                'estado' => 'RECHAZADO',
                'codigo_sunat' => $code,
                'descripcion_sunat' => $message,
                'xml_path' => $xmlPath,
            ]));

            return $resultado('RECHAZADO', 'SUNAT rechazó resumen: ' . $code . ' - ' . $message, null, count($boletas));
        }

        // SUNAT encoló el resumen — tenemos ticket
        $ticket = $res->getTicket();

        $cdrZip = null;
        $cdrCode = null;
        $cdrDescription = null;
        $estadoFinal = 'ENVIADO'; // si SUNAT aún no responde

        for ($intento = 1; $intento <= max(1, $intentosConsulta); $intento++) {
            sleep($intento === 1 ? 3 : $esperaSegundos);
            try {
                $statusRes = $see->getStatus($ticket);
                if ($statusRes->isSuccess()) {
                    $cdr = $statusRes->getCdrResponse();
                    $cdrZip = $statusRes->getCdrZip();
                    $cdrCode = (string)$cdr->getCode();
                    $cdrDescription = (string)$cdr->getDescription();
                    // El CDR puede traer un rechazo: entonces las boletas siguen pendientes.
                    $estadoFinal = $cdr->isAccepted() ? 'ACEPTADO' : 'RECHAZADO';
                    break;
                }
            } catch (\Throwable $ignore) {
                // Sin respuesta: el resumen queda ENVIADO y se puede consultar después
            }
        }

        $cdrPath = null;
        if ($cdrZip) {
            $cdrDir = WWW_ROOT . 'cdr' . DS;
            if (!is_dir($cdrDir)) {
                mkdir($cdrDir, 0777, true);
            }
            $cdrPath = 'cdr/R-' . $nombre . '.zip';
            file_put_contents(WWW_ROOT . $cdrPath, $cdrZip);
        }

        // Persistir todo en una transacción atómica
        $summaryEntity = null;
        $DailySummaries->getConnection()->transactional(function () use (
            $DailySummaries, $Invoices, $companyId, $fecha, $corr, $nombre,
            $ticket, $xmlPath, $cdrPath, $cdrCode, $cdrDescription,
            $estadoFinal, $boletas, &$summaryEntity
        ) {
            $summaryEntity = $DailySummaries->newEntity([
                'company_id' => $companyId,
                'fecha' => $fecha->format('Y-m-d'),
                'correlativo' => $corr,
                'nombre' => $nombre,
                'estado' => $estadoFinal,
                'ticket' => $ticket,
                'xml_path' => $xmlPath,
                'cdr_path' => $cdrPath,
                'codigo_sunat' => $cdrCode,
                'descripcion_sunat' => $cdrDescription,
            ]);

            if (!$DailySummaries->save($summaryEntity)) {
                throw new \RuntimeException('No se pudo guardar el resumen diario.');
            }

            foreach ($boletas as $b) {
                $b->daily_summary_id = $summaryEntity->id;
                // Rechazado: siguen pendientes para un nuevo resumen.
                $b->estado = match ($estadoFinal) {
                    'ACEPTADO' => 'ACEPTADO',
                    'RECHAZADO' => 'PENDIENTE_RESUMEN',
                    default => 'EN_RESUMEN',
                };

                if ($estadoFinal === 'ACEPTADO') {
                    $b->codigo_sunat = $cdrCode;
                    $b->descripcion_sunat = $cdrDescription;
                    $b->cdr_path = $cdrPath;
                }

                if (!$Invoices->save($b)) {
                    throw new \RuntimeException('No se pudo actualizar boleta ID ' . $b->id);
                }
            }
        });

        $mensaje = match ($estadoFinal) {
            'ACEPTADO' => '✅ Resumen aceptado por SUNAT: ' . $cdrDescription,
            'RECHAZADO' => 'SUNAT rechazó el resumen: ' . $cdrCode . ' - ' . $cdrDescription . '. Las boletas siguen pendientes para reenviarlas.',
            default => '📨 Resumen enviado (Ticket: ' . $ticket . '). SUNAT aún no procesó la respuesta. ' .
                'Puedes consultar el CDR desde el detalle del resumen.',
        };

        return $resultado($estadoFinal, $mensaje, (int)$summaryEntity->id, count($boletas));
    }
}
