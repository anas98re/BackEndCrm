<?php

namespace App\Http\Controllers;

use App\Models\commentParticipate;
use App\Http\Requests\StorecommentParticipateRequest;
use App\Http\Requests\UpdatecommentParticipateRequest;
use App\Models\participate;
use App\Models\users;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CommentParticipateController extends Controller
{
    public function addCommentParticipate(Request $request)
    {
        $data = $request->all();
        $data['participate_id'] = intval($data['participate_id']);
        $data['date_comment'] = Carbon::now('Asia/Riyadh')->format('Y-m-d H:i:s');
        $data['user_id'] = auth('sanctum')->user() ? auth('sanctum')->user()->id_user : null;
        $comment = commentParticipate::create($data);

        $user = users::find(auth('sanctum')->user()->id_user);
        $participate = participate::where('id_participate',$request->participate_id)->first();

        $comment->nameUser = $user->nameUser;
        $comment->img_image = $user->img_image ? $user->img_image : '';
        $comment->user_id = $user->id_user;

        $comment->add_date = $participate->add_date;
        $comment->update_date = $participate->update_date;

        return $this->sendResponse($comment, 'Done');
    }

    public function getParticipateComments($id)
    {
        $comments = CommentParticipate::where('participate_id', $id)
            ->orderBy('id', 'desc')
            ->with('participates','users')
            ->get();

        return $this->sendResponse(
            $this->commentsWithParticipate($comments),
            'These are all comments for this participate'
        );
    }

    private function commentsWithParticipate($comments)
    {
        return $commentsWithParticipate = $comments->map(function ($comment) {
            return [
                'participate_id' => $comment->participate_id,
                'content' => $comment->content,
                'date_comment' => $comment->date_comment,
                'user_id' => $comment->user_id,
                'id' => $comment->id,
                'nameUser' => $comment->users->nameUser,
                'img_image' => $comment->users->img_image ? $comment->users->img_image : '',
                'add_date' => $comment->participates->add_date,
                'update_date' => $comment->participates->update_date,
            ];
        });
    }
}
