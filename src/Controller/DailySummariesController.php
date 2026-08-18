<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\SunatService;

class DailySummariesController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
    }

    public function index()
    {
        $query = $this->DailySummaries->find()
            ->contain(['Companies'])
            ->order(['DailySummaries.created' => 'DESC']);

        $dailySummaries = $this->paginate($query);
        $this->set(compact('dailySummaries'));
    }

    public function view($id = null)
    {
        $summary = $this->DailySummaries->get($id, [
            'contain' => ['Companies', 'Invoices'],
        ]);

        $this->set(compact('summary'));
    }

    public function consultarCdr($id = null)
    {
        $this->request->allowMethod(['get', 'post']);

        $summary = $this->DailySummaries->get($id, [
            'contain' => ['Companies'],
        ]);

        if (empty($summary->ticket)) {
            $this->Flash->error('El resumen no tiene ticket para consultar.');
            return $this->redirect(['action' => 'view', $id]);
        }

        if ($summary->estado === 'ACEPTADO') {
            $this->Flash->warning('Este resumen ya fue aceptado por SUNAT.');
            return $this->redirect(['action' => 'view', $id]);
        }

        $company = $summary->company;

        try {
            $sunat = new SunatService($company);
            $see   = $sunat->getSee();

            $res = $see->getStatus($summary->ticket);

            if (!$res->isSuccess()) {
                $err     = $res->getError();
                $code    = (string)$err->getCode();
                $message = (string)$err->getMessage();

                // Código 98: SUNAT aún procesando, no es error definitivo
                if ($code === '98') {
                    $this->Flash->warning('SUNAT aún está procesando el resumen. Reintenta en unos minutos.');
                    return $this->redirect(['action' => 'view', $id]);
                }

                $summary->estado            = 'RECHAZADO';
                $summary->codigo_sunat      = $code;
                $summary->descripcion_sunat = $message;
                $this->DailySummaries->save($summary);

                $this->Flash->error('SUNAT rechazó: ' . $code . ' - ' . $message);
                return $this->redirect(['action' => 'view', $id]);
            }

            $cdrDir = WWW_ROOT . 'cdr' . DS;
            if (!is_dir($cdrDir)) {
                mkdir($cdrDir, 0777, true);
            }

            $cdrPath = 'cdr/R-' . $summary->nombre . '.zip';
            file_put_contents(WWW_ROOT . $cdrPath, $res->getCdrZip());

            $cdr = $res->getCdrResponse();

            $summary->estado            = 'ACEPTADO';
            $summary->codigo_sunat      = (string)$cdr->getCode();
            $summary->descripcion_sunat = (string)$cdr->getDescription();
            $summary->cdr_path          = $cdrPath;
            $this->DailySummaries->save($summary);

            // Actualizar todas las boletas del resumen
            $Invoices = $this->fetchTable('Invoices');
            $boletas  = $Invoices->find()->where(['daily_summary_id' => $summary->id])->all();

            foreach ($boletas as $b) {
                $b->estado            = 'ACEPTADO';
                $b->codigo_sunat      = (string)$cdr->getCode();
                $b->descripcion_sunat = (string)$cdr->getDescription();
                $b->cdr_path          = $cdrPath;
                $Invoices->save($b);
            }

            $this->Flash->success('✅ Resumen aceptado por SUNAT: ' . $cdr->getDescription());
            return $this->redirect(['action' => 'view', $id]);

        } catch (\Throwable $e) {
            $this->Flash->error('Error al consultar CDR: ' . $e->getMessage());
            return $this->redirect(['action' => 'view', $id]);
        }
    }
}