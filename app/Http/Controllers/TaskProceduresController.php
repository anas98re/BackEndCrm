<?php

namespace App\Http\Controllers;

use App\Models\taskStatus;
use App\Http\Requests\StoretaskStatusRequest;
use App\Http\Requests\UpdatetaskStatusRequest;
use App\Models\client_comment;
use App\Models\client_invoice;
use App\Models\clients;
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

    public function addTaskToApproveAdminAfterAddInvoice(Request $request)
    {
        try {
            DB::beginTransaction();

            $assigned_to = users::where('fk_regoin', $request->fk_regoin)
                ->where('type_level', 14)->first();
            $existingTask = Task::where('invoice_id', $request->invoice_id)
                ->where('public_Type', 'approveAdmin')
                ->first();

            $invoice = client_invoice::where('id_invoice', $request->invoice_id)->first();
            $client = clients::where('id_clients', $invoice->fk_idClient)->first();
            $message = 'يوجد فاتورة للعميل ( ? ) بانتظار الموافقة';
            $messageDescription = str_replace('?', $client->name_enterprise, $message);

            if (!$existingTask) {
                $task = new task();
                $task->title = 'موافقة المشرف';
                $task->description = $messageDescription;
                $task->invoice_id = $request->invoice_id;
                $task->public_Type = 'approveAdmin';
                $task->main_type_task = 'ProccessAuto';
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

            $invoice = client_invoice::where('id_invoice', $request->idInvoice)->first();
            $client = clients::where('id_clients', $invoice->fk_idClient)->first();
            $message = 'مراجعة فاتورة العميل ( ? ) بعد ان تم إضافة دفعة جديدة لها';
            $messageDescription = str_replace('?', $client->name_enterprise, $message);
            if (!$existingTask) {
                $task = new task();
                $task->title = 'مراجعة فاتورة';
                $task->description = $messageDescription;
                $task->invoice_id = $request->idInvoice;
                $task->public_Type = 'AddPayment';
                $task->main_type_task = 'ProccessAuto';
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
        $index = 0;
        $index1 = 0;
        $Date = Carbon::now()->subMonthsNoOverflow(1)->startOfMonth()->toDateString();

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
                    $elementOfRegions[] = $element;
                    $countRegions[] = $count;
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
                        ->first();
                    if ($IsUser14->id_regoin == 14) {


                        if ($userToken) {
                            $message = implode("\n", $RegionNamesAndDuplicates);
                            Notification::send(
                                null,
                                new SendNotification(
                                    'تعليقات العملاء',
                                    'cls',
                                    $message,
                                    [$userToken->token]
                                )
                            );

                            notifiaction::create([
                                'message' => $message,
                                'type_notify' => 'checkComment',
                                'to_user' => $key,
                                'isread' => 0,
                                'data' => 'cls',
                                'from_user' => 330,
                                'dateNotify' => Carbon::now('Asia/Riyadh')
                            ]);
                        }
                    } else {
                        foreach ($duplicates as $d => $dValue) {
                            if ($IsUser14->id_regoin == $d) {
                                $theRepeate = $dValue;
                            }
                        }
                        $message1 = ' ،هناك ? عميل في ! لم يُعلّق لهم ';
                        $messageWithCount1 = str_replace('?', $theRepeate, $message1);
                        $messageWithRegion1 = str_replace('!', $IsUser14->name_regoin, $messageWithCount1);
                        $messageWithDate1 = $messageWithRegion1 . ' منذ تاريخ % لتاريخ اليوم';
                        $messageRegionWithPlaceholder1 = str_replace('%', $Date, $messageWithDate1);
                        if ($userToken) {
                            Notification::send(
                                null,
                                new SendNotification(
                                    'تعليقات العملاء',
                                    'cls',
                                    $messageRegionWithPlaceholder1,
                                    [$userToken->token]
                                )
                            );

                            notifiaction::create([
                                'message' => $messageRegionWithPlaceholder1,
                                'type_notify' => 'checkComment',
                                'to_user' => $IsUser14->id_user,
                                'isread' => 0,
                                'data' => 'cls',
                                'from_user' => 330,
                                'dateNotify' => Carbon::now('Asia/Riyadh')
                            ]);
                        }
                    }
                }

                // Sending notifications to Branch supervisors
                $BranchSupervisors = users::where('type_level', 14)
                    ->whereIn('fk_regoin', $elementOfRegions)
                    ->join('regoin', 'users.fk_regoin', '=', 'regoin.id_regoin')
                    ->select('users.id_user', 'regoin.name_regoin', 'regoin.id_regoin')
                    ->get();
                foreach ($BranchSupervisors as $key => $value) {
                    $userToken = DB::table('user_token')->where('fkuser', $value->id_user)
                        ->where('token', '!=', null)
                        ->first();
                    foreach ($duplicates as $d => $dValue) {
                        if ($value->id_regoin == $d) {
                            $theRepeate = $dValue;
                        }
                    }
                    $message2 = ' ،لديك ? عميل في ! لم يُعلّق لهم ';

                    $messageWithCount2 = str_replace('?', $theRepeate, $message2);
                    $messageWithRegion2 = str_replace('!', $value->name_regoin, $messageWithCount2);
                    $messageWithDate2 = $messageWithRegion2 . ' منذ تاريخ % لتاريخ اليوم';
                    $messageRegionWithPlaceholder2 = str_replace('%', $Date, $messageWithDate2);
                    if ($userToken) {
                        Notification::send(
                            null,
                            new SendNotification(
                                'تعليقات العملاء',
                                'cls',
                                $messageRegionWithPlaceholder2,
                                [$userToken->token]
                            )
                        );

                        notifiaction::create([
                            'message' => $messageRegionWithPlaceholder2,
                            'type_notify' => 'checkComment',
                            'to_user' => $value->id_user,
                            'isread' => 0,
                            'data' => 'cls',
                            'from_user' => 330,
                            'dateNotify' => Carbon::now('Asia/Riyadh')
                        ]);
                    }
                }

                // Sending notifications to employees responsible for clients
                foreach ($array_count_values_ID_USERS_For_Clients as $key => $value) {
                    $userToken = DB::table('user_token')->where('fkuser', $key)
                        ->where('token', '!=', null)
                        ->first();

                    $message3 = ' ،لديك ? عملاء لم يُعلّق لهم ';
                    $messageWithPlaceholder3 = str_replace('?', $value, $message3);
                    $messageWithDate3 = $messageWithPlaceholder3 . ' منذ تاريخ % لتاريخ اليوم';
                    $messageRegionWithPlaceholder3 = str_replace('%', $Date, $messageWithDate3);
                    if ($userToken) {
                        Notification::send(
                            null,
                            new SendNotification(
                                'تعليقات العملاء',
                                'cls',
                                $messageRegionWithPlaceholder3,
                                [$userToken->token]
                            )
                        );

                        notifiaction::create([
                            'message' => $messageRegionWithPlaceholder3,
                            'type_notify' => 'checkComment',
                            'to_user' => $key,
                            'isread' => 0,
                            'data' => 'cls',
                            'from_user' => 330,
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
// [
//     [
//         {
//             "id_user": 5,

//             "maincity_fk": null
//         },
//         {
//             "id_user": 14,

//             "maincity_fk": null
//         },
//         {
//             "id_user": 141,

//             "maincity_fk": null
//         },
//     ]
// ]

// {
//     "5": 5,
//     "14": 5,
//     "141": 5,
//     "273": 5,
//     "294": 5,
//     "320": 1,
//     "322": 1,
//     "323": 1,
//     "325": 1,
//     "326": 1,
//     "328": 1,
//     "329": 1,
//     "331": 1
// }

// {
//     "59": 2,
//     "271": 5,
//     "8": 1,
//     "55": 3,
//     "163": 10,
//     "278": 3,
//     "160": 2,
//     "208": 18,
//     "43": 7,
//     "10": 3,
//     "204": 1
// }
