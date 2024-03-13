<?php

namespace App\Http\Controllers;

use App\Models\task;
use App\Http\Requests\StoretaskRequest;
use App\Http\Requests\TaskManagementRequests\GroupRequest;

use App\Services\TaskManangement\GroupService;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    private $MyService;

    public function __construct(GroupService $MyService)
    {
        $this->MyService = $MyService;
    }

    public function addGroup(GroupRequest $request)
    {
        $data = $this->MyService->addGroup($request);
        return $this->sendResponse($data, 'Done');
    }

    public function editGroup(Request $request, $id)
    {
        $data = $this->MyService->editGroup($request, $id);
        return $this->sendResponse($data, 'Done');
    }
}
