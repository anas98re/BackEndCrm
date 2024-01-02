<?php

namespace App\Services\TaskManangement;

use App\Http\Requests\TaskManagementRequests\GroupRequest;
use App\Http\Requests\TaskManagementRequests\TaskRequest;
use App\Models\client_invoice;
use App\Models\clients;
use App\Models\notifiaction;
use App\Models\statuse_task_fraction;
use App\Models\task;
use App\Models\taskStatus;
use App\Models\tsks_group;
use App\Notifications\SendNotification;
use App\Services\JsonResponeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class TaskProceduresService extends JsonResponeService
{
    private $MyQueriesService;

    public function __construct(queriesService $MyQueriesService)
    {
        $this->MyQueriesService = $MyQueriesService;
    }

    public function handleNotificationForTaskProcedures($message, $type, $to_user, $invoice_id, $client_id)
    {
        $userToken = DB::table('user_token')->where('fkuser', $to_user)
            ->where('token', '!=', null)
            ->first();
        $communication = DB::table('client_communication')
            ->where('fk_client', $client_id)
            ->where('id_invoice', $invoice_id)
            ->first();
        Notification::send(
            null,
            new SendNotification(
                'مهمة',
                '/id_client =' . $client_id . '/id_invoice=' . $invoice_id .
                    '/id_communication=' . ($communication != null ? $communication->id_communication : 'null'),
                $message,
                [($userToken != null ? $userToken->token : 'null')]
            )
        );

        notifiaction::create([
            'message' => $message,
            'type_notify' => $type,
            'to_user' => $to_user,
            'isread' => 0,
            'data' => $client_id . '/' . $invoice_id .
                '/' . ($communication != null ? $communication->id_communication : 'null'),
            'from_user' => 0,
            'dateNotify' => Carbon::now('Asia/Riyadh')
        ]);
    }

    public function handleNotificationForTaskManual(
        $message,
        $type,
        $to_user,
        $from_user,
        $from_Nameuser,
        $from_department,
        $from_region,
        $userTo_Value,
        $departmentTo_Value,
        $regionTo_Value,
        $start_Date
    ) {
        $userToken = DB::table('user_token')->where('fkuser', $to_user)
            ->where('token', '!=', null)
            ->first();
        $fromWhat = (
            $from_Nameuser ? $from_Nameuser : ($from_department ? $from_department : $from_region)
        );
        $toWhat = (
            $userTo_Value ? $userTo_Value : ($departmentTo_Value ? $departmentTo_Value : $regionTo_Value)
        );
        $message = 'مهمة من ( ? ) الى ( ! ) ، تاريخ البدء % ';
        $messageDescription = str_replace('?', $fromWhat, $message);
        $fullMessage1 = str_replace('!', $toWhat, $messageDescription);
        $fullMessage = str_replace('%', $start_Date, $fullMessage1);
        Notification::send(
            null,
            new SendNotification(
                'مهمة',
                'Tsk',
                $fullMessage,
                [($userToken != null ? $userToken->token : 'null')]
            )
        );

        notifiaction::create([
            'message' => $fullMessage,
            'type_notify' => $type,
            'to_user' => $to_user,
            'isread' => 0,
            'data' => 'Tsk',
            'from_user' => $from_user,
            'dateNotify' => Carbon::now('Asia/Riyadh')
        ]);
    }

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
                $users = $this->MyQueriesService->departmentSupervisorsToTheRequiredLevelForTaskProcedures(2);
                foreach ($users as $userID) {
                    $this->handleNotificationForTaskProcedures(
                        $message = $task->title,
                        $type = 'task',
                        $to_user = $userID,
                        $invoice_id = $idInvoice,
                        $client_id = $id_clients
                    );
                }
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
                $users = $this->MyQueriesService->departmentSupervisorsToTheRequiredLevelForTaskProcedures(4);
                foreach ($users as $userID) {
                    $this->handleNotificationForTaskProcedures(
                        $message = $task->title,
                        $type = 'task',
                        $to_user = $userID,
                        $invoice_id = $idInvoice,
                        $client_id = $client->id_clients
                    );
                }
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
