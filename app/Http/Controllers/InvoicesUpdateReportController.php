<?php

namespace App\Http\Controllers;

use App\Models\invoicesUpdateReport;
use App\Http\Requests\StoreinvoicesUpdateReportRequest;
use App\Http\Requests\UpdateinvoicesUpdateReportRequest;
use App\Models\client_invoice;
use App\Models\clients;
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
        $dataBeforeUpdateHandeling = [];
        // foreach ($dataBeforeUpdate as $key => $value) {
            $dataBeforeUpdateHandeling = [
                'name_enterprise'=> $client->name_enterprise,
                'name_client'=> $client->name_client,
                'fk_client'=> $client->id_clients,
                'date_create'=> $client->date_create,
                'date_create'=> $dataBeforeUpdate['date_approve'],
            ];
        // }

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
