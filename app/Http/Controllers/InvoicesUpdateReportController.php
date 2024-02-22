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
        info('$request->all() for storageInvoicesUpdates:',$request->all());
        $updates = [];
        foreach ($request->all()  as $key => $value) {

            $updates[] = $key . ' : ' . $value ;

        }
        $InvoicesUpdates = implode("\n", $updates);

        $invoiceData = client_invoice::where('id_invoice',$request->id_invoice)->first();
        $isApprove = $invoiceData->isApprove === 1 ? true : false;

        $invoicesUpdateReport = new invoicesUpdateReport();
        $invoicesUpdateReport->changesData = $InvoicesUpdates;
        $invoicesUpdateReport->afterApprove = $isApprove;
        $invoicesUpdateReport->edit_date = Carbon::now('Asia/Riyadh')->toDateTimeString();
        $invoicesUpdateReport->user_id = $request->fk_idUser;
        $invoicesUpdateReport->save();

        return $this->sendResponse($invoicesUpdateReport, 'Updated success');
    }
}
