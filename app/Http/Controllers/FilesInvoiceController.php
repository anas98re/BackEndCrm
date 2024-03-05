<?php

namespace App\Http\Controllers;

use App\Models\files_invoice;
use App\Http\Requests\Storefiles_invoiceRequest;
use App\Http\Requests\Updatefiles_invoiceRequest;
use Illuminate\Support\Facades\DB;

class FilesInvoiceController extends Controller
{
    //thses Api's for support employees

    private function handlingfileInvoiceName($file)
    {
        $originalFilename = $file->getClientOriginalName();
        $fileExtension = $file->getClientOriginalExtension();
        $randomNumber = mt_rand(10000, 99999);

        // Remove the file extension from the original filename
        $filenameWithoutExtension = pathinfo($originalFilename, PATHINFO_FILENAME);

        $modifiedFilename = str_replace(' ', '_', $filenameWithoutExtension) . '_' . $randomNumber;

        // Combine the filename and extension
        $generatedFilename = $modifiedFilename . '.' . $fileExtension;

        // Store the file with the modified filename
        $generatedPath = $file->storeAs('invoiceFiles', $generatedFilename);
        return $generatedPath;
    }

    public function addInvoiceFiles(Storefiles_invoiceRequest $request)
    {
        try {
            DB::beginTransaction();

            $fileInvoice = [];
            foreach ($request->file('file_attach_invoice') as $index => $file) {
                $filsHandled = $this->handlingfileInvoiceName($file);

                $fileInvoice[$index] = new files_invoice();
                $fileInvoice[$index]->file_attach_invoice = $filsHandled;
                $fileInvoice[$index]->fk_invoice = $request->input("fk_invoice.$index");
                $fileInvoice[$index]->is_support_employee = 1;
                $fileInvoice[$index]->save();
            }

            DB::commit();
            return response()->json($fileInvoice, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function updateInvoiceFiles(Updatefiles_invoiceRequest $request, files_invoice $files_invoice)
    {
        //
    }

    public function deleteInvoiceFiles(files_invoice $files_invoice)
    {
        //
    }
}
