<?php

namespace App\Http\Controllers;

use App\Models\invoicesUpdateReport;
use App\Http\Requests\StoreinvoicesUpdateReportRequest;
use App\Http\Requests\UpdateinvoicesUpdateReportRequest;
use App\Models\client_invoice;
use App\Models\clients;
use App\Models\users;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoicesUpdateReportController extends Controller
{
    public function storageInvoicesUpdates(Request $request)
    {
        info('****************************************************************');
        info('$request->all() for storageInvoicesUpdates:', $request->all());

        $requestData = json_decode($request->getContent(), true);
        $dataBeforeUpdate = json_decode($requestData['dataBeforeUpdate'], true);
        info('dataBeforeUpdate is:', $dataBeforeUpdate);
        info('requestData is:', $requestData);
        // foreach ($dataBeforeUpdate as $key1 => $value1) {
        //     if($key1 == 'fk_idClient')
        //     {
                $client = clients::where('id_clients',$dataBeforeUpdate[0]['fk_idClient'])->first();
        //     }
        // }
        $user = null;
        if($dataBeforeUpdate[0]['lastuserupdate']){
            $user = users::where('id_user',$dataBeforeUpdate[0]['lastuserupdate'])->first()->nameUser;
        }

        $dataBeforeUpdateHandeling = [];
            $dataBeforeUpdateHandeling = [
                'name_enterprise'=> $client->name_enterprise,
                'name_client'=> $client->name_client,
                'fk_client'=> $client->id_clients,
                'date_create'=> $client->date_create,
                'date_approve'=> $dataBeforeUpdate[0]['date_approve'],
                'fk_idUser'=> $dataBeforeUpdate[0]['fk_idUser'],
                'fk_regoin_invoice'=> $dataBeforeUpdate[0]['fk_regoin_invoice'],
                'fk_regoin'=> $dataBeforeUpdate[0]['fk_regoin_invoice'],
                'fkcountry'=> 1,
                'lastuserupdate'=> $dataBeforeUpdate[0]['lastuserupdate'],
                'lastnameuser'=> $user,
                'id_invoice'=> $dataBeforeUpdate[0]['id_invoice'],
                'date_lastuserupdate'=> $dataBeforeUpdate[0]['date_lastuserupdate'],
            ];


        $values = $requestData['values'];

        info('values is:', $values);
        $data = [];
        $infoData = [];
        foreach ($values as $index => $value) {
            if ($value !== $dataBeforeUpdateHandeling[$index]) {
                $changes[] = [
                    'before' => $dataBeforeUpdateHandeling[$index],
                    'after' => $value,
                ];
                $infoData[] = [
                    'value' => $value,
                    'dataBeforeUpdate' => $dataBeforeUpdateHandeling[$index],
                ];
            }
        }

        foreach ($infoData as $data) {
            info('$value inside for is:', $data['value']);
            info('$dataBeforeUpdate[$index] inside for is:', $data['dataBeforeUpdate']);
        }


        info('changes is:', $changes);

        $updates = [];
        foreach ($changes as $key => $change) {
            $before = is_array($change['before']) ? json_encode($change['before']) : $change['before'];
            $after = is_array($change['after']) ? json_encode($change['after']) : $change['after'];
            $updates[] = $key . ' : ' . $before . ' -> ' . $after;
        }

        $InvoicesUpdates = implode("\n", $updates);

        $invoiceData = client_invoice::where('id_invoice', $request->id_invoice)->first();
        $isApprove = $invoiceData->isApprove === 1 ? 'true' : 'false';

        $invoicesUpdateReport = new invoicesUpdateReport();
        $invoicesUpdateReport->changesData = $InvoicesUpdates;
        $invoicesUpdateReport->afterApprove = $isApprove;
        $invoicesUpdateReport->edit_date = Carbon::now('Asia/Riyadh')->toDateTimeString();
        $invoicesUpdateReport->user_id = $request->fk_idUser;
        $invoicesUpdateReport->save();

        return $this->sendResponse($invoicesUpdateReport, 'Updated success');
    }
}
