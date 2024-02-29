<?php

namespace App\Services;

use App\Constants;
use App\Http\Requests\TaskManagementRequests\GroupRequest;
use App\Http\Requests\TaskManagementRequests\TaskRequest;
use App\Models\agent;
use App\Models\agentComment;
use App\Models\client_comment;
use App\Models\clients;
use App\Models\clients_date;
use App\Models\notifiaction;
use App\Models\regoin;
use App\Models\tsks_group;
use App\Models\users;
use App\Notifications\SendNotification;
use App\Services\JsonResponeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ClientsDateService extends JsonResponeService
{
    public function handleNotificationAndComments($privilage_id, $typeProcess, $idclients_date, $processReason)
    {
        $privgLevelUsers = DB::table('privg_level_user')
            ->where('fk_privileg', $privilage_id)
            ->where('is_check', 1)
            ->pluck('fk_level');
        $usersId = DB::table('users')
            ->where('isActive', 1)
            ->whereIn('type_level', $privgLevelUsers)
            ->pluck('id_user');

        $clintOrAgentData = 0;
        $typeNotify = 0;
        $clientData = clients_date::where('idclients_date', $idclients_date)
            ->first();
        $agentData = clients_date::where('idclients_date', $idclients_date)
            ->first();
        if ($clientData->fk_client) {
            $client = $clientData->fk_client;
            $typeNotify = 'clientVisit';
            $clintOrAgentData = $client;
            $clienName = clients::where('id_clients', $client)->first()->name_enterprise;
            if ($typeProcess == 'الغاء زيارة') {
                $content = 'تم الغاء زيارة للعميل ?';
                $message = str_replace('?', $clienName, $content);
            } else {
                $content = 'تم اعادة جدولة زيارة للعميل ?';
                $message = str_replace('?', $clienName, $content);

                //add processReason as a comment in clientComment table
                $this->addClientComment($client, $processReason);
            }
        } else {
            // $agent = clients_date::where('idclients_date', $idclients_date)
            //     ->first()
            $agent = $agentData->fk_agent;
            $typeNotify = 'agentVisit';
            $clintOrAgentData = $agent;
            $agentName = agent::where('id_agent', $agent)->first()->name_agent;
            if ($typeProcess == 'الغاء زيارة') {
                $content = 'تم الغاء زيارة للوكيل ?';
                $message = str_replace('?', $agentName, $content);
            } else {
                $content = 'تم اعادة جدولة زيارة للوكيل ?';
                $message = str_replace('?', $agentName, $content);

                //add processReason as a comment in agentComment table
                $this->addagentComment($agent, $processReason);
            }
        }
        foreach ($usersId as $Id) {
            $userToken = DB::table('user_token')->where('fkuser', $Id)
                ->where('token', '!=', null)
                ->latest('date_create')
                ->first();
            Notification::send(
                null,
                new SendNotification(
                    $typeProcess,
                    $message,
                    1,
                    ($userToken != null ? $userToken->token : null)
                )
            );

            notifiaction::create([
                'message' => $message,
                'type_notify' => $typeNotify,
                'to_user' => $Id,
                'isread' => 0,
                'data' =>  $clintOrAgentData,
                'from_user' => 1,
                'dateNotify' => Carbon::now('Asia/Riyadh')->format('Y-m-d H:i:s')
            ]);
        }
    }


    private function addClientComment($client, $processReason)
    {
        $client_comment = new client_comment();
        $client_comment->fk_client = $client;
        $client_comment->type_comment = 'زيارة عميل';
        $client_comment->content = $processReason;
        $client_comment->date_comment = Carbon::now('Asia/Riyadh');
        $client_comment->fk_user = auth('sanctum')->user()->id_user;
        $client_comment->save();
    }

    private function addagentComment($agent, $processReason)
    {
        $agent_comment = new agentComment();
        $agent_comment->agent_id = $agent;
        $agent_comment->content = $processReason;
        $agent_comment->date_comment = Carbon::now('Asia/Riyadh');
        $agent_comment->user_id = auth('sanctum')->user()->id_user;
        $agent_comment->save();
    }
}
