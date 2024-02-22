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
        // $keys = $requestData['keys'];
        $values = $requestData['values'];
        // info('keys is:', $keys);
        info('values is:', $values);
        $data = [];
        foreach ($values as $key => $value) {
            $data[$key] = $value;
        }
        info('data is:', $data);
        $changes = [];
        foreach ($dataBeforeUpdate as $key => $value) {
            if (isset($data[$key]) && $data[$key] !== $value) {
                $changes[$key] = [
                    'before' => $value,
                    'after' => $data[$key],
                ];
            }
        }

        info('changes is:', $changes);

        $updates = [];
        foreach ($changes as $key => $change) {
            $before = is_array($change['before']) ? json_encode($change['before']) : $change['before'];
            $after = is_array($change['after']) ? json_encode($change['after']) : $change['after'];
            $updates[] = $key . ' : ' . $before . ' -> ' . $after;
        }

        $InvoicesUpdates = implode("\n", $updates);
        // $InvoicesChanges = implode("\n", $changes);

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
