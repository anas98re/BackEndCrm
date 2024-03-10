<?php

namespace App\Http\Controllers;

use App\Models\clientsUpdateReport;
use App\Http\Requests\StoreclientsUpdateReportRequest;
use App\Http\Requests\UpdateclientsUpdateReportRequest;
use App\Models\clients;
use Illuminate\Http\Request;

class ClientsUpdateReportController extends Controller
{
    public function storageClientsUpdates(Request $request)
    {
        $client = clients::where('id_clients', $request->id_client)->first();

        $clientAfter = $client->getDirty();
        $clientBefore = $client->getOriginal();
        $dataBeforeUpdate = json_decode($request->dataBeforeUpdate, true)[0];

        $differences = array_diff_assoc($clientBefore, $dataBeforeUpdate);

        info('$differences: ', $differences);
        info('$clientBefore: ', array($clientBefore));
        info('$clientAfter: ', array($clientAfter));
        info('$dataBeforeUpdate: ', array(json_decode($request->dataBeforeUpdate, true)));
        info('$values: ', array($request->values->getDirty()));
    }
}
