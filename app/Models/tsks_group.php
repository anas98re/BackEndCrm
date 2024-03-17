<?php

namespace App\Models;

use App\Logger\CustomActivityLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class tsks_group extends Model
{
    use LogsActivity;

    protected static $activityLoggerClass = CustomActivityLogger::class;

    protected $table = 'tasks_groups';

    protected $fillable = [
        'groupName',
        'description',
        'created_by',
    ];

    public $incrementing = true;

    public function getActivitylogOptions(): LogOptions
    {
        $request = app(Request::class);
        $routePattern = $request->route()->uri();
        $ip = $request->ip();
        $user = auth('sanctum')->user();
        $userName = $user->nameUser;

        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->useLogName('tsks_group')
            ->setDescriptionForEvent(function (string $eventName) use ($routePattern, $ip, $userName) {
                // Provide the description for the event based on the event name, route pattern, and IP
                if ($eventName === 'created') {
                    return "Task group created by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'updated') {
                    return "Task group updated by $userName, using route: $routePattern from IP: $ip.";
                } elseif ($eventName === 'deleted') {
                    return "Task group deleted by $userName, using route: $routePattern from IP: $ip.";
                }

                // Default description if the event name is not recognized
                return "Task group action occurred by $userName, using route: $routePattern from IP: $ip.";
            });
    }

    public function getActivityLogger()
    {
        return new CustomActivityLogger($this);
    }
}


// public function editClientByTypeClient($id_clients, Request $request)
// {
//     try {
//         DB::beginTransaction();
//         $client = clients::find($id_clients);

//         if ($request->type_client == 'عرض سعر' || $request->type_client == 'تفاوض') {
//             $client->type_client = $request->type_client;
//             $client->date_changetype = Carbon::now('Asia/Riyadh')->toDateTimeString();
//             $client->offer_price = $request->offer_price;
//             $client->date_price = $request->date_price;
//             $client->user_do = $request->id_user;
//             $client->save();
//         } elseif ($request->type_client == 'مستبعد') {
//             $client->type_client = 'معلق استبعاد';
//             $client->date_reject = Carbon::now('Asia/Riyadh')->toDateTimeString();
//             $client->fk_rejectClient = $request->fk_rejectClient;
//             $client->reason_change = $request->reason_change;
//             $client->fk_user_reject = $request->id_user;
//             $client->save();

//             // Add comment to client comment table.
//             $lastId = client_comment::orderBy('id_comment', 'desc')->value('id_comment');

//             $idComment = $lastId + 1;
//             $comment = new client_comment();
//             $comment->id_comment = $idComment;
//             $comment->type_comment = 'استبعاد عميل';
//             $comment->content = $request->reason_change;
//             $comment->date_comment = Carbon::now('Asia/Riyadh');
//             $comment->fk_client = $id_clients;
//             $comment->fk_user = $request->id_user;
//             $comment->save();

//             // Send notification to supervisor salse for client's brunch
//             $brunchClient = $client->fk_regoin;

//             $usersId = users::where('fk_regoin', $brunchClient)
//                 ->where('isActive', 1)
//                 ->where('type_level', 14)
//                 ->pluck('id_user');

//             $nameClient = $client->name_enterprise;

//             $message1 = 'العميل ? يحتاج لموافقة على الاستبعاد';
//             $messageNotifi = str_replace('?', $nameClient, $message1);

//             foreach ($usersId as $Id) {
//                 $userToken = user_token::where('fkuser', $Id)
//                     ->where('token', '!=', null)
//                     ->latest('date_create')
//                     ->first();

//                 $data = 'id_client =' . $id_clients .
//                     ' ,title =' . 'موافقة استبعاد' .
//                     ' ,Type =' . 'exclude' .
//                     ' ,messageNotifi=' . $messageNotifi;

//                 Notification::send(
//                     null,
//                     new SendNotification(
//                         'موافقة استبعاد',
//                         $messageNotifi,
//                         $data,
//                         ($userToken != null ? $userToken->token : null)
//                     )
//                 );

//                 notifiaction::create([
//                     'message' => $messageNotifi,
//                     'type_notify' => 'exclude',
//                     'to_user' => $Id,
//                     'isread' => 0,
//                     'data' => $id_clients,
//                     'from_user' => 1,
//                     'dateNotify' => Carbon::now('Asia/Riyadh')
//                 ]);
//             }
//         }

//         $clientData = clients::find($id_clients);
//         DB::commit();
//         return $this->sendResponse($clientData, 'updated');
//     } catch (\Throwable $th) {
//         throw $th;
//         DB::rollBack();
//     }
// }
