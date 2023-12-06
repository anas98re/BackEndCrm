<?php

namespace App\Http\Controllers;

use App\Models\task;
use App\Http\Requests\StoretaskRequest;
use App\Http\Requests\TaskManagementRequests\TaskRequest;
use App\Http\Requests\UpdatetaskRequest;
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
        return $this->sendResponse($data, 'Done');
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
            $this->sendResponse($data, 'These are tasks assigned by this user') :
            $this->sendError($data, 'Not Found');
    }

    public function viewTaskByIdTask($id)
    {
        $data = $this->MyService->viewTaskByIdTask($id);
        return ($data) ?
            $this->sendResponse($data, 'This is the task') :
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
            $this->sendResponse($data, 'These are all tasks') :
            $this->sendError($data, 'Not Found');
    }

    public function filterTaskesByAll(Request $request)
    {
        $data = $this->MyService->filterTaskesByAll($request);
        return ($data) ?
            $this->sendResponse($data, 'These are all tasks with filters') :
            $this->sendError($data, 'Not Found');
    }

}
