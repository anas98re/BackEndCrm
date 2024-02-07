<?php

namespace App\Http\Controllers;

use App\Models\commentParticipate;
use App\Http\Requests\StorecommentParticipateRequest;
use App\Http\Requests\UpdatecommentParticipateRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CommentParticipateController extends Controller
{
    public function addCommentParticipate(Request $request)
    {
        $data = $request->all();
        $data['date_comment'] = Carbon::now('Asia/Riyadh')->format('Y-m-d H:i:s');
        $comment = commentParticipate::create($data);
        return $this->sendResponse($comment, 'Done');
    }

    public function getParticipateComments($id)
    {
        $comments = CommentParticipate::where('participate_id', $id)
            ->orderBy('id', 'desc')
            ->with('participates')
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
                'comment_id' => $comment->id,
                'participate_id' => $comment->participate_id,
                'content' => $comment->content,
                'date_comment' => $comment->date_comment,
                'name_participate' => $comment->participates->name_participate,
                'mobile_participate' => $comment->participates->mobile_participate,
                'namebank_participate' => $comment->participates->namebank_participate,
                'numberbank_participate' => $comment->participates->numberbank_participate,
                'add_date' => $comment->participates->add_date,
                'update_date' => $comment->participates->update_date,
            ];
        });
    }
}
