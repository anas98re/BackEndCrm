<?php

namespace App\Jobs;

use App\Models\agent;
use App\Models\invoicesUpdateReport;
use App\Models\participate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class StorageInvoicesUpdatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invoiceId;
    protected $dataBeforeUpdate;
    protected $dataAfterUpdate;
    protected $dateUpdate;
    protected $userId;
    protected $update_source;
    /**
     * Create a new job instance.
     */
    public function __construct($invoiceId, $dataBeforeUpdate, $dataAfterUpdate, $dateUpdate, $userId, $update_source)
    {
        $this->invoiceId = $invoiceId;
        $this->dataBeforeUpdate = $dataBeforeUpdate;
        $this->dataAfterUpdate = $dataAfterUpdate;
        $this->dateUpdate = $dateUpdate;
        $this->userId = $userId;
        $this->update_source = $update_source;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        info('fourth');
        $dataBeforeUpdate = $this->dataBeforeUpdate;
        $dataAfterUpdate = $this->dataAfterUpdate;

        $differences = array_diff_assoc($dataAfterUpdate, $dataBeforeUpdate);

        $report = $this->generateReport($differences, $dataBeforeUpdate, $dataAfterUpdate);

        $reportMessage = implode("\n", $report);

        $clientsUpdateReport = new invoicesUpdateReport();
        $clientsUpdateReport->changesData = $reportMessage;
        $clientsUpdateReport->edit_date = $this->dateUpdate;
        $clientsUpdateReport->user_id = (int) $this->userId;
        $clientsUpdateReport->invoice_id = $this->invoiceId;
        $clientsUpdateReport->update_source = $this->update_source;
        $clientsUpdateReport->save();
    }

    private function generateReport($differences, $dataBeforeUpdate, $dataAfterUpdate)
    {
        info('$differences for invoicess: ', array($differences));
        $report = [];
        foreach ($differences as $key => $value) {
            switch ($key) {
                case 'participate_fk':
                    $participateBefore = participate::where('id_participate', $dataBeforeUpdate[$key])->first()->name_participate;
                    $participateAfter = participate::where('id_participate', $dataAfterUpdate[$key])->first()->name_participate;
                    $report[] = $key . ': (' . $participateBefore . ') TO (' . $participateAfter . ')';
                    break;
                case 'fk_agent':
                    $agentBefore = 'not_found';
                    $agentAfter = 'not_found';
                    if ($dataBeforeUpdate[$key]) {
                        $agentBefore = agent::where('id_agent', $dataBeforeUpdate[$key])->first()->name_agent;
                    }
                    if ($dataAfterUpdate[$key]) {
                        $agentAfter = agent::where('id_agent', $dataAfterUpdate[$key])->first()->name_agent;
                    }
                    $report[] = $key . ': (' . $agentBefore . ') TO (' . $agentAfter . ')';
                    break;
                default:
                    $report[] = $key . ': ( ' . $dataBeforeUpdate[$key] . ') TO (' . $dataAfterUpdate[$key] . ' ) ';
                    break;
            }
        }
        info('$report for invoicess: ', array($report));
        $reportMessage = implode("\n", $report);

        $clientsUpdateReport = new invoicesUpdateReport();
        $clientsUpdateReport->changesData = $reportMessage;
        $clientsUpdateReport->edit_date = $this->dateUpdate;
        $clientsUpdateReport->user_id = (int) $this->userId;
        $clientsUpdateReport->invoice_id = $this->invoiceId;
        $clientsUpdateReport->update_source = $this->update_source;
        $clientsUpdateReport->save();
    }
}
