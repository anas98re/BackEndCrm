<?php

namespace App\Http\Controllers;

use App\Models\payment_detail;
use App\Http\Requests\Storepayment_detailRequest;
use App\Http\Requests\Updatepayment_detailRequest;
use App\Models\client_invoice;
use Illuminate\Http\Request;

class PaymentDetailController extends Controller
{
    public function createPaymentDetails(Request $request)
    {
        info('request all for PaymentDetails: ' . json_encode($request->all()));
        $data = $request->all();
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
