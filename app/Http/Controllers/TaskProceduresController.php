<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\taskStatus;
use App\Http\Requests\StoretaskStatusRequest;
use App\Http\Requests\UpdatetaskStatusRequest;
use App\Models\client_comment;
use App\Models\client_invoice;
use App\Models\clients;
use App\Models\config_table;
use App\Models\notifiaction;
use App\Models\regoin;
use App\Models\statuse_task_fraction;
use App\Models\task;
use App\Models\users;
use App\Notifications\SendNotification;
use App\Services\TaskManangement\queriesService;
use App\Services\TaskManangement\TaskProceduresService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class TaskProceduresController extends Controller
{
    private $MyService;
    private $MyQueriesService;

    public function __construct(TaskProceduresService $MyService, queriesService $MyQueriesService)
    {
        $this->MyService = $MyService;
        $this->MyQueriesService = $MyQueriesService;
    }

    public function addTaskToApproveAdminAfterAddInvoiceTEST(Request $request)
    {

        try {
            DB::beginTransaction();
            info('request all 1: ' . json_encode($request->all()));
            info('fk_regoin: ' . json_encode($request->fk_regoin));
            info('Constants::ALL_BRUNSHES: ' . json_encode(Constants::ALL_BRUNSHES));
            $assigneds_to = users::where('fk_regoin', $request->fk_regoin)
                ->where('type_level', 14)->get();

            if ($assigneds_to->isEmpty()) {
                // Handle the case where $assigneds_to is empty (no users found)
                // For example, you can log a message or return early
                Log::warning('No users found for the specified region and type level.');
                return;
            } else {
            }
            $invoice = client_invoice::where('id_invoice', $request->invoice_id)->first();
            $client = clients::where('id_clients', $invoice->fk_idClient)->first();
            $message = 'يوجد فاتورة للعميل ( ? ) بانتظار الموافقة';
            $messageDescription = str_replace('?', $client->name_enterprise, $message);

            foreach ($assigneds_to as $assigned_to) {
                $existingTask = Task::where('invoice_id', $request->invoice_id)
                    ->where('public_Type', 'approveAdmin')
                    ->where('assigned_to', $assigned_to->id_user)
                    ->first();
                if (!$existingTask) {
                    $task = new task();
                    $task->title = 'موافقة المشرف';
                    $task->description = $messageDescription;
                    $task->invoice_id = $request->invoice_id;
                    $task->public_Type = 'approveAdmin';
                    $task->main_type_task = 'ProccessAuto';
                    $task->assigend_department_from  = 2;
                    $task->assigned_to = $assigned_to ? $assigned_to->id_user : null;
                    $task->assigend_department_to = $assigned_to ? null : 2;
                    $task->start_date = Carbon::now('Asia/Riyadh');
                    $task->save();

                    !empty($task) ? $this->MyService->addTaskStatus($task) : null;

                    $this->MyService->handleNotificationForTaskProcedures(
                        $message = $task->title,
                        $type = 'task',
                        $to_user = $assigned_to->id_user,
                        $invoice_id = $request->invoice_id,
                        $client_id = $client->id_clients
                    );
                } else {
                    $task = null;
                }
            }
            DB::commit();
            return $task;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function addTaskToApproveAdminAfterAddInvoice(Request $request)
    {
        try {
            DB::beginTransaction();

            // Retrieve assigned users based on region and type level
            $assigneds_to = users::where('fk_regoin', $request->fk_regoin)
                ->where('type_level', 14)->get();

            $invoice = client_invoice::where('id_invoice', $request->invoice_id)->first();
            $client = clients::where('id_clients', $invoice->fk_idClient)->first();
            $message = 'يوجد فاتورة للعميل ( ? ) بانتظار الموافقة';
            $messageDescription = str_replace('?', $client->name_enterprise, $message);
            // Check if $assigneds_to is empty
            if ($assigneds_to->isEmpty()) {
                // Log a warning message if no users are found and execute the loop once
                Log::warning('No users found for the specified region and type level.');
                $usersIdsManamgerSuprevisor = users::where('type_level', 9)
                    ->Where('type_administration', 2)
                    ->get();

                foreach ($usersIdsManamgerSuprevisor as $user) {
                    $existingTask = Task::where('invoice_id', $request->invoice_id)
                        ->where('public_Type', 'approveAdmin')
                        ->where('assigned_to', $user->id_user)
                        ->first();
                    if (!$existingTask) {
                        $task = new task();
                        $task->title = 'موافقة المشرف';
                        $task->description = $messageDescription;
                        $task->invoice_id = $request->invoice_id;
                        $task->public_Type = 'approveAdmin';
                        $task->main_type_task = 'ProccessAuto';
                        $task->assigend_department_from  = 2;
                        $task->assigend_department_to = 2;
                        $task->start_date = Carbon::now('Asia/Riyadh');
                        $task->save();

                        // Add task status and handle notifications
                        $this->MyService->addTaskStatus($task);
                        $this->MyService->handleNotificationForTaskProcedures(
                            $message = $task->title,
                            $type = 'task',
                            $to_user = $user->id_user,
                            $invoice_id = $request->invoice_id,
                            $client_id = $client->id_clients
                        );
                    }
                }
                DB::commit();
                return true;
            } else {
                foreach ($assigneds_to as $assigned_to) {
                    // Check if task already exists for the invoice and assigned user
                    $existingTask = Task::where('invoice_id', $request->invoice_id)
                        ->where('public_Type', 'approveAdmin')
                        ->where('assigned_to', $assigned_to->id_user)
                        ->first();

                    // If task doesn't exist, create a new task
                    if (!$existingTask) {

                        $task = new task();
                        $task->title = 'موافقة المشرف';
                        $task->description = $messageDescription;
                        $task->invoice_id = $request->invoice_id;
                        $task->public_Type = 'approveAdmin';
                        $task->main_type_task = 'ProccessAuto';
                        $task->assigend_department_from  = 2;
                        $task->assigned_to = $assigned_to->id_user;
                        $task->start_date = Carbon::now('Asia/Riyadh');
                        $task->save();

                        // Add task status and handle notifications
                        $this->MyService->addTaskStatus($task);
                        $this->MyService->handleNotificationForTaskProcedures(
                            $message = $task->title,
                            $type = 'task',
                            $to_user = $assigned_to->id_user,
                            $invoice_id = $request->invoice_id,
                            $client_id = $client->id_clients
                        );
                    }
                }
            }

            // Commit the transaction
            DB::commit();

            // Return the last created task
            return $task ?? null;
        } catch (\Throwable $th) {
            // Rollback the transaction and re-throw the exception
            DB::rollBack();
            throw $th;
        }
    }

    public function closeTaskApproveAdminAfterAddInvoice(Request $request)
    {
        try {
            DB::beginTransaction();
            info('request all 2: ' . json_encode($request->all()));
            // $client_communication = DB::table('client_communication')->insertGetId([
            //     'fk_client' => $request->id_clients,
            //     'type_communcation' => 'ترحيب',
            //     'id_invoice' => $request->idInvoice
            // ]);
            $task = task::where('invoice_id', $request->idInvoice)
                ->where('public_Type', 'approveAdmin')
                ->first();
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
                        'changed_by' => $request->iduser_approve
                    ]);
                $this->MyService->addTaskAfterApproveInvoice(
                    $request->idInvoice,
                    $request->id_clients,
                    $request->lastCommunicatinId
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
                $task->actual_delivery_date = Carbon::now('Asia/Riyadh');
                $task->save();
                DB::table('statuse_task_fraction')
                    ->where('task_id', $task->id)
                    ->update([
                        'task_statuse_id' => 4,
                        'changed_date' => Carbon::now('Asia/Riyadh'),
                        'changed_by' => $request->iduser_updateed
                    ]);
                // $this->MyService->afterCommunicateWithClient(
                //     $request->idInvoice,
                //     $request->id_communication,
                //     $request->iduser_updateed
                // );
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
                ->where('type_communcation', 'تركيب')
                ->where('type_install', 1)
                ->where('id_invoice', $request->idInvoice)
                ->first();

            $existingTask = Task::where('invoice_id', $request->idInvoice)
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
                $task->invoice_id = $request->idInvoice;
                $task->id_communication  = $welcomed_user_id->id_communication;
                $task->client_id  = $client_id->fk_idClient;
                $task->public_Type = 'com_install_1';
                $task->main_type_task = 'ProccessAuto';
                $task->assigend_department_from  = 3;
                $task->assigned_to  = ($welcomed_user_id != null ? $welcomed_user_id->fk_user : null);
                $task->start_date  = $newDatetime;
                $task->save();

                !empty($task) ? $this->MyService->addTaskStatus($task) : null;
                $this->MyService->handleNotificationForTaskProcedures(
                    $message = $task->title,
                    $type = 'task',
                    $to_user = $welcomed_user_id->fk_user,
                    $invoice_id = $request->idInvoice,
                    $client_id = $client_id->fk_idClient
                );
            } else {
                $task = null;
            }

            DB::commit();
            return $task;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function closeTaskAfterInstallClient(Request $request)
    {
        info('$id_communication in controller is: ' . json_encode($request->last_idCommuncation2));
        try {
            DB::beginTransaction();

            $task = task::where('id_communication', $request->id_communication)
                ->where('public_Type', 'com_install_1')
                ->first();
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
                        'changed_by' => $request->iduser_updateed
                    ]);

                $this->MyService->afterCommunicateWithClient(
                    $request->idInvoice,
                    $task->client_id,
                    $request->last_idCommuncation2,
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

    public function addTaskApproveFinanceAfterApproveSales(Request $request)
    {
        try {
            DB::beginTransaction();

            $existingTask = Task::where('invoice_id', $request->idInvoice)
                ->where('client_id', $request->id_clients)
                ->where('public_Type', 'ApproveFinance')
                ->first();

            $invoice = client_invoice::where('id_invoice', $request->idInvoice)->first();
            $client = clients::where('id_clients', $invoice->fk_idClient)->first();
            $message = 'يوجد فاتورة للعميل ( ? ) بانتظار الموافقة';
            $messageDescription = str_replace('?', $client->name_enterprise, $message);

            if (!$existingTask) {
                $task = new task();
                $task->title = 'موافقة المالية';
                $task->description = $messageDescription;
                $task->invoice_id = $request->idInvoice;
                $task->client_id = $request->id_clients;
                $task->public_Type = 'ApproveFinance';
                $task->main_type_task = 'ProccessAuto';
                $task->assigend_department_from  = 2;
                $task->assigend_department_to  = 5;
                $task->start_date = Carbon::now('Asia/Riyadh');
                $task->save();

                !empty($task) ? $this->MyService->addTaskStatus($task) : null;

                $users = $this->MyQueriesService->departmentSupervisorsToTheRequiredLevelForTaskProcedures(5);
                foreach ($users as $userID) {
                    $this->MyService->handleNotificationForTaskProcedures(
                        $message = $task->title,
                        $type = 'task',
                        $to_user = $userID,
                        $invoice_id = $request->idInvoice,
                        $client_id = $request->id_clients
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

    public function closeTaskApproveFinanceAfterApproveSales(Request $request)
    {
        try {
            DB::beginTransaction();
            $task = task::where('invoice_id', $request->idInvoice)
                ->where('public_Type', 'ApproveFinance')->first();
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

            $invoice = client_invoice::where('id_invoice', $request->idInvoice)->first();
            $client = clients::where('id_clients', $invoice->fk_idClient)->first();
            $message = 'جدولة زيارة للعميل ( ? )';
            $messageDescription = str_replace('?', $client->name_enterprise, $message);
            if (!$existingTask) {
                $task = new task();
                $task->title = 'موعد لزيارة العميل';
                $task->description = $messageDescription;
                $task->invoice_id = $request->idInvoice;
                $task->client_id = $request->id_clients;
                $task->public_Type = 'AddVisitDate';
                $task->main_type_task = 'ProccessAuto';
                $task->assigend_department_from  = 2;
                $task->assigend_department_to  = 3;
                $task->start_date = Carbon::now('Asia/Riyadh');
                $task->save();

                !empty($task) ? $this->MyService->addTaskStatus($task) : null;

                $users = $this->MyQueriesService->departmentSupervisorsToTheRequiredLevelForTaskProcedures(3);
                foreach ($users as $userID) {
                    $this->MyService->handleNotificationForTaskProcedures(
                        $message = $task->title,
                        $type = 'task',
                        $to_user = $userID,
                        $invoice_id = $request->idInvoice,
                        $client_id = $request->id_clients
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

    public function closeTaskAddVisitDateAfterApproveInvoice(Request $request)
    {
        try {
            DB::beginTransaction();
            $task = task::where('invoice_id', $request->idInvoice)
                ->where('public_Type', 'AddVisitDate')->first();
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
                $task->actual_delivery_date = Carbon::now('Asia/Riyadh');
                $task->save();

                DB::table('statuse_task_fraction')
                    ->where('task_id', $task->id)
                    ->update([
                        'task_statuse_id' => 4,
                        'changed_date' => Carbon::now('Asia/Riyadh'),
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

            $existingTask = task::where('invoice_id', $request->idInvoice)
                ->where('public_Type', 'AddPayment')
                ->first();

            $invoice = client_invoice::where('id_invoice', $request->idInvoice)->first();
            $client = clients::where('id_clients', $invoice->fk_idClient)->first();
            $message = 'مراجعة فاتورة العميل ( ? ) بعد ان تم إضافة دفعة جديدة لها';
            $messageDescription = str_replace('?', $client->name_enterprise, $message);
            if (!$existingTask) {
                $task = new task();
                $task->title = 'مراجعة فاتورة';
                $task->description = $messageDescription;
                $task->invoice_id = $request->idInvoice;
                $task->client_id = $client->id_clients;
                $task->public_Type = 'AddPayment';
                $task->main_type_task = 'ProccessAuto';
                $task->assigend_department_from  = 2;
                $task->assigend_department_to  = 5;
                $task->start_date = Carbon::now('Asia/Riyadh');
                $task->save();

                !empty($task) ? $this->MyService->addTaskStatus($task) : null;

                $users = $this->MyQueriesService->departmentSupervisorsToTheRequiredLevelForTaskProcedures(3);
                foreach ($users as $userID) {
                    $this->MyService->handleNotificationForTaskProcedures(
                        $message = $task->title,
                        $type = 'task',
                        $to_user = $userID,
                        $invoice_id = $request->idInvoice,
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
                $task->actual_delivery_date = Carbon::now('Asia/Riyadh');
                $task->save();

                DB::table('statuse_task_fraction')
                    ->where('task_id', $task->id)
                    ->update([
                        'task_statuse_id' => 4,
                        'changed_date' => Carbon::now('Asia/Riyadh'),
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

    public function addTaskWhenThereIsNoUpdateToTheLatestClientUpdatesFor5Days()
    {
        $index = 0;
        $index1 = 0;
        $Date = Carbon::now('Asia/Riyadh')->subMonthsNoOverflow(1)->startOfMonth()->toDateString();
        // $Date = Carbon::now('Asia/Riyadh')->startOfMonth()->toDateString();

        $query = $this->MyQueriesService->getClientsThatIsNoUpdateToTheLatestClientUpdatesFor5Days();

        try {
            $result = $query->get();
            $idUsersForClients = [];
            $id_regoinsForClients = [];

            foreach ($result as $userID) {
                $idUsersForClients[] = $userID->fk_user; // Extract the id_user value and add it to the array
            }
            foreach ($result as $regionId) {
                $id_regoinsForClients[] = $regionId->fk_regoin; // Extract the fk_regoin value and add it to the array
            }


            $arrJson = [];
            $arrJsonProduct = [];

            $usersAll = [];
            $clientArray1 = [];
            if (count($result) > 0) {
                foreach ($result as $row) {
                    $clientArray = [];
                    $clientArray[$index]['id_clients'] = $row->id_clients;
                    $clientArray[$index]['name_client'] = $row->name_client;
                    $clientArray[$index]['name_enterprise'] = $row->name_enterprise;
                    $clientArray[$index]['type_job'] = $row->type_job;
                    $clientArray[$index]['fk_regoin'] = $row->fk_regoin;
                    $clientArray1[] = $row->id_clients;
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

                    // DB::table('clients as u')
                    //     ->where('u.id_clients', $row->id_clients)
                    //     ->update([
                    //         'is_comments_check' => 1
                    //     ]);
                }

                $duplicates = array_count_values($id_regoinsForClients);
                $elementOfRegions = [];
                $countRegions = [];
                foreach ($duplicates as $element => $count) {
                    if ($element != Constants::ALL_BRUNSHES) {
                        $elementOfRegions[] = $element;
                        $countRegions[] = $count;
                    }
                }

                $privgLevelUsers = DB::table('privg_level_user')
                    ->where('fk_privileg', 175)
                    ->where('is_check', 1)
                    ->get();
                $typeLevel = [];
                foreach ($privgLevelUsers as $level) {
                    $typeLevel[] = $level->fk_level;
                }

                $BranchSupervisorsToTheRequiredLevel =
                    $this->MyQueriesService->BranchSupervisorsToTheRequiredLevel($elementOfRegions, $typeLevel);


                $xIDs = [];
                foreach ($BranchSupervisorsToTheRequiredLevel as $el) {
                    $xIDs[] = $el->id_user;
                }

                $array_count_values_USERS = array_count_values($xIDs);
                $array_count_values_ID_USERS_For_Clients = array_count_values($idUsersForClients);

                // Sending notifications to responsible for all (regions)brunches
                $RegionNamesAndDuplicates = $this->MyQueriesService->getRegionNamesAndDuplicates($duplicates);
                foreach ($array_count_values_USERS as $key => $value) {
                    $IsUser14 = users::where('id_user', $key)
                        ->join('regoin', 'users.fk_regoin', '=', 'regoin.id_regoin')
                        ->select('users.id_user', 'regoin.name_regoin', 'regoin.id_regoin')
                        ->first();
                    $userToken = DB::table('user_token')->where('fkuser', $key)
                        ->where('token', '!=', null)
                        ->latest('date_create')
                        ->first();
                    if ($IsUser14->id_regoin == Constants::ALL_BRUNSHES) {


                        if ($userToken) {
                            $message = implode("\n", $RegionNamesAndDuplicates);
                            Notification::send(
                                null,
                                new SendNotification(
                                    'تعليقات العملاء',
                                    $message,
                                    $message,
                                    ($userToken != null ? $userToken->token : null)
                                )
                            );

                            notifiaction::create([
                                'message' => $message,
                                'type_notify' => 'checkComment',
                                'to_user' => $key,
                                'isread' => 0,
                                'data' => 'cls',
                                'from_user' => 1,
                                'dateNotify' => Carbon::now('Asia/Riyadh')
                            ]);
                        }
                    } else {
                        foreach ($duplicates as $d => $dValue) {
                            if ($IsUser14->id_regoin == $d) {
                                $theRepeate = $dValue;
                            }
                        }
                        $message1 = ' هناك ? عميل في ! لم يُعلّق لهم';
                        $messageWithCount1 = str_replace('?', $theRepeate, $message1);
                        $messageWithRegion1 = str_replace('!', $IsUser14->name_regoin, $messageWithCount1);
                        // $messageWithDate1 = $messageWithRegion1 . ' [تم الاحصاء منذ تاريخ % لتاريخ اليوم]';
                        // $messageWithDate1 = $messageWithRegion1;
                        // $messageRegionWithPlaceholder1 = str_replace('%', $Date, $messageWithDate1);
                        if ($userToken) {
                            Notification::send(
                                null,
                                new SendNotification(
                                    'تعليقات العملاء',
                                    $messageWithRegion1,
                                    $messageWithRegion1,
                                    ($userToken != null ? $userToken->token : null)
                                )
                            );

                            notifiaction::create([
                                'message' => $messageWithRegion1,
                                'type_notify' => 'checkComment',
                                'to_user' => $IsUser14->id_user,
                                'isread' => 0,
                                'data' => 'cls',
                                'from_user' => 1,
                                'dateNotify' => Carbon::now('Asia/Riyadh')
                            ]);
                        }
                    }
                }

                // Sending notifications to Branch supervisors
                $BranchSupervisors = users::where('type_level', Constants::ALL_BRUNSHES)
                    ->whereIn('fk_regoin', $elementOfRegions)
                    ->join('regoin', 'users.fk_regoin', '=', 'regoin.id_regoin')
                    ->select('users.id_user', 'regoin.name_regoin', 'regoin.id_regoin')
                    ->get();
                foreach ($BranchSupervisors as $key => $value) {
                    $userToken = DB::table('user_token')->where('fkuser', $value->id_user)
                        ->where('token', '!=', null)
                        ->latest('date_create')
                        ->first();
                    foreach ($duplicates as $d => $dValue) {
                        if ($value->id_regoin == $d) {
                            $theRepeate = $dValue;
                        }
                    }
                    $message2 = ' لديك ? عميل في ! لم يُعلّق لهم ';

                    $messageWithCount2 = str_replace('?', $theRepeate, $message2);
                    $messageWithRegion2 = str_replace('!', $value->name_regoin, $messageWithCount2);
                    // $messageWithDate2 = $messageWithRegion2 . ' [تم الاحصاء منذ تاريخ % لتاريخ اليوم]';
                    // $messageWithDate2 = $messageWithRegion2;
                    // $messageRegionWithPlaceholder2 = str_replace('%', $Date, $messageWithDate2);
                    if ($userToken) {
                        Notification::send(
                            null,
                            new SendNotification(
                                'تعليقات العملاء',
                                $messageWithRegion2,
                                $messageWithRegion2,
                                ($userToken != null ? $userToken->token : null)
                            )
                        );

                        notifiaction::create([
                            'message' => $messageWithRegion2,
                            'type_notify' => 'checkComment',
                            'to_user' => $value->id_user,
                            'isread' => 0,
                            'data' => 'cls',
                            'from_user' => 1,
                            'dateNotify' => Carbon::now('Asia/Riyadh')
                        ]);
                    }
                }

                // Sending notifications to employees responsible for clients
                foreach ($array_count_values_ID_USERS_For_Clients as $key => $value) {
                    $userToken = DB::table('user_token')->where('fkuser', $key)
                        ->where('token', '!=', null)
                        ->latest('date_create')
                        ->first();

                    $message3 = ' لديك ? عملاء لم يُعلّق لهم ';
                    $messageWithPlaceholder3 = str_replace('?', $value, $message3);
                    // $messageWithDate3 = $messageWithPlaceholder3 . ' [ تم الاحصاء منذ تاريخ % لتاريخ اليوم]';
                    // $messageWithDate3 = $messageWithPlaceholder3;
                    // $messageRegionWithPlaceholder3 = str_replace('%', $Date, $messageWithDate3);
                    if ($userToken) {
                        Notification::send(
                            null,
                            new SendNotification(
                                'تعليقات العملاء',
                                $messageWithPlaceholder3,
                                $messageWithPlaceholder3,
                                ($userToken != null ? $userToken->token : null)
                            )
                        );

                        notifiaction::create([
                            'message' => $messageWithPlaceholder3,
                            'type_notify' => 'checkComment',
                            'to_user' => $key,
                            'isread' => 0,
                            'data' => 'cls',
                            'from_user' => 1,
                            'dateNotify' => Carbon::now('Asia/Riyadh')
                        ]);
                    }
                    $this->MyService->addTaskToEmployeesResponsibleForClients(
                        $key,
                        $value,
                        $Date
                    );
                }
                //-----------------------------------------------------------

            }
            $resJson = [
                "result" => "success",
                "code" => 200,
                "message" => $arrJson
            ];


            // return count($result);
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
