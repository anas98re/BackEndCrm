<?php

namespace App\Jobs;

use App\Models\agent;
use App\Models\invoicesUpdateReport;
use App\Models\participate;
use App\Models\regoin;
use App\Models\users;
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
    protected $id_invoice_product;
    /**
     * Create a new job instance.
     */
    public function __construct(
        $invoiceId,
        $dataBeforeUpdate,
        $dataAfterUpdate,
        $dateUpdate,
        $userId,
        $update_source,
        $id_invoice_product
    ) {
        $this->invoiceId = $invoiceId;
        $this->dataBeforeUpdate = $dataBeforeUpdate;
        $this->dataAfterUpdate = $dataAfterUpdate;
        $this->dateUpdate = $dateUpdate;
        $this->userId = $userId;
        $this->update_source = $update_source;
        $this->id_invoice_product = $id_invoice_product;
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
        if ($differences) {
            $report = $this->generateReport($differences, $dataBeforeUpdate, $dataAfterUpdate);

            $reportMessage = implode("\n", $report);

            $clientsUpdateReport = new invoicesUpdateReport();
            $clientsUpdateReport->changesData = $reportMessage;
            $clientsUpdateReport->edit_date = $this->dateUpdate;
            $clientsUpdateReport->user_id = (int) $this->userId;
            $clientsUpdateReport->invoice_id = $this->invoiceId;
            $clientsUpdateReport->update_source = $this->update_source;
            $clientsUpdateReport->id_invoice_product = $this->id_invoice_product ? $this->id_invoice_product : null;
            $clientsUpdateReport->save();
        }
    }

    private function generateReport($differences, $dataBeforeUpdate, $dataAfterUpdate)
    {
        info('$differences for invoicess: ', array($differences));
        $report = [];
        foreach ($differences as $key => $value) {
            switch ($key) {
                case 'participate_fk':
                    $participateBefore = 'not_found';
                    $participateAfter = 'not_found';
                    if ($dataBeforeUpdate[$key]) {
                        $participateBefore = participate::where('id_participate', $dataBeforeUpdate[$key])->first()->name_participate;
                    }
                    if ($dataAfterUpdate[$key]) {
                        $participateAfter = participate::where('id_participate', $dataAfterUpdate[$key])->first()->name_participate;
                    }
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
                case 'type_seller':
                    $typeSellerOptions = ['موزع', 'وكيل', 'متعاون', 'موظف'];

                    $typeSellerAfter = $typeSellerOptions[$dataAfterUpdate[$key]] ?? 'موظف';
                    $typeSellerBefore = $typeSellerOptions[$dataBeforeUpdate[$key]] ?? 'موظف';

                    $report[] = 'typeSellerName' . ': (' . $typeSellerBefore . ') TO (' . $typeSellerAfter . ')';
                    break;
                case 'fk_idUser':
                    $userBefore = users::where('id_user', $dataBeforeUpdate[$key])->first()->nameUser;
                    $userAfter = users::where('id_user', $dataAfterUpdate[$key])->first()->nameUser;
                    $report[] = 'userName' . ': (' . $userBefore . ') TO (' . $userAfter . ') ';
                    break;
                case 'fk_regoin_invoice':
                    $regoinBefore = regoin::where('id_regoin', $dataBeforeUpdate[$key])->first()->name_regoin;
                    $regoinAfter = regoin::where('id_regoin', $dataAfterUpdate[$key])->first()->name_regoin;
                    $report[] = 'regoinName' . ': (' . $regoinBefore . ') TO (' . $regoinAfter . ') ';
                    break;
                default:
                    $report[] = $key . ': (' . $dataBeforeUpdate[$key] . ') TO (' . $dataAfterUpdate[$key] . ' ) ';
                    break;
            }
        }
        return $report;
    }
}
