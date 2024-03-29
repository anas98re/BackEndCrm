<?php

namespace App\Http\Controllers;

use App\Models\task;
use App\Http\Requests\StoretaskRequest;
use App\Http\Requests\TaskManagementRequests\TaskRequest;
use App\Http\Requests\UpdatetaskRequest;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskResourceForFilter;
use App\Models\client_invoice;
use App\Services\TaskManangement\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private $MyService;

    public function __construct(TaskService $MyService)
    {
        $this->MyService = $MyService;
    }

    public function addTask(TaskRequest $request)
    {
        $data = $this->MyService->addTask($request);
        return ($data) ?
            $this->sendResponse($data, 'Added Done') :
            $this->sendError($data, 'Error');
    }

    public function editTask(Request $request, $id)
    {
        $data = $this->MyService->editTask($request, $id);
        return ($data) ?
            $this->sendResponse($data, 'Updated Done') :
            $this->sendError($data, 'Error');
    }

    public function assignTaskToEmployee(Request $request, $id)
    {
        $data = $this->MyService->assignTaskToEmployee($request, $id);
        return $this->sendResponse($data, 'Done');
    }


    public function changeStatuseTask(Request $request, $id)
    {
        $data = $this->MyService->changeStatuseTask($request, $id);
        return ($data === true) ?
            $this->sendResponse($data, 'Done') :
            $this->sendError($data, 'Error');
    }

    public function viewTasksByIdAssigned($id)
    {
        $data = $this->MyService->viewTasksByIdAssigned($id);
        return ($data) ?
            $this->sendResponse(TaskResource::collection($data), 'These are tasks assigned by this user') :
            $this->sendError($data, 'Not Found');
    }

    public function viewTaskByIdTask($id)
    {
        $data = $this->MyService->viewTaskByIdTask($id);
        return ($data) ?
        $this->sendResponse(new TaskResource($data), 'This is the task') :
            $this->sendError($data, 'Not Found');
    }

    public function viewAllTasksByStatus($statusName)
    {
        $data = $this->MyService->viewAllTasksByStatus($statusName);
        return ($data) ?
            $this->sendResponse($data, 'These are all tasks by this status') :
            $this->sendError($data, 'Not Found');
    }

    public function viewAllTasksByDateTimeCrated(Request $request)
    {
        $data = $this->MyService->viewAllTasksByDateTimeCrated($request);
        return ($data) ?
            $this->sendResponse($data, 'These are all tasks by this Date Time Crated') :
            $this->sendError($data, 'Not Found');
    }

    public function viewAllTasks()
    {
        $data = $this->MyService->viewAllTasks();
        return ($data) ?
            $this->sendResponse(TaskResource::collection($data), 'These are all tasks with filters') :
            $this->sendError($data, 'Not Found');
    }

    public function filterTaskesByAll(Request $request)
    {
        $data = $this->MyService->filterTaskesByAll($request);
        return (
            $data ?
            $this->sendResponse(TaskResourceForFilter::collection($data), 'These are all tasks with filters') :
            $this->sendResponse($data, 'Not Found')
        );
    }

    public function changeTaskGroup(Request $request, $id)
    {
        $data = $this->MyService->changeTaskGroup($request, $id);
        return ($data) ?
            $this->sendResponse($data, 'Done Updated Group') :
            $this->sendError($data, 'Error');
    }

    public function addAttachmentsToTask(Request $request, $id)
    {
        $data = $this->MyService->addAttachmentsToTask($request, $id);
        return ($data) ?
            $this->sendResponse($data, 'Done Added Attachments To Task') :
            $this->sendError($data, 'Error');
    }

    public function addCommentToTask(Request $request, $id)
    {
        $data = $this->MyService->addCommentToTask($request, $id);
        return ($data) ?
            $this->sendResponse($data, 'Done Added comment To Task') :
            $this->sendError($data, 'Error');
    }

    public function viewCommentsByTaskId($id)
    {
        $data = $this->MyService->viewCommentsByTaskId($id);
        return ($data) ?
            $this->sendResponse($data, 'These are all comments for this Task') :
            $this->sendError($data, 'Error');
    }

    public function getGroupsInfo()
    {
        $data = $this->MyService->getGroupsInfo();
        return ($data) ?
            $this->sendResponse($data, 'These are all groups') :
            $this->sendError($data, 'Error');
    }
}
