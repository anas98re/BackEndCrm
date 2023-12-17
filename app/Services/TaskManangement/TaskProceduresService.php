<?php

namespace App\Services\TaskManangement;

use App\Http\Requests\TaskManagementRequests\GroupRequest;
use App\Http\Requests\TaskManagementRequests\TaskRequest;
use App\Models\statuse_task_fraction;
use App\Models\task;
use App\Models\taskStatus;
use App\Models\tsks_group;
use App\Services\JsonResponeService;
use Illuminate\Support\Facades\DB;


class TaskProceduresService extends JsonResponeService
{
    public function addTaskAfterApproveInvoice($idInvoice, $id_clients, $client_communication)
    {
        try {
            DB::beginTransaction();

            $task = new task();
            $task->title = 'welcome to clients';
            $task->description = 'you should to welcome';
            $task->invoice_id = $idInvoice;
            $task->client_id = $id_clients;
            $task->id_communication = $client_communication;
            $task->public_Type = 'welcome';
            $task->assigend_department_from  = 2;
            $task->assigend_department_to  = 2;
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

    public function afterCommunicateWithClient($idInvoice, $id_communication, $iduser_updateed)
    {
        try {
            DB::beginTransaction();

            $task = new task();
            $task->title = 'for communicate install 2';
            $task->description = 'you should to install 2';
            $task->invoice_id = $idInvoice;
            $task->assigned_to = $iduser_updateed;
            $task->id_communication = $id_communication;
            $task->public_Type = 'com_install_2';
            $task->assigend_department_from  = 4;
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
}
