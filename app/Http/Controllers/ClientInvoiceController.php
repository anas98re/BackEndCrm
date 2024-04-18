<?php

namespace App\Http\Controllers;

use App\Models\client_invoice;
use App\Http\Requests\Storeclient_invoiceRequest;
use App\Http\Requests\Updateclient_invoiceRequest;
use App\Models\clients;
use App\Models\notifiaction;
use App\Models\privileges;
use App\Models\regoin;
use App\Models\users;
use App\Notifications\SendNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ClientInvoiceController extends Controller
{
    public function deleteInvoice($id_invoice)
    {
        $user_delete = auth('sanctum')->user()->id_user;

        $result = client_invoice::where('id_invoice', $id_invoice)
            ->update([
                'isdelete' => 1,
                'date_delete' => now(),
                'user_delete' => $user_delete
            ]);

        $invoice = client_invoice::where('id_invoice', $id_invoice)->first();

        $fk_user = $user_delete;
        $fk_idClient = $invoice->fk_idClient;
        $data = $this->checkstate($fk_idClient);
        if (count($data) <= 0) {
            $client = clients::find($fk_idClient);
            if ($client) {
                $client->type_client = 'تفاوض';
                $client->save();
            }
        }


        $fk_regoin = clients::find($fk_idClient)->fk_regoin;
        $fkcountry = regoin::find($fk_regoin)->fk_country;

        $Ids =  getIdUsers($fk_regoin, 56, $fkcountry);
        $id_users = collect($Ids)->unique();
        $name_enterprise = clients::find($fk_idClient)->name_enterprise;
        $nameuser = users::find($fk_user)->nameUser;
        $message = 'تم حذف فاتورة العميل من قبل (?) من قبل (!) ';
        $messageDescription = str_replace('?', $name_enterprise, $message);
        $fullMessage = str_replace('!', $nameuser, $messageDescription);
        $type = 'InvoiceDeleted';

        foreach ($id_users as $user_id) {
            $userToken = DB::table('user_token')->where('fkuser', $user_id)
                ->where('token', '!=', null)
                ->latest('date_create')
                ->first();
            Notification::send(
                null,
                new SendNotification(
                    'حذف فاتورة',
                    $fullMessage,
                    $fullMessage,
                    ($userToken != null ? $userToken->token : null)
                )
            );

            notifiaction::create([
                'message' => $fullMessage,
                'type_notify' => $type,
                'to_user' => $user_id,
                'isread' => 0,
                'data' => 'Tsk',
                'from_user' => $fk_user,
                'dateNotify' => Carbon::now('Asia/Riyadh')
            ]);
        }

        return $result ? $this->sendSucssas('Done') : $this->sendSucssas('Fail');
    }

    private function checkState($idClient)
    {
        $arrJson = clients::join(
            'client_invoice',
            'clients.id_clients',
            '=',
            'client_invoice.fk_idClient'
        )
            ->select('client_invoice.stateclient')
            ->where('clients.id_clients', $idClient)
            ->get();

        return $arrJson;
    }
}
