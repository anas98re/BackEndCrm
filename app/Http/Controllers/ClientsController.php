<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\clients;
use App\Http\Requests\StoreclientsRequest;
use App\Http\Requests\UpdateclientsRequest;
use App\Models\client_comment;
use App\Models\notifiaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendNotification;
use App\Services\clientSrevices;

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
            if ($request->type_client == 'معلق استبعاد') {
                $updateClientData = DB::table('clients')
                    ->where('id_clients', $id_clients)
                    ->update(
                        [
                            'type_client' => $request->type_client,
                            'date_changetype' => Carbon::now('Asia/Riyadh'),
                            'fk_rejectClient' => $request->fk_rejectClient,
                            'reason_change' => $request->reason_change,
                            'user_do' => $request->id_user,
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
                foreach ($usersId as $Id) {
                    $userToken = DB::table('user_token')->where('fkuser', $Id)
                        ->where('token', '!=', null)
                        ->latest('date_create')
                        ->first();
                    Notification::send(
                        null,
                        new SendNotification(
                            'تحديث جديد لنوع عميل',
                            $request->type_client,
                            'Edit',
                            ($userToken != null ? $userToken->token : null)
                        )
                    );

                    notifiaction::create([
                        'message' => 'تحديث جديد لنوع عميل',
                        'type_notify' => 'تحديث عميل',
                        'to_user' => $Id,
                        'isread' => 0,
                        'data' => 'Tsk',
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
                        'date_changetype' => Carbon::now('Asia/Riyadh'),
                    ]
                );
        } else {
            $updateClientData = DB::table('clients')
                ->where('id_clients', $id_clients)
                ->update(
                    [
                        'type_client' => 'تفاوض',
                        'date_changetype' => Carbon::now('Asia/Riyadh'),
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
            clients::all();
            $eightWorkingDaysAgo = Carbon::now('Asia/Riyadh');

            // Adjust the date to exclude Fridays and Saturdays and go back 8 working days
            for ($i = 0; $i < 8; $i++) {
                do {
                    $eightWorkingDaysAgo->subDay();
                } while ($eightWorkingDaysAgo->isFriday() || $eightWorkingDaysAgo->isSaturday());
            }

            $formattedDate = $eightWorkingDaysAgo->format('Y-m-d H:i:s');
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
        $query = clients::query();

        if ($request->has('name_client')) {
            $query->orWhere('name_client', 'LIKE', '%' . $request->name_client . '%');
        }
        if ($request->has('name_enterprise')) {
            $query->orWhere('name_enterprise', 'LIKE', '%' . $request->name_enterprise . '%');
        }
        if ($request->has('phone')) {
            $query->orWhere('phone', 'LIKE', '%' . $request->phone . '%');
        }

        $results = $query->select('name_client', 'name_enterprise', 'phone')->get();

        return response()->json($results);
    }
    // to test ..
    private function getPluckColumn(Request $request)
    {
        return $request->has('name_client')
            ? 'name_client'
            : ($request->has('name_enterprise') ? 'name_enterprise' : 'phone');
    }
}
