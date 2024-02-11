<?php

namespace App\Http\Controllers;

use App\Models\clientCommentMention;
use App\Http\Requests\StoreclientCommentMentionRequest;
use App\Http\Requests\UpdateclientCommentMentionRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Models\notifiaction;
use App\Models\users;
use App\Notifications\SendNotification;

class ClientCommentMentionController extends Controller
{
    public function addCommentClientMention(Request $request)
    {
        DB::beginTransaction();

        try {
            $userIds = $request->input('userIds');
            $userIds = json_decode($request->input('userIds'), true);

            $commentContent = $request->input('commentContent');

            foreach ($userIds as $userId) {
                clientCommentMention::create([
                    'comment_id' => $request->commentId,
                    'user_id' => $userId,
                    'content' => $commentContent,
                    'date_mention' => Carbon::now('Asia/Riyadh')->format('Y-m-d H:i:s'),
                    'is_read' => false
                ]);

                $userToken = DB::table('user_token')->where('fkuser', $userId)
                    ->where('token', '!=', null)
                    ->latest('date_create')
                    ->first();
                $Name = users::where('id_user', $request->id_user)->first()->nameUser;
                $content = 'تم ذكرك في تعليق بواسطة ?';
                $message = str_replace('?', $Name, $content);
                Notification::send(
                    null,
                  
                        'منشن',
                        $message,
                        1,
                        ($userToken != null ? $userToken->token : null)
                    )
                );

                notifiaction::create([
                    'message' => $message,
                    'type_notify' => 'mention',
                    'to_user' => $userId,
                    'isread' => 0,
                    'data' =>  $request->commentId,
                    'from_user' => 1,
                    'dateNotify' => Carbon::now('Asia/Riyadh')->format('Y-m-d H:i:s')
                ]);
            }
            DB::commit();
            return $this->sendResponse(['message' => 'done'], 200);
        } catch (\Exception $e) {
            DB::rollback();

            return $this->sendResponse(['message' => 'Failed to process. Please try again.'], 500);
        }
    }
}
