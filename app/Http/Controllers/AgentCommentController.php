<?php

namespace App\Http\Controllers;

use App\Models\agentComment;
use App\Http\Requests\StoreagentCommentRequest;
use App\Http\Requests\UpdateagentCommentRequest;
use App\Services\AgentCommentService;
use Illuminate\Http\Request;

class AgentCommentController extends Controller
{
    private $myService;
    public function __construct(AgentCommentService $myService)
    {
        $this->myService = $myService;
    }

    public function addCommentAgent(Request $request)
    {
        $comment = $this->myService->addCommentAgentService($request);
        return $this->sendResponse($comment, 'Done');
    }

    public function getAgentComments($id)
    {
        $comments = $this->myService->getAgentCommentsServices($id);
        return $this->sendResponse($comments, 'These are all comments for this Agent');
    }

}
