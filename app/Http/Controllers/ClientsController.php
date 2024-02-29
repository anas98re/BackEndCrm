<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\clients;
use App\Http\Requests\StoreclientsRequest;
use App\Http\Requests\UpdateclientsRequest;
use App\Imports\AnotherDateClientsImport;
use App\Imports\ClientsImport;
use App\Mail\sendStactictesConvretClientsToEmail;
use App\Mail\sendStactictesConvretClientsTothabetEmail;
use App\Models\client_comment;
use App\Models\convertClintsStaticts;
use App\Models\notifiaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendNotification;
use App\Services\clientSrevices;
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
            if ($request->type_client == 'عرض سعر') {
                $updateClientData = DB::table('clients')
                    ->where('id_clients', $id_clients)
                    ->update(
                        [
                            'type_client' => $request->type_client,
                            'date_changetype' => Carbon::now('Asia/Riyadh'),
                            'offer_price' => $request->offer_price,
                            'date_price' => $request->date_price,
                            'user_do' => $request->id_user,
                        ]
                    );
            }
            if ($request->type_client == 'تفاوض') {
                $updateClientData = DB::table('clients')
                    ->where('id_clients', $id_clients)
                    ->update(
                        [
                            'type_client' => $request->type_client,
                            'date_changetype' => Carbon::now('Asia/Riyadh'),
                            'offer_price' => $request->offer_price,
                            'date_price' => $request->date_price,
                            'user_do' => $request->id_user,
                        ]
                    );
            }
            if ($request->type_client == 'مستبعد') {
                $updateClientData = DB::table('clients')
                    ->where('id_clients', $id_clients)
                    ->update(
                        [
                            'type_client' => 'معلق استبعاد',
                            'date_reject' => Carbon::now('Asia/Riyadh'),
                            'fk_rejectClient' => $request->fk_rejectClient,
                            'reason_change' => $request->reason_change,
                            'fk_user_reject' => $request->id_user,
                        ]
                    );
                //add comment to client comment table.
                $lastId = DB::table('client_comment')
                    ->orderBy('id_comment', 'desc')
                    ->value('id_comment');

                $idComment = $lastId + 1;
                $comment = new client_comment();
                $comment->id_comment = $idComment;
                $comment->content = $request->reason_change;
                $comment->date_comment = Carbon::now('Asia/Riyadh');
                $comment->fk_client = $id_clients;
                $comment->fk_user = $request->id_user;
                $comment->save();

                //send notification to supervisor salse for client's brunch
                $brunchClient = DB::table('clients')
                    ->where('id_clients', $id_clients)
                    ->first()
                    ->fk_regoin;

                $usersId = DB::table('users')
                    ->where('fk_regoin', $brunchClient)
                    ->where('isActive', 1)
                    ->where('type_level', 14)
                    ->pluck('id_user');

                $nameClient = DB::table('clients')
                    ->where('id_clients', $id_clients)
                    ->first()->name_enterprise;

                $message1 = 'العميل ? يحتاج لموافقة على الاستبعاد';
                $messageNotifi = str_replace('?', $nameClient, $message1);

                foreach ($usersId as $Id) {
                    $userToken = DB::table('user_token')->where('fkuser', $Id)
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
                        'data' =>  $id_clients,
                        'from_user' => 1,
                        'dateNotify' => Carbon::now('Asia/Riyadh')
                    ]);
                }
            }
            $ClientData = DB::table('clients')
                ->where('id_clients', $id_clients)->first();
            DB::commit();
            return $this->sendResponse($ClientData, 'updated');
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function appproveAdmin($id_clients, Request $request)
    {
        if ($request->isAppprove) {
            $updateClientData = DB::table('clients')
                ->where('id_clients', $id_clients)
                ->update(
                    [
                        'type_client' => 'مستبعد',
                        'date_approve_reject' => Carbon::now('Asia/Riyadh'),
                        // 'approveIduser_reject' => auth('sanctum')->user()->id_user,
                    ]
                );
        } else {
            $updateClientData = DB::table('clients')
                ->where('id_clients', $id_clients)
                ->update(
                    [
                        'type_client' => 'تفاوض',
                        'date_approve_reject' => Carbon::now('Asia/Riyadh'),
                        'date_changetype' => Carbon::now('Asia/Riyadh'),
                        // 'approveIduser_reject' => auth('sanctum')->user()->id_user,
                    ]
                );
        }
        $ClientData = DB::table('clients')
            ->where('id_clients', $id_clients)->first();
        return $this->sendResponse($ClientData, 'Done');
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

            // $updateClientData = DB::table('clients')
            //     ->where('ismarketing', 1)
            //     ->where('is_check_marketing', 0)
            //     ->whereDate('date_create', '>=', Carbon::createFromDate(2024, 1, 1)->endOfDay())
            //     ->where('date_create', '<', $formattedDate)
            //     ->update([
            //         // 'oldSourceClient' => DB::raw('sourcclient'), // Assuming 'oldSourceClient' is the column where you want to store old values
            //         // 'sourcclient' => Constants::MAIDANI,
            //         'is_check_marketing' => 1,
            //     ]);

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

    public function addClient(StoreclientsRequest $request)
    {
        $serialnumber =
            $this->MyService->generate_serialnumber_InsertedClient(
                $request->input('date_create')
            );

        $data = $request->all();
        $data['SerialNumber'] = $serialnumber;

        clients::create($data);

        return response()->json(['message' => 'Client created successfully']);
    }


    public function SimilarClientsNames(Request $request)
    {
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
                $dataUpdatedAndOld[] = 'Client Id is: '.$clientId. ', '.$datePrice.' -> '.$formattedDatetime;
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
}
