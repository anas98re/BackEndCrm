<?php

namespace App\Jobs;

use App\Models\activity_type;
use App\Models\city;
use App\Models\clients;
use App\Models\clientsUpdateReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StorageClientsUpdatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;
    /**
     * Create a new job instance.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $client = clients::where('id_clients', $this->request->id_client)->first();

        $clientBefore = $client->getOriginal();
        $dataBeforeUpdate = json_decode($this->request->dataBeforeUpdate, true)[0];
        $dataAfterUpdate = json_decode($this->request->dataAfterUpdate, true)[0];

        $differences = array_diff_assoc($dataAfterUpdate, $dataBeforeUpdate);

        foreach ($differences as $key => $value) {
            if ($key == 'city') {
                $cityValue = city::where('id_city', $value)->first()->name_city;
                $report[] = $key . ' ( ' . $cityValue . ' ) ';
            } elseif ($key == 'activity_type_fk') {
                $id_activity_type_value = activity_type::where('id_activity_type', $value)
                    ->first()->name_activity_type;
                $report[] = 'activity_type' . ' ( ' . $id_activity_type_value . ' ) ';
            } else {
                $report[] = $key . ' ( ' . $value . ' ) ';
            }
        }

        $reportMessage = implode("\n", $report);

        $clientsUpdateReport = new clientsUpdateReport();
        $clientsUpdateReport->changesData = $reportMessage;
        $clientsUpdateReport->edit_date = $this->request->dateUpdate;
        $clientsUpdateReport->fk_user = $this->request->fk_idUser;
        $clientsUpdateReport->save();
    }
}
