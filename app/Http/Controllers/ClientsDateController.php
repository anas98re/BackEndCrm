<?php

namespace App\Http\Controllers;

use App\Models\clients_date;
use App\Http\Requests\Storeclients_dateRequest;
use App\Http\Requests\Updateclients_dateRequest;
use App\Models\clients;
use App\Models\notifiaction;
use App\Notifications\SendNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ClientsDateController extends Controller
{
    public function rescheduleOrCancelVisitClient(Request $request, $idclients_date)
    {
        DB::beginTransaction();

        try {
            if ($request->typeProcess == 'reschedule') {
                $client = clients_date::where('idclients_date', $idclients_date)
                    ->update([
                        'date_client_visit' => $request->date_client_visit,
                        'processReason' => $request->processReason,
                        'user_id_process' => auth('sanctum')->user()->id_user
                    ]);

                $this->handleNotification(
                    $privilage_id = 59,
                    $typeProcess = 'اعادة جدولة زيارة',
                    $idclients_date
                );
            } elseif ($request->typeProcess == 'cancel') {
                $client = clients_date::where('idclients_date', $idclients_date)
                    ->update([
                        'is_done' => 2,
                        'processReason' => $request->processReason,
                        'user_id_process' => auth('sanctum')->user()->id_user
                    ]);

                $this->handleNotification(
                    $privilage_id = 181,
                    $typeProcess = 'الغاء زيارة',
                    $idclients_date
                );
            } else {
                return;
            }

            DB::commit();
            return $this->sendResponse(['message' => $request->typeProcess . ' Process completed successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();

            return $this->sendResponse(['message' => 'Failed to process. Please try again.'], 500);
        }
    }

    private function handleNotification($privilage_id, $typeProcess, $idclients_date)
    {
        $privgLevelUsers = DB::table('privg_level_user')
            ->where('fk_privileg', $privilage_id)
            ->where('is_check', 1)
            ->pluck('fk_level');
        $usersId = DB::table('users')
            ->where('isActive', 1)
            ->whereIn('type_level', $privgLevelUsers)
            ->pluck('id_user');

        $client = clients_date::where('idclients_date', $idclients_date)
            ->first()->fk_client;
        $clienName = clients::where('id_clients', $client)->first()->name_enterprise;
        if ($typeProcess == 'الغاء زيارة') {
            $content = 'تم الغاء زيارة للعميل ?';
            $message = str_replace('?', $clienName, $content);
        } else {
            $content = 'تم اعادة جدولة زيارة للعميل ?';
            $message = str_replace('?', $clienName, $content);
        }
        foreach ($usersId as $Id) {
            $userToken = DB::table('user_token')->where('fkuser', $Id)
                ->where('token', '!=', null)
                ->latest('date_create')
                ->first();
            // Notification::send(
            //     null,
            //     new SendNotification(
            //         $typeProcess,
            //         $message,
            //         1,
            //         ($userToken != null ? $userToken->token : null)
            //     )
            // );

            notifiaction::create([
                'message' => $message,
                'type_notify' => 'exclude',
                'to_user' => $Id,
                'isread' => 0,
                'data' =>  $client,
                'from_user' => 1,
                'dateNotify' => Carbon::now('Asia/Riyadh')->format('Y-m-d H:i:s')
            ]);
        }
    }
}
