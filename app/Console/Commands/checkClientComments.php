<?php

namespace App\Console\Commands;

use App\Models\client_comment;
use App\Models\notifiaction;
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

                    DB::table('clients as u')
                        ->where('u.id_clients', $row->id_clients)
                        ->update([
                            'is_comments_check' => 1
                        ]);
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


                // Sending notifications to employees responsible for clients
                foreach ($array_count_values_ID_USERS_For_Clients as $key => $value) {
                    $userToken = DB::table('user_token')->where('fkuser', $key)
                        ->where('token', '!=', null)
                        ->first();

                    $message = 'لديك ? عملاء لم يُعلّق لهم منذ خمس أيام';
                    $messageWithPlaceholder = str_replace('?', $value, $message);
                    if ($userToken) {
                        Notification::send(
                            null,
                            new SendNotification(
                                'Hi anas',
                                'dsad',
                                $messageWithPlaceholder,
                                [$userToken->token]
                            )
                        );

                        notifiaction::create([
                            'message' => $messageWithPlaceholder,
                            'type_notify' => 'checkComment',
                            'to_user' => $key,
                            'isread' => 0,
                            'from_user' => 330,
                            'dateNotify' => Carbon::now()
                        ]);
                    }
                }
                //-----------------------------------------------------------
                // Sending notifications to Branch Supervisor responsible for clients
                foreach ($array_count_values_USERS as $key => $value) {
                    $userToken = DB::table('user_token')->where('fkuser', $key)
                        ->where('token', '!=', null)
                        ->first();

                    $message = 'لديك ? عملاء في فرعك لم يُعلّق لهم منذ خمس أيام';
                    $messageWithPlaceholder = str_replace('?', $value, $message);
                    if ($userToken) {
                        Notification::send(
                            null,
                            new SendNotification(
                                'Hi anas',
                                'dsad',
                                $messageWithPlaceholder,
                                [$userToken->token]
                            )
                        );

                        notifiaction::create([
                            'message' => $messageWithPlaceholder,
                            'type_notify' => 'checkComment',
                            'to_user' => $key,
                            'isread' => 0,
                            'from_user' => 330,
                            'dateNotify' => Carbon::now()
                        ]);
                    }
                }
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
