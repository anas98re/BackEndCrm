<?php

namespace App\Http\Controllers;

use App\Models\files_invoice;
use App\Http\Requests\Storefiles_invoiceRequest;
use App\Http\Requests\Updatefiles_invoiceRequest;
use App\Services\AppSrevices;
use App\Services\invoicesSrevices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FilesInvoiceController extends Controller
{
    //thses Api's for support employees
    private $myService;
    private $invoiceSrevice;

    public function __construct(AppSrevices $myService, invoicesSrevices $invoiceSrevice)
    {
        $this->myService = $myService;
        $this->invoiceSrevice = $invoiceSrevice;
    }

    //this is the api we use for all procesess add, update and delete..
    public function InvoiceFiles(Storefiles_invoiceRequest $request)
    {
        $filesDelete = json_decode($request->input('files_delete_ids'));
        $filesAdd = $request->file('file_attach_invoice');
        $invoiceId = $request->input("fk_invoice");
        $data = $this->invoiceSrevice
            ->addAndUpdateInvoiceFiles($filesDelete, $filesAdd, $invoiceId);
        return $this->sendSucssas($data);
    }

    public function getFilesInvoices()
    {
        $filesInvoices = files_invoice::where('type_file', 1)->get();
        return $this->sendSucssas($filesInvoices);
    }

    //for test if we want just add files without update or delete
    public function addInvoiceFiles(Storefiles_invoiceRequest $request)
    {
        try {
            DB::beginTransaction();
            info($request->file('file_attach_invoice'));
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

    //just update, for test
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

    //just delete, for test
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
