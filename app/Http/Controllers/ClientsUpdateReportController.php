<?php

namespace App\Http\Controllers;

use App\Models\clientsUpdateReport;
use App\Http\Requests\StoreclientsUpdateReportRequest;
use App\Http\Requests\UpdateclientsUpdateReportRequest;
use App\Jobs\StorageClientsUpdatesJob;
use App\Models\activity_type;
use App\Models\city;
use App\Models\clients;
use Illuminate\Http\Request;

class ClientsUpdateReportController extends Controller
{
    public function storageClientsUpdates(Request $request)
    {
        StorageClientsUpdatesJob::dispatch($request);
        // $client = clients::where('id_clients', $request->id_client)->first();

        // $clientBefore = $client->getOriginal();
        // $dataBeforeUpdate = json_decode($request->dataBeforeUpdate, true)[0];
        // $dataAfterUpdate = json_decode($request->dataAfterUpdate, true)[0];

        // $differences = array_diff_assoc($dataAfterUpdate, $dataBeforeUpdate);

        // foreach ($differences as $key => $value) {
        //     if ($key == 'city') {
        //         $cityValue = city::where('id_city', $value)->first()->name_city;
        //         $report[] = $key . ' ( ' . $cityValue . ' ) ';
        //     } elseif ($key == 'activity_type_fk') {
        //         $id_activity_type_value = activity_type::where('id_activity_type', $value)
        //             ->first()->name_activity_type;
        //         $report[] = 'activity_type' . ' ( ' . $id_activity_type_value . ' ) ';
        //     } else {
        //         $report[] = $key . ' ( ' . $value . ' ) ';
        //     }
        // }

        // $reportMessage = implode("\n", $report);

        // $clientsUpdateReport = new clientsUpdateReport();
        // $clientsUpdateReport->changesData = $reportMessage;
        // $clientsUpdateReport->edit_date = $request->dateUpdate;
        // $clientsUpdateReport->fk_user = $request->fk_idUser;
        // $clientsUpdateReport->save();
    }
}
