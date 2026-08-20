<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\View\Exception\MissingTemplateException;

class PagesController extends AppController
{
    public function display(string ...$path): ?Response
    {
        if (!$path) {
            return $this->redirect('/');
        }

        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }

        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }

        if ($page === 'home') {
            $inicioMes = date('Y-m-01 00:00:00');
            $finMes = date('Y-m-t 23:59:59');

            // Cumpleaños hoy
            $historiasTable = $this->fetchTable('HistoriasClinicas');
            $mesActual = (int)date('m');
            $diaActual = (int)date('d');
            $cumpleañosHoy = $historiasTable->find()
                ->contain(['Pacientes'])
                ->where([
                    'MONTH(HistoriasClinicas.fecha_nacimiento)' => $mesActual,
                    'DAY(HistoriasClinicas.fecha_nacimiento)' => $diaActual,
                ])
                ->toArray();

            // Tratamientos más realizados en los últimos 7 días (según facturación, sin anuladas)
            $hace7Dias = (new \DateTime('today'))->modify('-6 days')->format('Y-m-d 00:00:00');
            $hoyFinDia = (new \DateTime('today'))->modify('+1 day -1 second')->format('Y-m-d H:i:s');
            $invoiceItemsTable = $this->fetchTable('InvoiceItems');
            $tratamientosUltimos7Dias = $invoiceItemsTable->find()
                ->select([
                    'nombre' => 'Tratamientos.nombre',
                    'total' => 'SUM(InvoiceItems.cantidad)',
                ])
                ->innerJoinWith('Tratamientos')
                ->innerJoinWith('Invoices')
                ->where([
                    'InvoiceItems.tipo_item' => 'tratamiento',
                    'Invoices.estado !=' => 'ANULADO',
                    'Invoices.created >=' => $hace7Dias,
                    'Invoices.created <=' => $hoyFinDia,
                ])
                ->group('Tratamientos.id')
                ->order(['total' => 'DESC'])
                ->limit(10)
                ->enableHydration(false)
                ->toArray();

            // Ingresos por tratamiento (facturas del mes actual, sin anuladas)
            $ingresosPorTratamiento = $invoiceItemsTable->find()
                ->select([
                    'nombre' => 'Tratamientos.nombre',
                    'total' => 'SUM(InvoiceItems.total)',
                ])
                ->innerJoinWith('Tratamientos')
                ->innerJoinWith('Invoices')
                ->where([
                    'InvoiceItems.tipo_item' => 'tratamiento',
                    'Invoices.estado !=' => 'ANULADO',
                    'Invoices.created >=' => $inicioMes,
                    'Invoices.created <=' => $finMes,
                ])
                ->group('Tratamientos.id')
                ->order(['total' => 'DESC'])
                ->limit(10)
                ->enableHydration(false)
                ->toArray();

            $this->set(compact(
                'cumpleañosHoy',
                'tratamientosUltimos7Dias',
                'ingresosPorTratamiento'
            ));
        }

        $this->set(compact('page', 'subpage'));

        try {
            return $this->render(implode('/', $path));
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }
    }
}
