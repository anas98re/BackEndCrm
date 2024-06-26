<?php

namespace App\Services\TaskManangement;

use App\Constants;
use App\Http\Requests\Registeration\RegisterationRequest;
use App\Http\Requests\TaskManagementRequests\TaskRequest;
use App\Models\attachment;
use App\Models\client_comment;
use App\Models\clients;
use App\Models\config_table;
use App\Models\managements;
use App\Models\regoin;
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
use Illuminate\Support\Facades\Auth;

class TaskService extends JsonResponeService
{
    private $MyService;
    private $queriesService;

    public function __construct(TaskProceduresService $MyService, queriesService $queriesService)
    {
        $this->MyService = $MyService;
        $this->queriesService = $queriesService;
    }



    public function addTask(TaskRequest $request)
    {
        try {
            DB::beginTransaction();

            $task = new Task();
            $task->title = $request->title;
            $task->created_by = $request->id_user;
            $task->assignment_type_from = $request->assignment_type_from;

            if ($request->hasAny(['assigned_to', 'assigend_department_to', 'assigend_region_to'])) {
                $currentRequestFromThese = array_intersect_key(
                    $request->all(),
                    array_flip(['assigned_to', 'assigend_department_to', 'assigend_region_to'])
                );
                $userValue = null;
                $departmentValue  = null;
                $regionValue  = null;
                $userToValue = null;
                $departmentToValue = null;
                $regionToValue = null;
                $startDate = $request->input('start_date') ?? Carbon::now('Asia/Riyadh');
                // $this->MyService->fillAssignedviaType(
                //     $startDate,
                //     $request->id_user,
                //     $request->assignment_type_from,
                //     $request->title
                // );
                foreach ($currentRequestFromThese as $key => $value) {
                    switch ($key) {
                        case 'assigned_to':
                            switch ($request->assignment_type_from) {
                                case 'user':
                                    $task->assigned_by = $request->id_user;
                                    $userValue = users::where('id_user', $request->id_user)
                                        ->first()->nameUser;
                                    break;
                                case 'department':
                                    $currentData = users::where('id_user', $request->id_user)->first();
                                    $task->assigend_department_from = $currentData->type_administration;
                                    $departmentValue = managements::where('idmange', $currentData->type_administration)
                                        ->first()->name_mange;
                                    break;
                                case 'region':
                                    $currentData = users::where('id_user', $request->id_user)->first();
                                    $task->assigend_region_from = $currentData->fk_regoin;
                                    $regionValue = regoin::where('id_regoin', $currentData->fk_regoin)
                                        ->first()->name_regoin;
                                    break;
                            }
                            $task->assigned_to = $value;
                            $userToValue = users::where('id_user', $value)
                                ->first()->nameUser;
                            $users = $this->queriesService->ToBothDepartmentAndRegionSupervisorsToTheRequiredLevelForTaskProcedures($value);
                            foreach ($users as $userID) {
                                $this->MyService->handleNotificationForTaskManual(
                                    $message = $request->title,
                                    $type = 'task',
                                    $to_user = $userID,
                                    $from_user = $request->id_user,
                                    $from_Nameuser = $userValue,
                                    $from_department = $departmentValue,
                                    $from_region = $regionValue,
                                    $userTo_Value = $userToValue,
                                    $departmentTo_Value = $departmentToValue,
                                    $regionTo_Value = $userToValue,
                                    $start_Date = $startDate
                                );
                            }
                            break;
                        case 'assigend_department_to':
                            switch ($request->assignment_type_from) {
                                case 'user':
                                    $task->assigned_by = $request->id_user;
                                    $userValue = users::where('id_user', $request->id_user)->first()->nameUser;
                                    break;
                                case 'department':
                                    $currentData = users::where('id_user', $request->id_user)->first();
                                    $task->assigend_department_from = $currentData->type_administration;
                                    $departmentValue = managements::where('idmange', $currentData->type_administration)
                                        ->first()->name_mange;
                                    break;
                                case 'region':
                                    $currentData = users::where('id_user', $request->id_user)->first();
                                    $task->assigend_region_from = $currentData->fk_regoin;
                                    $regionValue = regoin::where('id_regoin', $currentData->fk_regoin)
                                        ->first()->name_regoin;
                                    break;
                            }
                            $task->assigend_department_to = $value;
                            $departmentToValue = managements::where('idmange', $value)
                                ->first()->name_mange;
                            $users = $this->queriesService->departmentSupervisorsToTheRequiredLevelForTaskProcedures($value);
                            foreach ($users as $userID) {
                                $this->MyService->handleNotificationForTaskManual(
                                    $message = $request->title,
                                    $type = 'task',
                                    $to_user = $userID,
                                    $from_user = $request->id_user,
                                    $from_Nameuser = $userValue,
                                    $from_department = $departmentValue,
                                    $from_region = $regionValue,
                                    $userTo_Value = $userToValue,
                                    $departmentTo_Value = $departmentToValue,
                                    $regionTo_Value = $regionToValue,
                                    $start_Date = $startDate
                                );
                            }
                            break;
                        case 'assigend_region_to':
                            switch ($request->assignment_type_from) {
                                case 'user':
                                    $task->assigned_by = $request->id_user;
                                    $userValue = users::where('id_user', $request->id_user)->first()->nameUser;
                                    break;
                                case 'department':
                                    $currentData = users::where('id_user', $request->id_user)->first();
                                    $task->assigend_department_from = $currentData->type_administration;
                                    $departmentValue = managements::where('idmange', $currentData->type_administration)
                                        ->first()->name_mange;
                                    break;
                                case 'region':
                                    $currentData = users::where('id_user', $request->id_user)->first();
                                    $task->assigend_region_from = $currentData->fk_regoin;
                                    $regionValue = regoin::where('id_regoin', $currentData->fk_regoin)
                                        ->first()->name_regoin;
                                    break;
                            }
                            $task->assigend_region_to = $value;
                            if ($value != Constants::ALL_BRUNSHES) {
                                $regionToValue = regoin::where('id_regoin', $value)
                                    ->first()->name_regoin;
                                $users = $this->queriesService->BranchSupervisorsToTheRequiredLevelForTaskProcedures($value);
                                foreach ($users as $userID) {
                                    $this->MyService->handleNotificationForTaskManual(
                                        $message = $request->title,
                                        $type = 'task',
                                        $to_user = $userID,
                                        $from_user = $request->id_user,
                                        $from_Nameuser = $userValue,
                                        $from_department = $departmentValue,
                                        $from_region = $regionValue,
                                        $userTo_Value = $userToValue,
                                        $departmentTo_Value = $departmentToValue,
                                        $regionTo_Value = $regionToValue,
                                        $start_Date = $startDate
                                    );
                                }
                            }
                            break;
                    }
                }
            }

            $task->client_id = $request->client_id;
            $task->public_Type = $request->public_Type;
            $task->main_type_task = $request->main_type_task;
            $task->description = $request->description;
            $task->invoice_id = $request->invoice_id;
            $task->group_id = $request->group_id;
            $task->start_date = $request->input('start_date') ?? Carbon::now('Asia/Riyadh');
            $task->deadline = $request->deadline;

            // To calucate the hours from deadline
            $resultOfHouresWithoutFriday = $this->queriesService
                ->calucateTheHoursFromDeadline($request->start_date, $request->deadline);
            $task->hours = $resultOfHouresWithoutFriday;

            $task->recurring = $request->recurring;
            $task->dateTimeCreated = Carbon::now('Asia/Riyadh');
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
                $generatedPath = $this->MyService->handlingImageName($request->file('file_path'));
                $attachment->file_path = $generatedPath;
                $attachment->task_id = $task->id;
                $attachment->create_date = $request->start_date;
                $attachment->created_by = $request->id_user;
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
            $comment = null;
            if ($request->public_Type == 'linkComment') {
                $lastId = DB::table('client_comment')
                    ->orderBy('id_comment', 'desc')
                    ->value('id_comment');

                $idComment = $lastId + 1;
                $comment = new client_comment();
                $comment->id_comment = $idComment;
                $comment->content = ($request->description ? $request->description : 'no description');
                $comment->date_comment = Carbon::now('Asia/Riyadh');
                $comment->fk_client = ($request->client_id ? $request->client_id : null);;
                $comment->fk_user = $request->id_user;
                $comment->save();
            }

            DB::commit();

            return  $data = [
                "task" => $task,
                "commentID" => ($comment != null ? $comment->id_comment : 'null') // Add the comment object to the response
            ];
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
            if ($request->hasAny(['assigned_to', 'assigend_department_to', 'assigend_region_to'])) {
                $currentRequestFromThese = array_intersect_key(
                    $request->all(),
                    array_flip(['assigned_to', 'assigend_department_to', 'assigend_region_to'])
                );

                foreach ($currentRequestFromThese as $key => $value) {
                    switch ($key) {
                        case 'assigned_to':
                            $task->assigned_to = $value;
                            $task->assigend_department_to = $task->assigend_region_to = null;
                            break;
                        case 'assigend_department_to':
                            $task->assigend_department_to = $value;
                            $task->assigned_to = $task->assigend_region_to = null;
                            break;
                        case 'assigend_region_to':
                            $task->assigend_region_to = $value;
                            $task->assigned_to = $task->assigend_department_to = null;
                            break;
                    }
                }
            }
            $task->client_id = $request->client_id;
            $task->public_Type = $request->public_Type;
            $task->main_type_task = $request->main_type_task;
            $task->description = $request->description;
            $task->invoice_id = $request->invoice_id;
            $task->group_id = $request->group_id;
            $task->start_date = $request->input('start_date');
            $task->deadline = $request->deadline;

            // To update the hours from deadline
            $resultOfHouresWithoutFriday = $this->queriesService
                ->calucateTheHoursFromDeadline($request->start_date, $request->deadline);
            $task->hours = $resultOfHouresWithoutFriday;

            $task->recurring = $request->input('recurring', 0);
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
                $attachment->created_by = $request->id_user;
                $attachment->save();
            }

            if ($request->has('collaborator_employee_id')) { // If we want to add any collaborator employees to the task...
                $existingCollaborators = task_collaborator::where('task_id', $task->id)->pluck('collaborator_employee_id')->toArray();
                $newCollaborators = $request->collaborator_employee_id;

                // Find the collaborators to be added (newCollaborators - existingCollaborators)
                $collaboratorsToAdd = array_diff($newCollaborators, $existingCollaborators);

                // Find the collaborators to be removed (existingCollaborators - newCollaborators)
                $collaboratorsToRemove = array_diff($existingCollaborators, $newCollaborators);

                // Remove the collaborators to be removed
                // task_collaborator::where('task_id', $task->id)->whereIn('collaborator_employee_id', $collaboratorsToRemove)->delete();

                // Add the collaborators to be added
                foreach ($collaboratorsToAdd as $collaborator) {
                    $taskCollaborator = new task_collaborator();
                    $taskCollaborator->collaborator_employee_id = $collaborator;
                    $taskCollaborator->task_id = $task->id;
                    $taskCollaborator->save();
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
        $task->assigned_by = $request->id_user;
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
        // return auth('sanctum')->user()->id_user; ..
        try {
            DB::beginTransaction();
            $task = task::find($id);
            if ($task->main_type_task != 'ProccessAuto') {
                if ($request->task_statuse_id == 4) {
                    $task->actual_delivery_date = Carbon::now('Asia/Riyadh');
                    $task->save();
                }
                if ($request->task_statuse_id == 8) {
                    $task->recive_date = Carbon::now('Asia/Riyadh');
                    $task->save();
                }
                $updatedData = DB::table('statuse_task_fraction')
                    ->where('task_id', $task->id)
                    ->first();

                if ($request->task_statuse_id != 4 && $updatedData->task_statuse_id == 4) {
                    $task->actual_delivery_date = null;
                    $task->save();
                }

                $updatedData = DB::table('statuse_task_fraction')
                    ->where('task_id', $task->id)
                    ->update([
                        'task_statuse_id' => $request->task_statuse_id,
                        // 'changed_by' => $request->id_user
                        'changed_by' => auth('sanctum')->user()->id_user
                    ]);
            } else {
                if ($task->public_Type == 'AddPayment') {
                    if ($request->task_statuse_id == 4) {
                        $task->actual_delivery_date = Carbon::now('Asia/Riyadh');
                        $task->save();
                    }
                    if ($request->task_statuse_id == 8) {
                        $task->recive_date = Carbon::now('Asia/Riyadh');
                        $task->save();
                    }
                    $updatedData = DB::table('statuse_task_fraction')
                        ->where('task_id', $task->id)
                        ->first();

                    if ($request->task_statuse_id != 4 && $updatedData->task_statuse_id == 4) {
                        $task->actual_delivery_date = null;
                        $task->save();
                    }

                    $updatedData = DB::table('statuse_task_fraction')
                        ->where('task_id', $task->id)
                        ->update([
                            'task_statuse_id' => $request->task_statuse_id,
                            // 'changed_by' => $request->id_user
                            'changed_by' => auth('sanctum')->user()->id_user
                        ]);
                } else {
                    return false;
                }
            }
            DB::commit();
            if (!$updatedData) {
                return false;
            }
            return true;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
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
        // $tasks = task::paginate(2);
        $tasks = task::orderBy('created_at', 'desc')->get();
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
        // $filePath = $request->file_path->store('public/Files');

        $originalFilename = $request->file_path->getClientOriginalName();
        $fileExtension = $request->file_path->getClientOriginalExtension();
        $randomNumber = mt_rand(10000, 99999);

        // Remove the file extension from the original filename
        $filenameWithoutExtension = pathinfo($originalFilename, PATHINFO_FILENAME);

        $modifiedFilename = str_replace(' ', '_', $filenameWithoutExtension) . '_' . $randomNumber;

        // Combine the filename and extension
        $generatedFilename = $modifiedFilename . '.' . $fileExtension;

        $generatedPath = $request->file_path->storeAs('Files', $generatedFilename, 'public');

        $attachment->file_path = $generatedPath;
        $attachment->task_id = $task->id;
        $attachment->create_date = $task->start_date;
        $attachment->created_by = $request->id_user;
        $attachment->save();
        return ($attachment ? true : false);
    }

    public function addCommentToTask($request, $id)
    {
        $task = task::find($id);
        $comment = new task_comment();
        $comment->CommentText = $request->CommentText;
        $comment->comment_date = Carbon::now('Asia/Riyadh');
        $comment->commented_by = $request->id_user;
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




    // New TaskProcedures Services ...
    public function afterInstallClient($data)
    {
        try {
            $client_id = DB::table('client_invoice')->where('id_invoice', $data['idInvoice'])
                ->first();

            $welcomed_user_id = DB::table('client_communication')
                ->where('fk_client', $client_id->fk_idClient)
                ->where('type_communcation', 'تركيب')
                ->where('type_install', 1)
                ->where('id_invoice', $data['idInvoice'])
                ->first();

            $existingTask = task::where('invoice_id', $data['idInvoice'])
                ->where('client_id', $client_id->fk_idClient)
                ->where('public_Type', 'com_install_1')
                ->first();

            $time = config_table::where('name_config', 'period_commincation2')
                ->first()->value_config;
            $carbonDatetime = Carbon::parse(Carbon::now('Asia/Riyadh'))->addDays($time);
            $newDatetime = $carbonDatetime->toDateTimeString();

            $client = clients::where('id_clients', $client_id->fk_idClient)->first();
            $message = 'عميل مشترك ( ? ) يحتاج لتواصل الجودة الأول له';
            $messageDescription = str_replace('?', $client->name_enterprise, $message);
            if (!$existingTask) {
                $task = new task();
                $task->title = 'تواصل جودة اول';
                $task->description = $messageDescription;
                $task->invoice_id = $data['idInvoice'];
                $task->id_communication  = $welcomed_user_id->id_communication;
                $task->client_id  = $client_id->fk_idClient;
                $task->public_Type = 'com_install_1';
                $task->main_type_task = 'ProccessAuto';
                $task->assigend_department_from  = 3;
                if (is_null($welcomed_user_id?->fk_user))
                    $task->assigend_department_to = Constants::TYPE_ADMINISTRATION['CUSTOMER_CARE_MANAGEMENT'];
                else
                    $task->assigned_to  = $welcomed_user_id?->fk_user;
                $task->start_date  = $newDatetime;
                $task->save();

                $service = new TaskProceduresService(new queriesService);
                !empty($task) ? $service->addTaskStatus($task) : null;
                if (is_null($welcomed_user_id?->fk_user)) {
                    $user_ids = users::where('type_administration', Constants::TYPE_ADMINISTRATION['CUSTOMER_CARE_MANAGEMENT'])->get()->pluck('id_user');
                    foreach ($user_ids as $id_user) {
                        $service->handleNotificationForTaskProcedures(
                            $message = $task->title,
                            $type = 'task',
                            $to_user = $id_user,
                            $invoice_id = $data['idInvoice'],
                            $client_id = $client->id_clients
                        );
                    }
                } else
                    $service->handleNotificationForTaskProcedures(
                        $message = $task->title,
                        $type = 'task',
                        $to_user = $welcomed_user_id->fk_user,
                        $invoice_id = $data['idInvoice'],
                        $client_id = $client_id->fk_idClient
                    );
            } else {
                $task = null;
            }
            return $task;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function closeWelcomeTaskAfterUpdateCommunication($data)
    {
        try {
            DB::beginTransaction();
            $task = task::where('id_communication', $data['id_communication'])
                ->where('public_Type', 'welcome')
                ->first();
            if ($task) {
                $statuse_task_fraction = DB::table('statuse_task_fraction')
                    ->where('task_id', $task->id)->first();
                if ($statuse_task_fraction->task_statuse_id == 1) {
                    $task->actual_delivery_date = Carbon::now('Asia/Riyadh');
                    $task->save();
                    DB::table('statuse_task_fraction')
                        ->where('task_id', $task->id)
                        ->update([
                            'task_statuse_id' => 4,
                            'changed_date' => Carbon::now('Asia/Riyadh'),
                            'changed_by' => $data['iduser_updateed']
                        ]);
                } else {
                    return;
                }
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function closeTaskafterCommunicateWithClient($data)
    {
        try {
            DB::beginTransaction();
            $task = task::where('invoice_id', $data['idInvoice'])
                ->where('id_communication', $data['id_communication'])
                ->where('public_Type', 'com_install_2')
                ->first();
            if ($task) {
                $statuse_task_fraction = DB::table('statuse_task_fraction')
                    ->where('task_id', $task->id)->first();
                if ($statuse_task_fraction->task_statuse_id == 1) {
                    $task->actual_delivery_date = Carbon::now('Asia/Riyadh');
                    $task->save();

                    DB::table('statuse_task_fraction')
                        ->where('task_id', $task->id)
                        ->update([
                            'task_statuse_id' => 4,
                            'changed_date' => Carbon::now('Asia/Riyadh'),
                            'changed_by' => $data['iduser_updateed']
                        ]);
                } else {
                    return;
                }
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function closeTaskAfterInstallClient($data)
    {
        try {
            DB::beginTransaction();

            $task = task::where('id_communication', $data['id_communication'])
                ->where('public_Type', 'com_install_1')
                ->first();
            if ($task) {
                $statuse_task_fraction = DB::table('statuse_task_fraction')
                    ->where('task_id', $task->id)->first();
                if ($statuse_task_fraction->task_statuse_id == 1) {
                    $task->actual_delivery_date = Carbon::now('Asia/Riyadh');
                    $task->save();
                    DB::table('statuse_task_fraction')
                        ->where('task_id', $task->id)
                        ->update([
                            'task_statuse_id' => 4,
                            'changed_date' => Carbon::now('Asia/Riyadh'),
                            'changed_by' => $data['iduser_updateed']
                        ]);

                    $this->MyService->afterCommunicateWithClient(
                        $data['idInvoice'],
                        $task->client_id,
                        $data['last_idCommuncation2'],
                        $data['iduser_updateed']
                    );
                } else {
                    return;
                }
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function closeTaskAddVisitDateAfterApproveInvoice($data)
    {
        try {
            DB::beginTransaction();
            $task = task::where('invoice_id', $data['idInvoice'])
                ->where('public_Type', 'AddVisitDate')->first();
            if ($task) {
                $statuse_task_fraction = DB::table('statuse_task_fraction')
                    ->where('task_id', $task->id)->first();
                if ($statuse_task_fraction->task_statuse_id == 1) {
                    $task->actual_delivery_date = Carbon::now('Asia/Riyadh');
                    $task->save();
                    DB::table('statuse_task_fraction')
                        ->where('task_id', $task->id)
                        ->update([
                            'task_statuse_id' => 4,
                            'changed_date' => Carbon::now('Asia/Riyadh'),
                            'changed_by' => $data['iduser_FApprove']
                        ]);
                } else {
                    return;
                }
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }
}
