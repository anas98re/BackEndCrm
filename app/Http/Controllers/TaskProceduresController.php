<?php

namespace App\Http\Controllers;

use App\Models\taskStatus;
use App\Http\Requests\StoretaskStatusRequest;
use App\Http\Requests\UpdatetaskStatusRequest;
use App\Models\client_comment;
use App\Models\clients;
use App\Models\statuse_task_fraction;
use App\Models\task;
use App\Models\users;
use App\Notifications\SendNotification;
use App\Services\TaskManangement\TaskProceduresService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

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
            $existingTask = Task::where('invoice_id', $request->invoice_id)
                ->where('public_Type', 'approveAdmin')
                ->first();

            if (!$existingTask) {
                $task = new task();
                $task->title = 'approve Admin To add Invoice';
                $task->description = 'you have to approve';
                $task->invoice_id = $request->invoice_id;
                $task->public_Type = 'approveAdmin';
                $task->assigend_department_from  = 2;
                $task->assigned_to  = $assigned_to->id_user;
                $task->save();

                !empty($task) ? $this->MyService->addTaskStatus($task) : null;
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

    public function closeTaskApproveAdminAfterAddInvoice(Request $request)
    {
        try {
            DB::beginTransaction();
            $client_communication = DB::table('client_communication')->insertGetId([
                'fk_client' => $request->id_clients,
                'type_communcation' => 'ترحيب',
                'id_invoice' => $request->idInvoice
            ]);
            $task = task::where('invoice_id', $request->idInvoice)
                ->where('public_Type', 'approveAdmin')
                ->first();
            $statuse_task_fraction = DB::table('statuse_task_fraction')
                ->where('task_id', $task->id)->first();
            if ($statuse_task_fraction->task_statuse_id == 1) {
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
            } else {
                return;
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function closeWelcomeTaskAfterUpdateCommunication(Request $request)
    {
        try {
            DB::beginTransaction();
            $task = task::where('id_communication', $request->id_communication)
                ->where('public_Type', 'welcome')
                ->first();
            $statuse_task_fraction = DB::table('statuse_task_fraction')
                ->where('task_id', $task->id)->first();
            if ($statuse_task_fraction->task_statuse_id == 1) {
                $task->actual_delivery_date = Carbon::now();
                $task->save();
                DB::table('statuse_task_fraction')
                    ->where('task_id', $task->id)
                    ->update([
                        'task_statuse_id' => 4,
                        'changed_date' => Carbon::now(),
                        'changed_by' => $request->iduser_updateed
                    ]);
                $this->MyService->afterCommunicateWithClient(
                    $request->idInvoice,
                    $request->id_communication,
                    $request->iduser_updateed
                );
            } else {
                return;
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function afterInstallClient(Request $request)
    {
        try {
            DB::beginTransaction();
            $client_id = DB::table('client_invoice')->where('id_invoice', $request->idInvoice)
                ->first();
            $welcomed_user_id = DB::table('client_communication')
                ->where('fk_client', $client_id->fk_idClient)
                ->where('type_communcation', 'ترحيب')
                ->first();
            $existingTask = Task::where('invoice_id', $request->idInvoice)
                ->where('client_id', $client_id->fk_idClient)
                ->where('public_Type', 'com_install_1')
                ->first();

            if (!$existingTask) {
                $task = new task();
                $task->title = 'for communicate install 1';
                $task->description = 'you should to install 1';
                $task->invoice_id = $request->idInvoice;
                $task->id_communication  = $welcomed_user_id->id_communication;
                $task->client_id  = $client_id->fk_idClient;
                $task->public_Type = 'com_install_1';
                $task->assigend_department_from  = 3;
                $task->assigned_to  = $welcomed_user_id->fk_user;
                $task->save();

                !empty($task) ? $this->MyService->addTaskStatus($task) : null;
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

    public function addTaskApproveFinanceAfterApproveSales(Request $request)
    {
        try {
            DB::beginTransaction();

            $existingTask = Task::where('invoice_id', $request->idInvoice)
                ->where('client_id', $request->id_clients)
                ->where('public_Type', 'ApproveFinance')
                ->first();

            if (!$existingTask) {
                $task = new task();
                $task->title = 'ApproveFinance';
                $task->description = 'you should to approve';
                $task->invoice_id = $request->idInvoice;
                $task->client_id = $request->id_clients;
                $task->public_Type = 'ApproveFinance';
                $task->assigend_department_from  = 2;
                $task->assigend_department_to  = 5;
                $task->save();
                !empty($task) ? $this->MyService->addTaskStatus($task) : null;
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

    public function closeTaskApproveFinanceAfterApproveSales(Request $request)
    {
        try {
            DB::beginTransaction();
            $task = task::where('invoice_id', $request->idInvoice)
                ->where('public_Type', 'ApproveFinance')->first();
            $statuse_task_fraction = DB::table('statuse_task_fraction')
                ->where('task_id', $task->id)->first();
            if ($statuse_task_fraction->task_statuse_id == 1) {
                $task->actual_delivery_date = Carbon::now();
                $task->save();
                DB::table('statuse_task_fraction')
                    ->where('task_id', $task->id)
                    ->update([
                        'task_statuse_id' => 4,
                        'changed_date' => Carbon::now(),
                        'changed_by' => $request->iduser_FApprove
                    ]);
            } else {
                return;
            }

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function addTaskAddVisitDateAfterApproveInvoice(Request $request)
    {
        try {
            DB::beginTransaction();

            $existingTask = Task::where('invoice_id', $request->idInvoice)
                ->where('client_id', $request->id_clients)
                ->where('public_Type', 'AddVisitDate')
                ->first();

            if (!$existingTask) {
                $task = new task();
                $task->title = 'AddVisitDate';
                $task->description = 'you should to approve';
                $task->invoice_id = $request->idInvoice;
                $task->client_id = $request->id_clients;
                $task->public_Type = 'AddVisitDate';
                $task->assigend_department_from  = 2;
                $task->assigend_department_to  = 3;
                $task->save();
                !empty($task) ? $this->MyService->addTaskStatus($task) : null;
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

    public function closeTaskAddVisitDateAfterApproveInvoice(Request $request)
    {
        try {
            DB::beginTransaction();
            $task = task::where('invoice_id', $request->idInvoice)
                ->where('public_Type', 'AddVisitDate')->first();
            $statuse_task_fraction = DB::table('statuse_task_fraction')
                ->where('task_id', $task->id)->first();
            if ($statuse_task_fraction->task_statuse_id == 1) {
                $task->actual_delivery_date = Carbon::now();
                $task->save();
                DB::table('statuse_task_fraction')
                    ->where('task_id', $task->id)
                    ->update([
                        'task_statuse_id' => 4,
                        'changed_date' => Carbon::now(),
                        'changed_by' => $request->iduser_FApprove
                    ]);
            } else {
                return;
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function closeTaskafterCommunicateWithClient(Request $request)
    {
        try {
            DB::beginTransaction();
            $task = task::where('invoice_id', $request->idInvoice)
                ->where('id_communication', $request->id_communication)
                ->where('public_Type', 'com_install_2')
                ->first();
            $statuse_task_fraction = DB::table('statuse_task_fraction')
                ->where('task_id', $task->id)->first();
            if ($statuse_task_fraction->task_statuse_id == 1) {
                $task->actual_delivery_date = Carbon::now();
                $task->save();

                DB::table('statuse_task_fraction')
                    ->where('task_id', $task->id)
                    ->update([
                        'task_statuse_id' => 4,
                        'changed_date' => Carbon::now(),
                        'changed_by' => $request->iduser_updateed
                    ]);
            } else {
                return;
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function addTaskafterAddPaymentToTheInvoiceForReviewInvoice(Request $request) // 16
    {
        try {
            DB::beginTransaction();

            $existingTask = Task::where('invoice_id', $request->idInvoice)
                ->where('public_Type', 'AddPayment')
                ->first();

            if (!$existingTask) {
                $task = new task();
                $task->title = 'review invoice after AddPayment';
                $task->description = 'you should to review';
                $task->invoice_id = $request->idInvoice;
                $task->public_Type = 'AddPayment';
                $task->assigend_department_from  = 2;
                $task->assigend_department_to  = 5;
                $task->save();
                !empty($task) ? $this->MyService->addTaskStatus($task) : null;
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

    public function closeTaskafterAddPaymentToTheInvoiceForReviewInvoice(Request $request) // 16, not run
    {
        try {
            DB::beginTransaction();
            $task = task::where('invoice_id', $request->idInvoice)
                ->where('public_Type', 'AddPayment')
                ->first();
            $statuse_task_fraction = DB::table('statuse_task_fraction')
                ->where('task_id', $task->id)->first();
            if ($statuse_task_fraction->task_statuse_id == 1) {
                $task->actual_delivery_date = Carbon::now();
                $task->save();

                DB::table('statuse_task_fraction')
                    ->where('task_id', $task->id)
                    ->update([
                        'task_statuse_id' => 4,
                        'changed_date' => Carbon::now(),
                        'changed_by' => $request->iduser_updateed
                    ]);
            } else {
                return;
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function addTaskWhenThereIsNoUpdateToTheLatestClientUpdatesFor5Days(Request $request)
    {
        $params = [];
        // if ($request->has('fk_user')) {
        //     $val = $request->input('fk_user');
        //     $params[] = "u.fk_user = $val";
        // }

        // if ($request->has('fk_regoin')) {
        //     $val = $request->input('fk_regoin');
        //     $params[] = "r.id_regoin = $val";
        // }

        // if ($request->has('fk_country')) {
        //     $val = $request->input('fk_country');
        //     $params[] = "c.id_country = $val";
        // }

        // if ($request->has('ismarketing')) {
        //     $params[] = "u.ismarketing = 1";
        // }
        $params[] = "c.id_country = 1";
        $index = 0;
        $index1 = 0;
        $selectArray = [];
        DB::statement("SET sql_mode = ''");

        $sql = "
        SELECT
            clcomm.date_comment AS dateCommentClient,
            u.*, c.nameCountry, r.name_regoin, us.nameUser, r.fk_country
        FROM
            clients u
        LEFT JOIN
            regoin r ON r.id_regoin = u.fk_regoin
        LEFT JOIN
            country c ON c.id_country = r.fk_country
        INNER JOIN
            users us ON us.id_user = u.fk_user
        LEFT JOIN
            users uuserss ON uuserss.id_user = u.user_add
        LEFT JOIN
            client_comment clcomm ON clcomm.fk_client = u.id_clients
        WHERE
            " . implode(' AND ', $params) . "
            AND (clcomm.date_comment = (
                    SELECT
                        MAX(date_comment)
                    FROM
                        client_comment cl
                    WHERE
                        cl.fk_client = u.id_clients
                ) OR clcomm.date_comment IS NULL)
            AND u.type_client = 'تفاوض'
            AND u.date_create >= '2023-11-01'
            AND (
                (u.ismarketing = 1 AND DATEDIFF(clcomm.date_comment, u.date_create) > 5)
                OR
                (u.ismarketing != 1 AND DATEDIFF(clcomm.date_comment, u.date_create) > 3)
            )
        GROUP BY
            u.id_clients
        ORDER BY
            dateCommentClient ASC";
        try {
            $result = DB::select($sql);
            $arrJson = [];
            $arrJsonProduct = [];

            if (count($result) > 0) {
                foreach ($result as $row) {
                    $clientArray = [];
                    $clientArray[$index]['id_clients'] = $row->id_clients;
                    $clientArray[$index]['name_client'] = $row->name_client;
                    $clientArray[$index]['name_enterprise'] = $row->name_enterprise;
                    $clientArray[$index]['type_job'] = $row->type_job;
                    $clientArray[$index]['fk_regoin'] = $row->fk_regoin;
                    $clientArray[$index]['date_create'] = $row->date_create;
                    $clientArray[$index]['type_client'] = $row->type_client;
                    $clientArray[$index]['fk_user'] = $row->fk_user;
                    $clientArray[$index]['name_regoin'] = $row->name_regoin;
                    $clientArray[$index]['nameUser'] = $row->nameUser;
                    $arrJson[$index1]["client_obj"] = $clientArray;
                    $arrJson[$index1]["dateCommentClient"] = $row->dateCommentClient;

                    $date1 = now()->timezone('Asia/Riyadh')->format('Y-m-d H:i:s');
                    $date2 = $row->dateCommentClient;

                    if ($date2 != null) {
                        $timestamp1 = date('Y-m-d', strtotime($date1));
                        $timestamp2 = date('Y-m-d', strtotime($date2));
                        $difference = strtotime($timestamp2) - strtotime($timestamp1);

                        $days = floor($difference / (24 * 60 * 60));
                        $days = abs($days);
                        $hour = $days;
                    } else {
                        $hour = -1;
                    }

                    $arrJson[$index1]['hoursLastComment'] = $hour . '';
                    $index1++;
                    $index = 0;
                }
            }

            $resJson = [
                "result" => "success",
                "code" => 200,
                "message" => $arrJson
            ];
            // return count($result);
            $idClients = $resJson['message'][0]['client_obj'][0]['id_clients'];
            $client = client_comment::where('fk_client', $idClients)->update(['name_enterprise' => 'hi']);
            $user = DB::table('user_token')->where('fkuser',330)
                ->first();


            Notification::send(
                    null,
                    new SendNotification(
                        'Hi anas',
                        'dsad',
                        'you should to ...',
                        [$user->token]
                    )
                );

            return response()->json($resJson);
        } catch (\Throwable $e) {
            $resJson = [
                "result" => "error",
                "code" => 400,
                "message" => $e->getMessage()
            ];

            return response()->json($resJson);
        }
    }
}
