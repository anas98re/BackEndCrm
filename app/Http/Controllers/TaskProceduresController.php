<?php

namespace App\Http\Controllers;

use App\Models\taskStatus;
use App\Http\Requests\StoretaskStatusRequest;
use App\Http\Requests\UpdatetaskStatusRequest;
use App\Models\statuse_task_fraction;
use App\Models\task;
use App\Models\users;
use App\Services\TaskManangement\TaskProceduresService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskProceduresController extends Controller
{
    private $MyService;

    public function __construct(TaskProceduresService $MyService)
    {
        $this->MyService = $MyService;
    }
    public function addTaskToApproveAdminAfterAddInvoice(Request $request)
    {
        try {
            DB::beginTransaction();

            $assigned_to = users::where('fk_regoin', $request->fk_regoin)
                ->where('type_level', 14)->first();

            $task = new task();
            $task->title = 'approve Admin To add Invoice';
            $task->description = 'you have to approve';
            $task->invoice_id = $request->invoice_id;
            $task->public_Type = 'approveAdmin';
            $task->assigend_department_from  = 2;
            $task->assigned_to  = $assigned_to->id_user;
            $task->save();

            if ($task) {
                $taskStatuse = taskStatus::where('name', 'Open')->first();
                $statuse_task_fraction = new statuse_task_fraction();
                $statuse_task_fraction->task_id = $task->id;
                $statuse_task_fraction->task_statuse_id = $taskStatuse->id;
                $statuse_task_fraction->save();
            }

            DB::commit();
            return $task;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function closeTaskApproveAdminAfterAddInvoice(Request $request)
    {
        try {
            DB::beginTransaction();
            $client_communication = DB::table('client_communication')->insertGetId([
                'fk_client' => $request->id_clients,
                'type_communcation' => 'ترحيب',
                'id_invoice' => $request->idInvoice
            ]);
            $task = task::where('invoice_id', $request->idInvoice)->first();
            $task->actual_delivery_date = Carbon::now();
            $task->save();
            DB::table('statuse_task_fraction')
                ->where('task_id', $task->id)
                ->update([
                    'task_statuse_id' => 4,
                    'changed_date' => Carbon::now(),
                    'changed_by' => $request->iduser_approve
                ]);
            $this->MyService->addTaskAfterApproveInvoice(
                $request->idInvoice,
                $request->id_clients,
                $client_communication
            );
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function closeWelcomeTaskAfterUpdateCommunication(Request $request)
    {
        $task = task::where('id_communication', $request->id_communication)->first();
        $task->actual_delivery_date = Carbon::now();
        $task->save();
        DB::table('statuse_task_fraction')
            ->where('task_id', $task->id)
            ->update([
                'task_statuse_id' => 4,
                'changed_date' => Carbon::now(),
                'changed_by' => $request->iduser_approve
            ]);
    }
}
