<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Http\Resources\ClientResource;
use App\Http\Resources\InvoiceResource;
use App\Models\clients;
use App\Http\Requests\StoreclientsRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Requests\UpdateclientsRequest;
use App\Http\Resources\ClientTransferedResource;
use App\Imports\AnotherDateClientsImport;
use App\Imports\ClientsImport;
use App\Mail\sendStactictesConvretClientsToEmail;
use App\Mail\sendStactictesConvretClientsTothabetEmail;
use App\Models\client_comment;
use App\Models\client_communication;
use App\Models\client_invoice;
use App\Models\config_table;
use App\Models\convertClintsStaticts;
use App\Models\notifiaction;
use App\Models\privg_level_user;
use App\Models\task;
use App\Models\User;
use App\Models\user_maincity;
use App\Models\user_token;
use App\Models\users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendNotification;
use App\Services\clientSrevices;
use App\Services\TaskManangement\queriesService;
use App\Services\TaskManangement\TaskProceduresService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class ClientsController extends Controller
{
    private $MyService;

    public function __construct(clientSrevices $MyService)
    {
        $this->MyService = $MyService;
    }

    public function editClientByTypeClient($id_clients, Request $request)
    {
        try {
            DB::beginTransaction();
            $client = clients::find($id_clients);

            if ($request->type_client == 'عرض سعر' || $request->type_client == 'تفاوض') {
                $client->type_client = $request->type_client;
                $client->date_changetype = Carbon::now('Asia/Riyadh')->toDateTimeString();
                $client->offer_price = $request->offer_price;
                $client->date_price = $request->date_price;
                $client->user_do = $request->id_user;
                $client->save();
            } elseif ($request->type_client == 'مستبعد') {
                $client->type_client = 'معلق استبعاد';
                $client->date_reject = Carbon::now('Asia/Riyadh')->toDateTimeString();
                $client->fk_rejectClient = $request->fk_rejectClient;
                $client->reason_change = $request->reason_change;
                $client->fk_user_reject = $request->id_user;
                $client->save();

                // Add comment to client comment table.
                $lastId = client_comment::orderBy('id_comment', 'desc')->value('id_comment');

                $idComment = $lastId + 1;
                $comment = new client_comment();
                $comment->id_comment = $idComment;
                $comment->type_comment = 'استبعاد عميل';
                $comment->content = $request->reason_change;
                $comment->date_comment = Carbon::now('Asia/Riyadh');
                $comment->fk_client = $id_clients;
                $comment->fk_user = $request->id_user;
                $comment->save();

                // Send notification to supervisor salse for client's brunch
                $brunchClient = $client->fk_regoin;

                $usersId = users::where('fk_regoin', $brunchClient)
                    ->where('isActive', 1)
                    ->where('type_level', 14)
                    ->pluck('id_user');

                $nameClient = $client->name_enterprise;

                $message1 = 'العميل ? يحتاج لموافقة على الاستبعاد';
                $messageNotifi = str_replace('?', $nameClient, $message1);

                foreach ($usersId as $Id) {
                    $userToken = user_token::where('fkuser', $Id)
                        ->where('token', '!=', null)
                        ->latest('date_create')
                        ->first();

                    $data = 'id_client =' . $id_clients .
                        ' ,title =' . 'موافقة استبعاد' .
                        ' ,Type =' . 'exclude' .
                        ' ,messageNotifi=' . $messageNotifi;

                    Notification::send(
                        null,
                        new SendNotification(
                            'موافقة استبعاد',
                            $messageNotifi,
                            $data,
                            ($userToken != null ? $userToken->token : null)
                        )
                    );

                    notifiaction::create([
                        'message' => $messageNotifi,
                        'type_notify' => 'exclude',
                        'to_user' => $Id,
                        'isread' => 0,
                        'data' => $id_clients,
                        'from_user' => 1,
                        'dateNotify' => Carbon::now('Asia/Riyadh')
                    ]);
                }
            }

            $clientData = clients::find($id_clients);
            DB::commit();
            return $this->sendResponse($clientData, 'updated');
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function appproveAdmin($id_clients, Request $request)
    {
        $client = clients::find($id_clients);

        if ($request->isAppprove) {
            $client->type_client = 'مستبعد';
        } else {
            $client->type_client = 'تفاوض';
            $client->date_changetype = Carbon::now('Asia/Riyadh')->toDateTimeString();
        }

        $client->date_approve_reject = Carbon::now('Asia/Riyadh')->toDateTimeString();
        $client->approveIduser_reject = auth('sanctum')->user()->id_user;
        $client->save();

        return $this->sendResponse($client, 'Done');
    }

    public function transformClientsFromMarketingIfOverrideLimit8Days()
    {
        try {
            DB::beginTransaction();
            $sevenWorkingDaysAgo = Carbon::now('Asia/Riyadh');

            // Adjust the date to exclude Fridays and Saturdays and go back 7 working days
            for ($i = 0; $i < 7; $i++) {
                do {
                    $sevenWorkingDaysAgo->subDay();
                } while ($sevenWorkingDaysAgo->isFriday() || $sevenWorkingDaysAgo->isSaturday());
            }

            $formattedDate = $sevenWorkingDaysAgo->format('Y-m-d H:i:s');
            $branchesIdsWithNumberRepetitions = $this->MyService
                ->branchesIdsWithCountForTransformClientsFromMarketing($formattedDate);

            $this->MyService
                ->sendNotificationsToResponsapilUserOfClient($formattedDate);

            $this->MyService
                ->sendNotificationsToBranchSupervisorsAndWhoHasPrivilage($branchesIdsWithNumberRepetitions);


            // 'oldSourceClient' will now contain the old values of 'sourcclient'
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function addClient(Request $request)
    {
        $data = $request->all();
        $serialnumber =
            $this->MyService->generate_serialnumber_InsertedClient(
                Carbon::now(),
            );

        $data = $request->all();

        $data['fk_user'] = auth()->user()->id_user;
        $data['fk_regoin'] = auth()->user()->fk_regoin;

        $data['SerialNumber'] = $serialnumber;
        $data['date_create'] = Carbon::now();
        $data['user_add'] = auth('sanctum')->user()->id_user;

        $client = clients::create($data);

        $result = new ClientResource($client);

        return response()->json(array("result" => "success", "code" => "200", "message" => $result));
    }

    public function updateClient(Request $request, string $id)
    {
        $data = $request->all();

        $client = clients::query()->where('id_clients', $id)->first();

        $type_record = key_exists('type_record', $data) == true ? $data['type_record']: $client->type_record;
        if ($type_record == "صحيح")
            $data['type_classification'] = null;
        else
            $data['type_classification'] = $data["type_classification"]?? $client->type_classification;

        $client->fill($data);
        $client->save();
        $result = new ClientResource($client);

        return response()->json(array("result" => "success", "code" => "200", "message" => $result));
    }


    public function SimilarClientsNames(Request $request)
    {
        //Temporarily due to a malfunction
        // return response()->json();


        $selectFields = [
            'name_client',
            'name_enterprise',
            'phone',
            'id_clients',
            'date_create',
            'SerialNumber'
        ];

        $query = clients::query();

        if ($request->has('phone') && !empty($request->phone)) {
            // Create a separate instance of the query to execute only the phone condition
            $phoneQuery = clients::query()->where('phone', $request->phone)->select($selectFields)->get();

            // Check if there are results for phone query
            if ($phoneQuery->count() > 0) {
                return response()->json($phoneQuery);
            }
        }

        // Continue with name_client or name_enterprise condition
        if ($request->has('name_client') || $request->has('name_enterprise')) {
            $nameClient = $request->input('name_client');
            $nameEnterprise = $request->input('name_enterprise');

            $this->MyService->filterByNameClientOrEnterprise($query, $nameClient, $nameEnterprise);
        }

        // Get results for name_client or name_enterprise condition
        $results = $query->select($selectFields)->get();

        return response()->json($results);
    }



    // to test ..
    private function getPluckColumn(Request $request)
    {
        return $request->has('name_client')
            ? 'name_client'
            : ($request->has('name_enterprise') ? 'name_enterprise' : 'phone');
    }

    public function convertClientsFromAnEmployeeToEmployee(Request $request)
    {
        try {
            DB::beginTransaction();
            $oldUserId = $request->oldUserId;
            $newUserId = $request->newUserId;
            $Count = DB::table('clients')->where('fk_user', $oldUserId)
                ->where(function ($query) {
                    $query->where('type_client', 'مستبعد')
                        ->orWhere('type_client', 'تفاوض');
                })
                ->whereYear('date_create', 2023)->count();

            convertClintsStaticts::create([
                'numberOfClients' => $Count != 0  ? $Count : 0,
                'convert_date' => Carbon::now('Asia/Riyadh'),
                'oldUserId' => $oldUserId,
                'newUserId' => $newUserId,
            ]);
            // Perform the update and get the number of affected rows
            $affectedRows = DB::table('clients')
                ->where('fk_user', $oldUserId)
                ->where(function ($query) {
                    $query->where('type_client', 'مستبعد')
                        ->orWhere('type_client', 'تفاوض');
                })
                ->whereYear('date_create', 2023)
                ->update(['fk_user' => $newUserId]);

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function sendStactictesConvretClientsToEmail(Request $request)
    {
        $ClintsStaticts = convertClintsStaticts::with(['oldUser', 'newUser'])->get();
        Mail::to($request->email)->send(new sendStactictesConvretClientsToEmail($ClintsStaticts));
    }

    public function editDatePriceDataToCorrectFormatt()
    {
        $dataUpdatedAndOld = [];
        $clients = clients::pluck('date_price', 'id_clients');
        foreach ($clients as $clientId => $datePrice) {
            if ($datePrice !== null) {
                $formattedDatetime = Carbon::parse($datePrice)->format('Y-m-d H:i:s');
                clients::where('id_clients', $clientId)->update(['date_price1' => $formattedDatetime]);
                $dataUpdatedAndOld[] = 'Client Id is: ' . $clientId . ', ' . $datePrice . ' -> ' . $formattedDatetime;
            }
        }
        return $dataUpdatedAndOld;
    }

    public function importClints(Request $request)
    {
        $file = $request->file('file');

        Excel::import(new ClientsImport, $file);

        return $this->sendResponse('success', 'Important clients imported successfully.');
    }

    public function importAnotherClints(Request $request)
    {
        $file = $request->file('file');

        Excel::import(new AnotherDateClientsImport, $file);

        return $this->sendResponse('success', 'Important clients imported successfully.');
    }

    public function getClientByID($id)
    {
        try
        {
            $client = clients::find($id);
            $result = new ClientResource($client);

            return response()->json(array("result" => "success", "code" => "200", "message" => $result));
        }
        catch(Exception $e)
        {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function transferClient(Request $request, string $id)
    {
        DB::beginTransaction();
        // $data = $request->validate([
        //     'fk_user' => 'required|numeric',
        //     // 'fkusertrasfer' => 'required|numeric',
        //     // 'name_enterprise' => 'required',
        //     // 'nameusertransfer' => 'required',
        //     // 'date_transfer' => 'required',
        // ]);
        $data = $request->all();
        try
        {

            $update = array();
            $user = users::query()->where('id_user', $data['fk_user'])->first();
            $user_transfer = users::query()->where('id_user', $data['fk_user'])->first();

            $update['fk_regoin'] = $user?->fk_regoin;
            $update['fkusertrasfer'] = auth()->user()->id_user;
            $update['date_transfer'] = Carbon::now();
            $update['reason_transfer'] = $data['fk_user'];

            $client = clients::query()->where('id_clients', $id)->first();
            $client->update($update);

            $name_enterprise = $client->name_enterprise;
            $nameApprove = $user_transfer->nameUser;

            $titlenameapprove = "تم تحويل العميل ";
            $nametitle = "من قبل";
            $message = "$titlenameapprove $name_enterprise \r$nametitle \r $nameApprove";
            $userToken = user_token::where('fkuser', $user->id_user)
                        ->where('token', '!=', null)
                        ->latest('date_create')
                        ->first();

            Notification::send(
                null,
                new SendNotification(
                    'نقل عميل',
                    $message,
                    $message,
                    ($userToken != null ? $userToken->token : null)
                )
            );

            notifiaction::create([
                'message' => $message,
                'type_notify' => 'transfer',
                'to_user' => $user->id_user,
                'isread' => 0,
                'data' => $id,
                'from_user' => $user_transfer->id_user,
                'dateNotify' => Carbon::now('Asia/Riyadh')
            ]);

            $response = array("result" => "success", "code" => "200", "message" => new ClientResource($client));
            DB::commit();
            return response()->json($response);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function approveOrRefuseTransferClient(Request $request, string $id)
    {
        $data = $request->all();
        DB::beginTransaction();
        try
        {
            $client = clients::query()->where('id_clients', $id)->first();
            $id_clients = $client->id_clients;
            $name_enterprise = $client->name_enterprise;
            $fk_user = $client->reason_transfer;
            $fk_regoin = users::query()->where('id_user', $client->reason_transfer)?->first()?->fk_regoin;

            if(! is_null($data['approve']?? null) )
            {
                $updateArray = array();

                $updateArray['fk_user'] = $fk_user;
                $updateArray['reason_transfer'] = null;
                $updateArray['fk_regoin'] = $fk_regoin;

                $client = clients::query()
                    ->where('id_clients', $id)
                    ->first();
                $client->update($updateArray);

                $nameApprove = auth()->user()->nameUser;
                $id_users = $this->getIdUsers($fk_regoin, 126);
                $id_users->push($fk_user);

                $title = "قبول تحويل العميل";
                $titlenameapprove = "تم قبول تحويل العميل";
                $nametitle = "من قبل";

                $message = "$titlenameapprove $name_enterprise \r$nametitle \r $nameApprove";
                foreach($id_users as $user_id)
                {
                    $userToken = user_token::where('fkuser', $user_id)
                        ->where('token', '!=', null)
                        ->latest('date_create')
                        ->first();

                    Notification::send(
                        null,
                        new SendNotification(
                            $title,
                            $message,
                            $message,
                            ($userToken != null ? $userToken->token : null)
                        )
                    );

                    notifiaction::create([
                        'message' => $message,
                        'type_notify' => 'ApproveDone',
                        'to_user' => $id,
                        'isread' => 0,
                        'data' => $id_clients,
                        'from_user' => $client->fkusertrasfer,
                        'dateNotify' => Carbon::now('Asia/Riyadh')
                    ]);
                }
                DB::commit();
                $resJson = array("result" => "success", "code" => "200", "message" => 'done approved');
                return response()->json($resJson, 200);
            }
            else
            {
                $updateArray = array();
                $updateArray['reason_transfer'] = null;
                $updateArray['fkusertrasfer'] = null;
                $updateArray['date_transfer'] = null;

                $client = clients::query()
                    ->where('id_clients', $id)
                    ->first();
                $userTransfered = $client->fkusertrasfer;
                $client->update($updateArray);

                $nameRefused = auth()->user()->nameUser;
                $id_users = $this->getIdUsers($fk_regoin, 126);
                $id_users->push($fk_user);

                $title = "رفض تحويل العميل";
                $titlenameapprove = "تم رفض تحويل العميل";
                $nametitle = "من قبل";

                $message = "$titlenameapprove $name_enterprise \r$nametitle \r $nameRefused";

                foreach($id_users as $user_id)
                {
                    $userToken = user_token::where('fkuser', $user_id)
                        ->where('token', '!=', null)
                        ->latest('date_create')
                        ->first();

                    Notification::send(
                        null,
                        new SendNotification(
                            $title,
                            $message,
                            $message,
                            ($userToken != null ? $userToken->token : null)
                        )
                    );

                    notifiaction::create([
                        'message' => $message,
                        'type_notify' => 'TransferRefuse',
                        'to_user' => $id,
                        'isread' => 0,
                        'data' => $id_clients,
                        'from_user' => $userTransfered,
                        'dateNotify' => Carbon::now('Asia/Riyadh')
                    ]);
                }
                DB::commit();
                $resJson = array("result" => "success", "code" => "200", "message" => 'done refused');
                return response()->json($resJson, 200);
            }
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function getIdLevelsByPrivilge($fk_privileg): Collection
    {
        return privg_level_user::query()
            ->where('fk_privileg', $fk_privileg)
            ->where('is_check', 1)
            ->get()
            ->pluck('fk_level');
    }

    public function getIdLevelsByPrivilges(array $fk_privilegs): Collection
    {
        return privg_level_user::query()
            ->whereIn('fk_privileg', $fk_privilegs)
            ->where('is_check', 1)
            ->get()
            ->pluck('fk_level')
            ->unique();
    }

    public function getIdUsers($fk_regoin, $fk_privileg, $fk_country = null)
    {
        $levels = $this->getIdLevelsByPrivilge($fk_privileg);
        if(is_null($fk_country))
            $id_users = users::query()
                ->where(function ($query) use ($levels, $fk_regoin) {
                    $query->where('fk_regoin', $fk_regoin)
                        ->whereIn('type_level', $levels);
                })
                ->orWhere(function ($query) use ($levels) {
                    $query->where('fk_regoin', 14)
                        ->whereIn('type_level', $levels);
                })
                ->get()
                ->pluck('id_user');
        else
            $id_users = users::query()
                ->where(function ($query) use ($levels, $fk_regoin) {
                    $query->where('fk_regoin', $fk_regoin)
                        ->whereIn('type_level', $levels);
                })
                ->orWhere(function ($query) use ($levels, $fk_country) {
                    $query->where('fk_regoin', 14)
                        ->where('fk_country', $fk_country)
                        ->whereIn('type_level', $levels);
                })
                ->get()
                ->pluck('id_user');

        return $id_users;
    }

    public function getTransferClientsWithPrivileges(): JsonResponse
    {
        try
        {
            $user = auth()->user();

            $allLevels = $this->getIdLevelsByPrivilge(Constants::PRIVILEGES_IDS['TRANSFER_CLIENTS_ALL']);
            $employeeLevels = $this->getIdLevelsByPrivilge(Constants::PRIVILEGES_IDS['TRANSFER_CLIENTS_EMPLOYEE']);
            $adminLevels = $this->getIdLevelsByPrivilge(Constants::PRIVILEGES_IDS['TRANSFER_CLIENTS_ADMIN']);

            $is_all = false;
            $is_admin = false;

            $clients = collect();
            if($allLevels->contains($user->type_level))
            {
                $clients = clients::query()->whereNotNull('reason_transfer')->get();
                $is_all = true;
            }

            if($adminLevels->contains($user->type_level) && ! ($is_all) )
            {

                $clients = clients::query()
                    ->where('reason_transfer', $user->id_user)
                    ->orWhere(function ($query) use($user) {
                        if($user->fk_regoin == 14)
                            $query->whereNotNull('reason_transfer');
                        else
                            $query->where('fk_regoin', $user->fk_regoin)
                                ->whereNotNull('reason_transfer');
                    })
                    ->get();
                $is_admin = true;
            }

            if($employeeLevels->contains($user->type_level)  && (! $is_admin) && (! $is_all) )
            {
                $clients = clients::query()->where('reason_transfer', $user->id_user)->get();
            }

            $resJson = array("result" => "success", "code" => "200", "message" => ClientTransferedResource::collection($clients));
            return response()->json($resJson);
        }
        catch(Exception $e)
        {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function setDateInstall(Request $request, string $id_invoice)
    {
        DB::beginTransaction();
        $data = $request->all();
        try
        {
            $invoice = client_invoice::where('id_invoice', $id_invoice)?->first();
            $client = $invoice->client;
            $updateArray = array();
            $updateArray['userinstall'] = auth()->user()->id_user;
            $updateArray['clientusername'] = $client->name_client;
            $updateArray['isdoneinstall'] = 1;
            $updateArray['dateinstall_done'] = Carbon::now();

            $invoice->update($updateArray);

            $time = config_table::where('name_config', 'period_commincation3')
                ->first()->value_config;

            $insertArray = array();
            $insertArray['fk_client'] = $client->id_clients;
            $insertArray['date_next'] = Carbon::parse(Carbon::now())->addDays($time);
            $insertArray['id_invoice'] = $id_invoice;
            $insertArray['type_communcation'] = 'تركيب';
            $insertArray['type_install'] = 1;

            client_communication::create($insertArray);

            $this->update_fkuser_communication($client->id_clients);

            $time = config_table::where('name_config', 'period_commincation3')
                ->first()->value_config;
            $carbonDatetime = Carbon::parse(Carbon::now())->addDays($time);
            $date_next = $carbonDatetime;

            $arrJson_result = $this->getcommunication_repeatcheck($client->id_clients);
            if (!$arrJson_result) // [] => false, ![] => true
                $this->addcommunication($client->id_clients, $date_next);


            $fk_regoin = $client->fk_regoin;
            $fkcountry = $client->regoin?->fk_country;
            $id_users =  $this->getIdUsers($fk_regoin, 21, $fkcountry);
            $array2user = $this->getIdUsersRegoin($fkcountry, 21, $client->id_clients);
            $id_users = array_merge($id_users->toArray(), $array2user->toArray());


            $dataIn = [
                'idInvoice' => $id_invoice,
                'fkIdClient' => $client->id_clients
            ];
            $this->afterInstallClient($dataIn);

            $name_enterprise = $client->name_enterprise;
            $nameuserinstall = auth()->user()->nameUser;
            $title = "تم التركيب";
            $titlenameapprove = "الموظف الذي ركب للعميل ";
            $id_users = collect($id_users)->unique();
            $message = "$titlenameapprove $name_enterprise هو\r $nameuserinstall";
            foreach($id_users as $user_id)
            {
                $userToken = user_token::where('fkuser', $user_id)
                    ->where('token', '!=', null)
                    ->latest('date_create')
                    ->first();

                Notification::send(
                    null,
                    new SendNotification(
                        $title,
                        $message,
                        $message,
                        ($userToken != null ? $userToken->token : null)
                    )
                );

                notifiaction::create([
                    'message' => $message,
                    'type_notify' => 'Install',
                    'to_user' => $user_id,
                    'isread' => 0,
                    'data' => $client->id_clients,
                    'from_user' => auth()->user()->id_user,
                    'dateNotify' => Carbon::now('Asia/Riyadh')
                ]);
            }

            $invoice = client_invoice::where('id_invoice', $id_invoice)->first();
            $arrJson = new InvoiceResource($invoice);

            $resJson = array("result" => "success", "code" => "200", "message" => $arrJson);

            DB::commit();
            return response()->json($resJson);

        }
        catch(Exception $e)
        {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    protected function update_fkuser_communication($fkIdclient)
    {
        $result = client_communication::where('fk_client', $fkIdclient)
            ->whereNotNull('fk_user')
            ->where('type_communcation', 'ترحيب')
            ->orderBy('date_communication', 'desc')
            ?->first();

        if(!is_null($result))
        {
            $communications = client_communication::where('fk_client', $fkIdclient)
                ->whereNull('date_communication')
                ->get();
            foreach ($communications as $communication)
                $communication->update(['fk_user' => $result->fk_user]);
        }
    }

    protected function getcommunication_repeatcheck($fkclient)
    {
        return client_communication::where('fk_client', $fkclient)
            ->where('type_communcation', 'دوري')
            ->orderBy('date_communication', 'desc')
            ->get();
    }

    protected function addcommunication($fk_client, $date_next)
    {
        client_communication::create([
            'fk_client' => $fk_client,
            'type_communcation' => 'دوري',
            'date_next' => $date_next,
        ]);


        $this->update_fkuser_communication($fk_client);
    }

    protected function getIdUsersRegoin($fkcountry,$fk_privileg,$fkclient )
    {
        $fkmaincity=  clients::where('id_clients', $fkclient)?->first()?->cityRelation?->fk_maincity;
        $arraylevel = $this->getIdLevelsByPrivilge($fk_privileg);

        return user_maincity::select('users.id_user')
        ->join('users', 'users.id_user', '=', 'user_maincity.fk_user')
        ->where('users.fk_country', $fkcountry)
        ->where('user_maincity.fk_maincity', $fkmaincity)
        ->whereIn('users.type_level', $arraylevel)
        ->get()
        ->pluck('id_user');
    }

    protected function afterInstallClient($data)
    {
        try {
            $client_id = DB::table('client_invoice')->where('id_invoice', $data['idInvoice'])
                ->first();
            $welcomed_user_id = DB::table('client_communication')
                ->where('fk_client', $client_id->fk_idClient)
                ->where('type_communcation', 'تركيب')
                ->where('type_install', 1)
                ->where('id_invoice', $data['idInvoice'])
                ->first();


            $existingTask = task::where('invoice_id', $data['idInvoice'])
                ->where('client_id', $client_id->fk_idClient)
                ->where('public_Type', 'com_install_1')
                ->first();

            $time = config_table::where('name_config', 'period_commincation2')
                ->first()->value_config;
            $carbonDatetime = Carbon::parse(Carbon::now('Asia/Riyadh'))->addDays($time);
            $newDatetime = $carbonDatetime->toDateTimeString();

            $client = clients::where('id_clients', $client_id->fk_idClient)->first();
            $message = 'عميل مشترك ( ? ) يحتاج لتواصل الجودة الأول له';
            $messageDescription = str_replace('?', $client->name_enterprise, $message);

            if (!$existingTask) {
                $task = new task();
                $task->title = 'تواصل جودة اول';
                $task->description = $messageDescription;
                $task->invoice_id = $data['idInvoice'];
                $task->id_communication  = $welcomed_user_id->id_communication;
                $task->client_id  = $client_id->fk_idClient;
                $task->public_Type = 'com_install_1';
                $task->main_type_task = 'ProccessAuto';
                $task->assigend_department_from  = 3;
                $task->assigned_to  = ($welcomed_user_id != null ? $welcomed_user_id->fk_user : null);
                $task->start_date  = $newDatetime;
                $task->save();

                $service = new TaskProceduresService(new queriesService);
                !empty($task) ? $service->addTaskStatus($task) : null;
                $service->handleNotificationForTaskProcedures(
                    $message = $task->title,
                    $type = 'task',
                    $to_user = $welcomed_user_id->fk_user,
                    $invoice_id = $data['idInvoice'],
                    $client_id = $client_id->fk_idClient
                );
            } else {
                $task = null;
            }
            return $task;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

}
