<?php

namespace App\Services\TaskManangement;

use App\Http\Requests\Registeration\RegisterationRequest;
use App\Http\Requests\TaskManagementRequests\TaskRequest;
use App\Models\attachment;
use App\Models\statuse_task_fraction;
use App\Models\task;
use App\Models\task_collaborator;
use App\Models\taskStatus;
use App\Models\User;
use App\Services\JsonResponeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use PhpParser\Node\Stmt\TryCatch;
use SebastianBergmann\Type\VoidType;

class TaskService extends JsonResponeService
{
    private $queriesService;
    public function __construct(queriesService $queriesService)
    {
        $this->queriesService = $queriesService;
    }

    public function addTask(TaskRequest $request)
    {
        try {
            DB::beginTransaction();

            $task = new Task();
            $task->title = $request->title;
            $task->assigned_by = auth('sanctum')->user()->id_user;
            $task->assigned_to = $request->assigned_to;
            $task->client_id = $request->client_id;
            $task->invoice_id = $request->invoice_id;
            $task->group_id = $request->group_id;
            $task->start_date = $request->start_date;
            $task->deadline = $request->deadline;
            $task->hours = $request->hours;
            $task->recurring = $request->recurring;
            $task->recurring_type = $request->recurring_type;
            $task->Number_Of_Recurring = $request->Number_Of_Recurring;
            $task->save();

            if ($task) {
                $taskStatuse = taskStatus::where('name', 'Open')->first();
                $statuse_task_fraction = new statuse_task_fraction();
                $statuse_task_fraction->task_id = $task->id;
                $statuse_task_fraction->task_statuse_id = $taskStatuse->id;
                $statuse_task_fraction->save();
            }

            if ($request->has('file_path')) { // If we want add attachment files to the task..
                $attachment = new attachment();
                $attachment->file_path = $request->file_path;
                $attachment->task_id = $task->id;
                $attachment->create_date = $request->start_date;
                $attachment->created_by = auth('sanctum')->user()->id_user;
                $attachment->save();
            }

            if ($request->has('collaborator_employee_id')) { // If we want add any collaborator employees to the task..
                for ($i = 0; $i < count($request->collaborator_employee_id); $i++) {
                    $task_collaborator = new task_collaborator();
                    $task_collaborator->collaborator_employee_id = $request->collaborator_employee_id[$i];
                    $task_collaborator->task_id = $task->id;
                    $task_collaborator->save();
                }
            }

            DB::commit();
            return $task;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function assignTaskToEmployee(Request $request, $id)
    {
        $task = task::find($id);
        if (count($request->assigned_to) == 1) {
            $task->assigned_to = $request->assigned_to[0];
            $task->save();
        } else {
            for ($i = 1; $i < count($request->assigned_to); $i++) {
                $task_collaborator = new task_collaborator();
                $task_collaborator->collaborator_employee_id = $request->assigned_to[$i];
                $task_collaborator->task_id = $task->id;
                $task_collaborator->save();
            }
        }
        return true;
    }

    public function changeStatuseTask(Request $request, $id)
    {
        $task = task::find($id);
        $updatedData = DB::table('statuse_task_fraction')
            ->where('task_id', $task->id)
            ->update([
                'task_statuse_id' => $request->task_statuse_id,
                'changed_by' => auth('sanctum')->user()->id_user
            ]);
        if (!$updatedData) {
            return false;
        }
        return true;
    }

    public function viewTasksByIdAssigned($id)
    {
        $tasks = task::where('assigned_by', $id)->get();
        if (!$tasks) {
            return false;
        }
        return $tasks;
    }

    public function viewTaskByIdTask($id)
    {
        $task = task::find($id);
        if (!$task) {
            return false;
        }
        return $task;
    }

    public function viewAllTasksByStatus($statusName)
    {
        $tasks = DB::table('tasks')
            ->join('statuse_task_fraction', 'tasks.id', '=', 'statuse_task_fraction.task_id')
            ->join('task_statuses', 'statuse_task_fraction.task_statuse_id', '=', 'task_statuses.id')
            ->where('task_statuses.name', '=', $statusName)
            ->get();
        if (!$tasks) {
            return false;
        }
        return $tasks;
    }

    public function viewAllTasksByDateTimeCrated(Request $request)
    {
        $tasks = DB::table('tasks')
            ->where('dateTimeCreated', $request->dateTimeCreated)
            ->get();
        if (!$tasks) {
            return false;
        }
        return $tasks;
    }


    public function filterTaskesByAll($request)
    {
        $task = new Task();
        $dataFilter = $task->filterTaskesByAll($request);
        return (
            count($dataFilter) > 0 ?
            $dataFilter :
            false
        );
    }

    public function viewAllTasks()
    {
        $tasks = task::all();
        if (!$tasks) {
            return false;
        }
        return $tasks;
    }

    public function changeTaskGroup($request, $id)
    {
        $task = task::find($id);
        $task->update([
            'group_id' => $request->group_id
        ]);
        return ($task ? true : false);
    }
}
