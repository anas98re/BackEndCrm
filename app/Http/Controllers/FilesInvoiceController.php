<?php

namespace App\Http\Controllers;

use App\Models\files_invoice;
use App\Http\Requests\Storefiles_invoiceRequest;
use App\Http\Requests\Updatefiles_invoiceRequest;
use App\Services\AppSrevices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FilesInvoiceController extends Controller
{
    private $myService;

    public function __construct(AppSrevices $myService)
    {
        $this->myService = $myService;
    }

    //thses Api's for support employees
    public function addInvoiceFiles(Storefiles_invoiceRequest $request)
    {
        try {
            DB::beginTransaction();

            $fileInvoice = [];
            foreach ($request->file('file_attach_invoice') as $index => $file) {
                $filsHandled = $this->myService->handlingfileInvoiceName($file);

                $fileInvoice[$index] = new files_invoice();
                $fileInvoice[$index]->file_attach_invoice = $filsHandled;
                $fileInvoice[$index]->fk_invoice = $request->input("fk_invoice.$index");
                $fileInvoice[$index]->is_support_employee = 1;
                $fileInvoice[$index]->save();
            }

            DB::commit();
            return $this->sendSucssas($fileInvoice);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function updateInvoiceFile(Updatefiles_invoiceRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $filsHandled = $this->myService->handlingfileInvoiceName($request->file_attach_invoice);

            $fileInvoice = files_invoice::where('id', $id)->first();
            $oldFilePath = $fileInvoice->file_attach_invoice;

            $fileInvoice->file_attach_invoice = $filsHandled;
            $fileInvoice->fk_invoice = $request->fk_invoice;
            $fileInvoice->save();

            Storage::delete($oldFilePath);

            DB::commit();
            return $this->sendSucssas($fileInvoice);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function deleteInvoiceFile($id)
    {
        try {
            DB::beginTransaction();
            $fileInvoice = files_invoice::where('id', $id)->first();
            $oldFilePath = $fileInvoice->file_attach_invoice;
            Storage::delete($oldFilePath);
            $fileInvoice->delete();
            DB::commit();
            return $this->sendSucssas('Deleted dode');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
