<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\files_invoice;
use App\Http\Requests\Storefiles_invoiceRequest;
use App\Http\Requests\Updatefiles_invoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\client_invoice;
use App\Models\clients;
use App\Models\invoice_product;
use App\Models\task;
use App\Models\users;
use App\Services\AppSrevices;
use App\Services\invoicesSrevices;
use App\Services\TaskManangement\TaskProceduresService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FilesInvoiceController extends Controller
{
    //thses Api's for support employees
    private $myService;
    private $MyService;
    private $invoiceSrevice;

    public function __construct(AppSrevices $myService, invoicesSrevices $invoiceSrevice, TaskProceduresService $MyService)
    {
        $this->myService = $myService;
        $this->MyService = $MyService;
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
        $fk_invoice = request()->query('fk_invoice');
        $filesInvoices = files_invoice::where('type_file', 1)
            ->where('fk_invoice', $fk_invoice)
            ->get();
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

    public function addInvoice(Request $request)
    {
        DB::beginTransaction();
        $data = $request->all();
        try
        {
            $data['date_create'] = Carbon::now()->format("Y-m-d H:i:s");
            $data['type_pay'] = $data['type_pay'] ?? 0;
            $data['type_installation'] = $data['type_installation'] ?? 0;
            $data['amount_paid'] = $data['amount_paid'] ?? 0;

            // add invoice and get invoice_id
            if(key_exists('date_not_readyinstall', $request->all()))
                $invoice = client_invoice::create([
                    'date_create' => $data['date_create'],
                    'invoice_source' => $data['invoice_source'] ?? null,
                    'type_pay' => $data['type_pay'],
                    'renew_year' => $data['renew_year'] ?? '',
                    'renew2year' => $data['renew2year'] ?? '',
                    'renew_pluse' => $data['renew_pluse'] ?? '',
                    'participate_fk' => $data['participate_fk'] ?? null,
                    'fk_agent' => $data['fk_agent'] ?? null,
                    'type_seller' => $data['type_seller'] ?? null,
                    'rate_participate' => $data['rate_participate'] ?? null,
                    'fk_regoin_invoice' => $data['fk_regoin_invoice'], // required
                    'type_installation' => $data['type_installation'],
                    'image_record' => $data['image_record']?? '',
                    'fk_idClient' => $data['fk_idClient'], // required
                    'fk_idUser' => $data['fk_idUser'],// auth()->user()->id_user,
                    'amount_paid' => $data['amount_paid'],
                    'notes' => $data['notes'] ?? '',
                    'total' => $data['total']?? '',
                    'address_invoice' => $data['address_invoice'], // required,
                    'numbarnch' => $data['numbarnch'], // required
                    'nummostda' => $data['nummostda'], // required
                    'numusers' => $data['numusers'], // required
                    'imagelogo' => $data['imagelogo'] ?? '',
                    'stateclient' => 'مشترك',
                    'numTax' => $data['numTax'], // required,
                    'ready_install' => $data['ready_install'], // required
                    'currency_name' => $data['currency_name'], //required
                    'renew_agent' => $data['renew_agent']?? null,
                    'file_attach' => '',
                    'date_not_readyinstall' => $data['date_not_readyinstall'], // required,
                    'user_not_ready_install' => $data['user_not_ready_install'], // required
                ]);
            else
                $invoice = client_invoice::create([
                    'date_create' => $data['date_create'],
                    'invoice_source' => $data['invoice_source'] ?? null,
                    'type_pay' => $data['type_pay'],
                    'renew_year' => $data['renew_year'] ?? '',
                    'renew2year' => $data['renew2year'] ?? '',
                    'participate_fk' => $data['participate_fk'] ?? null,
                    'fk_agent' => $data['fk_agent'] ?? null,
                    'type_seller' => $data['type_seller'] ?? null,
                    'rate_participate' => $data['rate_participate'] ?? null,
                    'fk_regoin_invoice' => $data['fk_regoin_invoice'], // required
                    'type_installation' => $data['type_installation'],
                    'image_record' => $data['image_record']?? '',
                    'fk_idClient' => $data['fk_idClient'], // required
                    'fk_idUser' => $data['fk_idUser'],// auth()->user()->id_user,
                    'amount_paid' => $data['amount_paid'],
                    'notes' => $data['notes'] ?? '',
                    'total' => $data['total']?? '',
                    'address_invoice' => $data['address_invoice'], // required,
                    'numbarnch' => $data['numbarnch'], // required
                    'nummostda' => $data['nummostda'], // required
                    'numusers' => $data['numusers'], // required
                    'imagelogo' => $data['imagelogo'] ?? '',
                    'stateclient' => 'مشترك',
                    'numTax' => $data['numTax'], // required,
                    'ready_install' => $data['ready_install'], // required
                    'currency_name' => $data['currency_name'], //required
                    'renew_agent' => $data['renew_agent']?? null,
                    'file_attach' => '',
                    'renew_pluse' => $data['renew_pluse']?? null,
                    'date_readyinstall' => null,
                    'user_ready_install' => null,
                ]);

            $client = clients::where('id_clients', $data['fk_idClient'])->first();
            $client->update(['type_client' => 'مشترك']);

            addComment($data['comment'], $data['fk_idClient'], $data['fk_idUser'], 'متطلبات العميل');

            foreach($request['products'] as $product)
            {
                $insertArray = array();
                $insertArray['amount'] = $product['amount']?? 0;
                $insertArray['price'] = $product['price']?? 0;
                $insertArray['fk_id_invoice'] = $invoice->id_invoice;
                $insertArray['fk_product'] = $product['fk_product'];
                $insertArray['taxtotal'] = $product['taxtotal']?? 0.0;
                $insertArray['rate_admin'] = $product['rate_admin']?? 0.0;
                $insertArray['rateUser'] = $product['rateUser']?? 0.0;

                invoice_product::create($insertArray);
            }

            // DB::commit();
            // $resJson = array("result" => "success", "code" => "200", "message" => new InvoiceResource($invoice));
            // echo json_encode($resJson, JSON_UNESCAPED_UNICODE);

            $data['image_record'] = '';
            $data['imagelogo'] = '';
            if(key_exists('file', $request->all()))
            {
                $filsHandled = $this->myService->storeFile($request->file, 'invoices');
                $data['image_record'] = $filsHandled;
            }
            if(key_exists('logo', $request->all()))
            {
                $filsHandled = $this->myService->storeThumbnail($request->logo, 'logo_client', 200);
                $data['imagelogo'] = $filsHandled;
            }
            $invoice->update([
                'image_record' => $data['image_record'],
                'imagelogo' => $data['imagelogo'],
            ]);

            if(key_exists('uploadfiles', $request->all()))
            {
                foreach($request->uploadfiles as $file)
                {
                    $filsHandled = $this->myService->storeFile($file, 'invoices');
                    $fileInvoice = new files_invoice([
                        'fk_invoice' => $invoice->id_invoice,
                        'file_attach_invoice' => $filsHandled,
                    ]);
                }
            }

            $this->addTaskToApproveAdminAfterAddInvoice($invoice->id_invoice, $data['fk_regoin']);

            // ------------ notification -------------
            $fk_regoin = $_POST['fk_regoin']; //fk_regoin_invoice
            $fk_country = $_POST['fkcountry']; //owner not related in regoin
            $name_enterprise = $client->name_enterprise;
            $title_name_approve = "تم إنشاء فاتورة للعميل ";
            $name_user = $_POST['nameUser']; //موظف المبيعات
            $message = "$title_name_approve $name_enterprise من قبل \r $name_user";


            $user_ids =  getIdUsers($fk_regoin, 10, $fk_country);
            $tokens = getTokens($user_ids);
            $title = "طلب موافقة";
            $this->invoiceSrevice->sendNotification(
                $tokens,
                $client->id_clients,
                'ApproveRequest',
                $title,
                $message,
            );

            $arrayUser =  getIdUsers($fk_regoin, 111, $fk_country);
            $title = "طلب موافقة المالية";
            $tokens =  getTokens($arrayUser);
            $this->invoiceSrevice->sendNotification(
                $tokens,
                $client->id_clients,
                'ApproveFRequest',
                $title,
                $message,
            );

            DB::commit();
            return response()->json(['message' => new InvoiceResource($invoice), 'result' => 'success']);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function addTaskToApproveAdminAfterAddInvoice($invoice_id, $fk_regoin)
    {
        try {
            // Retrieve assigned users based on region and type level
            $assigneds_to = users::where('fk_regoin', $fk_regoin)
                ->where('type_level', Constants::ALL_BRUNSHES)->get();

            $invoice = client_invoice::where('id_invoice', $invoice_id)->first();
            $client = clients::where('id_clients', $invoice->fk_idClient)->first();
            $message = 'يوجد فاتورة للعميل ( ? ) بانتظار الموافقة';
            $messageDescription = str_replace('?', $client->name_enterprise, $message);
            // Check if $assigneds_to is empty
            if ($assigneds_to->isEmpty()) {

                $usersIdsManagerSuprevisor = users::where('type_level', 9)
                    ->Where('type_administration', Constants::TYPE_ADMINISTRATION['SALES_MANAGEMENT'])
                    ->get();

                foreach ($usersIdsManagerSuprevisor as $user) {
                    $existingTask = task::where('invoice_id', $invoice_id)
                        ->where('public_Type', 'approveAdmin')
                        ->where('assigned_to', $user->id_user)
                        ->first();
                    if (!$existingTask) {
                        $task = new task();
                        $task->title = 'موافقة المشرف';
                        $task->description = $messageDescription;
                        $task->invoice_id = $invoice_id;
                        $task->public_Type = 'approveAdmin';
                        $task->main_type_task = 'ProccessAuto';
                        $task->assigend_department_from  = 2;
                        $task->assigend_department_to = 2;
                        $task->start_date = Carbon::now('Asia/Riyadh');
                        $task->save();

                        // Add task status and handle notifications
                        $this->MyService->addTaskStatus($task);
                        $this->MyService->handleNotificationForTaskProcedures(
                            $message = $task->title,
                            $type = 'task',
                            $to_user = $user->id_user,
                            $invoice_id = $invoice_id,
                            $client_id = $client->id_clients
                        );
                    }
                }
                return true;
            } else {
                foreach ($assigneds_to as $assigned_to) {
                    // Check if task already exists for the invoice and assigned user
                    $existingTask = Task::where('invoice_id', $invoice_id)
                        ->where('public_Type', 'approveAdmin')
                        ->where('assigned_to', $assigned_to->id_user)
                        ->first();

                    // If task doesn't exist, create a new task
                    if (!$existingTask) {

                        $task = new task();
                        $task->title = 'موافقة المشرف';
                        $task->description = $messageDescription;
                        $task->invoice_id = $invoice_id;
                        $task->public_Type = 'approveAdmin';
                        $task->main_type_task = 'ProccessAuto';
                        $task->assigend_department_from  = 2;
                        $task->assigned_to = $assigned_to->id_user;
                        $task->start_date = Carbon::now('Asia/Riyadh');
                        $task->save();

                        // Add task status and handle notifications
                        $this->MyService->addTaskStatus($task);
                        $this->MyService->handleNotificationForTaskProcedures(
                            $message = $task->title,
                            $type = 'task',
                            $to_user = $assigned_to->id_user,
                            $invoice_id = $invoice_id,
                            $client_id = $client->id_clients
                        );
                    }
                }
            }

            // Return the last created task
            return $task ?? null;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
