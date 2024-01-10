<?php

namespace App\Http\Controllers;

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

class ClientsController extends Controller
{
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
            } else {
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
                $comment->content = 'Update type client to ' . $request->type_client;
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
                        'from_user' => 0,
                        'dateNotify' => Carbon::now('Asia/Riyadh')
                    ]);
                }
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }
}
