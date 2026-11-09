<?php
declare(strict_types=1);

namespace App\Controller;

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

    /**
     * La consulta del ticket vive en Invoices::consultarResumen (una sola lógica
     * para resúmenes normales y de anulación).
     */
    public function consultarCdr($id = null)
    {
        return $this->redirect(['controller' => 'Invoices', 'action' => 'consultarResumen', (int)$id]);
    }
}