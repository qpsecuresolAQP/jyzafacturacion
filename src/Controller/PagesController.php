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
            $citasTable = $this->fetchTable('Citas');

            $today = date('Y-m-d');
            $inicioMes = date('Y-m-01 00:00:00');
            $finMes = date('Y-m-t 23:59:59');

            // Citas creadas hoy por usuario (usando created_at en zona horaria Lima)
            $tzLima = new \DateTimeZone('America/Lima');
            $hoyInicio = new \DateTime('today', $tzLima);
            $hoyFin = (clone $hoyInicio)->modify('+1 day -1 second');

            // Convertir a UTC (porque los timestamps están en UTC en la base de datos)
            $hoyInicioUTC = (clone $hoyInicio)->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
            $hoyFinUTC = (clone $hoyFin)->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');

            // Consulta actualizada
            $citasPorUsuarioHoy = $citasTable->find()
                ->select([
                    'user_id',
                    'total' => 'COUNT(*)'
                ])
                ->where([
                    'created_at >=' => $hoyInicioUTC,
                    'created_at <=' => $hoyFinUTC,
                    'campana_id IS NOT' => null
                ])
                ->group('user_id')
                ->contain([
                    'Users' => function ($q) {
                        return $q->select(['id', 'username']);
                    }
                ])
                ->toArray();

            // Citas por estado hoy (solo sin campaña)
            $citasPorEstadoHoy = $citasTable->find()
                ->select([
                    'estado',
                    'total' => 'COUNT(*)'
                ])
                ->where(function ($exp) use ($hoyInicioUTC, $hoyFinUTC) {
                    return $exp
                        ->between('created_at', $hoyInicioUTC, $hoyFinUTC)
                        ->isNotNull('campana_id');
                })
                ->group('estado')
                ->toArray();



            // Citas finalizadas por usuario en el mes actual (solo SIN campaña)
            $citasFinalizadasMensual = $citasTable->find()
                ->select([
                    'user_id',
                    'total' => 'COUNT(*)'
                ])
                ->where([
                    'estado' => 'finalizado',
                    'fecha_hora >=' => $inicioMes,
                    'fecha_hora <=' => $finMes,
                    'campana_id IS NOT' => null
                ])
                ->group('user_id')
                ->contain([
                    'Users' => function ($q) {
                        return $q->select(['id', 'username']);
                    }
                ])
                ->toArray();


            // Pacientes nuevos vs frecuentes en el mes
            // Pacientes del mes (paciente_id únicos)
            $pacientesDelMes = $citasTable->find()
                ->select(['paciente_id'])
                ->distinct()
                ->where([
                    'fecha_hora >=' => $inicioMes,
                    'fecha_hora <=' => $finMes,
                    'estado' => 'finalizado' // solo los atendidos
                ])
                ->enableHydration(false)
                ->toArray();

            $pacienteIdsDelMes = array_column($pacientesDelMes, 'paciente_id');

            // Inicializa en vacío
            $pacienteIdsFrecuentes = [];

            if (!empty($pacienteIdsDelMes)) {
                // Pacientes frecuentes (con citas antes del mes actual y con estado finalizado)
                $pacientesFrecuentes = $citasTable->find()
                    ->select(['paciente_id'])
                    ->distinct()
                    ->where([
                        'paciente_id IN' => $pacienteIdsDelMes,
                        'fecha_hora <' => $inicioMes,
                        'estado' => 'finalizado'
                    ])
                    ->enableHydration(false)
                    ->toArray();

                $pacienteIdsFrecuentes = array_column($pacientesFrecuentes, 'paciente_id');
            }

            // Conteos protegidos contra negativos
            $pacientesFrecuentesCount = count($pacienteIdsFrecuentes);
            $pacientesNuevos = max(0, count($pacienteIdsDelMes) - $pacientesFrecuentesCount);

            // Tiempo promedio entre llegada e inicio de consulta en el mes actual
            $tiempoPromedioLlegadaInicio = $citasTable->find()
                ->select([
                    'avg_diff' => 'AVG(TIMESTAMPDIFF(MINUTE, hora_llegada, hora_inicio_consulta))'
                ])
                ->where([
                    'hora_llegada IS NOT' => null,
                    'hora_inicio_consulta IS NOT' => null,
                    'fecha_hora >=' => $inicioMes,
                    'fecha_hora <=' => $finMes,
                ])
                ->first();

            $promedioMinutos = $tiempoPromedioLlegadaInicio?->avg_diff ?? 0;
            // Total de citas finalizadas por doctor en el mes actual
            $citasFinalizadasPorDoctor = $citasTable->find()
                ->select([
                    'doctor_id',
                    'doctor_nombre' => 'Doctores.nombre',
                    'total' => 'COUNT(*)'
                ])
                ->where([
                    'estado' => 'finalizado',
                    'fecha_hora >=' => $inicioMes,
                    'fecha_hora <=' => $finMes,
                ])
                ->group('doctor_id')
                ->leftJoinWith('Doctores')
                ->toArray();

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

            // Citas del día (todas las citas programadas para hoy)
            $citasDelDia = $citasTable->find()
                ->contain([
                    'Pacientes',
                    'Doctores',
                    'Users'
                ])
                ->where([
                    'DATE(Citas.fecha_hora)' => $today
                ])
                ->order(['Citas.fecha_hora' => 'ASC'])
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

            // Recordatorios de pacientes pendientes para los próximos 5 días
            $recordatorioControlesTable = $this->fetchTable('RecordatorioControles');
            $fechaRecordatorioEn5Dias = (new \DateTime($today))->modify('+5 days')->format('Y-m-d');
            $recordatoriosPendientesEn5Dias = $recordatorioControlesTable->find()
                ->select([
                    'id' => 'RecordatorioControles.id',
                    'fecha_control' => 'RecordatorioControles.fecha_control',
                    'proximo_control' => 'RecordatorioControles.proximo_control',
                    'estado' => 'RecordatorioControles.estado',
                    'recordatorio_enviado' => 'RecordatorioControles.recordatorio_enviado',
                    'recordatorio_titulo' => 'Recordatorios.titulo',
                    'paciente_nombre' => 'Pacientes.nombre',
                    'paciente_apellido' => 'Pacientes.apellido',
                    'paciente_telefono' => 'Pacientes.telefono_celular',
                ])
                ->leftJoinWith('Recordatorios.Pacientes')
                ->where([
                    'RecordatorioControles.estado_control' => 'A',
                    'RecordatorioControles.proximo_control >=' => $today,
                    'RecordatorioControles.proximo_control <=' => $fechaRecordatorioEn5Dias,
                ])
                ->order([
                    'Recordatorios.titulo' => 'ASC',
                    'Pacientes.nombre' => 'ASC',
                    'Pacientes.apellido' => 'ASC',
                ])
                ->enableHydration(false)
                ->toArray();

            // Pacientes con citas en estado "programar"
            try {
                $citasTable = $this->fetchTable('Citas');
                $citasPorProgramarQuery = $citasTable->find('all')
                    ->contain(['Pacientes'])
                    ->where(['Citas.estado' => 'programar'])
                    ->order(['Citas.id' => 'DESC'])
                    ->limit(15);

                $citasPorProgramar = $citasPorProgramarQuery->toArray();
            } catch (\Exception $e) {
                $citasPorProgramar = [];
            }

            try {

                $paquetesPagosTable = $this->fetchTable('PaquetesPagos');
                $cuotasTable = $this->fetchTable('PaquetesPagosCuotas');

                $proximos7Dias = (new \DateTime())
                    ->modify('+7 days')
                    ->format('Y-m-d');

                // ==========================
                // PRÓXIMOS 7 DÍAS
                // ==========================

                $pagosProximosContado = $paquetesPagosTable->find()
                    ->contain([
                        'HistoriasClinicas' => ['Pacientes']
                    ])
                    ->where([
                        'PaquetesPagos.estado' => 'pendiente',
                        'PaquetesPagos.estado_paquete' => 'A',
                        'PaquetesPagos.tipo_pago' => 'contado',
                        'PaquetesPagos.fecha_recordatorio <=' => $proximos7Dias
                    ])
                    ->order(['PaquetesPagos.fecha_recordatorio' => 'ASC'])
                    ->toArray();

                $pagosProximosCuotas = $cuotasTable->find()
                    ->contain([
                        'PaquetesPagos' => [
                            'HistoriasClinicas' => ['Pacientes']
                        ]
                    ])
                    ->where([
                        'PaquetesPagosCuotas.estado' => 'pendiente',
                        'PaquetesPagosCuotas.estado_cuota' => 'A',
                        'PaquetesPagosCuotas.fecha_recordatorio <=' => $proximos7Dias
                    ])
                    ->order(['PaquetesPagosCuotas.fecha_recordatorio' => 'ASC'])
                    ->toArray();

                // ==========================
                // TODOS LOS PENDIENTES
                // ==========================

                $pagosTodosContado = $paquetesPagosTable->find()
                    ->contain([
                        'HistoriasClinicas' => ['Pacientes']
                    ])
                    ->where([
                        'PaquetesPagos.estado' => 'pendiente',
                        'PaquetesPagos.estado_paquete' => 'A',
                        'PaquetesPagos.tipo_pago' => 'contado'
                    ])
                    ->order(['PaquetesPagos.fecha_recordatorio' => 'ASC'])
                    ->toArray();

                $pagosTodosCuotas = $cuotasTable->find()
                    ->contain([
                        'PaquetesPagos' => [
                            'HistoriasClinicas' => ['Pacientes']
                        ]
                    ])
                    ->where([
                        'PaquetesPagosCuotas.estado' => 'pendiente',
                        'PaquetesPagosCuotas.estado_cuota' => 'A'
                    ])
                    ->order(['PaquetesPagosCuotas.fecha_recordatorio' => 'ASC'])
                    ->toArray();
            } catch (\Exception $e) {

                $pagosProximosContado = [];
                $pagosProximosCuotas = [];

                $pagosTodosContado = [];
                $pagosTodosCuotas = [];
            }

            // Agrupar para la vista
            $recordatoriosPagoProximos = [
                'contado' => $pagosProximosContado,
                'partes' => $pagosProximosCuotas
            ];

            $recordatoriosPagoTodos = [
                'contado' => $pagosTodosContado,
                'partes' => $pagosTodosCuotas
            ];

            $this->set(compact(
                'citasPorUsuarioHoy',
                'citasPorEstadoHoy',
                'citasFinalizadasMensual',
                'pacientesNuevos',
                'pacientesFrecuentesCount',
                'promedioMinutos',
                'citasFinalizadasPorDoctor',
                'cumpleañosHoy',
                'citasDelDia',
                'tratamientosUltimos7Dias',
                'ingresosPorTratamiento',
                'recordatoriosPendientesEn5Dias',
                'citasPorProgramar',
                'recordatoriosPagoProximos',
                'recordatoriosPagoTodos'
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
