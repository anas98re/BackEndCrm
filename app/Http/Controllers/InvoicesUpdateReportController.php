<?php

namespace App\Http\Controllers;

use App\Models\invoicesUpdateReport;
use App\Http\Requests\StoreinvoicesUpdateReportRequest;
use App\Http\Requests\UpdateinvoicesUpdateReportRequest;
use App\Models\client_invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoicesUpdateReportController extends Controller
{
    public function storageInvoicesUpdates(Request $request)
    {
        info('$request->all() for storageInvoicesUpdates:', $request->all());

        $requestData = json_decode($request->getContent(), true);
        $dataBeforeUpdate = json_decode($requestData['dataBeforeUpdate'], true);
        info('dataBeforeUpdate is:', $dataBeforeUpdate);
        info('requestData is:', $requestData);
        $keys = $requestData['keys'];
        $values = $requestData['values'];
        info('keys is:', $keys);
        info('values is:', $values);
        $data = [];
        foreach ($keys as $index => $key) {
            $value = $values[$index];
            $data[$key] = $value;
        }

        $changes = [];
        foreach ($dataBeforeUpdate as $key => $value) {
            if (isset($data[$key]) && $data[$key] !== $value) {
                $changes[$key] = [
                    'before' => $value,
                    'after' => $data[$key],
                ];
            }
        }

        $updates = [];
        foreach ($changes as $key => $change) {
            $updates[] = $key . ' : ' . $change['before'] . ' -> ' . $change['after'];
        }

        $InvoicesUpdates = implode("\n", $updates);
        $InvoicesChanges = implode("\n", $changes);

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
