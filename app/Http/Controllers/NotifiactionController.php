<?php

namespace App\Http\Controllers;

use App\Models\notifiaction;
use App\Http\Requests\StorenotifiactionRequest;
use App\Http\Requests\UpdatenotifiactionRequest;
use App\Notifications\SendNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class NotifiactionController extends Controller
{
    public function sendFCM($arratoken, $data, $title, $body)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $apiKey = "AAAAF9_eV84:APA91bGE2Aun8EWVDnkqH0hVplyNS5zIIdgYgYfstKwZ9tlInqsPlwGsTcyJD4cjKfAjYn9z2ofW8EwQUP6_xHi7KB5V-eJ3u655ymD0PgRFJ1e094IIMcuRWijiAbVM8uS6HjoZ-crN";

        $notifData = [
            'title' => $title,
            'body' => $body,
            "image" => "http://smartcrm.ws/aya/api/imagesApp/smart_icon.jpg",
            'click_action' => "FLUTTER_NOTIFICATION_CLICK"
        ];

        $apiBody = [
            'notification' => $notifData,
            'data' => $data,
            'registration_ids' => $arratoken,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'key=' . $apiKey,
            'Content-Type' => 'application/json'
        ])->post($url, $apiBody);

        return $response->json();
    }

    public function testNotify()
    {
        $userToken = DB::table('user_token')
            ->where('fkuser', 408)
            ->where('token', '!=', null)
            ->latest('date_create')
            ->first();
            // return $userToken->token;
        notifiaction::create([
            'message' => 'Firbase',
            'type_notify' => 'type',
            'to_user' => 408,
            'isread' => 0,
            'data' => 'Tsk',
            'from_user' => 408,
            'dateNotify' => Carbon::now('Asia/Riyadh')
        ]);

        Notification::send(
            null,
            new SendNotification(
                'Firbase',
                'Tsk',
                'data',
                $userToken->token
            )
        );
        // $this->sendFCM($userToken->token, [1, 2], 'hi', 'hi');
    }
}
