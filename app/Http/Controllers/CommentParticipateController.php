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
        $comment = commentParticipate::create($request->all());
        $comment['date_comment'] = Carbon::now('Asia/Riyadh')->format('Y-m-d H:i:s');
        return $this->sendResponse($comment, 'Done');
    }

    public function getParticipateComments($id)
    {
        $comments = commentParticipate::where('participate_id', $id)
            ->orderBy('id', 'desc')->get();
        return $this->sendResponse($comments, 'These are all comments');
    }
}
