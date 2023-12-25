<?php

namespace App\Console\Commands;

use App\Models\client_comment;
use App\Notifications\SendNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class checkClientComments extends Command
{
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
        $params = [];

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
            $client = client_comment::where('fk_client', $idClients)->update(['name_enterprise' => 'hi 3']);

            foreach ($idClients as $idClients) {
                Notification::send(
                    null,
                    new SendNotification(
                        'Hi ' . $idClients->name . ',',
                        'dsad',
                        'A patient has been found for you to treat, go and check your patients',
                        [$idClients->fcm_token]
                    )
                );
            }
            // return response()->json($resJson);
        }catch (\Throwable $e) {
            $resJson = [
                "result" => "error",
                "code" => 400,
                "message" => $e->getMessage()
            ];

            return response()->json($resJson);
        }
    }

}
