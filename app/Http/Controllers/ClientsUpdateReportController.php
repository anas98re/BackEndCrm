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

        $clientBefore = $client->getOriginal();
        $dataBeforeUpdate = json_decode($request->dataBeforeUpdate, true)[0];

        $differences = [];

        foreach ($dataBeforeUpdate as $key => $value) {
            if (isset($clientBefore[$key]) && $value !== $clientBefore[$key]) {
                $differences[$key] = [
                    'old_value' => $clientBefore[$key],
                    'new_value' => $value,
                ];
            }
        }

        info('$differences: ', $differences);
        info('$clientBefore: ', array($clientBefore));
        // info('$clientAfter: ', array($clientAfter));
        info('$dataBeforeUpdate: ', array(json_decode($request->dataBeforeUpdate, true)));
        // info('$values: ', array($request->values->getDirty()));
    }
}
