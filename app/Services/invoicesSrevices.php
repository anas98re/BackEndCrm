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

    public function addAndUpdateInvoiceFiles($filesDelete, $filesAdd, $invoiceId)
    {
        try {
            DB::beginTransaction();

            if (!empty($filesDelete) && is_array($filesDelete)) {
                foreach ($filesDelete as $fileId) {
                    $fileInvoice = files_invoice::where('id', $fileId)->first();
                    if ($fileInvoice) {
                        $oldFilePath = $fileInvoice->file_attach_invoice;
                        Storage::delete($oldFilePath);
                        $fileInvoice->delete();
                    }
                }
            }
            info($filesAdd);
            $fileInvoice = [];
            foreach ($filesAdd as $index => $file) {
                $filsHandled = $this->myService->handlingfileInvoiceName($file);

                $fileInvoice[$index] = new files_invoice();
                $fileInvoice[$index]->file_attach_invoice = $filsHandled;
                $fileInvoice[$index]->fk_invoice = $invoiceId;
                $fileInvoice[$index]->is_support_employee = 1;
                $fileInvoice[$index]->save();
            }

            DB::commit();
            return $fileInvoice;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
