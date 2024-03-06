<?php

namespace App\Http\Controllers;

use App\Models\payment_detail;
use App\Http\Requests\Storepayment_detailRequest;
use App\Http\Requests\Updatepayment_detailRequest;
use App\Models\client_invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentDetailController extends Controller
{
    public function createPaymentDetails(Request $request)
    {
        info('request all for PaymentDetails: ' . json_encode($request->all()));
        // if ($request->isRefresh) {
        //     $amountPaidRequest = (float) $request->amount_paid;

        //     $clientInvoice = client_invoice::where('id_invoice', $request->fk_invoice)->first();
        //     $amountPaidClientInvoice = (float) $clientInvoice->amount_paid;
        //     $amountPaid = $amountPaidRequest - $amountPaidClientInvoice;

        //     $data = [
        //         'payment_idAdd' => $request->payment_idAdd,
        //         'fk_invoice' => $request->fk_invoice,
        //         'payment_date' => $request->payment_date,
        //         'date_updatePayment' => Carbon::now('Asia/Riyadh'),
        //         'amount_paid' => $amountPaid,
        //     ];
        // } else {
            $data = [
                'payment_idAdd' => $request->payment_idAdd,
                'fk_invoice' => $request->fk_invoice,
                'payment_date' => $request->payment_date,
                'date_updatePayment' => Carbon::now('Asia/Riyadh'),
                'amount_paid' => $request->amount_paid,
            ];
        // }
        payment_detail::create($data);
        info('Done PaymentDetails added successfully');
    }

    public function getPaymaentsViaInvoiceId($id)
    {
        $paymentDetails = payment_detail::where('fk_invoice', $id)->with('users:id_user,nameUser')->get();
        $invoiceName = client_invoice::where('id_invoice', $id)->first()->name_enterpriseinv;
        // $userNameAdd = ;

        $data = $paymentDetails->map(function ($payment) use ($invoiceName) {
            $payment['invoice_name_enterpriseinv'] = $invoiceName;
            $payment['nameUserPaymentAdd'] = $payment->users ? $payment->users->nameUser : null;
            unset($payment->users);
            return $payment;
        });

        return $this->sendResponse($data, 'done');
    }
}
