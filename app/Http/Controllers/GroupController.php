<?php

namespace App\Http\Controllers;

use App\Models\task;
use App\Http\Requests\StoretaskRequest;
use App\Http\Requests\TaskManagementRequests\GroupRequest;

use App\Services\TaskManangement\GroupService;

class GroupController extends Controller
{
    private $MyService;

    public function __construct(GroupService $MyService)
    {
        $this->MyService = $MyService;
    }

    public function addGroup(GroupRequest $request)
    {
        return $request->id_user;
        return auth('sanctum')->user();
        $data = $this->MyService->addGroup($request);
        return $this->sendResponse($data,'Done');
    }
}
