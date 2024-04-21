<?php

namespace App\Services;

use App\Models\files_invoice;
use App\Services\JsonResponeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class invoicesSrevices extends JsonResponeService
{
    private $myService;

    public function __construct(AppSrevices $myService)
    {
        $this->myService = $myService;
    }

    public function sendNotification($title, $message, )

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
