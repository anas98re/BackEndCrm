<?php

namespace App\Services\TaskManangement;

use App\Http\Requests\TaskManagementRequests\GroupRequest;
use App\Http\Requests\TaskManagementRequests\TaskRequest;
use App\Models\client_invoice;
use App\Models\clients;
use App\Models\statuse_task_fraction;
use App\Models\task;
use App\Models\taskStatus;
use App\Models\tsks_group;
use App\Services\JsonResponeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class TaskProceduresService extends JsonResponeService
{
    public function addTaskStatus($task)
    {
        $taskStatuse = taskStatus::where('name', 'Open')->first();
        $statuse_task_fraction = new statuse_task_fraction();
        $statuse_task_fraction->task_id = $task->id;
        $statuse_task_fraction->task_statuse_id = $taskStatuse->id;
        $statuse_task_fraction->save();
    }

    public function addTaskAfterApproveInvoice($idInvoice, $id_clients, $client_communication)
    {
        try {
            DB::beginTransaction();

            $existingTask = Task::where('invoice_id', $idInvoice)
                ->where('client_id', $id_clients)
                ->where('public_Type', 'welcome')
                ->first();
            $invoice = client_invoice::where('id_invoice', $idInvoice)->first();
            $client = clients::where('id_clients', $invoice->fk_idClient)->first();
            $message = 'عميل مشترك ( ? ) يحتاج للترحيب به';
            $messageDescription = str_replace('?', $client->name_enterprise, $message);
            if (!$existingTask) {
                $task = new task();
                $task->title = 'الترحيب بالعميل';
                $task->description = $messageDescription;
                $task->invoice_id = $idInvoice;
                $task->client_id = $id_clients;
                $task->id_communication = $client_communication;
                $task->public_Type = 'welcome';
                $task->main_type_task = 'ProccessAuto';
                $task->assigend_department_from  = 2;
                $task->assigend_department_to  = 2;
                $task->save();

                !empty($task) ? $this->addTaskStatus($task) : null;
            } else {
                $task = null;
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

            $existingTask = Task::where('invoice_id', $idInvoice)
                ->where('id_communication', $id_communication)
                ->where('public_Type', 'com_install_2')
                ->first();

            $invoice = client_invoice::where('id_invoice', $idInvoice)->first();
            $client = clients::where('id_clients', $invoice->fk_idClient)->first();
            $message = 'عميل مشترك ( ? ) يحتاج لتواصل جودة ثاني';
            $messageDescription = str_replace('?', $client->name_enterprise, $message);

            if (!$existingTask) {
                $task = new task();
                $task->title = 'تواصل جودة ثاني';
                $task->description = $messageDescription;
                $task->invoice_id = $idInvoice;
                $task->assigned_to = $iduser_updateed;
                $task->id_communication = $id_communication;
                $task->public_Type = 'com_install_2';
                $task->main_type_task = 'ProccessAuto';
                $task->assigend_department_from  = 4;
                $task->save();

                !empty($task) ? $this->addTaskStatus($task) : null;
            } else {
                $task = null;
            }
            DB::commit();
            return $task;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function addTaskToEmployeesResponsibleForClients($key, $value, $Date)
    {
        $message = ' لديك ? عملاء لم يُعلّق لهم ';
        $messageWithPlaceholder = str_replace('?', $value, $message);
        $messageWithDate = $messageWithPlaceholder . ' [منذ تاريخ % لتاريخ اليوم]';
        $messageRegionWithPlaceholder = str_replace('%', $Date, $messageWithDate);

        $task = new task();
        $task->title = 'تعليقات العملاء';
        $task->description = $messageRegionWithPlaceholder;
        $task->assigned_to = $key;
        $task->public_Type = 'checkComments';
        $task->main_type_task = 'ProccessAuto';
        $task->assigend_department_from  = 2;
        $task->dateTimeCreated  = Carbon::now('Asia/Riyadh');

        $task->save();

        !empty($task) ? $this->addTaskStatus($task) : null;
    }
}
