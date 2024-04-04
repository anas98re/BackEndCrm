<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ClientResource;
use App\Models\clients;
use App\Http\Requests\StoreclientsRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Requests\UpdateclientsRequest;
use App\Imports\AnotherDateClientsImport;
use App\Imports\ClientsImport;
use App\Mail\sendStactictesConvretClientsToEmail;
use App\Mail\sendStactictesConvretClientsTothabetEmail;
use App\Models\client_comment;
use App\Models\convertClintsStaticts;
use App\Models\notifiaction;
use App\Models\User;
use App\Models\user_token;
use App\Models\users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendNotification;
use App\Services\clientSrevices;
use Exception;
use Exception;
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
                $data['date_create'],
            );

        $data = $request->all();
        $data['SerialNumber'] = $serialnumber;
        $data['date_create'] = Carbon::now();
        $data['user_add'] = auth('sanctum')->user()->id_user;;

        $client = clients::create($data);

        $client = clients::create($data);

        $result = new ClientResource($client);

        return response()->json(array("result" => "success", "code" => "200", "message" => $result));
    }

    public function updateClient(Request $request, string $id)
    {
        $data = $request->all();

        $client = clients::query()->where('id_clients', $id)->first();

        $client->fill($data);
        $client->save();
        $result = new ClientResource($client);

        return response()->json(array("result" => "success", "code" => "200", "message" => $result));
    }



    public function SimilarClientsNames(Request $request)
    {
        //Temporarily due to a malfunction
        return response()->json();

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
}
