<?php

namespace App\Console\Commands;

use App\Models\client_comment;
use App\Models\notifiaction;
use App\Models\users;
use App\Notifications\SendNotification;
use App\Services\TaskManangement\queriesService;
use App\Services\TaskManangement\TaskProceduresService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class checkClientComments extends Command
{
    private $MyService;
    private $MyQueriesService;

    public function __construct(TaskProceduresService $MyService, queriesService $MyQueriesService)
    {
        $this->MyService = $MyService;
        $this->MyQueriesService = $MyQueriesService;
        parent::__construct();
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-client-comments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $index = 0;
        $index1 = 0;
        $Date = Carbon::now('Asia/Riyadh')->subMonthsNoOverflow(1)->startOfMonth()->toDateString();

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
                        $message1 = ' هناك ? عميل في ! لم يُعلّق لهم';
                        $messageWithCount1 = str_replace('?', $theRepeate, $message1);
                        $messageWithRegion1 = str_replace('!', $IsUser14->name_regoin, $messageWithCount1);
                        $messageWithDate1 = $messageWithRegion1 . ' [منذ تاريخ % لتاريخ اليوم]';
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
                    $message2 = ' لديك ? عميل في ! لم يُعلّق لهم ';

                    $messageWithCount2 = str_replace('?', $theRepeate, $message2);
                    $messageWithRegion2 = str_replace('!', $value->name_regoin, $messageWithCount2);
                    $messageWithDate2 = $messageWithRegion2 . ' [منذ تاريخ % لتاريخ اليوم]';
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

                    $message3 = ' لديك ? عملاء لم يُعلّق لهم ';
                    $messageWithPlaceholder3 = str_replace('?', $value, $message3);
                    $messageWithDate3 = $messageWithPlaceholder3 . ' [منذ تاريخ % لتاريخ اليوم]';
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
