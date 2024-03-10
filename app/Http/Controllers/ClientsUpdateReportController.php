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
        $dataAfterUpdate = $request->values[0];

        $Orginal = array_diff_assoc($dataBeforeUpdate, $clientBefore);
        $differences = array_diff_assoc($dataAfterUpdate, $clientBefore);

        info('$Orginal: ', $Orginal);
        info('$differences: ', $differences);

        foreach ($differences as $key => $value) {
            $report[] = $key . '(' . $value . ')';
        }

        $reportMessage = implode("\n", $report);

        $clientsUpdateReport = new clientsUpdateReport();
        $clientsUpdateReport->changesData = $reportMessage;
        $clientsUpdateReport->edit_date = $request->dateUpdate;
        $clientsUpdateReport->fk_user = $request->fk_idUser;
        $clientsUpdateReport->save();
    }
}
