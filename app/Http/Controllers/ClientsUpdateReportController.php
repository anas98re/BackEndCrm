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
        $clientId = $request->input('id_client');
        $dataBeforeUpdate = json_decode($request->input('dataBeforeUpdate'), true)[0];
        $dataAfterUpdate = json_decode($request->input('dataAfterUpdate'), true)[0];
        $dateUpdate = $request->input('dateUpdate');
        $userId = $request->input('fk_idUser');

        StorageClientsUpdatesJob::dispatch($clientId, $dataBeforeUpdate, $dataAfterUpdate, $dateUpdate, $userId);
    }
}
