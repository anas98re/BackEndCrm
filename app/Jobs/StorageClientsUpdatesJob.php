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
        $report = [];
        foreach ($differences as $key => $value) {
            switch ($key) {
                case 'city':
                    $cityValue = city::where('id_city', $value)->first()->name_city;
                    $report[] = $key . ' ( ' . $cityValue . ' ) ';
                    break;
                case 'activity_type_fk':
                    $id_activity_type_value = activity_type::where('id_activity_type', $value)
                        ->first()->name_activity_type;
                    $report[] = 'activity_type' . ' ( ' . $id_activity_type_value . ' ) ';
                    break;
                case 'presystem':
                    $presystem_value = company::where('id_Company', $value)
                        ->first()->name_company;
                    $report[] = 'presystem' . ' ( ' . $presystem_value . ' ) ';
                    break;
                default:
                    $report[] = $key . ' ( ' . $value . ' ) ';
                    break;
            }
        }
        $reportMessage = implode("\n", $report);

        // to storage old data
        $differences = array_diff_assoc($dataBeforeUpdate, $dataAfterUpdate);
        $reportOld = [];
        foreach ($differences as $key => $value) {
            switch ($key) {
                case 'city':
                    $cityValue = city::where('id_city', $value)->first()->name_city;
                    $reportOld[] = $key . ' ( ' . $cityValue . ' ) ';
                    break;
                case 'activity_type_fk':
                    $id_activity_type_value = activity_type::where('id_activity_type', $value)
                        ->first()->name_activity_type;
                    $reportOld[] = 'activity_type' . ' ( ' . $id_activity_type_value . ' ) ';
                    break;
                case 'presystem':
                    $presystem_value = company::where('id_Company', $value)
                        ->first()->name_company;
                    $reportOld[] = 'presystem' . ' ( ' . $presystem_value . ' ) ';
                    break;
                default:
                    $reportOld[] = $key . ' ( ' . $value . ' ) ';
                    break;
            }
        }
        $reportMessageOld = implode("\n", $reportOld);

        $clientsUpdateReport = new clientsUpdateReport();
        $clientsUpdateReport->changesData = $reportMessageOld;
        $clientsUpdateReport->edit_date = $this->dateUpdate;
        $clientsUpdateReport->fk_user = $this->userId;
        $clientsUpdateReport->save();
    }
}
