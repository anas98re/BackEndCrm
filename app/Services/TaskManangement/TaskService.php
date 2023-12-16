<?php

namespace App\Services\TaskManangement;

use App\Http\Requests\Registeration\RegisterationRequest;
use App\Http\Requests\TaskManagementRequests\TaskRequest;
use App\Models\attachment;
use App\Models\statuse_task_fraction;
use App\Models\task;
use App\Models\task_collaborator;
use App\Models\task_comment;
use App\Models\taskStatus;
use App\Models\tsks_group;
use App\Models\User;
use App\Models\users;
use App\Services\JsonResponeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use PhpParser\Node\Stmt\TryCatch;
use SebastianBergmann\Type\VoidType;
use Carbon\CarbonInterval;
use DateTime;

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
            $task->created_by = auth('sanctum')->user()->id_user;


            if ($request->hasAny(['assigned_to', 'assigend_department_to', 'assigend_region_to'])) {
                $currentRequestFromThese = array_intersect_key(
                    $request->all(),
                    array_flip(['assigned_to', 'assigend_department_to', 'assigend_region_to'])
                );

                foreach ($currentRequestFromThese as $key => $value) {
                    switch ($key) {
                        case 'assigned_to':
                            switch ($request->assignment_type_from) {
                                case 'user':
                                    $task->assigned_by = auth('sanctum')->user()->id_user;
                                    break;
                                case 'department':
                                    $currentData = users::where('id_user', auth('sanctum')->user()->id_user)->first();
                                    $task->assigend_department_from = $currentData->type_administration;
                                    break;
                                case 'region':
                                    $currentData = users::where('id_user', auth('sanctum')->user()->id_user)->first();
                                    $task->assigend_region_from = $currentData->fk_regoin;
                                    break;
                            }
                            $task->assigned_to = $value;
                            break;
                        case 'assigend_department_to':
                            switch ($request->assignment_type_from) {
                                case 'user':
                                    $task->assigned_by = auth('sanctum')->user()->id_user;
                                    break;
                                case 'department':
                                    $currentData = users::where('id_user', auth('sanctum')->user()->id_user)->first();
                                    $task->assigend_department_from = $currentData->type_administration;
                                    break;
                                case 'region':
                                    $currentData = users::where('id_user', auth('sanctum')->user()->id_user)->first();
                                    $task->assigend_region_from = $currentData->fk_regoin;
                                    break;
                            }
                            $task->assigend_department_to = $value;
                            break;
                        case 'assigend_region_to':
                            switch ($request->assignment_type_from) {
                                case 'user':
                                    $task->assigned_by = auth('sanctum')->user()->id_user;
                                    break;
                                case 'department':
                                    $currentData = users::where('id_user', auth('sanctum')->user()->id_user)->first();
                                    $task->assigend_department_from = $currentData->type_administration;
                                    break;
                                case 'region':
                                    $currentData = users::where('id_user', auth('sanctum')->user()->id_user)->first();
                                    $task->assigend_region_from = $currentData->fk_regoin;
                                    break;
                            }
                            $task->assigend_region_to = $value;
                            break;
                    }
                }
            }



            $task->client_id = $request->client_id;
            $task->description = $request->description;
            $task->invoice_id = $request->invoice_id;
            $task->group_id = $request->group_id;
            $task->start_date = $request->start_date;
            $task->deadline = $request->deadline;
            $task->hours = $request->hours;

            /////////////////////////////////// To calucate the hours from deadline
            // $startDateTime = Carbon::parse($request->start_date);
            // $time = $startDateTime->format('H:i:s');
            // $startTimeMonth = $startDateTime->format('y:m:d');
            // $endDateTime = Carbon::parse($request->deadline);
            // $EndTimeMonth = $endDateTime->format('y:m:d');
            // return   $diffHours = $startDateTime->diffInHours($endDateTime);
            // $diffDays = $startDateTime->diffInDays($endDateTime);

            // return $totalDuration = ($diffDays * 9) + $diffHours;
            // $time1 = new DateTime('17:00:00');
            // $time2 = new DateTime($time);
            // $diff = $time1->diff($time2);
            // $totalHours = $diff->h;
            // $totalMinutes = $diff->i;

            // return $stayTimeFromDay = $totalHours . ':' . sprintf('%02d', $totalMinutes); // Format minutes with leading zero if necessary

            // $totalMinutes = ($diff->h * 60) + $diff->i;
            // $totalDuration = floor(($totalMinutes * 9) / 60); // Use floor() to round down to the nearest whole number of hours

            // return $totalDuration . ':' . sprintf('%02d', (($totalMinutes * 9) % 60)); // Format minutes with leading zero if necessary


            // $currentDateTime = $startDateTime;

            // while ($currentDateTime < $endDateTime) {
            //     if ($currentDateTime->isFriday()) {
            //         $currentDateTime->addDay();
            //         continue;
            //     }

            //     if (
            //         $currentDateTime->isBefore($currentDateTime->copy()->setTime(8, 30))
            //         || $currentDateTime->isAfter($currentDateTime->copy()->setTime(17, 0))
            //     ) {
            //         $currentDateTime->addDay();
            //         continue;
            //     }

            //     $hours += $currentDateTime->diffInHours($endDateTime);
            //     $currentDateTime->addDay();
            // }

            // return $hours;



            ///////////////////////////////////////////

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

    public function editTask($request, $id)
    {
        try {
            DB::beginTransaction();

            $task = task::find($id);
            $task->title = $request->title;
            if ($request->has('assigned_to')) {
                $task->assigned_by = auth('sanctum')->user()->id_user;
                $task->assigned_to = $request->assigned_to;
            }
            $task->client_id = $request->client_id;
            $task->description = $request->description;
            $task->invoice_id = $request->invoice_id;
            $task->group_id = $request->group_id;
            if ($request->has('start_date')) {
                $task->start_date = $request->start_date;
            } else {
                $task->start_date = Carbon::now();
            }
            $task->deadline = $request->deadline;
            $task->hours = $request->hours;
            if ($request->has('recurring')) {
                $task->recurring = $request->recurring;
            } else {
                $task->recurring = 0;
            }
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
                $task_collaborator = task_collaborator::where('task_id', $task->id)->delete();
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
        $task->assigned_by = auth('sanctum')->user()->id_user;
        $task->save();
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
        $task = Task::with(
            'taskStatuses',
            'assignedByUser',
            'assignedToUser',
            'taskGroup',
            'Clients',
            'invoices'
        )->find($id);
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
        // User::paginate(15);
        $tasks = task::paginate(2);
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

    public function addAttachmentsToTask($request, $id)
    {
        $task = task::find($id);
        $attachment = new attachment();
        $filePath = $request->file_path->store('public/Files');
        $attachment->file_path = $filePath;
        $attachment->task_id = $task->id;
        $attachment->create_date = $task->start_date;
        $attachment->created_by = auth('sanctum')->user()->id_user;
        $attachment->save();
        return ($attachment ? true : false);
    }

    public function addCommentToTask($request, $id)
    {
        $task = task::find($id);
        $comment = new task_comment();
        $comment->CommentText = $request->CommentText;
        $comment->comment_date = Carbon::now();
        $comment->commented_by = auth('sanctum')->user()->id_user;
        $comment->task_id = $task->id;
        $comment->save();
        return ($comment ? true : false);
    }

    public function viewCommentsByTaskId($id)
    {
        $comments = task_comment::where('task_id', $id)
            ->with('commented_byUser', 'tasks')
            ->select('id', 'CommentText', 'comment_date', 'task_id', 'commented_by')
            ->get()
            ->makeHidden(['task_id', 'commented_by']);

        return ($comments ? $comments : false);
    }

    public function getGroupsInfo()
    {
        return tsks_group::select('id', 'groupName')->get();
    }
}
