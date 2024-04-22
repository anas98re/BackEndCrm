<?php

namespace App\Services;

use App\Models\files_invoice;
use App\Models\notifiaction;
use App\Models\user_token;
use App\Notifications\SendNotification;
use App\Services\JsonResponeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class invoicesSrevices extends JsonResponeService
{
    private $myService;

    public function __construct(AppSrevices $myService)
    {
        $this->myService = $myService;
    }

    public function sendNotification($tokens ,$id_client, $type ,$title, $message)
    {
        
        foreach($tokens as $token)
        {
            Notification::send(
                null,
                new SendNotification(
                    $title,
                    $message,
                    $message,
                    $token,
                )
            );
        }
        return $this;
    }

    public function storeNotification($user_ids, $message, $type, $id_client)
    {
        foreach($user_ids as $user_id)
        {
            $notification = notifiaction::create([
                'message' => $message,
                'type_notify' => $type,
                'to_user' => $user_id,
                'isread' => 0,
                'data' => $id_client,
                'from_user' => auth()->user()->id_user,
                'dateNotify' => Carbon::now('Asia/Riyadh')
            ]);
        }
    }

    public function addAndUpdateInvoiceFiles($filesDelete, $filesAdd, $invoiceId)
    {
        try {
            DB::beginTransaction();
            $response = '';
            if (!empty($filesDelete) && is_array($filesDelete)) {
                foreach ($filesDelete as $fileId) {
                    $fileInvoice = files_invoice::where('id', $fileId)->first();
                    if ($fileInvoice) {
                        $oldFilePath = $fileInvoice->file_attach_invoice;
                        Storage::delete('public/' . $oldFilePath);
                        $fileInvoice->delete();
                    }
                }
                $response = 'deleteed successfully';
            }
            info($filesAdd);
            $fileInvoice = [];
            if (!empty($filesAdd) && is_array($filesAdd)) {
                foreach ($filesAdd as $index => $file) {
                    $filsHandled = $this->myService->handlingfileInvoiceName($file);

                    $fileInvoice[$index] = new files_invoice();
                    $fileInvoice[$index]->file_attach_invoice = $filsHandled;
                    $fileInvoice[$index]->fk_invoice = $invoiceId;
                    $fileInvoice[$index]->type_file = 1;
                    // $fileInvoice[$index]->add_date = Carbon::now()->toDateTimeString();
                    $fileInvoice[$index]->save();
                }
                $response = $fileInvoice;
            }
            DB::commit();
            return $response;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
