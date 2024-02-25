<?php

namespace App\Http\Controllers;

use App\Models\invoicesUpdateReport;
use App\Http\Requests\StoreinvoicesUpdateReportRequest;
use App\Http\Requests\UpdateinvoicesUpdateReportRequest;
use App\Models\client_invoice;
use App\Models\clients;
use App\Models\regoin;
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

        $client = clients::where('id_clients', $dataBeforeUpdate[0]['fk_idClient'])->first();

        $user = null;
        if ($dataBeforeUpdate[0]['lastuserupdate']) {
            $user = users::where('id_user', $dataBeforeUpdate[0]['lastuserupdate'])->first()->nameUser;
        }

        $dataBeforeUpdateHandeling = [];
        $dataBeforeUpdateHandeling = [
            'name_enterprise' => $client->name_enterprise,
            'name_client' => $client->name_client,
            'fk_client' => $client->id_clients,
            'date_create' => $client->date_create,
            'date_approve' => $dataBeforeUpdate[0]['date_approve'],
            'fk_idUser' => $dataBeforeUpdate[0]['fk_idUser'],
            'fk_regoin_invoice' => $dataBeforeUpdate[0]['fk_regoin_invoice'],
            'fk_regoin' => $client->fk_regoin,
            'fkcountry' => 1,
            'lastuserupdate' => $dataBeforeUpdate[0]['lastuserupdate'],
            'lastnameuser' => $user,
            'id_invoice' => $dataBeforeUpdate[0]['id_invoice'],
            'date_lastuserupdate' => $dataBeforeUpdate[0]['date_lastuserupdate'],
        ];


        $values = $requestData['values'];
        info('values is:', $values);
        $keys = array_keys($dataBeforeUpdateHandeling);
        info('$keys are:', $keys);

        $data = [];
        $infoData = [];
        foreach ($values as $index => $value) {
            if ($index !== 'fkcountry' && $index !== 'date_lastuserupdate') {
                if ($value !== $dataBeforeUpdateHandeling[$index]) {
                    $changes[] = [
                        'before' => $dataBeforeUpdateHandeling[$index],
                        'after' => $value,
                        'infoKays' => $index,
                    ];
                    $infoData[] = [
                        'value' => $value,
                        'dataBeforeUpdate' => $dataBeforeUpdateHandeling[$index],
                        'infoKays' => $index,
                    ];
                }
            }
        }

        info('changes is:', $changes);

        $updates = [];
        foreach ($changes as $key => $change) {
            $updates[] = 'InvoiceNumber' . ' : ' . $request->id_invoice;
            if ($change['infoKays'] == 'fk_client') {
                $nameEnterPriseBefor = clients::where(
                    'id_clients',
                    is_array($change['after'])
                        ? json_encode($change['before'])
                        : $change['after']
                )
                    ->first()->name_enterprise;
                $nameEnterPriseAfter = clients::where(
                    'id_clients',
                    is_array($change['after'])
                        ? json_encode($change['after'])
                        : $change['after']
                )
                    ->first()->name_client;
                $infoKay = 'nameEnterPrise';
                $updates[] = $infoKay . ' : ' . $nameEnterPriseBefor . ',  name_client: ' . $nameEnterPriseAfter;
            } elseif($change['infoKays'] == 'fk_regoin_invoice'){
                $nameRegoinBefor = regoin::where(
                    'id_regoin',
                    is_array($change['before'])
                        ? json_encode($change['before'])
                        : $change['before']
                )
                    ->first()->name_regoin;
                $nameRegoinAfter = regoin::where(
                    'id_regoin',
                    is_array($change['after'])
                        ? json_encode($change['after'])
                        : $change['after']
                )
                    ->first()->name_regoin;
                $infoKay = 'nameUser';
                $updates[] = $infoKay . ' : ' . $nameRegoinBefor . ' TO ' . $nameRegoinAfter;
            } else {
                $before = is_array($change['before']) ? json_encode($change['before']) : $change['before'];
                $after = is_array($change['after']) ? json_encode($change['after']) : $change['after'];
                $infoKay = is_array($change['infoKays']) ? json_encode($change['infoKays']) : $change['infoKays'];
                $updates[] = $infoKay . ' : ' . $before . ' TO ' . $after;
            }
        }

        $InvoicesUpdates = implode("\n", $updates);

        $invoiceData = client_invoice::where('id_invoice', $request->id_invoice)->first();
        $isApprove = $invoiceData->isApprove === "1" ? 'true' : 'false';
        info('$invoiceData is: ', array($invoiceData));
        info('$invoiceData->isApprove is: ', array($invoiceData->isApprove));
        $invoicesUpdateReport = new invoicesUpdateReport();
        $invoicesUpdateReport->changesData = $InvoicesUpdates;
        $invoicesUpdateReport->afterApprove = $isApprove;
        $invoicesUpdateReport->edit_date = Carbon::now('Asia/Riyadh')->toDateTimeString();
        $invoicesUpdateReport->user_id = $request->fk_idUser;
        $invoicesUpdateReport->save();

        return $this->sendResponse($invoicesUpdateReport, 'Updated success');
    }
}
