<?php

namespace App\Http\Controllers;

use App\Models\payment_detail;
use App\Http\Requests\Storepayment_detailRequest;
use App\Http\Requests\Updatepayment_detailRequest;
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
}
