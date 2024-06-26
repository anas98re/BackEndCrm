<?php

namespace App\Http\Controllers;

use App\Models\invoicesUpdateReport;
use App\Http\Requests\StoreinvoicesUpdateReportRequest;
use App\Http\Requests\UpdateinvoicesUpdateReportRequest;
use App\Jobs\StorageInvoicesUpdatesJob;
use App\Models\client_invoice;
use App\Models\clients;
use App\Models\invoice_product;
use App\Models\regoin;
use App\Models\users;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoicesUpdateReportController extends Controller
{
    //all down to testing
    public function storageInvoicesUpdates1(Request $request)
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
        $client = clients::where('id_clients', $dataBeforeUpdate[0]['fk_idClient'])->first();
        //     }
        // }
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
            if ($change['infoKays'] == 'fk_idUser') {
                $nameUserBefor = users::where(
                    'id_user',
                    is_array($change['before'])
                        ? json_encode($change['before'])
                        : $change['before']
                )
                    ->first()->nameUser;
                $nameUserAfter = users::where(
                    'id_user',
                    is_array($change['after'])
                        ? json_encode($change['after'])
                        : $change['after']
                )
                    ->first()->nameUser;
                $infoKay = 'nameUser';
                $updates[] = $infoKay . ' : ' . $nameUserBefor . ' TO ' . $nameUserAfter;
            } elseif ($change['infoKays'] == 'fk_regoin_invoice') {
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
                $infoKay = 'nameRegoin';
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
        $invoicesUpdateReport->invoice_id = $request->id_invoice;
        $invoicesUpdateReport->edit_date = Carbon::now('Asia/Riyadh')->toDateTimeString();
        $invoicesUpdateReport->user_id = $request->fk_idUser;
        $invoicesUpdateReport->save();

        return $this->sendResponse($invoicesUpdateReport, 'Updated success');
    }

    public function storageInvoicesUpdates2(Request $request)
    {
        info('$request->all() for storageInvoicesUpdates:', $request->all());
        $dataBeforeUpdate = json_decode($request->input('dataBeforeUpdate'), true)[0];
        $dataAfterUpdate = json_decode($request->input('dataAfterUpdate'), true)[0];

        $differences = array_diff_assoc($dataAfterUpdate, $dataBeforeUpdate);
        info('$differences for invoicess: ', array($differences));
        $report = [];
        foreach ($differences as $key => $value) {
            // if ($key == 'city') {
            //     $cityValue = city::where('id_city', $value)->first()->name_city;
            //     $report[] = $key . ' ( ' . $cityValue . ' ) ';
            // } elseif ($key == 'activity_type_fk') {
            //     $id_activity_type_value = activity_type::where('id_activity_type', $value)
            //         ->first()->name_activity_type;
            //     $report[] = 'activity_type' . ' ( ' . $id_activity_type_value . ' ) ';
            // } else {
            $report[] = $key . ' ( ' . $value . ' ) ';
            // }
        }
        info('$report for invoicess: ', array($report));
        $reportMessage = implode("\n", $report);

        $invoiceData = client_invoice::where('id_invoice', $request->id_invoice)->first();
        $isApprove = $invoiceData->isApprove === "1" ? 'true' : 'false';
        info('$invoiceData is: ', array($invoiceData));
        info('$invoiceData->isApprove is: ', array($invoiceData->isApprove));
        $invoicesUpdateReport = new invoicesUpdateReport();
        $invoicesUpdateReport->changesData = $reportMessage;
        $invoicesUpdateReport->afterApprove = $isApprove;
        $invoicesUpdateReport->invoice_id = $request->id_invoice;
        $invoicesUpdateReport->edit_date = Carbon::now('Asia/Riyadh')->toDateTimeString();
        $invoicesUpdateReport->user_id = $request->fk_idUser;
        $invoicesUpdateReport->save();
    }

    public function storageInvoicesUpdates(Request $request)
    {
        $invoiceId = $request->input('id_invoice');
        $dataBeforeUpdate = json_decode($request->input('dataBeforeUpdate'), true)[0];
        $dataAfterUpdate = json_decode($request->input('dataAfterUpdate'), true)[0];
        $dateUpdate = Carbon::now('Asia/Riyadh')->toDateTimeString();
        if ($request->input('fk_idUser') != 'Error: Failed to fetch data from the API') {
            $userId = $request->input('fk_idUser');
        } else {
            $userId = null;
        }
        $update_source = 'تغيير بيانات الفاتورة';
        $id_invoice_product = null;
        StorageInvoicesUpdatesJob::dispatch(
            $invoiceId,
            $dataBeforeUpdate,
            $dataAfterUpdate,
            $dateUpdate,
            $userId,
            $update_source,
            $id_invoice_product
        );
    }
    
    public function addInvoicesUpdateReport(Request $request)
    {
        info('request->all() for addInvoicesUpdateReport:', $request->all());
        $invoiceId = $request->input('id_invoice');
        $dataBeforeUpdate = json_decode($request->input('dataBeforeUpdate'), true)[0];
        $dataAfterUpdate = json_decode($request->input('dataAfterUpdate'), true)[0];
        $dateUpdate = $request->input('dateUpdate');
        if ($request->input('fk_idUser') != 'Error: Failed to fetch data from the API') {
            $userId = $request->input('fk_idUser');
        } else {
            $userId = null;
        }
        $update_source = 'تعديل الفاتورة';

        $id_invoice_product = null;
        StorageInvoicesUpdatesJob::dispatch(
            $invoiceId,
            $dataBeforeUpdate,
            $dataAfterUpdate,
            $dateUpdate,
            $userId,
            $update_source,
            $id_invoice_product
        );
        info('third');
    }

    public function addInvoiceProductReport(Request $request)
    {
        info('request->all() for addInvoiceProductReport:', $request->all());
        $id_invoice_product = $request->input('id_invoice_product');
        $dataBeforeUpdate = json_decode($request->input('dataBeforeUpdate'), true)[0];
        $dataAfterUpdate = json_decode($request->input('dataAfterUpdate'), true)[0];
        $dateUpdate = Carbon::now('Asia/Riyadh')->toDateTimeString();
        $fk_user_update = $request->input('fk_user_update');

        $update_source = 'تعديل منتجات الفاتورة';

        $invoiceId = invoice_product::where('id_invoice_product', $id_invoice_product)
            ->first()->fk_id_invoice;
        StorageInvoicesUpdatesJob::dispatch(
            $invoiceId,
            $dataBeforeUpdate,
            $dataAfterUpdate,
            $dateUpdate,
            $fk_user_update,
            $update_source,
            $id_invoice_product
        );
    }
}
