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

        $updates = [];
        $id_invoice = null;
        foreach ($request->all() as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $updates[] = $key . ' : ' . $value;
            if ($key === 'id_invoice') {
                $id_invoice = $value;
            }
        }

        $InvoicesUpdates = implode("\n", $updates);

        $invoiceData = client_invoice::where('id_invoice', $id_invoice)->first();
        $isApprove = $invoiceData->isApprove === 1 ? true : false;

        $invoicesUpdateReport = new invoicesUpdateReport();
        $invoicesUpdateReport->changesData = $InvoicesUpdates;
        $invoicesUpdateReport->afterApprove = $isApprove;
        $invoicesUpdateReport->edit_date = Carbon::now('Asia/Riyadh')->toDateTimeString();
        $invoicesUpdateReport->user_id = $request->input('fk_idUser');
        $invoicesUpdateReport->save();

        return $this->sendResponse($invoicesUpdateReport, 'Updated success');
    }
}
