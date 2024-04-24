<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\client_invoice;
use App\Http\Requests\Storeclient_invoiceRequest;
use App\Http\Requests\Updateclient_invoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\InvoiceResourceForGetInvoicesByPrivilages;
use App\Models\clients;
use App\Models\files_invoice;
use App\Models\invoice_product;
use App\Models\notifiaction;
use App\Models\privg_level_user;
use App\Models\privileges;
use App\Models\regoin;
use App\Models\task;
use App\Models\users;
use App\Notifications\SendNotification;
use App\Services\AppSrevices;
use App\Services\invoicesSrevices;
use App\Services\TaskManangement\TaskProceduresService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ClientInvoiceController extends Controller
{
    private $myService;
    private $MyService;
    private $invoiceSrevice;

    public function __construct(AppSrevices $myService, invoicesSrevices $invoiceSrevice, TaskProceduresService $MyService)
    {
        $this->myService = $myService;
        $this->MyService = $MyService;
        $this->invoiceSrevice = $invoiceSrevice;
    }
    public function addInvoice(Request $request)
    {
        DB::beginTransaction();
        $data = $request->all();
        try {
            $data['date_create'] = Carbon::now()->format("Y-m-d H:i:s");
            $data['type_pay'] = $data['type_pay'] ?? 0;
            $data['type_installation'] = $data['type_installation'] ?? 0;
            $data['amount_paid'] = $data['amount_paid'] ?? 0;

            // add invoice and get invoice_id
            if (key_exists('date_not_readyinstall', $request->all()))
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
                    'image_record' => $data['image_record'] ?? '',
                    'fk_idClient' => $data['fk_idClient'], // required
                    'fk_idUser' => $data['fk_idUser'], // auth()->user()->id_user,
                    'amount_paid' => $data['amount_paid'],
                    'notes' => $data['notes'] ?? '',
                    'total' => $data['total'] ?? '',
                    'address_invoice' => $data['address_invoice'], // required,
                    'numbarnch' => $data['numbarnch'], // required
                    'nummostda' => $data['nummostda'], // required
                    'numusers' => $data['numusers'], // required
                    'imagelogo' => $data['imagelogo'] ?? '',
                    'stateclient' => 'مشترك',
                    'numTax' => $data['numTax'], // required,
                    'ready_install' => $data['ready_install'], // required
                    'currency_name' => $data['currency_name'], //required
                    'renew_agent' => $data['renew_agent'] ?? null,
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
                    'image_record' => $data['image_record'] ?? '',
                    'fk_idClient' => $data['fk_idClient'], // required
                    'fk_idUser' => $data['fk_idUser'], // auth()->user()->id_user,
                    'amount_paid' => $data['amount_paid'],
                    'notes' => $data['notes'] ?? '',
                    'total' => $data['total'] ?? '',
                    'address_invoice' => $data['address_invoice'], // required,
                    'numbarnch' => $data['numbarnch'], // required
                    'nummostda' => $data['nummostda'], // required
                    'numusers' => $data['numusers'], // required
                    'imagelogo' => $data['imagelogo'] ?? '',
                    'stateclient' => 'مشترك',
                    'numTax' => $data['numTax'], // required,
                    'ready_install' => $data['ready_install'], // required
                    'currency_name' => $data['currency_name'], //required
                    'renew_agent' => $data['renew_agent'] ?? null,
                    'file_attach' => '',
                    'renew_pluse' => $data['renew_pluse'] ?? null,
                    'date_readyinstall' => null,
                    'user_ready_install' => null,
                ]);

            $client = clients::where('id_clients', $data['fk_idClient'])->first();
            $client->update(['type_client' => 'مشترك']);

            addComment($data['comment'], $data['fk_idClient'], $data['fk_idUser'], 'متطلبات العميل');

            foreach ($request['products'] as $product) {
                $insertArray = array();
                $insertArray['amount'] = $product['amount'] ?? 0;
                $insertArray['price'] = $product['price'] ?? 0;
                $insertArray['fk_id_invoice'] = $invoice->id_invoice;
                $insertArray['fk_product'] = $product['fk_product'];
                $insertArray['taxtotal'] = $product['taxtotal'] ?? 0.0;
                $insertArray['rate_admin'] = $product['rate_admin'] ?? 0.0;
                $insertArray['rateUser'] = $product['rateUser'] ?? 0.0;

                invoice_product::create($insertArray);
            }

            // DB::commit();
            // $resJson = array("result" => "success", "code" => "200", "message" => new InvoiceResource($invoice));
            // echo json_encode($resJson, JSON_UNESCAPED_UNICODE);

            $data['image_record'] = '';
            $data['imagelogo'] = '';
            if (key_exists('file', $request->all())) {
                $filsHandled = $this->myService->storeFile($request->file, 'invoices');
                $data['image_record'] = $filsHandled;
            }
            if (key_exists('logo', $request->all())) {
                $filsHandled = $this->myService->storeThumbnail($request->logo, 'logo_client', 200);
                $data['imagelogo'] = $filsHandled;
            }
            $invoice->update([
                'image_record' => $data['image_record'],
                'imagelogo' => $data['imagelogo'],
            ]);

            if (key_exists('uploadfiles', $request->all())) {
                foreach ($request->uploadfiles as $file) {
                    $filsHandled = $this->myService->storeFile($file, 'invoices');
                    $fileInvoice = files_invoice::create([
                        'fk_invoice' => $invoice->id_invoice,
                        'file_attach_invoice' => $filsHandled,
                    ]);
                }
            }

            $this->addTaskToApproveAdminAfterAddInvoice($invoice->id_invoice, $data['fk_regoin']);

            // ------------ notification -------------
            $fk_regoin = $data['fk_regoin']; //fk_regoin_invoice
            $fk_country = $data['fk_country']; //owner not related in regoin
            $name_enterprise = $client->name_enterprise;
            $title_name_approve = "تم إنشاء فاتورة للعميل ";
            $name_user = $data['nameUser']; //موظف المبيعات
            $message = "$title_name_approve $name_enterprise من قبل \r $name_user";


            $user_ids =  getIdUsers($fk_regoin, 10, $fk_country);
            // dd($user_ids);
            // $user_ids = collect([472, 418]);
            $tokens = getTokens($user_ids);
            // dd($tokens);
            $title = "طلب موافقة";
            $this->invoiceSrevice->sendNotification(
                $tokens,
                $client->id_clients,
                'ApproveRequest',
                $title,
                $message,
            )->storeNotification($user_ids, $message, 'ApproveRequest', $client->id_clients);

            $arrayUser =  getIdUsers($fk_regoin, 111, $fk_country);
            $title = "طلب موافقة المالية";
            $tokens =  getTokens($arrayUser);
            $this->invoiceSrevice->sendNotification(
                $tokens,
                $client->id_clients,
                'ApproveFRequest',
                $title,
                $message,
            )->storeNotification($arrayUser, $message, 'ApproveRequest', $client->id_clients);

            DB::commit();
            return response()->json(['message' => new InvoiceResource($invoice), 'result' => 'success']);
        } catch (Exception $e) {
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

    public function updateInvoice(Request $request, string $invoice_id)
    {
        DB::beginTransaction();
        $data = $request->all();
        try {
            $invoice = client_invoice::where('id_invoice', $invoice_id)->first();

            $invoice->update([
                'date_create' => $request->input('date_create', $invoice->date_create),
                'date_approve' => $request->input('date_approve', $invoice->date_approve),
                'fk_regoin_invoice' => $request->input('fk_regoin_invoice', $invoice->fk_regoin_invoice),
                'total' => $request->input('total', $invoice->total),
                'type_pay' => $request->input('type_pay', $invoice->type_pay),
                'renew_year' => $request->input('renew_year', $invoice->renew_year),
                'renew2year' => $request->input('renew2year', $invoice->renew2year),
                'renew_pluse' => $request->input('renew_pluse', $invoice->renew_pluse),
                'participate_fk' => $request->input('participate_fk', $invoice->participate_fk),
                'fk_agent' => $request->input('fk_agent', $invoice->fk_agent),
                'type_seller' => $request->input('type_seller', $invoice->type_seller),
                'rate_participate' => $request->input('rate_participate', $invoice->rate_participate),
                'type_installation' => $request->input('type_installation', $invoice->type_installation),
                'image_record' => $request->input('image_record', $invoice->image_record),
                'fk_idClient' => $request->input('fk_idClient', $invoice->fk_idClient),
                'fk_idUser' => $request->total != null ? $invoice->fk_idUser : $request->input('fk_idUser', $invoice->fk_idUser),
                'amount_paid' => $request->input('amount_paid', $invoice->amount_paid),
                'notes' => $request->input('notes', $invoice->notes),
                'lastuserupdate' => $request->input('lastuserupdate', $invoice->lastuserupdate),
                'date_lastuserupdate' => Carbon::now()->format('Y-m-d H:i:s'),
                'address_invoice' => $request->input('address_invoice', $invoice->address_invoice),
                'clientusername' => $request->input('clientusername', $invoice->clientusername),
                'numbarnch' => $request->input('numbarnch', $invoice->numbarnch),
                'nummostda' => $request->input('nummostda', $invoice->nummostda),
                'numusers' => $request->input('numusers', $invoice->numusers),
                'numTax' => $request->input('numTax', $invoice->numTax),
                'currency_name' => $request->input('currency_name', $invoice->currency_name),
                'imagelogo' => $request->input('imagelogo', $invoice->imagelogo),
                'renew_agent' => $request->input('renew_agent', $invoice->renew_agent),
                'invoice_source' => $request->input('invoice_source', $invoice->invoice_source),
            ]);

            if (key_exists('product_to_delete', $data)) {
                foreach ($data['product_to_delete'] as $product_id) {
                    invoice_product::where('fk_product', $product_id)
                        ->where('fk_id_invoice', $invoice_id)
                        ->first()?->delete();
                }
            }

            if (key_exists('products', $data)) {
                foreach ($request['products'] as $product) {
                    $insertArray = array();
                    $insertArray['amount'] = $product['amount'] ?? 0;
                    $insertArray['price'] = $product['price'] ?? 0;
                    $insertArray['fk_id_invoice'] = $invoice->id_invoice;
                    $insertArray['fk_product'] = $product['fk_product'];
                    $insertArray['taxtotal'] = $product['taxtotal'] ?? 0.0;
                    $insertArray['rate_admin'] = $product['rate_admin'] ?? 0.0;
                    $insertArray['rateUser'] = $product['rateUser'] ?? 0.0;

                    invoice_product::create($insertArray);
                }
            }

            $invoice = crudMultiInvoiceFiles($request->all(), $invoice_id, $this->myService);

            $name_enterprise = clients::where('id_clients', $data['fk_idClient'])->first()?->name_enterprise;
            $nameuser = auth()->user()->nameUser; //
            $nametitle = "من قبل";
            $titlenameapprove = "تم تعديل فاتورة العميل ";
            $message = "$titlenameapprove $name_enterprise \r$nametitle \r $nameuser";
            $fk_regoin = $_POST['fk_regoin']; //fk_regoin_invoice
            $fkcountry = $_POST['fk_country']; //owner not related in regoin
            $user_ids =  getIdUsers($fk_regoin, 57, $fkcountry);
            $tokens = getTokens($user_ids);
            $title = "تعديل فاتورة";

            $this->invoiceSrevice->sendNotification(
                $tokens,
                $data['fk_idClient'],
                'InvoiceUpdated',
                $title,
                $message,
            );
            $this->invoiceSrevice->storeNotification($user_ids, $message, 'InvoiceUpdated', $data['fk_idClient'], auth()->user()->id_user);

            DB::commit();
            return response()->json(['message' => new InvoiceResource($invoice), "result" => "success"]);
        }
        catch(Exception $e)
        {
            return response()->json(['message' => new InvoiceResource($invoice)]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

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


        $fk_regoin = $invoice?->fk_regoin_invoice;
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
                'data' => $id_invoice,
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

    public function getInvoicesByPrivilages()
    {
        $user = auth('sanctum')->user();
        $level = $user->type_level;

        $levelPriviligeAllClientInvoices = privg_level_user::where(
            'fk_privileg',
            Constants::PRIVILEGES_IDS['ALL_CLIENT_INVOICES']
        )
            ->where('fk_level', $level)
            ->first();

        $levelPriviligeAllRegoinInvoices = privg_level_user::where(
            'fk_privileg',
            Constants::PRIVILEGES_IDS['ALL_CLIENT_REGOIN']
        )
            ->where('fk_level', $level)
            ->first();

        $levelPriviligeAllEmployeeInvoices = privg_level_user::where(
            'fk_privileg',
            Constants::PRIVILEGES_IDS['ALL_CLIENT_EMPLOYEE']
        )
            ->where('fk_level', $level)
            ->first();


        $query = client_invoice::whereNull('isdelete')
            ->where('stateclient', 'مشترك')
            ->where('isApprove', 1)
            ->orderByDesc('date_approve');

        switch (true) {
            case $levelPriviligeAllClientInvoices?->is_check == 1:
                break;

            case $levelPriviligeAllRegoinInvoices?->is_check == 1:
                $query->where('fk_regoin_invoice', $user->fk_regoin);
                break;

            case $levelPriviligeAllEmployeeInvoices?->is_check == 1:
                $query->where('fk_idUser', $user->id_user);
                break;

            default:
                $query->whereRaw('1 = 0');
                break;
        }

        $data = $query->filter(request()->all())->paginate(request()->limit?? 15);

        $response = InvoiceResourceForGetInvoicesByPrivilages::collection($data);
        return $this->sendSucssas($response);
    }

    public function getInvoiceMainCity(Request $request)
    {
        $fk_country = $request->query('fk_country');
        $state_param = $request->query('state');
        $maincity_fks_param = $request->query('maincity_fks');
        $city_fks_param = $request->query('city_fks');

        $data = [];

        if ($request->has('allstate')) {
            info('1');
            $mainCity = $maincity_fks_param;
            $data = $this->invoiceSrevice->getInvoicesMaincityAllstate($fk_country, $mainCity);
        }


        if ($request->has('allmix')) {
            info('2');
            $state = $state_param;
            $mainCity = $maincity_fks_param;
            $data = $this->invoiceSrevice->getInvoicesMaincityMix($fk_country, $mainCity, $state);
        }

        if ($request->has('allmixCity')) {
            info('3');
            $state = $state_param;
            info($state);
            $city = $city_fks_param;
            $data = $this->invoiceSrevice->getInvoicesCityState($fk_country, $city, $state);
        }

        if ($request->has('allCityState')) {
            info('4');
            $city = $city_fks_param;
            $data = $this->invoiceSrevice->getInvoicesCity($fk_country, $city);
        }

        return $this->sendSucssas($data);
        // $response = InvoiceResourceForGetInvoicesByPrivilages::collection($data);
        // return $this->sendSucssas($response);
    }
}
