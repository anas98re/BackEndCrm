<?php

namespace App\Jobs;

use App\Models\activity_type;
use App\Models\city;
use App\Models\clients;
use App\Models\clientsUpdateReport;
use App\Models\company;
use App\Models\invoicesUpdateReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StorageClientsUpdatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $clientId;
    protected $dataBeforeUpdate;
    protected $dataAfterUpdate;
    protected $dateUpdate;
    protected $userId;
    /**
     * Create a new job instance.
     */
    public function __construct($clientId, $dataBeforeUpdate, $dataAfterUpdate, $dateUpdate, $userId)
    {
        $this->clientId = $clientId;
        $this->dataBeforeUpdate = $dataBeforeUpdate;
        $this->dataAfterUpdate = $dataAfterUpdate;
        $this->dateUpdate = $dateUpdate;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $dataBeforeUpdate = $this->dataBeforeUpdate;
        $dataAfterUpdate = $this->dataAfterUpdate;

        // to storage new data
        $differences = array_diff_assoc($dataAfterUpdate, $dataBeforeUpdate);
        $report = $this->generateReport($differences, $dataBeforeUpdate, $dataAfterUpdate);

        $reportMessage = implode("\n", $report);

        $clientsUpdateReport = new clientsUpdateReport();
        $clientsUpdateReport->changesData = $reportMessage;
        $clientsUpdateReport->edit_date = $this->dateUpdate;
        $clientsUpdateReport->fk_user = $this->userId;
        $clientsUpdateReport->save();
    }

    private function generateReport($differences, $dataBeforeUpdate, $dataAfterUpdate)
    {
        $report = [];
        foreach ($differences as $key => $value) {
            switch ($key) {
                case 'city':
                    $cityBefore = city::where('id_city', $dataBeforeUpdate[$key])->first()->name_city;
                    $cityAfter = city::where('id_city', $dataAfterUpdate[$key])->first()->name_city;
                    $report[] = $key . ' ( ' . $cityBefore . ' ) to ( ' . $cityAfter . ' )';
                    break;
                case 'activity_type_fk':
                    $activityBefore = activity_type::where('id_activity_type', $dataBeforeUpdate[$key])->first()->name_activity_type;
                    $activityAfter = activity_type::where('id_activity_type', $dataAfterUpdate[$key])->first()->name_activity_type;
                    $report[] = 'activity_type' . ' ( ' . $activityBefore . ' ) to ( ' . $activityAfter . ' )';
                    break;
                case 'presystem':
                    $presystemBefore = company::where('id_Company', $dataBeforeUpdate[$key])->first()->name_company;
                    $presystemAfter = company::where('id_Company', $dataAfterUpdate[$key])->first()->name_company;
                    $report[] = 'presystem' . ' ( ' . $presystemBefore . ' ) to ( ' . $presystemAfter . ' )';
                    break;
                default:
                    $report[] = $key . ' ( ' . $dataBeforeUpdate[$key] . ' ) to ( ' . $dataAfterUpdate[$key] . ' )';
                    break;
            }
        }
        return $report;
    }
}
