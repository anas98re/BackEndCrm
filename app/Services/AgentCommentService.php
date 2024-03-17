<?php

namespace App\Services;

use App\Constants;
use App\Http\Requests\TaskManagementRequests\GroupRequest;
use App\Http\Requests\TaskManagementRequests\TaskRequest;
use App\Models\agent;
use App\Models\agentComment;
use App\Models\clients;
use App\Models\notifiaction;
use App\Models\regoin;
use App\Models\tsks_group;
use App\Models\users;
use App\Notifications\SendNotification;
use App\Services\JsonResponeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AgentCommentService extends JsonResponeService
{
    public function addCommentAgentService($request)
    {
        $data = $request->all();
        $data['agent_id'] = intval($data['agent_id']);
        $data['date_comment'] = Carbon::now('Asia/Riyadh')->format('Y-m-d H:i:s');
        $data['user_id'] = auth('sanctum')->user() ? auth('sanctum')->user()->id_user : null;
        $comment = agentComment::create($data);

        $user = users::find(auth('sanctum')->user()->id_user);
        $agent = agent::where('id_agent', $request->agent_id)->first();

        $comment->nameUser = $user->nameUser;
        $comment->img_image = $user->img_image ? $user->img_image : '';
        $comment->user_id = $user->id_user;

        $comment->name_agent = $agent->name_agent;
        $comment->type_agent = $agent->type_agent;
        $comment->image_agent = $agent->image_agent;

        return $comment;
    }

    public function getAgentCommentsServices($id)
    {
        $comments = agentComment::where('agent_id', $id)
            ->orderBy('id', 'desc')
            ->with('agents', 'users')
            ->get();
        return $this->commentsWithParticipate($comments);
    }

    private function commentsWithParticipate($comments)
    {
        return $commentsWithAgents = $comments->map(function ($comment) {
            return [
                'agent_id' => $comment->agent_id,
                'content' => $comment->content,
                'date_comment' => $comment->date_comment,
                'user_id' => $comment->user_id,
                'id' => $comment->id,
                'nameUser' => $comment->users->nameUser,
                'img_image' => $comment->users->img_image ? $comment->users->img_image : '',
                'name_agent' => $comment->agents->name_agent ? $comment->agents->name_agent : '',
                'type_agent' => $comment->agents->type_agent ? $comment->agents->type_agent : '',
                'image_agent' => $comment->agents->image_agent ? $comment->agents->image_agent : '',
            ];
        });
    }
}
